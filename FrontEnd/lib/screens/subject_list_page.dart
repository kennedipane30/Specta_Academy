import 'package:flutter/material.dart';
import 'module_week_list_page.dart';

class SubjectListPage extends StatelessWidget {
  final int classId;
  final String className;
  final String token;
  final List subjects; // Daftar dari Laravel: [{"subject_id": 1, "name": "Biologi"}, ...]
  final List materi;   // Daftar file materi lengkap

  const SubjectListPage({
    super.key,
    required this.classId,
    required this.className,
    required this.token,
    required this.subjects,
    required this.materi,
  });

  @override
  Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);
    const Color spektaDark = Color(0xFF1A1A1A);

    // 💡 LOGIKA: Menentukan daftar mata pelajaran yang akan ditampilkan
    List displaySubjects = [];

    if (subjects.isNotEmpty) {
      // Jika ada daftar subjek resmi dari Laravel, gunakan itu
      displaySubjects = subjects;
    } else {
      // Jika kosong, ambil nama unik subjek dari daftar materi (fallback)
      // Kita mengambil field 'subject_name' atau 'subject' dari list materi
      displaySubjects = materi
          .map((e) => e['subject_name']?.toString() ?? e['subject']?.toString() ?? 'Umum')
          .toSet() // Menghilangkan duplikat
          .toList();
    }

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: Text(
          className, // Menampilkan nama kelas (contoh: PTN UNHAN)
          style: const TextStyle(
            fontWeight: FontWeight.bold, 
            color: Colors.white, 
            fontSize: 18
          )
        ),
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
                  Text(
                    "Belum ada mata pelajaran tersedia.",
                    style: TextStyle(color: Colors.grey, fontWeight: FontWeight.w600),
                  ),
                ],
              ),
            )
          : ListView.builder(
              padding: const EdgeInsets.all(20),
              itemCount: displaySubjects.length,
              itemBuilder: (context, index) {
                final dynamic item = displaySubjects[index];
                String sName = "";

                // Ekstraksi Nama Mata Pelajaran
                if (item is Map) {
                  sName = item['name']?.toString() ?? "Mata Pelajaran";
                } else {
                  sName = item.toString();
                }

                return Container(
                  margin: const EdgeInsets.only(bottom: 16),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(20),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.04),
                        blurRadius: 15,
                        offset: const Offset(0, 8),
                      )
                    ],
                  ),
                  child: ListTile(
                    contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                    leading: Container(
                      width: 50,
                      height: 50,
                      decoration: BoxDecoration(
                        color: spektaRed.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(15),
                      ),
                      child: const Center(
                        child: Icon(Icons.menu_book_rounded, color: spektaRed, size: 24),
                      ),
                    ),
                    title: Text(
                      sName,
                      style: const TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 17,
                        color: spektaDark,
                      ),
                    ),
                    subtitle: const Text(
                      "Lihat materi per minggu",
                      style: TextStyle(fontSize: 12, color: Colors.grey),
                    ),
                    trailing: const Icon(
                      Icons.arrow_forward_ios_rounded,
                      size: 14,
                      color: Colors.grey,
                    ),
                    onTap: () {
                      // ✨ DEBUG: Pastikan token ada sebelum pindah halaman
                      debugPrint("Navigasi ke $sName dengan token: ${token.isNotEmpty ? 'Tersedia' : 'KOSONG'}");

                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => ModuleWeekListPage(
                            subjectName: sName,
                            token: token, // 🔑 Mengirim token ke ModuleWeekListPage
                            allMaterials: materi, // Mengirim semua file materi untuk difilter di sana
                          ),
                        ),
                      );
                    },
                  ),
                );
              },
            ),
    );
  }
}