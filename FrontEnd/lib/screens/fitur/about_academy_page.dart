import 'package:flutter/material.dart';

class AboutAcademyPage extends StatelessWidget {
  const AboutAcademyPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Tentang Spekta", style: TextStyle(fontWeight: FontWeight.w900, color: Colors.white)),
        backgroundColor: const Color(0xFF990000),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(25),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              height: 150,
              width: double.infinity,
              decoration: BoxDecoration(
                color: const Color(0xFF990000),
                borderRadius: BorderRadius.circular(25),
              ),
              child: const Center(child: Text("SPEKTA ACADEMY", style: TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w900))), // PERBAIKAN: Gunakan w900
            ),
            const SizedBox(height: 30),
            const Text("Visi & Misi", style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900)),
            const SizedBox(height: 15),
            const Text(
              "Membantu putra-putri terbaik bangsa untuk meraih impian melalui sistem pembelajaran yang inovatif.",
              style: TextStyle(fontSize: 14, color: Colors.black87, height: 1.6),
            ),
          ],
        ),
      ),
    );
  }
}