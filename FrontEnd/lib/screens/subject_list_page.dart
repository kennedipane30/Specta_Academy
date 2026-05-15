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
    // Definisi warna agar tidak error "undefined"
    final Color spektaRed = const Color(0xFF990000);

    // Mengambil daftar subjek unik (TIU, TWK, dll) dari data materi
    final subjects = materi.map((e) => e['material_name']).toSet().toList();

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
              // PERBAIKAN 1: Gunakan EdgeInsets.only(bottom: 15)
              margin: const EdgeInsets.only(bottom: 15),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                // PERBAIKAN 2: Hapus 'const' agar withOpacity bisa jalan
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.05), 
                    blurRadius: 10
                  )
                ]
              ),
              child: ListTile(
                contentPadding: const EdgeInsets.all(15),
                leading: Container(
                  width: 50, height: 50,
                  decoration: BoxDecoration(
                    color: spektaRed.withOpacity(0.1), 
                    borderRadius: BorderRadius.circular(15)
                  ),
                  child: Center(
                    child: Text("${index + 1}", 
                      style: TextStyle(color: spektaRed, fontWeight: FontWeight.bold))
                  ),
                ),
                title: Text(sName, 
                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
                subtitle: const Text("Klik untuk lihat materi 20 minggu"),
                trailing: const Icon(Icons.arrow_forward_ios_rounded, size: 16, color: Colors.grey),
                onTap: () {
                  // PERBAIKAN 3: Tambahkan parameter 'allMaterials' yang diminta
                  Navigator.push(
                    context, 
                    MaterialPageRoute(
                      builder: (context) => ModuleWeekListPage(
                        subjectName: sName,
                        token: token,
                        allMaterials: materi, // Kirim data materi lengkap ke halaman berikutnya
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