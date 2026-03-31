import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'login_page.dart'; // Import halaman login
import 'dart:convert';

class OtpPage extends StatefulWidget {
  final String name;
  final String otpSimulasi;

  const OtpPage({super.key, required this.name, required this.otpSimulasi});

  @override
  State<OtpPage> createState() => _OtpPageState();
}

class _OtpPageState extends State<OtpPage> {
  final TextEditingController otpCtrl = TextEditingController();
  final Color spektaRed = const Color(0xFF990000);
  String currentText = "";

  void handleVerify() async {
    if (otpCtrl.text.length < 6) return;

    // Tampilkan Loading
    showDialog(context: context, barrierDismissible: false, builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000))));

    // Panggil API Verifikasi Registrasi
    var resp = await AuthService.verifyRegistration(widget.name, otpCtrl.text);

    if (!mounted) return;
    Navigator.pop(context); // Tutup Loading

    if (resp.statusCode == 200) {
      // 1. Tampilkan Notifikasi Sukses
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          backgroundColor: Colors.green, 
          content: Text("Akun Berhasil Diaktifkan! Silakan Login menggunakan Nama & Password.")
        )
      );

      // 2. ARAHKAN LANGSUNG KE HALAMAN LOGIN
      // Kita hapus semua tumpukan halaman (stack) agar siswa tidak bisa kembali ke OTP
      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (context) => const LoginPage()),
        (route) => false,
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(backgroundColor: Colors.red, content: Text("Kode OTP Salah!"))
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Aktivasi Akun"),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(30),
        child: Column(
          children: [
            const SizedBox(height: 20),
            const Icon(Icons.verified_user_outlined, size: 80, color: Color(0xFF990000)),
            const SizedBox(height: 30),
            Text(
              "Halo ${widget.name}, masukkan kode OTP yang dikirim ke WhatsApp Anda:",
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.grey[700], fontSize: 16),
            ),
            const SizedBox(height: 10),
            Text(
              "SIMULASI: ${widget.otpSimulasi}",
              style: const TextStyle(color: Colors.red, fontWeight: FontWeight.bold, fontSize: 18),
            ),
            const SizedBox(height: 50),
            
            // Input OTP Kotak-kotak Merah
            TextField(
              controller: otpCtrl,
              textAlign: TextAlign.center,
              keyboardType: TextInputType.number,
              maxLength: 6,
              style: const TextStyle(fontSize: 32, letterSpacing: 20, fontWeight: FontWeight.bold, color: Color(0xFF990000)),
              decoration: const InputDecoration(
                hintText: "000000",
                counterText: "",
                enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: Colors.grey)),
                focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: Color(0xFF990000), width: 2)),
              ),
            ),
            
            const SizedBox(height: 60),
            
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed,
                minimumSize: const Size(double.infinity, 55),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
              ),
              onPressed: handleVerify,
              child: const Text("VERIFIKASI & AKTIFKAN", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      ),
    );
  }
}