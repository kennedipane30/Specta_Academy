import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'reset_otp_page.dart';
import 'dart:convert';

class ForgotPasswordPage extends StatelessWidget {
  const ForgotPasswordPage({super.key});

  @override
  Widget build(BuildContext context) {
    final phoneCtrl = TextEditingController();
    const Color spektaRed = Color(0xFF990000);

    return Scaffold(
      appBar: AppBar(title: const Text("Lupa Password"), backgroundColor: spektaRed, foregroundColor: Colors.white),
      body: Padding(
        padding: const EdgeInsets.all(30),
        child: Column(
          children: [
            const Icon(Icons.lock_reset_rounded, size: 80, color: spektaRed),
            const SizedBox(height: 20),
            const Text("Masukkan nomor WhatsApp yang terdaftar untuk menerima kode reset.", textAlign: TextAlign.center),
            const SizedBox(height: 30),
            TextField(
              controller: phoneCtrl,
              keyboardType: TextInputType.phone,
              decoration: const InputDecoration(labelText: "Nomor WhatsApp", border: OutlineInputBorder()),
            ),
            const SizedBox(height: 30),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 50)),
              onPressed: () async {
                var resp = await AuthService.forgotPassword(phoneCtrl.text);
                if (resp.statusCode == 200) {
                  final data = jsonDecode(resp.body);
                  Navigator.push(context, MaterialPageRoute(builder: (_) => ResetOtpPage(phone: phoneCtrl.text, otpSimulasi: data['otp'].toString())));
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Nomor tidak ditemukan!")));
                }
              },
              child: const Text("KIRIM KODE OTP", style: TextStyle(color: Colors.white)),
            )
          ],
        ),
      ),
    );
  }
}