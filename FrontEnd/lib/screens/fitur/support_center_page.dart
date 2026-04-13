import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart'; // Pastikan ini di-import

class SupportCenterPage extends StatelessWidget {
  const SupportCenterPage({super.key});

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
      appBar: AppBar(
        title: const Text("Pusat Bantuan",
            style: TextStyle(fontWeight: FontWeight.w900, color: Colors.white)),
        backgroundColor: const Color(0xFF990000),
        iconTheme: const IconThemeData(color: Colors.white), // Tombol back jadi putih
      ),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          const Text("Pertanyaan Populer",
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
          const SizedBox(height: 20),
          _buildFaqItem("Bagaimana cara akses tryout?"),
          _buildFaqItem("Cara download materi PDF?"),
          _buildFaqItem("Lupa kata sandi akun?"),
          const SizedBox(height: 30),

          // Tombol Chat Customer Service (Klik di sini untuk WA)
          InkWell(
            onTap: _openWhatsApp, // Panggil fungsi WA di sini
            borderRadius: BorderRadius.circular(20),
            child: Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.green.shade50, // Ubah warna ke hijau (identik WA)
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: Colors.green.shade200),
              ),
              child: const Row(
                children: [
                Icon(Icons.message, size: 40, color: Colors.green),// Icon diganti WhatsApp
                  SizedBox(width: 15),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text("Masih butuh bantuan?",
                            style: TextStyle(fontWeight: FontWeight.bold)),
                        Text("Chat Customer Service kami sekarang",
                            style: TextStyle(fontSize: 12, color: Colors.black54)),
                      ],
                    ),
                  ),
                  Icon(Icons.arrow_forward_ios, size: 16, color: Colors.green),
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
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: ListTile(
        title: Text(title, style: const TextStyle(fontSize: 14)),
        trailing: const Icon(Icons.chevron_right, color: Colors.grey),
        onTap: () {
          // Tambahkan logika detail FAQ jika perlu
        },
      ),
    );
  }
}