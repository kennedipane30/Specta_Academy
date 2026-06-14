import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'reset_otp_page.dart';
import 'dart:convert'; // Dibutuhkan untuk jsonDecode

class ForgotPasswordPage extends StatelessWidget {
  const ForgotPasswordPage({super.key});

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

  @override
  Widget build(BuildContext context) {
    final emailCtrl = TextEditingController();

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
          "Forgot Password",
          style: TextStyle(
            fontWeight: FontWeight.w900,
            color: Colors.white,
            fontSize: 17,
          ),
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
        padding: const EdgeInsets.symmetric(horizontal: 35, vertical: 40),
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(25),
              decoration: BoxDecoration(
                  color: accentTeal.withOpacity(0.1), shape: BoxShape.circle),
              child: const Icon(Icons.lock_reset_rounded,
                  size: 80, color: accentTeal),
            ),
            const SizedBox(height: 30),
            const Text(
              "Reset Password",
              style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: textDark),
            ),
            const SizedBox(height: 10),
            Text(
              "Enter your registered Email to receive a 6-digit verification code.",
              textAlign: TextAlign.center,
              style: TextStyle(color: neutralGray),
            ),
            const SizedBox(height: 40),
            TextField(
              controller: emailCtrl,
              keyboardType: TextInputType.emailAddress,
              style: const TextStyle(color: textDark, fontWeight: FontWeight.w600),
              decoration: InputDecoration(
                labelText: "Email Address",
                labelStyle: const TextStyle(color: neutralGray),
                hintText: "example@gmail.com",
                hintStyle: TextStyle(color: neutralGray.withOpacity(0.7)),
                prefixIcon: const Icon(Icons.email_outlined, color: accentTeal),
                border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(15),
                    borderSide: BorderSide(color: outlineVariant)),
                enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(15),
                    borderSide: BorderSide(color: outlineVariant)),
                focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(15),
                    borderSide: const BorderSide(color: accentTeal, width: 1.5)),
                filled: true,
                fillColor: Colors.white,
              ),
            ),
            const SizedBox(height: 40),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: accentTeal,
                minimumSize: const Size(double.infinity, 55),
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(15)),
                elevation: 5,
                shadowColor: accentTeal.withOpacity(0.3),
              ),
              onPressed: () async {
                // Gunakan .trim() untuk menghapus spasi tak terlihat
                String emailInput = emailCtrl.text.trim();

                if (emailInput.isEmpty || !emailInput.contains('@')) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      backgroundColor: primaryRed,
                      content: Text("Please enter a valid email"),
                      behavior: SnackBarBehavior.floating,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.all(Radius.circular(12))),
                    )
                  );
                  return;
                }

                // Tampilkan Loading
                showDialog(
                    context: context,
                    barrierDismissible: false,
                    builder: (_) =>
                        const Center(child: CircularProgressIndicator(color: accentTeal)));

                try {
                  // Memanggil API forgotPassword dengan email yang sudah di-trim
                  var resp = await AuthService.forgotPassword(emailInput);
                  
                  if (!context.mounted) return;
                  Navigator.pop(context); // Tutup Loading

                  if (resp.statusCode == 200) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(
                        backgroundColor: darkTeal,
                        content: Text("OTP Code sent to your email!"),
                        behavior: SnackBarBehavior.floating,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.all(Radius.circular(12))),
                      )
                    );
                        
                    // Navigasi ke halaman ResetOtpPage
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => ResetOtpPage(email: emailInput),
                      ),
                    );
                  } else {
                    // Ambil pesan error asli dari Laravel
                    final Map<String, dynamic> errorData = jsonDecode(resp.body);
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(
                        backgroundColor: primaryRed,
                        content: Text(errorData['message'] ?? "Email not found!"),
                        behavior: SnackBarBehavior.floating,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.all(Radius.circular(12))),
                      )
                    );
                  }
                } catch (e) {
                  if (context.mounted) Navigator.pop(context);
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      backgroundColor: primaryRed,
                      content: Text("Connection Error! Check your internet."),
                      behavior: SnackBarBehavior.floating,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.all(Radius.circular(12))),
                    )
                  );
                }
              },
              child: const Text("SEND OTP CODE",
                  style: TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                      fontSize: 16)),
            )
          ],
        ),
      ),
    );
  }
}