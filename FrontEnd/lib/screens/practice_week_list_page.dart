import 'package:flutter/material.dart';
import 'practice_quiz_page.dart';

class PracticeWeekListPage extends StatelessWidget {
  final String subjectName;
  final List allExercises; // Data dari tabel latihan_soals
  final String token;

  const PracticeWeekListPage({
    super.key,
    required this.subjectName,
    required this.allExercises,
    required this.token,
  });

  @override
  Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: Text("LATIHAN ${subjectName.toUpperCase()}", 
          style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(20),
        itemCount: 20, // 20 Minggu
        itemBuilder: (context, index) {
          int weekNumber = index + 1;
          
          // Cek apakah ada soal untuk minggu ini
          List weekSoals = allExercises.where((e) => 
            e['subject'] == subjectName && 
            e['minggu'].toString() == weekNumber.toString()
          ).toList();

          bool isAvailable = weekSoals.isNotEmpty;

          return Container(
            margin: const EdgeInsets.only(bottom: 12),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(18),
              boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10)],
            ),
            child: ListTile(
              leading: CircleAvatar(
                backgroundColor: isAvailable ? Colors.orange.withOpacity(0.1) : Colors.grey.shade100,
                child: Text("$weekNumber", style: TextStyle(color: isAvailable ? Colors.orange : Colors.grey, fontWeight: FontWeight.bold)),
              ),
              title: Text("Minggu Ke-$weekNumber", style: const TextStyle(fontWeight: FontWeight.bold)),
              subtitle: Text(isAvailable ? "${weekSoals.length} Soal Tersedia" : "Belum ada soal"),
              trailing: const Icon(Icons.arrow_forward_ios, size: 14),
              onTap: isAvailable 
                ? () => Navigator.push(context, MaterialPageRoute(builder: (c) => PracticeQuizPage(
                    subject: subjectName,
                    week: weekNumber,
                    questions: weekSoals,
                  )))
                : () => ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Soal belum tersedia untuk minggu ini."))),
            ),
          );
        },
      ),
    );
  }
}