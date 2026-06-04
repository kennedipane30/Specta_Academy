import 'package:flutter/material.dart';
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

  final Color redPrimary = const Color(0xFF9C0412);
  final Color redDark = const Color(0xFF740107);
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

  Future<void> _refreshProfile() async {
    if (!mounted) return;
    setState(() => isLoading = true);
    try {
      final response = await AuthService.getUserProfile(widget.token);
      if (response != null && response['user'] != null) {
        setState(() {
          currentData = response['user'];
        });
      }
    } catch (e) {
      debugPrint("Refresh Error: $e");
    } finally {
      if (mounted) setState(() => isLoading = false);
    }
  }

  void _showLogoutDialog() {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)),
          title: Text("Konfirmasi Logout", style: TextStyle(color: redWine, fontWeight: FontWeight.w900)),
          content: const Text("Apakah Anda yakin ingin keluar dari aplikasi Spekta Academy?"),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: Text("Batal", style: TextStyle(color: Colors.grey[600], fontWeight: FontWeight.bold)),
            ),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: redPrimary, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))),
              onPressed: () {
                Navigator.pushAndRemoveUntil(context, MaterialPageRoute(builder: (_) => const LoginPage()), (route) => false);
              },
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
            // 🔴 HEADER MERAH SEPERTI GAMBAR 1 🔴
            _buildRedHeader(),
            // 🔴 SISA KONTEN (TIDAK DIHAPUS)
            Expanded(
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.fromLTRB(18, 20, 18, 120),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildProfileCard(),
                    const SizedBox(height: 28),
                    _sectionTitle("Account Settings"),
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

  // ========== 🔴 HEADER BARU WARNA MERAH (seperti gambar 1) 🔴 ==========
  Widget _buildRedHeader() {
    return Container(
      width: double.infinity,
      decoration: BoxDecoration(
        color: redPrimary,
        borderRadius: const BorderRadius.only(
          bottomLeft: Radius.circular(30),
          bottomRight: Radius.circular(30),
        ),
        boxShadow: [
          BoxShadow(
            color: redDeep.withOpacity(0.3),
            blurRadius: 15,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: SafeArea(
        bottom: false,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(20, 20, 20, 30),
          child: Column(
            children: [
              // Baris atas: Tombol back + Title Profile
              Row(
                children: [
                  // Tombol kembali ke Home
                  GestureDetector(
                    onTap: widget.onGoToHome,
                    child: Container(
                      width: 40,
                      height: 40,
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.2),
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: const Icon(
                        Icons.arrow_back,
                        color: Colors.white,
                        size: 22,
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  const Expanded(
                    child: Text(
                      "Profile",
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 24,
                        fontWeight: FontWeight.w900,
                        letterSpacing: -0.5,
                      ),
                    ),
                  ),
                  // Ikon notifikasi
                  Stack(
                    clipBehavior: Clip.none,
                    children: [
                      Container(
                        width: 40,
                        height: 40,
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.2),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: const Icon(
                          Icons.notifications_none_rounded,
                          color: Colors.white,
                          size: 22,
                        ),
                      ),
                      Positioned(
                        right: 8,
                        top: 8,
                        child: Container(
                          width: 10,
                          height: 10,
                          decoration: const BoxDecoration(
                            color: Colors.white,
                            shape: BoxShape.circle,
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
              const SizedBox(height: 24),
              // Avatar
              Container(
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  border: Border.all(color: Colors.white, width: 3),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.2),
                      blurRadius: 12,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: CircleAvatar(
                  radius: 45,
                  backgroundColor: Colors.white,
                  child: Icon(
                    Icons.person_rounded,
                    color: redPrimary,
                    size: 55,
                  ),
                ),
              ),
              const SizedBox(height: 14),
              // Nama
              Text(
                currentData['name'] ?? "steven",
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 22,
                  fontWeight: FontWeight.w900,
                ),
              ),
              const SizedBox(height: 6),
              // Email
              Text(
                currentData['email'] ?? "stevensimanjuntak2606@gmail.com",
                style: TextStyle(
                  color: Colors.white.withOpacity(0.85),
                  fontSize: 13,
                  fontWeight: FontWeight.w500,
                ),
              ),
              const SizedBox(height: 12),
              // Badge STUDENT
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 6),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(
                  "STUDENT",
                  style: TextStyle(
                    color: redPrimary,
                    fontSize: 11,
                    fontWeight: FontWeight.w900,
                    letterSpacing: 0.5,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // ========== KODE ASLI (TIDAK DIHAPUS, HANYA DISESUAIKAN) ==========
  Widget _buildProfileCard() {
    return Container(
      width: double.infinity,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(30),
        boxShadow: [BoxShadow(color: redDeep.withOpacity(0.2), blurRadius: 25, offset: const Offset(0, 12))],
      ),
      child: Column(
        children: [
          // Stats Row (Langsung tanpa header gradient karena sudah di header merah)
          Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(vertical: 20),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(30),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.05),
                  blurRadius: 10,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: Row(
              children: [
                _statItem(Icons.description_rounded, "0", "Tryouts"),
                _divider(),
                _statItem(Icons.menu_book_rounded, "4", "Classes"),
                _divider(),
                _statItem(Icons.emoji_events_rounded, "-", "Rank"),
                _divider(),
                _statItem(Icons.calendar_month_rounded, "12", "Days"),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _statItem(IconData icon, String value, String label) {
    return Expanded(
      child: Column(
        children: [
          Icon(icon, color: redPrimary, size: 20),
          const SizedBox(height: 6),
          Text(value, style: TextStyle(color: redWine, fontSize: 16, fontWeight: FontWeight.w900)),
          Text(label, style: const TextStyle(color: Colors.grey, fontSize: 10, fontWeight: FontWeight.w600)),
        ],
      ),
    );
  }

  Widget _divider() => Container(width: 1, height: 40, color: Colors.grey.withOpacity(0.15));

  Widget _sectionTitle(String t) => Text(t, style: TextStyle(color: redWine, fontSize: 18, fontWeight: FontWeight.w900));

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
              Navigator.push(context, MaterialPageRoute(builder: (_) => EditProfilePage(userData: currentData, token: widget.token))).then((_) => _refreshProfile());
            },
          ),
          _line(),
          _menuItem(
            title: "Security",
            subtitle: "Change password and security",
            icon: Icons.lock_outline_rounded,
            onTap: () {},
          ),
        ],
      ),
    );
  }

  Widget _menuItem({required String title, required String subtitle, required IconData icon, required VoidCallback onTap}) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(20),
      child: Padding(
        padding: const EdgeInsets.all(18),
        child: Row(
          children: [
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(color: softRed, borderRadius: BorderRadius.circular(12)),
              child: Icon(icon, color: redPrimary, size: 22),
            ),
            const SizedBox(width: 15),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: TextStyle(color: redWine, fontSize: 15, fontWeight: FontWeight.bold)),
                  Text(subtitle, style: const TextStyle(color: Colors.grey, fontSize: 11)),
                ],
              ),
            ),
            const Icon(Icons.arrow_forward_ios_rounded, size: 14, color: Colors.grey),
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
        decoration: BoxDecoration(color: softRed, borderRadius: BorderRadius.circular(20)),
        child: Row(
          children: [
            Icon(Icons.logout_rounded, color: redPrimary),
            const SizedBox(width: 15),
            Expanded(child: Text("Sign Out", style: TextStyle(color: redPrimary, fontSize: 16, fontWeight: FontWeight.bold))),
            Icon(Icons.arrow_forward_ios_rounded, color: redPrimary, size: 14),
          ],
        ),
      ),
    );
  }

  Widget _line() => Padding(padding: const EdgeInsets.only(left: 75, right: 15), child: Divider(height: 1, color: Colors.grey.withOpacity(0.1)));

  BoxDecoration _cardDecoration() => BoxDecoration(
    color: Colors.white,
    borderRadius: BorderRadius.circular(25),
    boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 15, offset: const Offset(0, 8))],
  );
}