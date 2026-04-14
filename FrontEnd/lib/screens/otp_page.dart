import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'dart:convert';
import 'login_page.dart'; 

class OtpPage extends StatefulWidget {
  final String name;
  final String email;

  const OtpPage({
    super.key, 
    required this.name, 
    required this.email,
  });

  @override
  State<OtpPage> createState() => _OtpPageState();
}

class _OtpPageState extends State<OtpPage> {
  final TextEditingController _otpCtrl = TextEditingController();
  bool _isLoading = false;

  void _verifyOtp() async {
    if (_otpCtrl.text.isEmpty) {
      _showSnackBar("Masukkan kode OTP", Colors.orange);
      return;
    }

    setState(() => _isLoading = true);

    try {
      var response = await AuthService.verifyOtp({
        'name': widget.name,
        'otp': _otpCtrl.text.trim(),
      });

      if (!mounted) return;
      setState(() => _isLoading = false);

      final responseData = jsonDecode(response.body);

      if (response.statusCode == 200) {
        _showSnackBar("Verifikasi Berhasil! Silakan Login.", Colors.green);
        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(builder: (_) => const LoginPage()), 
          (route) => false,
        );
      } else {
        _showSnackBar(responseData['message'] ?? "Kode OTP Salah", Colors.red);
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
      _showSnackBar("Kesalahan Koneksi!", Colors.black);
    }
  }

  void _showSnackBar(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg), backgroundColor: color, behavior: SnackBarBehavior.floating));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(title: const Text("Verifikasi OTP"), backgroundColor: Colors.white, foregroundColor: const Color(0xFF990000), elevation: 0),
      body: Padding(
        padding: const EdgeInsets.all(30.0),
        child: Column(
          children: [
            const Icon(Icons.mark_email_read_outlined, size: 80, color: Color(0xFF990000)),
            const SizedBox(height: 20),
            Text("Halo ${widget.name}", style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            Text("Masukkan 6 digit kode yang dikirim ke Gmail:", textAlign: TextAlign.center),
            Text(widget.email, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.black)),
            const SizedBox(height: 40),
            TextField(
              controller: _otpCtrl,
              keyboardType: TextInputType.number,
              textAlign: TextAlign.center,
              maxLength: 6,
              style: const TextStyle(fontSize: 30, fontWeight: FontWeight.bold, letterSpacing: 10),
              decoration: InputDecoration(
                counterText: "",
                hintText: "000000",
                filled: true,
                fillColor: Colors.grey[100],
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide.none),
              ),
            ),
            const SizedBox(height: 30),
            _isLoading 
              ? const CircularProgressIndicator(color: Color(0xFF990000))
              : ElevatedButton(
                  style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF990000), minimumSize: const Size(double.infinity, 55), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
                  onPressed: _verifyOtp,
                  child: const Text("VERIFIKASI SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                ),
          ],
        ),
      ),
    );
  }
}