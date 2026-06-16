import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';

import '../services/auth_service.dart';
import '../theme/theme_controller.dart';
import 'otp_page.dart';

class RegisterPage extends StatefulWidget {
  const RegisterPage({super.key});

  @override
  State<RegisterPage> createState() => _RegisterPageState();
}

class _RegisterPageState extends State<RegisterPage> {
  final _formKey = GlobalKey<FormState>();

  bool _obscurePass = true;
  bool _obscureConfirm = true;

  final _nameCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _waCtrl = TextEditingController();
  final _passCtrl = TextEditingController();
  final _confirmPassCtrl = TextEditingController();

  static const Color mainRed = Color(0xFFD32F2F);
  static const Color textDark = Color(0xFF1F2028);
  static const Color textMuted = Color(0xFF8C8C95);

  static const double radiusLg = 22;
  static const double radiusMd = 12;
  static const double spacing = 14;

  // ── Validasi ───────────────────────────────────────────────────────────────

  String? _validateName(String? v) {
    if (v == null || v.trim().isEmpty) return 'Nama lengkap wajib diisi';
    if (!RegExp(r"^[a-zA-Z\s']+$").hasMatch(v.trim())) {
      return 'Nama hanya boleh berisi huruf';
    }
    return null;
  }

  String? _validateEmail(String? v) {
    if (v == null || v.trim().isEmpty) return 'Email wajib diisi';
    if (!RegExp(r'^[\w.-]+@[\w.-]+\.\w{2,}$').hasMatch(v.trim())) {
      return 'Format email tidak valid';
    }
    return null;
  }

  String? _validateWa(String? v) {
    if (v == null || v.trim().isEmpty) return 'Nomor WhatsApp wajib diisi';
    if (!RegExp(r'^\d+$').hasMatch(v.trim())) return 'Hanya boleh berisi angka';
    if (v.trim().length < 8) return 'Nomor terlalu pendek';
    return null;
  }

  String? _validatePassword(String? v) {
    if (v == null || v.isEmpty) return 'Kata sandi wajib diisi';
    if (v.length < 8) return 'Minimal 8 karakter';
    if (!RegExp(r'[A-Z]').hasMatch(v)) return 'Harus ada minimal 1 huruf kapital';
    if (!RegExp(r'[0-9]').hasMatch(v)) return 'Harus ada minimal 1 angka';
    return null;
  }

  String? _validateConfirm(String? v) {
    if (v != _passCtrl.text) return 'Kata sandi tidak cocok';
    return null;
  }

  // ── Register handler ───────────────────────────────────────────────────────

  void _handleRegister() async {
    if (_formKey.currentState!.validate()) {
      showDialog(
        context: context,
        barrierDismissible: false,
        builder: (_) => const Center(
          child: CircularProgressIndicator(color: mainRed),
        ),
      );

      try {
        final fullWa = '+62${_waCtrl.text.trim()}';
        var response = await AuthService.register({
          'name': _nameCtrl.text.trim(),
          'email': _emailCtrl.text.trim(),
          'nomor_wa': fullWa,
          'password': _passCtrl.text,
          'password_confirmation': _confirmPassCtrl.text,
        });

        if (!mounted) return;
        Navigator.pop(context);

        if (response.statusCode == 201) {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (_) => OtpPage(name: _nameCtrl.text),
            ),
          );
        } else {
          final err = jsonDecode(response.body);
          _showSnackBar(err['message'] ?? 'Registrasi gagal', Colors.red);
        }
      } catch (e) {
        if (mounted) Navigator.pop(context);
        _showSnackBar('Kesalahan koneksi. Coba lagi.', Colors.black);
      }
    }
  }

  void _showSnackBar(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(backgroundColor: color, content: Text(msg)),
    );
  }

  // ── Build ──────────────────────────────────────────────────────────────────

  @override
  Widget build(BuildContext context) {
    final theme = Provider.of<ThemeController>(context);
    final isDark = theme.isDark;
    final topPad = MediaQuery.of(context).padding.top;

    return Scaffold(
      backgroundColor:
          isDark ? const Color(0xFF0D0D0D) : const Color(0xFFF0F2F5),
      body: Column(
        children: [
          // ── Compact gradient header ──────────────────────────────────────
          Container(
            width: double.infinity,
            // Fixed compact height: status bar + 100 px content
            padding: EdgeInsets.fromLTRB(18, topPad + 10, 18, 32),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: isDark
                    ? const [Color(0xFF7B0000), Color(0xFF1A1A2E)]
                    : const [Color(0xFFC62828), Color(0xFF26A69A)],
              ),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                // Top row: back + theme toggle
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    _circleBtn(
                      Icons.arrow_back_rounded,
                      () => Navigator.pop(context),
                    ),
                    _circleBtn(
                      isDark
                          ? Icons.dark_mode_rounded
                          : Icons.light_mode_rounded,
                      () => Provider.of<ThemeController>(context, listen: false)
                          .toggleTheme(),
                      size: 18,
                    ),
                  ],
                ),
                const SizedBox(height: 14),
                const Text(
                  'Daftar Akun',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 24,
                    fontWeight: FontWeight.w800,
                    letterSpacing: -0.3,
                  ),
                ),
                const SizedBox(height: 3),
                const Text(
                  'Mulai perjalanan akademikmu hari ini',
                  style: TextStyle(
                    color: Colors.white70,
                    fontSize: 12.5,
                  ),
                ),
              ],
            ),
          ),

          // ── Scrollable form card ─────────────────────────────────────────
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.fromLTRB(16, 0, 16, 24),
              child: Transform.translate(
                offset: const Offset(0, -22),
                child: Container(
                  width: double.infinity,
                  padding: const EdgeInsets.fromLTRB(18, 22, 18, 22),
                  decoration: BoxDecoration(
                    color: isDark ? const Color(0xFF1A1A1A) : Colors.white,
                    borderRadius: BorderRadius.circular(radiusLg),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black
                            .withOpacity(isDark ? 0.30 : 0.08),
                        blurRadius: 20,
                        offset: const Offset(0, 6),
                      ),
                    ],
                  ),
                  child: Form(
                    key: _formKey,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _fieldLabel(isDark, 'Nama Lengkap'),
                        const SizedBox(height: 6),
                        _buildInput(
                          isDark, _nameCtrl, 'Masukkan nama lengkap',
                          Icons.person_outline_rounded, _validateName,
                          inputType: TextInputType.name,
                        ),
                        const SizedBox(height: spacing),

                        _fieldLabel(isDark, 'Email Aktif'),
                        const SizedBox(height: 6),
                        _buildInput(
                          isDark, _emailCtrl, 'contoh@email.com',
                          Icons.email_outlined, _validateEmail,
                          inputType: TextInputType.emailAddress,
                        ),
                        const SizedBox(height: spacing),

                        _fieldLabel(isDark, 'Nomor WhatsApp'),
                        const SizedBox(height: 6),
                        _buildWaInput(isDark),
                        const SizedBox(height: spacing),

                        _fieldLabel(isDark, 'Kata Sandi'),
                        const SizedBox(height: 6),
                        _buildPasswordInput(
                          isDark, _passCtrl, 'Minimal 8 karakter',
                          Icons.lock_outline_rounded,
                          _obscurePass,
                          () => setState(() => _obscurePass = !_obscurePass),
                          _validatePassword,
                        ),
                        const SizedBox(height: spacing),

                        _fieldLabel(isDark, 'Konfirmasi Kata Sandi'),
                        const SizedBox(height: 6),
                        _buildPasswordInput(
                          isDark, _confirmPassCtrl, 'Ulangi kata sandi',
                          Icons.lock_reset_rounded,
                          _obscureConfirm,
                          () => setState(
                              () => _obscureConfirm = !_obscureConfirm),
                          _validateConfirm,
                        ),

                        const SizedBox(height: 24),

                        _buildRegisterButton(),

                        const SizedBox(height: 16),

                        Center(
                          child: GestureDetector(
                            onTap: () => Navigator.pop(context),
                            child: RichText(
                              text: TextSpan(
                                text: 'Sudah punya akun? ',
                                style: TextStyle(
                                  color: isDark
                                      ? Colors.white.withOpacity(0.55)
                                      : textMuted,
                                  fontSize: 12.5,
                                ),
                                children: [
                                  TextSpan(
                                    text: 'Masuk di sini',
                                    style: TextStyle(
                                      color:
                                          isDark ? Colors.white : mainRed,
                                      fontWeight: FontWeight.w800,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  // ── Reusable widgets ───────────────────────────────────────────────────────

  /// Small circle icon button for header
  Widget _circleBtn(IconData icon, VoidCallback onTap, {double size = 20}) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 36,
        height: 36,
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.20),
          shape: BoxShape.circle,
        ),
        child: Icon(icon, color: Colors.white, size: size),
      ),
    );
  }

  Widget _fieldLabel(bool isDark, String text) {
    return Text(
      text,
      style: TextStyle(
        color: isDark ? Colors.white : textDark,
        fontSize: 12.5,
        fontWeight: FontWeight.w700,
      ),
    );
  }

  InputDecoration _baseDecoration(
    bool isDark,
    String hint,
    IconData icon, {
    Widget? prefix,
    Widget? suffixIcon,
  }) {
    final fill = isDark
        ? Colors.white.withOpacity(0.06)
        : const Color(0xFFF4F6FB);
    final border = isDark
        ? Colors.white.withOpacity(0.12)
        : const Color(0xFFDDE3EF);

    return InputDecoration(
      filled: true,
      fillColor: fill,
      hintText: hint,
      hintStyle: TextStyle(
        color: isDark ? Colors.white30 : const Color(0xFFB0B8C8),
        fontSize: 13,
      ),
      prefixIcon: prefix == null
          ? Icon(icon,
              color: isDark
                  ? Colors.white38
                  : const Color(0xFF9CA3AF),
              size: 18)
          : null,
      prefix: prefix,
      suffixIcon: suffixIcon,
      isDense: true,
      errorStyle: const TextStyle(
        color: mainRed,
        fontSize: 10.5,
        fontWeight: FontWeight.w500,
      ),
      contentPadding:
          const EdgeInsets.symmetric(vertical: 13, horizontal: 14),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(radiusMd),
        borderSide: BorderSide(color: border),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(radiusMd),
        borderSide: const BorderSide(color: mainRed, width: 1.5),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(radiusMd),
        borderSide: const BorderSide(color: mainRed),
      ),
      focusedErrorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(radiusMd),
        borderSide: const BorderSide(color: mainRed),
      ),
    );
  }

  Widget _buildInput(
    bool isDark,
    TextEditingController ctrl,
    String hint,
    IconData icon,
    String? Function(String?)? validator, {
    TextInputType inputType = TextInputType.text,
  }) {
    return TextFormField(
      controller: ctrl,
      validator: validator,
      keyboardType: inputType,
      style: TextStyle(
        color: isDark ? Colors.white : textDark,
        fontSize: 13,
        fontWeight: FontWeight.w500,
      ),
      decoration: _baseDecoration(isDark, hint, icon),
    );
  }

  Widget _buildWaInput(bool isDark) {
    return TextFormField(
      controller: _waCtrl,
      validator: _validateWa,
      keyboardType: TextInputType.phone,
      inputFormatters: [FilteringTextInputFormatter.digitsOnly],
      style: TextStyle(
        color: isDark ? Colors.white : textDark,
        fontSize: 13,
        fontWeight: FontWeight.w500,
      ),
      decoration: _baseDecoration(
        isDark,
        '812xxxx',
        Icons.phone_android_rounded,
        prefix: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(Icons.phone_android_rounded,
                color: isDark
                    ? Colors.white38
                    : const Color(0xFF9CA3AF),
                size: 18),
            const SizedBox(width: 6),
            Text(
              '+62',
              style: TextStyle(
                color: isDark ? Colors.white70 : textDark,
                fontSize: 13,
                fontWeight: FontWeight.w600,
              ),
            ),
            const SizedBox(width: 6),
            Container(
              width: 1,
              height: 16,
              color: isDark
                  ? Colors.white24
                  : const Color(0xFFDDE3EF),
            ),
            const SizedBox(width: 6),
          ],
        ),
      ),
    );
  }

  Widget _buildPasswordInput(
    bool isDark,
    TextEditingController ctrl,
    String hint,
    IconData icon,
    bool obscure,
    VoidCallback onToggle,
    String? Function(String?)? validator,
  ) {
    return TextFormField(
      controller: ctrl,
      obscureText: obscure,
      validator: validator,
      style: TextStyle(
        color: isDark ? Colors.white : textDark,
        fontSize: 13,
        fontWeight: FontWeight.w500,
      ),
      decoration: _baseDecoration(isDark, hint, icon).copyWith(
        suffixIcon: IconButton(
          icon: Icon(
            obscure
                ? Icons.visibility_off_outlined
                : Icons.visibility_outlined,
            color: isDark ? Colors.white38 : const Color(0xFF9CA3AF),
            size: 18,
          ),
          onPressed: onToggle,
        ),
      ),
    );
  }

  Widget _buildRegisterButton() {
    return Container(
      width: double.infinity,
      height: 48,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(radiusMd),
        gradient: const LinearGradient(
          colors: [Color(0xFF26A69A), Color(0xFF00796B)],
          begin: Alignment.centerLeft,
          end: Alignment.centerRight,
        ),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF26A69A).withOpacity(0.35),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: ElevatedButton(
        onPressed: _handleRegister,
        style: ElevatedButton.styleFrom(
          backgroundColor: Colors.transparent,
          shadowColor: Colors.transparent,
          foregroundColor: Colors.white,
          elevation: 0,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(radiusMd),
          ),
        ),
        child: const Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(
              'Daftar Sekarang',
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w700,
                letterSpacing: 0.2,
              ),
            ),
            SizedBox(width: 6),
            Icon(Icons.arrow_forward_rounded, size: 16),
          ],
        ),
      ),
    );
  }

  @override
  void dispose() {
    _nameCtrl.dispose();
    _emailCtrl.dispose();
    _waCtrl.dispose();
    _passCtrl.dispose();
    _confirmPassCtrl.dispose();
    super.dispose();
  }
}