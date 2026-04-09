import 'package:flutter/material.dart';

class MaterialsPage extends StatelessWidget {
  const MaterialsPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Materi Belajar", style: TextStyle(fontWeight: FontWeight.w900, color: Colors.white)),
        backgroundColor: const Color(0xFF990000),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.menu_book_rounded, size: 80, color: Colors.orange),
            SizedBox(height: 20),
            Text("Kurikulum Terpadu", style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
            Text("Materi akan segera tersedia", style: TextStyle(color: Colors.grey)),
          ],
        ),
      ),
    );
  }
}