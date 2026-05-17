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
    // Mengambil daftar subjek unik dari data materi yang dikirim Go
 // Ubah bagian ini:
  final subjects = materi
      .where((e) => e['material_name'] != null || e['MaterialName'] != null) // Filter data rusak
      .map((e) => (e['material_name'] ?? e['MaterialName'] ?? 'Tanpa Nama').toString())
      .toSet()
      .toList();

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text("Pilih Mata Pelajaran", 
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: subjects.isEmpty 
      ? const Center(child: Text("Belum ada mata pelajaran tersedia."))
      : ListView.builder(
          padding: const EdgeInsets.all(20),
          itemCount: subjects.length,
          itemBuilder: (context, index) {
            final sName = subjects[index] ?? "Subjek Tanpa Nama";
            return Container(
              margin: const EdgeInsets.only(bottom: 15),
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10)]),
              child: ListTile(
                contentPadding: const EdgeInsets.all(15),
                leading: Container(width: 50, height: 50, decoration: BoxDecoration(color: spektaRed.withOpacity(0.1), borderRadius: BorderRadius.circular(15)), child: Center(child: Text("${index + 1}", style: TextStyle(color: spektaRed, fontWeight: FontWeight.bold)))),
                title: Text(sName, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
                subtitle: const Text("Klik untuk lihat materi 20 minggu"),
                trailing: const Icon(Icons.arrow_forward_ios_rounded, size: 16, color: Colors.grey),
                onTap: () {
                  Navigator.push(context, MaterialPageRoute(builder: (context) => ModuleWeekListPage(subjectName: sName, token: token, allMaterials: materi)));
                },
              ),
            );
          },
        ),
    );
  }
}