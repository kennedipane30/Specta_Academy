import 'package:flutter/material.dart';
import 'home_page.dart';
import 'report_page.dart';
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

  // Fungsi untuk berpindah tab
  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    // List halaman yang ditampilkan berdasarkan tab yang dipilih
    final List<Widget> pages = [
      // INDEX 0: HOME
      HomePage(
        userName: widget.userName,
        token: widget.token,
        userData: widget.userProfileData,
      ),

      // INDEX 1: CLASSES
      KelasPage(
        token: widget.token,
        userData: widget.userProfileData,
        onGoToProfile: () => setState(() => _selectedIndex = 3), // Pindah ke Profile
        onGoToHome: () => setState(() => _selectedIndex = 0),    // Balik ke Home
      ),

      // INDEX 2: REPORT
      ReportPage(
        token: widget.token,
        userData: widget.userProfileData,
        onGoToHome: () => setState(() => _selectedIndex = 0),    // Balik ke Home
      ),

      // INDEX 3: PROFILE
      AkunPage(
        token: widget.token, 
        userData: widget.userProfileData,
         onGoToHome: () => setState(() => _selectedIndex = 0)
      ),
    ];

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: IndexedStack(
        index: _selectedIndex,
        children: pages,
      ),
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
              offset: const Offset(0, -5)
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
                _buildNavItem(0, Icons.grid_view_rounded, "Home"),
                _buildNavItem(1, Icons.auto_stories_rounded, "Classes"),
                _buildNavItem(2, Icons.analytics_rounded, "Report"), 
                _buildNavItem(3, Icons.person_rounded, "Profile"),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildNavItem(int index, IconData icon, String label) {
    bool isSelected = _selectedIndex == index;
    return Expanded(
      child: InkWell(
        splashColor: Colors.transparent,
        highlightColor: Colors.transparent,
        onTap: () => _onItemTapped(index),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              icon, 
              size: isSelected ? 28 : 24, 
              color: isSelected ? spektaRed : Colors.grey.shade400
            ),
            const SizedBox(height: 4),
            Text(
              label, 
              style: TextStyle(
                color: isSelected ? spektaRed : Colors.grey.shade400, 
                fontSize: 11, 
                fontWeight: isSelected ? FontWeight.bold : FontWeight.w500
              )
            ),
          ],
        ),
      ),
    );
  }
}