import 'dart:convert';

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

class _LoginPageState extends State<LoginPage> {
  final TextEditingController nameCtrl = TextEditingController();
  final TextEditingController passCtrl = TextEditingController();

  bool _isObscure = true;

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

  void handleLogin() async {
    if (nameCtrl.text.isEmpty || passCtrl.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Name and Password are required!")),
      );
      return;
    }

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => const Center(
        child: CircularProgressIndicator(color: mainRed),
      ),
    );

    try {
      var resp = await AuthService.login(nameCtrl.text, passCtrl.text);

      if (!mounted) return;
      Navigator.pop(context);

      if (resp.statusCode == 200) {
        final data = jsonDecode(resp.body);

        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(
            builder: (_) => MainScreen(
              userName: data['user']['name'],
              token: data['token'],
              userProfileData: data['user'],
            ),
          ),
          (route) => false,
        );
      } else {
        final errorData = jsonDecode(resp.body);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: Colors.red,
            content: Text(
              errorData['message'] ?? "Invalid Name or Password!",
            ),
          ),
        );
      }
    } catch (e) {
      if (!mounted) return;
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          backgroundColor: Colors.black,
          content: Text("Connection Error: Check your server."),
        ),
      );
    }
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
            child: Column(
              children: [
                _buildTopToggle(isDark),

                const SizedBox(height: 34),

                _buildLogo(isDark),

                const SizedBox(height: 24),

                Text(
                  "SPEKTA ACADEMY",
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 28,
                    fontWeight: FontWeight.w800,
                    color: primaryText(isDark),
                    letterSpacing: 1.2,
                  ),
                ),

                const SizedBox(height: 8),

                Text(
                  "Achieve Your Dream of Serving the Nation",
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: secondaryText(isDark),
                    fontSize: 13,
                    fontWeight: FontWeight.w500,
                  ),
                ),

                const SizedBox(height: 42),

                _buildLoginCard(isDark),

                const SizedBox(height: 24),

                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text(
                      "Don't have an account? ",
                      style: TextStyle(
                        color: secondaryText(isDark),
                        fontSize: 13,
                      ),
                    ),
                    GestureDetector(
                      onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => const RegisterPage(),
                        ),
                      ),
                      child: Text(
                        "Register",
                        style: TextStyle(
                          color: isDark ? Colors.white : deepRed,
                          fontWeight: FontWeight.w800,
                          fontSize: 13,
                        ),
                      ),
                    ),
                  ],
                ),

                const SizedBox(height: 24),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildTopToggle(bool isDark) {
    return Align(
      alignment: Alignment.centerRight,
      child: GestureDetector(
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
    );
  }

  Widget _buildLogo(bool isDark) {
    return Container(
      padding: const EdgeInsets.all(22),
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
        Icons.school_rounded,
        size: 66,
        color: isDark ? Colors.white : deepRed,
      ),
    );
  }

  Widget _buildLoginCard(bool isDark) {
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
            isDark: isDark,
            controller: nameCtrl,
            label: "Full Name",
            icon: Icons.person_outline_rounded,
          ),
          const SizedBox(height: spacing),
          _buildInput(
            isDark: isDark,
            controller: passCtrl,
            label: "Password",
            icon: Icons.lock_outline_rounded,
            isPassword: true,
          ),
          Align(
            alignment: Alignment.centerRight,
            child: TextButton(
              onPressed: () => Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (_) => const ForgotPasswordPage(),
                ),
              ),
              child: Text(
                "Forgot Password?",
                style: TextStyle(
                  color: isDark ? Colors.white : deepRed,
                  fontWeight: FontWeight.w700,
                  fontSize: 13,
                ),
              ),
            ),
          ),
          const SizedBox(height: 16),
          _buildLoginButton(),
        ],
      ),
    );
  }

  Widget _buildLoginButton() {
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
        onPressed: handleLogin,
        style: ElevatedButton.styleFrom(
          backgroundColor: Colors.transparent,
          shadowColor: Colors.transparent,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(radiusMd),
          ),
        ),
        child: const Text(
          "LOGIN",
          style: TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.w800,
            fontSize: 15,
            letterSpacing: 1.2,
          ),
        ),
      ),
    );
  }

  Widget _buildInput({
    required bool isDark,
    required TextEditingController controller,
    required String label,
    required IconData icon,
    bool isPassword = false,
  }) {
    return TextField(
      controller: controller,
      obscureText: isPassword ? _isObscure : false,
      style: TextStyle(
        color: fieldText(isDark),
        fontSize: 14,
        fontWeight: FontWeight.w500,
      ),
      decoration: InputDecoration(
        filled: true,
        fillColor: inputColor(isDark),
        labelText: label,
        labelStyle: TextStyle(
          color: labelText(isDark),
          fontSize: 13,
          fontWeight: FontWeight.w500,
        ),
        prefixIcon: Icon(icon, color: iconColor(isDark)),
        suffixIcon: isPassword
            ? IconButton(
                icon: Icon(
                  _isObscure ? Icons.visibility_off : Icons.visibility,
                  color: isDark ? Colors.white54 : textMuted,
                ),
                onPressed: () => setState(() => _isObscure = !_isObscure),
              )
            : null,
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
        contentPadding: const EdgeInsets.symmetric(
          vertical: 18,
          horizontal: 16,
        ),
      ),
    );
  }
}