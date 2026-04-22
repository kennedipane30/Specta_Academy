import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:intl/intl.dart';

import '../services/auth_service.dart';
import 'payment_confirmation_page.dart'; // Pastikan import halaman baru ini
import 'midtrans_payment_page.dart';

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
  // State dasar kelas
  String status = "none";
  int basePrice = 0;
  String description = "";
  String imageUrl = "";
  List materi = [];
  List tryouts = [];
  List practiceQuestions = [];
  bool isLoading = true;

  final Color spektaRed = const Color(0xFF990000);
  final currency = NumberFormat.currency(
      locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

  @override
  void initState() {
    super.initState();
    _fetchDetail();
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
            imageUrl = data['image_url'] ?? "";
            isLoading = false;
          });
        }
      }
    } catch (e) {
      if (mounted) setState(() => isLoading = false);
    }
  }

  // --- FUNGSI NAVIGASI KE KONFIRMASI PEMBAYARAN ---
  void _navigateToConfirmation() {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => PaymentConfirmationPage(
          classId: widget.classId,
          className: widget.className,
          basePrice: basePrice,
          token: widget.token,
          userData: widget.userData,
        ),
      ),
    );
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
                        Text(
                          widget.className,
                          style: const TextStyle(
                            fontSize: 24,
                            fontWeight: FontWeight.bold,
                            letterSpacing: -0.5,
                          ),
                        ),
                        const SizedBox(height: 24),
                        _buildSectionTitle("Tentang Kelas"),
                        const SizedBox(height: 8),
                        Text(
                          description,
                          style: TextStyle(
                            fontSize: 15,
                            color: Colors.grey[700],
                            height: 1.5,
                          ),
                        ),
                        const SizedBox(height: 24),
                        _buildSectionTitle("Apa yang akan kamu pelajari?"),
                        const SizedBox(height: 12),
                        _buildFeatureItem(Icons.menu_book_rounded, "${materi.length} Materi Video & PDF"),
                        _buildFeatureItem(Icons.assignment_rounded, "${tryouts.length} Tryout Simulasi"),
                        _buildFeatureItem(Icons.quiz_rounded, "${practiceQuestions.length} Latihan Soal"),
                        const SizedBox(height: 100),
                      ],
                    ),
                  ),
                ),
              ],
            ),
      bottomNavigationBar: (status != 'active') ? _buildPremiumBottomBar() : _buildActiveBottomBar(),
    );
  }

  Widget _buildSliverAppBar() {
    String finalImageUrl = imageUrl
        .replaceAll('127.0.0.1', '10.0.2.2')
        .replaceAll('localhost', '10.0.2.2');

    return SliverAppBar(
      expandedHeight: 250.0,
      floating: false,
      pinned: true,
      backgroundColor: spektaRed,
      leading: Padding(
        padding: const EdgeInsets.all(8.0),
        child: CircleAvatar(
          backgroundColor: Colors.black26,
          child: IconButton(
            icon: const Icon(Icons.arrow_back, color: Colors.white),
            onPressed: () => Navigator.pop(context),
          ),
        ),
      ),
      flexibleSpace: FlexibleSpaceBar(
        background: Stack(
          fit: StackFit.expand,
          children: [
            imageUrl.isNotEmpty
                ? Image.network(finalImageUrl, fit: BoxFit.cover)
                : Container(color: spektaRed),
            Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [
                    Colors.black.withOpacity(0.4),
                    Colors.transparent,
                    Colors.black.withOpacity(0.6),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatusBadge() {
    Color badgeColor;
    String label;

    switch (status) {
      case 'active':
        badgeColor = Colors.green;
        label = "TERDAFTAR";
        break;
      case 'pending':
        badgeColor = Colors.orange;
        label = "MENUNGGU PEMBAYARAN";
        break;
      default:
        badgeColor = Colors.blue;
        label = "TERSEDIA";
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: badgeColor.withOpacity(0.1),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: badgeColor),
      ),
      child: Text(
        label,
        style: TextStyle(color: badgeColor, fontWeight: FontWeight.bold, fontSize: 12),
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(
      title,
      style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
    );
  }

  Widget _buildFeatureItem(IconData icon, String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Row(
        children: [
          Icon(icon, color: spektaRed, size: 22),
          const SizedBox(width: 12),
          Text(text, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w500)),
        ],
      ),
    );
  }

  Widget _buildPremiumBottomBar() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 15),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(color: Colors.black12, blurRadius: 10, offset: const Offset(0, -2))
        ],
      ),
      child: SafeArea(
        child: Row(
          children: [
            Expanded(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text("Harga Investasi", style: TextStyle(fontSize: 12, color: Colors.grey)),
                  Text(
                    currency.format(basePrice),
                    style: TextStyle(color: spektaRed, fontSize: 20, fontWeight: FontWeight.bold),
                  ),
                ],
              ),
            ),
            SizedBox(
              height: 50,
              width: 180,
              child: ElevatedButton(
                onPressed: _navigateToConfirmation, // Pindah ke halaman konfirmasi
                style: ElevatedButton.styleFrom(
                  backgroundColor: spektaRed,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  elevation: 0,
                ),
                child: Text(
                  status == 'pending' ? "SELESAIKAN" : "DAFTAR SEKARANG",
                  style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14),
                ),
              ),
            )
          ],
        ),
      ),
    );
  }

  Widget _buildActiveBottomBar() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: const BoxDecoration(color: Colors.white),
      child: SafeArea(
        child: SizedBox(
          width: double.infinity,
          height: 50,
          child: OutlinedButton(
            onPressed: () {},
            style: OutlinedButton.styleFrom(
              side: BorderSide(color: spektaRed),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
            child: Text("ANDA SUDAH TERDAFTAR", style: TextStyle(color: spektaRed, fontWeight: FontWeight.bold)),
          ),
        ),
      ),
    );
  }
}