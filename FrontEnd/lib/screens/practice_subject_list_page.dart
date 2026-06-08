import 'package:flutter/material.dart';
import 'practice_week_list_page.dart';

class PracticeSubjectListPage extends StatelessWidget {
  final List allExercises;
  final String token;
  // userId dihapus dari sini

  const PracticeSubjectListPage({
    super.key, 
    required this.allExercises, 
    required this.token,
  });

  @override
  Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);

    // Cek semua kemungkinan key (subject, Subject, subject_name)
    final subjects = allExercises
        .map((e) {
          var name = e['subject'] ?? e['Subject'] ?? e['subject_name'] ?? '';
          return name.toString().trim();
        })
        .where((name) => name.isNotEmpty) 
        .toSet() 
        .toList();

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text(
          "Pilih Subjek Latihan", 
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)
        ),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        centerTitle: true,
        elevation: 0,
      ),
      body: subjects.isEmpty 
      ? Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.quiz_outlined, size: 70, color: Colors.grey[300]),
              const SizedBox(height: 10),
              const Text("Belum ada latihan tersedia.", style: TextStyle(color: Colors.grey)),
            ],
          ),
        )
      : ListView.builder(
          padding: const EdgeInsets.all(20),
          itemCount: subjects.length,
          itemBuilder: (context, index) {
            final String sName = subjects[index].toString();
            
            return Container(
              margin: const EdgeInsets.only(bottom: 15),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10)]
              ),
              child: ListTile(
                contentPadding: const EdgeInsets.all(15),
                leading: Container(
                  width: 50, height: 50,
                  decoration: BoxDecoration(
                    color: spektaRed.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(15)
                  ),
                  child: const Icon(Icons.quiz_rounded, color: spektaRed),
                ),
                title: Text(
                  sName, 
                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18)
                ),
                subtitle: const Text("Lihat tantangan mingguan"),
                trailing: const Icon(Icons.arrow_forward_ios_rounded, size: 14),
                onTap: () {
                  Navigator.push(
                    context, 
                    MaterialPageRoute(
                      builder: (context) => PracticeWeekListPage(
                        subjectName: sName,
                        allExercises: allExercises,
                        token: token,
                      )
                    )
                  );
                },
              ),
            );
          },
        ),
    );
  }
}