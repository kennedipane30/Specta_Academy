import 'package:flutter/material.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;

// --- IMPORT HALAMAN FITUR (PASTIKAN NAMA FILE DAN CLASS SESUAI) ---
import 'fitur/about_academy_page.dart';
import 'fitur/support_center_page.dart';
import 'fitur/materials_page.dart';
import 'fitur/question_sharing_page.dart';
import 'fitur/dedicated_tutor_page.dart';
import 'fitur/consultation_page.dart';
import 'pendaftaran_kelas_promo_page.dart';

class HomePage extends StatefulWidget {
  final String userName;
  final String token;
  final Map userData;

  const HomePage({
    super.key, 
    required this.userName, 
    required this.token, 
    required this.userData
  });

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  final Color spektaRed = const Color(0xFF990000);
  
  List galeriData = [];
  List promoData = []; 
  List announcementData = []; 
  
  late PageController _galeriController;
  int _currentGaleriPage = 0;
  Timer? _galeriTimer;

  @override
  void initState() {
    super.initState();
    _galeriController = PageController();
    fetchAnnouncements();
    fetchGaleri();
    fetchPromos(); 
  }

  @override
  void dispose() {
    _galeriTimer?.cancel();
    _galeriController.dispose();
    super.dispose();
  }

  // --- AMBIL DATA DARI API ---

  Future<void> fetchAnnouncements() async {
    try {
      final response = await http.get(Uri.parse('http://10.0.2.2:8000/api/announcements'));
      if (response.statusCode == 200) {
        setState(() => announcementData = jsonDecode(response.body)['data'] ?? []);
      }
    } catch (e) { debugPrint("Error Announcement: $e"); }
  }

  Future<void> fetchPromos() async {
    try {
      final response = await http.get(Uri.parse('http://10.0.2.2:8000/api/promos'));
      if (response.statusCode == 200) {
        setState(() => promoData = jsonDecode(response.body)['data'] ?? []);
      }
    } catch (e) { debugPrint("Error Promo: $e"); }
  }

  Future<void> fetchGaleri() async {
    try {
      final response = await http.get(Uri.parse('http://10.0.2.2:8000/api/galeri'));
      if (response.statusCode == 200) {
        setState(() => galeriData = jsonDecode(response.body)['data'] ?? []);
      }
    } catch (e) { debugPrint("Error Galeri: $e"); }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SingleChildScrollView(
        child: Column(
          children: [
            _buildHeader(),
            
            // 1. PENGUMUMAN TERKINI
            if (announcementData.isNotEmpty) _buildAnnouncementSection(),

            // 2. GALERI SLIDER
            if (galeriData.isNotEmpty) _buildGallerySlider(),

            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 25.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const SizedBox(height: 35), 
                  const Text("Layanan Spekta", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18, letterSpacing: -0.5)),
                  const SizedBox(height: 25),
                  
                  // 3. MENU UTAMA (6 ICON)
                  _buildMainMenuGrid(),

                  const SizedBox(height: 35),

                  // 4. PROMO / PENAWARAN
                  if (promoData.isNotEmpty) ...[
                    const Text("Penawaran Spesial", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
                    const SizedBox(height: 15),
                    _buildPromoSlider(),
                  ],
                  const SizedBox(height: 120),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // --- WIDGET GRID MENU UTAMA ---

  Widget _buildMainMenuGrid() {
    // List Menu tanpa menggunakan 'const' agar fleksibel
    final List<Map<String, dynamic>> homeMenus = [
      {'title': 'Materi Belajar', 'icon': Icons.menu_book_rounded, 'color': Colors.orange, 'page': const MaterialsPage()},
      {'title': 'Dedicated Tutor', 'icon': Icons.person_search_rounded, 'color': Colors.indigo, 'page': const DedicatedTutorPage()},
      {'title': 'Bank Soal', 'icon': Icons.history_edu_rounded, 'color': Colors.green, 'page': const QuestionSharingPage()},
      {'title': 'Tentang Spekta', 'icon': Icons.info_outline_rounded, 'color': Colors.blue, 'page': const AboutAcademyPage()},
      {'title': 'Konsultasi', 'icon': Icons.forum_rounded, 'color': Colors.purple, 'page': const ConsultationPage()},
      {'title': 'Pusat Bantuan', 'icon': Icons.contact_support_rounded, 'color': Colors.blueGrey, 'page': const SupportCenterPage()},
    ];

    return GridView.builder(
      shrinkWrap: true,
      padding: EdgeInsets.zero,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 3, 
        mainAxisSpacing: 25,     
        crossAxisSpacing: 15, 
        childAspectRatio: 0.85,
      ),
      itemCount: 6, 
      itemBuilder: (context, index) {
        var item = homeMenus[index];
        return InkWell(
          borderRadius: BorderRadius.circular(22),
          onTap: () => Navigator.push(context, MaterialPageRoute(builder: (c) => item['page'])),
          child: Column(
            children: [
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: item['color'].withOpacity(0.12),
                  borderRadius: BorderRadius.circular(22),
                ),
                child: Icon(item['icon'], color: item['color'], size: 30),
              ),
              const SizedBox(height: 10),
              Text(
                item['title'], 
                textAlign: TextAlign.center, 
                style: const TextStyle(fontSize: 10.5, fontWeight: FontWeight.w800, color: Colors.black87),
              ),
            ],
          ),
        );
      },
    );
  }

  // --- SUB WIDGETS ---

  Widget _buildHeader() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.only(top: 60, left: 25, right: 25, bottom: 30),
      decoration: BoxDecoration(
        color: spektaRed, 
        borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(35), bottomRight: Radius.circular(35))
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text("Selamat Datang,", style: TextStyle(color: Colors.white70, fontSize: 13, fontWeight: FontWeight.w600)),
              Text(widget.userName, style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w900)),
            ],
          ),
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(color: Colors.white12, borderRadius: BorderRadius.circular(12)),
            child: const Icon(Icons.notifications_none_rounded, color: Colors.white, size: 28),
          )
        ],
      ),
    );
  }

  Widget _buildAnnouncementSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Padding(
          padding: EdgeInsets.only(left: 25, top: 25, bottom: 10),
          child: Text("Info Terkini", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 18)),
        ),
        SizedBox(
          height: 220,
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 20),
            itemCount: announcementData.length,
            itemBuilder: (context, index) {
              var news = announcementData[index];
              return Container(
                width: 280,
                margin: const EdgeInsets.only(right: 15, bottom: 10),
                decoration: BoxDecoration(
                  color: Colors.white, 
                  borderRadius: BorderRadius.circular(25), 
                  boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 10)]
                ),
                child: Column(
                  children: [
                    ClipRRect(
                      borderRadius: const BorderRadius.vertical(top: Radius.circular(25)),
                      child: Image.network(
                        'http://10.0.2.2:8000/storage/${news['image']}', 
                        height: 120, width: double.infinity, fit: BoxFit.cover,
                        errorBuilder: (c,e,s) => Container(height: 120, color: Colors.grey[200]),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.all(12),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(news['title'], maxLines: 1, style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 14)),
                          const SizedBox(height: 4),
                          Text(news['description'], maxLines: 2, style: const TextStyle(color: Colors.grey, fontSize: 11, height: 1.3)),
                        ],
                      ),
                    )
                  ],
                ),
              );
            },
          ),
        ),
      ],
    );
  }

  Widget _buildGallerySlider() {
    return Container(
      height: 180,
      margin: const EdgeInsets.only(top: 15),
      child: PageView.builder(
        controller: _galeriController,
        itemCount: galeriData.length,
        itemBuilder: (context, index) {
          var item = galeriData[index];
          String imgUrl = 'http://10.0.2.2:8000/view-galeri/${item['foto'].split('/').last}';
          return Container(
            margin: const EdgeInsets.symmetric(horizontal: 20),
            decoration: BoxDecoration(borderRadius: BorderRadius.circular(20), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10)]),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(20), 
              child: Image.network(imgUrl, fit: BoxFit.cover, errorBuilder: (c,e,s) => Container(color: Colors.grey[100])),
            ),
          );
        },
      ),
    );
  }

  Widget _buildPromoSlider() {
    return SizedBox(
      height: 150,
      child: PageView.builder(
        itemCount: promoData.length,
        itemBuilder: (context, index) {
          var p = promoData[index];
          String imgUrl = 'http://10.0.2.2:8000/view-galeri/${p['image_banner'].split('/').last}';
          return InkWell(
            onTap: () => Navigator.push(context, MaterialPageRoute(builder: (c) => PendaftaranKelasPromoPage(classId: p['class_id'], className: p['class_model']['nama_program'], token: widget.token, userData: widget.userData))),
            child: Container(
              margin: const EdgeInsets.only(right: 15),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(20), 
                image: DecorationImage(image: NetworkImage(imgUrl), fit: BoxFit.cover)
              ),
            ),
          );
        },
      ),
    );
  }
}