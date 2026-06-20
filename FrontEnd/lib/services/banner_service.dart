import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/banner_model.dart';
import '../config/app_config.dart '; // 👈 Tambahkan import file konfigurasi terpusat Anda di sini

class BannerService {
  // ✨ MODIFIKASI: Menggunakan baseUrl langsung dari AppConfig
  static const String baseUrl = AppConfig.baseUrl;

  // ✨ Parameter token dan header tetap dipertahankan
  static Future<List<BannerModel>> getBanners(String token) async {
    final response = await http.get(
      Uri.parse('$baseUrl/banners'), // Otomatis menembak ke http://3.107.184.92/api/banners
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