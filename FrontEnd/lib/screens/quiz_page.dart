import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'dart:convert';
import 'explanation_page.dart';

class QuizPage extends StatefulWidget {
  final List questions;
  final int tryoutId;
  final String token;
  final int userId;

  const QuizPage({
    super.key,
    required this.questions,
    required this.tryoutId,
    required this.token,
    required this.userId,
  });

  @override
  State<QuizPage> createState() => _QuizPageState();
}

class _QuizPageState extends State<QuizPage> {
  int _currentIndex = 0;
  Map<int, String> _myAnswers = {};

  // ============================================================
  // 🎨 PALET WARNA SPEKTA (KONSISTEN DENGAN HOMEPAGE)
  // ============================================================
  static const Color primaryRed      = Color(0xFFC5352C);
  static const Color accentTeal      = Color(0xFF2EA8AB);
  static const Color darkTeal        = Color(0xFF00696C);
  static const Color lightBlueBg     = Color(0xFFEFF4FF);
  static const Color pageBg          = Color(0xFFF1F5F9);
  static const Color textDark        = Color(0xFF0F172A);
  static const Color textDarkVariant = Color(0xFF334155);
  static const Color neutralGray     = Color(0xFF64748B);
  static const Color outlineVariant  = Color(0xFFE2BEBA);

  void _submitQuiz() async {
    if (widget.userId == 0) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: const Text("Akses Ditolak: ID User tidak valid. Silakan relogin."),
        backgroundColor: primaryRed,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      ));
      return;
    }

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) => const Center(child: CircularProgressIndicator(color: accentTeal)),
    );

    try {
      var resp = await AuthService.submitTryout(
        tryoutId: widget.tryoutId,
        userId: widget.userId,
        answers: _myAnswers,
        token: widget.token,
      );

      if (!mounted) return;
      Navigator.pop(context);

      if (resp.statusCode == 200) {
        final data = jsonDecode(resp.body);
        _showResultDialog(
          (data['score'] ?? "0").toString(),
          (data['correct'] ?? "0").toString(),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          content: const Text("Gagal menyimpan nilai ke server!"),
          backgroundColor: primaryRed,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        ));
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
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        backgroundColor: Colors.white,
        title: const Text(
          "Hasil Try Out 🎓",
          textAlign: TextAlign.center,
          style: TextStyle(fontWeight: FontWeight.w900, color: textDark),
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Text(
              "Skor Akhir Anda:",
              style: TextStyle(color: textDarkVariant, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 8),
            Text(
              score,
              style: const TextStyle(
                fontSize: 64,
                fontWeight: FontWeight.w900,
                color: primaryRed,
              ),
            ),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
              decoration: BoxDecoration(
                color: const Color(0xFFE2F9FC),
                borderRadius: BorderRadius.circular(99),
              ),
              child: Text(
                "Benar: $correct / ${widget.questions.length}",
                style: const TextStyle(
                  color: darkTeal,
                  fontWeight: FontWeight.w900,
                  fontSize: 13,
                ),
              ),
            ),
          ],
        ),
        actions: [
          ElevatedButton.icon(
            style: ElevatedButton.styleFrom(
              backgroundColor: darkTeal,
              minimumSize: const Size(double.infinity, 48),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              elevation: 0,
            ),
            onPressed: () {
              for (var i = 0; i < widget.questions.length; i++) {
                var qData = widget.questions[i];
                int qId = int.parse((qData['question_id'] ?? qData['QuestionID'] ?? qData['id'] ?? qData['ID'] ?? 0).toString());
                String userChoice = _myAnswers[qId] ?? "-";
                widget.questions[i]['user_answer'] = userChoice;
              }
              Navigator.pop(context);
              Navigator.pushReplacement(
                context,
                MaterialPageRoute(
                  builder: (context) => ExplanationPage(questions: widget.questions),
                ),
              );
            },
            icon: const Icon(Icons.menu_book_rounded, color: Colors.white),
            label: const Text(
              "LIHAT PENJELASAN",
              style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
            ),
          ),
          const SizedBox(height: 8),
          TextButton(
            onPressed: () {
              Navigator.pop(context);
              Navigator.pop(context, true);
            },
            child: const Text(
              "KEMBALI KE MENU",
              style: TextStyle(color: primaryRed, fontWeight: FontWeight.bold),
            ),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    var q = widget.questions[_currentIndex];
    int currentQId = int.parse(
      (q['question_id'] ?? q['QuestionID'] ?? q['id'] ?? q['ID'] ?? 0).toString(),
    );
    bool isLast = _currentIndex == widget.questions.length - 1;

    return Scaffold(
      backgroundColor: pageBg,
      appBar: AppBar(
        flexibleSpace: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [primaryRed, accentTeal],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
        ),
        title: Text(
          "Soal ${_currentIndex + 1} / ${widget.questions.length}",
          style: const TextStyle(
            fontWeight: FontWeight.w900,
            color: Colors.white,
            fontSize: 16,
          ),
        ),
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: Column(
        children: [
          // Progress bar
          LinearProgressIndicator(
            value: (_currentIndex + 1) / widget.questions.length,
            backgroundColor: outlineVariant.withOpacity(0.3),
            color: accentTeal,
            minHeight: 4,
          ),

          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.fromLTRB(16, 20, 16, 20),
              child: Column(
                children: [
                  // Kotak soal
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: outlineVariant.withOpacity(0.4)),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.03),
                          blurRadius: 10,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: Text(
                      q['question'] ?? q['Question'] ?? "",
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w700,
                        color: textDark,
                        height: 1.5,
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                  _buildOption("A", q['option_a'] ?? q['OptionA'] ?? "", currentQId),
                  _buildOption("B", q['option_b'] ?? q['OptionB'] ?? "", currentQId),
                  _buildOption("C", q['option_c'] ?? q['OptionC'] ?? "", currentQId),
                  _buildOption("D", q['option_d'] ?? q['OptionD'] ?? "", currentQId),
                  if ((q['option_e'] ?? q['OptionE']) != null &&
                      (q['option_e'] ?? q['OptionE']).toString().trim().isNotEmpty)
                    _buildOption("E", q['option_e'] ?? q['OptionE'] ?? "", currentQId),
                ],
              ),
            ),
          ),

          // Bottom nav
          Container(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 24),
            decoration: BoxDecoration(
              color: Colors.white,
              border: Border(top: BorderSide(color: outlineVariant.withOpacity(0.4))),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.04),
                  blurRadius: 10,
                  offset: const Offset(0, -4),
                ),
              ],
            ),
            child: Row(
              children: [
                if (_currentIndex > 0)
                  Container(
                    margin: const EdgeInsets.only(right: 12),
                    decoration: BoxDecoration(
                      color: lightBlueBg,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: outlineVariant.withOpacity(0.4)),
                    ),
                    child: IconButton(
                      onPressed: () => setState(() => _currentIndex--),
                      icon: const Icon(Icons.arrow_back_ios_rounded, color: accentTeal, size: 18),
                    ),
                  ),
                Expanded(
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: isLast ? darkTeal : accentTeal,
                      padding: const EdgeInsets.symmetric(vertical: 15),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                      elevation: 0,
                    ),
                    onPressed: () => isLast
                        ? _submitQuiz()
                        : setState(() => _currentIndex++),
                    child: Text(
                      isLast ? "SELESAI" : "BERIKUTNYA",
                      style: const TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.w900,
                        fontSize: 14,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildOption(String code, String text, int qId) {
    bool isSelected = _myAnswers[qId] == code;
    return GestureDetector(
      onTap: () => setState(() => _myAnswers[qId] = code),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: isSelected ? accentTeal.withOpacity(0.07) : Colors.white,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
            color: isSelected ? accentTeal : outlineVariant.withOpacity(0.5),
            width: isSelected ? 2 : 1,
          ),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.02),
              blurRadius: 6,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Row(
          children: [
            Container(
              width: 36,
              height: 36,
              decoration: BoxDecoration(
                color: isSelected ? accentTeal : lightBlueBg,
                borderRadius: BorderRadius.circular(10),
              ),
              child: Center(
                child: Text(
                  code,
                  style: TextStyle(
                    color: isSelected ? Colors.white : textDark,
                    fontWeight: FontWeight.w900,
                    fontSize: 13,
                  ),
                ),
              ),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Text(
                text,
                style: TextStyle(
                  fontSize: 14,
                  color: isSelected ? textDark : textDarkVariant,
                  fontWeight: isSelected ? FontWeight.w700 : FontWeight.normal,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}