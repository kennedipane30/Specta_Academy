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
      backgroundColor: Colors.white,
      appBar: AppBar(title: const Text("Forgot Password"), backgroundColor: Colors.white, foregroundColor: spektaRed, elevation: 0),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 35, vertical: 40),
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(25),
              decoration: BoxDecoration(color: spektaRed.withOpacity(0.1), shape: BoxShape.circle),
              child: const Icon(Icons.lock_reset_rounded, size: 80, color: spektaRed),
            ),
            const SizedBox(height: 30),
            const Text("Reset Password", style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            const Text("Enter your WhatsApp number to receive an OTP verification code.", textAlign: TextAlign.center, style: TextStyle(color: Colors.grey)),
            const SizedBox(height: 40),
            TextField(
              controller: phoneCtrl,
              keyboardType: TextInputType.phone,
              decoration: InputDecoration(
                labelText: "WhatsApp Number",
                prefixIcon: const Icon(Icons.phone_android, color: spektaRed),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
              ),
            ),
            const SizedBox(height: 40),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed,
                minimumSize: const Size(double.infinity, 55),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                elevation: 5,
              ),
              onPressed: () async {
                showDialog(context: context, builder: (_) => const Center(child: CircularProgressIndicator(color: spektaRed)));
                var resp = await AuthService.forgotPassword(phoneCtrl.text);
                if (!context.mounted) return;
                Navigator.pop(context);
                
                if (resp.statusCode == 200) {
                  final data = jsonDecode(resp.body);
                  Navigator.push(context, MaterialPageRoute(builder: (_) => ResetOtpPage(phone: phoneCtrl.text, otpSimulasi: data['otp'].toString())));
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.red, content: Text("Number not found!")));
                }
              },
              child: const Text("SEND OTP CODE", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
            )
          ],
        ),
      ),
    );
  }
}