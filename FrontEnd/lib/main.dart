import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:spectaacademy/screens/login_page.dart';

void main() => runApp(const SpektaApp());

class SpektaApp extends StatelessWidget {
  const SpektaApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Spekta Academy',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        useMaterial3: true,
        primaryColor: const Color(0xFFC50337),
        fontFamily: GoogleFonts.poppins().fontFamily,
        textTheme: GoogleFonts.poppinsTextTheme(),
        scaffoldBackgroundColor: const Color(0xFF02060E),
      ),
      home: const LoginPage(),
    );
  }
}