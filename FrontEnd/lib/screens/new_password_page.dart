import 'package:flutter/material.dart';
import '../services/auth_service.dart';

class NewPasswordPage extends StatefulWidget {
  final String phone;
  final String otp;
  const NewPasswordPage({super.key, required this.phone, required this.otp});

  @override State<NewPasswordPage> createState() => _NewPasswordPageState();
}

class _NewPasswordPageState extends State<NewPasswordPage> {
  final _passCtrl = TextEditingController();
  final _confCtrl = TextEditingController();
  final _formKey = GlobalKey<FormState>();
  bool _isObscure = true;

  String? _validatePassword(String? v) {
    if (v == null || v.length < 8) return 'Minimum 8 characters';
    if (!RegExp(r'^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])').hasMatch(v)) {
      return 'Must include Uppercase, Number, & Symbol!';
    }
    return null;
  }

  void _handleReset() async {
    if (_formKey.currentState!.validate()) {
      showDialog(context: context, builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000))));
      var resp = await AuthService.resetPassword({
        'phone': widget.phone,
        'otp': widget.otp,
        'password': _passCtrl.text,
        'password_confirmation': _confCtrl.text,
      });
      Navigator.pop(context);

      if (resp.statusCode == 200) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.green, content: Text("Password Updated! Please Login.")));
        Navigator.popUntil(context, (route) => route.isFirst);
      } else {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.red, content: Text("Failed to update password!")));
      }
    }
  }

  @override Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(title: const Text("New Password"), backgroundColor: Colors.white, foregroundColor: spektaRed, elevation: 0),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 35, vertical: 20),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text("Account Security", style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
              const Text("Create a strong and unique password.", style: TextStyle(color: Colors.grey)),
              const SizedBox(height: 40),
              _buildPasswordInput(_passCtrl, "New Password"),
              const SizedBox(height: 20),
              _buildPasswordInput(_confCtrl, "Confirm New Password", isConfirm: true),
              const SizedBox(height: 40),
              ElevatedButton(
                style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 55), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
                onPressed: _handleReset,
                child: const Text("SAVE NEW PASSWORD", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
              )
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildPasswordInput(TextEditingController ctrl, String label, {bool isConfirm = false}) {
    return TextFormField(
      controller: ctrl,
      obscureText: _isObscure,
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: const Icon(Icons.lock_outline, color: Color(0xFF990000)),
        suffixIcon: IconButton(
          icon: Icon(_isObscure ? Icons.visibility_off : Icons.visibility, color: Colors.grey),
          onPressed: () => setState(() => _isObscure = !_isObscure),
        ),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
      ),
      validator: isConfirm ? (v) => v != _passCtrl.text ? 'Passwords do not match!' : null : _validatePassword,
    );
  }
}