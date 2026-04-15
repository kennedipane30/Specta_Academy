import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'login_page.dart';
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

  void handleVerify() async {
    if (otpCtrl.text.length < 6) return;

    showDialog(context: context, barrierDismissible: false, builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000))));

    var resp = await AuthService.verifyRegistration(widget.name, otpCtrl.text);

    if (!mounted) return;
    Navigator.pop(context);

    if (resp.statusCode == 200) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(backgroundColor: Colors.green, content: Text("Account Activated! Please login with your name & password."))
      );

      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (context) => const LoginPage()),
        (route) => false,
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.red, content: Text("Invalid OTP Code!")));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Account Activation"), backgroundColor: spektaRed, foregroundColor: Colors.white),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(30),
        child: Column(
          children: [
            const SizedBox(height: 20),
            const Icon(Icons.verified_user_outlined, size: 80, color: Color(0xFF990000)),
            const SizedBox(height: 30),
            Text(
              "Hi ${widget.name}, please enter the OTP code sent to your WhatsApp:",
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.grey[700], fontSize: 16),
            ),
            const SizedBox(height: 10),
            Text("SIMULATION: ${widget.otpSimulasi}", style: const TextStyle(color: Colors.red, fontWeight: FontWeight.bold, fontSize: 18)),
            const SizedBox(height: 50),
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
              style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 55), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
              onPressed: handleVerify,
              child: const Text("VERIFY & ACTIVATE", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      ),
    );
  }
}