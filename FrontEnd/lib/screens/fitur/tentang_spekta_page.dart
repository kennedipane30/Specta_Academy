import 'package:flutter/material.dart';

class TentangSpektaPage extends StatelessWidget {
  const TentangSpektaPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Tentang Spekta"), backgroundColor: const Color(0xFF990000)),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text("Apa itu Spekta Academy?", style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            const Text("Spekta Academy adalah lembaga bimbingan belajar yang berfokus pada persiapan siswa menuju instansi kedinasan, TNI/POLRI, dan PTN Favorit."),
            const SizedBox(height: 20),
            const Text("Program Unggulan:", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            const Text("- Bimbingan Intensif CAT\n- Tes Psikologi & Kesehatan\n- Pembinaan Jasmani (Binsik)"),
          ],
        ),
      ),
    );
  }
}