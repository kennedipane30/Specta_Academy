import 'package:flutter/material.dart';
import 'dart:convert';
import 'dart:async'; // 🔥 Diperlukan untuk Timer Debouncing
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart'; 
import 'class_detail_page.dart';
import 'subject_list_page.dart';
import '../services/auth_service.dart';
import '../config/app_config.dart'; 

class KelasPage extends StatefulWidget {
  final String token;
  final Map userData;
  final VoidCallback onGoToProfile;
  final VoidCallback onGoToHome;

  const KelasPage({
    super.key,
    required this.token,
    required this.userData,
    required this.onGoToProfile,
    required this.onGoToHome,
  });

  @override
  State<KelasPage> createState() => _KelasPageState();
}

class _KelasPageState extends State<KelasPage> {
  // ============================================================
  // 🎨 PALET WARNA SPEKTA (KONSISTEN DENGAN TRYOUTDETAILPAGE)
  // ============================================================
  static const Color primaryRed       = Color(0xFFC5352C);
  static const Color accentTeal       = Color(0xFF2EA8AB);
  static const Color darkTeal         = Color(0xFF00696C);
  static const Color lightBlueBg      = Color(0xFFEFF4FF);
  static const Color pageBg           = Color(0xFFF1F5F9);
  static const Color textDark         = Color(0xFF0F172A);
  static const Color textDarkVariant = Color(0xFF334155);
  static const Color neutralGray     = Color(0xFF64748B);
  static const Color outlineVariant  = Color(0xFFE2BEBA);
  static const Color spektaYellow    = Color(0xFFF5A623);
  static const Color successGreen    = Color(0xFF22C55E);

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

  @override
  void dispose() {
    super.dispose();
  }

  Future<void> _initializeData() async {
    await Future.wait([
      _fetchPrograms(),
      _refreshUserStatus(),
    ]);
  }

  Future<void> _refreshUserStatus() async {
    try {
      // 🔥 PERBAIKAN: Menggunakan AppConfig.baseUrl agar port :8000 lokal terikat
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/user'),
        headers: {'Authorization': 'Bearer ${widget.token}', 'Accept': 'application/json'},
      );
      if (response.statusCode == 200) {
        if (mounted) setState(() => currentData = json.decode(response.body));
      }
    } catch (e) {
      debugPrint("Error Refresh: $e");
    }
  }

  Future<void> _fetchPrograms() async {
    try {
      // 🔥 PERBAIKAN: Menggunakan AppConfig.baseUrl agar port :8000 lokal terikat
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/classes'),
        headers: {'Authorization': 'Bearer ${widget.token}', 'Accept': 'application/json'},
      );
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (mounted) {
          setState(() {
            programs = data['data'] ?? [];
          });
        }
      } else {
        if (mounted) _showWarningSnack("Mohon maaf sistem sedang sibuk");
      }
    } catch (e) {
      debugPrint('CLASSES FETCH EXCEPTION: $e');
      if (mounted) _showWarningSnack("Mohon maaf sistem sedang sibuk");
    } finally {
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

  void _showWarningSnack(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        backgroundColor: primaryRed,
        content: Text(msg, style: const TextStyle(fontWeight: FontWeight.bold)),
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: pageBg,
      body: isLoading
          ? const Center(child: CircularProgressIndicator(color: accentTeal))
          : CustomScrollView(
              physics: const BouncingScrollPhysics(),
              slivers: [
                _buildCurvedAppBar(),
                SliverPadding(
                  padding: const EdgeInsets.fromLTRB(20, 20, 20, 100),
                  sliver: SliverList(
                    delegate: SliverChildBuilderDelegate(
                      (context, index) => _buildProgramCard(context, programs[index]), 
                      childCount: programs.length,
                    ),
                  ),
                ),
              ],
            ),
    );
  }

  Widget _buildCurvedAppBar() {
    return SliverAppBar(
      expandedHeight: 110.0,
      pinned: true,
      elevation: 0,
      backgroundColor: Colors.transparent,
      automaticallyImplyLeading: false,
      centerTitle: true,
      leading: Padding(
        padding: const EdgeInsets.all(8.0),
        child: CircleAvatar(
          backgroundColor: Colors.white.withOpacity(0.15),
          child: IconButton(
            icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white, size: 16),
            onPressed: widget.onGoToHome,
          ),
        ),
      ),
      flexibleSpace: FlexibleSpaceBar(
        centerTitle: true,
        title: const Text(
          "Study Program",
          style: TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.w900,
            fontSize: 18,
            letterSpacing: -0.5,
          ),
        ),
        background: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [primaryRed, accentTeal],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.vertical(
              bottom: Radius.circular(20),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildProgramCard(BuildContext context, Map<String, dynamic> item) {
    dynamic activeClassId = currentData?['student']?['class_id'];
    bool isMyClass = activeClassId?.toString() == item['class_id'].toString();

    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      decoration: BoxDecoration(
        color: Colors.white, 
        borderRadius: BorderRadius.circular(24), 
        border: Border.all(color: outlineVariant.withOpacity(0.4)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.025), 
            blurRadius: 15, 
            offset: const Offset(0, 6),
          )
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // ============================================================
          // 🖼️ GAME OVERLAY IMAGE
          // ============================================================
          ClipRRect(
            borderRadius: const BorderRadius.vertical(top: Radius.circular(24)), 
            child: Image.asset(
              _getProgramImage(item['class_id']), 
              height: 180, 
              width: double.infinity, 
              fit: BoxFit.cover,
            ),
          ),
          
          // ============================================================
          // 📝 BAGIAN DESKRIPSI
          // ============================================================
          Padding(
            padding: const EdgeInsets.all(20.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Baris Header dengan badge "TERDAFTAR" jika sudah terdaftar
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text(
                      "OFFICIAL ACADEMY PROGRAM", 
                      style: TextStyle(color: neutralGray, fontSize: 9, fontWeight: FontWeight.w800, letterSpacing: 1.1),
                    ),
                    // Menampilkan badge "TERDAFTAR" jika sudah terdaftar
                    if (isMyClass)
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4), 
                        decoration: BoxDecoration(
                          color: successGreen, 
                          borderRadius: BorderRadius.circular(6),
                        ), 
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: const [
                            Icon(Icons.check_circle, color: Colors.white, size: 12),
                            SizedBox(width: 4),
                            Text(
                              "TERDAFTAR", 
                              style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 9),
                            ),
                          ],
                        ),
                      ),
                  ],
                ),
                const SizedBox(height: 8),
                Text(item['program_name'], style: const TextStyle(color: textDark, fontSize: 20, fontWeight: FontWeight.w900, letterSpacing: -0.5)),
                const SizedBox(height: 8),
                Text(item['description'] ?? "...", maxLines: 2, overflow: TextOverflow.ellipsis, style: const TextStyle(color: textDarkVariant, fontSize: 13, height: 1.4, fontWeight: FontWeight.w600)),
                const SizedBox(height: 20),
                ElevatedButton(
                  onPressed: () => _navigateToDetail(context, item),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: accentTeal,
                    foregroundColor: Colors.white,
                    minimumSize: const Size(double.infinity, 52), 
                    elevation: 0,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)), 
                  ),
                  child: const Text("VIEW DETAILS", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 13, letterSpacing: 0.5)),
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

    final classId = int.parse(item['class_id'].toString());
    int classPrice = int.tryParse(item['price'].toString()) ?? 0;

    dynamic activeClassId = currentData?['student']?['class_id'];
    bool isEnrolledInThis = activeClassId?.toString() == classId.toString();

    if (isEnrolledInThis) {
      showDialog(context: context, barrierDismissible: false, builder: (_) => const Center(child: CircularProgressIndicator(color: accentTeal)));

      try {
        final response = await AuthService.getClassContent(classId, widget.token);
        if (!mounted) return;
        Navigator.pop(context); 

        if (response.statusCode == 200) {
          final decoded = jsonDecode(response.body);
          final String enrollStatus = decoded['enroll_status'] ?? "none";

          if (enrollStatus == 'active') {
            Navigator.push(context, MaterialPageRoute(builder: (context) => SubjectListPage(
              classId: classId,
              className: decoded['program_name'] ?? item['program_name'],
              token: widget.token,
              materi: decoded['materi'] ?? [], 
            )));
            return; 
          }
        } else {
          _showWarningSnack("Mohon maaf sistem sedang sibuk");
        }
      } catch (e) {
        if (mounted) Navigator.pop(context);
        debugPrint("Error Auto Navigate: $e");
        if (mounted) _showWarningSnack("Mohon maaf sistem sedang sibuk");
      }
    }

    Navigator.push(
      context, 
      MaterialPageRoute(
        builder: (context) => ClassDetailPage(
          classId: classId, 
          className: item['program_name'], 
          price: classPrice, 
          token: widget.token,
          userData: currentData!, 
        ),
      ),
    );
  }
}