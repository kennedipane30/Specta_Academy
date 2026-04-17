import 'dart:convert';
import 'package:http/http.dart' as http;

class QuestionBankService {
  static const String baseUrl = 'http://10.0.2.2:8000/api';

  static Future<http.Response> getAllQuestions(String token) async {
    return await http.get(
      Uri.parse('$baseUrl/question-bank'),
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'},
    );
  }

  static Future<http.StreamedResponse> uploadQuestion({
    required String title,
    required String subject,
    required String filePath,
    required String token,
  }) async {
    var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/question-bank/upload'));
    request.headers.addAll({'Authorization': 'Bearer $token', 'Accept': 'application/json'});
    request.fields['title'] = title;
    request.fields['subject'] = subject;
    request.files.add(await http.MultipartFile.fromPath('file_pdf', filePath));
    return await request.send();
  }
}