import 'package:flutter/material.dart';
import 'practice_week_list_page.dart'; // Pastikan import ini benar

class PracticeSubjectListPage extends StatelessWidget {
  final List allExercises;
  final String token;

  const PracticeSubjectListPage({
    super.key, 
    required this.allExercises, 
    required this.token
  });

  @override
  Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);

    // Mengambil daftar subjek unik dari data latihan yang diterima
    final subjects = allExercises.map((e) => e['subject']).toSet().toList();

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text(
          "Pilih Subjek Latihan", 
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)
        ),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: subjects.isEmpty 
      ? const Center(child: Text("Belum ada latihan tersedia."))
      : ListView.builder(
          padding: const EdgeInsets.all(20),
          itemCount: subjects.length,
          itemBuilder: (context, index) {
            final String sName = subjects[index].toString();
            
            return Card(
              // ✨ PERBAIKAN 1: Gunakan EdgeInsets.only(bottom: 15)
              margin: const EdgeInsets.only(bottom: 15), 
              elevation: 0,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(15),
                side: BorderSide(color: Colors.grey.shade200)
              ),
              child: ListTile(
                contentPadding: const EdgeInsets.all(12),
                leading: Container(
                  width: 45, height: 45,
                  decoration: BoxDecoration(
                    color: spektaRed.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(12)
                  ),
                  child: const Icon(Icons.quiz_rounded, color: spektaRed),
                ),
                title: Text(
                  sName, 
                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)
                ),
                subtitle: const Text("Lihat tantangan mingguan"),
                trailing: const Icon(Icons.arrow_forward_ios, size: 14),
                onTap: () {
                  // ✨ PERBAIKAN 2: Pastikan parameter dikirim lengkap
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