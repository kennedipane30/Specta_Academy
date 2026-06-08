import 'package:flutter/material.dart';
import 'dart:convert';
import '../services/auth_service.dart';
import 'quiz_page.dart';
import 'explanation_page.dart';

class TryoutDetailPage extends StatelessWidget {
  final Map tryoutData;
  final String token;
  final bool isDone;
  // ✨ MODIFIKASI 1: Tambahkan parameter userId
  final int userId; 

  const TryoutDetailPage({
    super.key, 
    required this.tryoutData, 
    required this.token,
    required this.userId, // ✨ MODIFIKASI 1: Wajib diisi agar bisa diteruskan
    this.isDone = false, 
  });

  @override
  Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text("Instruksi Ujian", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)), 
        backgroundColor: spektaRed, 
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
      ),
      body: Padding(
        padding: const EdgeInsets.all(30.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              tryoutData['title'] ?? tryoutData['name'] ?? "Tryout Simulation", 
              style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: spektaRed)
            ),
            const SizedBox(height: 25),
            
            _buildInfoRow(Icons.timer_rounded, "Durasi Ujian: ${tryoutData['duration']} Menit"),
            _buildInfoRow(Icons.help_center_rounded, "Jumlah Soal: Sesuai Sistem"),
            
            const SizedBox(height: 35),
            const Text("Peraturan Penting:", style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16)),
            const SizedBox(height: 10),
            _buildPoint("1. Kerjakan secara jujur dan mandiri."),
            _buildPoint("2. Waktu akan berjalan otomatis setelah tombol diklik."),
            _buildPoint("3. Pastikan koneksi internet stabil selama ujian."),
            _buildPoint("4. Jangan keluar dari aplikasi saat ujian berlangsung."),
            
            const Spacer(),
            
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: isDone ? Colors.green : spektaRed,
                minimumSize: const Size(double.infinity, 60),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                elevation: 8,
              ),
              onPressed: () async {
                showDialog(
                  context: context, 
                  barrierDismissible: false, 
                  builder: (_) => Center(child: CircularProgressIndicator(color: isDone ? Colors.green : spektaRed))
                );

                try {
                  final dynamic rawId = tryoutData['tryout_id'] ?? tryoutData['id'] ?? tryoutData['ID'] ?? 0;
                  final int id = int.parse(rawId.toString());
                  
                  var resp = await AuthService.getQuestions(id, token);
                  
                  if (!context.mounted) return;
                  Navigator.pop(context); // Tutup loading

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
                      // JIKA SUDAH SELESAI -> Masuk ke Pembahasan
                      Navigator.push(context, MaterialPageRoute(
                        builder: (_) => ExplanationPage(questions: questions)
                      ));
                    } else {
                      // JIKA BELUM SELESAI -> Masuk Ujian
                      Navigator.pushReplacement(context, MaterialPageRoute(
                        builder: (_) => QuizPage(
                          questions: questions, 
                          tryoutId: id, 
                          token: token,
                          userId: userId, // ✨ MODIFIKASI 2: Teruskan userId ke QuizPage
                        )
                      ));
                    }

                  } else {
                    _showError(context, "Gagal mengambil soal dari server (Status: ${resp.statusCode})");
                  }
                } catch (e) {
                  if (context.mounted) Navigator.pop(context);
                  debugPrint("❌ Tryout Error: $e");
                  _showError(context, "Kesalahan koneksi ke server Tryout.");
                }
              },
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(isDone ? Icons.check_circle_outline : Icons.play_arrow_rounded, color: Colors.white),
                  const SizedBox(width: 10),
                  Text(
                    isDone ? "LIHAT PEMBAHASAN" : "MULAI UJIAN SEKARANG", 
                    style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900, letterSpacing: 1)
                  ),
                ],
              ),
            )
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 15),
      child: Row(
        children: [
          Icon(icon, size: 20, color: const Color(0xFF990000)),
          const SizedBox(width: 15),
          Text(text, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
        ],
      ),
    );
  }

  Widget _buildPoint(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Text(text, style: TextStyle(color: Colors.grey[700], fontSize: 14, height: 1.5)),
    );
  }

  void _showError(BuildContext context, String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(backgroundColor: Colors.red, content: Text(msg), behavior: SnackBarBehavior.floating));
  }
}