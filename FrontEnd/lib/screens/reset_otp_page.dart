import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'dart:async';
import '../services/auth_service.dart';
import 'new_password_page.dart';

class ResetOtpPage extends StatefulWidget {
  final String email;
  const ResetOtpPage({super.key, required this.email});

  @override
  State<ResetOtpPage> createState() => _ResetOtpPageState();
}

class _ResetOtpPageState extends State<ResetOtpPage> {
  // 6 Controller & FocusNode untuk 6 kotak OTP
  List<TextEditingController> controllers = List.generate(6, (index) => TextEditingController());
  List<FocusNode> focusNodes = List.generate(6, (index) => FocusNode());
  
  final Color spektaRed = const Color(0xFF990000);
  Timer? _timer;
  int _start = 60;
  bool _isResendClickable = false;

  @override
  void initState() {
    super.initState();
    startTimer();
  }

  @override
  void dispose() {
    _timer?.cancel();
    for (var node in focusNodes) node.dispose();
    for (var ctrl in controllers) ctrl.dispose();
    super.dispose();
  }

  void startTimer() {
    _timer?.cancel();
    setState(() { _isResendClickable = false; _start = 60; });
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (_start == 0) {
        setState(() { _isResendClickable = true; timer.cancel(); });
      } else {
        setState(() { _start--; });
      }
    });
  }

  String getOtpCode() => controllers.map((e) => e.text).join();

  // Fungsi Kirim Ulang OTP Khusus Lupa Password
  void handleResendOtp() async {
    if (!_isResendClickable) return;

    showDialog(context: context, builder: (_) => Center(child: CircularProgressIndicator(color: spektaRed)));
    
    // Gunakan fungsi forgotPassword untuk mengirim ulang kode ke email tersebut
    var resp = await AuthService.forgotPassword(widget.email);
    
    if (!mounted) return;
    Navigator.pop(context);

    if (resp.statusCode == 200) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.green, content: Text("Kode OTP baru telah dikirim ke email!")));
      startTimer();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.red, content: Text("Gagal mengirim ulang kode.")));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(elevation: 0, backgroundColor: Colors.white, foregroundColor: spektaRed, title: const Text("Verifikasi Reset Password")),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 25),
        child: Column(
          children: [
            const SizedBox(height: 30),
            Icon(Icons.lock_reset_rounded, size: 80, color: spektaRed),
            const SizedBox(height: 30),
            const Text("Masukkan Kode OTP", style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            Text("Kami telah mengirimkan 6 digit kode ke email:", textAlign: TextAlign.center, style: TextStyle(color: Colors.grey[600])),
            Text(widget.email, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.black)),
            const SizedBox(height: 50),
            
            // --- BOX INPUT OTP (6 BOXES) ---
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: List.generate(6, (index) => _buildOtpBox(index)),
            ),
            
            const SizedBox(height: 60),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed,
                minimumSize: const Size(double.infinity, 60),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
              ),
              onPressed: () {
                String otp = getOtpCode();
                if (otp.length < 6) {
                  ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Masukkan 6 digit kode lengkap!")));
                  return;
                }
                Navigator.push(context, MaterialPageRoute(builder: (_) => NewPasswordPage(email: widget.email, otp: otp)));
              },
              child: const Text("VERIFIKASI KODE", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
            ),
            const SizedBox(height: 30),
            
            // Tombol Kirim Ulang
            TextButton(
              onPressed: _isResendClickable ? handleResendOtp : null,
              child: Text(
                _isResendClickable ? "Kirim Ulang Kode" : "Kirim Ulang dalam $_start s",
                style: TextStyle(color: _isResendClickable ? spektaRed : Colors.grey, fontWeight: FontWeight.bold),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildOtpBox(int index) {
    return Container(
      width: 45,
      height: 55,
      decoration: BoxDecoration(
        color: const Color(0xFFF8F9FA),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: focusNodes[index].hasFocus ? spektaRed : Colors.grey.shade300, width: 2),
      ),
      child: Center(
        child: TextField(
          controller: controllers[index],
          focusNode: focusNodes[index],
          textAlign: TextAlign.center,
          keyboardType: TextInputType.number,
          inputFormatters: [FilteringTextInputFormatter.digitsOnly],
          maxLength: 1,
          style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
          decoration: const InputDecoration(counterText: "", border: InputBorder.none, hintText: "-"),
          onChanged: (value) {
            if (value.isNotEmpty && index < 5) {
              focusNodes[index + 1].requestFocus();
            } else if (value.isEmpty && index > 0) {
              focusNodes[index - 1].requestFocus();
            }
            setState(() {});
          },
        ),
      ),
    );
  }
}