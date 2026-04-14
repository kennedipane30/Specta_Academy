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
  bool _isObscureConfirm = true;

  final TextEditingController _nameCtrl = TextEditingController();
  final TextEditingController _emailCtrl = TextEditingController();
  final TextEditingController _waCtrl = TextEditingController();
  final TextEditingController _passCtrl = TextEditingController();
  final TextEditingController _confirmPassCtrl = TextEditingController();

  void _handleRegister() async {
    if (_formKey.currentState!.validate()) {
      showDialog(
          context: context,
          barrierDismissible: false,
          builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000))));

      Map<String, dynamic> data = {
        'name': _nameCtrl.text.trim(),
        'email': _emailCtrl.text.trim(),
        'nomor_wa': _waCtrl.text.trim(),
        'password': _passCtrl.text,
        'password_confirmation': _confirmPassCtrl.text,
      };

      try {
        var response = await AuthService.register(data);
        if (!mounted) return;
        Navigator.pop(context); // Tutup loading

        if (response.statusCode == 201 || response.statusCode == 200) {
          _showSnackBar("Registrasi Berhasil! Cek Gmail Anda.", Colors.green);

          // PINDAH KE OTP PAGE (Kirim Name dan Email)
          Navigator.push(
            context, 
            MaterialPageRoute(
              builder: (context) => OtpPage(
                name: _nameCtrl.text.trim(),
                email: _emailCtrl.text.trim(),
              )
            )
          );
        } else {
          final errorData = jsonDecode(response.body);
          _showSnackBar(errorData['message'] ?? "Registrasi Gagal", Colors.red);
        }
      } catch (e) {
        if (mounted) Navigator.pop(context);
        _showSnackBar("Gagal terhubung ke server!", Colors.black);
      }
    }
  }

  void _showSnackBar(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(backgroundColor: color, content: Text(msg), behavior: SnackBarBehavior.floating));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(title: const Text("Buat Akun Baru"), backgroundColor: Colors.white, foregroundColor: spektaRed, elevation: 0),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 30, vertical: 20),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              const Text("Daftar Spekta Academy", style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
              const SizedBox(height: 30),
              _buildInput(_nameCtrl, "Nama Lengkap", Icons.person_outline),
              _buildInput(_emailCtrl, "Email Aktif", Icons.email_outlined),
              _buildInput(_waCtrl, "WhatsApp", Icons.phone_android_outlined),
              _buildPasswordInput(_passCtrl, "Password", _isObscure, () => setState(() => _isObscure = !_isObscure)),
              _buildPasswordInput(_confirmPassCtrl, "Konfirmasi Password", _isObscureConfirm, () => setState(() => _isObscureConfirm = !_isObscureConfirm)),
              const SizedBox(height: 40),
              ElevatedButton(
                style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 55), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
                onPressed: _handleRegister,
                child: const Text("DAFTAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildInput(TextEditingController ctrl, String label, IconData icon) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 15),
      child: TextFormField(
        controller: ctrl,
        decoration: InputDecoration(labelText: label, prefixIcon: Icon(icon, color: spektaRed), border: OutlineInputBorder(borderRadius: BorderRadius.circular(15))),
        validator: (v) => v!.isEmpty ? "Wajib diisi" : null,
      ),
    );
  }

  Widget _buildPasswordInput(TextEditingController ctrl, String label, bool obscure, VoidCallback toggle) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 15),
      child: TextFormField(
        controller: ctrl,
        obscureText: obscure,
        decoration: InputDecoration(
          labelText: label,
          prefixIcon: Icon(Icons.lock_outline, color: spektaRed),
          suffixIcon: IconButton(icon: Icon(obscure ? Icons.visibility_off : Icons.visibility), onPressed: toggle),
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
        ),
        validator: (v) => v!.length < 8 ? "Minimal 8 karakter" : null,
      ),
    );
  }
}