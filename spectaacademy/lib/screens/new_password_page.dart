import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'dart:convert';

class NewPasswordPage extends StatefulWidget {
  final String phone;
  final String otp;
  const NewPasswordPage({super.key, required this.phone, required this.otp});

  @override State<NewPasswordPage> createState() => _NewPasswordPageState();
}

class _NewPasswordPageState extends State<NewPasswordPage> {
  final _passCtrl = TextEditingController();
  final _confCtrl = TextEditingController();
  final _formKey = GlobalKey<FormState>();

  String? _validatePassword(String? v) {
    if (v == null || v.length < 8) return 'Minimal 8 Karakter';
    if (!RegExp(r'^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])').hasMatch(v)) {
      return 'Wajib ada Kapital, Angka, & Simbol!';
    }
    return null;
  }

  void _handleReset() async {
    if (_formKey.currentState!.validate()) {
      var resp = await AuthService.resetPassword({
        'phone': widget.phone,
        'otp': widget.otp,
        'password': _passCtrl.text,
        'password_confirmation': _confCtrl.text,
      });

      if (resp.statusCode == 200) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.green, content: Text("Password Berhasil Diperbarui! Silakan Login.")));
        // Kembali ke halaman Login paling awal
        Navigator.popUntil(context, (route) => route.isFirst);
      } else {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Gagal memperbarui password!")));
      }
    }
  }

  @override Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);
    return Scaffold(
      appBar: AppBar(title: const Text("Password Baru"), backgroundColor: spektaRed),
      body: Padding(
        padding: const EdgeInsets.all(30),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              TextFormField(controller: _passCtrl, obscureText: true, decoration: const InputDecoration(labelText: "Password Baru"), validator: _validatePassword),
              const SizedBox(height: 20),
              TextFormField(controller: _confCtrl, obscureText: true, decoration: const InputDecoration(labelText: "Konfirmasi Password Baru"), validator: (v) => v != _passCtrl.text ? 'Password tidak cocok!' : null),
              const SizedBox(height: 30),
              ElevatedButton(
                style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 50)),
                onPressed: _handleReset,
                child: const Text("SIMPAN PASSWORD BARU", style: TextStyle(color: Colors.white)),
              )
            ],
          ),
        ),
      ),
    );
  }
}