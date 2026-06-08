import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';

class BannerDetailPage extends StatelessWidget {
  final Map bannerData;
  final String imageUrl;

  const BannerDetailPage({
    super.key,
    required this.bannerData,
    required this.imageUrl,
  });

  // Fungsi untuk membuka WhatsApp
  Future<void> _contactAdmin(BuildContext context) async {
    // ✨ GANTI DENGAN NOMOR WA ADMIN (Gunakan kode negara 62, tanpa 0 atau +)
    const String adminPhone = "6281234567890"; 
    
    final String title = bannerData['title'] ?? 'Promo/Informasi';
    final String message = "Halo Admin, saya ingin bertanya info lebih lanjut tentang promo: *$title* yang ada di aplikasi.";
    
    final Uri waUrl = Uri.parse("https://wa.me/$adminPhone?text=${Uri.encodeComponent(message)}");

    if (await canLaunchUrl(waUrl)) {
      await launchUrl(waUrl, mode: LaunchMode.externalApplication);
    } else {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Tidak dapat membuka WhatsApp.')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final title = bannerData['title'] ?? 'Informasi Detail';
    final description = bannerData['description'] ?? 'Tidak ada deskripsi lebih lanjut untuk informasi ini.';

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text('Detail Informasi', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 18)),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF172033),
        elevation: 0,
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (imageUrl.isNotEmpty)
              Image.network(
                imageUrl,
                width: double.infinity,
                fit: BoxFit.cover,
                errorBuilder: (context, error, stackTrace) => Container(
                  height: 200, color: Colors.grey.shade200,
                  child: const Center(child: Icon(Icons.broken_image, size: 50, color: Colors.grey)),
                ),
              ),
            
            Padding(
              padding: const EdgeInsets.all(22.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: Color(0xFF172033), height: 1.2),
                  ),
                  const SizedBox(height: 16),
                  Text(
                    description,
                    style: TextStyle(fontSize: 14, height: 1.6, color: Colors.grey.shade800),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.all(22),
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, -5))],
        ),
        child: ElevatedButton(
          onPressed: () => _contactAdmin(context),
          style: ElevatedButton.styleFrom(
            backgroundColor: const Color(0xFF25D366), // Warna WhatsApp
            padding: const EdgeInsets.symmetric(vertical: 16),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
            elevation: 0,
          ),
          child: const Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.wechat_rounded, color: Colors.white),
              SizedBox(width: 8),
              Text(
                "Hubungi Admin",
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: Colors.white),
              ),
            ],
          ),
        ),
      ),
    );
  }
}