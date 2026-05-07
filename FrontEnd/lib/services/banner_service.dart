import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/banner_model.dart';

class BannerService {
  static const String baseUrl = 'http://10.0.2.2:8000/api';

  static Future<List<BannerModel>> getBanners() async {
    final response = await http.get(Uri.parse('$baseUrl/banners'));

    if (response.statusCode != 200) {
      throw Exception('Gagal mengambil banner');
    }

    final body = jsonDecode(response.body);
    final List data = body['data'];

    return data.map((item) => BannerModel.fromJson(item)).toList();
  }
}