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
  
  // ============================================================
  // 🎨 PALET WARNA SPEKTA (KONSISTEN DENGAN TRYOUTDETAILPAGE)
  // ============================================================
  static const Color primaryRed      = Color(0xFFC5352C);
  static const Color accentTeal      = Color(0xFF2EA8AB);
  static const Color darkTeal        = Color(0xFF00696C);
  static const Color lightBlueBg     = Color(0xFFEFF4FF);
  static const Color pageBg          = Color(0xFFF1F5F9);
  static const Color textDark        = Color(0xFF0F172A);
  static const Color textDarkVariant = Color(0xFF334155);
  static const Color neutralGray     = Color(0xFF64748B);
  static const Color outlineVariant  = Color(0xFFE2BEBA);
  
  bool _isObscurePass = true;
  bool _isObscureConfirm = true;

  void _handleReset() async {
    // 1. Validasi Input di HP
    if (_passCtrl.text.isEmpty) {
      _showSnackBar("Password baru tidak boleh kosong", primaryRed);
      return;
    }

    if (_passCtrl.text.length < 8) {
      _showSnackBar("Password minimal 8 karakter", primaryRed);
      return;
    }

    if (_passCtrl.text != _confirmPassCtrl.text) {
      _showSnackBar("Konfirmasi password tidak cocok!", primaryRed);
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
      builder: (_) => const Center(child: CircularProgressIndicator(color: accentTeal))
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
        _showSnackBar("Password berhasil diperbarui!", darkTeal);
        
        // Balik ke Login dan hapus semua halaman sebelumnya
        Navigator.pushAndRemoveUntil(
          context, 
          MaterialPageRoute(builder: (_) => const LoginPage()), 
          (route) => false
        );
      } else {
        final data = jsonDecode(resp.body);
        _showSnackBar(data['message'] ?? "Kode OTP Salah atau Kadaluarsa", primaryRed);
      }
    } catch (e) {
      if (mounted) Navigator.pop(context);
      _showSnackBar("Kesalahan koneksi ke server", primaryRed);
    }
  }

  void _showSnackBar(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        backgroundColor: color, 
        content: Text(msg, style: const TextStyle(fontWeight: FontWeight.bold)), 
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        duration: const Duration(seconds: 3)
      )
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: pageBg,
      appBar: AppBar(
        flexibleSpace: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [primaryRed, accentTeal],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
        ),
        title: const Text(
          "Buat Password Baru", 
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white, fontSize: 17)
        ), 
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(30),
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(25),
              decoration: BoxDecoration(
                color: accentTeal.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(Icons.lock_open_rounded, size: 80, color: accentTeal),
            ),
            const SizedBox(height: 20),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              decoration: BoxDecoration(
                color: lightBlueBg,
                borderRadius: BorderRadius.circular(20),
              ),
              child: Text(
                "Email: ${widget.email}",
                style: const TextStyle(fontWeight: FontWeight.bold, color: textDarkVariant),
              ),
            ),
            const SizedBox(height: 16),
            const Text(
              "Masukkan password baru Anda. Pastikan kombinasi huruf dan angka agar aman.",
              textAlign: TextAlign.center,
              style: TextStyle(color: neutralGray, height: 1.5),
            ),
            const SizedBox(height: 40),
            
            // Password Baru
            TextField(
              controller: _passCtrl,
              obscureText: _isObscurePass,
              style: const TextStyle(color: textDark, fontWeight: FontWeight.w600),
              decoration: InputDecoration(
                labelText: "Password Baru",
                labelStyle: const TextStyle(color: neutralGray),
                prefixIcon: Icon(Icons.vpn_key_outlined, color: accentTeal),
                suffixIcon: IconButton(
                  icon: Icon(_isObscurePass ? Icons.visibility_off : Icons.visibility, color: neutralGray), 
                  onPressed: () => setState(() => _isObscurePass = !_isObscurePass)
                ),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(15),
                  borderSide: BorderSide(color: outlineVariant),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(15),
                  borderSide: BorderSide(color: outlineVariant),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(15),
                  borderSide: const BorderSide(color: accentTeal, width: 1.5),
                ),
                filled: true,
                fillColor: Colors.white,
              ),
            ),
            const SizedBox(height: 20),

            // Konfirmasi Password
            TextField(
              controller: _confirmPassCtrl,
              obscureText: _isObscureConfirm,
              style: const TextStyle(color: textDark, fontWeight: FontWeight.w600),
              decoration: InputDecoration(
                labelText: "Konfirmasi Password",
                labelStyle: const TextStyle(color: neutralGray),
                prefixIcon: Icon(Icons.check_circle_outline, color: accentTeal),
                suffixIcon: IconButton(
                  icon: Icon(_isObscureConfirm ? Icons.visibility_off : Icons.visibility, color: neutralGray), 
                  onPressed: () => setState(() => _isObscureConfirm = !_isObscureConfirm)
                ),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(15),
                  borderSide: BorderSide(color: outlineVariant),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(15),
                  borderSide: BorderSide(color: outlineVariant),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(15),
                  borderSide: const BorderSide(color: accentTeal, width: 1.5),
                ),
                filled: true,
                fillColor: Colors.white,
              ),
            ),
            
            const SizedBox(height: 40),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: accentTeal, 
                minimumSize: const Size(double.infinity, 60), 
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                elevation: 5,
                shadowColor: accentTeal.withOpacity(0.3),
              ),
              onPressed: _handleReset, 
              child: const Text(
                "SIMPAN PASSWORD BARU", 
                style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)
              )
            ),
          ],
        ),
      ),
    );
  }
}