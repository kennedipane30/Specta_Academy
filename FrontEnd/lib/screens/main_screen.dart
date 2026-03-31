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
    required this.userProfileData
  });

  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  int _selectedIndex = 0;
  final Color spektaRed = const Color(0xFF990000);

  // Fungsi navigasi
  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });
  }

  Widget _getBody() {
    switch (_selectedIndex) {
      case 0: return HomePage(userName: widget.userName);
      case 1: return KelasPage(token: widget.token, userData: widget.userProfileData);
      case 2: 
        // MODIFIKASI: Mengganti Notifikasi menjadi Jadwal
        return JadwalPage(token: widget.token);
      case 3: return AkunPage(token: widget.token, userData: widget.userProfileData);
      default: return HomePage(userName: widget.userName);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      extendBody: true, 
      body: _getBody(),

      // --- TOMBOL TOGA MERAH UTAMA (FLOATING) ---
      floatingActionButton: Container(
        height: 70,
        width: 70,
        decoration: BoxDecoration(
          color: spektaRed,
          shape: BoxShape.circle,
          boxShadow: [
            BoxShadow(
              color: spektaRed.withOpacity(0.4),
              spreadRadius: 4,
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
          border: Border.all(color: Colors.white, width: 4),
        ),
        child: FloatingActionButton(
          onPressed: () {
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(backgroundColor: Color(0xFF990000), content: Text("🎓 Mulai Belajar di Spekta Academy!")),
            );
          },
          backgroundColor: Colors.transparent,
          elevation: 0,
          highlightElevation: 0,
          child: const Icon(
            Icons.school_rounded,
            color: Colors.white,
            size: 35,
          ),
        ),
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerDocked,

      // --- BOTTOM APP BAR DENGAN LUBANG (NOTCH) ---
      bottomNavigationBar: BottomAppBar(
        shape: const CircularNotchedRectangle(),
        notchMargin: 10.0,
        color: Colors.white,
        elevation: 15,
        child: Container(
          height: 60,
          padding: const EdgeInsets.symmetric(horizontal: 10),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              // SISI KIRI: Beranda & Kelas
              Row(
                children: [
                  _buildNavItem(0, Icons.grid_view_rounded, "Beranda"),
                  _buildNavItem(1, Icons.auto_stories_rounded, "Kelas"),
                ],
              ),
              
              const SizedBox(width: 40), 

              // SISI KANAN: Jadwal & Akun
              Row(
                children: [
                  // MODIFIKASI: Ikon dan Label diganti menjadi Jadwal
                  _buildNavItem(2, Icons.calendar_month_rounded, "Jadwal"),
                  _buildNavItem(3, Icons.account_circle_rounded, "Akun"),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildNavItem(int index, IconData icon, String label) {
    bool isSelected = _selectedIndex == index;
    return MaterialButton(
      minWidth: 40,
      splashColor: Colors.transparent,
      highlightColor: Colors.transparent,
      onPressed: () => _onItemTapped(index),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            icon,
            size: 26,
            color: isSelected ? spektaRed : Colors.grey.shade400,
          ),
          const SizedBox(height: 2),
          Text(
            label,
            style: TextStyle(
              color: isSelected ? spektaRed : Colors.grey.shade400,
              fontSize: 10,
              fontWeight: isSelected ? FontWeight.bold : FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }
}