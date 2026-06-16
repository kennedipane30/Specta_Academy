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
  // 🎨 PALET WARNA SPEKTA (KONSISTEN DENGAN TRYOUTDETAILPAGE)
  // ============================================================
  static const Color primaryRed      = Color(0xFFC5352C);
  static const Color accentTeal      = Color(0xFF2EA8AB);
  static const Color darkTeal        = Color(0xFF00696C);
  static const Color neutralGray     = Color(0xFF64748B);
  static const Color outlineVariant  = Color(0xFFE2BEBA);

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
        userName: widget.userName, // Tambahkan userName untuk AkunPage
      ),
    ];

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: IndexedStack(
        index: _selectedIndex,
        children: pages,
      ),
      bottomNavigationBar: _buildSolidBottomNav(),
    );
  }

  Widget _buildSolidBottomNav() {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: const BorderRadius.only(
          topLeft: Radius.circular(20),
          topRight: Radius.circular(20),
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 20, 
            offset: const Offset(0, -5)
          ),
        ],
      ),
      child: SafeArea(
        child: SizedBox(
          height: 65,
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
        behavior: HitTestBehavior.opaque,
        child: Column(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            AnimatedContainer(
              duration: const Duration(milliseconds: 300),
              curve: Curves.easeOutCubic,
              height: 3,
              width: isSelected ? 24 : 0,
              decoration: BoxDecoration(
                color: isSelected ? primaryRed : Colors.transparent,
                borderRadius: const BorderRadius.only(
                  bottomLeft: Radius.circular(5),
                  bottomRight: Radius.circular(5),
                )
              ),
            ),
            
            const Spacer(),
            
            AnimatedSwitcher(
              duration: const Duration(milliseconds: 200),
              transitionBuilder: (Widget child, Animation<double> animation) {
                return ScaleTransition(scale: animation, child: child);
              },
              child: Icon(
                isSelected ? activeIcon : inactiveIcon, 
                key: ValueKey<bool>(isSelected),
                size: isSelected ? 26 : 24, 
                color: isSelected ? primaryRed : neutralGray,
              ),
            ),
            
            const SizedBox(height: 4),
            
            Text(
              label, 
              style: TextStyle(
                color: isSelected ? primaryRed : neutralGray, 
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