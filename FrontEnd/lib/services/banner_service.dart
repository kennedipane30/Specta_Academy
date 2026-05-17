import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/banner_model.dart';

class BannerService {
  static const String baseUrl = 'http://10.0.2.2:8000/api';

  // ✨ MODIFIKASI: Tambahkan parameter token dan header
  static Future<List<BannerModel>> getBanners(String token) async {
    final response = await http.get(
      Uri.parse('$baseUrl/banners'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );

    if (response.statusCode != 200) {
      throw Exception('Gagal mengambil banner: ${response.statusCode}');
    }

    final body = jsonDecode(response.body);
    // Sesuaikan key 'data' sesuai response controller Laravel Anda
    final List data = body['data'] ?? body['banners'] ?? [];

    return data.map((item) => BannerModel.fromJson(item)).toList();
  }
}