import 'package:flutter/material.dart';

class PracticeQuizPage extends StatefulWidget {
  final List questions;
  final String token;

  const PracticeQuizPage({super.key, required this.questions, required this.token});

  @override
  State<PracticeQuizPage> createState() => _PracticeQuizPageState();
}

class _PracticeQuizPageState extends State<PracticeQuizPage> {
  int currentIndex = 0;
  String? selectedAnswer;
  bool isChecked = false;
  bool isQuizFinished = false;
  bool isCorrect = false;
  int correctScore = 0;
  String? hintText;
  String? explanationText;
  String? trueAnswerLocal;

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
      setState(() => isQuizFinished = true);
    }
  }

  void _checkAnswer() {
    if (selectedAnswer == null) return;
    setState(() {
      var q = widget.questions[currentIndex];
      trueAnswerLocal = (q['correct_answer'] ?? '').toString().toUpperCase().trim();
      isCorrect = (selectedAnswer == trueAnswerLocal);
      if (isCorrect) {
        correctScore++;
        explanationText = q['explanation'];
        hintText = null;
      } else {
        hintText = q['hint'];
        explanationText = null;
      }
      isChecked = true;
    });
  }

  Widget _buildSummaryScreen() {
    final double scorePercent = correctScore / widget.questions.length;

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
        title: const Text(
          "HASIL LATIHAN",
          style: TextStyle(
            fontWeight: FontWeight.w900,
            fontSize: 16,
            color: Colors.white,
          ),
        ),
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        centerTitle: true,
        automaticallyImplyLeading: false,
        elevation: 0,
      ),
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                padding: const EdgeInsets.all(28),
                decoration: BoxDecoration(
                  color: accentTeal.withOpacity(0.1),
                  shape: BoxShape.circle,
                  border: Border.all(color: accentTeal.withOpacity(0.3), width: 2),
                ),
                child: Icon(
                  Icons.emoji_events_rounded,
                  size: 72,
                  color: accentTeal,
                ),
              ),
              const SizedBox(height: 24),
              const Text(
                "Latihan Selesai!",
                style: TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.w900,
                  color: textDark,
                ),
              ),
              const SizedBox(height: 16),

              Container(
                width: double.infinity,
                padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 24),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: outlineVariant.withOpacity(0.4)),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.03),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Column(
                  children: [
                    Text(
                      "$correctScore",
                      style: const TextStyle(
                        fontSize: 64,
                        fontWeight: FontWeight.w900,
                        color: primaryRed,
                        height: 1,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      "dari ${widget.questions.length} soal benar",
                      style: const TextStyle(
                        fontSize: 14,
                        color: textDarkVariant,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 12),
                    ClipRRect(
                      borderRadius: BorderRadius.circular(99),
                      child: LinearProgressIndicator(
                        value: scorePercent,
                        minHeight: 8,
                        backgroundColor: outlineVariant.withOpacity(0.3),
                        color: scorePercent >= 0.7 ? darkTeal : accentTeal,
                      ),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      "${(scorePercent * 100).round()}% akurasi",
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: scorePercent >= 0.7 ? darkTeal : accentTeal,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 32),

              SizedBox(
                width: double.infinity,
                height: 52,
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
                  label: const Text(
                    "ULANGI LATIHAN",
                    style: TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.w900,
                      fontSize: 15,
                    ),
                  ),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: accentTeal,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                    elevation: 0,
                  ),
                ),
              ),
              const SizedBox(height: 12),

              SizedBox(
                width: double.infinity,
                height: 52,
                child: OutlinedButton.icon(
                  onPressed: () => Navigator.pop(context),
                  icon: const Icon(Icons.arrow_back_rounded, color: accentTeal),
                  label: const Text(
                    "KEMBALI KE DAFTAR MINGGU",
                    style: TextStyle(
                      color: accentTeal,
                      fontWeight: FontWeight.w900,
                      fontSize: 14,
                    ),
                  ),
                  style: OutlinedButton.styleFrom(
                    side: BorderSide(color: accentTeal, width: 1.5),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  ),
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
    if (isQuizFinished) return _buildSummaryScreen();

    var q = widget.questions[currentIndex];
    bool isLastQuestion = currentIndex == widget.questions.length - 1;

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
          "Question ${currentIndex + 1} / ${widget.questions.length}",
          style: const TextStyle(
            fontWeight: FontWeight.w900,
            fontSize: 16,
            color: Colors.white,
          ),
        ),
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: Column(
        children: [
          LinearProgressIndicator(
            value: (currentIndex + 1) / widget.questions.length,
            backgroundColor: outlineVariant.withOpacity(0.3),
            color: accentTeal,
            minHeight: 4,
          ),

          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.fromLTRB(16, 20, 16, 20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
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
                      q['question'] ?? "Question not found",
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w700,
                        color: textDark,
                        height: 1.5,
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),

                  _option("A", q['option_a'] ?? ""),
                  _option("B", q['option_b'] ?? ""),
                  _option("C", q['option_c'] ?? ""),
                  _option("D", q['option_d'] ?? ""),

                  if (isChecked && !isCorrect && hintText != null) ...[
                    const SizedBox(height: 4),
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: const Color(0xFFFFF7ED),
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: Colors.orange.shade300),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            "JAWABAN SALAH",
                            style: TextStyle(
                              fontWeight: FontWeight.w900,
                              fontSize: 11,
                              color: Colors.orange,
                              letterSpacing: 0.5,
                            ),
                          ),
                          const SizedBox(height: 6),
                          Text(
                            "Kata Kunci: $hintText",
                            style: const TextStyle(
                              fontSize: 13,
                              fontStyle: FontStyle.italic,
                              color: textDark,
                              height: 1.4,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],

                  if (isChecked && isCorrect && explanationText != null) ...[
                    const SizedBox(height: 4),
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: const Color(0xFFE2F9FC),
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: darkTeal.withOpacity(0.3)),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            "JAWABAN BENAR!",
                            style: TextStyle(
                              fontWeight: FontWeight.w900,
                              fontSize: 11,
                              color: darkTeal,
                              letterSpacing: 0.5,
                            ),
                          ),
                          const SizedBox(height: 6),
                          Text(
                            "Penjelasan:\n$explanationText",
                            style: const TextStyle(
                              fontSize: 13,
                              color: textDark,
                              height: 1.4,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ],
              ),
            ),
          ),

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
            child: SizedBox(
              width: double.infinity,
              height: 52,
              child: ElevatedButton(
                onPressed: isChecked
                    ? _next
                    : (selectedAnswer != null ? _checkAnswer : null),
                style: ElevatedButton.styleFrom(
                  backgroundColor: isChecked
                      ? (isLastQuestion ? darkTeal : accentTeal)
                      : accentTeal,
                  disabledBackgroundColor: outlineVariant.withOpacity(0.4),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(14),
                  ),
                  elevation: 0,
                ),
                child: Text(
                  isChecked
                      ? (isLastQuestion ? "FINISH" : "NEXT QUESTION")
                      : "CHECK ANSWER",
                  style: const TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.w900,
                    fontSize: 15,
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _option(String code, String text) {
    bool isSelected = selectedAnswer == code;
    bool isTrue = isChecked && trueAnswerLocal == code;
    bool isWrong = isChecked && isSelected && !isCorrect;

    Color borderColor = outlineVariant.withOpacity(0.5);
    Color bgColor = Colors.white;
    Color codeBoxColor = lightBlueBg;
    Color codeTextColor = textDark;

    if (isChecked) {
      if (isTrue) {
        borderColor = darkTeal;
        bgColor = const Color(0xFFE2F9FC);
        codeBoxColor = darkTeal;
        codeTextColor = Colors.white;
      } else if (isWrong) {
        borderColor = primaryRed;
        bgColor = const Color(0xFFFFF1F1);
        codeBoxColor = primaryRed;
        codeTextColor = Colors.white;
      }
    } else if (isSelected) {
      borderColor = accentTeal;
      bgColor = accentTeal.withOpacity(0.06);
      codeBoxColor = accentTeal;
      codeTextColor = Colors.white;
    }

    return GestureDetector(
      onTap: isChecked ? null : () => setState(() => selectedAnswer = code),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: bgColor,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: borderColor, width: isSelected || isTrue || isWrong ? 2 : 1),
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
                color: codeBoxColor,
                borderRadius: BorderRadius.circular(10),
              ),
              child: Center(
                child: Text(
                  code,
                  style: TextStyle(
                    color: codeTextColor,
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
                  color: textDark,
                  fontWeight: isSelected || isTrue ? FontWeight.w700 : FontWeight.normal,
                ),
              ),
            ),
            if (isTrue)
              const Icon(Icons.check_circle_rounded, color: darkTeal, size: 20),
            if (isWrong)
              const Icon(Icons.cancel_rounded, color: primaryRed, size: 20),
          ],
        ),
      ),
    );
  }
}