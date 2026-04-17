import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;

class AuthService {
  // Gunakan 10.0.2.2 untuk Emulator Android agar bisa akses localhost laptop
  static const String baseUrl = 'http://10.0.2.2:8000/api';

  // 1. REGISTRASI SISWA
  static Future<http.Response> register(Map<String, dynamic> data) async {
    return await http.post(
      Uri.parse('$baseUrl/register'), 
      headers: {'Accept': 'application/json'}, 
      body: data.map((key, value) => MapEntry(key, value.toString())),
    );
  }

  // 2. VERIFIKASI REGISTRASI (OTP AKTIVASI)
  static Future<http.Response> verifyRegistration(String name, String otp) async {
    return await http.post(
      Uri.parse('$baseUrl/verify-registration'), 
      headers: {'Accept': 'application/json'}, 
      body: {'name': name.trim(), 'otp': otp.trim()},
    );
  }

  // 🔥 3. KIRIM ULANG OTP (RESEND OTP)
  static Future<http.Response> resendOtp(String name) async {
    return await http.post(
      Uri.parse('$baseUrl/resend-otp'),
      headers: {'Accept': 'application/json'},
      body: {'name': name.trim()}, // Memastikan tidak ada spasi yang terbawa
    );
  }

  // 4. LOGIN SISWA
  static Future<http.Response> login(String name, String password) async {
    return await http.post(
      Uri.parse('$baseUrl/login'), 
      headers: {'Accept': 'application/json'}, 
      body: {'name': name.trim(), 'password': password},
    );
  }

  // 5. AMBIL PROFIL USER
  static Future<Map<String, dynamic>?> getUserProfile(String token) async {
    final response = await http.get(
      Uri.parse('$baseUrl/user'), 
      headers: {
        'Accept': 'application/json', 
        'Authorization': 'Bearer $token'
      },
    );
    if (response.statusCode == 200) return jsonDecode(response.body);
    return null;
  }

  // 6. LENGKAPI PROFIL
  static Future<http.Response> updateProfile(Map<String, dynamic> data, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/update-profile'), 
      headers: {
        'Accept': 'application/json', 
        'Authorization': 'Bearer $token'
      }, 
      body: data.map((key, value) => MapEntry(key, value.toString())),
    );
  }

  // 7. AMBIL KONTEN MATERI
  static Future<http.Response> getClassContent(int classId, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/class/content'), 
      headers: {
        'Accept': 'application/json', 
        'Authorization': 'Bearer $token'
      }, 
      body: {'class_id': classId.toString()},
    );
  }

  // 8. CEK STATUS PENDAFTARAN
  static Future<http.Response> checkClassStatus(int classId, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/class/check-status'), 
      headers: {
        'Accept': 'application/json', 
        'Authorization': 'Bearer $token'
      }, 
      body: {'class_id': classId.toString()},
    );
  }

  // 9. DAFTARKAN SISWA (Upload Bukti Bayar)
  static Future<http.StreamedResponse> joinClass(int classId, String filePath, String token) async {
    var req = http.MultipartRequest('POST', Uri.parse('$baseUrl/class/join'))
      ..headers.addAll({
        'Accept': 'application/json', 
        'Authorization': 'Bearer $token'
      })
      ..fields['class_id'] = classId.toString()
      ..files.add(await http.MultipartFile.fromPath('payment_proof', filePath));
    return await req.send();
  }

  // 10. AMBIL BANNER PROMO
  static Future<http.Response> getActivePromos() async {
    return await http.get(
      Uri.parse('$baseUrl/promos'), 
      headers: {'Accept': 'application/json'},
    );
  }

  // 11. CEK KODE PROMO
  static Future<http.Response> checkPromoCode(String code, int classId, int price, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/promo/check'), 
      headers: {
        'Accept': 'application/json', 
        'Authorization': 'Bearer $token'
      }, 
      body: {
        'code': code.trim(), 
        'class_id': classId.toString(), 
        'price': price.toString()
      },
    );
  }

  // 12. DAFTAR KELAS JALUR PROMO
  static Future<http.StreamedResponse> joinClassPromo({
    required int classId,
    required String promoCode,
    required String filePath,
    required String token,
  }) async {
    var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/class/join-promo'));
    request.headers.addAll({
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    });
    request.fields['class_id'] = classId.toString();
    request.fields['promo_code'] = promoCode.trim();
    request.files.add(await http.MultipartFile.fromPath('payment_proof', filePath));
    return await request.send();
  }

  // 13. AMBIL JADWAL
  static Future<http.Response> getSiswaSchedule(String token) async {
    return await http.get(
      Uri.parse('$baseUrl/schedules'), 
      headers: {
        'Accept': 'application/json', 
        'Authorization': 'Bearer $token'
      },
    );
  }

  // 14. AMBIL SOAL TRYOUT
  static Future<http.Response> getQuestions(int tryoutId, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/tryout/questions'), 
      headers: {
        'Accept': 'application/json', 
        'Authorization': 'Bearer $token'
      }, 
      body: {'tryout_id': tryoutId.toString()},
    );
  }

  // 15. SUBMIT TRYOUT
  static Future<http.Response> submitTryout({
    required int tryoutId, 
    required Map<int, String> answers, 
    required String token
  }) async {
    Map<String, String> stringAnswers = answers.map((key, value) => MapEntry(key.toString(), value));
    return await http.post(
      Uri.parse('$baseUrl/tryout/submit'), 
      headers: {
        'Accept': 'application/json', 
        'Content-Type': 'application/json', 
        'Authorization': 'Bearer $token'
      }, 
      body: jsonEncode({'tryout_id': tryoutId, 'answers': stringAnswers}),
    );
  }

  // 16. FORGOT PASSWORD (EMAIL)
  static Future<http.Response> forgotPassword(String email) async {
    return await http.post(
      Uri.parse('$baseUrl/forgot-password'), 
      headers: {'Accept': 'application/json'}, 
      body: {'email': email.trim()},
    );
  }

  // 17. RESET PASSWORD
  static Future<http.Response> resetPassword(Map<String, dynamic> data) async {
    return await http.post(
      Uri.parse('$baseUrl/reset-password'), 
      headers: {'Accept': 'application/json'}, 
      body: data.map((key, value) => MapEntry(key, value.toString())),
    );
  }

  // 18. LOGOUT
  static Future<http.Response> logout(String token) async {
    return await http.post(
      Uri.parse('$baseUrl/logout'), 
      headers: {
        'Accept': 'application/json', 
        'Authorization': 'Bearer $token'
      },
    );
  }
}