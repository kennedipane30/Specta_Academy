// =====================================================================
//  login_page.dart — Spekta Academy (Updated UI to match HTML Design)
// =====================================================================

import 'dart:convert';
import 'dart:ui'; // For ImageFilter (Glassmorphism)

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../services/auth_service.dart';
import '../theme/theme_controller.dart';
import 'register_page.dart';
import 'main_screen.dart';
import 'forgot_password_page.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage>
    with SingleTickerProviderStateMixin {
  final _nameCtrl = TextEditingController();
  final _passCtrl = TextEditingController();
  bool _obscure = true;
  bool _loading = false;

  late AnimationController _ac;
  late Animation<double> _fade;
  late Animation<Offset> _slide;

  // --- Tailwind Colors from HTML ---
  static const Color gradStart = Color(0xFFC5352C);
  static const Color gradEnd = Color(0xFF2EA8AB);
  static const Color primaryColor = Color(0xFFA21B17);
  static const Color primaryContainer = Color(0xFFC5352C);
  static const Color secondaryColor = Color(0xFF00696C);
  static const Color textDark = Color(0xFF0B1C30);
  static const Color textVariant = Color(0xFF5A413D);
  static const Color outlineColor = Color(0xFFE2BEBA);

  // ── Lifecycle ─────────────────────────────────────────────────────
  @override
  void initState() {
    super.initState();
    _ac = AnimationController(
        vsync: this, duration: const Duration(milliseconds: 700));
    _fade = CurvedAnimation(parent: _ac, curve: Curves.easeOut);
    _slide = Tween<Offset>(begin: const Offset(0, 0.06), end: Offset.zero)
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

  // ── Login handler ─────────────────────────────────────────────────
  Future<void> _login() async {
    if (_nameCtrl.text.trim().isEmpty || _passCtrl.text.isEmpty) {
      _snack('Nama dan Password wajib diisi!', isError: true);
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
        _snack(e['message'] ?? 'Nama atau Password salah!', isError: true);
      }
    } catch (_) {
      if (mounted) {
        setState(() => _loading = false);
        _snack('Koneksi gagal. Periksa server.', isError: true);
      }
    }
  }

  void _snack(String msg, {bool isError = false}) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text(msg, style: const TextStyle(fontWeight: FontWeight.w600)),
      backgroundColor: isError ? primaryColor : const Color(0xFF22C55E),
      behavior: SnackBarBehavior.floating,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
      margin: const EdgeInsets.all(16),
    ));
  }

  // ── Build ─────────────────────────────────────────────────────────
  @override
  Widget build(BuildContext context) {
    final theme = Provider.of<ThemeController>(context);
    final isDark = theme.isDark;

    return Scaffold(
      extendBody: true,
      resizeToAvoidBottomInset: true,
      body: Stack(
        children: [
          // 1. Gradient Background
          Positioned.fill(
            child: Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: isDark
                      ? [const Color(0xFF4A0000), const Color(0xFF00383A)]
                      : [gradStart, gradEnd],
                ),
              ),
            ),
          ),

          // 2. Main Content (Grid painter removed)
          SafeArea(
            bottom: false,
            child: FadeTransition(
              opacity: _fade,
              child: SlideTransition(
                position: _slide,
                child: Column(
                  children: [
                    // Theme Toggle (Right Top)
                    Align(
                      alignment: Alignment.centerRight,
                      child: Padding(
                        padding: const EdgeInsets.only(right: 16, top: 8),
                        child: GestureDetector(
                          onTap: theme.toggleTheme,
                          child: Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: Colors.white.withOpacity(0.2),
                              shape: BoxShape.circle,
                            ),
                            child: Icon(
                              isDark
                                  ? Icons.dark_mode_rounded
                                  : Icons.light_mode_rounded,
                              color: Colors.white,
                              size: 20,
                            ),
                          ),
                        ),
                      ),
                    ),

                    Expanded(
                      child: SingleChildScrollView(
                        padding: const EdgeInsets.symmetric(horizontal: 24),
                        child: Column(
                          children: [
                            const SizedBox(height: 10),
                            _buildHeader(),
                            const SizedBox(height: 32),
                            _buildLoginCard(isDark),
                            const SizedBox(height: 24),
                            _buildFooterLinks(),
                            const SizedBox(height: 50),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  // ── Header (Icon & Titles) ────────────────────────────────────────
  Widget _buildHeader() {
    return Column(
      children: [
        // Frosted Icon Container
        ClipRRect(
          borderRadius: BorderRadius.circular(24),
          child: BackdropFilter(
            filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
            child: Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.2),
                borderRadius: BorderRadius.circular(24),
              ),
              child: const Icon(
                Icons.school_rounded,
                color: Colors.white,
                size: 48,
              ),
            ),
          ),
        ),
        const SizedBox(height: 16),
        const Text(
          'Spekta Academy',
          style: TextStyle(
            color: Colors.white,
            fontSize: 28,
            fontWeight: FontWeight.w800,
            letterSpacing: -0.5,
          ),
        ),
        const SizedBox(height: 6),
        Text(
          'Your path to academic excellence',
          style: TextStyle(
            color: Colors.white.withOpacity(0.9),
            fontSize: 16,
            fontWeight: FontWeight.w400,
          ),
        ),
      ],
    );
  }

  // ── Login Card (Glassmorphism) ────────────────────────────────────
  Widget _buildLoginCard(bool isDark) {
    final bgColor = isDark
        ? const Color(0xFF1E2024).withOpacity(0.85)
        : Colors.white.withOpacity(0.95);

    return ClipRRect(
      borderRadius: BorderRadius.circular(32),
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: 12, sigmaY: 12),
        child: Container(
          width: double.infinity,
          padding: const EdgeInsets.all(32),
          decoration: BoxDecoration(
            color: bgColor,
            borderRadius: BorderRadius.circular(32),
            border: Border.all(
              color: Colors.white.withOpacity(0.2),
            ),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.1),
                blurRadius: 24,
                offset: const Offset(0, 10),
              )
            ],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Welcome Back',
                style: TextStyle(
                  color: isDark ? Colors.white : textDark,
                  fontSize: 24,
                  fontWeight: FontWeight.w700,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                'Sign in to continue your journey',
                style: TextStyle(
                  color: isDark ? Colors.white70 : textVariant,
                  fontSize: 14,
                ),
              ),
              const SizedBox(height: 28),

              // Full Name Field
              _fieldLabel(isDark, 'Full Name'),
              const SizedBox(height: 8),
              _buildInput(
                isDark,
                ctrl: _nameCtrl,
                hint: 'John Doe',
                icon: Icons.person_outline_rounded,
                inputType: TextInputType.name,
              ),
              const SizedBox(height: 20),

              // Password Field Header (Label + Forgot)
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  _fieldLabel(isDark, 'Password'),
                  GestureDetector(
                    onTap: () => Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (_) => const ForgotPasswordPage()),
                    ),
                    child: Text(
                      'Forgot?',
                      style: TextStyle(
                        color: isDark ? Colors.white70 : secondaryColor,
                        fontSize: 14,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              _buildPasswordInput(isDark),

              const SizedBox(height: 32),
              _buildActionBtn(),
            ],
          ),
        ),
      ),
    );
  }

  // ── Footer Text (New to Academy) ──────────────────────────────────
  Widget _buildFooterLinks() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Text(
          'New to Academy? ',
          style: TextStyle(
            color: Colors.white.withOpacity(0.9),
            fontSize: 14,
          ),
        ),
        GestureDetector(
          onTap: () => Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => const RegisterPage()),
          ),
          child: const Text(
            'Create Account',
            style: TextStyle(
              color: Colors.white,
              fontSize: 14,
              fontWeight: FontWeight.w600,
              decoration: TextDecoration.underline,
              decorationColor: Colors.white,
            ),
          ),
        ),
      ],
    );
  }

  // ── Helpers field ─────────────────────────────────────────────────
  Widget _fieldLabel(bool isDark, String text) {
    return Text(
      text,
      style: TextStyle(
        color: isDark ? Colors.white70 : textVariant,
        fontSize: 14,
        fontWeight: FontWeight.w600,
      ),
    );
  }

  InputDecoration _baseDecoration(bool isDark, String hint, IconData icon,
      {Widget? suffixIcon}) {
    final bgColor = isDark ? Colors.black.withOpacity(0.2) : Colors.white;
    final borderColor =
        isDark ? Colors.white.withOpacity(0.1) : outlineColor;
    final hintColor =
        isDark ? Colors.white30 : outlineColor.withOpacity(0.8);
    final iconColor = isDark ? Colors.white54 : outlineColor;

    return InputDecoration(
      filled: true,
      fillColor: bgColor,
      hintText: hint,
      hintStyle: TextStyle(color: hintColor, fontSize: 16),
      prefixIcon: Icon(icon, color: iconColor, size: 22),
      suffixIcon: suffixIcon,
      contentPadding:
          const EdgeInsets.symmetric(vertical: 18, horizontal: 16),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(14),
        borderSide: BorderSide(color: borderColor),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(14),
        borderSide: const BorderSide(color: secondaryColor, width: 2),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(14),
        borderSide: const BorderSide(color: primaryColor),
      ),
      focusedErrorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(14),
        borderSide: const BorderSide(color: primaryColor),
      ),
    );
  }

  Widget _buildInput(bool isDark,
      {required TextEditingController ctrl,
      required String hint,
      required IconData icon,
      TextInputType inputType = TextInputType.text}) {
    return TextField(
      controller: ctrl,
      keyboardType: inputType,
      style: TextStyle(
        color: isDark ? Colors.white : textDark,
        fontSize: 16,
      ),
      decoration: _baseDecoration(isDark, hint, icon),
    );
  }

  Widget _buildPasswordInput(bool isDark) {
    return TextField(
      controller: _passCtrl,
      obscureText: _obscure,
      style: TextStyle(
        color: isDark ? Colors.white : textDark,
        fontSize: 16,
      ),
      decoration: _baseDecoration(
        isDark,
        '••••••••',
        Icons.lock_outline_rounded,
        suffixIcon: IconButton(
          icon: Icon(
            _obscure
                ? Icons.visibility_off_outlined
                : Icons.visibility_outlined,
            color: isDark ? Colors.white54 : outlineColor,
            size: 22,
          ),
          onPressed: () => setState(() => _obscure = !_obscure),
        ),
      ),
    );
  }

  // ── Tombol Red Sign In ────────────────────────────────────────────
  Widget _buildActionBtn() {
    return Container(
      width: double.infinity,
      height: 56,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(14),
        boxShadow: [
          BoxShadow(
            color: primaryColor.withOpacity(0.2),
            blurRadius: 15,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: ElevatedButton(
        onPressed: _loading ? null : _login,
        style: ElevatedButton.styleFrom(
          backgroundColor: primaryColor,
          foregroundColor: Colors.white,
          elevation: 0,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(14),
          ),
        ),
        child: _loading
            ? const SizedBox(
                width: 24,
                height: 24,
                child: CircularProgressIndicator(
                  color: Colors.white,
                  strokeWidth: 2.5,
                ),
              )
            : const Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(
                    'Sign In to Account',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  SizedBox(width: 8),
                  Icon(Icons.arrow_forward_rounded, size: 20),
                ],
              ),
      ),
    );
  }
}