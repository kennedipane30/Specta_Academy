import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart'; // Pastikan ini di-import

class SupportCenterPage extends StatelessWidget {
  const SupportCenterPage({super.key});

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

  // Fungsi untuk membuka WhatsApp
  Future<void> _openWhatsApp() async {
    const String phoneNumber = "6282235805348";
    const String message = "Halo Admin Spekta, saya butuh bantuan mengenai aplikasi.";
    final Uri whatsappUri = Uri.parse("https://wa.me/$phoneNumber?text=${Uri.encodeComponent(message)}");

    try {
      if (await canLaunchUrl(whatsappUri)) {
        await launchUrl(whatsappUri, mode: LaunchMode.externalApplication);
      } else {
        await launchUrl(whatsappUri, mode: LaunchMode.platformDefault);
      }
    } catch (e) {
      debugPrint("Gagal membuka WhatsApp: $e");
    }
  }

  @override
  Widget build(BuildContext context) {
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
          "Pusat Bantuan",
          style: TextStyle(fontWeight: FontWeight.w900, color: Colors.white, fontSize: 17),
        ),
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          const Text(
            "Pertanyaan Populer",
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: textDark),
          ),
          const SizedBox(height: 20),
          _buildFaqItem("Bagaimana cara akses tryout?"),
          _buildFaqItem("Cara download materi PDF?"),
          _buildFaqItem("Lupa kata sandi akun?"),
          const SizedBox(height: 30),

          // Tombol Chat Customer Service (Klik di sini untuk WA)
          InkWell(
            onTap: _openWhatsApp,
            borderRadius: BorderRadius.circular(20),
            child: Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: accentTeal.withOpacity(0.08),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: accentTeal.withOpacity(0.3)),
              ),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: accentTeal,
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: const Icon(Icons.message, size: 24, color: Colors.white),
                  ),
                  const SizedBox(width: 15),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          "Masih butuh bantuan?",
                          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: textDark),
                        ),
                        Text(
                          "Chat Customer Service kami sekarang",
                          style: TextStyle(fontSize: 12, color: neutralGray),
                        ),
                      ],
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: accentTeal.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: const Icon(Icons.arrow_forward_ios, size: 14, color: accentTeal),
                  ),
                ],
              ),
            ),
          )
        ],
      ),
    );
  }

  Widget _buildFaqItem(String title) {
    return Card(
      margin: const EdgeInsets.only(bottom: 10),
      elevation: 0,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      color: Colors.white,
      child: ListTile(
        title: Text(
          title, 
          style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: textDark),
        ),
        trailing: Container(
          padding: const EdgeInsets.all(6),
          decoration: BoxDecoration(
            color: accentTeal.withOpacity(0.1),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Icon(Icons.chevron_right, size: 16, color: accentTeal),
        ),
        onTap: () {
          // Tambahkan logika detail FAQ jika perlu
        },
      ),
    );
  }
}