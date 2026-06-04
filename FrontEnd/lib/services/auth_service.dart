import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:path_provider/path_provider.dart';
import 'package:flutter/foundation.dart';

class AuthService {
  // ============================================================
  // 🌐 CONFIGURATION
  // ============================================================
  static const String host = '10.0.2.2'; // Emulator Android ke Localhost
  static const String baseUrl = 'http://$host:8000/api';
  static const String storageBaseUrl = 'http://$host:8000/storage';
  static const String alternativeBaseUrl = 'http://$host:8000/pdf-materi';

  // ============================================================
  // 📥 DOWNLOAD & CACHE SERVICE (PDF)
  // ============================================================
  
  static Future<String?> downloadMateri(
    String filePath,
    Function(int, int) onProgress, {
    String? token,
    int maxRetry = 3,
  }) async {
    try {
      // Mencoba rute alternatif dulu (jika ada setup khusus di Laravel)
      String? result = await _downloadWithAlternativeRoute(filePath, onProgress, 
          token: token, maxRetry: maxRetry);
      
      if (result != null) return result;

      // Jika gagal, coba rute original (/storage/...)
      return await _downloadWithOriginalRoute(filePath, onProgress, 
          token: token, maxRetry: maxRetry);
    } catch (e) {
      debugPrint('❌ Download Exception: $e');
      return null;
    }
  }

  static Future<String?> _downloadWithAlternativeRoute(String filePath, Function(int, int) onProgress, {String? token, int maxRetry = 3}) async {
    String filename = filePath.split('/').last;
    String alternativeUrl = '$alternativeBaseUrl/$filename';

    for (int attempt = 1; attempt <= maxRetry; attempt++) {
      try {
        final dir = await getTemporaryDirectory();
        final String savePath = '${dir.path}/materi_${filename.hashCode}.pdf';
        
        final request = await HttpClient().getUrl(Uri.parse(alternativeUrl));
        if (token != null) {
          request.headers.set(HttpHeaders.authorizationHeader, 'Bearer $token');
        }

        final response = await request.close().timeout(const Duration(seconds: 30));
        
        if (response.statusCode != 200) {
          debugPrint('⚠️ Alt Route Attempt $attempt: Status ${response.statusCode}');
          continue;
        }

        final file = File(savePath);
        final sink = file.openWrite();
        int received = 0;
        int total = response.contentLength;

        await for (final chunk in response) { 
          sink.add(chunk); 
          received += chunk.length; 
          onProgress(received, total); 
        }
        await sink.close();
        return savePath;
      } catch (e) { 
        debugPrint('❌ Alt Route Error: $e');
        if (attempt == maxRetry) break; 
      }
    }
    return null;
  }

  static Future<String?> _downloadWithOriginalRoute(String filePath, Function(int, int) onProgress, {String? token, int maxRetry = 3}) async {
    // Memastikan URL lengkap
    String fullUrl = filePath.startsWith('http') 
        ? filePath 
        : '$storageBaseUrl/${filePath.startsWith('/') ? filePath.substring(1) : filePath}';
    
    final dir = await getTemporaryDirectory();
    final String savePath = '${dir.path}/materi_${fullUrl.hashCode}.pdf';
    final httpClient = HttpClient()..connectionTimeout = const Duration(seconds: 20);

    try {
      for (int attempt = 1; attempt <= maxRetry; attempt++) {
        try {
          final request = await httpClient.getUrl(Uri.parse(fullUrl));
          if (token != null) {
            request.headers.set(HttpHeaders.authorizationHeader, 'Bearer $token');
          }

          final response = await request.close().timeout(const Duration(minutes: 2));
          
          if (response.statusCode != 200) { 
            debugPrint('⚠️ Org Route Attempt $attempt: Status ${response.statusCode}');
            await response.drain(); 
            continue; 
          }

          final file = File(savePath);
          final sink = file.openWrite();
          int received = 0;
          int total = response.contentLength;

          await for (final chunk in response) { 
            sink.add(chunk); 
            received += chunk.length; 
            onProgress(received, total); 
          }
          await sink.close();
          return savePath;
        } catch (e) { 
          debugPrint('❌ Org Route Error: $e');
          if (attempt == maxRetry) break; 
        }
      }
    } finally { 
      httpClient.close(); 
    }
    return null;
  }

  static Future<void> clearMateriCache(String filePath) async {
    try {
      final dir = await getTemporaryDirectory();
      // Hapus file berdasarkan hash URL/nama
      final file1 = File('${dir.path}/materi_${filePath.split('/').last.hashCode}.pdf');
      final file2 = File('${dir.path}/materi_${filePath.hashCode}.pdf');
      if (await file1.exists()) await file1.delete();
      if (await file2.exists()) await file2.delete();
    } catch (e) { 
      debugPrint('❌ Gagal hapus cache: $e'); 
    }
  }

  // ============================================================
  // 🔐 AUTHENTICATION METHODS
  // ============================================================

  static Future<http.Response> register(Map<String, dynamic> data) async {
    return await http.post(
      Uri.parse('$baseUrl/register'), 
      headers: {'Accept': 'application/json'}, 
      body: data.map((key, value) => MapEntry(key, value.toString()))
    );
  }
  
  static Future<http.Response> login(String name, String password) async {
    return await http.post(Uri.parse('$baseUrl/login'), 
      body: {'name': name.trim(), 'password': password});
  }

  static Future<http.Response> verifyRegistration(String name, String otp) async {
    return await http.post(Uri.parse('$baseUrl/verify-registration'), 
      body: {'name': name.trim(), 'otp': otp.trim()});
  }

  static Future<http.Response> resendOtp(String name) async {
    return await http.post(Uri.parse('$baseUrl/resend-otp'), 
      body: {'name': name.trim()});
  }

  static Future<http.Response> forgotPassword(String email) async {
    return await http.post(Uri.parse('$baseUrl/forgot-password'), 
      body: {'email': email.trim()});
  }

  static Future<http.Response> resetPassword(Map<String, dynamic> data) async {
    return await http.post(Uri.parse('$baseUrl/reset-password'), 
      body: data.map((key, value) => MapEntry(key, value.toString())));
  }

  static Future<Map<String, dynamic>?> getUserProfile(String token) async {
    final response = await http.get(Uri.parse('$baseUrl/user'), 
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'});
    if (response.statusCode == 200) return jsonDecode(response.body);
    return null;
  }

  static Future<http.Response> updateProfile(Map<String, dynamic> data, String token) async {
    return await http.post(Uri.parse('$baseUrl/update-profile'), 
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'}, 
      body: data.map((key, value) => MapEntry(key, value.toString())));
  }

  // ============================================================
  // 📚 CONTENT & TRYOUT METHODS
  // ============================================================
  
  static Future<http.Response> getClassContent(int classId, String token) async {
    return await http.post(Uri.parse('$baseUrl/class/content'),
      headers: {
        'Content-Type': 'application/json', 
        'Authorization': 'Bearer $token',
        'Accept': 'application/json'
      },
      body: jsonEncode({'class_id': classId}));
  }

  static Future<http.Response> getTryouts(String token) async =>
      await http.get(Uri.parse('$baseUrl/tryouts'), 
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'});

  static Future<http.Response> getQuestions(int tryoutId, String token) async =>
      await http.get(Uri.parse('$baseUrl/tryouts/$tryoutId/questions'), 
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'});

  static Future<http.Response> getTryoutHistory(String token) async =>
      await http.get(Uri.parse('$baseUrl/tryouts/my'), 
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'});

  static Future<http.Response> submitTryout({
    required int tryoutId,
    required Map<dynamic, dynamic> answers, 
    required String token,
  }) async {
    Map<String, String> stringAnswers = answers.map((k, v) => MapEntry(k.toString(), v.toString()));
    return await http.post(Uri.parse('$baseUrl/tryouts/$tryoutId/submit'),
      headers: {
        'Content-Type': 'application/json', 
        'Authorization': 'Bearer $token',
        'Accept': 'application/json'
      },
      body: jsonEncode({'tryout_id': tryoutId, 'answers': stringAnswers}));
  }

  static Future<http.Response> getLearningReport(String token) async =>
      await http.get(Uri.parse('$baseUrl/learning-report'), 
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'});

  static Future<http.Response> getAnnouncements(String token) async =>
      await http.get(Uri.parse('$baseUrl/announcements'), 
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'});
}