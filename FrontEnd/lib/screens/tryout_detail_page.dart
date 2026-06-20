import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http; 
import '../services/auth_service.dart';
import '../config/app_config.dart'; 
import 'quiz_page.dart';
import 'explanation_page.dart';

class TryoutDetailPage extends StatelessWidget {
  final Map tryoutData;
  final String token;
  final bool isDone;
  final int userId;

  const TryoutDetailPage({
    super.key,
    required this.tryoutData,
    required this.token,
    required this.userId,
    this.isDone = false,
  });

  // ============================================================
  // 🎨 PALET WARNA SPEKTA
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
    final String title = tryoutData['title'] ?? tryoutData['name'] ?? "Tryout Simulation";
    final String duration = tryoutData['duration']?.toString() ?? '-';

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
          "Instruksi Ujian",
          style: TextStyle(fontWeight: FontWeight.w900, color: Colors.white, fontSize: 17),
        ),
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(16, 20, 16, 32),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [

            // Header card judul tryout
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [primaryRed, accentTeal],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: primaryRed.withOpacity(0.2),
                    blurRadius: 12,
                    offset: const Offset(0, 5),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      isDone ? "SUDAH DIKERJAKAN" : "SIAP DIKERJAKAN",
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 10,
                        fontWeight: FontWeight.w900,
                        letterSpacing: 1.2,
                      ),
                    ),
                  ),
                  const SizedBox(height: 10),
                  Text(
                    title,
                    style: const TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.w900,
                      color: Colors.white,
                      height: 1.2,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Info card
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(18),
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
              child: Column(
                children: [
                  _buildInfoRow(Icons.timer_rounded, "Durasi Ujian", "$duration Menit"),
                  const Divider(height: 20, color: Color(0xFFE2E8F0)),
                  _buildInfoRow(Icons.help_center_rounded, "Jumlah Soal", "Sesuai Sistem"),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Peraturan card
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(18),
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
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    "Peraturan Penting",
                    style: TextStyle(
                      fontWeight: FontWeight.w900,
                      fontSize: 15,
                      color: textDark,
                    ),
                  ),
                  const SizedBox(height: 14),
                  _buildPoint("Kerjakan secara jujur dan mandiri."),
                  _buildPoint("Waktu akan berjalan otomatis setelah tombol diklik."),
                  _buildPoint("Pastikan koneksi internet stabil selama ujian."),
                  _buildPoint("Jangan keluar dari aplikasi saat ujian berlangsung."),
                ],
              ),
            ),
            const SizedBox(height: 32),

            // Tombol aksi
            SizedBox(
              width: double.infinity,
              height: 56,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: isDone ? darkTeal : accentTeal,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(16),
                  ),
                  elevation: 0,
                ),
                onPressed: () async {
                  showDialog(
                    context: context,
                    barrierDismissible: false,
                    builder: (_) => Center(
                      child: CircularProgressIndicator(
                        color: isDone ? darkTeal : accentTeal,
                      ),
                    ),
                  );

                  try {
                    final dynamic rawId = tryoutData['tryout_id'] ?? tryoutData['id'] ?? tryoutData['ID'] ?? 0;
                    final int id = int.parse(rawId.toString());

                    // Ambil soal dari server
                    var resp = await AuthService.getQuestions(id, token);

                    if (!context.mounted) return;
                    Navigator.pop(context); // Tutup loading pertama (ambil soal)

                    if (resp.statusCode == 200) {
                      var decoded = jsonDecode(resp.body);
                      List questions = [];
                      if (decoded is List) {
                        questions = decoded;
                      } else if (decoded is Map) {
                        questions = decoded['data'] ?? [];
                      }

                      if (questions.isEmpty) {
                        _showError(context, "Soal belum tersedia untuk paket ini.");
                        return;
                      }

                      if (isDone) {
                        showDialog(
                          context: context,
                          barrierDismissible: false,
                          builder: (_) => const Center(
                            child: CircularProgressIndicator(color: darkTeal),
                          ),
                        );

                        try {
                          // ✨ MODIFIKASI: Ganti baseUrl dengan tryoutUrl (Arahkan ke Golang Port 9002)
                          String urlAPI = '${AppConfig.tryoutUrl}/tryouts/submissions?tryout_id=$id';
                          debugPrint("🔍 [DEBUG] URL API Pembahasan: $urlAPI");

                          final subRes = await http.get(
                            Uri.parse(urlAPI),
                            headers: {'Authorization': 'Bearer $token'},
                          ).timeout(const Duration(seconds: 10));

                          if (subRes.statusCode == 200) {
                            final subDecoded = jsonDecode(subRes.body);
                            
                            List submissions = [];
                            if (subDecoded is List) {
                              submissions = subDecoded;
                            } else if (subDecoded is Map && subDecoded['data'] != null) {
                              submissions = subDecoded['data'];
                            }

                            var mySubmission;
                            for (var s in submissions) {
                              if (s['user_id'].toString() == userId.toString()) {
                                mySubmission = s;
                                break;
                              }
                            }

                            if (mySubmission != null) {
                              if (mySubmission['answers'] != null) {
                                Map<String, dynamic> userAnswersMap = {};
                                
                                if (mySubmission['answers'] is String) {
                                  userAnswersMap = jsonDecode(mySubmission['answers']);
                                } else if (mySubmission['answers'] is Map) {
                                  userAnswersMap = mySubmission['answers'];
                                }

                                for (var i = 0; i < questions.length; i++) {
                                  String qId = questions[i]['question_id'].toString();
                                  questions[i]['user_answer'] = userAnswersMap[qId];
                                }
                              }
                            }
                          } else {
                            if (context.mounted) {
                               Navigator.pop(context); 
                               debugPrint("❌ Gagal: Status ${subRes.statusCode}, Body: ${subRes.body}");
                               _showError(context, "Mohon maaf sistem sedang sibuk (Error Server: ${subRes.statusCode})");
                            }
                            return;
                          }
                        } catch (e) {
                          if (context.mounted) {
                               Navigator.pop(context); 
                               debugPrint("❌ Error Fetching Submissions: $e");
                               _showError(context, "Gagal terhubung ke server. Periksa koneksi Anda.");
                          }
                          return;
                        }

                        if (!context.mounted) return;
                        Navigator.pop(context); // Tutup loading kedua

                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => ExplanationPage(questions: questions),
                          ),
                        );
                      } else {
                        // Jika belum dikerjakan, Buka QuizPage
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => QuizPage(
                              questions: questions,
                              tryoutId: id,
                              token: token,
                              userId: userId,
                            ),
                          ),
                        ).then((_) {
                          if (context.mounted) {
                            Navigator.pop(context);
                          }
                        });
                      }
                    } else {
                      _showError(context, "Mohon maaf sistem sedang sibuk");
                    }
                  } catch (e) {
                    if (context.mounted) Navigator.pop(context); 
                    debugPrint("❌ Tryout Error: $e");
                    _showError(context, "Terjadi kesalahan, pastikan internet stabil.");
                  }
                },
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(
                      isDone ? Icons.menu_book_rounded : Icons.play_arrow_rounded,
                      color: Colors.white,
                      size: 20,
                    ),
                    const SizedBox(width: 10),
                    Text(
                      isDone ? "LIHAT PEMBAHASAN" : "MULAI UJIAN SEKARANG",
                      style: const TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.w900,
                        fontSize: 15,
                        letterSpacing: 0.5,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Row(
      children: [
        Container(
          width: 40,
          height: 40,
          decoration: BoxDecoration(
            color: lightBlueBg,
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, size: 18, color: accentTeal),
        ),
        const SizedBox(width: 14),
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              label,
              style: const TextStyle(
                fontSize: 11,
                color: neutralGray,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 2),
            Text(
              value,
              style: const TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w900,
                color: textDark,
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildPoint(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            margin: const EdgeInsets.only(top: 5),
            width: 6,
            height: 6,
            decoration: const BoxDecoration(
              color: accentTeal,
              shape: BoxShape.circle,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              text,
              style: const TextStyle(
                color: textDarkVariant,
                fontSize: 13,
                height: 1.5,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ],
      ),
    );
  }

  void _showError(BuildContext context, String msg) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        backgroundColor: primaryRed,
        content: Text(msg),
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      ),
    );
  }
}