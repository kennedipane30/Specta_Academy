import 'package:flutter/material.dart';

class QuestionSharingPage extends StatelessWidget {
  const QuestionSharingPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Bank Soal", style: TextStyle(fontWeight: FontWeight.w900, color: Colors.white)),
        backgroundColor: const Color(0xFF990000),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: GridView.count(
        padding: const EdgeInsets.all(20),
        crossAxisCount: 2,
        mainAxisSpacing: 15,
        crossAxisSpacing: 15,
        children: List.generate(4, (index) => Container(
          decoration: BoxDecoration(
            color: Colors.green.withOpacity(0.1),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: Colors.green.withOpacity(0.3)),
          ),
          child: const Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.history_edu_rounded, color: Colors.green, size: 40),
              SizedBox(height: 10),
              Text("Paket Soal", style: TextStyle(fontWeight: FontWeight.bold)),
              Text("Latihan Mandiri", style: TextStyle(fontSize: 12, color: Colors.grey)),
            ],
          ),
        )),
      ),
    );
  }
}