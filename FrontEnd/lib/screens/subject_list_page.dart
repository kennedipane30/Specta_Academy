import 'package:flutter/material.dart';
import 'module_week_list_page.dart';

class SubjectListPage extends StatelessWidget {
  final int classId;
  final String className;
  final String token;
  final List materi;

  const SubjectListPage({
    super.key,
    required this.classId,
    required this.className,
    required this.token,
    required this.materi,
  });

  @override
  Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);

    // ✨ PERBAIKAN LOGIKA: Mengambil Mata Pelajaran Unik
// ✨ PERBAIKAN LOGIKA: Ambil mata pelajaran unik
    final subjects = materi
        .map((e) {
          // Cek semua kemungkinan key yang dikirim oleh Go
          return (e['subject_name'] ?? e['material_name'] ?? e['title'] ?? '').toString();
        })
        .where((name) => name.isNotEmpty) 
        .toSet() 
        .toList();

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text("Pilih Mata Pelajaran", 
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        centerTitle: true,
      ),
      body: subjects.isEmpty 
      ? Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.auto_stories_outlined, size: 70, color: Colors.grey[300]),
              const SizedBox(height: 10),
              const Text("Materi pelajaran belum tersedia", style: TextStyle(color: Colors.grey)),
            ],
          )
        )
      : ListView.builder(
          padding: const EdgeInsets.all(20),
          itemCount: subjects.length,
          itemBuilder: (context, index) {
            final sName = subjects[index];
            
            return Container(
              margin: const EdgeInsets.only(bottom: 15),
              decoration: BoxDecoration(
                color: Colors.white, 
                borderRadius: BorderRadius.circular(20), 
                boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10)]
              ),
              child: ListTile(
                contentPadding: const EdgeInsets.all(15),
                leading: Container(
                  width: 50, height: 50, 
                  decoration: BoxDecoration(color: spektaRed.withOpacity(0.1), borderRadius: BorderRadius.circular(15)), 
                  child: const Icon(Icons.menu_book_rounded, color: spektaRed)
                ),
                title: Text(sName, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
                subtitle: const Text("Lihat materi 20 minggu"),
                trailing: const Icon(Icons.arrow_forward_ios_rounded, size: 14),
                onTap: () {
                  // ➡️ NAVIGASI KE HALAMAN 20 MINGGU
                  Navigator.push(context, MaterialPageRoute(
                    builder: (context) => ModuleWeekListPage(
                      subjectName: sName, 
                      token: token, 
                      allMaterials: materi
                    )
                  ));
                },
              ),
            );
          },
        ),
    );
  }
}