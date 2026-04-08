import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'register_page.dart'; 
import 'main_screen.dart';   
import 'forgot_password_page.dart';
import 'dart:convert';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final TextEditingController nameCtrl = TextEditingController();
  final TextEditingController passCtrl = TextEditingController();
  final Color spektaRed = const Color(0xFF990000);
  bool _isObscure = true; // Untuk toggle password

  void handleLogin() async {
    if (nameCtrl.text.isEmpty || passCtrl.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Nama dan Password wajib diisi!"))
      );
      return;
    }

    showDialog(
      context: context, 
      barrierDismissible: false, 
      builder: (context) => Center(child: CircularProgressIndicator(color: spektaRed))
    );

    try {
      var resp = await AuthService.login(nameCtrl.text, passCtrl.text);
      
      if (!mounted) return;
      Navigator.pop(context); 

      if (resp.statusCode == 200) {
        final data = jsonDecode(resp.body);
        
        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(builder: (_) => MainScreen(
            userName: data['user']['name'], 
            token: data['token'],
            userProfileData: data['user'], 
          )),
          (route) => false,
        );
      } else {
        final errorData = jsonDecode(resp.body);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(backgroundColor: Colors.red, content: Text(errorData['message'] ?? "Nama atau Password Salah!"))
        );
      }
    } catch (e) {
      if (!mounted) return;
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(backgroundColor: Colors.black, content: Text("Koneksi Error: Pastikan server Laravel menyala."))
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA), // Latar belakang abu-abu sangat muda
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 30),
          child: Column(
            children: [
              const SizedBox(height: 60),
              // --- HEADER SECTION ---
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: spektaRed.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: Icon(Icons.school_rounded, size: 80, color: spektaRed),
              ),
              const SizedBox(height: 25),
              Text(
                "SPEKTA ACADEMY",
                style: TextStyle(
                  fontSize: 28,
                  fontWeight: FontWeight.w900,
                  color: spektaRed,
                  letterSpacing: 2,
                ),
              ),
              const Text(
                "Wujudkan Impian Menjadi Abdi Negara",
                style: TextStyle(color: Colors.grey, fontSize: 14, fontWeight: FontWeight.w500),
              ),
              
              const SizedBox(height: 60),

              // --- INPUT SECTION ---
              _buildTextField(
                controller: nameCtrl,
                label: "Nama Lengkap",
                icon: Icons.person_outline,
              ),
              const SizedBox(height: 20),
              _buildTextField(
                controller: passCtrl,
                label: "Password",
                icon: Icons.lock_outline,
                isPassword: true,
              ),

              // --- FORGOT PASSWORD ---
              Align(
                alignment: Alignment.centerRight,
                child: TextButton(
                  onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const ForgotPasswordPage())),
                  child: Text(
                    "Lupa Password?",
                    style: TextStyle(color: spektaRed, fontWeight: FontWeight.bold, fontSize: 13),
                  ),
                ),
              ),

              const SizedBox(height: 30),

              // --- BUTTON MASUK ---
              Container(
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(15),
                  boxShadow: [
                    BoxShadow(
                      color: spektaRed.withOpacity(0.3),
                      blurRadius: 10,
                      offset: const Offset(0, 5),
                    ),
                  ],
                ),
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: spektaRed,
                    minimumSize: const Size(double.infinity, 55),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                    elevation: 0,
                  ),
                  onPressed: handleLogin,
                  child: const Text(
                    "MASUK KE AKUN",
                    style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16, letterSpacing: 1),
                  ),
                ),
              ),

              const SizedBox(height: 40),

              // --- REGISTER SECTION ---
              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Text("Belum punya akun? ", style: TextStyle(color: Colors.grey)),
                  GestureDetector(
                    onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const RegisterPage())),
                    child: Text(
                      "Daftar Sekarang",
                      style: TextStyle(color: spektaRed, fontWeight: FontWeight.bold),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 20),
            ],
          ),
        ),
      ),
    );
  }

  // Widget Reusable untuk Input Field
  Widget _buildTextField({
    required TextEditingController controller,
    required String label,
    required IconData icon,
    bool isPassword = false,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: TextField(
        controller: controller,
        obscureText: isPassword ? _isObscure : false,
        decoration: InputDecoration(
          labelText: label,
          labelStyle: const TextStyle(color: Colors.grey, fontSize: 14),
          prefixIcon: Icon(icon, color: spektaRed),
          suffixIcon: isPassword 
            ? IconButton(
                icon: Icon(_isObscure ? Icons.visibility_off : Icons.visibility, color: Colors.grey),
                onPressed: () => setState(() => _isObscure = !_isObscure),
              )
            : null,
          border: InputBorder.none,
          contentPadding: const EdgeInsets.symmetric(vertical: 18, horizontal: 20),
        ),
      ),
    );
  }
}