import 'package:flutter/material.dart';
import 'practice_week_list_page.dart';

class PracticeSubjectListPage extends StatelessWidget {
  final List allExercises;
  final String token;

  const PracticeSubjectListPage({
    super.key,
    required this.allExercises,
    required this.token,
  });

  // ============================================================
  // 🎨 PALET WARNA SPEKTA (KONSISTEN DENGAN HOMEPAGE)
  // ============================================================
  static const Color primaryRed   = Color(0xFFC5352C);
  static const Color accentTeal   = Color(0xFF2EA8AB);
  static const Color lightBlueBg  = Color(0xFFEFF4FF);
  static const Color pageBg       = Color(0xFFF1F5F9);
  static const Color textDark     = Color(0xFF0F172A);
  static const Color textDarkVariant = Color(0xFF334155);
  static const Color outlineVariant  = Color(0xFFE2BEBA);

  @override
  Widget build(BuildContext context) {
    final subjects = allExercises
        .map((e) {
          var name = e['subject'] ?? e['Subject'] ?? e['subject_name'] ?? '';
          return name.toString().trim();
        })
        .where((name) => name.isNotEmpty)
        .toSet()
        .toList();

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
          "Pilih Subjek Latihan",
          style: TextStyle(
            fontWeight: FontWeight.w900,
            color: Colors.white,
            fontSize: 18,
            letterSpacing: -0.3,
          ),
        ),
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        centerTitle: true,
        elevation: 0,
      ),
      body: subjects.isEmpty
          ? Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Container(
                    padding: const EdgeInsets.all(24),
                    decoration: const BoxDecoration(
                      color: lightBlueBg,
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(
                      Icons.quiz_outlined,
                      size: 52,
                      color: primaryRed,
                    ),
                  ),
                  const SizedBox(height: 16),
                  const Text(
                    "Belum ada latihan tersedia.",
                    style: TextStyle(
                      color: textDarkVariant,
                      fontSize: 14,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ],
              ),
            )
          : ListView.builder(
              padding: const EdgeInsets.fromLTRB(16, 20, 16, 32),
              itemCount: subjects.length,
              itemBuilder: (context, index) {
                final String sName = subjects[index].toString();

                return Container(
                  margin: const EdgeInsets.only(bottom: 12),
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
                  child: InkWell(
                    borderRadius: BorderRadius.circular(20),
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => PracticeWeekListPage(
                            subjectName: sName,
                            allExercises: allExercises,
                            token: token,
                          ),
                        ),
                      );
                    },
                    child: Padding(
                      padding: const EdgeInsets.all(14),
                      child: Row(
                        children: [
                          Container(
                            width: 48,
                            height: 48,
                            decoration: BoxDecoration(
                              color: lightBlueBg,
                              borderRadius: BorderRadius.circular(14),
                              border: Border.all(
                                color: outlineVariant.withOpacity(0.3),
                              ),
                            ),
                            child: const Icon(
                              Icons.quiz_rounded,
                              color: primaryRed,
                              size: 22,
                            ),
                          ),
                          const SizedBox(width: 14),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  sName,
                                  style: const TextStyle(
                                    fontWeight: FontWeight.w900,
                                    fontSize: 15,
                                    color: textDark,
                                  ),
                                ),
                                const SizedBox(height: 3),
                                const Text(
                                  "Lihat tantangan mingguan",
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: textDarkVariant,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          const Icon(
                            Icons.arrow_forward_ios_rounded,
                            size: 14,
                            color: primaryRed,
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