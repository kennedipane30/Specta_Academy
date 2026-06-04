import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:intl/intl.dart';
import 'package:http/http.dart' as http;

import '../services/auth_service.dart';
import 'payment_confirmation_page.dart';
import 'subject_list_page.dart'; 
import 'practice_subject_list_page.dart'; 
import 'tryout_detail_page.dart';

class ClassDetailPage extends StatefulWidget {
  final int classId;
  final String className;
  final int price; 
  final String token;
  final Map userData;

  const ClassDetailPage({
    super.key,
    required this.classId,
    required this.className,
    required this.price, 
    required this.token,
    required this.userData,
  });

  @override
  State<ClassDetailPage> createState() => _ClassDetailPageState();
}

class _ClassDetailPageState extends State<ClassDetailPage> {
  String status = "none";
  late int basePrice; 
  late Map currentLocalUserData;
  String description = "";
  List materi = [];
  List subjects = []; 
  List tryouts = [];
  List practiceQuestions = [];
  bool isLoading = true;

  final Color spektaRed = const Color(0xFF990000);
  final currency = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

  @override
  void initState() {
    super.initState();
    basePrice = widget.price; 
    currentLocalUserData = widget.userData; 
    _fetchDetail();
  }

  // --- FIX ERROR: FUNGSI ASSET GAMBAR ---
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

  // --- FUNGSI REFRESH PROFIL USER ---
  Future<void> _refreshUserProfile() async {
    try {
      final response = await http.get(
        Uri.parse('http://10.0.2.2:8000/api/user'),
        headers: {
          'Authorization': 'Bearer ${widget.token}',
          'Accept': 'application/json'
        },
      );
      if (response.statusCode == 200) {
        if (mounted) {
          setState(() {
            currentLocalUserData = json.decode(response.body);
          });
        }
      }
    } catch (e) {
      debugPrint("Error Refresh Profil: $e");
    }
  }

  Future<void> _fetchDetail() async {
    if (!mounted) return;
    setState(() => isLoading = true);
    
    try {
      final response = await AuthService.getClassContent(widget.classId, widget.token);

      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        final apiData = decoded['data'] ?? {}; 

        if (mounted) {
          setState(() {
            status = decoded['enroll_status'] ?? "none";
            materi = apiData['materi'] ?? [];
            subjects = apiData['subjects'] ?? []; 
            tryouts = apiData['tryouts'] ?? [];
            practiceQuestions = apiData['practice_questions'] ?? [];
            description = apiData['description'] ?? "Materi belajar lengkap tersedia untuk membantu kelulusanmu.";
            basePrice = int.tryParse(apiData['price']?.toString() ?? widget.price.toString()) ?? widget.price;
            isLoading = false;
          });
        }
      } else {
        if (mounted) setState(() => isLoading = false);
      }
    } catch (e) {
      debugPrint("Error Fetching Detail: $e");
      if (mounted) setState(() => isLoading = false);
    }
  }

  void _showAlreadyEnrolledDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)),
        title: const Text("Pendaftaran Gagal", style: TextStyle(fontWeight: FontWeight.bold)),
        content: const Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(Icons.error_outline_rounded, color: Colors.red, size: 64),
            SizedBox(height: 20),
            Text(
              "Maaf, Anda sudah terdaftar dalam program kelas lain. Setiap siswa hanya diperbolehkan memiliki 1 program aktif.",
              textAlign: TextAlign.center,
            ),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text("MENGERTI"))
        ],
      ),
    );
  }

  void _navigateToMaterials() {
    Navigator.push(context, MaterialPageRoute(builder: (context) => SubjectListPage(
      classId: widget.classId, className: widget.className, token: widget.token, subjects: subjects, materi: materi,
    )));
  }

  void _navigateToPractice() {
    if (practiceQuestions.isEmpty) { _showWarningSnack("Latihan soal belum tersedia."); return; }
    Navigator.push(context, MaterialPageRoute(builder: (context) => PracticeSubjectListPage(allExercises: practiceQuestions, token: widget.token)));
  }

  void _navigateToTryouts() {
    if (tryouts.isEmpty) { _showWarningSnack("Tryout belum tersedia."); } 
    else { Navigator.push(context, MaterialPageRoute(builder: (context) => TryoutDetailPage(tryoutData: tryouts[0], token: widget.token))); }
  }

  void _showWarningSnack(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(backgroundColor: Colors.orange, content: Text(msg)));
  }

  @override
  Widget build(BuildContext context) {
    bool isActive = (status == 'active');
    dynamic enrolledClassId = currentLocalUserData['active_class_id'] ?? currentLocalUserData['student']?['class_id'];
    bool hasOtherClassActive = enrolledClassId != null && enrolledClassId.toString() != widget.classId.toString();

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: spektaRed))
          : RefreshIndicator(
              onRefresh: () async {
                await _refreshUserProfile();
                await _fetchDetail();
              },
              child: CustomScrollView(
                slivers: [
                  _buildSliverAppBar(),
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.all(20.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildStatusBadge(hasOtherClassActive),
                          const SizedBox(height: 12),
                          Text(widget.className, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
                          const SizedBox(height: 24),
                          const Text("Tentang Kelas", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                          const SizedBox(height: 8),
                          Text(description, style: TextStyle(fontSize: 15, color: Colors.grey[700], height: 1.5)),
                          const SizedBox(height: 30),
                          const Text("Kurikulum & Fitur Belajar", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                          const SizedBox(height: 15),
                          _buildFeatureButton(
                            icon: Icons.menu_book_rounded,
                            title: "Materi Video & PDF",
                            subtitle: subjects.isEmpty ? "Materi segera hadir" : "${subjects.length} Mata Pelajaran tersedia",
                            onTap: _navigateToMaterials,
                            isLocked: !isActive,
                          ),
                          _buildFeatureButton(
                            icon: Icons.quiz_rounded,
                            title: "Latihan Soal Mingguan",
                            subtitle: practiceQuestions.isEmpty ? "Belum tersedia" : "Asah kemampuanmu setiap minggu",
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
            ),
      bottomNavigationBar: isActive 
          ? _buildSuccessBottomBar() 
          : _buildPremiumBottomBar(hasOtherClassActive),
    );
  }

  Widget _buildSliverAppBar() {
    return SliverAppBar(
      expandedHeight: 280.0, pinned: true, backgroundColor: spektaRed,
      leading: Padding(
        padding: const EdgeInsets.all(8.0),
        child: CircleAvatar(backgroundColor: Colors.black26, child: IconButton(icon: const Icon(Icons.arrow_back, color: Colors.white), onPressed: () => Navigator.pop(context)))),
      flexibleSpace: FlexibleSpaceBar(background: Image.asset(_getLocalAsset(), fit: BoxFit.cover)),
    );
  }

  Widget _buildStatusBadge(bool isOtherClassActive) {
    String txt = status == 'active' ? "TERDAFTAR" : (isOtherClassActive ? "KELAS LAIN AKTIF" : "TERSEDIA");
    Color col = status == 'active' ? Colors.green : (isOtherClassActive ? Colors.orange : Colors.blue);
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(color: col.withOpacity(0.1), borderRadius: BorderRadius.circular(8), border: Border.all(color: col)),
      child: Text(txt, style: TextStyle(color: col, fontWeight: FontWeight.bold, fontSize: 12)),
    );
  }

  Widget _buildFeatureButton({required IconData icon, required String title, required String subtitle, required VoidCallback onTap, bool isLocked = true, Color color = const Color(0xFF990000)}) {
    return Container(
      margin: const EdgeInsets.only(bottom: 15),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10)]),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: isLocked ? null : onTap,
          borderRadius: BorderRadius.circular(20),
          child: Padding(
            padding: const EdgeInsets.all(20),
            child: Row(
              children: [
                Container(padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: isLocked ? Colors.grey[100] : color.withOpacity(0.1), borderRadius: BorderRadius.circular(15)), child: Icon(isLocked ? Icons.lock_outline_rounded : icon, color: isLocked ? Colors.grey : color)),
                const SizedBox(width: 15),
                Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [Text(title, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: isLocked ? Colors.grey : Colors.black)), Text(subtitle, style: TextStyle(fontSize: 12, color: Colors.grey[500]))])),
                if (!isLocked) const Icon(Icons.arrow_forward_ios_rounded, size: 14, color: Colors.grey),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildSuccessBottomBar() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 15),
      decoration: const BoxDecoration(color: Colors.white, boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10)]),
      child: SafeArea(
        child: ElevatedButton(
          onPressed: _navigateToMaterials, 
          style: ElevatedButton.styleFrom(backgroundColor: Colors.green, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)), minimumSize: const Size(double.infinity, 50)), 
          child: const Text("MULAI BELAJAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold))
        ),
      ),
    );
  }

  Widget _buildPremiumBottomBar(bool isOtherClassActive) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 15),
      decoration: const BoxDecoration(color: Colors.white, boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10)]),
      child: SafeArea(
        child: Row(
          children: [
            Expanded(child: Text(currency.format(basePrice), style: TextStyle(color: spektaRed, fontSize: 20, fontWeight: FontWeight.bold))),
            ElevatedButton(
              onPressed: () async {
                if (isOtherClassActive) {
                  _showAlreadyEnrolledDialog();
                } else {
                  final bool? success = await Navigator.push(
                    context, 
                    MaterialPageRoute(builder: (_) => PaymentConfirmationPage(
                      classId: widget.classId, className: widget.className, basePrice: basePrice, token: widget.token, userData: currentLocalUserData
                    ))
                  );

                  if (success == true) {
                    setState(() => isLoading = true); 
                    await Future.delayed(const Duration(seconds: 3));
                    await _refreshUserProfile();
                    await _fetchDetail();
                    if (mounted) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(backgroundColor: Colors.green, content: Text("Pembayaran Berhasil!"))
                      );
                    }
                  }
                }
              }, 
              style: ElevatedButton.styleFrom(backgroundColor: spektaRed, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))), 
              child: const Text("DAFTAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold))
            )
          ]
        )
      )
    );
  }
}