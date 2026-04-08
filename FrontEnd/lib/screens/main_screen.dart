import 'package:flutter/material.dart';
import 'home_page.dart';
import 'jadwal_page.dart';
import 'kelas_page.dart';
import 'akun_page.dart';

class MainScreen extends StatefulWidget {
  final String userName;
  final String token;
  final Map userProfileData;

  const MainScreen({
    super.key,
    required this.userName,
    required this.token,
    required this.userProfileData,
  });

  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  int _selectedIndex = 0;
  final Color spektaRed = const Color(0xFF990000);

  // Daftar halaman disimpan agar tidak reload setiap kali pindah tab
  late List<Widget> _pages;

  @override
  void initState() {
    super.initState();
    _pages = [
      HomePage(
        userName: widget.userName,
        token: widget.token,
        userData: widget.userProfileData,
      ),
      KelasPage(token: widget.token, userData: widget.userProfileData),
      JadwalPage(token: widget.token),
      AkunPage(token: widget.token, userData: widget.userProfileData),
    ];
  }

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA), // Latar belakang abu tipis yang bersih
      // IndexedStack menjaga state/posisi scroll di setiap halaman
      body: IndexedStack(
        index: _selectedIndex,
        children: _pages,
      ),

      // --- BOTTOM NAVIGATION BAR PROFESIONAL ---
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: const BorderRadius.only(
            topLeft: Radius.circular(25),
            topRight: Radius.circular(25),
          ),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.08),
              blurRadius: 20,
              offset: const Offset(0, -5),
            ),
          ],
        ),
        child: SafeArea(
          child: Container(
            height: 75,
            padding: const EdgeInsets.symmetric(horizontal: 10),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                _buildNavItem(0, Icons.grid_view_rounded, "Beranda"),
                _buildNavItem(1, Icons.auto_stories_rounded, "Kelas"),
                _buildNavItem(2, Icons.calendar_month_rounded, "Jadwal"),
                _buildNavItem(3, Icons.person_rounded, "Akun"),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // Widget Pembantu untuk Navigasi Bar dengan Indikator
  Widget _buildNavItem(int index, IconData icon, String label) {
    bool isSelected = _selectedIndex == index;
    
    return Expanded(
      child: InkWell(
        onTap: () => _onItemTapped(index),
        splashColor: Colors.transparent,
        highlightColor: Colors.transparent,
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Ikon dengan animasi perubahan ukuran kecil
            AnimatedContainer(
              duration: const Duration(milliseconds: 300),
              padding: const EdgeInsets.all(4),
              child: Icon(
                icon,
                size: isSelected ? 28 : 24,
                color: isSelected ? spektaRed : Colors.grey.shade400,
              ),
            ),
            const SizedBox(height: 2),
            Text(
              label,
              style: TextStyle(
                color: isSelected ? spektaRed : Colors.grey.shade400,
                fontSize: 11,
                fontWeight: isSelected ? FontWeight.bold : FontWeight.w500,
                letterSpacing: 0.2,
              ),
            ),
            const SizedBox(height: 6),
            // Indikator garis kecil di bawah menu yang aktif
            AnimatedContainer(
              duration: const Duration(milliseconds: 300),
              height: 3,
              width: isSelected ? 20 : 0,
              decoration: BoxDecoration(
                color: spektaRed,
                borderRadius: BorderRadius.circular(2),
              ),
            ),
          ],
        ),
      ),
    );
  }
}