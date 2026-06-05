import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'dart:convert';
import 'explanation_page.dart'; 

class QuizPage extends StatefulWidget {
  final List questions;
  final int tryoutId;
  final String token;

  const QuizPage({
    super.key, 
    required this.questions, 
    required this.tryoutId, 
    required this.token
  });

  @override
  State<QuizPage> createState() => _QuizPageState();
}

class _QuizPageState extends State<QuizPage> {
  int _currentIndex = 0;
  // Map untuk menyimpan jawaban user: {question_id: "A"}
  Map<int, String> _myAnswers = {}; 
  final Color spektaRed = const Color(0xFF990000);

  void _submitQuiz() async {
    showDialog(
      context: context, 
      barrierDismissible: false, 
      builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000)))
    );

    try {
      var resp = await AuthService.submitTryout(
        tryoutId: widget.tryoutId, 
        answers: _myAnswers, 
        token: widget.token
      );

      if (!mounted) return;
      Navigator.pop(context); // Tutup loading

      if (resp.statusCode == 200) {
        final data = jsonDecode(resp.body);
        _showResultDialog(
          (data['score'] ?? "0").toString(), 
          (data['correct'] ?? "0").toString()
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Gagal menyimpan nilai ke server!")));
      }
    } catch (e) {
      if (mounted) Navigator.pop(context);
      debugPrint("❌ Submit Error: $e");
    }
  }

  void _showResultDialog(String score, String correct) {
    showDialog(
      context: context, 
      barrierDismissible: false, 
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)),
        title: const Text("Hasil Try Out 🎓", textAlign: TextAlign.center, style: TextStyle(fontWeight: FontWeight.bold)),
        content: Column(
          mainAxisSize: MainAxisSize.min, 
          children: [
            const Text("Skor Akhir Anda:"),
            Text(score, style: TextStyle(fontSize: 60, fontWeight: FontWeight.bold, color: spektaRed)),
            Text("Benar: $correct / ${widget.questions.length}", 
              style: const TextStyle(color: Colors.green, fontWeight: FontWeight.bold)),
          ],
        ),
        actions: [
          ElevatedButton.icon(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.green, minimumSize: const Size(double.infinity, 45)),
            onPressed: () {
              // ✨ MODIFIKASI: Penyesuaian Key ID untuk sinkronisasi ExplanationPage
              for (var i = 0; i < widget.questions.length; i++) {
                // Mendukung key question_id atau practice_question_id dari Go
                var qData = widget.questions[i];
                int qId = int.parse((qData['question_id'] ?? qData['practice_question_id'] ?? 0).toString());
                
                String userChoice = _myAnswers[qId] ?? "-";
                widget.questions[i]['user_answer'] = userChoice;
              }

              Navigator.pop(context); // Tutup dialog
              Navigator.push(context, MaterialPageRoute(
                builder: (context) => ExplanationPage(questions: widget.questions)
              ));
            }, 
            icon: const Icon(Icons.menu_book_rounded, color: Colors.white), 
            label: const Text("LIHAT PENJELASAN", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold))
          ),
          TextButton(
            onPressed: () { 
              Navigator.pop(context); // Tutup dialog
              Navigator.pop(context); // Kembali ke menu sebelumnya
            }, 
            child: const Text("KEMBALI KE MENU")
          ),
        ],
      )
    );
  }

  @override
  Widget build(BuildContext context) {
    var q = widget.questions[_currentIndex];
    // Ambil ID secara dinamis
    int currentQId = int.parse((q['question_id'] ?? q['practice_question_id'] ?? 0).toString());

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: Text("Soal ${_currentIndex + 1} / ${widget.questions.length}"), 
        backgroundColor: spektaRed, 
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: Column(
        children: [
          LinearProgressIndicator(
            value: (_currentIndex + 1) / widget.questions.length, 
            backgroundColor: Colors.red[50], 
            color: spektaRed
          ),
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(25), 
              child: Column(
                children: [
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20), 
                    decoration: BoxDecoration(color: Colors.grey[100], borderRadius: BorderRadius.circular(15)),
                    child: Text(q['question'] ?? "", style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w600))
                  ),
                  const SizedBox(height: 30),

                  _buildOption("A", q['option_a'] ?? "", currentQId),
                  _buildOption("B", q['option_b'] ?? "", currentQId),
                  _buildOption("C", q['option_c'] ?? "", currentQId),
                  _buildOption("D", q['option_d'] ?? "", currentQId),
                ]
              )
            )
          ),
          
          Container(
            padding: const EdgeInsets.all(20), 
            decoration: const BoxDecoration(
              color: Colors.white,
              boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10, offset: Offset(0, -2))]
            ),
            child: Row(
              children: [
                if (_currentIndex > 0) 
                  IconButton(
                    onPressed: () => setState(() => _currentIndex--), 
                    icon: const Icon(Icons.arrow_back_ios)
                  ),
                const SizedBox(width: 10),
                Expanded(
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: _currentIndex == widget.questions.length - 1 ? Colors.green : spektaRed,
                      padding: const EdgeInsets.symmetric(vertical: 15),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))
                    ),
                    onPressed: () => _currentIndex == widget.questions.length - 1 
                      ? _submitQuiz() 
                      : setState(() => _currentIndex++),
                    child: Text(
                      _currentIndex == widget.questions.length - 1 ? "SELESAI" : "BERIKUTNYA", 
                      style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)
                    ),
                  ),
                ),
              ],
            ),
          )
        ],
      ),
    );
  }

  Widget _buildOption(String code, String text, int qId) {
    bool isSelected = _myAnswers[qId] == code;
    return GestureDetector(
      onTap: () => setState(() => _myAnswers[qId] = code),
      child: Container(
        margin: const EdgeInsets.only(bottom: 15), 
        padding: const EdgeInsets.all(15),
        decoration: BoxDecoration(
          color: isSelected ? spektaRed.withOpacity(0.1) : Colors.white, 
          borderRadius: BorderRadius.circular(15), 
          border: Border.all(color: isSelected ? spektaRed : Colors.grey[300]!, width: 2)
        ),
        child: Row(
          children: [
            CircleAvatar(
              backgroundColor: isSelected ? spektaRed : Colors.grey[200], 
              child: Text(code, style: TextStyle(color: isSelected ? Colors.white : Colors.black))
            ),
            const SizedBox(width: 15), 
            Expanded(child: Text(text)),
          ]
        )
      ),
    );
  }
}