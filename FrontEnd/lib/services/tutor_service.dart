import 'dart:convert';
import 'package:http/http.dart' as http;

class TutorService {
  static const String baseUrl = 'http://10.0.2.2:8000/api';

  // 1. Ambil Materi Kelas (Dropdown)
  static Future<http.Response> getTutorData(String token) async {
    return await http.get(
      Uri.parse('$baseUrl/tutor/form-data'),
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'},
    );
  }

  // 2. Kirim Pengajuan Dedicated Tutor
  static Future<http.Response> submitTutor(Map body, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/dedicated-tutors'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: jsonEncode(body),
    );
  }

  // 3. Ambil Riwayat Tutor
  static Future<http.Response> getTutorHistory(String token) async {
    return await http.get(
      Uri.parse('$baseUrl/dedicated-tutors'),
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'},
    );
  }
}