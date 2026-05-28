import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:path_provider/path_provider.dart';
import 'package:flutter/foundation.dart';

class AuthService {
  // ============================================================
  // 🌐 CONFIGURATION
  // ============================================================
  static const String host = '10.0.2.2';
  static const String baseUrl = 'http://$host:8000/api';
  static const String storageBaseUrl = 'http://$host:8000/storage';
  static const String alternativeBaseUrl = 'http://$host:8000/pdf-materi'; // ROUTE BARU

  // ============================================================
  // 🔍 TEST KONEKSI
  // ============================================================
  static Future<Map<String, dynamic>> testConnection(String filePath) async {
    String fullUrl;
    if (filePath.startsWith('http')) {
      fullUrl = filePath;
    } else {
      String cleanPath = filePath.startsWith('/') ? filePath.substring(1) : filePath;
      fullUrl = '$storageBaseUrl/$cleanPath';
    }

    debugPrint('🔍 ===== TEST KONEKSI =====');
    debugPrint('🔍 URL: $fullUrl');

    Map<String, dynamic> result = {
      'socket': false,
      'http': false,
      'message': '',
    };

    // Test 1: Cek host bisa dijangkau via socket
    try {
      final socket = await Socket.connect(
        host,
        8000,
        timeout: const Duration(seconds: 5),
      );
      debugPrint('✅ Socket TCP OK → $host:8000 bisa dijangkau');
      socket.destroy();
      result['socket'] = true;
    } catch (e) {
      debugPrint('❌ Socket TCP GAGAL → $host:8000 tidak bisa dijangkau');
      debugPrint('   Error: $e');
      result['message'] = 'Socket connection failed';
      return result;
    }

    // Test 2: HTTP GET
    try {
      final response = await http
          .get(
            Uri.parse(fullUrl),
            headers: {'Accept': 'application/pdf,*/*'},
          )
          .timeout(const Duration(seconds: 15));
      debugPrint('✅ HTTP GET status  : ${response.statusCode}');
      debugPrint('   Content-Type     : ${response.headers['content-type']}');
      debugPrint('   Content-Length   : ${response.headers['content-length']}');
      debugPrint('   Body length      : ${response.bodyBytes.length} bytes');
      
      result['http'] = response.statusCode == 200;
      result['statusCode'] = response.statusCode;
      result['contentLength'] = response.headers['content-length'];
    } catch (e) {
      debugPrint('❌ HTTP GET GAGAL: $e');
      result['message'] = e.toString();
    }

    debugPrint('🔍 ===== SELESAI TEST =====');
    return result;
  }

  // ============================================================
  // 📥 DOWNLOAD SERVICE - VERSION 2 (DENGAN FALLBACK)
  // ============================================================
  static Future<String?> downloadMateri(
    String filePath,
    Function(int, int) onProgress, {
    String? token,
    int maxRetry = 3,
  }) async {
    try {
      // Test koneksi dulu
      await testConnection(filePath);

      // Coba dengan alternative route dulu (lebih stabil)
      String? result = await _downloadWithAlternativeRoute(filePath, onProgress, token: token, maxRetry: maxRetry);
      if (result != null) return result;
      
      // Fallback ke route original
      return await _downloadWithOriginalRoute(filePath, onProgress, token: token, maxRetry: maxRetry);
      
    } catch (e, stack) {
      debugPrint('❌ AuthService Download Error: $e');
      debugPrint('   Stack:\n${stack.toString().split('\n').take(5).join('\n')}');
      return null;
    }
  }

  // ============================================================
  // 📥 DOWNLOAD DENGAN ALTERNATIVE ROUTE (LEBIH STABLE)
  // ============================================================
  static Future<String?> _downloadWithAlternativeRoute(
    String filePath,
    Function(int, int) onProgress, {
    String? token,
    int maxRetry = 3,
  }) async {
    // Extract filename dari path
    String filename = filePath.split('/').last;
    String alternativeUrl = '$alternativeBaseUrl/$filename';
    
    debugPrint('🌐 Mencoba alternative route: $alternativeUrl');
    
    for (int attempt = 1; attempt <= maxRetry; attempt++) {
      try {
        final dir = await getTemporaryDirectory();
        final String savePath = '${dir.path}/materi_${filename.hashCode}.pdf';
        
        // Cek cache
        final existingFile = File(savePath);
        if (await existingFile.exists() && await existingFile.length() > 1024) {
          debugPrint('✅ Pakai cache: $savePath');
          return savePath;
        }
        
        final request = await HttpClient().getUrl(Uri.parse(alternativeUrl));
        request.headers.set(HttpHeaders.acceptHeader, 'application/pdf,*/*');
        if (token != null) {
          request.headers.set(HttpHeaders.authorizationHeader, 'Bearer $token');
        }
        
        final response = await request.close().timeout(const Duration(seconds: 30));
        
        if (response.statusCode != 200) {
          debugPrint('❌ Alternative route gagal: ${response.statusCode}');
          continue;
        }
        
        final file = File(savePath);
        final sink = file.openWrite();
        int received = 0;
        final contentLength = response.contentLength;
        
        await for (final chunk in response) {
          sink.add(chunk);
          received += chunk.length;
          onProgress(received, contentLength);
        }
        
        await sink.close();
        
        // Validasi file
        if (await file.exists() && await file.length() > 1024) {
          debugPrint('✅ Alternative route berhasil!');
          return savePath;
        }
        
      } catch (e) {
        debugPrint('❌ Attempt $attempt alternative route gagal: $e');
        if (attempt == maxRetry) break;
        await Future.delayed(Duration(seconds: attempt));
      }
    }
    
    return null;
  }

  // ============================================================
  // 📥 DOWNLOAD DENGAN ORIGINAL ROUTE (FIXED)
  // ============================================================
  static Future<String?> _downloadWithOriginalRoute(
    String filePath,
    Function(int, int) onProgress, {
    String? token,
    int maxRetry = 3,
  }) async {
    String fullUrl;
    if (filePath.startsWith('http')) {
      fullUrl = filePath;
    } else {
      String cleanPath = filePath.startsWith('/') ? filePath.substring(1) : filePath;
      fullUrl = '$storageBaseUrl/$cleanPath';
    }

    debugPrint('🌐 URL Download (original): $fullUrl');

    final dir = await getTemporaryDirectory();
    final String savePath = '${dir.path}/materi_${fullUrl.hashCode}.pdf';
    debugPrint('💾 Save path: $savePath');

    // Cek cache
    final existingFile = File(savePath);
    if (await existingFile.exists() && await existingFile.length() > 1024) {
      debugPrint('✅ Pakai cache: $savePath (${await existingFile.length()} bytes)');
      return savePath;
    }

    final httpClient = HttpClient()
      ..connectionTimeout = const Duration(seconds: 30)
      ..idleTimeout = const Duration(minutes: 10);

    try {
      for (int attempt = 1; attempt <= maxRetry; attempt++) {
        try {
          debugPrint('📥 Download attempt $attempt/$maxRetry: $fullUrl');

          final request = await httpClient.getUrl(Uri.parse(fullUrl));
          request.headers.set(HttpHeaders.acceptHeader, 'application/pdf,*/*');
          request.headers.set(HttpHeaders.connectionHeader, 'keep-alive'); // PENTING!
          if (token != null) {
            request.headers.set(HttpHeaders.authorizationHeader, 'Bearer $token');
          }

          final response = await request.close().timeout(const Duration(minutes: 5));
          debugPrint('   Status code: ${response.statusCode}');
          debugPrint('   Content-Length: ${response.contentLength}');

          if (response.statusCode != 200) {
            await response.drain();
            if (attempt == maxRetry) return null;
            await Future.delayed(Duration(seconds: attempt * 2));
            continue;
          }

          final contentLength = response.contentLength < 0 ? 0 : response.contentLength;
          final file = File(savePath);
          final sink = file.openWrite();
          int received = 0;
          
          // Stream dengan buffer kecil untuk mencegah connection closed
          await for (final chunk in response) {
            sink.add(chunk);
            received += chunk.length;
            onProgress(received, contentLength);
            
            // Beri jeda kecil setiap 50KB agar server tidak kewalahan
            if (received % (50 * 1024) < chunk.length) {
              await Future.delayed(Duration.zero);
            }
          }
          
          await sink.close();

          // Validasi file
          final fileSize = await file.length();
          if (fileSize < 1024) {
            debugPrint('❌ File terlalu kecil ($fileSize bytes)');
            await file.delete();
            continue;
          }

          // Validasi header PDF
          final bytes = await file.openRead(0, 4).expand((x) => x).toList();
          final header = String.fromCharCodes(bytes);
          if (!header.startsWith('%PDF')) {
            debugPrint('❌ Bukan PDF valid (header: $header)');
            await file.delete();
            return null;
          }

          debugPrint('✅ Download berhasil: $savePath ($fileSize bytes)');
          return savePath;
          
        } on HttpException catch (e) {
          debugPrint('❌ Attempt $attempt gagal - HttpException: $e');
          if (attempt == maxRetry) return null;
          await Future.delayed(Duration(seconds: attempt * 2));
        } on SocketException catch (e) {
          debugPrint('❌ Attempt $attempt gagal - SocketException: $e');
          if (attempt == maxRetry) return null;
          await Future.delayed(Duration(seconds: attempt * 2));
        } catch (e) {
          debugPrint('❌ Attempt $attempt gagal - $e');
          if (attempt == maxRetry) return null;
          await Future.delayed(Duration(seconds: attempt));
        }
      }
    } finally {
      httpClient.close(force: true);
    }

    return null;
  }

  // ============================================================
  // 🗑️ HAPUS CACHE FILE
  // ============================================================
  static Future<void> clearMateriCache(String filePath) async {
    try {
      String filename = filePath.split('/').last;
      final dir = await getTemporaryDirectory();
      
      // Hapus kedua kemungkinan cache
      final file1 = File('${dir.path}/materi_${filePath.hashCode}.pdf');
      final file2 = File('${dir.path}/materi_${filename.hashCode}.pdf');
      
      if (await file1.exists()) await file1.delete();
      if (await file2.exists()) await file2.delete();
      
      debugPrint('🗑️ Cache dihapus');
    } catch (e) {
      debugPrint('❌ Gagal hapus cache: $e');
    }
  }

  // ============================================================
  // 🔐 AUTH METHODS (SAMA SEPERTI SEBELUMNYA)
  // ============================================================
  static Future<http.Response> login(String name, String password) async {
    return await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {'Accept': 'application/json'},
      body: {'name': name.trim(), 'password': password},
    );
  }

  static Future<http.Response> register(Map<String, dynamic> data) async {
    return await http.post(
      Uri.parse('$baseUrl/register'),
      headers: {'Accept': 'application/json'},
      body: data.map((key, value) => MapEntry(key, value.toString())),
    );
  }

  static Future<http.Response> verifyRegistration(String name, String otp) async {
    return await http.post(
      Uri.parse('$baseUrl/verify-registration'),
      headers: {'Accept': 'application/json'},
      body: {'name': name.trim(), 'otp': otp.trim()},
    );
  }

  static Future<http.Response> resendOtp(String name) async {
    return await http.post(
      Uri.parse('$baseUrl/resend-otp'),
      headers: {'Accept': 'application/json'},
      body: {'name': name.trim()},
    );
  }

  static Future<Map<String, dynamic>?> getUserProfile(String token) async {
    final response = await http.get(
      Uri.parse('$baseUrl/user'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );
    if (response.statusCode == 200) return jsonDecode(response.body);
    return null;
  }

  static Future<http.Response> getClassContent(int classId, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/class/content'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: jsonEncode({'class_id': classId}),
    );
  }

  static Future<http.Response> getLearningReport(String token) async =>
      await http.get(
        Uri.parse('$baseUrl/learning-report'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

  static Future<http.Response> getAnnouncements(String token) async =>
      await http.get(
        Uri.parse('$baseUrl/announcements'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

  static Future<http.Response> forgotPassword(String email) async =>
      await http.post(
        Uri.parse('$baseUrl/forgot-password'),
        headers: {'Accept': 'application/json'},
        body: {'email': email.trim()},
      );

  static Future<http.Response> resetPassword(Map<String, dynamic> data) async =>
      await http.post(
        Uri.parse('$baseUrl/reset-password'),
        headers: {'Accept': 'application/json'},
        body: data.map((key, value) => MapEntry(key, value.toString())),
      );

  static Future<http.Response> getQuestions(int tryoutId, String token) async =>
      await http.post(
        Uri.parse('$baseUrl/tryout/questions'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode({'tryout_id': tryoutId}),
      );

  static Future<http.Response> submitTryout({
    required int tryoutId,
    required Map<int, String> answers,
    required String token,
  }) async {
    Map<String, String> stringAnswers = answers.map((key, value) => MapEntry(key.toString(), value));
    return await http.post(
      Uri.parse('$baseUrl/tryout/submit'),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: jsonEncode({'tryout_id': tryoutId, 'answers': stringAnswers}),
    );
  }

  static Future<http.Response> updateProfile(Map<String, dynamic> data, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/update-profile'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: data.map((key, value) => MapEntry(key, value.toString())),
    );
  }
}