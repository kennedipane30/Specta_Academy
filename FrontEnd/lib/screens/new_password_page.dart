import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'login_page.dart';
import 'dart:convert';

class NewPasswordPage extends StatefulWidget {
  final String email;
  final String otp; // Ini harus berisi 6 digit angka dari halaman sebelumnya

  const NewPasswordPage({super.key, required this.email, required this.otp});

  @override
  State<NewPasswordPage> createState() => _NewPasswordPageState();
}

class _NewPasswordPageState extends State<NewPasswordPage> {
  final _passCtrl = TextEditingController();
  final _confirmPassCtrl = TextEditingController();
  final Color spektaRed = const Color(0xFF990000);
  
  bool _isObscurePass = true;
  bool _isObscureConfirm = true;

  void _handleReset() async {
    // 1. Validasi Input di HP
    if (_passCtrl.text.isEmpty) {
      _showSnackBar("Password baru tidak boleh kosong", Colors.orange);
      return;
    }

    if (_passCtrl.text.length < 8) {
      _showSnackBar("Password minimal 8 karakter", Colors.orange);
      return;
    }

    if (_passCtrl.text != _confirmPassCtrl.text) {
      _showSnackBar("Konfirmasi password tidak cocok!", Colors.red);
      return;
    }

    // --- DIAGNOSIS (Cek di Debug Console VS Code) ---
    print("Mencoba Reset Password...");
    print("Email: ${widget.email}");
    print("OTP yang diterima dari halaman sebelumnya: ${widget.otp}");
    // ------------------------------------------------

    showDialog(
      context: context, 
      barrierDismissible: false, 
      builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000)))
    );

    try {
      // 2. Kirim ke Server
      var resp = await AuthService.resetPassword({
        'email': widget.email.trim(),
        'otp': widget.otp.trim(),
        'password': _passCtrl.text,
        'password_confirmation': _confirmPassCtrl.text,
      });

      if (!mounted) return;
      Navigator.pop(context); // Tutup loading

      // 3. Respon dari Laravel
      if (resp.statusCode == 200) {
        _showSnackBar("Password berhasil diperbarui!", Colors.green);
        
        // Balik ke Login dan hapus semua halaman sebelumnya
        Navigator.pushAndRemoveUntil(
          context, 
          MaterialPageRoute(builder: (_) => const LoginPage()), 
          (route) => false
        );
      } else {
        final data = jsonDecode(resp.body);
        _showSnackBar(data['message'] ?? "Kode OTP Salah atau Kadaluarsa", Colors.red);
      }
    } catch (e) {
      if (mounted) Navigator.pop(context);
      _showSnackBar("Kesalahan koneksi ke server", Colors.black);
    }
  }

  void _showSnackBar(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(backgroundColor: color, content: Text(msg), duration: const Duration(seconds: 3))
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text("Buat Password Baru", style: TextStyle(fontWeight: FontWeight.bold)), 
        foregroundColor: spektaRed, 
        backgroundColor: Colors.white, 
        elevation: 0
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(30),
        child: Column(
          children: [
            const Icon(Icons.lock_open_rounded, size: 80, color: Color(0xFF990000)),
            const SizedBox(height: 20),
            Text(
              "Email: ${widget.email}",
              style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.blueGrey),
            ),
            const SizedBox(height: 10),
            const Text(
              "Masukkan password baru Anda. Pastikan kombinasi huruf dan angka agar aman.",
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.grey),
            ),
            const SizedBox(height: 40),
            
            // Password Baru
            TextField(
              controller: _passCtrl,
              obscureText: _isObscurePass,
              decoration: InputDecoration(
                labelText: "Password Baru",
                prefixIcon: Icon(Icons.vpn_key_outlined, color: spektaRed),
                suffixIcon: IconButton(
                  icon: Icon(_isObscurePass ? Icons.visibility_off : Icons.visibility, color: Colors.grey), 
                  onPressed: () => setState(() => _isObscurePass = !_isObscurePass)
                ),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
              ),
            ),
            const SizedBox(height: 20),

            // Konfirmasi Password
            TextField(
              controller: _confirmPassCtrl,
              obscureText: _isObscureConfirm,
              decoration: InputDecoration(
                labelText: "Konfirmasi Password",
                prefixIcon: Icon(Icons.check_circle_outline, color: spektaRed),
                suffixIcon: IconButton(
                  icon: Icon(_isObscureConfirm ? Icons.visibility_off : Icons.visibility, color: Colors.grey), 
                  onPressed: () => setState(() => _isObscureConfirm = !_isObscureConfirm)
                ),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
              ),
            ),
            
            const SizedBox(height: 40),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed, 
                minimumSize: const Size(double.infinity, 60), 
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                elevation: 5
              ),
              onPressed: _handleReset, 
              child: const Text("SIMPAN PASSWORD BARU", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16))
            ),
          ],
        ),
      ),
    );
  }
}