import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'login_page.dart';
import 'dart:convert';

class NewPasswordPage extends StatefulWidget {
  final String email;
  final String otp;

  const NewPasswordPage({super.key, required this.email, required this.otp});

  @override
  State<NewPasswordPage> createState() => _NewPasswordPageState();
}

class _NewPasswordPageState extends State<NewPasswordPage> {
  final _passCtrl = TextEditingController();
  final _confirmPassCtrl = TextEditingController();
  final Color spektaRed = const Color(0xFF990000);
  
  // 🔥 Menggunakan dua variabel berbeda agar ikon mata berfungsi mandiri
  bool _isObscurePass = true;
  bool _isObscureConfirm = true;

  void _handleReset() async {
    if (_passCtrl.text.isEmpty) {
      _showSnackBar("Password tidak boleh kosong", Colors.orange);
      return;
    }

    if (_passCtrl.text != _confirmPassCtrl.text) {
      _showSnackBar("Konfirmasi password tidak cocok!", Colors.red);
      return;
    }

    showDialog(
      context: context, 
      barrierDismissible: false, 
      builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000)))
    );

    // 1. Panggil API Reset Password
    var resp = await AuthService.resetPassword({
      'email': widget.email,
      'otp': widget.otp,
      'password': _passCtrl.text,
      'password_confirmation': _confirmPassCtrl.text,
    });

    if (!mounted) return;
    Navigator.pop(context); // Tutup loading

    // 2. Cek Response
    if (resp.statusCode == 200) {
      _showSnackBar("Password berhasil diperbarui!", Colors.green);
      
      // Kembali ke halaman Login
      Navigator.pushAndRemoveUntil(
        context, 
        MaterialPageRoute(builder: (_) => const LoginPage()), 
        (route) => false
      );
    } else {
      final data = jsonDecode(resp.body);
      _showSnackBar(data['message'] ?? "Gagal mereset password", Colors.red);
    }
  }

  void _showSnackBar(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(backgroundColor: color, content: Text(msg))
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text("Buat Password Baru"), 
        foregroundColor: spektaRed, 
        backgroundColor: Colors.white, 
        elevation: 0
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(30),
        child: Column(
          children: [
            const Icon(Icons.vpn_key_outlined, size: 80, color: Color(0xFF990000)),
            const SizedBox(height: 20),
            const Text(
              "Silakan buat password baru yang kuat untuk akun Anda.",
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.grey),
            ),
            const SizedBox(height: 40),
            
            // --- FIELD PASSWORD BARU ---
            TextField(
              controller: _passCtrl,
              obscureText: _isObscurePass,
              decoration: InputDecoration(
                labelText: "Password Baru",
                prefixIcon: Icon(Icons.lock_outline, color: spektaRed),
                suffixIcon: IconButton(
                  icon: Icon(_isObscurePass ? Icons.visibility_off : Icons.visibility, color: Colors.grey), 
                  onPressed: () => setState(() => _isObscurePass = !_isObscurePass)
                ),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(15),
                  borderSide: const BorderSide(color: Color(0xFFEEEEEE))
                ),
              ),
            ),
            const SizedBox(height: 20),

            // --- FIELD KONFIRMASI PASSWORD ---
            TextField(
              controller: _confirmPassCtrl,
              obscureText: _isObscureConfirm,
              decoration: InputDecoration(
                labelText: "Konfirmasi Password Baru",
                prefixIcon: Icon(Icons.lock_outline, color: spektaRed),
                suffixIcon: IconButton(
                  icon: Icon(_isObscureConfirm ? Icons.visibility_off : Icons.visibility, color: Colors.grey), 
                  onPressed: () => setState(() => _isObscureConfirm = !_isObscureConfirm)
                ),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(15),
                  borderSide: const BorderSide(color: Color(0xFFEEEEEE))
                ),
              ),
            ),
            
            const SizedBox(height: 40),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed, 
                minimumSize: const Size(double.infinity, 55), 
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                elevation: 5
              ),
              onPressed: _handleReset, 
              child: const Text("SIMPAN PASSWORD", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold))
            ),
          ],
        ),
      ),
    );
  }
}