import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:intl/intl.dart';

import '../services/auth_service.dart';
import 'payment_confirmation_page.dart';
import 'subject_list_page.dart'; // ✨ IMPORT HALAMAN PILIH SUBJEK

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

  // Ambil data detail dari API
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

  // --- LOGIKA NAVIGASI ---

  void _navigateToMaterials() {
    if (status != 'active') return;
    Navigator.push(context, MaterialPageRoute(builder: (context) => SubjectListPage(
      classId: widget.classId,
      className: widget.className,
      token: widget.token,
      materi: materi,
    )));
  }

  void _navigateToTryouts() {
    if (status != 'active') return;
    // Pindah ke halaman list tryout (buat file tryout_list_page jika belum ada)
    // Navigator.push(context, MaterialPageRoute(builder: (_) => TryoutListPage(...)));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
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
                        _buildStatusBadge(),
                        const SizedBox(height: 12),
                        Text(widget.className, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
                        const SizedBox(height: 24),
                        _buildSectionTitle("Tentang Kelas"),
                        const SizedBox(height: 8),
                        Text(description, style: TextStyle(fontSize: 15, color: Colors.grey[700], height: 1.5)),
                        const SizedBox(height: 24),
                        _buildSectionTitle("Apa yang akan kamu pelajari?"),
                        const SizedBox(height: 12),
                        
                        // Icon-icon Fitur (Bisa diklik jika sudah active)
                        _buildFeatureItem(Icons.menu_book_rounded, "${materi.length} Materi Video & PDF", _navigateToMaterials),
                        _buildFeatureItem(Icons.assignment_rounded, "${tryouts.length} Tryout Simulasi", _navigateToTryouts),
                        _buildFeatureItem(Icons.quiz_rounded, "${practiceQuestions.length} Latihan Soal", () {}),
                        
                        const SizedBox(height: 100),
                      ],
                    ),
                  ),
                ),
              ],
            ),
      bottomNavigationBar: (status == 'active') ? _buildActiveBottomBar() : _buildPremiumBottomBar(),
    );
  }

  Widget _buildSliverAppBar() {
    return SliverAppBar(
      expandedHeight: 280.0, pinned: true, backgroundColor: spektaRed,
      flexibleSpace: FlexibleSpaceBar(
        background: Image.asset('assets/images/abdi_negara.png', fit: BoxFit.cover), // Contoh asset
      ),
    );
  }

  Widget _buildStatusBadge() {
    bool isActive = status == 'active';
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: (isActive ? Colors.green : Colors.blue).withOpacity(0.1),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: isActive ? Colors.green : Colors.blue),
      ),
      child: Text(isActive ? "TERDAFTAR" : "TERSEDIA", style: TextStyle(color: isActive ? Colors.green : Colors.blue, fontWeight: FontWeight.bold, fontSize: 12)),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(title, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold));
  }

  // ✨ MODIFIKASI: Ditambahkan onTap agar item bisa diklik
  Widget _buildFeatureItem(IconData icon, String text, VoidCallback onTap) {
    return InkWell(
      onTap: status == 'active' ? onTap : null,
      child: Padding(
        padding: const EdgeInsets.symmetric(vertical: 8),
        child: Row(
          children: [
            Icon(icon, color: status == 'active' ? spektaRed : Colors.grey, size: 22),
            const SizedBox(width: 12),
            Text(text, style: TextStyle(fontSize: 15, fontWeight: FontWeight.w500, color: status == 'active' ? Colors.black : Colors.grey)),
            const Spacer(),
            if(status == 'active') const Icon(Icons.arrow_forward_ios_rounded, size: 14, color: Colors.grey)
          ],
        ),
      ),
    );
  }

  Widget _buildPremiumBottomBar() {
    return Container(
      padding: const EdgeInsets.all(20),
      color: Colors.white,
      child: SafeArea(
        child: Row(
          children: [
            Expanded(child: Text(currency.format(basePrice), style: TextStyle(color: spektaRed, fontSize: 20, fontWeight: FontWeight.bold))),
            ElevatedButton(
              onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => PaymentConfirmationPage(classId: widget.classId, className: widget.className, basePrice: basePrice, token: widget.token, userData: widget.userData))),
              style: ElevatedButton.styleFrom(backgroundColor: spektaRed),
              child: const Text("DAFTAR SEKARANG", style: TextStyle(color: Colors.white)),
            )
          ],
        ),
      ),
    );
  }

  Widget _buildActiveBottomBar() {
    return Container(
      padding: const EdgeInsets.all(20),
      color: Colors.white,
      child: SafeArea(
        child: ElevatedButton(
          onPressed: _navigateToMaterials,
          style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF1B5E20), minimumSize: const Size(double.infinity, 55)),
          child: const Text("MULAI BELAJAR", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
        ),
      ),
    );
  }
}