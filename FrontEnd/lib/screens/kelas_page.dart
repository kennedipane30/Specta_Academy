import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart'; 
import 'class_detail_page.dart';

class KelasPage extends StatefulWidget {
  final String token;
  final Map userData;
  final VoidCallback onGoToProfile;
  final VoidCallback onGoToHome; // ✨ Fungsi untuk pindah ke Tab Home

  const KelasPage({
    super.key,
    required this.token,
    required this.userData,
    required this.onGoToProfile,
    required this.onGoToHome, // ✨ Wajib diisi di main_screen
  });

  @override
  State<KelasPage> createState() => _KelasPageState();
}

class _KelasPageState extends State<KelasPage> {
  final Color spektaRed = const Color(0xFF990000);
  final Color spektaYellow = const Color(0xFFF1B401);
  final Color spektaDark = const Color(0xFF1A1A1A);

  List programs = [];
  Map? currentData;
  bool isLoading = true;
  final currencyFormat = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

  @override
  void initState() {
    super.initState();
    currentData = widget.userData;
    _initializeData();
  }

  Future<void> _initializeData() async {
    await Future.wait([
      _fetchPrograms(),
      _refreshUserStatus(),
    ]);
  }

  Future<void> _refreshUserStatus() async {
    try {
      final response = await http.get(
        Uri.parse('http://10.0.2.2:8000/api/user'),
        headers: {'Authorization': 'Bearer ${widget.token}', 'Accept': 'application/json'},
      );
      if (response.statusCode == 200) {
        if (mounted) setState(() => currentData = json.decode(response.body));
      }
    } catch (e) {
      debugPrint("Error Refresh User: $e");
    }
  }

  Future<void> _fetchPrograms() async {
    try {
      final response = await http.get(
        Uri.parse('http://10.0.2.2:8000/api/classes'), 
        headers: {'Authorization': 'Bearer ${widget.token}', 'Accept': 'application/json'},
      );
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (mounted) setState(() { programs = data['data']; isLoading = false; });
      }
    } catch (e) {
      if (mounted) setState(() => isLoading = false);
    }
  }

  String _getProgramImage(dynamic id) {
    int classId = int.tryParse(id.toString()) ?? 0;
    switch (classId) {
      case 1: return 'assets/images/abdi_negara.png';
      case 2: return 'assets/images/ptn_unhan.png';
      case 3: return 'assets/images/reguler.png';
      case 4: return 'assets/images/favorit.png';
      default: return 'assets/images/abdi_negara.png';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: spektaRed))
          : CustomScrollView(
              slivers: [
                // --- HEADER PENDEK (Sesuai Referensi Gambar 2) ---
                SliverAppBar(
                  expandedHeight: 100.0, 
                  pinned: true, 
                  elevation: 0, 
                  centerTitle: true,
                  backgroundColor: spektaRed,
                  automaticallyImplyLeading: false, 
                  // ✨ TOMBOL PANAH SIMPEL (Sesuai Gambar 1)
                  leading: IconButton(
                    icon: const Icon(Icons.arrow_back, color: Colors.white, size: 28),
                    onPressed: widget.onGoToHome, // ⬅️ BALIK KE HOME (Bukan Pop)
                  ),
                  flexibleSpace: FlexibleSpaceBar(
                    centerTitle: true,
                    title: const Text(
                      "Study Program", 
                      style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 18)
                    ),
                  ),
                ),

                SliverPadding(
                  padding: const EdgeInsets.fromLTRB(20, 20, 20, 100),
                  sliver: SliverList(
                    delegate: SliverChildBuilderDelegate(
                      (context, index) => _buildProgramCard(context, programs[index]), 
                      childCount: programs.length
                    ),
                  ),
                ),
              ],
            ),
    );
  }

  Widget _buildProgramCard(BuildContext context, Map<String, dynamic> item) {
    dynamic activeClassId = currentData?['active_class_id'] ?? currentData?['student']?['class_id'];
    bool isMyClass = activeClassId?.toString() == item['class_id'].toString();

    return Container(
      margin: const EdgeInsets.only(bottom: 25),
      decoration: BoxDecoration(
        color: Colors.white, 
        borderRadius: BorderRadius.circular(25), 
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 15, offset: const Offset(0, 8))
        ]
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Stack(
            children: [
              ClipRRect(
                borderRadius: const BorderRadius.vertical(top: Radius.circular(25)),
                child: Image.asset(
                  _getProgramImage(item['class_id']), 
                  height: 180, 
                  width: double.infinity, 
                  fit: BoxFit.cover
                ),
              ),
              if (isMyClass)
                Positioned(
                  top: 15, left: 15, 
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                    decoration: BoxDecoration(color: Colors.green, borderRadius: BorderRadius.circular(8)),
                    child: const Text("PROGRAM ANDA ✅", style: TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.bold)),
                  )
                ),
              Positioned(
                top: 15, right: 15, 
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6), 
                  decoration: BoxDecoration(color: Colors.white.withOpacity(0.9), borderRadius: BorderRadius.circular(10)), 
                  child: Text(
                    currencyFormat.format(int.tryParse(item['price'].toString()) ?? 0), 
                    style: TextStyle(color: spektaRed, fontWeight: FontWeight.w900, fontSize: 12)
                  )
                )
              ),
            ],
          ),
          Padding(
            padding: const EdgeInsets.all(20.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text("OFFICIAL ACADEMY PROGRAM", style: TextStyle(color: Colors.grey, fontSize: 9, fontWeight: FontWeight.w800, letterSpacing: 1.1)),
                const SizedBox(height: 6),
                Text(item['program_name'], style: TextStyle(color: spektaDark, fontSize: 20, fontWeight: FontWeight.w900)),
                const SizedBox(height: 8),
                Text(item['description'] ?? "...", maxLines: 2, style: TextStyle(color: Colors.grey.shade600, fontSize: 13, height: 1.4)),
                const SizedBox(height: 20),
                ElevatedButton(
                  onPressed: () => _navigateToDetail(context, item),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: spektaYellow, foregroundColor: Colors.black,
                    minimumSize: const Size(double.infinity, 52), 
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)), 
                    elevation: 0,
                  ),
                  child: const Text("VIEW DETAILS", style: TextStyle(fontWeight: FontWeight.w900)),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Future<void> _navigateToDetail(BuildContext context, Map<String, dynamic> item) async {
    await _refreshUserStatus();
    if (!mounted) return;
    Navigator.push(context, MaterialPageRoute(builder: (context) => ClassDetailPage(
      classId: int.parse(item['class_id'].toString()), 
      className: item['program_name'], 
      token: widget.token,
      userData: currentData!, 
    )));
  }
}