import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/auth_service.dart';

class EditProfilePage extends StatefulWidget {
  final Map userData;
  final String token;

  const EditProfilePage({
    super.key,
    required this.userData,
    required this.token,
  });

  @override
  State<EditProfilePage> createState() => _EditProfilePageState();
}

class _EditProfilePageState extends State<EditProfilePage> {
  final _parentCtrl = TextEditingController();
  final _addressCtrl = TextEditingController();
  final _parentPhoneCtrl = TextEditingController();
  final _nisnCtrl = TextEditingController();
  final _dobCtrl = TextEditingController();

  final Color redPrimary = const Color(0xFF9C0412);
  final Color redDark = const Color(0xFF740107);
  final Color redDeep = const Color(0xFF520102);
  final Color redWine = const Color(0xFF3D0606);
  final Color softRed = const Color(0xFFFFE8EA);
  final Color bgColor = const Color(0xFFFAF7F8);

  @override
  void initState() {
    super.initState();

    if (widget.userData['student'] != null) {
      final s = widget.userData['student'];

      _parentCtrl.text = s['parent_name'] ?? "";
      _addressCtrl.text = s['address'] ?? "";
      _parentPhoneCtrl.text = s['parent_phone'] ?? "";
      _nisnCtrl.text = s['national_id_number'] ?? "";
      _dobCtrl.text = s['date_of_birth'] ?? "";
    }
  }

  @override
  void dispose() {
    _parentCtrl.dispose();
    _addressCtrl.dispose();
    _parentPhoneCtrl.dispose();
    _nisnCtrl.dispose();
    _dobCtrl.dispose();
    super.dispose();
  }

  Future<void> _selectDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: DateTime(2005),
      firstDate: DateTime(1990),
      lastDate: DateTime.now(),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: ColorScheme.light(
              primary: redPrimary,
              onPrimary: Colors.white,
              onSurface: redWine,
            ),
          ),
          child: child!,
        );
      },
    );

    if (picked != null) {
      setState(() {
        _dobCtrl.text = DateFormat('yyyy-MM-dd').format(picked);
      });
    }
  }

  Future<void> _handleSave() async {
    if (_parentCtrl.text.isEmpty ||
        _addressCtrl.text.isEmpty ||
        _nisnCtrl.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Please fill all required fields!"),
        ),
      );
      return;
    }

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) => Center(
        child: CircularProgressIndicator(color: redPrimary),
      ),
    );

    final resp = await AuthService.updateProfile({
      'parent_name': _parentCtrl.text,
      'alamat': _addressCtrl.text,
      'wa_ortu': _parentPhoneCtrl.text,
      'nisn': _nisnCtrl.text,
      'dob': _dobCtrl.text,
    }, widget.token);

    if (!mounted) return;
    Navigator.pop(context);

    if (resp.statusCode == 200) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          backgroundColor: Colors.green,
          content: Text("Profile data successfully updated"),
        ),
      );
      Navigator.pop(context, true);
    } else {
      final err = jsonDecode(resp.body);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          backgroundColor: Colors.red,
          content: Text(err['message'] ?? "Failed to update profile"),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: bgColor,
      body: SafeArea(
        child: Column(
          children: [
            _buildHeader(),
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.fromLTRB(20, 18, 20, 28),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildUserInfoCard(),
                    const SizedBox(height: 26),
                    _sectionTitle("Student Information"),
                    const SizedBox(height: 14),
                    _buildFormCard(),
                    const SizedBox(height: 30),
                    _buildSaveButton(),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Container(
      padding: const EdgeInsets.fromLTRB(18, 14, 18, 22),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [redPrimary, redDark, redDeep],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: const BorderRadius.vertical(
          bottom: Radius.circular(28),
        ),
        boxShadow: [
          BoxShadow(
            color: redDeep.withOpacity(0.22),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Stack(
        children: [
          Positioned(
            right: -45,
            bottom: -65,
            child: Container(
              width: 150,
              height: 150,
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.07),
                shape: BoxShape.circle,
              ),
            ),
          ),
          Positioned(
            left: 42,
            top: 8,
            child: SizedBox(
              width: 80,
              child: Wrap(
                spacing: 6,
                runSpacing: 6,
                children: List.generate(
                  35,
                  (_) => Container(
                    width: 3,
                    height: 3,
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.18),
                      shape: BoxShape.circle,
                    ),
                  ),
                ),
              ),
            ),
          ),
          Row(
            children: [
              InkWell(
                borderRadius: BorderRadius.circular(16),
                onTap: () => Navigator.pop(context),
                child: Container(
                  width: 42,
                  height: 42,
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.12),
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: const Icon(
                    Icons.arrow_back_rounded,
                    color: Colors.white,
                  ),
                ),
              ),
              const SizedBox(width: 14),
              const Expanded(
                child: Text(
                  "Complete Profile Data",
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 19,
                    fontWeight: FontWeight.w900,
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildUserInfoCard() {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: _cardDecoration(),
      child: Column(
        children: [
          _infoTile(
            icon: Icons.email_outlined,
            title: "Gmail",
            value: widget.userData['email'] ?? "-",
          ),
          _line(),
          _infoTile(
            icon: Icons.phone_android_rounded,
            title: "WhatsApp Number",
            value: widget.userData['phone'] ?? "-",
          ),
        ],
      ),
    );
  }

  Widget _infoTile({
    required IconData icon,
    required String title,
    required String value,
  }) {
    return Row(
      children: [
        Container(
          width: 46,
          height: 46,
          decoration: BoxDecoration(
            color: softRed,
            borderRadius: BorderRadius.circular(15),
          ),
          child: Icon(icon, color: redPrimary, size: 22),
        ),
        const SizedBox(width: 14),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: TextStyle(
                  color: redWine,
                  fontSize: 14,
                  fontWeight: FontWeight.w900,
                ),
              ),
              const SizedBox(height: 3),
              Text(
                value,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(
                  color: Colors.grey,
                  fontSize: 12.5,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildFormCard() {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: _cardDecoration(),
      child: Column(
        children: [
          _buildInput(
            controller: _nisnCtrl,
            label: "Student ID (NISN)",
            icon: Icons.numbers_rounded,
          ),
          const SizedBox(height: 14),
          _buildInput(
            controller: _parentCtrl,
            label: "Parent Name",
            icon: Icons.person_outline_rounded,
          ),
          const SizedBox(height: 14),
          _buildInput(
            controller: _addressCtrl,
            label: "Full Address",
            icon: Icons.location_on_outlined,
            maxLines: 2,
          ),
          const SizedBox(height: 14),
          _buildInput(
            controller: _parentPhoneCtrl,
            label: "Parent WhatsApp Number",
            icon: Icons.phone_rounded,
            keyboardType: TextInputType.phone,
          ),
          const SizedBox(height: 14),
          _buildInput(
            controller: _dobCtrl,
            label: "Date of Birth",
            icon: Icons.calendar_month_rounded,
            readOnly: true,
            onTap: _selectDate,
          ),
        ],
      ),
    );
  }

  Widget _buildInput({
    required TextEditingController controller,
    required String label,
    required IconData icon,
    int maxLines = 1,
    bool readOnly = false,
    VoidCallback? onTap,
    TextInputType keyboardType = TextInputType.text,
  }) {
    return TextField(
      controller: controller,
      readOnly: readOnly,
      onTap: onTap,
      maxLines: maxLines,
      keyboardType: keyboardType,
      style: TextStyle(
        color: redWine,
        fontWeight: FontWeight.w700,
        fontSize: 14,
      ),
      decoration: InputDecoration(
        filled: true,
        fillColor: const Color(0xFFFCF7F8),
        labelText: label,
        labelStyle: TextStyle(
          color: redWine.withOpacity(0.55),
          fontSize: 13,
          fontWeight: FontWeight.w600,
        ),
        prefixIcon: Icon(icon, color: redPrimary, size: 22),
        contentPadding: const EdgeInsets.symmetric(
          horizontal: 16,
          vertical: 17,
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(18),
          borderSide: BorderSide(
            color: redPrimary.withOpacity(0.08),
          ),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(18),
          borderSide: BorderSide(
            color: redPrimary,
            width: 1.4,
          ),
        ),
      ),
    );
  }

  Widget _buildSaveButton() {
    return SizedBox(
      width: double.infinity,
      height: 58,
      child: ElevatedButton(
        onPressed: _handleSave,
        style: ElevatedButton.styleFrom(
          elevation: 0,
          backgroundColor: redPrimary,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(20),
          ),
          shadowColor: redDeep.withOpacity(0.25),
        ),
        child: const Text(
          "SAVE DATA",
          style: TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.w900,
            letterSpacing: 0.5,
          ),
        ),
      ),
    );
  }

  Widget _sectionTitle(String title) {
    return Text(
      title,
      style: TextStyle(
        color: redWine,
        fontSize: 18,
        fontWeight: FontWeight.w900,
      ),
    );
  }

  Widget _line() {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 15),
      child: Divider(
        height: 1,
        color: Colors.grey.withOpacity(0.16),
      ),
    );
  }

  BoxDecoration _cardDecoration() {
    return BoxDecoration(
      color: Colors.white,
      borderRadius: BorderRadius.circular(26),
      boxShadow: [
        BoxShadow(
          color: redDeep.withOpacity(0.06),
          blurRadius: 18,
          offset: const Offset(0, 9),
        ),
      ],
    );
  }
}