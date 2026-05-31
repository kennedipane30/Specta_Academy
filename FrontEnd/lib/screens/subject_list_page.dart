import 'package:flutter/material.dart';
import 'module_week_list_page.dart';

class SubjectListPage extends StatelessWidget {
  final int classId;
  final String className;
  final String token;
  final List subjects; // ✨ TAMBAHKAN INI: Daftar Mapel asli dari Laravel
  final List materi;   // Daftar file materi dari Go

  const SubjectListPage({
    super.key,
    required this.classId,
    required this.className,
    required this.token,
    required this.subjects, // ✨ Wajib diisi
    required this.materi,
  });

  @override
  Widget build(BuildContext context) {
    final Color spektaRed = const Color(0xFF990000);

    // 💡 LOGIKA: Kita gunakan 'subjects' dari Laravel sebagai daftar utama.
    // Jika 'subjects' kosong (misal karena error), barulah kita fallback ke cara lama (ekstrak dari materi).
    List displaySubjects = subjects.isNotEmpty 
        ? subjects 
        : materi
            .where((e) => e['material_name'] != null || e['MaterialName'] != null)
            .map((e) => (e['material_name'] ?? e['MaterialName'] ?? 'Tanpa Nama').toString())
            .toSet()
            .toList();

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text("Pilih Mata Pelajaran", 
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white, fontSize: 18)),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
      ),
      body: displaySubjects.isEmpty 
      ? const Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.book_outlined, size: 64, color: Colors.grey),
              SizedBox(height: 16),
              Text("Belum ada mata pelajaran tersedia.", 
                style: TextStyle(color: Colors.grey, fontWeight: FontWeight.w600)),
            ],
          )
        )
      : ListView.builder(
          padding: const EdgeInsets.all(20),
          itemCount: displaySubjects.length,
          itemBuilder: (context, index) {
            final String sName = displaySubjects[index].toString();
            
            return Container(
              margin: const EdgeInsets.only(bottom: 16),
              decoration: BoxDecoration(
                color: Colors.white, 
                borderRadius: BorderRadius.circular(20), 
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.04), 
                    blurRadius: 15, 
                    offset: const Offset(0, 8)
                  )
                ]
              ),
              child: ListTile(
                contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                leading: Container(
                  width: 50, 
                  height: 50, 
                  decoration: BoxDecoration(
                    color: spektaRed.withOpacity(0.1), 
                    borderRadius: BorderRadius.circular(15)
                  ), 
                  child: Center(
                    child: Icon(Icons.menu_book_rounded, color: spektaRed, size: 24)
                  )
                ),
                title: Text(
                  sName, 
                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 17, color: Color(0xFF1A1A1A))
                ),
                subtitle: const Text("Klik untuk lihat materi per minggu", 
                  style: TextStyle(fontSize: 12, color: Colors.grey)),
                trailing: const Icon(Icons.arrow_forward_ios_rounded, size: 14, color: Colors.grey),
                onTap: () {
                  // Kirim sName dan all materi ke halaman list minggu
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