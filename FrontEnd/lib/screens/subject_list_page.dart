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
    final Color spektaRed = const Color(0xFF990000);

    // ✨ MODIFIKASI: Logika pengambilan subjek unik yang lebih aman (Case Insensitive)
    // Ini menangani jika Go mengirim "material_name" atau "MaterialName"
    final subjects = materi
        .map((e) {
          // Cek semua kemungkinan kunci yang mungkin dikirim oleh Go/Laravel
          return (e['material_name'] ?? e['MaterialName'] ?? e['subject'] ?? 'Tanpa Nama').toString();
        })
        .where((name) => name != 'Tanpa Nama') // Filter jika ada data kosong
        .toSet() // Menghilangkan duplikat agar satu mapel hanya muncul sekali
        .toList();

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text(
          "Pilih Mata Pelajaran", 
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)
        ),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: subjects.isEmpty 
      ? Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.menu_book_outlined, size: 60, color: Colors.grey[300]),
              const SizedBox(height: 10),
              const Text("Belum ada mata pelajaran tersedia.", style: TextStyle(color: Colors.grey)),
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
                boxShadow: [
                  BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10)
                ]
              ),
              child: ListTile(
                contentPadding: const EdgeInsets.all(15),
                leading: Container(
                  width: 50, 
                  height: 50, 
                  decoration: BoxDecoration(
                    color: spektaRed.withOpacity(0.1), 
                    borderRadius: BorderRadius.circular(15)
                  ), 
                  child: Center(
                    child: Text(
                      "${index + 1}", 
                      style: TextStyle(color: spektaRed, fontWeight: FontWeight.bold)
                    )
                  )
                ),
                title: Text(
                  sName, 
                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18)
                ),
                subtitle: const Text("Klik untuk lihat materi mingguan"),
                trailing: const Icon(Icons.arrow_forward_ios_rounded, size: 16, color: Colors.grey),
                onTap: () {
                  Navigator.push(
                    context, 
                    MaterialPageRoute(
                      builder: (context) => ModuleWeekListPage(
                        subjectName: sName, 
                        token: token, 
                        allMaterials: materi
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