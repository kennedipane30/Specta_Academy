import 'package:flutter/material.dart';
import 'new_password_page.dart';

class ResetOtpPage extends StatefulWidget {
  final String phone;
  final String otpSimulasi;
  const ResetOtpPage({super.key, required this.phone, required this.otpSimulasi});

  @override State<ResetOtpPage> createState() => _ResetOtpPageState();
}

class _ResetOtpPageState extends State<ResetOtpPage> {
  final otpCtrl = TextEditingController();

  @override Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);
    return Scaffold(
      appBar: AppBar(title: const Text("Verifikasi OTP"), backgroundColor: spektaRed, foregroundColor: Colors.white),
      body: Padding(
        padding: const EdgeInsets.all(30),
        child: Column(
          children: [
            Text("Masukkan kode yang dikirim ke ${widget.phone}"),
            const SizedBox(height: 10),
            Text("SIMULASI: ${widget.otpSimulasi}", style: const TextStyle(color: Colors.red, fontWeight: FontWeight.bold)),
            TextField(
              controller: otpCtrl,
              textAlign: TextAlign.center,
              style: const TextStyle(fontSize: 30, letterSpacing: 10),
              keyboardType: TextInputType.number,
            ),
            const SizedBox(height: 30),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 50)),
              onPressed: () {
                if (otpCtrl.text == widget.otpSimulasi) {
                  Navigator.push(context, MaterialPageRoute(builder: (_) => NewPasswordPage(phone: widget.phone, otp: otpCtrl.text)));
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Kode OTP Salah!")));
                }
              },
              child: const Text("VERIFIKASI KODE", style: TextStyle(color: Colors.white)),
            )
          ],
        ),
      ),
    );
  }
}