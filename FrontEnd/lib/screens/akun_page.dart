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

  final Color redPrimary = const Color(0xFF9C0412);
  final Color redDeep = const Color(0xFF520102);
  final Color redWine = const Color(0xFF3D0606);
  final Color softRed = const Color(0xFFFFE8EA);
  final Color bgColor = const Color(0xFFF8F9FA);

  @override
  void initState() {
    super.initState();
    currentData = widget.userData;
    _refreshProfile();
  }

  // ✅ PERBAIKAN: Method refresh profile yang benar
  Future<void> _refreshProfile() async {
    if (!mounted) return;
    setState(() => isLoading = true);
    try {
      final userData = await AuthService.getUserProfile(widget.token);
      if (userData != null) {
        setState(() {
          currentData = userData;
        });
        // Debug: Cetak enrolled_classes ke console
        print("✅ ENROLLED CLASSES: ${currentData['enrolled_classes']}");
        if (currentData['enrolled_classes'] != null && currentData['enrolled_classes'].isNotEmpty) {
          print("✅ PROGRAM NAME: ${currentData['enrolled_classes'][0]['program_name']}");
        }
      } else {
        print("❌ Gagal mengambil profile: response null");
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
              content: Text("Foto profil berhasil diperbarui!", textAlign: TextAlign.center),
              backgroundColor: Colors.green,
              duration: Duration(seconds: 2),
            ),
          );
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text("Gagal mengunggah foto. Coba lagi.", textAlign: TextAlign.center),
              backgroundColor: Colors.red,
              duration: Duration(seconds: 2),
            ),
          );
        }
      }
    }
  }

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
            child: Icon(icon, color: redPrimary, size: 28),
          ),
          const SizedBox(height: 8),
          Text(label, style: TextStyle(color: redWine, fontWeight: FontWeight.w600)),
        ],
      ),
    );
  }

  void _showLogoutDialog() {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)),
          title: Text("Konfirmasi Logout", style: TextStyle(color: redWine, fontWeight: FontWeight.w900)),
          content: const Text("Apakah Anda yakin ingin keluar dari aplikasi?"),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context), 
              child: Text("Batal", style: TextStyle(color: Colors.grey[600], fontWeight: FontWeight.bold)),
            ),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: redPrimary,
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
      backgroundColor: bgColor,
      body: RefreshIndicator(
        color: redPrimary,
        onRefresh: _refreshProfile,
        child: Column(
          children: [
            _buildRedHeader(),
            Expanded(
              child: isLoading
                  ? const Center(child: CircularProgressIndicator())
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

  Widget _buildRedHeader() {
    String photoUrl = currentData['photo_url'] ?? '';
    String name = currentData['name'] ?? "User Name";
    String email = currentData['email'] ?? "user@email.com";
    String role = currentData['role'] ?? "STUDENT";
    List classes = currentData['enrolled_classes'] ?? [];

    // Debug: Cetak data ke console
    print("=== HEADER DEBUG ===");
    print("Photo URL: $photoUrl");
    print("Name: $name");
    print("Email: $email");
    print("Role: $role");
    print("Classes: $classes");

    return Container(
      width: double.infinity,
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [redPrimary, redDeep, redWine],
        ),
        borderRadius: const BorderRadius.only(
          bottomLeft: Radius.circular(35),
          bottomRight: Radius.circular(35),
        ),
        boxShadow: [
          BoxShadow(
            color: redDeep.withValues(alpha: 0.3),
            blurRadius: 20,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: SafeArea(
        bottom: false,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(20, 20, 20, 30),
          child: Column(
            children: [
              Row(
                children: [
                  GestureDetector(
                    onTap: widget.onGoToHome,
                    child: Container(
                      width: 44,
                      height: 44,
                      decoration: BoxDecoration(
                        color: Colors.white.withValues(alpha: 0.15),
                        borderRadius: BorderRadius.circular(14),
                      ),
                      child: const Icon(Icons.arrow_back, color: Colors.white, size: 22),
                    ),
                  ),
                  const SizedBox(width: 12),
                  const Expanded(
                    child: Text(
                      "Profile",
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 24,
                        fontWeight: FontWeight.w800,
                        letterSpacing: -0.5,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 28),
              
              // FOTO PROFIL
              GestureDetector(
                onTap: _pickAndUploadImage,
                child: Stack(
                  alignment: Alignment.bottomRight,
                  children: [
                    Container(
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border: Border.all(color: Colors.white, width: 4),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withValues(alpha: 0.25),
                            blurRadius: 16,
                            offset: const Offset(0, 6),
                          ),
                        ],
                      ),
                      child: CircleAvatar(
                        radius: 55,
                        backgroundColor: Colors.white,
                        child: isUploadingPhoto
                            ? const SizedBox(
                                width: 40,
                                height: 40,
                                child: CircularProgressIndicator(
                                  strokeWidth: 3,
                                  valueColor: AlwaysStoppedAnimation<Color>(Color(0xFF9C0412)),
                                ),
                              )
                            : (photoUrl.isNotEmpty)
                                ? ClipOval(
                                    child: CachedNetworkImage(
                                      imageUrl: photoUrl,
                                      width: 110,
                                      height: 110,
                                      fit: BoxFit.cover,
                                      placeholder: (context, url) => const Center(
                                        child: SizedBox(
                                          width: 30,
                                          height: 30,
                                          child: CircularProgressIndicator(strokeWidth: 2),
                                        ),
                                      ),
                                      errorWidget: (context, url, error) => Icon(
                                        Icons.person_rounded,
                                        color: redPrimary,
                                        size: 65,
                                      ),
                                    ),
                                  )
                                : Icon(Icons.person_rounded, color: redPrimary, size: 70),
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        shape: BoxShape.circle,
                        border: Border.all(color: redPrimary, width: 2.5),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withValues(alpha: 0.15),
                            blurRadius: 8,
                            offset: const Offset(0, 2),
                          ),
                        ],
                      ),
                      child: Icon(Icons.camera_alt_rounded, color: redPrimary, size: 20),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 18),
              
              // NAMA
              Text(
                name,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 24,
                  fontWeight: FontWeight.w800,
                  letterSpacing: -0.3,
                ),
              ),
              const SizedBox(height: 6),
              
              // EMAIL
              Text(
                email,
                style: TextStyle(
                  color: Colors.white.withValues(alpha: 0.85),
                  fontSize: 14,
                  fontWeight: FontWeight.w500,
                ),
              ),
              
              const SizedBox(height: 12),
              
              // ROLE BADGE
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.15),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(
                  role.toUpperCase(),
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 11,
                    fontWeight: FontWeight.w700,
                    letterSpacing: 0.5,
                  ),
                ),
              ),
              
              // ✅ DAFTAR KELAS DI HEADER
              if (classes.isNotEmpty) ...[
                const SizedBox(height: 20),
                Divider(color: Colors.white.withValues(alpha: 0.2), thickness: 1),
                const SizedBox(height: 14),
                Row(
                  children: [
                    Icon(Icons.school_rounded, color: Colors.white.withValues(alpha: 0.7), size: 16),
                    const SizedBox(width: 8),
                    Text(
                      "Kelas Terdaftar",
                      style: TextStyle(
                        color: Colors.white.withValues(alpha: 0.9),
                        fontSize: 13,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 10),
                Wrap(
                  spacing: 10,
                  runSpacing: 8,
                  children: classes.map((cls) {
                    return Container(
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                      decoration: BoxDecoration(
                        color: Colors.white.withValues(alpha: 0.15),
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: Colors.white.withValues(alpha: 0.3)),
                      ),
                      child: Text(
                        cls['program_name'] ?? 'Kelas',
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    );
                  }).toList(),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildEnrolledClasses() {
    List classes = currentData['enrolled_classes'] ?? [];

    if (classes.isEmpty) {
      return Container(
        width: double.infinity,
        padding: const EdgeInsets.all(28),
        decoration: _cardDecoration(),
        child: Column(
          children: [
            Icon(Icons.school_outlined, color: Colors.grey[400], size: 48),
            const SizedBox(height: 12),
            Text(
              "Belum ada kelas terdaftar",
              style: TextStyle(color: Colors.grey[500], fontWeight: FontWeight.w600),
            ),
            const SizedBox(height: 6),
            Text(
              "Silakan daftar kelas terlebih dahulu",
              style: TextStyle(color: Colors.grey[400], fontSize: 12),
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
        separatorBuilder: (context, index) => _line(),
        itemBuilder: (context, index) {
          final cls = classes[index];
          return ListTile(
            leading: Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [softRed, softRed.withValues(alpha: 0.5)],
                ),
                borderRadius: BorderRadius.circular(14),
              ),
              child: Icon(Icons.menu_book_rounded, color: redPrimary, size: 24),
            ),
            title: Text(
              cls['program_name'] ?? 'Nama Kelas',
              style: TextStyle(color: redWine, fontWeight: FontWeight.bold, fontSize: 15),
            ),
            trailing: Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                color: Colors.green.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(20),
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(Icons.check_circle, color: Colors.green[600], size: 16),
                  const SizedBox(width: 4),
                  Text("Aktif", style: TextStyle(color: Colors.green[600], fontSize: 10, fontWeight: FontWeight.bold)),
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
          height: 20,
          decoration: BoxDecoration(
            color: redPrimary,
            borderRadius: BorderRadius.circular(2),
          ),
        ),
        const SizedBox(width: 10),
        Text(t, style: TextStyle(color: redWine, fontSize: 18, fontWeight: FontWeight.w800)),
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
      borderRadius: BorderRadius.circular(20),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            Container(
              width: 48,
              height: 48,
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [softRed, softRed.withValues(alpha: 0.5)],
                ),
                borderRadius: BorderRadius.circular(14),
              ),
              child: Icon(icon, color: redPrimary, size: 24),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: TextStyle(color: redWine, fontSize: 15, fontWeight: FontWeight.bold)),
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
        padding: const EdgeInsets.all(18),
        decoration: BoxDecoration(
          color: softRed,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: Colors.red.withValues(alpha: 0.15)),
        ),
        child: Row(
          children: [
            Icon(Icons.logout_rounded, color: redPrimary, size: 24),
            const SizedBox(width: 16),
            Expanded(
              child: Text(
                "Sign Out",
                style: TextStyle(color: redPrimary, fontSize: 16, fontWeight: FontWeight.bold),
              ),
            ),
            Icon(Icons.arrow_forward_ios_rounded, color: redPrimary, size: 14),
          ],
        ),
      ),
    );
  }

  Widget _line() => Padding(
    padding: const EdgeInsets.only(left: 76, right: 16),
    child: Divider(height: 1, color: Colors.grey.withValues(alpha: 0.12)),
  );

  BoxDecoration _cardDecoration() => BoxDecoration(
    color: Colors.white,
    borderRadius: BorderRadius.circular(24),
    boxShadow: [
      BoxShadow(
        color: Colors.black.withValues(alpha: 0.04),
        blurRadius: 20,
        offset: const Offset(0, 8),
      ),
    ],
  );
}