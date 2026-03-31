import 'package:flutter/material.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;

class HomePage extends StatefulWidget {
  final String userName;
  const HomePage({super.key, required this.userName});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  final Color spektaRed = const Color(0xFF990000);
  
  List galeriData = [];
  late PageController _pageController;
  int _currentPage = 0;
  Timer? _timer;

  @override
  void initState() {
    super.initState();
    _pageController = PageController(initialPage: 0);
    fetchGaleri();
  }

  @override
  void dispose() {
    _timer?.cancel();
    _pageController.dispose();
    super.dispose();
  }

  Future<void> fetchGaleri() async {
    try {
      // Mengambil data galeri dari API Laravel
      final response = await http.get(Uri.parse('http://10.0.2.2:8000/api/galeri'));
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        setState(() {
          // Mengambil array dari 'data' sesuai format JSON kita
          galeriData = data['data'] ?? [];
        });
        if (galeriData.isNotEmpty) {
          _startAutoSlide();
        }
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
        _pageController.animateToPage(
          _currentPage,
          duration: const Duration(milliseconds: 900),
          curve: Curves.easeInOut,
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
            // --- HEADER MERAH ---
            Container(
              width: double.infinity,
              padding: const EdgeInsets.only(top: 60, left: 25, right: 25, bottom: 35),
              decoration: BoxDecoration(
                color: spektaRed,
                borderRadius: const BorderRadius.only(
                  bottomLeft: Radius.circular(35),
                  bottomRight: Radius.circular(35),
                ),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text("Hai, ${widget.userName}", 
                    style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold)),
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

                  // --- 2. BANNER PROMO ---
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                        colors: [Color(0xFF990000), Color(0xFFD32F2F)],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Row(
                      children: [
                        const Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text("TIM GERCEP SIAPIN UTBK", 
                                style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14)),
                              SizedBox(height: 5),
                              Text("Paket Hanya 900rb", 
                                style: TextStyle(color: Colors.yellow, fontSize: 18, fontWeight: FontWeight.w900)),
                              SizedBox(height: 10),
                              Text("Cuma Hari Ini!", style: TextStyle(color: Colors.white, fontSize: 10)),
                            ],
                          ),
                        ),
                        Image.network('https://cdn-icons-png.flaticon.com/512/3429/3429153.png', height: 70),
                      ],
                    ),
                  ),

                  const SizedBox(height: 35),

                  // --- 3. SECTION GALERI KEGIATAN (DIPERBAIKI) ---
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
                          
                          // SOLUSI: Menggunakan IP 10.0.2.2 untuk emulator dan folder storage
                          String imageUrl = 'http://10.0.2.2:8000/storage/${item['foto']}';
                          
                          return Column(
                            children: [
                              Text(item['judul'], 
                                style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFF990000)),
                                textAlign: TextAlign.center),
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
                                      // Handler saat loading
                                      loadingBuilder: (context, child, loadingProgress) {
                                        if (loadingProgress == null) return child;
                                        return const Center(child: CircularProgressIndicator(color: Color(0xFF990000)));
                                      },
                                      // HANDLER ERROR AGAR TIDAK BLANK ABU-ABU
                                      errorBuilder: (context, error, stackTrace) {
                                        debugPrint("Gagal muat gambar dari: $imageUrl");
                                        return Container(
                                          color: Colors.grey[200],
                                          child: const Center(child: Column(
                                            mainAxisAlignment: MainAxisAlignment.center,
                                            children: [
                                              Icon(Icons.broken_image, size: 40, color: Colors.grey),
                                              Text("Gambar gagal dimuat", style: TextStyle(fontSize: 10, color: Colors.grey)),
                                            ],
                                          )),
                                        );
                                      },
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
          decoration: BoxDecoration(
            color: color.withOpacity(0.1),
            borderRadius: BorderRadius.circular(18),
          ),
          child: Icon(icon, color: color, size: 30),
        ),
        const SizedBox(height: 8),
        Text(label, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: Colors.black87)),
      ],
    );
  }
}