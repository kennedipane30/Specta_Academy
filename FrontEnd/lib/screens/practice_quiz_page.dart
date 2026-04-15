import 'package:flutter/material.dart';

class PracticeQuizPage extends StatefulWidget {
  final List questions;
  const PracticeQuizPage({super.key, required this.questions});

  @override
  State<PracticeQuizPage> createState() => _PracticeQuizPageState();
}

class _PracticeQuizPageState extends State<PracticeQuizPage> {
  int currentIndex = 0;
  String? selectedAnswer;
  bool isChecked = false;

  void _next() {
    if (currentIndex < widget.questions.length - 1) {
      setState(() { currentIndex++; selectedAnswer = null; isChecked = false; });
    } else {
      Navigator.pop(context);
    }
  }

  @override
  Widget build(BuildContext context) {
    var q = widget.questions[currentIndex];
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(title: Text("Question ${currentIndex + 1} / ${widget.questions.length}", style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Colors.white)), backgroundColor: const Color(0xFF990000), foregroundColor: Colors.white, elevation: 0),
      body: Padding(
        padding: const EdgeInsets.all(25),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // MODIFIKASI: pertanyaan -> question
            Text(q['question'] ?? "Question not found", style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.black87)),
            const SizedBox(height: 30),
            // MODIFIKASI: opsi_x -> option_x, jawaban_benar -> correct_answer
            _option("A", q['option_a'] ?? "", q['correct_answer'] ?? ""),
            _option("B", q['option_b'] ?? "", q['correct_answer'] ?? ""),
            _option("C", q['option_c'] ?? "", q['correct_answer'] ?? ""),
            _option("D", q['option_d'] ?? "", q['correct_answer'] ?? ""),
            const Spacer(),
            // MODIFIKASI: pembahasan -> explanation
            if (isChecked)
              Container(width: double.infinity, padding: const EdgeInsets.all(20), decoration: BoxDecoration(color: Colors.blue.shade50, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.blue.shade100)),
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    const Text("EXPLANATION:", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 10, color: Colors.blue)),
                    const SizedBox(height: 8),
                    Text(q['explanation'] ?? "No explanation available for this question.", style: const TextStyle(fontSize: 12, fontStyle: FontStyle.italic, color: Colors.black54)),
                  ])),
            const SizedBox(height: 20),
            SizedBox(width: double.infinity, height: 55, child: ElevatedButton(
                onPressed: isChecked ? _next : (selectedAnswer != null ? () => setState(() => isChecked = true) : null),
                style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF990000), disabledBackgroundColor: Colors.grey.shade300, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)), elevation: 0),
                child: Text(isChecked ? "NEXT QUESTION" : "CHECK ANSWER", style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              )),
          ],
        ),
      ),
    );
  }

  Widget _option(String code, String text, String correct) {
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
      child: Container(margin: const EdgeInsets.only(bottom: 15), padding: const EdgeInsets.all(15), decoration: BoxDecoration(color: isSelected ? borderCol.withOpacity(0.05) : Colors.white, borderRadius: BorderRadius.circular(15), border: Border.all(color: borderCol, width: 2)),
        child: Row(children: [
            Text("$code.", style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
            const SizedBox(width: 12),
            Expanded(child: Text(text, style: const TextStyle(fontSize: 14, color: Colors.black87))),
            if (isChecked && isCorrect) const Icon(Icons.check_circle, color: Colors.green, size: 20),
            if (isChecked && isSelected && !isCorrect) const Icon(Icons.cancel, color: Colors.red, size: 20),
          ])),
    );
  }
}