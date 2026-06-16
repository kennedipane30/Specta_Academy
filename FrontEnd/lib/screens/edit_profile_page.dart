import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../services/auth_service.dart';
import 'main_screen.dart';
import 'login_page.dart';

class EditProfilePage extends StatefulWidget {
  final Map userData;
  final String token;
  final String userName;

  const EditProfilePage({
    super.key,
    required this.userData,
    required this.token,
    required this.userName,
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
  // 🎨 PALET WARNA SPEKTA
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

    debugPrint("🔍 [DEBUG EDIT PROFIL] userData: ${widget.userData}");
    debugPrint("🔍 [DEBUG EDIT PROFIL] userName: ${widget.userName}");

    // Cari data student
    Map<String, dynamic>? studentData;
    
    if (widget.userData.containsKey('student') && widget.userData['student'] != null) {
      studentData = Map<String, dynamic>.from(widget.userData['student']);
    }
    else if (widget.userData.containsKey('parent_name')) {
      studentData = Map<String, dynamic>.from(widget.userData);
    }
    else if (widget.userData.containsKey('data') && widget.userData['data'] != null) {
      var data = widget.userData['data'];
      if (data.containsKey('student') && data['student'] != null) {
        studentData = Map<String, dynamic>.from(data['student']);
      } else if (data.containsKey('parent_name')) {
        studentData = Map<String, dynamic>.from(data);
      }
    }
    
    if (studentData != null) {
      _parentCtrl.text = studentData['parent_name']?.toString() ?? "";
      _addressCtrl.text = studentData['address']?.toString() ?? "";
      _parentPhoneCtrl.text = studentData['parent_phone']?.toString() ?? "";
      _nisnCtrl.text = studentData['national_id_number']?.toString() ?? "";
      _dobCtrl.text = studentData['date_of_birth']?.toString() ?? "";
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

    try {
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
        
        // Kembali ke MainScreen
        if (mounted) {
          Navigator.pushReplacement(
            context,
            MaterialPageRoute(
              builder: (context) => MainScreen(
                userName: widget.userName,
                token: widget.token,
                userProfileData: widget.userData,
              ),
            ),
          );
        }
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
    } catch (e) {
      if (mounted) Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          backgroundColor: primaryRed,
          content: Text("Kesalahan koneksi: $e"),
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    // Ambil data untuk ditampilkan
    String displayName = widget.userData['full_name'] ?? 
                         widget.userData['name'] ?? 
                         widget.userName ?? 
                         "User";
    
    String displayEmail = widget.userData['email'] ?? "user@email.com";
    String displayPhone = widget.userData['phone'] ?? "-";

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
        title: const Text(
          "Edit Profil",
          style: TextStyle(
            fontWeight: FontWeight.w900,
            color: Colors.white,
            fontSize: 17,
          ),
        ),
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white),
          onPressed: () {
            // Kembali ke MainScreen
            Navigator.pushReplacement(
              context,
              MaterialPageRoute(
                builder: (context) => MainScreen(
                  userName: widget.userName,
                  token: widget.token,
                  userProfileData: widget.userData,
                ),
              ),
            );
          },
        ),
      ),
      body: SingleChildScrollView(
        physics: const BouncingScrollPhysics(),
        padding: const EdgeInsets.fromLTRB(20, 20, 20, 40),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Info Card User
            Container(
              padding: const EdgeInsets.all(20),
              decoration: _cardDecoration(),
              child: Column(
                children: [
                  _infoTile(
                    icon: Icons.person_outline_rounded,
                    title: "Nama Lengkap",
                    value: displayName,
                  ),
                  Padding(
                    padding: const EdgeInsets.symmetric(vertical: 15),
                    child: Divider(color: outlineVariant.withOpacity(0.4)),
                  ),
                  _infoTile(
                    icon: Icons.email_outlined,
                    title: "Email Terdaftar",
                    value: displayEmail,
                  ),
                  Padding(
                    padding: const EdgeInsets.symmetric(vertical: 15),
                    child: Divider(color: outlineVariant.withOpacity(0.4)),
                  ),
                  _infoTile(
                    icon: Icons.phone_android_rounded,
                    title: "Nomor WhatsApp",
                    value: displayPhone,
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),
            
            _buildSectionTitle("Informasi Detail Siswa", "📋"),
            const SizedBox(height: 14),
            _buildFormCard(),
            const SizedBox(height: 24),
            
            // ❌ SIGN OUT CARD TELAH DIHAPUS
            
            _buildSaveButton(),
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

  BoxDecoration _cardDecoration() => BoxDecoration(
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