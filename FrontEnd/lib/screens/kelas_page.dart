import 'package:flutter/material.dart';
import 'class_detail_page.dart';

class KelasPage extends StatelessWidget {
  final String token;
  final Map userData;

  const KelasPage({super.key, required this.token, required this.userData});

  final Color spektaRed = const Color(0xFF990000);
  final Color spektaYellow = const Color(0xFFF1B401);
  final Color spektaDark = const Color(0xFF1A1A1A);

  @override
  Widget build(BuildContext context) {
    final List<Map<String, dynamic>> programs = [
      {
        "id": 1,
        "name": "CALON ABDI NEGARA",
        "image": "assets/images/abdi_negara.png",
        "tag": "INTENSIF",
        "subtitle": "TNI • POLRI • SEKDIN"
      },
      {
        "id": 2,
        "name": "PTN & UNHAN",
        "image": "assets/images/ptn_unhan.png",
        "tag": "AKADEMIK",
        "subtitle": "PERSIAPAN MASUK KAMPUS IMPIAN"
      },
      {
        "id": 3,
        "name": "SMA & SMP REGULER",
        "image": "assets/images/reguler.png",
        "tag": "REGULER",
        "subtitle": "KURSUS HARIAN SISWA"
      },
      {
        "id": 4,
        "name": "SMA FAVORIT",
        "image": "assets/images/favorit.png",
        "tag": "PRESTASI",
        "subtitle": "DEL • TN • MATAULI • SOPOSURUNG"
      },
    ];

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: CustomScrollView(
        slivers: [
          // HEADER MODERN YANG LEBIH TIPIS
          SliverAppBar(
            expandedHeight: 85.0, // <-- DIPERKECIL (Sebelumnya 120)
            floating: false,
            pinned: true,
            elevation: 0,
            backgroundColor: spektaRed,
            flexibleSpace: FlexibleSpaceBar(
              titlePadding: const EdgeInsets.only(left: 20, bottom: 14), // Sesuaikan jarak teks
              title: const Text(
                "Pilih Program Kelas",
                style: TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                  fontSize: 18,
                ),
              ),
              background: Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [spektaRed, const Color(0xFF660000)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                ),
              ),
            ),
          ),

          // LIST PROGRAM
          SliverPadding(
            padding: const EdgeInsets.fromLTRB(20, 20, 20, 100),
            sliver: SliverList(
              delegate: SliverChildBuilderDelegate(
                (context, index) => _buildProgramCard(context, programs[index]),
                childCount: programs.length,
              ),
            ),
          ),
        ],
      ),
    );
  }

  // --- Widget Card tetap sama seperti sebelumnya agar konsisten ---
  Widget _buildProgramCard(BuildContext context, Map<String, dynamic> item) {
    return Container(
      margin: const EdgeInsets.only(bottom: 25),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.06),
            blurRadius: 20,
            offset: const Offset(0, 10),
          )
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Stack(
            children: [
              ClipRRect(
                borderRadius: const BorderRadius.vertical(top: Radius.circular(28)),
                child: Image.asset(
                  item['image'],
                  height: 180, // Perkecil sedikit tinggi gambar agar tidak terlalu panjang
                  width: double.infinity,
                  fit: BoxFit.cover,
                ),
              ),
              Positioned(
                top: 15,
                right: 15,
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.9),
                    borderRadius: BorderRadius.circular(15),
                  ),
                  child: Text(item['tag'], style: TextStyle(color: spektaRed, fontWeight: FontWeight.bold, fontSize: 10)),
                ),
              ),
            ],
          ),
          Padding(
            padding: const EdgeInsets.all(20.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(item['subtitle'], style: const TextStyle(color: Colors.grey, fontSize: 11, fontWeight: FontWeight.w600)),
                const SizedBox(height: 5),
                Text(item['name'], style: TextStyle(color: spektaDark, fontSize: 20, fontWeight: FontWeight.w900)),
                const SizedBox(height: 15),
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  decoration: BoxDecoration(
                    gradient: LinearGradient(colors: [spektaYellow, const Color(0xFFD49E00)]),
                    borderRadius: BorderRadius.circular(15),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: const [
                      Text("Info Selengkapnya", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                      SizedBox(width: 8),
                      Icon(Icons.arrow_forward_rounded, color: Colors.white, size: 18),
                    ],
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