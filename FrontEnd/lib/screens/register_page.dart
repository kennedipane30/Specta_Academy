import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'otp_page.dart';
import 'dart:convert';

class RegisterPage extends StatefulWidget {
  const RegisterPage({super.key});

  @override
  State<RegisterPage> createState() => _RegisterPageState();
}

class _RegisterPageState extends State<RegisterPage> {
  final _formKey = GlobalKey<FormState>();
  final Color spektaRed = const Color(0xFF990000);
  bool _isObscure = true;

  final TextEditingController _nameCtrl = TextEditingController();
  final TextEditingController _emailCtrl = TextEditingController();
  final TextEditingController _waCtrl = TextEditingController();
  final TextEditingController _passCtrl = TextEditingController();
  final TextEditingController _confirmPassCtrl = TextEditingController();

  String? _validateName(String? value) {
    if (value == null || value.isEmpty) return 'Nama wajib diisi';
    if (!RegExp(r'^[a-zA-Z\s]+$').hasMatch(value)) return 'Nama hanya boleh berisi huruf!';
    return null;
  }

  String? _validatePassword(String? value) {
    if (value == null || value.isEmpty) return 'Password wajib diisi';
    if (value.length < 8) return 'Minimal 8 karakter';
    if (!RegExp(r'^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])').hasMatch(value)) {
      return 'Wajib: Huruf Kapital, Kecil, Angka, & Simbol';
    }
    return null;
  }

  void _handleRegister() async {
    if (_formKey.currentState!.validate()) {
      showDialog(
          context: context,
          barrierDismissible: false,
          builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000))));

      Map<String, dynamic> data = {
        'name': _nameCtrl.text,
        'email': _emailCtrl.text,
        'nomor_wa': _waCtrl.text,
        'password': _passCtrl.text,
        'password_confirmation': _confirmPassCtrl.text,
      };

      try {
        var response = await AuthService.register(data);
        if (!mounted) return;
        Navigator.pop(context);

        if (response.statusCode == 201) {
          final responseData = jsonDecode(response.body);
          Navigator.push(context, MaterialPageRoute(builder: (_) => OtpPage(
              name: _nameCtrl.text, otpSimulasi: responseData['otp'].toString())));
        } else {
          final errorData = jsonDecode(response.body);
          _showSnackBar(errorData['message'] ?? "Registrasi Gagal", Colors.red);
        }
      } catch (e) {
        Navigator.pop(context);
        _showSnackBar("Koneksi Error!", Colors.black);
      }
    }
  }

  void _showSnackBar(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(backgroundColor: color, content: Text(msg)));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text("Buat Akun Baru", style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.white,
        foregroundColor: spektaRed,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 30, vertical: 20),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text("Daftar Spekta Academy", style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
              const Text("Lengkapi data diri untuk memulai belajar.", style: TextStyle(color: Colors.grey)),
              const SizedBox(height: 30),
              _buildInput(_nameCtrl, "Nama Lengkap", Icons.person_outline, _validateName),
              _buildInput(_emailCtrl, "Email Aktif", Icons.email_outlined, (v) => v!.contains('@') ? null : "Email tidak valid"),
              _buildInput(_waCtrl, "Nomor WhatsApp", Icons.phone_android_outlined, (v) => v!.length < 10 ? "Nomor tidak valid" : null),
              
              _buildPasswordInput(_passCtrl, "Password", _validatePassword),
              const SizedBox(height: 15),
              _buildPasswordInput(_confirmPassCtrl, "Konfirmasi Password", (v) => v != _passCtrl.text ? 'Password tidak cocok' : null),

              const SizedBox(height: 40),
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: spektaRed,
                  minimumSize: const Size(double.infinity, 55),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                  elevation: 5,
                  shadowColor: spektaRed.withOpacity(0.4)
                ),
                onPressed: _handleRegister,
                child: const Text("DAFTAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
              ),
              const SizedBox(height: 20),
              Center(
                child: TextButton(
                  onPressed: () => Navigator.pop(context),
                  child: RichText(
                    text: TextSpan(
                      text: "Sudah punya akun? ",
                      style: const TextStyle(color: Colors.grey),
                      children: [TextSpan(text: "Masuk", style: TextStyle(color: spektaRed, fontWeight: FontWeight.bold))],
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildInput(TextEditingController ctrl, String label, IconData icon, String? Function(String?)? validator) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 20),
      child: TextFormField(
        controller: ctrl,
        decoration: InputDecoration(
          labelText: label,
          prefixIcon: Icon(icon, color: spektaRed),
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
          enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: const BorderSide(color: Color(0xFFEEEEEE))),
          filled: true,
          fillColor: const Color(0xFFF9F9F9),
        ),
        validator: validator,
      ),
    );
  }

  Widget _buildPasswordInput(TextEditingController ctrl, String label, String? Function(String?)? validator) {
    return TextFormField(
      controller: ctrl,
      obscureText: _isObscure,
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: Icon(Icons.lock_outline, color: spektaRed),
        suffixIcon: IconButton(
          icon: Icon(_isObscure ? Icons.visibility_off : Icons.visibility, color: Colors.grey),
          onPressed: () => setState(() => _isObscure = !_isObscure),
        ),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: const BorderSide(color: Color(0xFFEEEEEE))),
        filled: true,
        fillColor: const Color(0xFFF9F9F9),
      ),
      validator: validator,
    );
  }
}