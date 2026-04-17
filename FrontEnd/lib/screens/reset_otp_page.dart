import 'package:flutter/material.dart';
import 'new_password_page.dart';

class ResetOtpPage extends StatefulWidget {
  final String email;

  const ResetOtpPage({super.key, required this.email});

  @override
  State<ResetOtpPage> createState() => _ResetOtpPageState();
}

class _ResetOtpPageState extends State<ResetOtpPage> {
  final otpCtrl = TextEditingController();
  final Color spektaRed = const Color(0xFF990000); // Ubah dari const ke final

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
          title: const Text("OTP Verification"),
          backgroundColor: Colors.white,
          foregroundColor: spektaRed,
          elevation: 0),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 40),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const SizedBox(height: 50),
            // Hapus 'const' di depan Icon karena menggunakan variabel spektaRed
            Icon(Icons.mark_email_read_outlined, size: 80, color: spektaRed),
            const SizedBox(height: 30),
            const Text("Verification Code",
                style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            RichText(
              textAlign: TextAlign.center,
              text: TextSpan(
                style: const TextStyle(color: Colors.grey, fontSize: 14),
                children: [
                  const TextSpan(text: "Please enter the 6-digit code sent to\n"),
                  TextSpan(
                    text: widget.email,
                    style: const TextStyle(
                        color: Colors.black, fontWeight: FontWeight.bold),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 50),
            TextField(
              controller: otpCtrl,
              textAlign: TextAlign.center,
              style: TextStyle( // Hapus 'const'
                  fontSize: 32,
                  letterSpacing: 10,
                  fontWeight: FontWeight.bold,
                  color: spektaRed),
              keyboardType: TextInputType.number,
              maxLength: 6,
              decoration: InputDecoration(
                counterText: "",
                hintText: "000000",
                hintStyle: TextStyle(color: Colors.grey.shade300),
                enabledBorder: UnderlineInputBorder(
                    borderSide: BorderSide(color: Colors.grey.shade300, width: 2)),
                focusedBorder: UnderlineInputBorder( // Hapus 'const'
                    borderSide: BorderSide(color: spektaRed, width: 2)),
              ),
            ),
            const SizedBox(height: 60),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed,
                minimumSize: const Size(double.infinity, 55),
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(15)),
              ),
              onPressed: () {
                if (otpCtrl.text.length < 6) {
                  ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
                      backgroundColor: Colors.orange,
                      content: Text("Please enter 6 digits code")));
                  return;
                }
                
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => NewPasswordPage(
                      email: widget.email, // Sekarang sudah sinkron
                      otp: otpCtrl.text,
                    ),
                  ),
                );
              },
              child: const Text("VERIFY CODE",
                  style: TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                      fontSize: 16)),
            ),
            const SizedBox(height: 20),
            TextButton(
              onPressed: () {
                // Resend OTP logic
              },
              child: Text("Resend Code", // Hapus 'const'
                  style: TextStyle(color: spektaRed, fontWeight: FontWeight.bold)),
            )
          ],
        ),
      ),
    );
  }
}