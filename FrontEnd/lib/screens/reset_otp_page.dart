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
      backgroundColor: Colors.white,
      appBar: AppBar(title: const Text("Verifikasi OTP"), backgroundColor: Colors.white, foregroundColor: spektaRed, elevation: 0),
      body: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 40),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.mark_email_read_outlined, size: 80, color: spektaRed),
            const SizedBox(height: 30),
            Text("Kode Verifikasi", style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            Text("Masukkan kode yang dikirim ke\n${widget.phone}", textAlign: TextAlign.center, style: TextStyle(color: Colors.grey)),
            const SizedBox(height: 30),
            TextField(
              controller: otpCtrl,
              textAlign: TextAlign.center,
              style: const TextStyle(fontSize: 32, letterSpacing: 15, fontWeight: FontWeight.bold, color: spektaRed),
              keyboardType: TextInputType.number,
              maxLength: 4,
              decoration: InputDecoration(
                counterText: "",
                hintText: "0000",
                hintStyle: TextStyle(color: Colors.grey.shade300),
                enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: Colors.grey.shade300, width: 2)),
                focusedBorder: const UnderlineInputBorder(borderSide: BorderSide(color: spektaRed, width: 2)),
              ),
            ),
            const SizedBox(height: 20),
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(color: Colors.amber.shade50, borderRadius: BorderRadius.circular(10)),
              child: Text("SIMULASI: ${widget.otpSimulasi}", style: const TextStyle(color: Colors.amber, fontWeight: FontWeight.bold)),
            ),
            const SizedBox(height: 40),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 55), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
              onPressed: () {
                if (otpCtrl.text == widget.otpSimulasi) {
                  Navigator.push(context, MaterialPageRoute(builder: (_) => NewPasswordPage(phone: widget.phone, otp: otpCtrl.text)));
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.red, content: Text("Kode OTP Salah!")));
                }
              },
              child: const Text("VERIFIKASI KODE", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
            )
          ],
        ),
      ),
    );
  }
}