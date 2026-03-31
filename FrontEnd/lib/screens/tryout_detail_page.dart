// lib/screens/tryout_detail_page.dart

import 'package:flutter/material.dart';
import 'quiz_page.dart';
import '../services/auth_service.dart';
import 'dart:convert';

class TryoutDetailPage extends StatelessWidget {
  final Map tryoutData;
  final String token;

  const TryoutDetailPage({super.key, required this.tryoutData, required this.token});

  @override
  Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);

    return Scaffold(
      appBar: AppBar(
        title: const Text("Instruksi Ujian"), 
        backgroundColor: spektaRed, 
        foregroundColor: Colors.white
      ),
      body: Padding(
        padding: const EdgeInsets.all(25.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              tryoutData['title'] ?? "Simulasi Tryout", 
              style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: spektaRed)
            ),
            const SizedBox(height: 20),
            _buildInfoRow(Icons.timer_outlined, "Durasi: ${tryoutData['duration']} Menit"),
            _buildInfoRow(Icons.help_outline, "Jumlah Soal: 25 Butir"),
            const SizedBox(height: 30),
            const Text("Penting:", style: TextStyle(fontWeight: FontWeight.bold)),
            const Text("1. Kerjakan dengan jujur.\n2. Waktu akan terus berjalan saat anda mulai.\n3. Jangan keluar dari aplikasi saat ujian."),
            const Spacer(),
            
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed, 
                minimumSize: const Size(double.infinity, 55),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30))
              ),
              onPressed: () async {
                // 1. Tampilkan Loading
                showDialog(context: context, builder: (_) => const Center(child: CircularProgressIndicator(color: spektaRed)));

                try {
                  // 2. Ambil data soal dari Laravel
                  var resp = await AuthService.getQuestions(tryoutData['tryoutsID'], token);
                  
                  if (!context.mounted) return;
                  Navigator.pop(context); // Tutup Loading

                  if (resp.statusCode == 200) {
                    List questions = jsonDecode(resp.body)['data'];
                    
                    // 3. PINDAH KE HALAMAN QUIZ (Nomor 1)
                    Navigator.pushReplacement(context, MaterialPageRoute(
                      builder: (_) => QuizPage(
                        questions: questions, 
                        tryoutId: tryoutData['tryoutsID'], 
                        token: token
                      )
                    ));
                  } else {
                    // JIKA GAGAL (Misal: Soal masih kosong di database)
                    final errorMsg = jsonDecode(resp.body)['message'] ?? "Gagal mengambil soal";
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(backgroundColor: Colors.red, content: Text(errorMsg))
                    );
                  }
                } catch (e) {
                  Navigator.pop(context);
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text("Error: Cek koneksi server Anda!"))
                  );
                }
              },
              child: const Text("MULAI QUIZ SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            )
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Row(children: [Icon(icon, size: 20, color: Colors.grey), const SizedBox(width: 10), Text(text)]),
    );
  }
}