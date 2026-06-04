import 'dart:convert';
import 'package:http/http.dart' as http;

class QuestionBankService {
  // Gunakan 10.0.2.2 untuk emulator Android
  static const String baseUrl = 'http://10.0.2.2:8000/api';

  /**
   * Mengambil daftar semua soal dari Hub
   * Endpoint: GET /api/question-bank
   */
  static Future<http.Response> getAllQuestions(String token) async {
    return await http.get(
      Uri.parse('$baseUrl/question-bank'),
      headers: {
        'Authorization': 'Bearer $token', 
        'Accept': 'application/json'
      },
    );
  }

  /**
   * Mengunggah file PDF soal ke Hub
   * Endpoint: POST /api/question-bank/upload
   */
  static Future<http.StreamedResponse> uploadQuestion({
    required String title,
    required String subject,
    required String filePath,
    required String token,
  }) async {
    // Memanggil endpoint /api/question-bank/upload
    var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/question-bank/upload'));
    
    // Menambahkan Header
    request.headers.addAll({
      'Authorization': 'Bearer $token', 
      'Accept': 'application/json'
    });

    // Menambahkan Field Teks
    request.fields['title'] = title;
    request.fields['subject'] = subject;

    // Menambahkan File PDF
    // PENTING: Key 'file_pdf' harus sama dengan yang ada di Controller Laravel
    request.files.add(await http.MultipartFile.fromPath('file_pdf', filePath));

    // Mengirim permintaan
    return await request.send();
  }
}