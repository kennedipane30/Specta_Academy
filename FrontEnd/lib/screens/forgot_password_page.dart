import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'reset_otp_page.dart';
import 'dart:convert'; // Dibutuhkan untuk jsonDecode

class ForgotPasswordPage extends StatelessWidget {
  const ForgotPasswordPage({super.key});

  @override
  Widget build(BuildContext context) {
    final emailCtrl = TextEditingController();
    const Color spektaRed = Color(0xFF990000);

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
          title: const Text("Forgot Password"),
          backgroundColor: Colors.white,
          foregroundColor: spektaRed,
          elevation: 0),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 35, vertical: 40),
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(25),
              decoration: BoxDecoration(
                  color: spektaRed.withOpacity(0.1), shape: BoxShape.circle),
              child: const Icon(Icons.lock_reset_rounded,
                  size: 80, color: spektaRed),
            ),
            const SizedBox(height: 30),
            const Text("Reset Password",
                style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            const Text(
              "Enter your registered Email to receive a 6-digit verification code.",
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.grey),
            ),
            const SizedBox(height: 40),
            TextField(
              controller: emailCtrl,
              keyboardType: TextInputType.emailAddress,
              decoration: InputDecoration(
                labelText: "Email Address",
                hintText: "example@gmail.com",
                prefixIcon: const Icon(Icons.email_outlined, color: spektaRed),
                border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(15)),
              ),
            ),
            const SizedBox(height: 40),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed,
                minimumSize: const Size(double.infinity, 55),
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(15)),
                elevation: 5,
              ),
              onPressed: () async {
                // Gunakan .trim() untuk menghapus spasi tak terlihat
                String emailInput = emailCtrl.text.trim();

                if (emailInput.isEmpty || !emailInput.contains('@')) {
                  ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
                      content: Text("Please enter a valid email")));
                  return;
                }

                // Tampilkan Loading
                showDialog(
                    context: context,
                    barrierDismissible: false,
                    builder: (_) =>
                        const Center(child: CircularProgressIndicator(color: spektaRed)));

                try {
                  // Memanggil API forgotPassword dengan email yang sudah di-trim
                  var resp = await AuthService.forgotPassword(emailInput);
                  
                  if (!context.mounted) return;
                  Navigator.pop(context); // Tutup Loading

                  if (resp.statusCode == 200) {
                    ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
                        backgroundColor: Colors.green,
                        content: Text("OTP Code sent to your email!")));
                        
                    // Berhasil: Pindah ke halaman input OTP
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => ResetOtpPage(email: emailInput),
                      ),
                    );
                  } else {
                    // Ambil pesan error asli dari Laravel
                    final Map<String, dynamic> errorData = jsonDecode(resp.body);
                    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
                        backgroundColor: Colors.red,
                        content: Text(errorData['message'] ?? "Email not found!")));
                  }
                } catch (e) {
                  Navigator.pop(context);
                  ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
                      backgroundColor: Colors.black,
                      content: Text("Connection Error! Check your internet.")));
                }
              },
              child: const Text("SEND OTP CODE",
                  style: TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                      fontSize: 16)),
            )
          ],
        ),
      ),
    );
  }
} 