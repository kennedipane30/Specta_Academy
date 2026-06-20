import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/app_config.dart'; // 👈 Tambahkan import file konfigurasi terpusat Anda di sini

class TutorService {
  // ✨ MODIFIKASI: Menggunakan baseUrl langsung dari AppConfig
  static const String baseUrl = AppConfig.baseUrl;

  static Future<http.Response> getTutorHistory(String token) async {
    return await http.get(
      Uri.parse('$baseUrl/tutor/history'), // Otomatis menembak ke http://3.107.184.92/api/tutor/history
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );
  }

  static Future<http.Response> submitTutor(Map<String, dynamic> data, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/tutor/submit'), // Otomatis menembak ke http://3.107.184.92/api/tutor/submit
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: jsonEncode(data),
    );
  }
}