import 'package:flutter/material.dart';

class ExplanationPage extends StatelessWidget {
  final List questions;

  const ExplanationPage({super.key, required this.questions});

  // Warna utama Spekta Academy
  static const Color primaryRed = Color(0xFF9C0412); 

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text("Pembahasan Tryout", 
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white, fontSize: 18)),
        backgroundColor: primaryRed,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
        // Menambahkan tombol kembali standar agar user bisa kembali ke list riwayat
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: questions.isEmpty 
        ? _buildEmptyState()
        : ListView.builder(
            padding: const EdgeInsets.fromLTRB(20, 20, 20, 100),
            itemCount: questions.length,
            itemBuilder: (context, index) {
              final q = questions[index];
              
              // ✨ LOGIKA PENGECEKAN JAWABAN
              String userAnswer = (q['user_answer'] ?? '-').toString().trim().toUpperCase();
              String correctAnswer = (q['correct_answer'] ?? '-').toString().trim().toUpperCase();
              
              if (userAnswer == "" || userAnswer == "NULL") userAnswer = "-";
              
              bool isCorrect = (userAnswer != "-") && (userAnswer == correctAnswer);

              return Container(
                margin: const EdgeInsets.only(bottom: 25),
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(25),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.04), 
                      blurRadius: 20,
                      offset: const Offset(0, 8)
                    )
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Header Soal
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                          decoration: BoxDecoration(
                            color: primaryRed.withOpacity(0.1),
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: Text("SOAL #${index + 1}", 
                            style: const TextStyle(fontWeight: FontWeight.w900, color: primaryRed, fontSize: 11)),
                        ),
                        
                        // Badge Status Benar/Salah
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                          decoration: BoxDecoration(
                            color: isCorrect ? Colors.green.withOpacity(0.1) : Colors.red.withOpacity(0.1),
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: Text(
                            isCorrect ? "BENAR" : (userAnswer == "-" ? "TIDAK DIJAWAB" : "SALAH"),
                            style: TextStyle(
                              color: isCorrect ? Colors.green : Colors.red,
                              fontWeight: FontWeight.w900,
                              fontSize: 10,
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 20),

                    // Teks Pertanyaan
                    Text(q['question'] ?? 'Pertanyaan tidak ditemukan.', 
                      style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold, height: 1.6, color: Color(0xFF2D3142))),
                    
                    // Gambar Pertanyaan (Jika Ada)
                    if (q['question_image'] != null && q['question_image'] != "")
                      Padding(
                        padding: const EdgeInsets.only(top: 15),
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(15),
                          child: Image.network(
                            "http://10.0.2.2:8000/storage/tryout/images/${q['question_image']}",
                            loadingBuilder: (context, child, loadingProgress) {
                              if (loadingProgress == null) return child;
                              return const Center(child: CircularProgressIndicator());
                            },
                            errorBuilder: (context, error, stackTrace) => Container(
                              padding: const EdgeInsets.all(10),
                              color: Colors.grey.shade100,
                              child: const Text("Gambar tidak dapat dimuat", style: TextStyle(fontSize: 10)),
                            ),
                          ),
                        ),
                      ),
                    
                    const Padding(
                      padding: EdgeInsets.symmetric(vertical: 20),
                      child: Divider(color: Color(0xFFF1F3F9), thickness: 1.5),
                    ),

                    // Info Jawaban User vs Kunci
                    Row(
                      children: [
                        _buildAnswerCircle("Jawaban Kamu", userAnswer, isCorrect ? Colors.green : Colors.red),
                        const SizedBox(width: 20),
                        _buildAnswerCircle("Kunci Jawaban", correctAnswer, const Color(0xFF1E88E5)),
                      ],
                    ),

                    const SizedBox(height: 25),

                    // Panel Pembahasan (Explanation)
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        color: const Color(0xFFF0F7FF), 
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: const Color(0xFFD0E8FF)),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Icon(Icons.auto_awesome, size: 16, color: Colors.blue.shade800),
                              const SizedBox(width: 8),
                              const Text("PENJELASAN:", 
                                style: TextStyle(fontWeight: FontWeight.w900, fontSize: 11, color: Color(0xFF0D47A1), letterSpacing: 1)),
                            ],
                          ),
                          const SizedBox(height: 12),
                          Text(
                            q['explanation'] != null && q['explanation'].toString().isNotEmpty
                                ? q['explanation']
                                : "Tidak ada penjelasan tertulis untuk soal ini.",
                            style: const TextStyle(fontSize: 13, height: 1.7, color: Color(0xFF0D47A1), fontWeight: FontWeight.w500),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              );
            },
          ),
      
      // Tombol Selesai yang mengambang di bawah
      bottomNavigationBar: Container(
        padding: const EdgeInsets.fromLTRB(20, 10, 20, 30),
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [
            BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, -5))
          ]
        ),
        child: ElevatedButton(
          onPressed: () => Navigator.of(context).popUntil((route) => route.isFirst),
          style: ElevatedButton.styleFrom(
            backgroundColor: primaryRed,
            minimumSize: const Size(double.infinity, 56),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
            elevation: 0
          ),
          child: const Text("KEMBALI KE BERANDA", 
            style: TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 14, letterSpacing: 1)),
        ),
      ),
    );
  }

  Widget _buildAnswerCircle(String label, String value, Color color) {
    return Expanded(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: const TextStyle(fontSize: 10, color: Colors.grey, fontWeight: FontWeight.w800, letterSpacing: 0.5)),
          const SizedBox(height: 8),
          Row(
            children: [
              Container(
                width: 34, height: 34,
                decoration: BoxDecoration(color: color, shape: BoxShape.circle),
                child: Center(child: Text(value, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14))),
              ),
              const SizedBox(width: 10),
              Text(value == "-" ? "Kosong" : "Opsi $value", 
                style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: color.withOpacity(0.8))),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState() {
    return const Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.find_in_page_outlined, size: 80, color: Colors.grey),
          SizedBox(height: 15),
          Text("Data pembahasan tidak ditemukan", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)),
        ],
      ),
    );
  }
}