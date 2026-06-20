import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:path_provider/path_provider.dart';
import 'package:flutter/foundation.dart';
import '../config/app_config.dart'; // 👈 Tambahkan import file konfigurasi terpusat Anda di sini

class AuthService {
  // ============================================================
  // 🌐 CONFIGURATION (LINKED TO CENTRAL APPCONFIG)
  // ============================================================
  static const String baseUrl            = AppConfig.baseUrl;
  static const String storageBaseUrl     = AppConfig.storageUrl;
  
  // Mengikuti pola host dari AppConfig untuk fallback download berkas PDF materi
  static const String alternativeBaseUrl = 'http://${AppConfig.host}/pdf-materi';
  
  // Endpoint Microservices yang rutenya dialihkan secara terpusat oleh Nginx Reverse Proxy
  static const String materiUrl          = AppConfig.materiUrl;
  static const String tryoutUrl          = AppConfig.tryoutUrl;
  static const String practiceUrl        = AppConfig.practiceUrl;

  // ============================================================
  // 📥 DOWNLOAD & CACHE SERVICE (PDF)
  // ============================================================
  
  static Future<String?> downloadMateri(String filePath, Function(int, int) onProgress, {String? token, int maxRetry = 3}) async {
    try {
      String? result = await _downloadWithAlternativeRoute(filePath, onProgress, token: token, maxRetry: maxRetry);
      if (result != null) return result;
      return await _downloadWithOriginalRoute(filePath, onProgress, token: token, maxRetry: maxRetry);
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
        if (token != null) request.headers.set(HttpHeaders.authorizationHeader, 'Bearer $token');
        final response = await request.close().timeout(const Duration(seconds: 30));
        if (response.statusCode != 200) continue;
        final file = File(savePath);
        final sink = file.openWrite();
        int received = 0; int total = response.contentLength;
        await for (final chunk in response) { sink.add(chunk); received += chunk.length; onProgress(received, total); }
        await sink.close(); return savePath;
      } catch (e) { if (attempt == maxRetry) break; }
    }
    return null;
  }

  static Future<String?> _downloadWithOriginalRoute(String filePath, Function(int, int) onProgress, {String? token, int maxRetry = 3}) async {
    String fullUrl = filePath.startsWith('http') ? filePath : '$storageBaseUrl/${filePath.startsWith('/') ? filePath.substring(1) : filePath}';
    final dir = await getTemporaryDirectory();
    final String savePath = '${dir.path}/materi_${fullUrl.hashCode}.pdf';
    final httpClient = HttpClient()..connectionTimeout = const Duration(seconds: 20);
    try {
      for (int attempt = 1; attempt <= maxRetry; attempt++) {
        try {
          final request = await httpClient.getUrl(Uri.parse(fullUrl));
          if (token != null) request.headers.set(HttpHeaders.authorizationHeader, 'Bearer $token');
          final response = await request.close().timeout(const Duration(minutes: 2));
          if (response.statusCode != 200) { await response.drain(); continue; }
          final file = File(savePath); final sink = file.openWrite();
          int received = 0; int total = response.contentLength;
          await for (final chunk in response) { sink.add(chunk); received += chunk.length; onProgress(received, total); }
          await sink.close(); return savePath;
        } catch (e) { if (attempt == maxRetry) break; }
      }
    } finally { httpClient.close(); }
    return null;
  }

  static Future<void> clearMateriCache(String filePath) async {
    try {
      final dir = await getTemporaryDirectory();
      final file1 = File('${dir.path}/materi_${filePath.split('/').last.hashCode}.pdf');
      final file2 = File('${dir.path}/materi_${filePath.hashCode}.pdf');
      if (await file1.exists()) await file1.delete();
      if (await file2.exists()) await file2.delete();
    } catch (e) { debugPrint('❌ Gagal hapus cache: $e'); }
  }

  // ============================================================
  // 📝 ENROLLMENT (LARAVEL)
  // ============================================================

  static Future<http.StreamedResponse> joinClass(int classId, String imagePath, String token) async {
    var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/enroll'));
    request.headers.addAll({'Authorization': 'Bearer $token', 'Accept': 'application/json'});
    request.fields['class_id'] = classId.toString();
    request.files.add(await http.MultipartFile.fromPath('payment_proof', imagePath));
    return await request.send();
  }

  // ============================================================
  // 🔐 AUTHENTICATION METHODS (LARAVEL)
  // ============================================================

  static Future<http.Response> register(Map<String, dynamic> data) async {
    return await http.post(Uri.parse('$baseUrl/register'), headers: {'Accept': 'application/json'}, body: data.map((key, value) => MapEntry(key, value.toString())));
  }
  
  static Future<http.Response> login(String name, String password) async {
    return await http.post(Uri.parse('$baseUrl/login'), body: {'name': name.trim(), 'password': password});
  }

  static Future<http.Response> verifyRegistration(String name, String otp) async {
    return await http.post(Uri.parse('$baseUrl/verify-registration'), body: {'name': name.trim(), 'otp': otp.trim()});
  }

  static Future<http.Response> resendOtp(String name) async {
    return await http.post(Uri.parse('$baseUrl/resend-otp'), body: {'name': name.trim()});
  }

  static Future<http.Response> forgotPassword(String email) async {
    return await http.post(Uri.parse('$baseUrl/forgot-password'), body: {'email': email.trim()});
  }

  static Future<http.Response> resetPassword(Map<String, dynamic> data) async {
    return await http.post(Uri.parse('$baseUrl/reset-password'), body: data.map((key, value) => MapEntry(key, value.toString())));
  }

  static Future<Map<String, dynamic>?> getUserProfile(String token) async {
    final response = await http.get(Uri.parse('$baseUrl/user'), headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'});
    if (response.statusCode == 200) return jsonDecode(response.body);
    return null;
  }

  static Future<http.Response> updateProfile(Map<String, dynamic> data, String token) async {
    return await http.post(Uri.parse('$baseUrl/update-profile'), headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'}, body: data.map((key, value) => MapEntry(key, value.toString())));
  }

  // ============================================================
  // 📚 CONTENT & MICROSERVICES METHODS
  // ============================================================
  
  // Ambil Materi (Materi Service via Reverse Proxy Nginx)
  static Future<http.Response> getClassContent(int classId, String token) async {
    return await http.get(
      Uri.parse('$materiUrl/materials?class_id=$classId'),
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json', 'Content-Type': 'application/json'},
    ).timeout(const Duration(seconds: 15));
  }

  // Ambil Latihan Soal Mingguan (Practice Service via Reverse Proxy Nginx)
  static Future<http.Response> getTryouts(String token, {int? classId}) async {
    String url = '$practiceUrl/tryouts';
    if (classId != null) url += '?class_id=$classId';
    return await http.get(
      Uri.parse(url), 
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json', 'Content-Type': 'application/json'},
    ).timeout(const Duration(seconds: 15));
  }

  // Ambil Simulasi Tryout (Tryout Service via Reverse Proxy Nginx)
  static Future<http.Response> getSimulasi(String token, {int? classId}) async {
    String url = '$tryoutUrl/tryouts';
    if (classId != null) url += '?class_id=$classId';
    return await http.get(
      Uri.parse(url), 
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json', 'Content-Type': 'application/json'},
    ).timeout(const Duration(seconds: 15));
  }

  // Ambil Soal-soal Tryout (Tryout Service via Reverse Proxy Nginx)
  static Future<http.Response> getQuestions(int tryoutId, String token) async {
    return await http.get(
      Uri.parse('$tryoutUrl/tryouts/$tryoutId/questions'), 
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json', 'Content-Type': 'application/json'},
    ).timeout(const Duration(seconds: 15));
  }

  // Sejarah Tryout & Submit
  static Future<http.Response> getTryoutHistory(String token) async =>
      await http.get(Uri.parse('$baseUrl/tryouts/my'), headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'});

  static Future<http.Response> submitTryout({required int tryoutId, required Map<dynamic, dynamic> answers, required String token}) async {
    Map<String, String> stringAnswers = answers.map((k, v) => MapEntry(k.toString(), v.toString()));
    return await http.post(
      Uri.parse('$baseUrl/tryouts/$tryoutId/submit'),
      headers: {'Content-Type': 'application/json', 'Authorization': 'Bearer $token', 'Accept': 'application/json'},
      body: jsonEncode({'tryout_id': tryoutId, 'answers': stringAnswers})
    ).timeout(const Duration(seconds: 20));
  }

  static Future<http.Response> getLearningReport(String token) async =>
      await http.get(Uri.parse('$baseUrl/learning-report'), headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'});

  static Future<http.Response> getAnnouncements(String token) async =>
      await http.get(Uri.parse('$baseUrl/announcements'), headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'});
}