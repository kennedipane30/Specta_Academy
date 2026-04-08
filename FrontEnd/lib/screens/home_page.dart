import 'package:flutter/material.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;
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
  List materiData = []; 
  bool isLoadingMateri = true;

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
    fetchMateri(); 
  }

  @override
  void dispose() {
    _galeriTimer?.cancel();
    _galeriController.dispose();
    _promoController.dispose();
    super.dispose();
  }

  // --- AMBIL DATA DARI API ---

  Future<void> fetchMateri() async {
    try {
      // DEBUG: Sangat penting! Cek isi userData di console VS Code
      debugPrint("DEBUG FULL USERDATA: ${widget.userData}");

      // Berdasarkan api.php -> user.load('student'), id_kelas biasanya ada di sini:
      var classId = widget.userData['student']?['class_id'] ?? 
                    widget.userData['class_id'] ?? 
                    widget.userData['id_kelas'];

      debugPrint("ID KELAS TERDETEKSI: $classId");

      if (classId == null) {
        setState(() => isLoadingMateri = false);
        return;
      }

      final response = await http.get(
        Uri.parse('http://10.0.2.2:8000/api/materials?class_id=$classId'),
        headers: {
          'Authorization': 'Bearer ${widget.token}',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        setState(() {
          materiData = jsonDecode(response.body)['data'] ?? [];
          isLoadingMateri = false;
        });
      } else {
        setState(() => isLoadingMateri = false);
      }
    } catch (e) {
      debugPrint("Error Fetch Materi: $e");
      setState(() => isLoadingMateri = false);
    }
  }

  Future<void> fetchPromos() async {
    try {
      final response = await http.get(Uri.parse('http://10.0.2.2:8000/api/promos'));
      if (response.statusCode == 200) {
        setState(() => promoData = jsonDecode(response.body)['data'] ?? []);
      }
    } catch (e) {}
  }

  Future<void> fetchGaleri() async {
    try {
      final response = await http.get(Uri.parse('http://10.0.2.2:8000/api/galeri'));
      if (response.statusCode == 200) {
        setState(() => galeriData = jsonDecode(response.body)['data'] ?? []);
        if (galeriData.isNotEmpty) _startAutoSlide();
      }
    } catch (e) {}
  }

  void _startAutoSlide() {
    _galeriTimer = Timer.periodic(const Duration(seconds: 5), (timer) {
      if (_galeriController.hasClients && galeriData.isNotEmpty) {
        _currentGaleriPage = (_currentGaleriPage + 1) % galeriData.length;
        _galeriController.animateToPage(_currentGaleriPage, 
            duration: const Duration(milliseconds: 800), curve: Curves.easeInOut);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    // Ambil nama program (Misal: CALON ABDI NEGARA)
    String namaProgram = widget.userData['student']?['class_model']?['nama_program'] ?? 
                         widget.userData['nama_program'] ?? "Layanan Spekta";

    return Scaffold(
      backgroundColor: Colors.white,
      body: RefreshIndicator(
        onRefresh: () async {
          fetchMateri();
          fetchGaleri();
          fetchPromos();
        },
        child: SingleChildScrollView(
          child: Column(
            children: [
              _buildHeader(),

              // --- 1. GALERI (PALING ATAS) ---
              if (galeriData.isNotEmpty) _buildGallerySlider(),

              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 25.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // --- 2. MATERI (TENGAH - RUANGGURU STYLE) ---
                    Text(namaProgram, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
                    const SizedBox(height: 15),
                    _buildMateriGrid(),

                    const SizedBox(height: 35),

                    // --- 3. PROMO (BAWAH) ---
                    if (promoData.isNotEmpty) ...[
                      const Text("Promo Spesial Hari Ini", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                      const SizedBox(height: 15),
                      _buildPromoSlider(),
                    ],
                    const SizedBox(height: 30),
                  ],
                ),
              ),
            ],
          ),
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
          Text("Hai, ${widget.userName}", style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold)),
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
              boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 5)]
            ),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(15),
              child: Image.network(imgUrl, fit: BoxFit.cover, 
                errorBuilder: (c, e, s) => const Center(child: Icon(Icons.broken_image, color: Colors.grey))),
            ),
          );
        },
      ),
    );
  }

  Widget _buildMateriGrid() {
    if (isLoadingMateri) return const Center(child: CircularProgressIndicator());

    // Gabungkan Materi DB + Item Statis (Latihan/Tryout)
    List displayItems = List.from(materiData);
    displayItems.add({'title': 'Latihan Soal'});
    displayItems.add({'title': 'Try-Out'});

    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 4, mainAxisSpacing: 20, crossAxisSpacing: 10, childAspectRatio: 0.75
      ),
      itemCount: displayItems.length,
      itemBuilder: (context, index) {
        var item = displayItems[index];
        return _buildSubjectIcon(item['title']);
      },
    );
  }

  Widget _buildSubjectIcon(String title) {
    IconData icon = Icons.book;
    Color color = Colors.blue;

    // Logika Ikon & Warna Bergaya Ruangguru
    if (title.contains("Matematika")) { icon = Icons.calculate; color = Colors.blue; }
    else if (title.contains("TIU") || title.contains("Psikotes")) { icon = Icons.psychology; color = Colors.orange; }
    else if (title.contains("Fisika")) { icon = Icons.bolt; color = Colors.pink; }
    else if (title.contains("Inggris")) { icon = Icons.language; color = Colors.indigo; }
    else if (title.contains("Biologi")) { icon = Icons.biotech; color = Colors.green; }
    else if (title.contains("Latihan")) { icon = Icons.edit_note; color = Colors.redAccent; }
    else if (title.contains("Try-Out")) { icon = Icons.emoji_events; color = Colors.amber; }

    return Column(
      children: [
        Container(
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: color.withOpacity(0.12), 
            borderRadius: BorderRadius.circular(18)
          ),
          child: Icon(icon, color: color, size: 28),
        ),
        const SizedBox(height: 8),
        Text(
          title.replaceAll("Materi ", ""), 
          textAlign: TextAlign.center, 
          style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold), 
          maxLines: 2,
        ),
      ],
    );
  }

  Widget _buildPromoSlider() {
    return SizedBox(
      height: 140,
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