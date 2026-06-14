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
    for (var node in focusNodes) {
      node.dispose();
    }
    for (var controller in controllers) {
      controller.dispose();
    }
    super.dispose();
  }

  void startTimer() {
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
      builder: (_) => Center(child: CircularProgressIndicator(color: accentTeal))
    );
    
    var resp = await AuthService.verifyRegistration(widget.name, otp);
    
    if (!mounted) return;
    Navigator.pop(context);

    if (resp.statusCode == 200) { 
      _showSuccessDialog(); 
    } else { 
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          backgroundColor: primaryRed, 
          content: const Text("OTP Salah atau Kadaluarsa"),
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        )
      ); 
    }
  }

  void _handleResendCode() async {
    if (!_isResendClickable) return;

    showDialog(
      context: context, 
      barrierDismissible: false,
      builder: (_) => Center(child: CircularProgressIndicator(color: accentTeal))
    );

    try {
      var resp = await AuthService.resendOtp(widget.name);
      
      if (!mounted) return;
      Navigator.pop(context);

      if (resp.statusCode == 200) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: darkTeal, 
            content: const Text("Kode OTP baru telah dikirim!"),
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          )
        );
        startTimer();
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: primaryRed, 
            content: const Text("Gagal mengirim ulang kode."),
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          )
        );
      }
    } catch (e) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          backgroundColor: primaryRed, 
          content: const Text("Kesalahan Koneksi!"),
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        )
      );
    }
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
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 25),
        child: Column(
          children: [
            const SizedBox(height: 20),
            Container(
              padding: const EdgeInsets.all(25),
              decoration: BoxDecoration(
                color: accentTeal.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(Icons.mark_email_read_rounded, size: 80, color: accentTeal),
            ),
            const SizedBox(height: 25),
            Text(
              "Verifikasi Kode", 
              style: TextStyle(fontSize: 26, fontWeight: FontWeight.bold, color: textDark)
            ),
            const SizedBox(height: 10),
            Text(
              "Masukkan 6 digit kode yang dikirim ke email Anda.", 
              textAlign: TextAlign.center, 
              style: TextStyle(color: neutralGray, fontSize: 15)
            ),
            const SizedBox(height: 50),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: List.generate(6, (index) => _buildOtpBox(index)),
            ),
            const SizedBox(height: 60),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: accentTeal,
                minimumSize: const Size(double.infinity, 55),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                elevation: 5,
                shadowColor: accentTeal.withOpacity(0.3),
              ),
              onPressed: handleVerify,
              child: const Text(
                "VERIFIKASI AKUN", 
                style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)
              ),
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
        color: focusNodes[index].hasFocus ? Colors.white : lightBlueBg,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: focusNodes[index].hasFocus ? accentTeal : outlineVariant,
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
          style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: textDark),
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
        Text("Belum menerima kode?", style: TextStyle(color: neutralGray)),
        TextButton(
          onPressed: _isResendClickable ? _handleResendCode : null,
          child: Text(
            _isResendClickable ? "Kirim Ulang" : "Kirim Ulang dalam $_start s",
            style: TextStyle(
              color: _isResendClickable ? accentTeal : neutralGray, 
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
        backgroundColor: Colors.white,
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Icon(Icons.check_circle, color: darkTeal, size: 70),
            const SizedBox(height: 20),
            const Text("Berhasil!", style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: textDark)),
            const SizedBox(height: 10),
            const Text("Akun Anda telah aktif. Silakan masuk.", textAlign: TextAlign.center, style: TextStyle(color: textDarkVariant)),
            const SizedBox(height: 30),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: accentTeal, 
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                minimumSize: const Size(double.infinity, 48),
              ),
              onPressed: () => Navigator.pushAndRemoveUntil(context, MaterialPageRoute(builder: (context) => const LoginPage()), (route) => false),
              child: const Text("LOGIN SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            )
          ],
        ),
      ),
    );
  }
}