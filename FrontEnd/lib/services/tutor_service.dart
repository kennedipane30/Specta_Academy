import 'dart:convert';
import 'package:http/http.dart' as http;

class TutorService {
  static const String baseUrl = 'http://10.0.2.2:8000/api';

  static Future<http.Response> getTutorData(String token) async {
    return await http.get(Uri.parse('$baseUrl/dedicated-tutor/data'), 
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'});
  }

  static Future<http.Response> submitTutor(Map<String, dynamic> data, String token) async {
    return await http.post(Uri.parse('$baseUrl/dedicated-tutor/store'), 
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'}, body: data);
  }
}