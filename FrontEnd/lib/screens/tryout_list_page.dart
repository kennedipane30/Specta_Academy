import 'package:flutter/material.dart';
import 'tryout_detail_page.dart';

class TryoutListPage extends StatelessWidget {
  final List tryouts;
  final String token;
  final int userId;

  const TryoutListPage({
    super.key,
    required this.tryouts,
    required this.token,
    required this.userId,
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
        title: const Text(
          "Pilih Paket Tryout",
          style: TextStyle(
            fontWeight: FontWeight.w900,
            color: Colors.white,
            fontSize: 17,
          ),
        ),
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
      ),
      body: tryouts.isEmpty
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
                      Icons.assignment_late_outlined,
                      size: 52,
                      color: primaryRed,
                    ),
                  ),
                  const SizedBox(height: 16),
                  const Text(
                    "Belum ada paket tryout tersedia.",
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
              itemCount: tryouts.length,
              itemBuilder: (context, index) {
                final tData = tryouts[index];
                final String title = tData['title'] ?? tData['name'] ?? 'Tryout';
                final String duration = tData['duration']?.toString() ?? '120';
                final bool isDone = tData['is_done'] == true ||
                    tData['is_done'] == 1 ||
                    tData['is_done'] == "1";
                final String score = tData['score']?.toString() ?? '-';

                return Container(
                  margin: const EdgeInsets.only(bottom: 12),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(
                      color: isDone
                          ? darkTeal.withOpacity(0.25)
                          : outlineVariant.withOpacity(0.4),
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
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => TryoutDetailPage(
                            tryoutData: tData,
                            token: token,
                            isDone: isDone,
                            userId: userId,
                          ),
                        ),
                      );
                    },
                    child: Padding(
                      padding: const EdgeInsets.all(14),
                      child: Row(
                        children: [
                          // Icon container
                          Container(
                            width: 48,
                            height: 48,
                            decoration: BoxDecoration(
                              color: isDone
                                  ? const Color(0xFFE2F9FC)
                                  : lightBlueBg,
                              borderRadius: BorderRadius.circular(14),
                              border: Border.all(
                                color: isDone
                                    ? darkTeal.withOpacity(0.2)
                                    : outlineVariant.withOpacity(0.3),
                              ),
                            ),
                            child: Icon(
                              isDone
                                  ? Icons.check_circle_rounded
                                  : Icons.assignment_rounded,
                              color: isDone ? darkTeal : primaryRed,
                              size: 22,
                            ),
                          ),
                          const SizedBox(width: 14),

                          // Title & subtitle
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  title,
                                  style: const TextStyle(
                                    fontWeight: FontWeight.w900,
                                    fontSize: 15,
                                    color: textDark,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Row(
                                  children: [
                                    const Icon(
                                      Icons.timer_outlined,
                                      size: 12,
                                      color: neutralGray,
                                    ),
                                    const SizedBox(width: 4),
                                    Text(
                                      "$duration Menit",
                                      style: const TextStyle(
                                        fontSize: 11,
                                        color: neutralGray,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                    const SizedBox(width: 8),
                                    Container(
                                      padding: const EdgeInsets.symmetric(
                                          horizontal: 7, vertical: 2),
                                      decoration: BoxDecoration(
                                        color: isDone
                                            ? const Color(0xFFE2F9FC)
                                            : lightBlueBg,
                                        borderRadius: BorderRadius.circular(99),
                                      ),
                                      child: Text(
                                        isDone ? "Selesai" : "Belum",
                                        style: TextStyle(
                                          fontSize: 10,
                                          fontWeight: FontWeight.w900,
                                          color: isDone ? darkTeal : neutralGray,
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                              ],
                            ),
                          ),

                          // Nilai trailing
                          Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            crossAxisAlignment: CrossAxisAlignment.end,
                            children: [
                              Text(
                                "Nilai",
                                style: const TextStyle(
                                  color: neutralGray,
                                  fontSize: 10,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 2),
                              Text(
                                score,
                                style: TextStyle(
                                  color: isDone ? primaryRed : neutralGray,
                                  fontSize: 20,
                                  fontWeight: FontWeight.w900,
                                ),
                              ),
                            ],
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