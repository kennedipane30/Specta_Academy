import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';

class AboutAcademyPage extends StatelessWidget {
  const AboutAcademyPage({super.key});

  // Fungsi untuk membuka link Google Maps
  Future<void> _launchMaps() async {
    final Uri url = Uri.parse(
        'https://www.google.com/maps/place/Spekta+Academy+Toba/@2.3296459,99.0524054,17z/data=!3m1!4b1!4m6!3m5!1s0x302e05ee072c557f:0x92d46a9902dbf83e!8m2!3d2.3296459!4d99.0549803!16s%2Fg%2F11wfzfyz33?entry=ttu&g_ep=EgoyMDI2MDYwMS4wIKXMDSoASAFQAw%3D%3D');
    if (!await launchUrl(url, mode: LaunchMode.externalApplication)) {
      debugPrint('Tidak dapat membuka link peta');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC), // Background abu-abu sangat muda agar card menonjol
      appBar: AppBar(
        title: const Text(
          "Tentang Spekta",
          style: TextStyle(fontWeight: FontWeight.w900, color: Colors.white, fontSize: 20),
        ),
        backgroundColor: const Color(0xFF990000),
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: SingleChildScrollView(
        physics: const BouncingScrollPhysics(),
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // --- HEADER BANNER ---
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(vertical: 30, horizontal: 20),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFFB30000), Color(0xFF800000)], // Gradasi merah
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: const Color(0xFF990000).withOpacity(0.3),
                    blurRadius: 15,
                    offset: const Offset(0, 8),
                  ),
                ],
              ),
              child: const Column(
                children: [
                  Icon(Icons.school_rounded, color: Colors.white, size: 50),
                  SizedBox(height: 12),
                  Text(
                    "SPEKTA ACADEMY",
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 24,
                      fontWeight: FontWeight.w900,
                      letterSpacing: 1.5,
                    ),
                  ),
                  SizedBox(height: 6),
                  Text(
                    "Bimbingan Belajar Transformatif",
                    style: TextStyle(
                      color: Colors.white70,
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      letterSpacing: 1.0,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 30),

            // --- VISI ---
            const Text(
              "Visi Kami",
              style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Color(0xFF1E293B)),
            ),
            const SizedBox(height: 12),
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(15),
                border: const Border(left: BorderSide(color: Color(0xFF990000), width: 4)),
                boxShadow: [
                  BoxShadow(
                    color: Colors.grey.withOpacity(0.1),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: const Text(
                "\"Menjadi bimbingan belajar transformatif yang mengintegrasikan kreativitas dan teknologi untuk melahirkan generasi cerdas, berkarakter, dan mencintai proses belajar.\"",
                style: TextStyle(
                  fontSize: 14,
                  color: Color(0xFF475569),
                  height: 1.6,
                  fontStyle: FontStyle.italic,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ),
            const SizedBox(height: 30),

            // --- MISI ---
            const Text(
              "Misi Kami",
              style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Color(0xFF1E293B)),
            ),
            const SizedBox(height: 12),
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(15),
                boxShadow: [
                  BoxShadow(
                    color: Colors.grey.withOpacity(0.1),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Column(
                children: [
                  _buildMissionItem("Menyelenggarakan metode pembelajaran berbasis active learning dan gamification yang menyenangkan serta mudah dipahami."),
                  const Divider(height: 24, color: Color(0xFFF1F5F9)),
                  _buildMissionItem("Mengasah kemampuan berpikir kritis (critical thinking) dan pemecahan masalah melalui pendekatan yang kreatif."),
                  const Divider(height: 24, color: Color(0xFFF1F5F9)),
                  _buildMissionItem("Memanfaatkan teknologi digital terkini untuk menyediakan fasilitas belajar yang interaktif dan adaptif."),
                  const Divider(height: 24, color: Color(0xFFF1F5F9)),
                  _buildMissionItem("Membentuk karakter siswa yang percaya diri, inovatif, dan berintegritas tinggi."),
                ],
              ),
            ),
            const SizedBox(height: 30),

            // --- LOKASI ---
            const Text(
              "Lokasi Kami",
              style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Color(0xFF1E293B)),
            ),
            const SizedBox(height: 12),
            InkWell(
              onTap: _launchMaps,
              borderRadius: BorderRadius.circular(15),
              child: Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(15),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.grey.withOpacity(0.1),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: const Color(0xFFFEF2F2),
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: const Icon(Icons.location_on_rounded, color: Color(0xFF990000), size: 28),
                    ),
                    const SizedBox(width: 16),
                    const Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            "Spekta Academy Toba",
                            style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: Color(0xFF1E293B)),
                          ),
                          SizedBox(height: 4),
                          Text(
                            "Ketuk untuk melihat rute di Google Maps",
                            style: TextStyle(fontSize: 12, color: Color(0xFF64748B), fontWeight: FontWeight.w500),
                          ),
                        ],
                      ),
                    ),
                    const Icon(Icons.arrow_forward_ios_rounded, color: Color(0xFFCBD5E1), size: 16),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 40), // Jarak bawah
          ],
        ),
      ),
    );
  }

  // Helper widget untuk list Misi
  Widget _buildMissionItem(String text) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Padding(
          padding: EdgeInsets.only(top: 3),
          child: Icon(
            Icons.check_circle_rounded,
            color: Color(0xFF990000),
            size: 18,
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: Text(
            text,
            style: const TextStyle(
              fontSize: 13,
              color: Color(0xFF334155),
              height: 1.5,
              fontWeight: FontWeight.w500,
            ),
          ),
        ),
      ],
    );
  }
}