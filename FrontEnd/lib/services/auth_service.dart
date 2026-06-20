import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:path_provider/path_provider.dart';
import 'package:flutter/foundation.dart';
import '../config/app_config.dart';

class AuthService {
  // ============================================================
  // 🌐 CENTRALIZED CONFIGURATION (LINKED TO APPCONFIG)
  // ============================================================
  
  // 👉 Ambil murni dari konfigurasi pusat agar tidak terjadi tumpang tindih string
  static const String baseUrl            = AppConfig.baseUrl;       // http://10.0.2.2:8000/api
  static const String storageBaseUrl     = AppConfig.storageUrl;    // http://10.0.2.2:8000/storage
  static const String alternativeBaseUrl = 'http://10.0.2.2:8000/pdf-materi';

  // 👉 Kembalikan port asli Microservices untuk pengujian BYPASS LOKAL:
  static const String materiUrl          = AppConfig.materiUrl;     // http://10.0.2.2:9001/api
  static const String tryoutUrl          = AppConfig.tryoutUrl;     // http://10.0.2.2:9002/api
  static const String practiceUrl        = AppConfig.practiceUrl;   // http://10.0.2.2:9003/api

  // ============================================================
  // 📥 DOWNLOAD & CACHE SERVICE (PDF)
  // ============================================================
  // 📥 DOWNLOAD & CACHE SERVICE (PDF)

  // ============================================================
  // 📥 DOWNLOAD & CACHE SERVICE (PDF)
  // ============================================================

  static Future<String?> downloadMateri(
      String filePath, Function(int, int) onProgress,
      {String? token, int maxRetry = 3}) async {
    try {
      String? result = await _downloadWithAlternativeRoute(filePath, onProgress,
          token: token, maxRetry: maxRetry);
      if (result != null) return result;
      return await _downloadWithOriginalRoute(filePath, onProgress,
          token: token, maxRetry: maxRetry);
    } catch (e) {
      debugPrint('❌ Download Exception: $e');
      return null;
    }
  }

  static Future<String?> _downloadWithAlternativeRoute(
      String filePath, Function(int, int) onProgress,
      {String? token, int maxRetry = 3}) async {
    String filename = filePath.split('/').last;
    String alternativeUrl = '$alternativeBaseUrl/$filename';
    for (int attempt = 1; attempt <= maxRetry; attempt++) {
      try {
        final dir = await getTemporaryDirectory();
        final String savePath = '${dir.path}/materi_${filename.hashCode}.pdf';
        final request = await HttpClient().getUrl(Uri.parse(alternativeUrl));
        if (token != null)
          request.headers.set(HttpHeaders.authorizationHeader, 'Bearer $token');
        final response =
            await request.close().timeout(const Duration(seconds: 30));
        if (response.statusCode != 200) continue;
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
        if (attempt == maxRetry) break;
      }
    }
    return null;
  }

  static Future<String?> _downloadWithOriginalRoute(
      String filePath, Function(int, int) onProgress,
      {String? token, int maxRetry = 3}) async {
    String fullUrl = filePath.startsWith('http')
        ? filePath
        : '$storageBaseUrl/${filePath.startsWith('/') ? filePath.substring(1) : filePath}';
    final dir = await getTemporaryDirectory();
    final String savePath = '${dir.path}/materi_${fullUrl.hashCode}.pdf';
    final httpClient = HttpClient()
      ..connectionTimeout = const Duration(seconds: 20);
    try {
      for (int attempt = 1; attempt <= maxRetry; attempt++) {
        try {
          final request = await httpClient.getUrl(Uri.parse(fullUrl));
          if (token != null)
            request.headers
                .set(HttpHeaders.authorizationHeader, 'Bearer $token');
          final response =
              await request.close().timeout(const Duration(minutes: 2));
          if (response.statusCode != 200) {
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
      final file1 =
          File('${dir.path}/materi_${filePath.split('/').last.hashCode}.pdf');
      final file2 = File('${dir.path}/materi_${filePath.hashCode}.pdf');
      if (await file1.exists()) await file1.delete();
      if (await file2.exists()) await file2.delete();
    } catch (e) {
      debugPrint('❌ Gagal hapus cache: $e');
    }
  }

  static Future<http.StreamedResponse> joinClass(
      int classId, String imagePath, String token) async {
    var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/enroll'));
    request.headers.addAll(
        {'Authorization': 'Bearer $token', 'Accept': 'application/json'});
    request.fields['class_id'] = classId.toString();
    request.files
        .add(await http.MultipartFile.fromPath('payment_proof', imagePath));
    return await request.send();
  }

  // ============================================================
  // 🔐 AUTHENTICATION METHODS
  // ============================================================

  static Future<http.Response> register(Map<String, dynamic> data) async {
    return await http.post(Uri.parse('$baseUrl/register'),
        headers: {'Accept': 'application/json'},
        body: data.map((key, value) => MapEntry(key, value.toString())));
  }

  static Future<http.Response> login(String name, String password) async {
    return await http.post(Uri.parse('$baseUrl/login'),
        body: {'name': name.trim(), 'password': password});
  }

  static Future<http.Response> verifyRegistration(
      String name, String otp) async {
    return await http.post(Uri.parse('$baseUrl/verify-registration'),
        body: {'name': name.trim(), 'otp': otp.trim()});
  }

  static Future<http.Response> resendOtp(String name) async {
    return await http
        .post(Uri.parse('$baseUrl/resend-otp'), body: {'name': name.trim()});
  }

  static Future<http.Response> forgotPassword(String email) async {
    return await http.post(
      Uri.parse('$baseUrl/forgot-password'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: jsonEncode({'email': email.trim()}),
    );
  }

  static Future<http.Response> resetPassword(Map<String, dynamic> data) async {
    return await http.post(
      Uri.parse('$baseUrl/reset-password'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: jsonEncode(data), // 🔥 Ubah menjadi jsonEncode murni
    );
  }

  static Future<http.Response> validateResetOtp(String email, String otp) async {
    return await http.post(
      Uri.parse('$baseUrl/validate-reset-otp'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: jsonEncode({
        'email': email.trim(),
        'otp': otp.trim(),
      }),
    );
  } 

  // ✅ MODIFIKASI: Memanggil endpoint /profile dan mengambil nested user object
  static Future<Map<String, dynamic>?> getUserProfile(String token) async {
    final response = await http.get(Uri.parse('$baseUrl/profile'), headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json'
    });

    print("📡 PROFILE RESPONSE STATUS: ${response.statusCode}");
    print("📡 PROFILE RESPONSE BODY: ${response.body}");

    if (response.statusCode == 200) {
      var data = jsonDecode(response.body);
      // ✅ LANGSUNG KEMBALIKAN DATA USER
      if (data['status'] == 'success' && data['user'] != null) {
        return data['user'];
      }
      return data;
    }
    return null;
  }

  static Future<http.Response> updateProfile(
      Map<String, dynamic> data, String token) async {
    return await http.post(Uri.parse('$baseUrl/update-profile'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json'
        },
        body: data.map((key, value) => MapEntry(key, value.toString())));
  }

  // ============================================================
  // 📚 CONTENT & TRYOUT METHODS (MICROSERVICES)
  // ============================================================

  // 1. Mengambil Materi (Port 9001)
  static Future<http.Response> getClassContent(int classId, String token) async {
    try {
      // Kita pastikan format URL bersih dan tepat sasaran
      final urlString = '${materiUrl.endsWith('/') ? materiUrl.substring(0, materiUrl.length - 1) : materiUrl}/materials?class_id=$classId';
      debugPrint("📡 [TRY-CATCH MATERI] Menembak URL: $urlString");

      final response = await http.get(
        Uri.parse(urlString),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
      ).timeout(const Duration(seconds: 10));

      debugPrint("📡 [TRY-CATCH MATERI] Status Kode: ${response.statusCode}");
      debugPrint("📡 [TRY-CATCH MATERI] Isi Respons: ${response.body}");
      return response;
    } catch (e, stacktrace) {
      debugPrint("❌ [TRY-CATCH MATERI] Terjadi Eror: $e");
      debugPrint("❌ [TRY-CATCH MATERI] Stacktrace: $stacktrace");
      // Mengembalikan objek response kosong dengan status 500 agar interupsi catch terisolasi
      return http.Response(jsonEncode({'status': 'error', 'message': e.toString()}), 500);
    }
  }

  // 2. Mengambil Latihan Soal Mingguan (Port 9003)
  static Future<http.Response> getTryouts(String token, {int? classId}) async {
    try {
      // 🔥 KOREKSI UTAMA: Mengubah akhiran dari '/tryouts' menjadi '/practices' agar tidak salah kamar ke modul Tryout
      String urlString = '${practiceUrl.endsWith('/') ? practiceUrl.substring(0, practiceUrl.length - 1) : practiceUrl}/practices';
      if (classId != null) urlString += '?class_id=$classId';
      
      debugPrint("📡 [TRY-CATCH LATIHAN] Menembak URL: $urlString");

      final response = await http.get(
        Uri.parse(urlString),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
      ).timeout(const Duration(seconds: 10));

      debugPrint("📡 [TRY-CATCH LATIHAN] Status Kode: ${response.statusCode}");
      debugPrint("📡 [TRY-CATCH LATIHAN] Isi Respons: ${response.body}");
      return response;
    } catch (e, stacktrace) {
      debugPrint("❌ [TRY-CATCH LATIHAN] Terjadi Eror: $e");
      debugPrint("❌ [TRY-CATCH LATIHAN] Stacktrace: $stacktrace");
      return http.Response(jsonEncode({'status': 'error', 'message': e.toString()}), 500);
    }
  }

  // (Fungsi submitPracticeAnswer dihapus dari sini karena dikerjakan secara lokal)

  // 3. Mengambil Simulasi Tryout (Port 9002)
  static Future<http.Response> getSimulasi(String token,
      {int? classId, int? userId}) async {
    String url = '$tryoutUrl/tryouts';
    if (classId != null) url += '?class_id=$classId';
    if (userId != null) url += '&user_id=$userId'; // ✨ Kirim userID

    return await http.get(Uri.parse(url), headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    }).timeout(const Duration(seconds: 15));
  }

  // Mengambil soal Tryout dari tryoutUrl (Port 9002)
  static Future<http.Response> getQuestions(int tryoutId, String token) async {
    return await http.get(Uri.parse('$tryoutUrl/tryouts/$tryoutId/questions'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }).timeout(const Duration(
        seconds: 10)); // Jangan return 408 di sini agar catch di UI berfungsi
  }

  // Submit Hasil Tryout ke Port 9002
  static Future<http.Response> submitTryout({
    required int tryoutId,
    required int userId,
    required Map<dynamic, dynamic> answers,
    required String token,
  }) async {
    Map<String, String> stringAnswers =
        answers.map((k, v) => MapEntry(k.toString(), v.toString()));
    return await http
        .post(Uri.parse('$tryoutUrl/tryouts/$tryoutId/submit'),
            headers: {
              'Content-Type': 'application/json',
              'Authorization': 'Bearer $token',
              'Accept': 'application/json'
            },
            body: jsonEncode({
              'tryout_id': tryoutId,
              'user_id': userId,
              'answers': stringAnswers
            }))
        .timeout(const Duration(seconds: 15));
  }

  // ============================================================
  // 📈 REPORTING & HISTORY (MENGGUNAKAN MICROSERVICE GO)
  // ============================================================

  static Future<http.Response> getTryoutHistory(
      String token, int userId) async {
    return await http
        .get(Uri.parse('$tryoutUrl/tryouts/history?user_id=$userId'), headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json'
    }).timeout(const Duration(seconds: 10),
            onTimeout: () => http.Response('[]', 408));
  }

  static Future<http.Response> getAnnouncements(String token) async =>
      await http.get(Uri.parse('$baseUrl/announcements'), headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json'
      });

  static Future<Map<String, dynamic>?> uploadProfilePhoto(
      File imageFile, String token) async {
    var request =
        http.MultipartRequest('POST', Uri.parse('$baseUrl/profile/photo'));

    request.headers.addAll({
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    });

    request.files
        .add(await http.MultipartFile.fromPath('photo', imageFile.path));

    try {
      var streamedResponse = await request.send();
      var response = await http.Response.fromStream(streamedResponse);

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      }
    } catch (e) {
      debugPrint("Upload Error: $e");
    }
    return null;
  }
// ============================================================
// 💳 PAYMENT METHODS
// ============================================================

  /// Mendapatkan Snap Token dari Midtrans
  static Future<Map<String, dynamic>?> getSnapToken({
    required int classId,
    required String token,
    String? promoCode,
  }) async {
    try {
      final body = {
        'class_id': classId.toString(),
      };
      if (promoCode != null && promoCode.isNotEmpty) {
        body['promo_code'] = promoCode;
      }

      final response = await http.post(
        Uri.parse('$baseUrl/payment/snap-token'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode(body),
      );

      debugPrint("📡 getSnapToken response: ${response.statusCode}");
      debugPrint("📡 Body: ${response.body}");

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      }
      return null;
    } catch (e) {
      debugPrint("❌ getSnapToken error: $e");
      return null;
    }
  }

  /// Manual update payment success (dipanggil setelah sukses bayar di WebView)
  static Future<bool> manualPaymentSuccess({
    required String orderId,
    required String token,
  }) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/payment/manual-success'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({'order_id': orderId}),
      );

      debugPrint("📡 manualPaymentSuccess response: ${response.statusCode}");
      debugPrint("📡 Body: ${response.body}");

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data['success'] == true;
      }
      return false;
    } catch (e) {
      debugPrint("❌ manualPaymentSuccess error: $e");
      return false;
    }
  }
}
