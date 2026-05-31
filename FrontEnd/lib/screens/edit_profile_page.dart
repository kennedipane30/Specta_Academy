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
  final Color bgColor = const Color(0xFFF8F9FA);

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
          backgroundColor: Colors.orange,
          content: Text("Harap isi semua bidang yang wajib!", style: TextStyle(fontWeight: FontWeight.bold)),
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
          content: Text("Data profil berhasil diperbarui", style: TextStyle(fontWeight: FontWeight.bold)),
        ),
      );
      Navigator.pop(context, true);
    } else {
      final err = jsonDecode(resp.body);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          backgroundColor: Colors.red,
          content: Text(err['message'] ?? "Gagal memperbarui profil"),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: bgColor,
      body: Column(
        children: [
          _buildHeader(),
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.fromLTRB(20, 20, 20, 40),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildUserInfoCard(),
                  const SizedBox(height: 30),
                  const Text(
                    "Informasi Detail Siswa",
                    style: TextStyle(
                      color: Color(0xFF1A1A1A),
                      fontSize: 18,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                  const SizedBox(height: 15),
                  _buildFormCard(),
                  const SizedBox(height: 35),
                  _buildSaveButton(),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildHeader() {
    return Container(
      padding: const EdgeInsets.fromLTRB(10, 50, 20, 25),
      decoration: BoxDecoration(
        color: redPrimary,
        borderRadius: const BorderRadius.vertical(
          bottom: Radius.circular(30),
        ),
        boxShadow: [
          BoxShadow(
            color: redDeep.withOpacity(0.15),
            blurRadius: 15,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Row(
        children: [
          // ✨ IKON PANAH SIMPEL SESUAI PERMINTAAN
          IconButton(
            icon: const Icon(
              Icons.arrow_back,
              color: Colors.white,
              size: 28,
            ),
            onPressed: () => Navigator.pop(context),
          ),
          const SizedBox(width: 8),
          const Expanded(
            child: Text(
              "Lengkapi Profil",
              style: TextStyle(
                color: Colors.white,
                fontSize: 20,
                fontWeight: FontWeight.w900,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildUserInfoCard() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: _cardDecoration(),
      child: Column(
        children: [
          _infoTile(
            icon: Icons.email_outlined,
            title: "Email Terdaftar",
            value: widget.userData['email'] ?? "-",
          ),
          Padding(
            padding: const EdgeInsets.symmetric(vertical: 15),
            child: Divider(color: Colors.grey.withOpacity(0.1)),
          ),
          _infoTile(
            icon: Icons.phone_android_rounded,
            title: "Nomor WhatsApp",
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
          width: 44,
          height: 44,
          decoration: BoxDecoration(
            color: softRed,
            borderRadius: BorderRadius.circular(12),
          ),
          child: Icon(icon, color: redPrimary, size: 20),
        ),
        const SizedBox(width: 15),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: const TextStyle(
                  color: Colors.grey,
                  fontSize: 11,
                  fontWeight: FontWeight.w700,
                ),
              ),
              const SizedBox(height: 2),
              Text(
                value,
                style: TextStyle(
                  color: redWine,
                  fontSize: 14,
                  fontWeight: FontWeight.w900,
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
      padding: const EdgeInsets.all(22),
      decoration: _cardDecoration(),
      child: Column(
        children: [
          _buildInput(
            controller: _nisnCtrl,
            label: "Nomor Induk Siswa (NISN)",
            icon: Icons.numbers_rounded,
          ),
          const SizedBox(height: 18),
          _buildInput(
            controller: _parentCtrl,
            label: "Nama Lengkap Orang Tua",
            icon: Icons.person_outline_rounded,
          ),
          const SizedBox(height: 18),
          _buildInput(
            controller: _addressCtrl,
            label: "Alamat Lengkap Rumah",
            icon: Icons.location_on_outlined,
            maxLines: 2,
          ),
          const SizedBox(height: 18),
          _buildInput(
            controller: _parentPhoneCtrl,
            label: "Nomor WA Orang Tua",
            icon: Icons.phone_rounded,
            keyboardType: TextInputType.phone,
          ),
          const SizedBox(height: 18),
          _buildInput(
            controller: _dobCtrl,
            label: "Tanggal Lahir",
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
      style: TextStyle(color: redWine, fontWeight: FontWeight.bold, fontSize: 14),
      decoration: InputDecoration(
        filled: true,
        fillColor: const Color(0xFFFBFBFB),
        labelText: label,
        labelStyle: TextStyle(color: Colors.grey.shade600, fontSize: 12, fontWeight: FontWeight.w600),
        prefixIcon: Icon(icon, color: redPrimary, size: 20),
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 18),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(15),
          borderSide: BorderSide(color: Colors.grey.shade200),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(15),
          borderSide: BorderSide(color: redPrimary, width: 1.5),
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
          elevation: 8,
          shadowColor: redPrimary.withOpacity(0.3),
          backgroundColor: redPrimary,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
        ),
        child: const Text(
          "SIMPAN PERUBAHAN",
          style: TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.w900,
            letterSpacing: 1,
          ),
        ),
      ),
    );
  }

  BoxDecoration _cardDecoration() {
    return BoxDecoration(
      color: Colors.white,
      borderRadius: BorderRadius.circular(25),
      boxShadow: [
        BoxShadow(
          color: Colors.black.withOpacity(0.03),
          blurRadius: 15,
          offset: const Offset(0, 8),
        ),
      ],
    );
  }
}