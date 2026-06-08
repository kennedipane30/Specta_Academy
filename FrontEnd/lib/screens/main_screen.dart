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

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    final List<Widget> pages = [
      HomePage(userName: widget.userName, token: widget.token, userData: widget.userProfileData),
      KelasPage(
        token: widget.token, userData: widget.userProfileData,
        onGoToProfile: () => setState(() => _selectedIndex = 3), 
        onGoToHome: () => setState(() => _selectedIndex = 0),    
      ),
      ReportPage(
        token: widget.token, userData: widget.userProfileData,
        onGoToHome: () => setState(() => _selectedIndex = 0),    
      ),
      AkunPage(
        token: widget.token, userData: widget.userProfileData,
        onGoToHome: () => setState(() => _selectedIndex = 0)
      ),
    ];

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: IndexedStack(
        index: _selectedIndex,
        children: pages,
      ),
      // MENGGUNAKAN DESAIN SOLID DOCKED (MENEMPEL DI BAWAH)
      bottomNavigationBar: _buildSolidBottomNav(),
    );
  }

  Widget _buildSolidBottomNav() {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        // Lengkungan sangat tipis di bagian atas
        borderRadius: const BorderRadius.only(
          topLeft: Radius.circular(20),
          topRight: Radius.circular(20),
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04), // Bayangan hitam sangat lembut
            blurRadius: 20, 
            offset: const Offset(0, -5)
          ),
        ],
      ),
      child: SafeArea(
        child: SizedBox(
          height: 65, // Ketinggian standar profesional
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              // ✨ PENGGUNAAN IKON OUTLINE (KOSONG) & FILLED (BERISI)
              _buildNavItem(
                index: 0, 
                label: "Home", 
                activeIcon: Icons.home_rounded, 
                inactiveIcon: Icons.home_outlined
              ),
              _buildNavItem(
                index: 1, 
                label: "Kelas", 
                activeIcon: Icons.auto_stories_rounded, 
                inactiveIcon: Icons.auto_stories_outlined
              ),
              _buildNavItem(
                index: 2, 
                label: "Report", 
                activeIcon: Icons.insert_chart_rounded, 
                inactiveIcon: Icons.insert_chart_outlined
              ),
              _buildNavItem(
                index: 3, 
                label: "Profil", 
                activeIcon: Icons.person_rounded, 
                inactiveIcon: Icons.person_outline_rounded
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildNavItem({
    required int index, 
    required String label, 
    required IconData activeIcon, 
    required IconData inactiveIcon
  }) {
    bool isSelected = _selectedIndex == index;

    return Expanded(
      child: GestureDetector(
        onTap: () => _onItemTapped(index),
        behavior: HitTestBehavior.opaque, // Agar seluruh area bisa diklik
        child: Column(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            // ✨ INDIKATOR GARIS ATAS (Muncul saat aktif)
            AnimatedContainer(
              duration: const Duration(milliseconds: 300),
              curve: Curves.easeOutCubic,
              height: 3,
              width: isSelected ? 24 : 0, // Garis memanjang jika dipilih
              decoration: BoxDecoration(
                color: spektaRed,
                borderRadius: const BorderRadius.only(
                  bottomLeft: Radius.circular(5),
                  bottomRight: Radius.circular(5),
                )
              ),
            ),
            
            const Spacer(),
            
            // ✨ ANIMASI GANTI IKON & UKURAN
            AnimatedSwitcher(
              duration: const Duration(milliseconds: 200),
              transitionBuilder: (Widget child, Animation<double> animation) {
                return ScaleTransition(scale: animation, child: child);
              },
              child: Icon(
                isSelected ? activeIcon : inactiveIcon, 
                key: ValueKey<bool>(isSelected), // Wajib untuk AnimatedSwitcher
                size: isSelected ? 26 : 24, 
                color: isSelected ? spektaRed : Colors.grey.shade400,
              ),
            ),
            
            const SizedBox(height: 4),
            
            // ✨ TEKS MENU
            Text(
              label, 
              style: TextStyle(
                color: isSelected ? spektaRed : Colors.grey.shade400, 
                fontSize: 11, 
                fontWeight: isSelected ? FontWeight.w800 : FontWeight.w600
              )
            ),
            
            const Spacer(),
          ],
        ),
      ),
    );
  }
}