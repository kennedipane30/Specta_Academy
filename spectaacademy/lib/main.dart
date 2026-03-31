import 'package:flutter/material.dart';
import 'package:spectaacademy/screens/login_page.dart';
import 'screens/main_screen.dart';

void main() => runApp(const SpektaApp());

class SpektaApp extends StatelessWidget {
  const SpektaApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      theme: ThemeData(primaryColor: const Color(0xFF990000)),
      home: const LoginPage(), // HALAMAN PERTAMA
    );
  }
}