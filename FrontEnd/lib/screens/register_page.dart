import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'otp_page.dart'; // Pastikan import ini benar
import 'dart:convert';

class RegisterPage extends StatefulWidget {
  const RegisterPage({super.key});

  @override
  State<RegisterPage> createState() => _RegisterPageState();
}

class _RegisterPageState extends State<RegisterPage> {
  final _formKey = GlobalKey<FormState>();
  final Color spektaRed = const Color(0xFF990000);

  // Controllers (Hanya yang disepakati)
  final TextEditingController _nameCtrl = TextEditingController();
  final TextEditingController _emailCtrl = TextEditingController();
  final TextEditingController _waCtrl = TextEditingController();
  final TextEditingController _passCtrl = TextEditingController();
  final TextEditingController _confirmPassCtrl = TextEditingController();

  // 1. VALIDASI NAMA (Hanya Huruf & Spasi)
  String? _validateName(String? value) {
    if (value == null || value.isEmpty) return 'Nama wajib diisi';
    if (!RegExp(r'^[a-zA-Z\s]+$').hasMatch(value)) {
      return 'Nama hanya boleh berisi huruf!';
    }
    return null;
  }

  // 2. VALIDASI PASSWORD (Kapital, Huruf Kecil, Angka, Simbol)
  String? _validatePassword(String? value) {
    if (value == null || value.isEmpty) return 'Password wajib diisi';
    if (value.length < 8) return 'Minimal 8 karakter';
    
    bool hasUppercase = value.contains(RegExp(r'[A-Z]'));
    bool hasLowercase = value.contains(RegExp(r'[a-z]'));
    bool hasDigits = value.contains(RegExp(r'[0-9]'));
    bool hasSpecialCharacters = value.contains(RegExp(r'[!@#$%^&*(),.?":{}|<>]'));

    if (!hasUppercase || !hasLowercase || !hasDigits || !hasSpecialCharacters) {
      return 'Wajib: Huruf Kapital, Kecil, Angka, & Simbol';
    }
    return null;
  }

  void _handleRegister() async {
    if (_formKey.currentState!.validate()) {
      // Munculkan Loading
      showDialog(context: context, barrierDismissible: false, builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000))));

      // Data yang dikirim ke Laravel
      Map<String, dynamic> data = {
        'name': _nameCtrl.text,
        'email': _emailCtrl.text,
        'nomor_wa': _waCtrl.text, // Tetap butuh WA untuk kirim OTP
        'password': _passCtrl.text,
        'password_confirmation': _confirmPassCtrl.text,
      };

      try {
        var response = await AuthService.register(data);
        if (!mounted) return;
        Navigator.pop(context); // Tutup Loading

        if (response.statusCode == 201) {
          final responseData = jsonDecode(response.body);
          // BERHASIL -> PINDAH KE HALAMAN OTP
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (_) => OtpPage(
                name: _nameCtrl.text, 
                otpSimulasi: responseData['otp'].toString()
              ),
            ),
          );
        } else {
          final errorData = jsonDecode(response.body);
          ScaffoldMessenger.of(context).showSnackBar(SnackBar(backgroundColor: Colors.red, content: Text(errorData['message'] ?? "Registrasi Gagal")));
        }
      } catch (e) {
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Koneksi Error!")));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Registrasi Siswa Spekta"),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(25),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              _buildInput(_nameCtrl, "Nama Lengkap", Icons.person, _validateName),
              _buildInput(_emailCtrl, "Gmail / Email", Icons.email, (v) => v!.contains('@') ? null : "Gmail tidak valid"),
              _buildInput(_waCtrl, "Nomor WhatsApp", Icons.phone_android, (v) => v!.length < 10 ? "Nomor tidak valid" : null),
              
              // Input Password
              TextFormField(
                controller: _passCtrl,
                obscureText: true,
                decoration: const InputDecoration(labelText: "Password", prefixIcon: Icon(Icons.lock_outline)),
                validator: _validatePassword,
              ),
              const SizedBox(height: 15),

              // Konfirmasi Password
              TextFormField(
                controller: _confirmPassCtrl,
                obscureText: true,
                decoration: const InputDecoration(labelText: "Konfirmasi Password", prefixIcon: Icon(Icons.lock)),
                validator: (v) => v != _passCtrl.text ? 'Password tidak cocok' : null,
              ),

              const SizedBox(height: 40),
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: spektaRed,
                  minimumSize: const Size(double.infinity, 55),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))
                ),
                onPressed: _handleRegister,
                child: const Text("DAFTAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildInput(TextEditingController ctrl, String label, IconData icon, String? Function(String?)? validator) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 15),
      child: TextFormField(
        controller: ctrl,
        decoration: InputDecoration(labelText: label, prefixIcon: Icon(icon)),
        validator: validator,
      ),
    );
  }
}