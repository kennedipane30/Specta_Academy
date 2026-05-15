import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart'; 
import 'class_detail_page.dart';

class KelasPage extends StatefulWidget {
  final String token;
  final Map userData;
  final VoidCallback onGoToProfile;

  const KelasPage({
    super.key,
    required this.token,
    required this.userData,
    required this.onGoToProfile,
  });

  @override
  State<KelasPage> createState() => _KelasPageState();
}

class _KelasPageState extends State<KelasPage> {
  final Color spektaRed = const Color(0xFF990000);
  final Color spektaYellow = const Color(0xFFF1B401);
  final Color spektaDark = const Color(0xFF1A1A1A);

  List programs = [];
  bool isLoading = true;
  final currencyFormat = NumberFormat.currency(locale: 'id_ID', symbol: 'IDR ', decimalDigits: 0);

  @override
  void initState() {
    super.initState();
    _fetchPrograms(); 
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

  Future<void> _checkProfileAndNavigate(BuildContext context, Map<String, dynamic> item) async {
    showDialog(context: context, barrierDismissible: false, builder: (context) => const Center(child: CircularProgressIndicator(color: Colors.white)));
    try {
      final response = await http.get(
        Uri.parse('http://10.0.2.2:8000/api/user'),
        headers: {'Authorization': 'Bearer ${widget.token}', 'Accept': 'application/json'},
      );
      if (!mounted) return;
      Navigator.pop(context); 

      if (response.statusCode == 200) {
        final latestUserData = json.decode(response.body);
        var student = latestUserData['student'];
        
        bool isComplete = student != null &&
            student['parent_name'] != null && student['parent_name'] != "-" &&
            student['address'] != null && student['address'] != "-" &&
            student['parent_phone'] != null && student['parent_phone'] != "-";

        if (isComplete) {
          Navigator.push(context, MaterialPageRoute(builder: (context) => ClassDetailPage(
            classId: int.parse(item['class_id'].toString()), 
            className: item['program_name'], 
            token: widget.token,
            userData: latestUserData, 
          )));
        } else {
          _showPremiumProfileDialog(context);
        }
      }
    } catch (e) {
      if (mounted) Navigator.pop(context);
    }
  }

  void _showPremiumProfileDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(Icons.assignment_ind_rounded, size: 60, color: spektaRed),
            const SizedBox(height: 20),
            const Text("Biodata Belum Lengkap", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
            const SizedBox(height: 30),
            ElevatedButton(
              onPressed: () { Navigator.pop(context); widget.onGoToProfile(); },
              style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 50), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
              child: const Text("LENGKAPI SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: spektaRed))
          : CustomScrollView(
              slivers: [
                SliverAppBar(expandedHeight: 120.0, pinned: true, elevation: 0, backgroundColor: spektaRed, flexibleSpace: FlexibleSpaceBar(title: const Text("Study Program", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 18)), background: Container(color: spektaRed))),
                SliverPadding(
                  padding: const EdgeInsets.fromLTRB(20, 25, 20, 100),
                  sliver: SliverList(delegate: SliverChildBuilderDelegate((context, index) => _buildProgramCard(context, programs[index]), childCount: programs.length)),
                ),
              ],
            ),
    );
  }

  Widget _buildProgramCard(BuildContext context, Map<String, dynamic> item) {
    dynamic currentEnrolledId = widget.userData['student']?['class_id'];
    bool isMyClass = currentEnrolledId?.toString() == item['class_id'].toString();
    bool hasOtherClass = currentEnrolledId != null && !isMyClass;

    return Container(
      margin: const EdgeInsets.only(bottom: 30),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(35), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 25, offset: const Offset(0, 12))]),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Stack(
            children: [
              ClipRRect(
                borderRadius: const BorderRadius.vertical(top: Radius.circular(35)),
                // ✨ Gambar tetap berwarna (Efek greyscale dihapus)
                child: Image.asset(_getProgramImage(item['class_id']), height: 200, width: double.infinity, fit: BoxFit.cover),
              ),
              if (isMyClass)
                Positioned(top: 20, left: 20, child: Container(padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6), decoration: BoxDecoration(color: Colors.green, borderRadius: BorderRadius.circular(10)), child: const Text("PROGRAM ANDA ✅", style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold)))),
              if (hasOtherClass)
                Positioned(top: 20, left: 20, child: Container(padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6), decoration: BoxDecoration(color: Colors.orange, borderRadius: BorderRadius.circular(10)), child: const Text("TERKUNCI 🔒", style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold)))),
              Positioned(top: 20, right: 20, child: Container(padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8), decoration: BoxDecoration(color: Colors.white.withOpacity(0.9), borderRadius: BorderRadius.circular(15)), child: Text(currencyFormat.format(int.parse(item['price'].toString())), style: TextStyle(color: spektaRed, fontWeight: FontWeight.w900, fontSize: 12)))),
            ],
          ),
          Padding(
            padding: const EdgeInsets.all(25.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text("OFFICIAL ACADEMY PROGRAM", style: TextStyle(color: Colors.grey, fontSize: 10, fontWeight: FontWeight.w800, letterSpacing: 1.2)),
                const SizedBox(height: 8),
                Text(item['program_name'], style: TextStyle(color: spektaDark, fontSize: 22, fontWeight: FontWeight.w900)),
                const SizedBox(height: 10),
                Text(item['description'] ?? "Segera bergabung dan raih impianmu.", maxLines: 2, overflow: TextOverflow.ellipsis, style: TextStyle(color: Colors.grey.shade600, fontSize: 13, height: 1.4)),
                const SizedBox(height: 25),
                ElevatedButton(
                  onPressed: () => _checkProfileAndNavigate(context, item),
                  style: ElevatedButton.styleFrom(backgroundColor: hasOtherClass ? Colors.grey[300] : spektaYellow, minimumSize: const Size(double.infinity, 55), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)), elevation: 0),
                  child: Text(hasOtherClass ? "LIHAT DETAIL (LOCKED)" : "VIEW DETAILS", style: TextStyle(color: hasOtherClass ? Colors.grey[600] : Colors.black, fontWeight: FontWeight.w900)),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}