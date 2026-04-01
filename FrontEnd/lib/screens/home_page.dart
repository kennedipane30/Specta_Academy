import 'package:flutter/material.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'pendaftaran_kelas_promo_page.dart'; // IMPORT BARU: Halaman khusus pendaftaran promo

class HomePage extends StatefulWidget {
  final String userName;
  final String token; // TAMBAHKAN: Token untuk kirim ke page selanjutnya
  final Map userData; // TAMBAHKAN: Data user untuk page selanjutnya

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
  late PageController _pageController;
  late PageController _promoController; 
  int _currentPage = 0;
  Timer? _timer;
  Timer? _promoTimer;

  @override
  void initState() {
    super.initState();
    _pageController = PageController(initialPage: 0);
    _promoController = PageController(initialPage: 0);
    fetchGaleri();
    fetchPromos(); 
  }

  @override
  void dispose() {
    _timer?.cancel();
    _promoTimer?.cancel();
    _pageController.dispose();
    _promoController.dispose();
    super.dispose();
  }

  // Ambil Data Promo dari Laravel
  Future<void> fetchPromos() async {
    try {
      final response = await http.get(Uri.parse('http://10.0.2.2:8000/api/promos'));
      if (response.statusCode == 200) {
        setState(() {
          promoData = jsonDecode(response.body)['data'] ?? [];
        });
      }
    } catch (e) {
      debugPrint("Error fetching promos: $e");
    }
  }

  Future<void> fetchGaleri() async {
    try {
      final response = await http.get(Uri.parse('http://10.0.2.2:8000/api/galeri'));
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        setState(() {
          galeriData = data['data'] ?? [];
        });
        if (galeriData.isNotEmpty) _startAutoSlide();
      }
    } catch (e) {
      debugPrint("Error fetching galeri: $e");
    }
  }

  void _startAutoSlide() {
    _timer = Timer.periodic(const Duration(seconds: 10), (Timer timer) {
      if (_currentPage < galeriData.length - 1) {
        _currentPage++;
      } else {
        _currentPage = 0;
      }
      if (_pageController.hasClients) {
        _pageController.animateToPage(_currentPage, duration: const Duration(milliseconds: 900), curve: Curves.easeInOut);
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
            // --- HEADER MERAH ---
            Container(
              width: double.infinity,
              padding: const EdgeInsets.only(top: 60, left: 25, right: 25, bottom: 35),
              decoration: BoxDecoration(
                color: spektaRed,
                borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(35), bottomRight: Radius.circular(35)),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text("Hai, ${widget.userName}", style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold)),
                  const Row(
                    children: [
                      Icon(Icons.notifications_none, color: Colors.white),
                      SizedBox(width: 15),
                      Icon(Icons.bookmark_border, color: Colors.white),
                    ],
                  )
                ],
              ),
            ),

            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 25.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // --- 1. LAYANAN SPEKTA ---
                  const Text("Layanan Spekta", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                  const SizedBox(height: 15),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      _buildMenuIcon(Icons.play_circle_fill, "Materi", Colors.purple),
                      _buildMenuIcon(Icons.edit_document, "Ujian", Colors.orange),
                      _buildMenuIcon(Icons.bolt, "Latihan", Colors.indigo),
                      _buildMenuIcon(Icons.emoji_events, "Try-Out", Colors.amber),
                    ],
                  ),

                  const SizedBox(height: 35),

                  // --- 2. SECTION PROMO DINAMIS ---
                  if (promoData.isNotEmpty) ...[
                    const Text("Promo Spesial Hari Ini", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                    const SizedBox(height: 15),
                    SizedBox(
                      height: 160,
                      child: PageView.builder(
                        controller: _promoController,
                        itemCount: promoData.length,
                        itemBuilder: (context, index) {
                          var p = promoData[index];
                          return InkWell(
                            onTap: () {
                              // MODIFIKASI: ARAHKAN KE HALAMAN PENDAFTARAN PROMO
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (context) => PendaftaranKelasPromoPage(
                                    classId: p['class_id'],
                                    className: p['class_model']['nama_program'],
                                    token: widget.token,
                                    userData: widget.userData,
                                  ),
                                ),
                              );
                            },
                            child: Container(
                              margin: const EdgeInsets.symmetric(horizontal: 5),
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(20),
                                image: DecorationImage(
                                  image: NetworkImage('http://10.0.2.2:8000/view-galeri/${p['image_banner'].split('/').last}'),
                                  fit: BoxFit.cover
                                ),
                                boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 5, offset: const Offset(0, 3))]
                              ),
                            ),
                          );
                        },
                      ),
                    ),
                  ],

                  const SizedBox(height: 35),

                  // --- 3. SECTION GALERI KEGIATAN ---
                  if (galeriData.isNotEmpty) ...[
                    const Center(child: Text("Kegiatan Spekta Terbaru", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16))),
                    const SizedBox(height: 15),
                    SizedBox(
                      height: 240,
                      child: PageView.builder(
                        controller: _pageController,
                        itemCount: galeriData.length,
                        onPageChanged: (index) => _currentPage = index,
                        itemBuilder: (context, index) {
                          var item = galeriData[index];
                          String imageUrl = 'http://10.0.2.2:8000/view-galeri/${item['foto'].split('/').last}';
                          return Column(
                            children: [
                              Text(item['judul'], style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFF990000)), textAlign: TextAlign.center),
                              const SizedBox(height: 12),
                              Expanded(
                                child: Container(
                                  margin: const EdgeInsets.symmetric(horizontal: 10),
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(20),
                                    boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 8, offset: const Offset(0, 4))],
                                  ),
                                  child: ClipRRect(
                                    borderRadius: BorderRadius.circular(20),
                                    child: Image.network(
                                      imageUrl,
                                      fit: BoxFit.cover,
                                      width: double.infinity,
                                      errorBuilder: (context, error, stackTrace) => Container(color: Colors.grey[200], child: const Icon(Icons.broken_image, size: 40, color: Colors.grey)),
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          );
                        },
                      ),
                    ),
                  ],
                  const SizedBox(height: 20),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMenuIcon(IconData icon, String label, Color color) {
    return Column(
      children: [
        Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(color: color.withOpacity(0.1), borderRadius: BorderRadius.circular(18)),
          child: Icon(icon, color: color, size: 30),
        ),
        const SizedBox(height: 8),
        Text(label, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: Colors.black87)),
      ],
    );
  }
}