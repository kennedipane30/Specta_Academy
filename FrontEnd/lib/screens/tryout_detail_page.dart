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
        title: const Text("Exam Instructions"), 
        backgroundColor: spektaRed, 
        foregroundColor: Colors.white
      ),
      body: Padding(
        padding: const EdgeInsets.all(25.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              tryoutData['title'] ?? "Tryout Simulation", 
              style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: spektaRed)
            ),
            const SizedBox(height: 20),
            _buildInfoRow(Icons.timer_outlined, "Duration: ${tryoutData['duration']} Minutes"),
            _buildInfoRow(Icons.help_outline, "Questions: Variable Items"),
            const SizedBox(height: 30),
            const Text("Important Note:", style: TextStyle(fontWeight: FontWeight.bold)),
            const Text("1. Work honestly and independently.\n2. The timer will start immediately when you click the button.\n3. Do not close or minimize the app during the exam."),
            const Spacer(),
            
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed, 
                minimumSize: const Size(double.infinity, 55),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30))
              ),
              onPressed: () async {
                // Tampilkan Loading
                showDialog(context: context, barrierDismissible: false, builder: (_) => const Center(child: CircularProgressIndicator(color: spektaRed)));

                try {
                  // MODIFIKASI: Gunakan 'tryout_id' (English) bukan 'tryoutsID'
                  final int id = tryoutData['tryout_id'];
                  
                  var resp = await AuthService.getQuestions(id, token);
                  
                  if (!context.mounted) return;
                  Navigator.pop(context); // Tutup Loading

                  if (resp.statusCode == 200) {
                    List questions = jsonDecode(resp.body)['data'];
                    
                    // PINDAH KE HALAMAN QUIZ
                    Navigator.pushReplacement(context, MaterialPageRoute(
                      builder: (_) => QuizPage(
                        questions: questions, 
                        tryoutId: id, 
                        token: token
                      )
                    ));
                  } else {
                    final errorMsg = jsonDecode(resp.body)['message'] ?? "Questions not found";
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(backgroundColor: Colors.red, content: Text(errorMsg))
                    );
                  }
                } catch (e) {
                  if (context.mounted) Navigator.pop(context);
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(backgroundColor: Colors.black, content: Text("Error: Check your server connection!"))
                  );
                }
              },
              child: const Text("START EXAM NOW", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
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