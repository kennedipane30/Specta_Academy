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
  static const Color softRed         = Color(0xFFFEE2E2);
  static const Color errorRed        = Color(0xFFBA1A1A);

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
            colorScheme: const ColorScheme.light(
              primary: accentTeal,
              onPrimary: Colors.white,
              onSurface: textDark,
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
        SnackBar(
          backgroundColor: accentTeal,
          content: const Text("Harap isi semua bidang yang wajib!", style: TextStyle(fontWeight: FontWeight.bold)),
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        ),
      );
      return;
    }

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) => const Center(
        child: CircularProgressIndicator(color: accentTeal),
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
        SnackBar(
          backgroundColor: darkTeal,
          content: const Text("Data profil berhasil diperbarui", style: TextStyle(fontWeight: FontWeight.bold)),
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        ),
      );
      Navigator.pop(context, true);
    } else {
      final err = jsonDecode(resp.body);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          backgroundColor: primaryRed,
          content: Text(err['message'] ?? "Gagal memperbarui profil"),
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        ),
      );
    }
  }

  String _safeText(dynamic value, {String fallback = '-'}) {
    if (value == null) return fallback;
    final text = value.toString().trim();
    if (text.isEmpty) return fallback;
    return text;
  }

  void _showSnack(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message, style: const TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: accentTeal,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: pageBg,
      body: Column(
        children: [
          _buildProfileHeader(), 
          Expanded(
            child: SingleChildScrollView(
              physics: const BouncingScrollPhysics(),
              padding: const EdgeInsets.fromLTRB(20, 20, 20, 40),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildUserInfoCard(),
                  const SizedBox(height: 24),
                  
                  // KELAS SAYA SECTION
                  _buildClassSection(),
                  const SizedBox(height: 28),
                  
                  // FORM DETAIL SISWA
                  _buildSectionTitle("Informasi Detail Siswa", "📋"),
                  const SizedBox(height: 14),
                  _buildFormCard(),
                  const SizedBox(height: 24),
                  
                  // SIGN OUT ASYMMETRIC CARD
                  _buildSignOutCard(),
                  const SizedBox(height: 28),
                  
                  _buildSaveButton(),
                ],
              ),
            ),
          ),
        ],
      ),
      bottomNavigationBar: _buildBottomNavigationBar(),
    );
  }

  // ========== 🟢 1. HEADER MERAH GRADIEN CURVED ==========
  Widget _buildProfileHeader() {
    final name = _safeText(widget.userData['full_name'] ?? widget.userData['name'], fallback: "Kennedi Pane");
    final email = _safeText(widget.userData['email'], fallback: "ken@gmail.com");

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.fromLTRB(16, 48, 16, 32),
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          colors: [primaryRed, accentTeal],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.vertical(
          bottom: Radius.circular(32),
        ),
      ),
      child: Stack(
        clipBehavior: Clip.none,
        children: [
          Positioned(
            right: -40,
            top: -40,
            child: Container(
              width: 140,
              height: 140,
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.06),
                shape: BoxShape.circle,
              ),
            ),
          ),
          Column(
            children: [
              // Top Action Row
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  CircleAvatar(
                    backgroundColor: Colors.white.withOpacity(0.15),
                    child: IconButton(
                      icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white, size: 16),
                      onPressed: () => Navigator.pop(context),
                    ),
                  ),
                  const Text(
                    "Profil Saya",
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 18,
                      fontWeight: FontWeight.w900,
                      letterSpacing: -0.5,
                    ),
                  ),
                  CircleAvatar(
                    backgroundColor: Colors.white.withOpacity(0.15),
                    child: IconButton(
                      icon: const Icon(Icons.settings_rounded, color: Colors.white, size: 18),
                      onPressed: () {
                        _showSnack("Pengaturan akun segera hadir!");
                      },
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 24),
              
              // Avatar dengan floating Camera Icon
              Stack(
                alignment: Alignment.center,
                children: [
                  Container(
                    width: 90,
                    height: 90,
                    padding: const EdgeInsets.all(2.5),
                    decoration: const BoxDecoration(
                      color: Colors.white,
                      shape: BoxShape.circle,
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black12,
                          blurRadius: 10,
                          offset: Offset(0, 4),
                        )
                      ],
                    ),
                    child: const ClipOval(
                      child: Icon(Icons.person, color: primaryRed, size: 52), 
                    ),
                  ),
                  Positioned(
                    bottom: 0,
                    right: 0,
                    child: Container(
                      width: 28,
                      height: 28,
                      decoration: BoxDecoration(
                        color: accentTeal,
                        shape: BoxShape.circle,
                        border: Border.all(color: Colors.white, width: 2.5),
                        boxShadow: const [
                          BoxShadow(
                            color: Colors.black12,
                            blurRadius: 4,
                            offset: Offset(0, 2),
                          )
                        ],
                      ),
                      child: const Icon(Icons.camera_alt_rounded, color: Colors.white, size: 12),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              Text(
                name,
                style: const TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.w900,
                  color: Colors.white,
                  letterSpacing: -0.5,
                ),
              ),
              const SizedBox(height: 2),
              Text(
                email,
                style: TextStyle(
                  fontSize: 12.5,
                  fontWeight: FontWeight.w600,
                  color: Colors.white.withOpacity(0.75),
                ),
              ),
              const SizedBox(height: 10),
              
              // Blur capsule badge "SISWA"
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 5),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.15),
                  borderRadius: BorderRadius.circular(30),
                  border: Border.all(color: Colors.white.withOpacity(0.2)),
                ),
                child: const Text(
                  'SISWA',
                  style: TextStyle(
                    fontSize: 10,
                    fontWeight: FontWeight.w900,
                    color: Colors.white,
                    letterSpacing: 1.2,
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  // ========== 🟢 2. SEKSI KELAS SAYA ==========
  Widget _buildClassSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Container(
              width: 4,
              height: 18,
              decoration: BoxDecoration(
                color: accentTeal,
                borderRadius: BorderRadius.circular(99),
              ),
            ),
            const SizedBox(width: 8),
            const Text(
              "Kelas Saya",
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: textDark),
            ),
          ],
        ),
        const SizedBox(height: 12),
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: outlineVariant.withOpacity(0.4)),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.015),
                blurRadius: 10,
                offset: const Offset(0, 4),
              )
            ],
          ),
          child: Row(
            children: [
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  color: softRed,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Icon(Icons.menu_book_rounded, color: primaryRed, size: 22),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      "CALON ABDI NEGARA",
                      style: TextStyle(fontSize: 14, fontWeight: FontWeight.w900, color: textDark),
                    ),
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        Container(
                          width: 6,
                          height: 6,
                          decoration: const BoxDecoration(
                            color: accentTeal,
                            shape: BoxShape.circle,
                          ),
                        ),
                        const SizedBox(width: 6),
                        const Text(
                          "Aktif",
                          style: TextStyle(color: accentTeal, fontSize: 11, fontWeight: FontWeight.bold),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              const Icon(Icons.chevron_right_rounded, color: neutralGray, size: 24),
            ],
          ),
        ),
      ],
    );
  }

  // ========== 🟢 3. SEKSI ASYMMETRIC SIGN OUT CARD ==========
  Widget _buildSignOutCard() {
    return InkWell(
      onTap: () {
        _showSnack("Proses logout berhasil.");
      },
      borderRadius: BorderRadius.circular(20),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: errorRed.withOpacity(0.08), 
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: errorRed.withOpacity(0.12)),
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: errorRed.withOpacity(0.15),
                borderRadius: BorderRadius.circular(12),
              ),
              child: const Icon(Icons.logout_rounded, color: errorRed, size: 22),
            ),
            const SizedBox(width: 14),
            const Expanded(
              child: Text(
                "Sign Out",
                style: TextStyle(fontSize: 14.5, fontWeight: FontWeight.w900, color: errorRed),
              ),
            ),
            const Icon(Icons.chevron_right_rounded, color: errorRed, size: 20),
          ],
        ),
      ),
    );
  }

  // ========== 🟢 4. HIGHLIGHTED BOTTOM NAVIGATION BAR ==========
  Widget _buildBottomNavigationBar() {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: const BorderRadius.only(
          topLeft: Radius.circular(20),
          topRight: Radius.circular(20),
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.06),
            blurRadius: 15,
            offset: const Offset(0, -3),
          ),
        ],
      ),
      child: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildNavItem(Icons.home_rounded, 'Beranda', false),
              _buildNavItem(Icons.school_rounded, 'Kelas', false),
              _buildNavItem(Icons.assignment_rounded, 'Tugas', false),
              _buildNavItem(Icons.person_rounded, 'Profil', true),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildNavItem(IconData icon, String label, bool isActive) {
    if (isActive) {
      return Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        decoration: BoxDecoration(
          color: accentTeal,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: accentTeal.withOpacity(0.2),
              blurRadius: 8,
              offset: const Offset(0, 4),
            )
          ],
        ),
        child: Row(
          children: [
            Icon(icon, color: Colors.white, size: 20),
            const SizedBox(width: 6),
            Text(
              label,
              style: const TextStyle(
                fontSize: 11.5,
                fontWeight: FontWeight.w900,
                color: Colors.white,
              ),
            ),
          ],
        ),
      );
    }

    return GestureDetector(
      onTap: () {},
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
        color: Colors.transparent,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              icon,
              color: neutralGray,
              size: 22,
            ),
            const SizedBox(height: 2),
            Text(
              label,
              style: const TextStyle(
                fontSize: 10.5,
                fontWeight: FontWeight.bold,
                color: neutralGray,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionTitle(String title, String emoji) {
    return Row(
      children: [
        Text(emoji, style: const TextStyle(fontSize: 18)),
        const SizedBox(width: 8),
        Text(
          title,
          style: const TextStyle(
            fontSize: 16.5, 
            fontWeight: FontWeight.w900, 
            color: textDark,
            letterSpacing: -0.4,
          ),
        ),
      ],
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
            child: Divider(color: outlineVariant.withOpacity(0.4)),
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
          child: Icon(icon, color: primaryRed, size: 20),
        ),
        const SizedBox(width: 15),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: const TextStyle(
                  color: neutralGray,
                  fontSize: 11,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 2),
              Text(
                value,
                style: const TextStyle(
                  color: textDark,
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
      style: const TextStyle(color: textDark, fontWeight: FontWeight.bold, fontSize: 14),
      decoration: InputDecoration(
        filled: true,
        fillColor: const Color(0xFFFBFBFB),
        labelText: label,
        labelStyle: const TextStyle(color: neutralGray, fontSize: 12, fontWeight: FontWeight.w600),
        prefixIcon: Icon(icon, color: accentTeal, size: 20),
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 18),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(15),
          borderSide: BorderSide(color: outlineVariant),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(15),
          borderSide: const BorderSide(color: accentTeal, width: 1.5),
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
          shadowColor: accentTeal.withOpacity(0.3),
          backgroundColor: accentTeal,
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
      border: Border.all(color: outlineVariant.withOpacity(0.4)),
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