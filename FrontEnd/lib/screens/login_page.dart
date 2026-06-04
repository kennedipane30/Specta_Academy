// =====================================================================
//  login_page.dart — Spekta Academy  (NO flutter_svg dependency)
//  Logo Spekta digambar dengan CustomPainter Flutter native
//  Google Fonts: google_fonts sudah ada di pubspec.yaml ✓
// =====================================================================

import 'dart:convert';
import 'dart:math' as math;

import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';

import '../services/auth_service.dart';
import '../theme/theme_controller.dart';
import 'register_page.dart';
import 'main_screen.dart';
import 'forgot_password_page.dart';

// ─────────────────────────────────────────────────────────────────────
//  BRAND COLORS
// ─────────────────────────────────────────────────────────────────────
class _C {
  static const red1   = Color(0xFFC50337);
  static const red2   = Color(0xFF9C0412);
  static const red3   = Color(0xFF520102);
  static const red4   = Color(0xFF1A0003);
  static const gold   = Color(0xFFF5A623);
  static const dark   = Color(0xFF172033);
}

// ─────────────────────────────────────────────────────────────────────
//  LOGIN PAGE
// ─────────────────────────────────────────────────────────────────────
class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage>
    with SingleTickerProviderStateMixin {

  final _nameCtrl = TextEditingController();
  final _passCtrl = TextEditingController();
  bool _obscure   = true;
  bool _loading   = false;

  late AnimationController _ac;
  late Animation<double>   _fade;
  late Animation<Offset>   _slide;

  // ── Theme helpers ────────────────────────────────────────────────
  List<Color> _bg(bool d) => d
      ? const [Color(0xFF050002), Color(0xFF180308), _C.red3]
      : const [Color(0xFFFF8099), _C.red1, _C.red2, _C.red3, _C.red4];

  Color _cardBg(bool d)    => d ? Colors.white.withOpacity(0.07) : Colors.white;
  Color _inputBg(bool d)   => d ? Colors.white.withOpacity(0.06) : const Color(0xFFFFF5F7);
  Color _inputBdr(bool d)  => d ? Colors.white.withOpacity(0.10) : const Color(0xFFFFCDD2);
  Color _iconCol(bool d)   => d ? Colors.white60 : _C.red2;
  Color _labelCol(bool d)  => d ? Colors.white38 : Colors.black38;
  Color _fieldTxt(bool d)  => d ? Colors.white   : _C.dark;
  Color _subTxt(bool d)    => Colors.white.withOpacity(d ? 0.55 : 0.82);
  Color _cardLbl(bool d)   => d ? Colors.white   : _C.dark;
  Color _cardSub(bool d)   => d ? Colors.white38 : Colors.black38;
  Color _fgPw(bool d)      => d ? Colors.white60 : _C.red2;

  // ── Lifecycle ────────────────────────────────────────────────────
  @override
  void initState() {
    super.initState();
    _ac = AnimationController(vsync: this, duration: const Duration(milliseconds: 850));
    _fade  = CurvedAnimation(parent: _ac, curve: Curves.easeOut);
    _slide = Tween<Offset>(begin: const Offset(0, 0.07), end: Offset.zero)
        .animate(CurvedAnimation(parent: _ac, curve: Curves.easeOutCubic));
    _ac.forward();
  }

  @override
  void dispose() {
    _ac.dispose();
    _nameCtrl.dispose();
    _passCtrl.dispose();
    super.dispose();
  }

  // ── Login ────────────────────────────────────────────────────────
  Future<void> _login() async {
    if (_nameCtrl.text.trim().isEmpty || _passCtrl.text.isEmpty) {
      _snack('Nama dan Password wajib diisi!', err: true);
      return;
    }
    setState(() => _loading = true);
    try {
      final r = await AuthService.login(_nameCtrl.text.trim(), _passCtrl.text);
      if (!mounted) return;
      setState(() => _loading = false);
      if (r.statusCode == 200) {
        final d = jsonDecode(r.body);
        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(
            builder: (_) => MainScreen(
              userName: d['user']['name'],
              token: d['token'],
              userProfileData: d['user'],
            ),
          ),
          (_) => false,
        );
      } else {
        final e = jsonDecode(r.body);
        _snack(e['message'] ?? 'Nama atau Password salah!', err: true);
      }
    } catch (_) {
      if (mounted) {
        setState(() => _loading = false);
        _snack('Koneksi gagal. Periksa server.', err: true);
      }
    }
  }

  void _snack(String msg, {bool err = false}) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text(msg, style: const TextStyle(fontWeight: FontWeight.w600)),
      backgroundColor: err ? _C.red2 : const Color(0xFF22C55E),
      behavior: SnackBarBehavior.floating,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
      margin: const EdgeInsets.all(16),
    ));
  }

  // ─────────────────────────────────────────────────────────────────
  //  BUILD
  // ─────────────────────────────────────────────────────────────────
  @override
  Widget build(BuildContext context) {
    final theme  = Provider.of<ThemeController>(context);
    final isDark = theme.isDark;

    return Scaffold(
      body: AnimatedContainer(
        duration: const Duration(milliseconds: 350),
        width: double.infinity,
        height: MediaQuery.of(context).size.height,
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: _bg(isDark),
          ),
        ),
        child: Stack(children: [
          // Dekorasi latar
          const Positioned.fill(child: _BgDecor()),

          // Konten
          SafeArea(
            child: FadeTransition(
              opacity: _fade,
              child: SlideTransition(
                position: _slide,
                child: SingleChildScrollView(
                  physics: const ClampingScrollPhysics(),
                  padding: const EdgeInsets.fromLTRB(22, 8, 22, 32),
                  child: Column(children: [

                    // Toggle dark mode
                    _ThemeToggle(isDark: isDark, theme: theme),
                    const SizedBox(height: 26),

                    // ── LOGO SPEKTA (CustomPainter) ──────────────
                    _SpektaLogo(),
                    const SizedBox(height: 16),

                    // Judul
                    Text(
                      'SPEKTA ACADEMY',
                      style: GoogleFonts.oswald(
                        fontSize: 26,
                        fontWeight: FontWeight.w700,
                        color: Colors.white,
                        letterSpacing: 2.5,
                      ),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      'Achieve Your Dream of Serving the Nation',
                      textAlign: TextAlign.center,
                      style: GoogleFonts.nunito(
                        fontSize: 12.5,
                        fontStyle: FontStyle.italic,
                        color: _subTxt(isDark),
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                    const SizedBox(height: 34),

                    // Kartu login
                    _buildCard(isDark),
                    const SizedBox(height: 20),

                    // Divider ATAU
                    _buildDivider(),
                    const SizedBox(height: 16),

                    // Tombol Google
                    _buildGoogleBtn(),
                    const SizedBox(height: 28),

                    // Link daftar
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text(
                          'Belum punya akun? ',
                          style: TextStyle(color: _subTxt(isDark), fontSize: 13),
                        ),
                        GestureDetector(
                          onTap: () => Navigator.push(context,
                              MaterialPageRoute(builder: (_) => const RegisterPage())),
                          child: const Text(
                            'Daftar Sekarang',
                            style: TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.w900,
                              fontSize: 13,
                              decoration: TextDecoration.underline,
                              decorationColor: Colors.white,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ]),
                ),
              ),
            ),
          ),
        ]),
      ),
    );
  }

  // ─────────────────────────────────────────────────────────────────
  //  CARD LOGIN
  // ─────────────────────────────────────────────────────────────────
  Widget _buildCard(bool isDark) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.fromLTRB(22, 24, 22, 24),
      decoration: BoxDecoration(
        color: _cardBg(isDark),
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: Colors.white.withOpacity(isDark ? 0.08 : 0.65)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(isDark ? 0.32 : 0.12),
            blurRadius: 34,
            offset: const Offset(0, 16),
          ),
        ],
      ),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [

        Text('Masuk ke Akun',
            style: GoogleFonts.nunito(
              fontSize: 18, fontWeight: FontWeight.w900, color: _cardLbl(isDark))),
        const SizedBox(height: 4),
        Text('Gunakan nama & password yang terdaftar',
            style: TextStyle(fontSize: 12, color: _cardSub(isDark))),
        const SizedBox(height: 22),

        // Full Name
        _field(isDark,
            ctrl: _nameCtrl,
            label: 'Full Name',
            hint: 'Masukkan nama lengkap',
            icon: Icons.person_outline_rounded),
        const SizedBox(height: 14),

        // Password
        _field(isDark,
            ctrl: _passCtrl,
            label: 'Password',
            hint: 'Masukkan password',
            icon: Icons.lock_outline_rounded,
            isPass: true),

        // Forgot PW
        Align(
          alignment: Alignment.centerRight,
          child: TextButton(
            onPressed: () => Navigator.push(context,
                MaterialPageRoute(builder: (_) => const ForgotPasswordPage())),
            style: TextButton.styleFrom(
              padding: const EdgeInsets.only(top: 4, bottom: 2),
              tapTargetSize: MaterialTapTargetSize.shrinkWrap,
            ),
            child: Text('Forgot Password?',
                style: TextStyle(
                  color: _fgPw(isDark),
                  fontWeight: FontWeight.w700,
                  fontSize: 12.5,
                )),
          ),
        ),
        const SizedBox(height: 14),

        // Tombol MASUK
        _buildLoginBtn(),
      ]),
    );
  }

  // ── Input field ──────────────────────────────────────────────────
  Widget _field(bool isDark,
      {required TextEditingController ctrl,
      required String label,
      required String hint,
      required IconData icon,
      bool isPass = false}) {
    return TextField(
      controller: ctrl,
      obscureText: isPass ? _obscure : false,
      style: TextStyle(
          color: _fieldTxt(isDark), fontSize: 14, fontWeight: FontWeight.w600),
      decoration: InputDecoration(
        filled: true,
        fillColor: _inputBg(isDark),
        labelText: label,
        hintText: hint,
        hintStyle: TextStyle(color: _labelCol(isDark), fontSize: 13),
        labelStyle: TextStyle(
            color: _labelCol(isDark), fontSize: 13, fontWeight: FontWeight.w500),
        prefixIcon: Padding(
          padding: const EdgeInsets.only(left: 4),
          child: Icon(icon, color: _iconCol(isDark), size: 22),
        ),
        suffixIcon: isPass
            ? IconButton(
                icon: Icon(
                  _obscure ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                  color: _iconCol(isDark), size: 20,
                ),
                onPressed: () => setState(() => _obscure = !_obscure),
              )
            : null,
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: BorderSide(color: _inputBdr(isDark), width: 1.2),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: const BorderSide(color: _C.red1, width: 2.0),
        ),
        contentPadding:
            const EdgeInsets.symmetric(vertical: 17, horizontal: 16),
      ),
    );
  }

  // ── Tombol MASUK ─────────────────────────────────────────────────
  Widget _buildLoginBtn() {
    return Container(
      width: double.infinity,
      height: 56,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(18),
        gradient: const LinearGradient(
          colors: [_C.red1, _C.red2, _C.red3],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        boxShadow: [
          BoxShadow(
              color: _C.red2.withOpacity(0.42),
              blurRadius: 18,
              offset: const Offset(0, 8)),
        ],
      ),
      child: ElevatedButton(
        onPressed: _loading ? null : _login,
        style: ElevatedButton.styleFrom(
          backgroundColor: Colors.transparent,
          shadowColor: Colors.transparent,
          shape:
              RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
        ),
        child: _loading
            ? const SizedBox(
                width: 22, height: 22,
                child: CircularProgressIndicator(
                    color: Colors.white, strokeWidth: 2.5))
            : Text('MASUK',
                style: GoogleFonts.oswald(
                  color: Colors.white,
                  fontWeight: FontWeight.w600,
                  fontSize: 16,
                  letterSpacing: 2.5,
                )),
      ),
    );
  }

  // ── Divider ATAU ─────────────────────────────────────────────────
  Widget _buildDivider() {
    return Row(children: [
      Expanded(child: Divider(color: Colors.white.withOpacity(0.25), thickness: 1)),
      Padding(
        padding: const EdgeInsets.symmetric(horizontal: 12),
        child: Text('ATAU',
            style: TextStyle(
              color: Colors.white.withOpacity(0.45),
              fontSize: 11,
              fontWeight: FontWeight.w700,
              letterSpacing: 1.5,
            )),
      ),
      Expanded(child: Divider(color: Colors.white.withOpacity(0.25), thickness: 1)),
    ]);
  }

  // ── Tombol Google ────────────────────────────────────────────────
  Widget _buildGoogleBtn() {
    return SizedBox(
      width: double.infinity,
      height: 52,
      child: OutlinedButton(
        onPressed: () {/* TODO: Google Sign-In */},
        style: OutlinedButton.styleFrom(
          side: BorderSide(color: Colors.white.withOpacity(0.28), width: 1.2),
          shape:
              RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          backgroundColor: Colors.white.withOpacity(0.07),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Google "G" icon painted natively
            CustomPaint(size: const Size(22, 22), painter: _GoogleIconPainter()),
            const SizedBox(width: 12),
            const Text('Lanjutkan dengan Google',
                style: TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.w700,
                    fontSize: 14)),
          ],
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────
//  SPEKTA LOGO — CustomPainter (garuda + ring + text)
// ─────────────────────────────────────────────────────────────────────
class _SpektaLogo extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Container(
      width: 116, height: 116,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        border: Border.all(color: _C.gold, width: 3.5),
        boxShadow: [
          BoxShadow(
              color: _C.red1.withOpacity(0.55),
              blurRadius: 32, spreadRadius: 4,
              offset: const Offset(0, 8)),
          BoxShadow(
              color: _C.gold.withOpacity(0.28),
              blurRadius: 20, spreadRadius: 0),
        ],
      ),
      child: ClipOval(
        child: CustomPaint(
          size: const Size(116, 116),
          painter: _SpektaLogoPainter(),
        ),
      ),
    );
  }
}

class _SpektaLogoPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final cx = size.width / 2;
    final cy = size.height / 2;
    final r  = size.width / 2;

    // ── Background merah ─────────────────────────────────────────
    canvas.drawCircle(
      Offset(cx, cy), r,
      Paint()..color = _C.red2,
    );

    // ── Ring putih dalam ────────────────────────────────────────
    canvas.drawCircle(Offset(cx, cy), r - 4,
        Paint()..color = Colors.white.withOpacity(0.35)
          ..style = PaintingStyle.stroke
          ..strokeWidth = 1.0);
    canvas.drawCircle(Offset(cx, cy), r - 9,
        Paint()..color = Colors.white.withOpacity(0.20)
          ..style = PaintingStyle.stroke
          ..strokeWidth = 0.7);

    final white  = Paint()..color = Colors.white;
    final wFill  = Paint()..color = Colors.white..style = PaintingStyle.fill;
    final gold   = Paint()..color = _C.gold;
    final redFill= Paint()..color = _C.red2;

    // ── SAYAP KIRI ───────────────────────────────────────────────
    final leftWing = Path()
      ..moveTo(cx, cy - 10)
      ..cubicTo(cx - 12, cy - 20, cx - 30, cy - 18, cx - 38, cy - 8)
      ..cubicTo(cx - 28, cy - 14, cx - 18, cy - 10, cx - 8, cy - 2)
      ..close();
    canvas.drawPath(leftWing, wFill);

    final leftWing2 = Path()
      ..moveTo(cx, cy - 10)
      ..cubicTo(cx - 10, cy - 24, cx - 26, cy - 26, cx - 38, cy - 18)
      ..cubicTo(cx - 28, cy - 18, cx - 18, cy - 14, cx - 6, cy - 4)
      ..close();
    canvas.drawPath(leftWing2,
        Paint()..color = Colors.white.withOpacity(0.75));

    // ── SAYAP KANAN ──────────────────────────────────────────────
    final rightWing = Path()
      ..moveTo(cx, cy - 10)
      ..cubicTo(cx + 12, cy - 20, cx + 30, cy - 18, cx + 38, cy - 8)
      ..cubicTo(cx + 28, cy - 14, cx + 18, cy - 10, cx + 8, cy - 2)
      ..close();
    canvas.drawPath(rightWing, wFill);

    final rightWing2 = Path()
      ..moveTo(cx, cy - 10)
      ..cubicTo(cx + 10, cy - 24, cx + 26, cy - 26, cx + 38, cy - 18)
      ..cubicTo(cx + 28, cy - 18, cx + 18, cy - 14, cx + 6, cy - 4)
      ..close();
    canvas.drawPath(rightWing2,
        Paint()..color = Colors.white.withOpacity(0.75));

    // ── BADAN ────────────────────────────────────────────────────
    canvas.drawOval(
      Rect.fromCenter(center: Offset(cx, cy + 4), width: 18, height: 26),
      wFill,
    );

    // ── KEPALA ───────────────────────────────────────────────────
    canvas.drawCircle(Offset(cx, cy - 14), 12, wFill);

    // ── PARUH ────────────────────────────────────────────────────
    final beak = Path()
      ..moveTo(cx, cy - 8)
      ..lineTo(cx + 9, cy - 4)
      ..lineTo(cx, cy - 2)
      ..close();
    canvas.drawPath(beak, gold);

    // ── MATA ─────────────────────────────────────────────────────
    canvas.drawCircle(Offset(cx + 4, cy - 16), 2.8, redFill);
    canvas.drawCircle(Offset(cx + 4, cy - 16), 1.4,
        Paint()..color = const Color(0xFF1A0003));

    // ── EKOR ─────────────────────────────────────────────────────
    final tail = Path()
      ..moveTo(cx - 10, cy + 16)
      ..quadraticBezierTo(cx - 12, cy + 30, cx - 6, cy + 34)
      ..quadraticBezierTo(cx - 2, cy + 30, cx, cy + 20)
      ..close();
    canvas.drawPath(tail, wFill);

    final tail2 = Path()
      ..moveTo(cx, cy + 20)
      ..quadraticBezierTo(cx + 2, cy + 30, cx + 6, cy + 34)
      ..quadraticBezierTo(cx + 12, cy + 30, cx + 10, cy + 16)
      ..close();
    canvas.drawPath(tail2, wFill);

    // Bulu ekor tengah
    canvas.drawLine(Offset(cx, cy + 18), Offset(cx, cy + 36),
        Paint()..color = Colors.white..strokeWidth = 3..strokeCap = StrokeCap.round);

    // ── CAKAR ────────────────────────────────────────────────────
    final clawL = Path()
      ..moveTo(cx - 8, cy + 17)
      ..cubicTo(cx - 16, cy + 22, cx - 18, cy + 28, cx - 14, cy + 30)
      ..lineTo(cx - 8, cy + 22)
      ..close();
    canvas.drawPath(clawL, wFill);

    final clawR = Path()
      ..moveTo(cx + 8, cy + 17)
      ..cubicTo(cx + 16, cy + 22, cx + 18, cy + 28, cx + 14, cy + 30)
      ..lineTo(cx + 8, cy + 22)
      ..close();
    canvas.drawPath(clawR, wFill);

    // ── PERISAI dada ────────────────────────────────────────────
    final shield = Path()
      ..moveTo(cx - 7, cy - 2)
      ..lineTo(cx, cy - 6)
      ..lineTo(cx + 7, cy - 2)
      ..lineTo(cx + 7, cy + 10)
      ..lineTo(cx, cy + 14)
      ..lineTo(cx - 7, cy + 10)
      ..close();
    canvas.drawPath(shield, redFill);
    canvas.drawPath(shield, Paint()
      ..color = Colors.white
      ..style = PaintingStyle.stroke
      ..strokeWidth = 1.2);

    // ── BINTANG kiri-kanan ──────────────────────────────────────
    _drawStar(canvas, Offset(cx - 36, cy + 2), 5.5, gold);
    _drawStar(canvas, Offset(cx + 36, cy + 2), 5.5, gold);

    // ── TEKS "SPEKTA" di atas ───────────────────────────────────
    _drawArcText(canvas, size, 'S P E K T A', r - 12, -math.pi * 0.68,
        isTop: true);

    // ── TEKS "ACADEMY" di bawah ─────────────────────────────────
    _drawArcText(canvas, size, 'A C A D E M Y', r - 12, math.pi * 0.55,
        isTop: false);
  }

  void _drawStar(Canvas canvas, Offset center, double size, Paint paint) {
    final path = Path();
    for (int i = 0; i < 5; i++) {
      final outerAngle = (i * 4 * math.pi / 5) - math.pi / 2;
      final innerAngle = outerAngle + (2 * math.pi / 10);
      final outer = Offset(
        center.dx + size * math.cos(outerAngle),
        center.dy + size * math.sin(outerAngle),
      );
      final inner = Offset(
        center.dx + (size * 0.4) * math.cos(innerAngle),
        center.dy + (size * 0.4) * math.sin(innerAngle),
      );
      if (i == 0) {
        path.moveTo(outer.dx, outer.dy);
      } else {
        path.lineTo(outer.dx, outer.dy);
      }
      path.lineTo(inner.dx, inner.dy);
    }
    path.close();
    canvas.drawPath(path, paint);
  }

  void _drawArcText(Canvas canvas, Size size, String text, double radius,
      double startAngle,
      {required bool isTop}) {
    final cx = size.width / 2;
    final cy = size.height / 2;

    final tp = TextPainter(textDirection: TextDirection.ltr);
    final charCount = text.length;
    // arc span berdasarkan panjang teks
    final totalAngle = (charCount * 0.095).clamp(0.0, math.pi * 0.9);
    final angleStep  = totalAngle / (charCount - 1);

    for (int i = 0; i < charCount; i++) {
      final angle = startAngle + (i - (charCount - 1) / 2) * angleStep;

      tp.text = TextSpan(
        text: text[i],
        style: const TextStyle(
          color: Colors.white,
          fontSize: 8,
          fontWeight: FontWeight.w900,
          letterSpacing: 0,
        ),
      );
      tp.layout();

      canvas.save();
      canvas.translate(
        cx + radius * math.cos(angle),
        cy + radius * math.sin(angle),
      );
      // rotasi karakter ikut kurva
      canvas.rotate(angle + (isTop ? -math.pi / 2 : math.pi / 2));
      canvas.translate(-tp.width / 2, -tp.height / 2);
      tp.paint(canvas, Offset.zero);
      canvas.restore();
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter old) => false;
}

// ─────────────────────────────────────────────────────────────────────
//  GOOGLE ICON — CustomPainter (4 warna asli Google)
// ─────────────────────────────────────────────────────────────────────
class _GoogleIconPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size s) {
    final cx = s.width / 2;
    final cy = s.height / 2;
    final r  = s.width / 2 - 1;

    // Clip circle
    canvas.clipPath(Path()..addOval(Rect.fromCircle(center: Offset(cx, cy), radius: r)));

    // Blue (top-right)
    canvas.drawArc(Rect.fromCircle(center: Offset(cx, cy), radius: r),
        -math.pi / 4, math.pi, false,
        Paint()..color = const Color(0xFF4285F4)..style = PaintingStyle.fill);

    // Green (bottom-right)
    canvas.drawArc(Rect.fromCircle(center: Offset(cx, cy), radius: r),
        math.pi * 3 / 4, math.pi / 2, false,
        Paint()..color = const Color(0xFF34A853)..style = PaintingStyle.fill);

    // Yellow (bottom-left)
    canvas.drawArc(Rect.fromCircle(center: Offset(cx, cy), radius: r),
        math.pi * 5 / 4, math.pi / 2, false,
        Paint()..color = const Color(0xFFFBBC05)..style = PaintingStyle.fill);

    // Red (top-left)
    canvas.drawArc(Rect.fromCircle(center: Offset(cx, cy), radius: r),
        math.pi * 7 / 4, math.pi / 2, false,
        Paint()..color = const Color(0xFFEA4335)..style = PaintingStyle.fill);

    // White center + G bar
    canvas.drawCircle(Offset(cx, cy), r * 0.6,
        Paint()..color = Colors.white);
    canvas.drawRect(
      Rect.fromLTWH(cx, cy - r * 0.18, r * 0.75, r * 0.36),
      Paint()..color = const Color(0xFF4285F4),
    );
  }

  @override
  bool shouldRepaint(covariant CustomPainter old) => false;
}

// ─────────────────────────────────────────────────────────────────────
//  THEME TOGGLE BUTTON
// ─────────────────────────────────────────────────────────────────────
class _ThemeToggle extends StatelessWidget {
  final bool isDark;
  final ThemeController theme;
  const _ThemeToggle({required this.isDark, required this.theme});

  @override
  Widget build(BuildContext context) {
    return Align(
      alignment: Alignment.centerRight,
      child: GestureDetector(
        onTap: theme.toggleTheme,
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 250),
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
          decoration: BoxDecoration(
            color: Colors.white.withOpacity(isDark ? 0.10 : 0.18),
            borderRadius: BorderRadius.circular(30),
            border: Border.all(color: Colors.white.withOpacity(0.22)),
          ),
          child: Icon(
            isDark ? Icons.dark_mode_rounded : Icons.light_mode_rounded,
            color: Colors.white,
            size: 20,
          ),
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────
//  BACKGROUND DECORATION
// ─────────────────────────────────────────────────────────────────────
class _BgDecor extends StatelessWidget {
  const _BgDecor();

  @override
  Widget build(BuildContext context) =>
      CustomPaint(painter: _BgDecorPainter(), child: const SizedBox.expand());
}

class _BgDecorPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final ring = Paint()
      ..color = Colors.white.withOpacity(0.045)
      ..style = PaintingStyle.stroke
      ..strokeWidth = 1.0;

    canvas.drawCircle(Offset(-size.width * 0.18, size.height * 0.04),
        size.width * 0.58, ring);
    canvas.drawCircle(Offset(size.width * 1.18, size.height * 0.88),
        size.width * 0.46, ring);
    canvas.drawCircle(Offset(size.width * 0.90, size.height * 0.40),
        size.width * 0.18, ring);

    final dot = Paint()
      ..color = Colors.white.withOpacity(0.07)
      ..style = PaintingStyle.fill;
    for (int r = 0; r < 5; r++) {
      for (int c = 0; c < 4; c++) {
        canvas.drawCircle(
          Offset(size.width * 0.70 + c * 20.0, size.height * 0.10 + r * 20.0),
          2.2, dot,
        );
      }
    }

    final line = Paint()
      ..color = Colors.white.withOpacity(0.04)
      ..strokeWidth = 0.8;
    for (int i = 0; i < 5; i++) {
      final x = size.width * 0.04 + i * 24.0;
      canvas.drawLine(
        Offset(x, size.height * 0.62), Offset(x + 32, size.height * 0.82), line);
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter old) => false;
}