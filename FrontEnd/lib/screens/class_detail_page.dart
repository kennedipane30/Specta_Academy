import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:intl/intl.dart';

import '../services/auth_service.dart';
import 'payment_confirmation_page.dart';
import 'subject_list_page.dart'; 
import 'practice_subject_list_page.dart'; 
import 'tryout_detail_page.dart'; // ✨ Import halaman detail tryout

class ClassDetailPage extends StatefulWidget {
  final int classId;
  final String className;
  final String token;
  final Map userData;

  const ClassDetailPage({
    super.key,
    required this.classId,
    required this.className,
    required this.token,
    required this.userData,
  });

  @override
  State<ClassDetailPage> createState() => _ClassDetailPageState();
}

class _ClassDetailPageState extends State<ClassDetailPage> {
  String status = "none";
  int basePrice = 0;
  String description = "";
  List materi = [];
  List tryouts = [];
  List practiceQuestions = [];
  bool isLoading = true;

  final Color spektaRed = const Color(0xFF990000);
  final currency = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

  @override
  void initState() {
    super.initState();
    _fetchDetail();
  }

  // --- FUNGSI MAPPING GAMBAR LOKAL ---
  String _getLocalAsset() {
    int cid = int.tryParse(widget.classId.toString()) ?? 0;
    switch (cid) {
      case 1: return 'assets/images/abdi_negara.png';
      case 2: return 'assets/images/ptn_unhan.png';
      case 3: return 'assets/images/reguler.png';
      case 4: return 'assets/images/favorit.png';
      default: return 'assets/images/abdi_negara.png';
    }
  }

  Future<void> _fetchDetail() async {
    try {
      var resp = await AuthService.getClassContent(widget.classId, widget.token);
      if (resp.statusCode == 200) {
        var data = jsonDecode(resp.body);
        if (mounted) {
          setState(() {
            status = data['enroll_status'] ?? "none";
            materi = data['materi'] ?? [];
            tryouts = data['tryouts'] ?? [];
            practiceQuestions = data['practice_questions'] ?? []; 
            basePrice = int.tryParse(data['price'].toString()) ?? 0;
            description = data['description'] ?? "Deskripsi program belum tersedia.";
            isLoading = false;
          });
        }
      }
    } catch (e) {
      if (mounted) setState(() => isLoading = false);
    }
  }

  // --- LOGIKA NAVIGASI FITUR ---

  void _navigateToMaterials() {
    Navigator.push(context, MaterialPageRoute(builder: (context) => SubjectListPage(
      classId: widget.classId,
      className: widget.className,
      token: widget.token,
      materi: materi,
    )));
  }

  void _navigateToPractice() {
    if (practiceQuestions.isEmpty) {
      _showWarningSnack("Latihan soal belum tersedia untuk kelas ini.");
      return;
    }
    Navigator.push(context, MaterialPageRoute(builder: (context) => PracticeSubjectListPage(
      allExercises: practiceQuestions, 
      token: widget.token,
    )));
  }

  void _navigateToTryouts() {
    if (tryouts.isEmpty) {
      _showWarningSnack("Tryout belum tersedia.");
    } else {
       // ✨ NAVIGASI KE DETAIL TRYOUT (Mengambil paket pertama)
       Navigator.push(context, MaterialPageRoute(builder: (context) => TryoutDetailPage(
         tryoutData: tryouts[0], 
         token: widget.token
       )));
    }
  }

  void _showWarningSnack(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(backgroundColor: Colors.orange, content: Text(msg, style: const TextStyle(fontWeight: FontWeight.bold)))
    );
  }

  @override
  Widget build(BuildContext context) {
    bool isActive = status == 'active';
    dynamic enrolledId = widget.userData['student']?['class_id'];
    bool isAnotherClassActive = enrolledId != null && enrolledId.toString() != widget.classId.toString();

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: spektaRed))
          : CustomScrollView(
              slivers: [
                _buildSliverAppBar(),
                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.all(20.0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildStatusBadge(isAnotherClassActive),
                        const SizedBox(height: 12),
                        Text(widget.className, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
                        const SizedBox(height: 24),
                        const Text("Tentang Kelas", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                        const SizedBox(height: 8),
                        Text(description, style: TextStyle(fontSize: 15, color: Colors.grey[700], height: 1.5)),
                        
                        const SizedBox(height: 30),
                        const Text("Kurikulum & Fitur Belajar", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                        const SizedBox(height: 15),
                        
                        // ✨ LIST FITUR MENJADI TOMBOL
                        _buildFeatureButton(
                          icon: Icons.menu_book_rounded,
                          title: "Materi Video & PDF",
                          subtitle: "${materi.length} Modul tersedia",
                          onTap: _navigateToMaterials,
                          isLocked: !isActive,
                        ),
                        
                        _buildFeatureButton(
                          icon: Icons.quiz_rounded,
                          title: "Latihan Soal Mingguan",
                          subtitle: "Asah kemampuanmu setiap minggu",
                          onTap: _navigateToPractice,
                          isLocked: !isActive,
                          color: Colors.blue,
                        ),

                        _buildFeatureButton(
                          icon: Icons.assignment_rounded,
                          title: "Simulasi Tryout",
                          subtitle: tryouts.isEmpty ? "Belum tersedia" : "${tryouts.length} Paket Tryout",
                          onTap: _navigateToTryouts,
                          isLocked: !isActive,
                          color: Colors.orange,
                        ),
                        
                        const SizedBox(height: 100),
                      ],
                    ),
                  ),
                ),
              ],
            ),
      // ✨ Tombol bawah hanya tampil untuk pendaftaran (Bukan untuk yang sudah aktif)
      bottomNavigationBar: (isActive || isAnotherClassActive) ? null : _buildPremiumBottomBar(),
    );
  }

  Widget _buildSliverAppBar() {
    return SliverAppBar(
      expandedHeight: 280.0, pinned: true, backgroundColor: spektaRed,
      leading: Padding(
        padding: const EdgeInsets.all(8.0),
        child: CircleAvatar(
          backgroundColor: Colors.black26,
          child: IconButton(icon: const Icon(Icons.arrow_back, color: Colors.white), onPressed: () => Navigator.pop(context)),
        ),
      ),
      flexibleSpace: FlexibleSpaceBar(
        background: Image.asset(_getLocalAsset(), fit: BoxFit.cover),
      ),
    );
  }

  Widget _buildStatusBadge(bool isLocked) {
    String txt = status == 'active' ? "TERDAFTAR" : (isLocked ? "TERKUNCI" : "TERSEDIA");
    Color col = status == 'active' ? Colors.green : (isLocked ? Colors.orange : Colors.blue);
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(color: col.withOpacity(0.1), borderRadius: BorderRadius.circular(8), border: Border.all(color: col)),
      child: Text(txt, style: TextStyle(color: col, fontWeight: FontWeight.bold, fontSize: 12)),
    );
  }

  Widget _buildFeatureButton({
    required IconData icon, 
    required String title, 
    required String subtitle, 
    required VoidCallback onTap, 
    bool isLocked = true,
    Color color = const Color(0xFF990000)
  }) {
    return Container(
      margin: const EdgeInsets.only(bottom: 15),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10)]
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: isLocked ? null : onTap,
          borderRadius: BorderRadius.circular(20),
          child: Padding(
            padding: const EdgeInsets.all(20),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: isLocked ? Colors.grey[100] : color.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(15),
                  ),
                  child: Icon(isLocked ? Icons.lock_outline_rounded : icon, color: isLocked ? Colors.grey : color),
                ),
                const SizedBox(width: 15),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(title, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: isLocked ? Colors.grey : Colors.black)),
                      Text(subtitle, style: TextStyle(fontSize: 12, color: Colors.grey[500])),
                    ],
                  ),
                ),
                if (!isLocked) const Icon(Icons.arrow_forward_ios_rounded, size: 14, color: Colors.grey),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildPremiumBottomBar() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 15),
      decoration: const BoxDecoration(color: Colors.white, boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10)]),
      child: SafeArea(
        child: Row(
          children: [
            Expanded(child: Text(currency.format(basePrice), style: TextStyle(color: spektaRed, fontSize: 20, fontWeight: FontWeight.bold))),
            ElevatedButton(
              onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => PaymentConfirmationPage(classId: widget.classId, className: widget.className, basePrice: basePrice, token: widget.token, userData: widget.userData))),
              style: ElevatedButton.styleFrom(backgroundColor: spektaRed, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))),
              child: const Text("DAFTAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            )
          ],
        ),
      ),
    );
  }
}