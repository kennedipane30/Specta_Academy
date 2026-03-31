import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;

class AuthService {
  static const String baseUrl = 'http://10.0.2.2:8000/api';

  // --- Fungsi Auth & Profile & Class Tetap ---
  static Future<http.Response> register(Map<String, dynamic> data) async => await http.post(Uri.parse('$baseUrl/register'), headers: {'Accept': 'application/json'}, body: data);
  static Future<http.Response> verifyRegistration(String name, String otp) async => await http.post(Uri.parse('$baseUrl/verify-registration'), headers: {'Accept': 'application/json'}, body: {'name': name, 'otp': otp});
  static Future<http.Response> login(String name, String password) async => await http.post(Uri.parse('$baseUrl/login'), headers: {'Accept': 'application/json'}, body: {'name': name, 'password': password});
  static Future<http.Response> updateProfile(Map<String, dynamic> data, String token) async => await http.post(Uri.parse('$baseUrl/update-profile'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'}, body: data);
  static Future<http.Response> getClassContent(int classId, String token) async => await http.post(Uri.parse('$baseUrl/class/content'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'}, body: {'class_id': classId.toString()});
  static Future<http.Response> checkClassStatus(int classId, String token) async => await http.post(Uri.parse('$baseUrl/class/check-status'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'}, body: {'class_id': classId.toString()});
  static Future<http.StreamedResponse> joinClass(int classId, String filePath, String token) async {
    var req = http.MultipartRequest('POST', Uri.parse('$baseUrl/class/join'))..headers.addAll({'Accept': 'application/json', 'Authorization': 'Bearer $token'})..fields['class_id'] = classId.toString()..files.add(await http.MultipartFile.fromPath('payment_proof', filePath));
    return await req.send();
  }
  static Future<http.Response> getSiswaSchedule(String token) async => await http.get(Uri.parse('$baseUrl/schedules'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'});

  // 8. AMBIL SOAL
  static Future<http.Response> getQuestions(int tryoutId, String token) async {
    return await http.post(Uri.parse('$baseUrl/tryout/questions'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'}, body: {'tryout_id': tryoutId.toString()});
  }

  // 9. SUBMIT JAWABAN (FIXED: JSON ENCODE)
  static Future<http.Response> submitTryout({required int tryoutId, required Map<int, String> answers, required String token}) async {
    Map<String, String> stringAnswers = answers.map((key, value) => MapEntry(key.toString(), value));
    return await http.post(Uri.parse('$baseUrl/tryout/submit'), headers: {'Accept': 'application/json', 'Content-Type': 'application/json', 'Authorization': 'Bearer $token'},
      body: jsonEncode({'tryout_id': tryoutId, 'answers': stringAnswers}));
  }
  // 1. Kirim nomor WA untuk minta kode OTP
static Future<http.Response> forgotPassword(String phone) async {
  return await http.post(
    Uri.parse('$baseUrl/forgot-password'),
    headers: {'Accept': 'application/json'},
    body: {'phone': phone},
  );
}

// 2. Kirim OTP dan Password Baru untuk Reset
static Future<http.Response> resetPassword(Map<String, dynamic> data) async {
  return await http.post(
    Uri.parse('$baseUrl/reset-password'),
    headers: {'Accept': 'application/json'},
    body: data,
  );
}
}