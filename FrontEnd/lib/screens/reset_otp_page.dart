import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'dart:async';
import 'dart:convert'; // 🔥 Ditambahkan untuk menangani jsonDecode respons Laravel
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
  
  // ============================================================
  // 🎨 PALET WARNA SPEKTA (KONSISTEN DENGAN TRYOUTDETAILPAGE)
  // ============================================================
  static const Color primaryRed       = Color(0xFFC5352C);
  static const Color accentTeal       = Color(0xFF2EA8AB);
  static const Color darkTeal         = Color(0xFF00696C);
  static const Color lightBlueBg      = Color(0xFFEFF4FF);
  static const Color pageBg           = Color(0xFFF1F5F9);
  static const Color textDark         = Color(0xFF0F172A);
  static const Color textDarkVariant = Color(0xFF334155);
  static const Color neutralGray     = Color(0xFF64748B);
  static const Color outlineVariant  = Color(0xFFE2BEBA);
  
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

    showDialog(context: context, builder: (_) => const Center(child: CircularProgressIndicator(color: accentTeal)));
    
    var resp = await AuthService.forgotPassword(widget.email);
    
    if (!mounted) return;
    Navigator.pop(context);

    if (resp.statusCode == 200) {
      _showSnackBar("Kode OTP baru telah dikirim ke email!", darkTeal);
      startTimer();
    } else {
      _showSnackBar("Gagal mengirim ulang kode.", primaryRed);
    }
  }

  // 🔥 FUNGSI UTAMA: Verifikasi OTP ke Gerbang Laravel Sebelum Pindah Halaman
  void _processVerification() async {
    String otp = getOtpCode();
    
    if (otp.length < 6) {
      _showSnackBar("Masukkan 6 digit kode lengkap!", primaryRed);
      return;
    }

    // Tampilkan Animasi Loading
    showDialog(
      context: context, 
      barrierDismissible: false, 
      builder: (_) => const Center(child: CircularProgressIndicator(color: accentTeal))
    );

    try {
      // 1. Tembak Validasi OTP ke Server Lokal Laravel
      var resp = await AuthService.validateResetOtp(widget.email, otp);
      
      if (!mounted) return;
      Navigator.pop(context); // Tutup loading

      // 2. Evaluasi Hasil Pemeriksaan Database PostgreSQL
      if (resp.statusCode == 200) {
        _showSnackBar("Kode OTP Valid!", darkTeal);
        
        // 3. JIKA COCOK: Loloskan navigasi ke halaman password baru bawa data OTP asli
        Navigator.push(
          context, 
          MaterialPageRoute(
            builder: (_) => NewPasswordPage(email: widget.email, otp: otp)
          )
        );
      } else {
        // 4. JIKA SALAH: Tahan di sini dan muntahkan pesan kegagalan dari server
        String serverError = "Kode OTP Salah atau Kadaluarsa";
        try {
          final data = jsonDecode(resp.body);
          serverError = data['message'] ?? serverError;
        } catch (_) {}

        _showSnackBar(serverError, primaryRed);
      }
    } catch (e) {
      if (mounted) Navigator.pop(context);
      debugPrint("❌ OTP VALIDATION EXCEPTION: $e");
      _showSnackBar("Gagal terhubung ke server lokal", primaryRed);
    }
  }

  void _showSnackBar(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        backgroundColor: color, 
        content: Text(msg, style: const TextStyle(fontWeight: FontWeight.bold)), 
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        duration: const Duration(seconds: 3),
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
        elevation: 0,
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        title: const Text(
          "Verifikasi Reset Password",
          style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
        ),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 25),
        child: Column(
          children: [
            const SizedBox(height: 30),
            Container(
              padding: const EdgeInsets.all(25),
              decoration: BoxDecoration(
                color: accentTeal.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.lock_reset_rounded, size: 80, color: accentTeal),
            ),
            const SizedBox(height: 30),
            const Text(
              "Masukkan Kode OTP", 
              style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: textDark)
            ),
            const SizedBox(height: 10),
            Text(
              "Kami telah mengirimkan 6 digit kode ke email:", 
              textAlign: TextAlign.center, 
              style: TextStyle(color: neutralGray)
            ),
            Text(
              widget.email, 
              style: const TextStyle(fontWeight: FontWeight.bold, color: textDark)
            ),
            const SizedBox(height: 50),
            
            // --- BOX INPUT OTP (6 BOXES) ---
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: List.generate(6, (index) => _buildOtpBox(index)),
            ),
            
            const SizedBox(height: 60),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: accentTeal,
                minimumSize: const Size(double.infinity, 60),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                elevation: 5,
                shadowColor: accentTeal.withOpacity(0.3),
              ),
              onPressed: _processVerification, // 🔥 Panggil fungsi validasi real-time baru
              child: const Text(
                "VERIFIKASI KODE", 
                style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)
              ),
            ),
            const SizedBox(height: 30),
            
            // Tombol Kirim Ulang
            TextButton(
              onPressed: _isResendClickable ? handleResendOtp : null,
              child: Text(
                _isResendClickable ? "Kirim Ulang Kode" : "Kirim Ulang dalam $_start s",
                style: TextStyle(
                  color: _isResendClickable ? accentTeal : neutralGray, 
                  fontWeight: FontWeight.bold
                ),
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
        color: focusNodes[index].hasFocus ? Colors.white : lightBlueBg,
        borderRadius: BorderRadius.circular(10),
        border: Border.all(
          color: focusNodes[index].hasFocus ? accentTeal : outlineVariant, 
          width: 2
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
          style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: textDark),
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