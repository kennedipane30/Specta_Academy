import 'package:flutter/material.dart';
import 'class_detail_page.dart';

class KelasPage extends StatelessWidget {
  final String token;
  final Map userData; // Data profil lengkap dari MainScreen

  const KelasPage({super.key, required this.token, required this.userData});

  final Color spektaRed = const Color(0xFF990000);
  final Color spektaYellow = const Color(0xFFF1B401);

  @override
  Widget build(BuildContext context) {
    // Data 4 Program Spekta
    final List<Map<String, dynamic>> programs = [
      {
        "id": 1,
        "name": "CALON\nABDI NEGARA",
        "image": "assets/images/abdi_negara.png",
        "title": "Program Bimbingan Belajar",
        "subtitle": "TNI - POLRI - SEKDIN"
      },
      {
        "id": 2,
        "name": "PTN &\nUNHAN",
        "image": "assets/images/ptn_unhan.png",
        "title": "Program Bimbingan Belajar",
        "subtitle": "PERSIAPAN MASUK KAMPUS IMPIAN"
      },
      {
        "id": 3,
        "name": "SMA & SMP\nREGULER",
        "image": "assets/images/reguler.png",
        "title": "Program Bimbingan Belajar",
        "subtitle": "KURSUS HARIAN SISWA"
      },
      {
        "id": 4,
        "name": "SMA\nFAVORIT",
        "image": "assets/images/favorit.png",
        "title": "Program Bimbingan Belajar",
        "subtitle": "DEL - TN - MATAULI - SOPOSURUNG"
      },
    ];

    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      appBar: AppBar(
        title: const Text("Pilih Program Kelas",
            style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(20),
        itemCount: programs.length,
        itemBuilder: (context, index) =>
            _buildProgramCard(context, programs[index]),
      ),
    );
  }

  // --- FUNGSI CEK DATA (GATEKEEPER) ---
  void _checkProfileAndNavigate(BuildContext context, Map<String, dynamic> item) {
    var student = userData['student'];

    // Cek kelengkapan data (Nama Ortu, Alamat, WA Ortu)
    // Sesuai seeder Laravel kita, jika kosong isinya "-"
    bool isComplete = student['parent_name'] != "-" &&
        student['school'] != "-" &&
        student['wa_ortu'] != "-";

    if (isComplete) {
      // JIKA LENGKAP -> Masuk ke Detail
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (context) => ClassDetailPage(
            classId: item['id'],
            className: item['name'].replaceAll('\n', ' '),
            token: token,
            userData: userData, // <-- PERBAIKAN: Sekarang data identitas dibawa!
          ),
        ),
      );
    } else {
      // JIKA TIDAK LENGKAP -> Tampilkan Peringatan
      showDialog(
        context: context,
        builder: (context) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          title: Text("Profil Belum Lengkap", style: TextStyle(color: spektaRed, fontWeight: FontWeight.bold)),
          content: const Text(
              "Anda wajib melengkapi data Nama Orang Tua, Alamat, dan WA Ortu di menu Akun sebelum mendaftar kelas."),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text("OKE, SAYA MENGERTI", style: TextStyle(fontWeight: FontWeight.bold)),
            )
          ],
        ),
      );
    }
  }

  Widget _buildProgramCard(BuildContext context, Map<String, dynamic> item) {
    return Container(
      margin: const EdgeInsets.only(bottom: 25),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(25),
        boxShadow: [
          BoxShadow(
              color: Colors.black.withOpacity(0.1),
              blurRadius: 10,
              offset: const Offset(0, 5))
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          ClipRRect(
            borderRadius: const BorderRadius.vertical(top: Radius.circular(25)),
            child: Image.asset(item['image'],
                height: 220, width: double.infinity, fit: BoxFit.cover),
          ),
          Padding(
            padding: const EdgeInsets.all(20.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(item['title'],
                    style: const TextStyle(
                        color: Colors.red,
                        fontSize: 11,
                        fontWeight: FontWeight.bold)),
                const SizedBox(height: 5),
                Text(item['name'],
                    style: TextStyle(
                        color: spektaRed,
                        fontSize: 22,
                        fontWeight: FontWeight.w900,
                        height: 1.1)),
                const SizedBox(height: 20),
                InkWell(
                  onTap: () => _checkProfileAndNavigate(context, item),
                  child: Container(
                    padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 25),
                    decoration: BoxDecoration(
                        color: spektaYellow,
                        borderRadius: BorderRadius.circular(30)),
                    child: const Text("Info Selengkapnya",
                        style: TextStyle(
                            color: Colors.white,
                            fontWeight: FontWeight.bold,
                            fontSize: 13)),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}