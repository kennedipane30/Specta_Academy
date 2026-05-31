import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';

import 'screens/login_page.dart';
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

    // Definisi Warna Spekta Utama
    const Color spektaRed = Color(0xFF9C0412);
    const Color spektaDark = Color(0xFF1A1A1A);

    return MaterialApp(
      title: 'Spekta Academy',
      debugShowCheckedModeBanner: false,
      themeMode: theme.isDark ? ThemeMode.dark : ThemeMode.light,

      // --- LIGHT THEME (Pembersihan Warna) ---
      theme: ThemeData(
        useMaterial3: true,
        primaryColor: spektaRed,
        colorScheme: ColorScheme.fromSeed(
          seedColor: spektaRed,
          primary: spektaRed,
          surface: Colors.white,
        ),
        fontFamily: GoogleFonts.plusJakartaSans().fontFamily, // Font lebih modern
        textTheme: GoogleFonts.plusJakartaSansTextTheme(),
        
        // Mengubah dari Pink ke Off-White agar konten lebih menonjol (Clean Look)
        scaffoldBackgroundColor: const Color(0xFFF8F9FA), 
        
        appBarTheme: const AppBarTheme(
          backgroundColor: spektaRed,
          foregroundColor: Colors.white,
          elevation: 0,
          centerTitle: true,
        ),
      ),

      // --- DARK THEME (Premium Dark) ---
      darkTheme: ThemeData(
        useMaterial3: true,
        primaryColor: spektaRed,
        colorScheme: const ColorScheme.dark(
          primary: spektaRed,
          secondary: spektaRed,
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