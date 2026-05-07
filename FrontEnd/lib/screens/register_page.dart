import 'dart:convert';

import 'package:flutter/material.dart';
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

  static const Color mainRed = Color(0xFFF24455);
  static const Color deepRed = Color(0xFFE5203A);
  static const Color textDark = Color(0xFF1F2028);
  static const Color textMuted = Color(0xFF8C8C95);

  static const double radiusLg = 28;
  static const double radiusMd = 18;
  static const double spacing = 16;

  List<Color> bgGradient(bool isDark) => isDark
      ? const [
          Color(0xFF02060E),
          Color(0xFF15101A),
          Color(0xFF660F24),
        ]
      : const [
          Color(0xFFFFDBE8),
          Color(0xFFFF94B2),
          Color(0xFFF24455),
        ];

  Color primaryText(bool isDark) => isDark ? Colors.white : textDark;

  Color secondaryText(bool isDark) =>
      isDark ? Colors.white.withOpacity(0.62) : textDark.withOpacity(0.58);

  Color cardColor(bool isDark) =>
      isDark ? Colors.white.withOpacity(0.08) : Colors.white;

  Color inputColor(bool isDark) =>
      isDark ? Colors.white.withOpacity(0.07) : const Color(0xFFFFF7FA);

  Color borderColor(bool isDark) =>
      isDark ? Colors.white.withOpacity(0.12) : Colors.white.withOpacity(0.75);

  Color iconColor(bool isDark) => isDark ? Colors.white70 : deepRed;

  Color fieldText(bool isDark) => isDark ? Colors.white : textDark;

  Color labelText(bool isDark) =>
      isDark ? Colors.white.withOpacity(0.55) : textMuted;

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
        var response = await AuthService.register({
          'name': _nameCtrl.text.trim(),
          'email': _emailCtrl.text.trim(),
          'nomor_wa': _waCtrl.text.trim(),
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
          _showSnackBar(err['message'] ?? "Registration failed", Colors.red);
        }
      } catch (e) {
        if (mounted) Navigator.pop(context);
        _showSnackBar("Connection error. Please try again.", Colors.black);
      }
    }
  }

  void _showSnackBar(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(backgroundColor: color, content: Text(msg)),
    );
  }

  @override
  Widget build(BuildContext context) {
    final theme = Provider.of<ThemeController>(context);
    final isDark = theme.isDark;

    return Scaffold(
      body: AnimatedContainer(
        duration: const Duration(milliseconds: 300),
        width: double.infinity,
        height: MediaQuery.of(context).size.height,
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: bgGradient(isDark),
          ),
        ),
        child: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.fromLTRB(20, 6, 20, 0),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildHeader(isDark),

                  const SizedBox(height: 28),

                  Center(
                    child: Container(
                      padding: const EdgeInsets.all(18),
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: isDark
                            ? Colors.white.withOpacity(0.08)
                            : Colors.white.withOpacity(0.75),
                        border: Border.all(color: borderColor(isDark)),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(isDark ? 0.18 : 0.08),
                            blurRadius: 22,
                            offset: const Offset(0, 10),
                          ),
                        ],
                      ),
                      child: Icon(
                        Icons.person_add_alt_1_rounded,
                        size: 56,
                        color: isDark ? Colors.white : deepRed,
                      ),
                    ),
                  ),

                  const SizedBox(height: 22),

                  Center(
                    child: Text(
                      "Register Spekta Academy",
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontSize: 25,
                        fontWeight: FontWeight.w800,
                        color: primaryText(isDark),
                      ),
                    ),
                  ),

                  const SizedBox(height: 8),

                  Center(
                    child: Text(
                      "Create your account to start learning.",
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        color: secondaryText(isDark),
                        fontSize: 13,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ),

                  const SizedBox(height: 30),

                  _buildFormCard(isDark),

                  const SizedBox(height: 22),

                  Center(
                    child: GestureDetector(
                      onTap: () => Navigator.pop(context),
                      child: RichText(
                        text: TextSpan(
                          text: "Already have an account? ",
                          style: TextStyle(
                            color: secondaryText(isDark),
                            fontSize: 13,
                          ),
                          children: [
                            TextSpan(
                              text: "Login",
                              style: TextStyle(
                                color: isDark ? Colors.white : deepRed,
                                fontWeight: FontWeight.w800,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),

                  const SizedBox(height: 24),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildHeader(bool isDark) {
    return Row(
      children: [
        IconButton(
          onPressed: () => Navigator.pop(context),
          icon: Icon(
            Icons.arrow_back_rounded,
            color: primaryText(isDark),
          ),
          padding: EdgeInsets.zero,
          constraints: const BoxConstraints(),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: Text(
            "Create Account",
            style: TextStyle(
              color: primaryText(isDark),
              fontSize: 20,
              fontWeight: FontWeight.w700,
            ),
          ),
        ),
        GestureDetector(
          onTap: () {
            Provider.of<ThemeController>(context, listen: false).toggleTheme();
          },
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 250),
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            decoration: BoxDecoration(
              color: isDark
                  ? Colors.white.withOpacity(0.12)
                  : Colors.white.withOpacity(0.75),
              borderRadius: BorderRadius.circular(30),
              border: Border.all(color: borderColor(isDark)),
            ),
            child: Icon(
              isDark ? Icons.dark_mode_rounded : Icons.light_mode_rounded,
              color: isDark ? Colors.white : deepRed,
              size: 20,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildFormCard(bool isDark) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(22),
      decoration: BoxDecoration(
        color: cardColor(isDark),
        borderRadius: BorderRadius.circular(radiusLg),
        border: Border.all(color: borderColor(isDark)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(isDark ? 0.20 : 0.08),
            blurRadius: 26,
            offset: const Offset(0, 14),
          ),
        ],
      ),
      child: Column(
        children: [
          _buildInput(
            isDark,
            _nameCtrl,
            "Full Name",
            Icons.person_outline_rounded,
            (v) => v!.isEmpty ? "Full name is required" : null,
          ),
          const SizedBox(height: spacing),

          _buildInput(
            isDark,
            _emailCtrl,
            "Active Email",
            Icons.email_outlined,
            (v) => v!.contains('@') ? null : "Invalid email address",
          ),
          const SizedBox(height: spacing),

          _buildInput(
            isDark,
            _waCtrl,
            "WhatsApp Number",
            Icons.phone_android_rounded,
            (v) => v!.length < 10 ? "Invalid phone number" : null,
          ),
          const SizedBox(height: spacing),

          _buildPasswordInput(
            isDark,
            _passCtrl,
            "Password",
            _obscurePass,
            () => setState(() => _obscurePass = !_obscurePass),
            (v) => v!.length < 8 ? "Minimum 8 characters" : null,
          ),
          const SizedBox(height: spacing),

          _buildPasswordInput(
            isDark,
            _confirmPassCtrl,
            "Confirm Password",
            _obscureConfirm,
            () => setState(() => _obscureConfirm = !_obscureConfirm),
            (v) => v != _passCtrl.text ? "Passwords do not match" : null,
          ),

          const SizedBox(height: 28),

          _buildRegisterButton(),
        ],
      ),
    );
  }

  Widget _buildRegisterButton() {
    return Container(
      width: double.infinity,
      height: 55,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(radiusMd),
        gradient: const LinearGradient(
          colors: [
            Color(0xFFF24455),
            Color(0xFFE5203A),
          ],
        ),
        boxShadow: [
          BoxShadow(
            color: deepRed.withOpacity(0.35),
            blurRadius: 16,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: ElevatedButton(
        onPressed: _handleRegister,
        style: ElevatedButton.styleFrom(
          backgroundColor: Colors.transparent,
          shadowColor: Colors.transparent,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(radiusMd),
          ),
        ),
        child: const Text(
          "REGISTER NOW",
          style: TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.w800,
            fontSize: 15,
            letterSpacing: 1.1,
          ),
        ),
      ),
    );
  }

  Widget _buildInput(
    bool isDark,
    TextEditingController ctrl,
    String label,
    IconData icon,
    String? Function(String?)? validator,
  ) {
    return TextFormField(
      controller: ctrl,
      validator: validator,
      style: TextStyle(
        color: fieldText(isDark),
        fontSize: 14,
        fontWeight: FontWeight.w500,
      ),
      decoration: _inputDecoration(isDark, label, icon),
    );
  }

  Widget _buildPasswordInput(
    bool isDark,
    TextEditingController ctrl,
    String label,
    bool obscure,
    VoidCallback onToggle,
    String? Function(String?)? validator,
  ) {
    return TextFormField(
      controller: ctrl,
      obscureText: obscure,
      validator: validator,
      style: TextStyle(
        color: fieldText(isDark),
        fontSize: 14,
        fontWeight: FontWeight.w500,
      ),
      decoration: _inputDecoration(isDark, label, Icons.lock_outline_rounded)
          .copyWith(
        suffixIcon: IconButton(
          icon: Icon(
            obscure ? Icons.visibility_off : Icons.visibility,
            color: isDark ? Colors.white54 : textMuted,
          ),
          onPressed: onToggle,
        ),
      ),
    );
  }

  InputDecoration _inputDecoration(bool isDark, String label, IconData icon) {
    return InputDecoration(
      filled: true,
      fillColor: inputColor(isDark),
      labelText: label,
      labelStyle: TextStyle(
        color: labelText(isDark),
        fontSize: 13,
        fontWeight: FontWeight.w500,
      ),
      prefixIcon: Icon(icon, color: iconColor(isDark)),
      errorStyle: TextStyle(
        color: isDark ? const Color(0xFFFFCCD5) : deepRed,
        fontSize: 11,
        fontWeight: FontWeight.w500,
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(radiusMd),
        borderSide: BorderSide(
          color: isDark
              ? Colors.white.withOpacity(0.12)
              : const Color(0xFFFFD1DC),
        ),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(radiusMd),
        borderSide: const BorderSide(
          color: mainRed,
          width: 1.6,
        ),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(radiusMd),
        borderSide: const BorderSide(color: deepRed),
      ),
      focusedErrorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(radiusMd),
        borderSide: const BorderSide(color: deepRed),
      ),
      contentPadding: const EdgeInsets.symmetric(
        vertical: 18,
        horizontal: 16,
      ),
    );
  }
}