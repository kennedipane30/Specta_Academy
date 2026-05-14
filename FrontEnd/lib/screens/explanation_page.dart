import 'package:flutter/material.dart';

class ExplanationPage extends StatelessWidget {
  final List questions;

  const ExplanationPage({super.key, required this.questions});

  @override
  Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text("Pembahasan Tryout", 
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(20),
        itemCount: questions.length,
        itemBuilder: (context, index) {
          final q = questions[index];
          return Container(
            margin: const EdgeInsets.only(bottom: 25),
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(25),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.05), 
                  blurRadius: 10,
                  offset: const Offset(0, 4)
                )
              ],
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Nomor Soal
                Text("SOAL NOMOR ${index + 1}", 
                  style: const TextStyle(fontWeight: FontWeight.w900, color: spektaRed, fontSize: 11)),
                const SizedBox(height: 10),

                // Pertanyaan
                Text(q['question'] ?? '', 
                  style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold, height: 1.4)),
                
                // Gambar Pertanyaan (Jika Ada)
                if (q['question_image'] != null && q['question_image'] != "")
                  Padding(
                    padding: const EdgeInsets.only(top: 10),
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(15),
                      child: Image.network(
                        "http://10.0.2.2:8000/storage/tryout/images/${q['question_image']}",
                        errorBuilder: (context, error, stackTrace) => const SizedBox(),
                      ),
                    ),
                  ),
                
                const Padding(
                  padding: EdgeInsets.symmetric(vertical: 15),
                  child: Divider(),
                ),

                // Jawaban Benar
                Row(
                  children: [
                    const Text("Kunci Jawaban: ", style: TextStyle(fontWeight: FontWeight.bold)),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(color: Colors.green, borderRadius: BorderRadius.circular(8)),
                      // ✨ PERBAIKAN: Menggunakan FontWeight.w900
                      child: Text(q['correct_answer'], 
                        style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900)),
                    ),
                  ],
                ),

                const SizedBox(height: 15),

                // Box Penjelasan
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(15),
                  decoration: BoxDecoration(
                    color: Colors.blue.withOpacity(0.05),
                    borderRadius: BorderRadius.circular(15),
                    border: Border.all(color: Colors.blue.withOpacity(0.1)),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text("PEMBAHASAN:", 
                        style: TextStyle(fontWeight: FontWeight.w900, fontSize: 10, color: Colors.blue)),
                      const SizedBox(height: 5),
                      Text(
                        q['explanation'] ?? "Tidak ada penjelasan tersedia.",
                        style: const TextStyle(fontSize: 13, height: 1.5, color: Colors.black87),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}