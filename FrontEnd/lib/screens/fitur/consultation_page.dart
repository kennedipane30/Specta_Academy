import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart'; // Import ini wajib

class ConsultationPage extends StatelessWidget {
  const ConsultationPage({super.key});

  // ============================================================
  // 🎨 PALET WARNA BARU SPEKTA GEN-Z (KONTRAS TINGGI, CLEAN, PREMIUM)
  // ============================================================
  static const Color primaryRed = Color(0xFFC5352C);       // Merah Spekta
  static const Color brightRed = Color(0xFFE53935);        // Aksen Merah Terang
  static const Color accentTeal = Color(0xFF2EA8AB);       // Teal Estetik
  static const Color pageBg = Color(0xFFF8FAFC);           // Slate 50 (Abu Terang Bersih)
  static const Color textDark = Color(0xFF0F172A);         // Slate 900
  static const Color textDarkVariant = Color(0xFF334155);  // Slate 700
  static const Color neutralGray = Color(0xFF64748B);      // Slate 500
  static const Color outlineVariant = Color(0xFFE2E8F0);   // Border Abu Halus
  static const Color lightBlueBg = Color(0xFFEFF4FF);      // Latar Ikon

  // Fungsi untuk membuka WhatsApp (Fokus Pembelian & Promo)
  Future<void> _openWhatsApp() async {
    const String phoneNumber = "6282235805348"; // Format 62...
    const String message = "Halo Admin Spekta, saya ingin bertanya mengenai pembelian kelas premium atau informasi promo aktif terbaru.";
    
    // Gunakan format https://wa.me/
    final Uri whatsappUri = Uri.parse("https://wa.me/$phoneNumber?text=${Uri.encodeComponent(message)}");

    try {
      if (await canLaunchUrl(whatsappUri)) {
        await launchUrl(
          whatsappUri,
          mode: LaunchMode.externalApplication, // Membuka aplikasi luar (WhatsApp)
        );
      } else {
        // Jika WhatsApp tidak terinstall, buka di browser
        await launchUrl(whatsappUri, mode: LaunchMode.platformDefault);
      }
    } catch (e) {
      debugPrint("Error: $e");
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: pageBg,
      appBar: AppBar(
        title: const Text(
          "Bantuan & Promo", 
          style: TextStyle(
            fontWeight: FontWeight.w900, 
            color: Colors.white,
            letterSpacing: -0.5,
            fontSize: 18,
          ),
        ),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
        leading: Padding(
          padding: const EdgeInsets.all(8.0),
          child: CircleAvatar(
            backgroundColor: Colors.white.withOpacity(0.15),
            child: IconButton(
              icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white, size: 16),
              onPressed: () => Navigator.pop(context),
            ),
          ),
        ),
        flexibleSpace: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [primaryRed, brightRed],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.vertical(
              bottom: Radius.circular(20),
            ),
          ),
        ),
      ),
      body: SingleChildScrollView(
        physics: const BouncingScrollPhysics(),
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
        child: Column(
          children: [
            // --- KARTU UTAMA BANTUAN PEMBELIAN & PROMO ---
            Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(24),
                border: Border.all(color: outlineVariant.withOpacity(0.4)),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.02),
                    blurRadius: 16,
                    offset: const Offset(0, 6),
                  )
                ],
              ),
              child: Column(
                children: [
                  // Icon badge menyala
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: accentTeal.withOpacity(0.08),
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(
                      Icons.shopping_bag_rounded, 
                      size: 64, 
                      color: accentTeal,
                    ),
                  ),
                  const SizedBox(height: 20),
                  const Text(
                    "Butuh Bantuan Pembelian?",
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w900,
                      color: textDark,
                      letterSpacing: -0.4,
                    ),
                  ),
                  const SizedBox(height: 8),
                  const Text(
                    "Hubungi Customer Service kami untuk bantuan aktivasi kelas premium, kendala pembayaran paket bimbingan, atau menanyakan informasi kode promo diskon terbaru.",
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      color: textDarkVariant,
                      fontSize: 12.5,
                      fontWeight: FontWeight.w600,
                      height: 1.5,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 28),

            // --- ALUR LANGKAH MUDAH KONSULTASI PEMBELIAN ---
            _buildSectionHeader("Alur Pembelian & Klaim Promo", "🚀"),
            const SizedBox(height: 14),
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: outlineVariant.withOpacity(0.4)),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.015),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  )
                ],
              ),
              child: Column(
                children: [
                  _buildStepItem(
                    stepNumber: "1",
                    title: "Pilih Program atau Kode Promo",
                    description: "Tentukan program bimbingan belajar premium pilihanmu atau kumpulkan kode promo diskon aktif yang ingin kamu klaim.",
                    color: accentTeal,
                  ),
                  const Divider(height: 28, color: outlineVariant),
                  _buildStepItem(
                    stepNumber: "2",
                    title: "Konsultasikan dengan Admin",
                    description: "Hubungi Admin kami via WhatsApp untuk kendala aktivasi, konfirmasi slip pembayaran, atau klaim potongan harga khusus.",
                    color: primaryRed,
                  ),
                  const Divider(height: 28, color: outlineVariant),
                  _buildStepItem(
                    stepNumber: "3",
                    title: "Aktivasi Instan & Mulai Belajar",
                    description: "Setelah pembayaran terverifikasi, akses kelas belajarmu akan diaktifkan instan oleh tim admin sehingga kamu bisa langsung belajar.",
                    color: Colors.orange,
                  ),
                ],
              ),
            ),
            const SizedBox(height: 32),

            // --- TOMBOL WHATSAPP MENYALA (VIBRANT WHATSAPP CTA) ---
            Container(
              width: double.infinity,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: const Color(0xFF25D366).withOpacity(0.25), // Efek glowing hijau WhatsApp
                    blurRadius: 12,
                    offset: const Offset(0, 4),
                  )
                ],
              ),
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF25D366), // WhatsApp Green
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  elevation: 0,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                ),
                onPressed: _openWhatsApp, 
                child: const Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.chat_bubble_rounded, color: Colors.white, size: 18),
                    SizedBox(width: 10),
                    Text(
                      "KONSULTASI PEMBELIAN SEKARANG", 
                      style: TextStyle(
                        color: Colors.white, 
                        fontWeight: FontWeight.w900,
                        letterSpacing: 0.5,
                        fontSize: 13,
                      ),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }

  // WIDGET JUDUL SEKSI DENGAN AKSEN EMOJI
  Widget _buildSectionHeader(String title, String emoji) {
    return Row(
      children: [
        Text(emoji, style: const TextStyle(fontSize: 18)),
        const SizedBox(width: 8),
        Text(
          title,
          style: const TextStyle(
            fontSize: 16, 
            fontWeight: FontWeight.w900, 
            color: textDark,
            letterSpacing: -0.4,
          ),
        ),
      ],
    );
  }

  // Helper widget untuk list alur bimbingan (timeline steps)
  Widget _buildStepItem({
    required String stepNumber,
    required String title,
    required String description,
    required Color color,
  }) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          width: 24,
          height: 24,
          decoration: BoxDecoration(
            color: color,
            shape: BoxShape.circle,
          ),
          child: Center(
            child: Text(
              stepNumber,
              style: const TextStyle(
                color: Colors.white,
                fontSize: 11,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ),
        const SizedBox(width: 14),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: const TextStyle(
                  fontSize: 13.5,
                  fontWeight: FontWeight.w900,
                  color: textDark,
                ),
              ),
              const SizedBox(height: 3),
              Text(
                description,
                style: const TextStyle(
                  fontSize: 11.5,
                  color: neutralGray,
                  fontWeight: FontWeight.bold,
                  height: 1.4,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}