import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';

import 'screens/login_page.dart'; // Sesuaikan jika ada perubahan path
import 'theme/theme_controller.dart';

void main() {
  runApp(
    ChangeNotifierProvider(
      create: (_) => ThemeController(),
      child: const SpektaApp(),
    ),
  );
}

class SpektaApp extends StatelessWidget {
  const SpektaApp({super.key});

  @override
  Widget build(BuildContext context) {
    final theme = Provider.of<ThemeController>(context);

    // ============================================================
    // 🎨 INTEGRASI WARNA TEMA BARU SPEKTA ACADEMY
    // ============================================================
    const Color spektaDarkRed = Color(0xFFC5352C);   // #c5352c (Warna Utama/Brand)
    const Color spektaBrightRed = Color(0xFFE53935); // #e53935 (Aksen Aktif)
    const Color spektaTeal = Color(0xFF2EA8AB);      // #2ea8ab (Segar & Edukatif)
    const Color spektaGray = Color(0xFF9E9E9E);      // #9e9e9e (Netral/Batas)
    const Color spektaBgLight = Color(0xFFF8F9FA);   // Putih Off-White untuk Clean Look

    return MaterialApp(
      title: 'Spekta Academy',
      debugShowCheckedModeBanner: false,
      themeMode: theme.isDark ? ThemeMode.dark : ThemeMode.light,

      // --- LIGHT THEME (Clean & Fresh Look) ---
      theme: ThemeData(
        useMaterial3: true,
        primaryColor: spektaDarkRed,
        colorScheme: ColorScheme.fromSeed(
          seedColor: spektaDarkRed,
          primary: spektaDarkRed,
          secondary: spektaTeal,
          surface: Colors.white,
          outline: spektaGray.withOpacity(0.3),
        ),
        fontFamily: GoogleFonts.plusJakartaSans().fontFamily,
        textTheme: GoogleFonts.plusJakartaSansTextTheme(),
        scaffoldBackgroundColor: spektaBgLight,
        
        appBarTheme: const AppBarTheme(
          backgroundColor: Colors.white, // Diubah ke putih agar tidak terlalu padat di atas
          foregroundColor: Colors.black87,
          elevation: 0,
          centerTitle: false, // Diganti false agar teks sapaan rapi di kiri
          iconTheme: IconThemeData(color: Colors.black87),
        ),
      ),

      // --- DARK THEME (Premium Dark Mode) ---
      darkTheme: ThemeData(
        useMaterial3: true,
        primaryColor: spektaDarkRed,
        colorScheme: const ColorScheme.dark(
          primary: spektaDarkRed,
          secondary: spektaTeal,
          surface: Color(0xFF111827), // Slate Dark
        ),
        fontFamily: GoogleFonts.plusJakartaSans().fontFamily,
        textTheme: GoogleFonts.plusJakartaSansTextTheme(
          ThemeData.dark().textTheme,
        ),
        scaffoldBackgroundColor: const Color(0xFF020617), // Deep Navy Black
      ),

      home: const LoginPage(),
    );
  }
}