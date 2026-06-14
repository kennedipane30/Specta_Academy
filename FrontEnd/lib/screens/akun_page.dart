import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../services/auth_service.dart';
import 'edit_profile_page.dart';
import 'login_page.dart';

class AkunPage extends StatefulWidget {
  final String token;
  final Map userData;
  final VoidCallback onGoToHome;

  const AkunPage({
    super.key,
    required this.token,
    required this.userData,
    required this.onGoToHome,
  });

  @override
  State<AkunPage> createState() => _AkunPageState();
}

class _AkunPageState extends State<AkunPage> {
  late Map currentData;
  bool isLoading = false;
  bool isUploadingPhoto = false;

  // ============================================================
  // 🎨 PALET WARNA BARU SPEKTA GEN-Z (KONTRAS TINGGI, CLEAN, PREMIUM)
  // ============================================================
  static const Color primaryRed = Color(0xFFC5352C);       // Merah Spekta
  static const Color brightRed = Color(0xFFE53935);        // Aksen Merah Terang
  static const Color accentTeal = Color(0xFF2EA8AB);       // Teal Estetik
  static const Color pageBg = Color(0xFFF8FAFC);           // Slate 50 (Abu Terang Bersih)
  static const Color textDark = Color(0xFF0F172A);         // Slate 900
  static const Color textDarkVariant = Color(0xFF334155);  // Slate 700
  static const Color neutralGray = Color(0xFF64748B);      // Slate 500
  static const Color outlineVariant = Color(0xFFE2E8F0);   // Border Abu Halus
  static const Color lightBlueBg = Color(0xFFEFF4FF);      // Latar Ikon
  static const Color softRed = Color(0xFFFEE2E2);          // Latar Merah Lembut
  static const Color errorRed = Color(0xFFBA1A1A);         // Merah Logout

  @override
  void initState() {
    super.initState();
    currentData = widget.userData;
    _refreshProfile();
  }

  Future<void> _refreshProfile() async {
    if (!mounted) return;
    setState(() => isLoading = true);
    try {
      final userData = await AuthService.getUserProfile(widget.token);
      if (userData != null) {
        setState(() {
          currentData = userData;
        });
        debugPrint("✅ ENROLLED CLASSES: ${currentData['enrolled_classes']}");
        if (currentData['enrolled_classes'] != null && currentData['enrolled_classes'].isNotEmpty) {
          debugPrint("✅ PROGRAM NAME: ${currentData['enrolled_classes'][0]['program_name']}");
        }
      }
    } catch (e) {
      debugPrint("Refresh Error: $e");
    } finally {
      if (mounted) setState(() => isLoading = false);
    }
  }

  Future<void> _pickAndUploadImage() async {
    final ImagePicker picker = ImagePicker();
    
    final ImageSource? source = await showModalBottomSheet<ImageSource>(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (BuildContext context) {
        return SafeArea(
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Text(
                  "Pilih Sumber Foto",
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 16),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                  children: [
                    _buildImageSourceOption(
                      icon: Icons.camera_alt,
                      label: "Kamera",
                      onTap: () => Navigator.pop(context, ImageSource.camera),
                    ),
                    _buildImageSourceOption(
                      icon: Icons.photo_library,
                      label: "Galeri",
                      onTap: () => Navigator.pop(context, ImageSource.gallery),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
              ],
            ),
          ),
        );
      },
    );

    if (source == null) return;

    final XFile? image = await picker.pickImage(
      source: source,
      imageQuality: 80,
      maxWidth: 1024,
      maxHeight: 1024,
    );

    if (image != null) {
      setState(() => isUploadingPhoto = true);
      
      File file = File(image.path);
      
      Map<String, dynamic>? result = await AuthService.uploadProfilePhoto(file, widget.token);

      setState(() => isUploadingPhoto = false);
      
      if (result != null && result['status'] == 'success') {
        final oldPhotoUrl = currentData['photo_url'];
        if (oldPhotoUrl != null && oldPhotoUrl.isNotEmpty) {
          await CachedNetworkImage.evictFromCache(oldPhotoUrl);
        }
        
        setState(() {
          currentData['photo_url'] = result['photo_url'];
        });
        
        await _refreshProfile();
        
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text("Foto profil berhasil diperbarui!", textAlign: TextAlign.center, style: TextStyle(fontWeight: FontWeight.bold)),
              backgroundColor: Color(0xFF10B981),
              duration: Duration(seconds: 2),
              behavior: SnackBarBehavior.floating,
            ),
          );
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text("Gagal mengunggah foto. Coba lagi.", textAlign: TextAlign.center, style: TextStyle(fontWeight: FontWeight.bold)),
              backgroundColor: primaryRed,
              duration: Duration(seconds: 2),
              behavior: SnackBarBehavior.floating,
            ),
          );
        }
      }
    }
  }

  // DI SINI PERBAIKANNYA: Menghapus keyword 'const' yang tidak konstan pada parameter dinamis
  Widget _buildImageSourceOption({
    required IconData icon,
    required String label,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Column(
        children: [
          Container(
            width: 60,
            height: 60,
            decoration: BoxDecoration(
              color: softRed,
              borderRadius: BorderRadius.circular(16),
            ),
            child: Icon(icon, color: primaryRed, size: 28),
          ),
          const SizedBox(height: 8),
          Text(label, style: const TextStyle(color: textDark, fontWeight: FontWeight.w600)),
        ],
      ),
    );
  }

  void _showLogoutDialog() {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          title: const Text("Konfirmasi Logout", style: TextStyle(color: textDark, fontWeight: FontWeight.w900)),
          content: const Text("Apakah Anda yakin ingin keluar dari aplikasi?"),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context), 
              child: const Text("Batal", style: TextStyle(color: neutralGray, fontWeight: FontWeight.bold)),
            ),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: primaryRed,
                elevation: 0,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              onPressed: () => Navigator.pushAndRemoveUntil(
                context, 
                MaterialPageRoute(builder: (_) => const LoginPage()), 
                (route) => false,
              ),
              child: const Text("Ya, Logout", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: pageBg,
      body: RefreshIndicator(
        color: primaryRed,
        onRefresh: _refreshProfile,
        child: Column(
          children: [
            _buildRedHeader(),
            Expanded(
              child: isLoading
                  ? const Center(child: CircularProgressIndicator(color: primaryRed))
                  : SingleChildScrollView(
                      physics: const AlwaysScrollableScrollPhysics(),
                      padding: const EdgeInsets.fromLTRB(18, 24, 18, 120),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _sectionTitle("Kelas Saya"),
                          const SizedBox(height: 14),
                          _buildEnrolledClasses(),
                          const SizedBox(height: 28),
                          _sectionTitle("Pengaturan Akun"),
                          const SizedBox(height: 14),
                          _buildSettingsCard(),
                          const SizedBox(height: 28),
                          _buildSignOutButton(),
                        ],
                      ),
                    ),
            ),
          ],
        ),
      ),
    );
  }

  // HEADER ATAS GRADIEN MERAH LENGKUNG PREMIUM DENGAN PETA PENUTUP TANDA KURUNG YANG SUDAH DIPERBAIKI PRESISI
  Widget _buildRedHeader() {
    String photoUrl = currentData['photo_url'] ?? '';
    String name = currentData['name'] ?? "User Name";
    String email = currentData['email'] ?? "user@email.com";
    String role = currentData['role'] ?? "STUDENT";

    return Container(
      width: double.infinity,
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [primaryRed, accentTeal],
        ),
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(32),
          bottomRight: Radius.circular(32),
        ),
      ),
      child: SafeArea(
        bottom: false,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(20, 20, 20, 30),
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
                  Row(
                    children: [
                      GestureDetector(
                        onTap: widget.onGoToHome,
                        child: Container(
                          width: 44,
                          height: 44,
                          decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.15),
                            borderRadius: BorderRadius.circular(14),
                          ),
                          child: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white, size: 18),
                        ),
                      ),
                      const SizedBox(width: 12),
                      const Expanded(
                        child: Text(
                          "Profile",
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 22,
                            fontWeight: FontWeight.w800,
                            letterSpacing: -0.5,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 28),
                  
                  // FOTO PROFIL BINGKAI PUTIH DENGAN EDIT BUTTON
                  GestureDetector(
                    onTap: _pickAndUploadImage,
                    child: Stack(
                      alignment: Alignment.bottomRight,
                      children: [
                        Container(
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            border: Border.all(color: Colors.white, width: 3.5),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black.withOpacity(0.15),
                                blurRadius: 12,
                                offset: const Offset(0, 4),
                              ),
                            ],
                          ),
                          child: CircleAvatar(
                            radius: 48,
                            backgroundColor: Colors.white,
                            child: isUploadingPhoto
                                ? const SizedBox(
                                    width: 36,
                                    height: 36,
                                    child: CircularProgressIndicator(
                                      strokeWidth: 3,
                                      valueColor: AlwaysStoppedAnimation<Color>(primaryRed),
                                    ),
                                  )
                                : ClipOval(
                                    child: (photoUrl.isNotEmpty)
                                        ? CachedNetworkImage(
                                            imageUrl: photoUrl,
                                            width: 96,
                                            height: 96,
                                            fit: BoxFit.cover,
                                            placeholder: (context, url) => const Center(
                                              child: SizedBox(
                                                width: 24,
                                                height: 24,
                                                child: CircularProgressIndicator(strokeWidth: 2),
                                              ),
                                            ),
                                            errorWidget: (context, url, error) => const Icon(
                                              Icons.person_rounded,
                                              color: primaryRed,
                                              size: 48,
                                            ),
                                          )
                                        : const Icon(Icons.person_rounded, color: primaryRed, size: 52),
                                  ),
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.all(7),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            shape: BoxShape.circle,
                            border: Border.all(color: primaryRed, width: 2.0),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black.withOpacity(0.1),
                                blurRadius: 6,
                                offset: const Offset(0, 2),
                              ),
                            ],
                          ),
                          child: const Icon(Icons.camera_alt_rounded, color: primaryRed, size: 14),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 18),
                  
                  // NAMA USER
                  Text(
                    name,
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 22,
                      fontWeight: FontWeight.w800,
                      letterSpacing: -0.4,
                    ),
                  ),
                  const SizedBox(height: 4),
                  
                  // EMAIL
                  Text(
                    email,
                    style: TextStyle(
                      color: Colors.white.withOpacity(0.85),
                      fontSize: 13,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  const SizedBox(height: 12),
                  
                  // ROLE BADGE (Kapsul Glassmorphic)
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 5),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.15),
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: Colors.white.withOpacity(0.2)),
                    ),
                    child: Text(
                      role.toUpperCase(),
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 10,
                        fontWeight: FontWeight.w900,
                        letterSpacing: 1.2,
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  // DAFTAR KELAS SAYA BERGAYA BENTO CARD
  Widget _buildEnrolledClasses() {
    List classes = currentData['enrolled_classes'] ?? [];

    if (classes.isEmpty) {
      return Container(
        width: double.infinity,
        padding: const EdgeInsets.all(28),
        decoration: _cardDecoration(),
        child: Column(
          children: [
            const Icon(Icons.school_outlined, color: neutralGray, size: 48),
            const SizedBox(height: 12),
            const Text(
              "Belum ada kelas terdaftar",
              style: TextStyle(color: textDarkVariant, fontWeight: FontWeight.bold, fontSize: 13),
            ),
            const SizedBox(height: 4),
            const Text(
              "Silakan daftar kelas terlebih dahulu",
              style: TextStyle(color: neutralGray, fontSize: 11, fontWeight: FontWeight.w500),
            ),
          ],
        ),
      );
    }

    return Container(
      decoration: _cardDecoration(),
      child: ListView.separated(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        itemCount: classes.length,
        padding: EdgeInsets.zero,
        separatorBuilder: (context, index) => _line(),
        itemBuilder: (context, index) {
          final cls = classes[index];
          return ListTile(
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            leading: Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: softRed,
                borderRadius: BorderRadius.circular(14),
              ),
              child: const Icon(Icons.menu_book_rounded, color: primaryRed, size: 24),
            ),
            title: Text(
              cls['program_name'] ?? 'Nama Kelas',
              style: const TextStyle(color: textDark, fontWeight: FontWeight.bold, fontSize: 15),
            ),
            trailing: Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                color: accentTeal.withOpacity(0.12),
                borderRadius: BorderRadius.circular(20),
              ),
              child: const Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(Icons.check_circle, color: accentTeal, size: 14),
                  SizedBox(width: 4),
                  Text("Aktif", style: TextStyle(color: accentTeal, fontSize: 10, fontWeight: FontWeight.bold)),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _sectionTitle(String t) {
    return Row(
      children: [
        Container(
          width: 4,
          height: 18,
          decoration: BoxDecoration(
            color: primaryRed,
            borderRadius: BorderRadius.circular(99),
          ),
        ),
        const SizedBox(width: 8),
        Text(t, style: const TextStyle(color: textDark, fontSize: 16, fontWeight: FontWeight.w900)),
      ],
    );
  }

  Widget _buildSettingsCard() {
    return Container(
      decoration: _cardDecoration(),
      child: Column(
        children: [
          _menuItem(
            title: "Personal Data",
            subtitle: "Update address and parent info",
            icon: Icons.person_outline_rounded,
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (_) => EditProfilePage(userData: currentData, token: widget.token),
                ),
              ).then((_) => _refreshProfile());
            },
          ),
          _line(),
          _menuItem(
            title: "Security",
            subtitle: "Change password and security",
            icon: Icons.lock_outline_rounded,
            onTap: () {
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text("Fitur sedang dalam pengembangan")),
              );
            },
          ),
        ],
      ),
    );
  }

  Widget _menuItem({
    required String title,
    required String subtitle,
    required IconData icon,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(24),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            Container(
              width: 48,
              height: 48,
              decoration: BoxDecoration(
                color: softRed,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(icon, color: primaryRed, size: 24),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: const TextStyle(color: textDark, fontSize: 15, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 2),
                  Text(subtitle, style: TextStyle(color: Colors.grey[500], fontSize: 12)),
                ],
              ),
            ),
            Icon(Icons.arrow_forward_ios_rounded, size: 14, color: Colors.grey[400]),
          ],
        ),
      ),
    );
  }

  Widget _buildSignOutButton() {
    return InkWell(
      onTap: _showLogoutDialog,
      borderRadius: BorderRadius.circular(20),
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: errorRed.withOpacity(0.08),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: errorRed.withOpacity(0.12)),
        ),
        child: Row(
          children: [
            const Icon(Icons.logout_rounded, color: errorRed, size: 22),
            const SizedBox(width: 16),
            const Expanded(
              child: Text(
                "Sign Out",
                style: TextStyle(color: errorRed, fontSize: 14.5, fontWeight: FontWeight.w900),
              ),
            ),
            const Icon(Icons.arrow_forward_ios_rounded, color: errorRed, size: 14),
          ],
        ),
      ),
    );
  }

  Widget _line() => Padding(
    padding: const EdgeInsets.only(left: 76, right: 16),
    child: Divider(height: 1, color: outlineVariant.withOpacity(0.4)),
  );

  BoxDecoration _cardDecoration() => BoxDecoration(
    color: Colors.white,
    borderRadius: BorderRadius.circular(24),
    border: Border.all(color: outlineVariant.withOpacity(0.4)),
    boxShadow: [
      BoxShadow(
        color: Colors.black.withOpacity(0.015),
        blurRadius: 20,
        offset: const Offset(0, 8),
      ),
    ],
  );
}