import 'package:flutter/material.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;

// IMPORT HALAMAN FITUR
import 'fitur/tentang_spekta_page.dart';
import 'fitur/info_program_page.dart';
import 'fitur/hubungi_kami_page.dart';
import 'fitur/semua_fitur_page.dart';
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
  
  late PageController _galeriController;
  late PageController _promoController; 
  int _currentGaleriPage = 0;
  Timer? _galeriTimer;

  @override
  void initState() {
    super.initState();
    _galeriController = PageController();
    _promoController = PageController();
    
    fetchGaleri();
    fetchPromos(); 
  }

  @override
  void dispose() {
    _galeriTimer?.cancel();
    _galeriController.dispose();
    _promoController.dispose();
    super.dispose();
  }

  // --- AMBIL DATA DARI API ---

  Future<void> fetchPromos() async {
    try {
      final response = await http.get(Uri.parse('http://10.0.2.2:8000/api/promos'));
      if (response.statusCode == 200) {
        setState(() => promoData = jsonDecode(response.body)['data'] ?? []);
      }
    } catch (e) {
      debugPrint("Error Promo: $e");
    }
  }

  Future<void> fetchGaleri() async {
    try {
      final response = await http.get(Uri.parse('http://10.0.2.2:8000/api/galeri'));
      if (response.statusCode == 200) {
        setState(() => galeriData = jsonDecode(response.body)['data'] ?? []);
        if (galeriData.isNotEmpty) _startAutoSlide();
      }
    } catch (e) {
      debugPrint("Error Galeri: $e");
    }
  }

  void _startAutoSlide() {
    _galeriTimer = Timer.periodic(const Duration(seconds: 5), (timer) {
      if (_galeriController.hasClients && galeriData.isNotEmpty) {
        _currentGaleriPage = (_currentGaleriPage + 1) % galeriData.length;
        _galeriController.animateToPage(
          _currentGaleriPage, 
          duration: const Duration(milliseconds: 800), 
          curve: Curves.easeInOut
        );
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SingleChildScrollView(
        child: Column(
          children: [
            // --- HEADER ---
            _buildHeader(),

            // --- 1. GALERI (DI ATAS) ---
            if (galeriData.isNotEmpty) _buildGallerySlider(),

            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const SizedBox(height: 30), // JARAK DARI GALERI KE JUDUL IKON

                  // --- 2. MENU GRID (TENGAH) ---
                  const Text("Layanan Spekta", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
                  const SizedBox(height: 15),
                  _buildMainMenuGrid(),

                  const SizedBox(height: 30), // JARAK DARI IKON KE JUDUL PROMO (DISAMAKAN)

                  // --- 3. PROMO (BAWAH) ---
                  if (promoData.isNotEmpty) ...[
                    const Text("Promo Spesial Hari Ini", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                    const SizedBox(height: 15),
                    _buildPromoSlider(),
                  ],
                  const SizedBox(height: 40),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // --- SUB-WIDGET UI ---

  Widget _buildHeader() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.only(top: 60, left: 25, right: 25, bottom: 30),
      decoration: BoxDecoration(
        color: spektaRed,
        borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(30), bottomRight: Radius.circular(30)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text("Selamat Datang,", style: TextStyle(color: Colors.white70, fontSize: 13)),
              Text(widget.userName, style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold)),
            ],
          ),
          const Icon(Icons.notifications_none, color: Colors.white, size: 28),
        ],
      ),
    );
  }

  Widget _buildGallerySlider() {
    return Container(
      height: 180,
      margin: const EdgeInsets.only(top: 20),
      child: PageView.builder(
        controller: _galeriController,
        itemCount: galeriData.length,
        itemBuilder: (context, index) {
          var item = galeriData[index];
          String imgUrl = 'http://10.0.2.2:8000/view-galeri/${item['foto'].split('/').last}';
          return Container(
            margin: const EdgeInsets.symmetric(horizontal: 20),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(15), 
              color: Colors.grey[200],
              boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 8, offset: const Offset(0, 4))]
            ),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(15),
              child: Image.network(imgUrl, fit: BoxFit.cover, 
                errorBuilder: (c, e, s) => const Center(child: Icon(Icons.broken_image, size: 40, color: Colors.grey))),
            ),
          );
        },
      ),
    );
  }

  Widget _buildMainMenuGrid() {
    final List<Map<String, dynamic>> homeMenus = [
      {'title': 'Tentang Spekta', 'icon': Icons.info_outline, 'color': Colors.blue, 'page': const TentangSpektaPage()},
      {'title': 'Abdi Negara', 'icon': Icons.security, 'color': Colors.red, 'page': const InfoProgramPage(title: 'Abdi Negara')},
      {'title': 'PTN / UNHAN', 'icon': Icons.school, 'color': Colors.orange, 'page': const InfoProgramPage(title: 'PTN / UNHAN')},
      {'title': 'SMA Favorit', 'icon': Icons.star_outline, 'color': Colors.purple, 'page': const InfoProgramPage(title: 'SMA Favorit')},
      {'title': 'SMA/SMP Reguler', 'icon': Icons.book_outlined, 'color': Colors.green, 'page': const InfoProgramPage(title: 'SMP/SMA Reguler')},
      {'title': 'Lihat Semua', 'icon': Icons.apps, 'color': Colors.grey, 'page': const SemuaFiturPage()},
    ];

    return GridView.builder(
      shrinkWrap: true,
      padding: EdgeInsets.zero, // Menghilangkan padding bawaan grid
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 3, 
        mainAxisSpacing: 10,     
        crossAxisSpacing: 10, 
        childAspectRatio: 1.0,  // Diatur 1.0 agar kotak simetris dan rapat
      ),
      itemCount: 6, 
      itemBuilder: (context, index) {
        var item = homeMenus[index];
        return InkWell(
          onTap: () => Navigator.push(context, MaterialPageRoute(builder: (c) => item['page'])),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: item['color'].withOpacity(0.12),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Icon(item['icon'], color: item['color'], size: 30),
              ),
              const SizedBox(height: 8),
              Text(
                item['title'], 
                textAlign: TextAlign.center, 
                style: const TextStyle(fontSize: 10.5, fontWeight: FontWeight.bold, height: 1.2),
                maxLines: 2,
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildPromoSlider() {
    return SizedBox(
      height: 150,
      child: PageView.builder(
        controller: _promoController,
        itemCount: promoData.length,
        itemBuilder: (context, index) {
          var p = promoData[index];
          String imgUrl = 'http://10.0.2.2:8000/view-galeri/${p['image_banner'].split('/').last}';
          return InkWell(
            onTap: () {
               Navigator.push(context, MaterialPageRoute(builder: (c) => PendaftaranKelasPromoPage(
                classId: p['class_id'],
                className: p['class_model']['nama_program'],
                token: widget.token,
                userData: widget.userData,
              )));
            },
            child: Container(
              margin: const EdgeInsets.only(right: 12),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(15),
                image: DecorationImage(image: NetworkImage(imgUrl), fit: BoxFit.cover),
              ),
            ),
          );
        },
      ),
    );
  }
}