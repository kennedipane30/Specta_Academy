import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart'; // Import ini wajib

class ConsultationPage extends StatelessWidget {
  const ConsultationPage({super.key});

  // Fungsi untuk membuka WhatsApp
  Future<void> _openWhatsApp() async {
    const String phoneNumber = "6282235805348"; // Format 62...
    const String message = "Halo Admin Spekta, saya ingin berkonsultasi mengenai program bimbingan.";
    
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
      appBar: AppBar(
        title: const Text("Konsultasi Private", 
          style: TextStyle(fontWeight: FontWeight.w900, color: Colors.white)),
        backgroundColor: const Color(0xFF990000),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: Padding(
        padding: const EdgeInsets.all(30),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.forum_rounded, size: 100, color: Colors.purple),
            const SizedBox(height: 30),
            const Text("Butuh bimbingan khusus?", 
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            const Text("Jadwalkan sesi konsultasi 1-on-1 dengan pengajar ahli kami.", 
              textAlign: TextAlign.center, style: TextStyle(color: Colors.grey)),
            const SizedBox(height: 40),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.purple,
                padding: const EdgeInsets.symmetric(horizontal: 50, vertical: 15),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))
              ),
              onPressed: _openWhatsApp, // Panggil fungsi di sini
              child: const Text("MULAI KONSULTASI", 
                style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            )
          ],
        ),
      ),
    );
  }
}