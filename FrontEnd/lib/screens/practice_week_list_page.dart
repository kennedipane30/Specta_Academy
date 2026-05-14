import 'package:flutter/material.dart';
import 'practice_quiz_page.dart';

class PracticeWeekListPage extends StatelessWidget {
  final String subjectName;
  final List allExercises;
  final String token;

  const PracticeWeekListPage({super.key, required this.subjectName, required this.allExercises, required this.token});

  @override
  Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: Text("${subjectName.toUpperCase()} PRACTICE", style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Colors.white)), 
        backgroundColor: spektaRed, 
        foregroundColor: Colors.white
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(20),
        itemCount: 20, 
        itemBuilder: (context, index) {
          int weekNumber = index + 1;

          // ✨ LOGIKA FILTER YANG DISEDERHANAKAN
          List weekSoals = allExercises.where((e) {
            return e['subject'].toString().toLowerCase() == subjectName.toLowerCase() && 
                   e['week'].toString() == weekNumber.toString();
          }).toList();

          bool isAvailable = weekSoals.isNotEmpty;

          return Container(
            margin: const EdgeInsets.only(bottom: 15), 
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10)]),
            child: ListTile(
              contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
              leading: CircleAvatar(
                backgroundColor: isAvailable ? Colors.blue.withOpacity(0.1) : Colors.grey.shade100, 
                child: Text("$weekNumber", style: TextStyle(color: isAvailable ? Colors.blue : Colors.grey, fontWeight: FontWeight.bold))
              ),
              title: Text("Week $weekNumber", style: TextStyle(fontWeight: FontWeight.w900, color: isAvailable ? Colors.black : Colors.grey)),
              subtitle: Text(isAvailable ? "${weekSoals.length} Questions Ready" : "Not yet uploaded"),
              trailing: const Icon(Icons.arrow_forward_ios, size: 14),
              onTap: isAvailable 
                ? () => Navigator.push(context, MaterialPageRoute(builder: (c) => PracticeQuizPage(questions: weekSoals)))
                : null,
            ),
          );
        },
      ),
    );
  }
}