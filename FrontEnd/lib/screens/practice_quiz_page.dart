import 'package:flutter/material.dart';

class PracticeQuizPage extends StatefulWidget {
  final List questions;
  final String token;
  // userId dihapus karena sudah tidak dipakai untuk API

  const PracticeQuizPage({super.key, required this.questions, required this.token});

  @override
  State<PracticeQuizPage> createState() => _PracticeQuizPageState();
}

class _PracticeQuizPageState extends State<PracticeQuizPage> {
  int currentIndex = 0;
  String? selectedAnswer;
  
  bool isChecked = false;
  bool isQuizFinished = false; 

  // Variabel Skor & UI
  bool isCorrect = false;
  int correctScore = 0; 
  String? hintText;
  String? explanationText;
  String? trueAnswerLocal;

  // Fungsi untuk Lanjut ke Soal Berikutnya / Finish
  void _next() {
    if (currentIndex < widget.questions.length - 1) {
      setState(() { 
        currentIndex++; 
        selectedAnswer = null; 
        isChecked = false; 
        hintText = null;
        explanationText = null;
        trueAnswerLocal = null;
      });
    } else {
      // Jika ini soal terakhir, tampilkan halaman skor
      setState(() { 
        isQuizFinished = true; 
      });
    }
  }

  // Fungsi Cek Jawaban Lokal (Sangat Cepat, Tanpa Loading/API)
  void _checkAnswer() {
    if (selectedAnswer == null) return;
    
    setState(() {
      var q = widget.questions[currentIndex];
      
      // Ambil kunci jawaban asli dari data soal
      trueAnswerLocal = (q['correct_answer'] ?? '').toString().toUpperCase().trim();
      
      // Cocokkan jawaban user
      isCorrect = (selectedAnswer == trueAnswerLocal);

      if (isCorrect) {
        correctScore++; // Tambah skor jika benar
        explanationText = q['explanation']; // Benar = Tampilkan Pembahasan
        hintText = null;
      } else {
        hintText = q['hint']; // Salah = Tampilkan Hint
        explanationText = null; 
      }
      
      // Langsung kunci soal
      isChecked = true; 
    });
  }

  // WIDGET Halaman Skor Akhir
  Widget _buildSummaryScreen() {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text("HASIL LATIHAN", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Colors.white)),
        backgroundColor: const Color(0xFF990000),
        foregroundColor: Colors.white,
        centerTitle: true,
        automaticallyImplyLeading: false, 
      ),
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(30.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.emoji_events_rounded, size: 120, color: Colors.amber.shade400),
              const SizedBox(height: 20),
              const Text("Latihan Selesai!", style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
              const SizedBox(height: 10),
              Text(
                "Skor Kamu:\n$correctScore dari ${widget.questions.length} Benar", 
                textAlign: TextAlign.center,
                style: TextStyle(fontSize: 18, color: Colors.grey.shade700, height: 1.5)
              ),
              const SizedBox(height: 50),
              
              // Tombol Ulangi
              SizedBox(
                width: double.infinity, height: 55,
                child: ElevatedButton.icon(
                  onPressed: () {
                    setState(() {
                      currentIndex = 0;
                      correctScore = 0;
                      isQuizFinished = false;
                      selectedAnswer = null;
                      isChecked = false;
                      hintText = null;
                      explanationText = null;
                      trueAnswerLocal = null;
                    });
                  },
                  icon: const Icon(Icons.refresh_rounded, color: Colors.white),
                  label: const Text("ULANGI LATIHAN", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                  style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF990000), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
                ),
              ),
              const SizedBox(height: 15),
              
              // Tombol Kembali
              SizedBox(
                width: double.infinity, height: 55,
                child: OutlinedButton.icon(
                  onPressed: () => Navigator.pop(context),
                  icon: const Icon(Icons.arrow_back_rounded, color: Color(0xFF990000)),
                  label: const Text("KEMBALI KE DAFTAR MINGGU", style: TextStyle(color: Color(0xFF990000), fontWeight: FontWeight.bold, fontSize: 16)),
                  style: OutlinedButton.styleFrom(side: const BorderSide(color: Color(0xFF990000), width: 2), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    if (isQuizFinished) {
      return _buildSummaryScreen();
    }

    var q = widget.questions[currentIndex];
    bool isLastQuestion = currentIndex == widget.questions.length - 1;

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(title: Text("Question ${currentIndex + 1} / ${widget.questions.length}", style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Colors.white)), backgroundColor: const Color(0xFF990000), foregroundColor: Colors.white, elevation: 0),
      body: Padding(
        padding: const EdgeInsets.all(25),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(q['question'] ?? "Question not found", style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.black87)),
            const SizedBox(height: 30),
            
            _option("A", q['option_a'] ?? ""),
            _option("B", q['option_b'] ?? ""),
            _option("C", q['option_c'] ?? ""),
            _option("D", q['option_d'] ?? ""),
            
            const Spacer(),

            // Tampilkan KATA KUNCI (HANYA JIKA SALAH)
            if (isChecked && !isCorrect && hintText != null)
              Container(width: double.infinity, padding: const EdgeInsets.all(15), margin: const EdgeInsets.only(bottom: 10), decoration: BoxDecoration(color: Colors.orange.shade50, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.orange)),
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    const Text("JAWABAN SALAH", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 12, color: Colors.orange)),
                    const SizedBox(height: 5),
                    Text("Kata Kunci: $hintText", style: const TextStyle(fontSize: 12, fontStyle: FontStyle.italic, color: Colors.black87)),
                  ])),

            // Tampilkan PEMBAHASAN (HANYA JIKA BENAR)
            if (isChecked && isCorrect && explanationText != null)
              Container(width: double.infinity, padding: const EdgeInsets.all(20), decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.green.shade200)),
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    const Text("JAWABAN BENAR!", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 12, color: Colors.green)),
                    const SizedBox(height: 8),
                    Text("Penjelasan:\n$explanationText", style: const TextStyle(fontSize: 12, color: Colors.black87)),
                  ])),

            const SizedBox(height: 20),
            
            SizedBox(width: double.infinity, height: 55, child: ElevatedButton(
                onPressed: isChecked ? _next : (selectedAnswer != null ? _checkAnswer : null),
                style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF990000), disabledBackgroundColor: Colors.grey.shade300, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)), elevation: 0),
                child: Text(isChecked 
                        ? (isLastQuestion ? "FINISH" : "NEXT QUESTION") 
                        : "CHECK ANSWER", 
                        style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              )),
          ],
        ),
      ),
    );
  }

  Widget _option(String code, String text) {
    bool isSelected = selectedAnswer == code;
    Color borderCol = Colors.grey.shade200;
    
    if (isChecked) {
      if (trueAnswerLocal == code || (isCorrect && isSelected)) {
        borderCol = Colors.green; 
      } else if (isSelected && !isCorrect) {
        borderCol = Colors.red; 
      }
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
            if (isChecked && (trueAnswerLocal == code || (isCorrect && isSelected))) 
              const Icon(Icons.check_circle, color: Colors.green, size: 20),
            if (isChecked && isSelected && !isCorrect) 
              const Icon(Icons.cancel, color: Colors.red, size: 20),
          ])),
    );
  }
}