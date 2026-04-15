import 'package:flutter/material.dart';
import 'quiz_page.dart';
import '../services/auth_service.dart';
import 'dart:convert';

class TryoutDetailPage extends StatelessWidget {
  final Map tryoutData;
  final String token;
  const TryoutDetailPage({super.key, required this.tryoutData, required this.token});

  @override Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);
    return Scaffold(
      appBar: AppBar(title: const Text("Exam Instructions"), backgroundColor: spektaRed, foregroundColor: Colors.white),
      body: Padding(padding: const EdgeInsets.all(25.0),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(tryoutData['title'] ?? "Tryout Simulation", style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: spektaRed)),
            const SizedBox(height: 20),
            _buildInfoRow(Icons.timer_outlined, "Duration: ${tryoutData['duration']} Minutes"),
            _buildInfoRow(Icons.help_outline, "Questions: 25 Items"),
            const SizedBox(height: 30),
            const Text("Important:", style: TextStyle(fontWeight: FontWeight.bold)),
            const Text("1. Work honestly.\n2. Timer will start when you begin.\n3. Do not leave the app during the exam."),
            const Spacer(),
            ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 55), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30))),
              onPressed: () async {
                showDialog(context: context, builder: (_) => const Center(child: CircularProgressIndicator(color: spektaRed)));
                try {
                  // MODIFIKASI: tryoutsID -> tryout_id
                  var resp = await AuthService.getQuestions(tryoutData['tryout_id'], token);
                  if (!context.mounted) return; Navigator.pop(context);
                  if (resp.statusCode == 200) {
                    List questions = jsonDecode(resp.body)['data'];
                    Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => QuizPage(questions: questions, tryoutId: tryoutData['tryout_id'], token: token)));
                  } else {
                    final err = jsonDecode(resp.body)['message'] ?? "Failed to get questions";
                    ScaffoldMessenger.of(context).showSnackBar(SnackBar(backgroundColor: Colors.red, content: Text(err)));
                  }
                } catch (e) { Navigator.pop(context); ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Error: Check your connection!"))); }
              }, child: const Text("START QUIZ NOW", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold))),
          ])),
    );
  }

  Widget _buildInfoRow(IconData icon, String text) => Padding(padding: const EdgeInsets.only(bottom: 10), child: Row(children: [Icon(icon, size: 20, color: Colors.grey), const SizedBox(width: 10), Text(text)]));
}