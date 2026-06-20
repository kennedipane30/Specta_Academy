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
  
  // ============================================================
  // 🎨 PALET WARNA TEAL PROFESIONAL
  // ============================================================
  static const Color primaryTeal      = Color(0xFF1A8A8D);
  static const Color lightTeal        = Color(0xFF2EA8AB);
  static const Color darkTeal         = Color(0xFF00696C);
  static const Color neutralGray      = Color(0xFF64748B);
  static const Color whiteColor       = Color(0xFFFFFFFF);

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
        token: widget.token, 
        userData: widget.userProfileData,
        onGoToProfile: () => setState(() => _selectedIndex = 3), 
        onGoToHome: () => setState(() => _selectedIndex = 0),    
      ),
      ReportPage(
        token: widget.token, 
        userData: widget.userProfileData,
        onGoToHome: () => setState(() => _selectedIndex = 0),    
      ),
      AkunPage(
        token: widget.token, 
        userData: widget.userProfileData,
        onGoToHome: () => setState(() => _selectedIndex = 0),
        userName: widget.userName,
      ),
    ];

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: IndexedStack(
        index: _selectedIndex,
        children: pages,
      ),
      bottomNavigationBar: _buildModernBottomNav(),
    );
  }

  Widget _buildModernBottomNav() {
    return Container(
      decoration: BoxDecoration(
        color: whiteColor,
        borderRadius: const BorderRadius.only(
          topLeft: Radius.circular(24),
          topRight: Radius.circular(24),
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.08),
            blurRadius: 25,
            offset: const Offset(0, -8),
            spreadRadius: 0,
          ),
        ],
      ),
      child: SafeArea(
        child: SizedBox(
          height: 75,
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
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
                activeIcon: Icons.insights_rounded, 
                inactiveIcon: Icons.insights_outlined
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
        behavior: HitTestBehavior.opaque,
        child: Container(
          margin: const EdgeInsets.symmetric(horizontal: 4),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              // Icon tanpa background box
              AnimatedSwitcher(
                duration: const Duration(milliseconds: 200),
                transitionBuilder: (Widget child, Animation<double> animation) {
                  return ScaleTransition(scale: animation, child: child);
                },
                child: Icon(
                  isSelected ? activeIcon : inactiveIcon, 
                  key: ValueKey<bool>(isSelected),
                  size: isSelected ? 32 : 26,
                  color: isSelected ? primaryTeal : neutralGray,
                ),
              ),
              
              const SizedBox(height: 6),
              
              // Label
              AnimatedDefaultTextStyle(
                duration: const Duration(milliseconds: 200),
                style: TextStyle(
                  color: isSelected ? primaryTeal : neutralGray, 
                  fontSize: isSelected ? 12 : 11,
                  fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
                  letterSpacing: isSelected ? 0.3 : 0,
                ),
                child: Text(label),
              ),
            ],
          ),
        ),
      ),
    );
  }
}