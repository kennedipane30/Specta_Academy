import 'package:flutter/material.dart';

class InfoProgramPage extends StatelessWidget {
  final String title;
  const InfoProgramPage({super.key, required this.title});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(title), backgroundColor: const Color(0xFF990000)),
      body: Center(child: Text("Halaman informasi mengenai program $title")),
    );
  }
}