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
  static const String host = '10.0.2.2'; 
  static const String baseUrl = 'http://$host:8000/api';
  static const String storageBaseUrl = 'http://$host:8000/storage';
  static const String alternativeBaseUrl = 'http://$host:8000/pdf-materi';

  // ============================================================
  // 📥 DOWNLOAD & CACHE SERVICE (PDF)
  // ============================================================
  
  // ✨ FIX: Menambahkan maxRetry agar tidak error di pdf_viewer_page
  static Future<String?> downloadMateri(
    String filePath,
    Function(int, int) onProgress, {
    String? token,
    int maxRetry = 3,
  }) async {
    try {
      // 1. Coba rute alternatif
      String? result = await _downloadWithAlternativeRoute(filePath, onProgress, 
          token: token, maxRetry: maxRetry);
      if (result != null) return result;

      // 2. Coba rute original
      return await _downloadWithOriginalRoute(filePath, onProgress, 
          token: token, maxRetry: maxRetry);
    } catch (e) {
      debugPrint('❌ Download Error: $e');
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
        int received = 0;
        await for (final chunk in response) { 
          sink.add(chunk); 
          received += chunk.length; 
          onProgress(received, response.contentLength); 
        }
        await sink.close();
        return savePath;
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
          final file = File(savePath);
          final sink = file.openWrite();
          int received = 0;
          await for (final chunk in response) { 
            sink.add(chunk); 
            received += chunk.length; 
            onProgress(received, response.contentLength); 
          }
          await sink.close();
          return savePath;
        } catch (e) { if (attempt == maxRetry) break; }
      }
    } finally { httpClient.close(); }
    return null;
  }

  static Future<void> clearMateriCache(String filePath) async {
    try {
      final dir = await getTemporaryDirectory();
      final file = File('${dir.path}/materi_${filePath.hashCode}.pdf');
      if (await file.exists()) await file.delete();
    } catch (e) { debugPrint('❌ Gagal hapus cache: $e'); }
  }

  // ============================================================
  // 🔐 AUTHENTICATION METHODS
  // ============================================================
  
  static Future<http.Response> login(String name, String password) async {
    return await http.post(Uri.parse('$baseUrl/login'), 
      body: {'name': name.trim(), 'password': password});
  }

  static Future<http.Response> register(Map<String, dynamic> data) async {
    return await http.post(Uri.parse('$baseUrl/register'), 
      body: data.map((key, value) => MapEntry(key, value.toString())));
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
      headers: {'Authorization': 'Bearer $token'});
    if (response.statusCode == 200) return jsonDecode(response.body);
    return null;
  }

  static Future<http.Response> updateProfile(Map<String, dynamic> data, String token) async {
    return await http.post(Uri.parse('$baseUrl/update-profile'), 
      headers: {'Authorization': 'Bearer $token'}, 
      body: data.map((key, value) => MapEntry(key, value.toString())));
  }

  // ============================================================
  // 📚 CONTENT & TRYOUT METHODS
  // ============================================================
  
  static Future<http.Response> getClassContent(int classId, String token) async {
    return await http.post(Uri.parse('$baseUrl/class/content'),
      headers: {'Content-Type': 'application/json', 'Authorization': 'Bearer $token'},
      body: jsonEncode({'class_id': classId}));
  }

  // Daftar TO
  static Future<http.Response> getTryouts(String token) async =>
      await http.get(Uri.parse('$baseUrl/tryouts'), headers: {'Authorization': 'Bearer $token'});

  // Ambil Soal
  static Future<http.Response> getQuestions(int tryoutId, String token) async =>
      await http.get(Uri.parse('$baseUrl/tryouts/$tryoutId/questions'), 
      headers: {'Authorization': 'Bearer $token'});

  // Ambil Riwayat
  static Future<http.Response> getTryoutHistory(String token) async =>
      await http.get(Uri.parse('$baseUrl/tryouts/my'), 
      headers: {'Authorization': 'Bearer $token'});

  // Submit Jawaban
  static Future<http.Response> submitTryout({
    required int tryoutId,
    required Map<dynamic, dynamic> answers, 
    required String token,
  }) async {
    Map<String, String> stringAnswers = answers.map((k, v) => MapEntry(k.toString(), v.toString()));
    return await http.post(Uri.parse('$baseUrl/tryouts/$tryoutId/submit'),
      headers: {'Content-Type': 'application/json', 'Authorization': 'Bearer $token'},
      body: jsonEncode({'tryout_id': tryoutId, 'answers': stringAnswers}));
  }

  static Future<http.Response> getLearningReport(String token) async =>
      await http.get(Uri.parse('$baseUrl/learning-report'), headers: {'Authorization': 'Bearer $token'});

  static Future<http.Response> getAnnouncements(String token) async =>
      await http.get(Uri.parse('$baseUrl/announcements'), headers: {'Authorization': 'Bearer $token'});
}