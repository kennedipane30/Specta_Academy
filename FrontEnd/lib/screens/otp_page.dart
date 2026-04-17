import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'dart:async';
import '../services/auth_service.dart';
import 'login_page.dart';

class OtpPage extends StatefulWidget {
  final String name;
  const OtpPage({super.key, required this.name});

  @override
  State<OtpPage> createState() => _OtpPageState();
}

class _OtpPageState extends State<OtpPage> {
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

  // PENTING: Matikan timer saat user keluar dari halaman
  @override
  void dispose() {
    _timer?.cancel();
    for (var node in focusNodes) {
      node.dispose();
    }
    for (var controller in controllers) {
      controller.dispose();
    }
    super.dispose();
  }

  void startTimer() {
    // Batalkan timer lama jika ada sebelum memulai yang baru
    _timer?.cancel();
    setState(() { 
      _isResendClickable = false; 
      _start = 60; 
    });
    
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (_start == 0) {
        setState(() { 
          _isResendClickable = true; 
          timer.cancel(); 
        });
      } else { 
        setState(() { _start--; }); 
      }
    });
  }

  String getOtpCode() => controllers.map((e) => e.text).join();

  void _handlePaste(String value) {
    if (value.length == 6 && RegExp(r'^\d+$').hasMatch(value)) {
      for (int i = 0; i < 6; i++) {
        controllers[i].text = value[i];
      }
      focusNodes[5].requestFocus();
      handleVerify();
    }
  }

  void handleVerify() async {
    String otp = getOtpCode();
    if (otp.length < 6) return;
    
    showDialog(
      context: context, 
      barrierDismissible: false,
      builder: (_) => Center(child: CircularProgressIndicator(color: spektaRed))
    );
    
    var resp = await AuthService.verifyRegistration(widget.name, otp);
    
    if (!mounted) return;
    Navigator.pop(context); // Tutup loading

    if (resp.statusCode == 200) { 
      _showSuccessDialog(); 
    } else { 
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(backgroundColor: Colors.red, content: Text("OTP Salah atau Kadaluarsa"))
      ); 
    }
  }

  // FUNGSI KIRIM ULANG (ANTI-SPAM)
  void _handleResendCode() async {
    if (!_isResendClickable) return; // Keamanan tambahan

    // Tampilkan Loading
    showDialog(
      context: context, 
      barrierDismissible: false,
      builder: (_) => Center(child: CircularProgressIndicator(color: spektaRed))
    );

    try {
      var resp = await AuthService.resendOtp(widget.name);
      
      if (!mounted) return;
      Navigator.pop(context); // Tutup Loading

      if (resp.statusCode == 200) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(backgroundColor: Colors.green, content: Text("Kode OTP baru telah dikirim!"))
        );
        // JALANKAN TIMER LAGI (Mengunci tombol selama 60 detik)
        startTimer();
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(backgroundColor: Colors.red, content: Text("Gagal mengirim ulang kode."))
        );
      }
    } catch (e) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(backgroundColor: Colors.black, content: Text("Kesalahan Koneksi!"))
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(elevation: 0, backgroundColor: Colors.white, foregroundColor: Colors.black),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 25),
        child: Column(
          children: [
            const SizedBox(height: 20),
            Icon(Icons.mark_email_read_rounded, size: 80, color: spektaRed),
            const SizedBox(height: 25),
            Text("Verifikasi Kode", style: TextStyle(fontSize: 26, fontWeight: FontWeight.bold, color: Colors.grey[800])),
            const SizedBox(height: 10),
            Text("Masukkan 6 digit kode yang dikirim ke email Anda.", textAlign: TextAlign.center, style: TextStyle(color: Colors.grey[500], fontSize: 15)),
            const SizedBox(height: 50),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: List.generate(6, (index) => _buildOtpBox(index)),
            ),
            const SizedBox(height: 60),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed,
                minimumSize: const Size(double.infinity, 55),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              onPressed: handleVerify,
              child: const Text("VERIFIKASI AKUN", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
            ),
            const SizedBox(height: 40),
            _buildResendUI(),
          ],
        ),
      ),
    );
  }

  Widget _buildOtpBox(int index) {
    return Container(
      width: 48,
      height: 58,
      decoration: BoxDecoration(
        color: focusNodes[index].hasFocus ? Colors.white : const Color(0xFFF8F9FA),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: focusNodes[index].hasFocus ? spektaRed : Colors.grey.shade300,
          width: 2,
        ),
      ),
      child: Center(
        child: TextField(
          controller: controllers[index],
          focusNode: focusNodes[index],
          textAlign: TextAlign.center,
          keyboardType: TextInputType.number,
          inputFormatters: [FilteringTextInputFormatter.digitsOnly],
          maxLength: 1,
          style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
          decoration: const InputDecoration(counterText: "", border: InputBorder.none, hintText: "-"),
          onChanged: (value) {
            if (value.length == 1 && index < 5) {
              focusNodes[index + 1].requestFocus();
            } else if (value.isEmpty && index > 0) {
              focusNodes[index - 1].requestFocus();
            }
            if (getOtpCode().length == 6) handleVerify();
            setState(() {}); 
          },
        ),
      ),
    );
  }

  Widget _buildResendUI() {
    return Column(
      children: [
        Text("Belum menerima kode?", style: TextStyle(color: Colors.grey[600])),
        TextButton(
          // Tombol otomatis disable jika _isResendClickable false
          onPressed: _isResendClickable ? _handleResendCode : null,
          child: Text(
            _isResendClickable ? "Kirim Ulang" : "Kirim Ulang dalam $_start s",
            style: TextStyle(
              color: _isResendClickable ? spektaRed : Colors.grey, 
              fontWeight: FontWeight.bold,
              fontSize: 16
            ),
          ),
        ),
      ],
    );
  }

  void _showSuccessDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Icon(Icons.check_circle, color: Colors.green, size: 70),
            const SizedBox(height: 20),
            const Text("Berhasil!", style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            const Text("Akun Anda telah aktif. Silakan masuk.", textAlign: TextAlign.center),
            const SizedBox(height: 30),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: spektaRed, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))),
              onPressed: () => Navigator.pushAndRemoveUntil(context, MaterialPageRoute(builder: (context) => const LoginPage()), (route) => false),
              child: const Text("LOGIN SEKARANG", style: TextStyle(color: Colors.white)),
            )
          ],
        ),
      ),
    );
  }
}