import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';

class AboutAcademyPage extends StatelessWidget {
  const AboutAcademyPage({super.key});

  // ============================================================
  // 🎨 PALET WARNA BARU SPEKTA GEN-Z (KONTRAS TINGGI, CLEAN, PREMIUM)
  // ============================================================
  static const Color primaryRed = Color(0xFFC5352C);       // Merah Spekta
  static const Color brightRed = Color(0xFFE53935);        // Aksen Merah Terang
  static const Color accentTeal = Color(0xFF2EA8AB);       // Teal Estetik
  static const Color pageBg = Color(0xFFF8FAFC);           // Slate 50 (Abu Terang Bersih)
  static const Color textDark = Color(0xFF0F172A);         // Slate 900
  static const Color textDarkVariant = Color(0xFF334155);  // Slate 700
  static const Color neutralGray = Color(0xFF64748B);      // Slate 500
  static const Color outlineVariant = Color(0xFFE2E8F0);   // Border Abu Halus

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
      backgroundColor: pageBg,
      extendBodyBehindAppBar: true, // Membuat AppBar transparan di atas gradasi
      appBar: AppBar(
        title: const Text(
          "Tentang Spekta",
          style: TextStyle(
            fontWeight: FontWeight.w900, 
            color: Colors.white, 
            fontSize: 18,
            letterSpacing: -0.5,
          ),
        ),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
        leading: Padding(
          padding: const EdgeInsets.all(8.0),
          child: CircleAvatar(
            backgroundColor: Colors.white.withOpacity(0.15),
            child: IconButton(
              icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white, size: 16),
              onPressed: () => Navigator.pop(context),
            ),
          ),
        ),
      ),
      body: Stack(
        children: [
          // 1. ELEMEN GRADIEN LENGKUNG DI BAGIAN ATAS
          Container(
            height: 250,
            width: double.infinity,
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                colors: [primaryRed, accentTeal],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.vertical(
                bottom: Radius.elliptical(250, 45),
              ),
            ),
          ),

          // 2. KONTEN UTAMA SCROLLABLE
          SingleChildScrollView(
            physics: const BouncingScrollPhysics(),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 110), // Memberikan ruang untuk overlap kartu utama

                // --- CENTRAL HERO CARD SPEKTA ACADEMY ---
                _buildCentralHeroCard(),
                const SizedBox(height: 20),

                // --- BENTO GRID: VISI ---
                _buildVisiCard(),
                const SizedBox(height: 14),

                // --- BENTO GRID: MISI ---
                _buildMisiCard(),
                const SizedBox(height: 20),

                // --- LOKASI KAMI CARD (INTEGRASI GOOGLE MAPS) ---
                _buildLocationCard(),
                const SizedBox(height: 40), // Jarak aman bawah
              ],
            ),
          ),
        ],
      ),
    );
  }

  // WIDGET KARTU UTAMA TENGAH (Central Hero Card)
  Widget _buildCentralHeroCard() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 20),
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: outlineVariant.withOpacity(0.5)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 16,
            offset: const Offset(0, 8),
          )
        ],
      ),
      child: Stack(
        clipBehavior: Clip.none,
        children: [
          Positioned(
            top: -24,
            right: -24,
            child: Container(
              width: 90,
              height: 90,
              decoration: BoxDecoration(
                color: accentTeal.withOpacity(0.06),
                shape: BoxShape.circle,
              ),
            ),
          ),
          Center(
            child: Column(
              children: [
                Container(
                  width: 60,
                  height: 60,
                  decoration: BoxDecoration(
                    color: primaryRed,
                    borderRadius: BorderRadius.circular(16),
                    boxShadow: [
                      BoxShadow(
                        color: primaryRed.withOpacity(0.2),
                        blurRadius: 8,
                        offset: const Offset(0, 4),
                      )
                    ],
                  ),
                  child: const Icon(Icons.school_rounded, color: Colors.white, size: 32),
                ),
                const SizedBox(height: 16),
                const Text(
                  "Spekta Academy",
                  style: TextStyle(
                    color: textDark,
                    fontSize: 22,
                    fontWeight: FontWeight.w900,
                    letterSpacing: -0.5,
                  ),
                ),
                const SizedBox(height: 3),
                const Text(
                  "BIMBINGAN BELAJAR TRANSFORMATIF",
                  style: TextStyle(
                    color: accentTeal,
                    fontSize: 10.5,
                    fontWeight: FontWeight.w800,
                    letterSpacing: 1.2,
                  ),
                ),
                const SizedBox(height: 16),
                Container(
                  width: 44,
                  height: 3.5,
                  decoration: BoxDecoration(
                    color: accentTeal,
                    borderRadius: BorderRadius.circular(99),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // KARTU VISI BERGAYA BENTO (DENGAN SOROTAN KATA KUNCI)
  Widget _buildVisiCard() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 20),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: outlineVariant.withOpacity(0.4)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 12,
            offset: const Offset(0, 4),
          )
        ],
      ),
      child: IntrinsicHeight(
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Container(
              width: 4,
              decoration: BoxDecoration(
                color: accentTeal,
                borderRadius: BorderRadius.circular(99),
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(6),
                        decoration: BoxDecoration(
                          color: accentTeal.withOpacity(0.12),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: const Icon(Icons.visibility_rounded, color: accentTeal, size: 16),
                      ),
                      const SizedBox(width: 10),
                      const Text(
                        "Visi",
                        style: TextStyle(
                          color: textDark,
                          fontSize: 16,
                          fontWeight: FontWeight.w900,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  RichText(
                    text: const TextSpan(
                      style: TextStyle(
                        fontSize: 14.5,
                        color: textDarkVariant,
                        height: 1.6,
                        fontFamily: 'Montserrat',
                        fontWeight: FontWeight.w500,
                      ),
                      children: [
                        TextSpan(text: "Menjadi bimbingan belajar transformatif yang mengintegrasikan "),
                        TextSpan(
                          text: "kreativitas",
                          style: TextStyle(color: accentTeal, fontWeight: FontWeight.bold),
                        ),
                        TextSpan(text: " dan "),
                        TextSpan(
                          text: "teknologi",
                          style: TextStyle(color: primaryRed, fontWeight: FontWeight.bold),
                        ),
                        TextSpan(text: " untuk melahirkan generasi cerdas, berkarakter, dan mencintai proses belajar."),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // KARTU MISI BERGAYA BENTO (DENGAN BULLET CHECK TEAL)
  Widget _buildMisiCard() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 20),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: outlineVariant.withOpacity(0.4)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 12,
            offset: const Offset(0, 4),
          )
        ],
      ),
      child: IntrinsicHeight(
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Container(
              width: 4,
              decoration: BoxDecoration(
                color: primaryRed,
                borderRadius: BorderRadius.circular(99),
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(6),
                        decoration: BoxDecoration(
                          color: primaryRed.withOpacity(0.12),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: const Icon(Icons.rocket_launch_rounded, color: primaryRed, size: 16),
                      ),
                      const SizedBox(width: 10),
                      const Text(
                        "Misi",
                        style: TextStyle(
                          color: textDark,
                          fontSize: 16,
                          fontWeight: FontWeight.w900,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),
                  _buildMissionItem("Menyediakan kurikulum adaptif yang dipersonalisasi untuk setiap gaya belajar siswa."),
                  const SizedBox(height: 12),
                  _buildMissionItem("Membangun ekosistem belajar digital yang interaktif dan menyenangkan."),
                  const SizedBox(height: 12),
                  _buildMissionItem("Mendorong penguatan karakter dan soft-skills melalui mentoring berkelanjutan."),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // KARTU LOKASI DENGAN INTEGRASI GOOGLE MAPS
  Widget _buildLocationCard() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: InkWell(
        onTap: _launchMaps,
        borderRadius: BorderRadius.circular(20),
        child: Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: outlineVariant.withOpacity(0.4)),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.015),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: primaryRed.withOpacity(0.08),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Icon(Icons.location_on_rounded, color: primaryRed, size: 22),
              ),
              const SizedBox(width: 14),
              const Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      "Spekta Academy Toba",
                      style: TextStyle(fontSize: 14, fontWeight: FontWeight.w900, color: textDark),
                    ),
                    SizedBox(height: 3),
                    Text(
                      "Ketuk untuk rute di Google Maps",
                      style: TextStyle(fontSize: 10.5, color: neutralGray, fontWeight: FontWeight.w600),
                    ),
                  ],
                ),
              ),
              const Icon(Icons.arrow_forward_ios_rounded, color: neutralGray, size: 14),
            ],
          ),
        ),
      ),
    );
  }

  // Helper widget untuk list Misi menggunakan bullet check teal modern
  Widget _buildMissionItem(String text) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          padding: const EdgeInsets.all(4),
          decoration: BoxDecoration(
            color: accentTeal.withOpacity(0.12),
            shape: BoxShape.circle,
          ),
          child: const Icon(
            Icons.check_rounded,
            color: accentTeal,
            size: 12,
          ),
        ),
        const SizedBox(width: 14),
        Expanded(
          child: Text(
            text,
            style: const TextStyle(
              fontSize: 13,
              color: textDarkVariant,
              height: 1.5,
              fontWeight: FontWeight.w600,
            ),
          ),
        ),
      ],
    );
  }
}