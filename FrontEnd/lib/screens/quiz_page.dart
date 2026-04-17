import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'dart:convert';
import 'package:url_launcher/url_launcher.dart';

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
  // Map menyimpan { question_id : jawaban_dipilih }
  Map<int, String> _myAnswers = {}; 
  final Color spektaRed = const Color(0xFF990000);

  void _nextSoal() {
    if (_currentIndex < widget.questions.length - 1) {
      setState(() => _currentIndex++);
    }
  }

  void _prevSoal() {
    if (_currentIndex > 0) {
      setState(() => _currentIndex--);
    }
  }

  // Fungsi mengunduh PDF Pembahasan
  Future<void> _downloadPDF(String resultId) async {
    final Uri url = Uri.parse("${AuthService.baseUrl}/tryout/download/$resultId?token=${widget.token}");
    if (!await launchUrl(url, mode: LaunchMode.externalApplication)) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Could not download file")));
    }
  }

  // Fungsi Kirim Jawaban (Finish)
  void _submitQuiz() async {
    // 1. Tampilkan Loading
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
      
      // 2. TUTUP LOADING SEGERA (Agar tidak macet)
      Navigator.pop(context); 

      if (resp.statusCode == 200) {
        final data = jsonDecode(resp.body);
        
        // 3. Tampilkan Dialog Hasil (Gunakan key 'result_id' sesuai Laravel baru)
        _showResultDialog(
          data['score'].toString(), 
          data['result_id'].toString(), 
          data['correct'].toString()
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(backgroundColor: Colors.red, content: Text("Failed to save score!"))
        );
      }
    } catch (e) {
      if (mounted) {
        Navigator.pop(context); // Tutup loading jika error koneksi
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("Connection Error! Check your server."))
        );
      }
    }
  }

  void _showResultDialog(String score, String resultId, String correct) {
    showDialog(
      context: context, 
      barrierDismissible: false, 
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)),
        title: const Text("Try Out Result 🎓", textAlign: TextAlign.center, style: TextStyle(fontWeight: FontWeight.bold)),
        content: Column(
          mainAxisSize: MainAxisSize.min, 
          children: [
            const Text("Your Final Score:"),
            Text(score, style: TextStyle(fontSize: 60, fontWeight: FontWeight.bold, color: spektaRed)),
            Text("Correct: $correct / ${widget.questions.length}", style: const TextStyle(color: Colors.green, fontWeight: FontWeight.bold)),
          ],
        ),
        actions: [
          ElevatedButton.icon(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.green, minimumSize: const Size(double.infinity, 45)),
            onPressed: () => _downloadPDF(resultId), 
            icon: const Icon(Icons.picture_as_pdf, color: Colors.white), 
            label: const Text("DOWNLOAD EXPLANATION", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold))
          ),
          const SizedBox(height: 10),
          TextButton(
            onPressed: () {
              Navigator.pop(context); // Tutup dialog
              Navigator.pop(context); // Kembali ke halaman kelas
            }, 
            child: const Text("BACK TO MENU")
          ),
        ],
      )
    );
  }

  @override
  Widget build(BuildContext context) {
    var q = widget.questions[_currentIndex];
    
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: Text("Question ${_currentIndex + 1} / ${widget.questions.length}"), 
        backgroundColor: spektaRed, 
        foregroundColor: Colors.white
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
                    padding: const EdgeInsets.all(20), 
                    decoration: BoxDecoration(color: Colors.grey[100], borderRadius: BorderRadius.circular(15)),
                    child: Text(q['question'], style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w600))
                  ),
                  const SizedBox(height: 30),
                  // MODIFIKASI: Gunakan 'question_id' (English) sesuai pgAdmin
                  _buildOption("A", q['option_a'], q['question_id']),
                  _buildOption("B", q['option_b'], q['question_id']),
                  _buildOption("C", q['option_c'], q['question_id']),
                  _buildOption("D", q['option_d'], q['question_id']),
                ]
              )
            )
          ),
          
          // Tombol Navigasi Bawah
          Container(
            padding: const EdgeInsets.all(20), 
            decoration: const BoxDecoration(color: Colors.white, boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10)]),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween, 
              children: [
                if (_currentIndex > 0) 
                  OutlinedButton(onPressed: _prevSoal, child: const Text("BACK")),
                
                const SizedBox(width: 10),

                Expanded(
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: _currentIndex == widget.questions.length - 1 ? Colors.green : spektaRed,
                      minimumSize: const Size(0, 50)
                    ),
                    onPressed: () { 
                      if (_currentIndex == widget.questions.length - 1) 
                        _submitQuiz(); 
                      else 
                        _nextSoal(); 
                    },
                    child: Text(
                      _currentIndex == widget.questions.length - 1 ? "FINISH" : "NEXT", 
                      style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)
                    )
                  ),
                ),
              ]
            )
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
            Expanded(child: Text(text, style: const TextStyle(fontSize: 16))),
          ]
        )
      ),
    );
  }
}