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

  // ============================================================
  // 🎨 PALET WARNA SPEKTA (KONSISTEN DENGAN TRYOUTDETAILPAGE)
  // ============================================================
  static const Color primaryRed      = Color(0xFFC5352C);
  static const Color accentTeal      = Color(0xFF2EA8AB);
  static const Color darkTeal        = Color(0xFF00696C);
  static const Color lightBlueBg     = Color(0xFFEFF4FF);
  static const Color pageBg          = Color(0xFFF1F5F9);
  static const Color textDark        = Color(0xFF0F172A);
  static const Color textDarkVariant = Color(0xFF334155);
  static const Color neutralGray     = Color(0xFF64748B);
  static const Color outlineVariant  = Color(0xFFE2BEBA);
  static const Color spektaYellow    = Color(0xFFF5A623);

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
          SnackBar(
            content: const Text('Tidak dapat membuka WhatsApp.'),
            backgroundColor: primaryRed,
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final title = bannerData['title'] ?? 'Informasi Detail';
    final description = bannerData['description'] ?? 'Tidak ada deskripsi lebih lanjut untuk informasi ini.';

    return Scaffold(
      backgroundColor: pageBg,
      appBar: AppBar(
        flexibleSpace: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [primaryRed, accentTeal],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
        ),
        title: const Text(
          'Detail Informasi', 
          style: TextStyle(fontWeight: FontWeight.w800, fontSize: 18, color: Colors.white),
        ),
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
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
                  height: 200, 
                  color: Colors.grey.shade200,
                  child: Center(
                    child: Icon(Icons.broken_image, size: 50, color: neutralGray),
                  ),
                ),
                loadingBuilder: (context, child, loadingProgress) {
                  if (loadingProgress == null) return child;
                  return Container(
                    height: 250,
                    width: double.infinity,
                    color: Colors.grey[200],
                    child: const Center(
                      child: CircularProgressIndicator(strokeWidth: 2, color: accentTeal),
                    ),
                  );
                },
              ),
            
            Padding(
              padding: const EdgeInsets.all(22.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: const TextStyle(
                      fontSize: 22, 
                      fontWeight: FontWeight.w900, 
                      color: textDark, 
                      height: 1.2,
                    ),
                  ),
                  const SizedBox(height: 16),
                  Text(
                    description,
                    style: TextStyle(
                      fontSize: 14, 
                      height: 1.6, 
                      color: textDarkVariant,
                    ),
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
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05), 
              blurRadius: 10, 
              offset: const Offset(0, -5),
            ),
          ],
        ),
        child: ElevatedButton(
          onPressed: () => _contactAdmin(context),
          style: ElevatedButton.styleFrom(
            backgroundColor: accentTeal, // Warna TEAL seperti gambar "LIHAT PEMBAHASAN"
            padding: const EdgeInsets.symmetric(vertical: 16),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
            elevation: 0,
            minimumSize: const Size(double.infinity, 52),
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