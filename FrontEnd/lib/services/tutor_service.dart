import 'dart:convert';
import 'package:http/http.dart' as http;

class TutorService {
  static const String baseUrl = 'http://10.0.2.2:8000/api';

  static Future<http.Response> getTutorData(String token) async {
    return await http.get(
      Uri.parse('$baseUrl/tutor/form-data'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );
  }

  static Future<http.Response> getTutorHistory(String token) async {
    return await http.get(
      Uri.parse('$baseUrl/tutor/history'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );
  }

  static Future<http.Response> submitTutor(Map data, String token) async {
    return await http.post(
      Uri.parse('$baseUrl/tutor/submit'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: data.map((key, value) => MapEntry(key, value.toString())),
    );
  }
}