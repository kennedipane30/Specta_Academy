import 'package:flutter/material.dart';

class PracticeQuizPage extends StatefulWidget {
  final String subject;
  final int week;
  final List questions;

  const PracticeQuizPage({super.key, required this.subject, required this.week, required this.questions});

  @override
  State<PracticeQuizPage> createState() => _PracticeQuizPageState();
}

class _PracticeQuizPageState extends State<PracticeQuizPage> {
  int currentIndex = 0;
  String? selectedAnswer;
  bool isChecked = false;

  void _nextQuestion() {
    if (currentIndex < widget.questions.length - 1) {
      setState(() {
        currentIndex++;
        selectedAnswer = null;
        isChecked = false;
      });
    } else {
      Navigator.pop(context); // Selesai, kembali ke daftar minggu
    }
  }

  @override
  Widget build(BuildContext context) {
    var q = widget.questions[currentIndex];

    return Scaffold(
      appBar: AppBar(
        title: Text("Soal ${currentIndex + 1} / ${widget.questions.length}"),
        backgroundColor: const Color(0xFF990000),
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: const EdgeInsets.all(25),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(q['pertanyaan'], style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            const SizedBox(height: 30),
            _buildOption("A", q['opsi_a'], q['jawaban_benar']),
            _buildOption("B", q['opsi_b'], q['jawaban_benar']),
            _buildOption("C", q['opsi_c'], q['jawaban_benar']),
            _buildOption("D", q['opsi_d'], q['jawaban_benar']),
            const Spacer(),
            if (isChecked) 
              Container(
                padding: const EdgeInsets.all(15),
                decoration: BoxDecoration(color: Colors.blue.shade50, borderRadius: BorderRadius.circular(15)),
                child: Text("Pembahasan: ${q['pembahasan'] ?? 'Tidak ada pembahasan'}", style: const TextStyle(fontSize: 12, fontStyle: FontStyle.italic)),
              ),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              height: 55,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF990000), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
                onPressed: isChecked ? _nextQuestion : () => setState(() => isChecked = true),
                child: Text(isChecked ? "SOAL BERIKUTNYA" : "CEK JAWABAN", style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              ),
            )
          ],
        ),
      ),
    );
  }

  Widget _buildOption(String code, String text, String correct) {
    bool isCorrect = code == correct;
    bool isSelected = selectedAnswer == code;

    Color borderCol = Colors.grey.shade200;
    if (isChecked) {
      if (isCorrect) borderCol = Colors.green;
      else if (isSelected) borderCol = Colors.red;
    } else if (isSelected) {
      borderCol = const Color(0xFF990000);
    }

    return GestureDetector(
      onTap: isChecked ? null : () => setState(() => selectedAnswer = code),
      child: Container(
        margin: const EdgeInsets.only(bottom: 15),
        padding: const EdgeInsets.all(15),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(15),
          border: Border.all(color: borderCol, width: 2),
          color: isSelected ? borderCol.withOpacity(0.05) : Colors.white,
        ),
        child: Row(
          children: [
            Text("$code.", style: const TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(width: 10),
            Expanded(child: Text(text)),
            if (isChecked && isCorrect) const Icon(Icons.check_circle, color: Colors.green),
            if (isChecked && isSelected && !isCorrect) const Icon(Icons.cancel, color: Colors.red),
          ],
        ),
      ),
    );
  }
}