import 'dart:convert';
import 'package:http/http.dart' as http;

class AuthService {
  // ============================
  // 🌐 ENDPOINT CONFIGURATION
  // ============================
  // Kita arahkan semua fitur utama ke Laravel (8000) sebagai API Gateway.
  // Pastikan Anda menggunakan 10.0.2.2 jika menjalankan di Emulator Android.
  static const String baseUrl = 'http://10.0.2.2:8000/api';        
  
  // URL Microservice tetap disimpan (Opsional)
  static const String materiBaseUrl = 'http://10.0.2.2:9001/api';  
  static const String tryoutBaseUrl = 'http://10.0.2.2:9002/api';  
  static const String practiceBaseUrl = 'http://10.0.2.2:9003/api';

  // ============================
  // 🔐 1. AUTHENTICATION (Laravel - Port 8000)
  // ============================

  static Future<http.Response> register(Map<String, dynamic> data) async {
    return await http.post(Uri.parse('$baseUrl/register'),
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

  static Future<http.Response> login(String name, String password) async {
    return await http.post(Uri.parse('$baseUrl/login'),
      headers: {'Accept': 'application/json'},
      body: {'name': name.trim(), 'password': password},
    );
  }

  static Future<http.Response> logout(String token) async {
    return await http.post(Uri.parse('$baseUrl/logout'),
      headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
    );
  }

  // ============================
  // 👤 2. USER PROFILE (Laravel - Port 8000)
  // ============================

  static Future<Map<String, dynamic>?> getUserProfile(String token) async {
    final response = await http.get(Uri.parse('$baseUrl/user'),
      headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
    );
    if (response.statusCode == 200) return jsonDecode(response.body);
    return null;
  }

  static Future<http.Response> updateProfile(Map<String, dynamic> data, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/update-profile'),
      headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
      body: data.map((key, value) => MapEntry(key, value.toString())),
    );
  }

  static Future<http.Response> getAllClasses(String token) async {
    return await http.get(Uri.parse('$baseUrl/classes'),
      headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
    );
  }

  // ==============================================
  // 📚 3. CONTENT GATEWAY (Laravel Port 8000)
  // ==============================================

  // Mengambil Konten Kelas (Materi, Tryout, Practice) sekaligus dari Gateway
  static Future<http.Response> getClassContent(int classId, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/class/content'), 
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json', 
        'Authorization': 'Bearer $token'
      },
      body: jsonEncode({'class_id': classId}),
    );
  }

  // Mengambil daftar materi (Jika dipanggil secara spesifik)
  static Future<http.Response> getPracticeData(int classId, String token) async {
    // Diarahkan ke Laravel Port 8000 Gateway
    return await http.get(
      Uri.parse('$baseUrl/materials?class_id=$classId'), 
      headers: {
        'Accept': 'application/json', 
        'Authorization': 'Bearer $token'
      },
    );
  }

  // Mengambil daftar Tryout
  static Future<http.Response> getTryoutList(int classId, String token) async {
    // Karena Laravel Gateway mengembalikan data tryout di getClassContent, 
    // kita gunakan endpoint yang sama agar konsisten.
    return await http.post(
      Uri.parse('$baseUrl/class/content'), 
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json', 
        'Authorization': 'Bearer $token'
      },
      body: jsonEncode({'class_id': classId}),
    );
  }

  // Mengambil Pertanyaan Tryout
  static Future<http.Response> getQuestions(int tryoutId, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/tryout/questions'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token'
      },
      body: jsonEncode({'tryout_id': tryoutId}),
    );
  }

  // Submit Hasil Tryout
  static Future<http.Response> submitTryout({
    required int tryoutId,
    required Map<int, String> answers,
    required String token
  }) async {
    // Konversi key int ke string agar bisa dikirim sebagai JSON yang valid
    Map<String, String> stringAnswers = answers.map((key, value) => MapEntry(key.toString(), value));
    
    return await http.post(
      Uri.parse('$baseUrl/tryout/submit'),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token'
      },
      body: jsonEncode({
        'tryout_id': tryoutId, 
        'answers': stringAnswers
      }),
    );
  }

  // Report Pembelajaran
  static Future<http.Response> getLearningReport(String token) async {
    return await http.get(
      Uri.parse('$baseUrl/learning-report'),
      headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
    );
  }

  // ============================
  // 🏷️ 6. OTHER UTILITIES (Laravel - Port 8000)
  // ============================

  static Future<http.StreamedResponse> joinClass(int classId, String filePath, String token) async {
    var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/class/join'));
    request.headers.addAll({'Accept': 'application/json', 'Authorization': 'Bearer $token'});
    request.fields['class_id'] = classId.toString();
    request.files.add(await http.MultipartFile.fromPath('payment_proof', filePath));
    return await request.send();
  }

  static Future<http.Response> getAnnouncements(String token) async => 
    await http.get(Uri.parse('$baseUrl/announcements'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'});

  static Future<http.Response> checkPromoCode(String code, int classId, int price, String token) async => 
    await http.post(Uri.parse('$baseUrl/promo/check'), 
      headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'}, 
      body: {'code': code.trim(), 'class_id': classId.toString(), 'price': price.toString()}
    );

  static Future<http.Response> getTutorData(String token) async => 
    await http.get(Uri.parse('$baseUrl/tutor/form-data'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'});

  static Future<http.Response> getTutorHistory(String token) async => 
    await http.get(Uri.parse('$baseUrl/tutor/history'), headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'});

  static Future<http.Response> submitTutor(Map data, String token) async => 
    await http.post(Uri.parse('$baseUrl/tutor/submit'), 
      headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'}, 
      body: data.map((key, value) => MapEntry(key, value.toString()))
    );

  static Future<http.Response> forgotPassword(String email) async => 
    await http.post(Uri.parse('$baseUrl/forgot-password'), headers: {'Accept': 'application/json'}, body: {'email': email.trim()});

  static Future<http.Response> resetPassword(Map<String, dynamic> data) async => 
    await http.post(Uri.parse('$baseUrl/reset-password'), headers: {'Accept': 'application/json'}, body: data.map((key, value) => MapEntry(key, value.toString())));
}