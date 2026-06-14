import 'package:flutter/material.dart';
import 'practice_quiz_page.dart';

class PracticeWeekListPage extends StatelessWidget {
  final String subjectName;
  final List allExercises;
  final String token;

  const PracticeWeekListPage({
    super.key,
    required this.subjectName,
    required this.allExercises,
    required this.token,
  });

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

  @override
  Widget build(BuildContext context) {
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
          "${subjectName.toUpperCase()} PRACTICE",
          style: const TextStyle(
            fontWeight: FontWeight.w900,
            fontSize: 16,
            color: Colors.white,
            letterSpacing: -0.3,
          ),
        ),
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        centerTitle: true,
        elevation: 0,
      ),
      body: ListView.builder(
        padding: const EdgeInsets.fromLTRB(16, 20, 16, 32),
        itemCount: 20,
        itemBuilder: (context, index) {
          int weekNumber = index + 1;

          List weekSoals = allExercises.where((e) {
            final String dbSubject = (e['subject'] ?? e['Subject'] ?? e['subject_name'] ?? '')
                .toString().toLowerCase().trim();
            final String dbWeek = (e['week'] ?? e['Week'] ?? '').toString();
            return dbSubject == subjectName.toLowerCase().trim() &&
                dbWeek == weekNumber.toString();
          }).toList();

          bool isAvailable = weekSoals.isNotEmpty;

          return Container(
            margin: const EdgeInsets.only(bottom: 12),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(20),
              border: Border.all(
                color: isAvailable
                    ? darkTeal.withOpacity(0.25)
                    : outlineVariant.withOpacity(0.35),
              ),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.03),
                  blurRadius: 10,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: InkWell(
              borderRadius: BorderRadius.circular(20),
              onTap: isAvailable
                  ? () => Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (c) => PracticeQuizPage(
                            questions: weekSoals,
                            token: token,
                          ),
                        ),
                      )
                  : null,
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                child: Row(
                  children: [
                    // Nomor minggu
                    Container(
                      width: 48,
                      height: 48,
                      decoration: BoxDecoration(
                        color: isAvailable
                            ? const Color(0xFFE2F9FC)  // soft teal — sama seperti bento Materi
                            : const Color(0xFFF1F5F9),
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(
                          color: isAvailable
                              ? darkTeal.withOpacity(0.2)
                              : outlineVariant.withOpacity(0.3),
                        ),
                      ),
                      child: Center(
                        child: Text(
                          "$weekNumber",
                          style: TextStyle(
                            color: isAvailable ? darkTeal : neutralGray,
                            fontWeight: FontWeight.w900,
                            fontSize: 16,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 14),
                    // Label & subtitle
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            "Week $weekNumber",
                            style: TextStyle(
                              fontWeight: FontWeight.w900,
                              fontSize: 15,
                              color: isAvailable ? textDark : neutralGray,
                            ),
                          ),
                          const SizedBox(height: 3),
                          Text(
                            isAvailable
                                ? "${weekSoals.length} Soal Tersedia"
                                : "Belum tersedia",
                            style: TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                              color: isAvailable ? textDarkVariant : neutralGray,
                            ),
                          ),
                        ],
                      ),
                    ),
                    // Trailing arrow / lock
                    Icon(
                      isAvailable
                          ? Icons.arrow_forward_ios_rounded
                          : Icons.lock_outline_rounded,
                      size: 14,
                      color: isAvailable ? primaryRed : neutralGray,
                    ),
                  ],
                ),
              ),
            ),
          );
        },
      ),
    );
  }
}