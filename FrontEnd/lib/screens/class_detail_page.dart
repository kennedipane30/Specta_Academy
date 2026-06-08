import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:intl/intl.dart';
import 'package:http/http.dart' as http;

import '../services/auth_service.dart';
import 'payment_confirmation_page.dart';
import 'subject_list_page.dart';
import 'practice_subject_list_page.dart';
import 'tryout_detail_page.dart';
import 'tryout_list_page.dart';

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
    required this.token,
    required this.userData,
    this.price = 0,
  });

  @override
  State<ClassDetailPage> createState() => _ClassDetailPageState();
}

class _ClassDetailPageState extends State<ClassDetailPage> {
  String status = "none";
  late int basePrice;
  late Map currentLocalUserData;
  String displayClassName = "";
  String description = "";
  List materi = [];
  List tryouts = [];
  List practiceQuestions = [];
  bool isLoading = true;

  final Color spektaRed = const Color(0xFF990000);
  final currency =
      NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

  @override
  void initState() {
    super.initState();
    currentLocalUserData = widget.userData;
    displayClassName = widget.className;
    basePrice = widget.price;
    _fetchDetail();
  }

  String _getLocalAsset() {
    int cid = widget.classId;
    switch (cid) {
      case 1:
        return 'assets/images/abdi_negara.png';
      case 2:
        return 'assets/images/ptn_unhan.png';
      case 3:
        return 'assets/images/reguler.png';
      case 4:
        return 'assets/images/favorit.png';
      default:
        return 'assets/images/abdi_negara.png';
    }
  }

  int _getCurrentUserId() {
    try {
      var data = currentLocalUserData;
      
      if (data.containsKey('data') && data['data'] is Map) {
        var innerData = data['data'];
        if (innerData.containsKey('id')) return int.parse(innerData['id'].toString());
        if (innerData.containsKey('usersID')) return int.parse(innerData['usersID'].toString());
        if (innerData.containsKey('user_id')) return int.parse(innerData['user_id'].toString());
      }

      if (data.containsKey('usersID') && data['usersID'] != null) {
        return int.parse(data['usersID'].toString());
      } else if (data.containsKey('user_id') && data['user_id'] != null) {
        return int.parse(data['user_id'].toString());
      } else if (data.containsKey('user') && data['user'] != null) {
        if (data['user'].containsKey('id')) return int.parse(data['user']['id'].toString());
        if (data['user'].containsKey('usersID')) return int.parse(data['user']['usersID'].toString());
      } else if (data.containsKey('student') && data['student'] != null) {
        if (data['student'].containsKey('user_id')) return int.parse(data['student']['user_id'].toString());
      } else if (data.containsKey('id') && data['id'] != null) {
        return int.parse(data['id'].toString());
      }
    } catch (e) {
      print("❌ Gagal membaca User ID dari Map: $e");
    }

    return 0; 
  }

  Future<void> _fetchDetail() async {
    if (!mounted) return;
    setState(() => isLoading = true);

    try {
      int currentUserId = _getCurrentUserId(); 

      final matRes = await AuthService.getClassContent(widget.classId, widget.token);
      final tryoutRes = await AuthService.getSimulasi(widget.token, classId: widget.classId, userId: currentUserId);
      final pracRes = await AuthService.getTryouts(widget.token, classId: widget.classId);

      List fetchedMateri = [];
      List fetchedTryouts = [];
      List fetchedPractice = [];
      String fetchedStatus = "none";
      String fetchedDesc = "";

      if (matRes.statusCode == 200) {
        final d = jsonDecode(matRes.body);
        if (d is List) {
          fetchedMateri = d;
        } else if (d is Map) {
          fetchedMateri = d['materi'] ?? d['data'] ?? [];
          fetchedStatus = d['enroll_status'] ?? "none";
          fetchedDesc = d['description'] ?? "";
        }
      }

      if (tryoutRes.statusCode == 200) {
        final d = jsonDecode(tryoutRes.body);
        fetchedTryouts = d is List ? d : (d['data'] ?? []);
      }

      if (pracRes.statusCode == 200) {
        final d = jsonDecode(pracRes.body);
        fetchedPractice = d is List ? d : (d['data'] ?? []);
      }

      if (mounted) {
        setState(() {
          materi = fetchedMateri;
          tryouts = fetchedTryouts;
          practiceQuestions = fetchedPractice;
          status = fetchedStatus;
          description = fetchedDesc;
          isLoading = false;
        });
      }
    } catch (e) {
      debugPrint("❌ Error Fetch Multi-Service: $e");
      if (mounted) setState(() => isLoading = false);
    }
  }

  void _navigateToMaterials() {
    Navigator.push(
        context,
        MaterialPageRoute(
            builder: (context) => SubjectListPage(
                  classId: widget.classId,
                  className: displayClassName,
                  token: widget.token,
                  materi: materi,
                )));
  }
  
  void _navigateToPractice() {
    if (practiceQuestions.isEmpty) {
      _showWarningSnack("Latihan soal belum tersedia.");
      return;
    }
    
    Navigator.push(
        context,
        MaterialPageRoute(
            builder: (context) => PracticeSubjectListPage(
                allExercises: practiceQuestions, 
                token: widget.token,
            )));
  }

  void _navigateToTryouts() {
    if (tryouts.isEmpty) {
      _showWarningSnack("Tryout belum tersedia.");
    } else {
      int currentUserId = _getCurrentUserId(); 

      Navigator.push(
          context,
          MaterialPageRoute(
              builder: (context) => TryoutListPage(
                  tryouts: tryouts, 
                  token: widget.token,
                  userId: currentUserId, 
              )
          )
      // ✨ FIX PENTING: .then() ini wajib ada untuk merefresh nilai!
      ).then((_) {
          _fetchDetail();
      });
    }
  }

  void _showWarningSnack(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        backgroundColor: Colors.orange[800],
        content: Text(msg),
        behavior: SnackBarBehavior.floating));
  }

  @override
  Widget build(BuildContext context) {
    dynamic userRegisteredClassId =
        currentLocalUserData['student']?['class_id'];
    bool isActive = (status == 'active' ||
        userRegisteredClassId?.toString() == widget.classId.toString());
    bool hasOtherClass = userRegisteredClassId != null &&
        userRegisteredClassId.toString() != widget.classId.toString();

    final subjectsCount = materi
        .map((e) =>
            (e['subject_name'] ?? e['material_name'] ?? e['subject'] ?? '')
                .toString())
        .where((s) => s.isNotEmpty)
        .toSet()
        .length;

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: spektaRed))
          : RefreshIndicator(
              onRefresh: _fetchDetail,
              child: CustomScrollView(
                slivers: [
                  _buildSliverAppBar(),
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.all(25.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildStatusBadge(isActive),
                          const SizedBox(height: 15),
                          Text(displayClassName,
                              style: const TextStyle(
                                  fontSize: 26,
                                  fontWeight: FontWeight.bold,
                                  letterSpacing: -0.5)),
                          const SizedBox(height: 25),
                          const Text("Tentang Kelas",
                              style: TextStyle(
                                  fontSize: 18, fontWeight: FontWeight.bold)),
                          const SizedBox(height: 10),
                          Text(description,
                              style: TextStyle(
                                  fontSize: 15,
                                  color: Colors.grey[600],
                                  height: 1.6)),
                          const SizedBox(height: 35),
                          const Text("Kurikulum & Fitur Belajar",
                              style: TextStyle(
                                  fontSize: 18, fontWeight: FontWeight.bold)),
                          const SizedBox(height: 15),
                          _buildFeatureButton(
                            icon: Icons.menu_book_rounded,
                            title: "Materi Video & PDF",
                            subtitle: subjectsCount == 0
                                ? "Materi segera hadir"
                                : "$subjectsCount Mata Pelajaran tersedia",
                            onTap: _navigateToMaterials,
                            isLocked: !isActive,
                          ),
                          _buildFeatureButton(
                            icon: Icons.quiz_rounded,
                            title: "Latihan Soal Mingguan",
                            subtitle: practiceQuestions.isEmpty
                                ? "Belum tersedia"
                                : "${practiceQuestions.length} Soal tersedia",
                            onTap: _navigateToPractice, 
                            isLocked: !isActive,
                            color: Colors.blue,
                          ),
                          _buildFeatureButton(
                            icon: Icons.assignment_rounded,
                            title: "Simulasi Tryout",
                            subtitle: tryouts.isEmpty
                                ? "Belum tersedia"
                                : "${tryouts.length} Paket Tryout tersedia",
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
      bottomNavigationBar:
          isActive ? null : _buildPremiumBottomBar(hasOtherClass),
    );
  }

  Widget _buildSliverAppBar() {
    return SliverAppBar(
      expandedHeight: 280.0,
      pinned: true,
      backgroundColor: spektaRed,
      leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () => Navigator.pop(context)),
      flexibleSpace: FlexibleSpaceBar(
          background: Image.asset(_getLocalAsset(), fit: BoxFit.cover)),
    );
  }

  Widget _buildStatusBadge(bool enrolled) {
    String txt = enrolled ? "TERDAFTAR" : "TERSEDIA";
    Color col = enrolled ? Colors.green : Colors.blue;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
      decoration: BoxDecoration(
          color: col.withOpacity(0.1),
          borderRadius: BorderRadius.circular(10),
          border: Border.all(color: col.withOpacity(0.5))),
      child: Text(txt,
          style: TextStyle(
              color: col,
              fontWeight: FontWeight.bold,
              fontSize: 11,
              letterSpacing: 1)),
    );
  }

  Widget _buildFeatureButton(
      {required IconData icon,
      required String title,
      required String subtitle,
      required VoidCallback onTap,
      bool isLocked = true,
      Color color = const Color(0xFF990000)}) {
    return Container(
      margin: const EdgeInsets.only(bottom: 15),
      decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(25),
          boxShadow: [
            BoxShadow(
                color: Colors.black.withOpacity(0.04),
                blurRadius: 15,
                offset: const Offset(0, 5))
          ]),
      child: ListTile(
        onTap: isLocked
            ? () =>
                _showWarningSnack("Selesaikan pembayaran untuk akses fitur.")
            : onTap,
        contentPadding: const EdgeInsets.all(15),
        leading: Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
                color: isLocked ? Colors.grey[100] : color.withOpacity(0.1),
                borderRadius: BorderRadius.circular(15)),
            child: Icon(isLocked ? Icons.lock_outline_rounded : icon,
                color: isLocked ? Colors.grey : color)),
        title: Text(title,
            style: TextStyle(
                fontWeight: FontWeight.bold,
                fontSize: 16,
                color: isLocked ? Colors.grey : Colors.black)),
        subtitle: Text(subtitle, style: const TextStyle(fontSize: 12)),
        trailing: const Icon(Icons.arrow_forward_ios_rounded,
            size: 14, color: Colors.grey),
      ),
    );
  }

  Widget _buildPremiumBottomBar(bool hasOtherClass) {
    return Container(
        padding: const EdgeInsets.all(20),
        decoration: const BoxDecoration(
            color: Colors.white,
            boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 20)]),
        child: SafeArea(
            child: Row(children: [
          Expanded(
              child: Text(currency.format(basePrice),
                  style: TextStyle(
                      color: spektaRed,
                      fontSize: 20,
                      fontWeight: FontWeight.bold))),
          ElevatedButton(
              onPressed: hasOtherClass
                  ? () => _showWarningSnack(
                      "Anda sudah memiliki kelas aktif lainnya.")
                  : () => Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (_) => PaymentConfirmationPage(
                              classId: widget.classId,
                              className: displayClassName,
                              basePrice: basePrice,
                              token: widget.token,
                              userData: currentLocalUserData))),
              style: ElevatedButton.styleFrom(
                  backgroundColor: hasOtherClass ? Colors.grey : spektaRed,
                  padding:
                      const EdgeInsets.symmetric(horizontal: 25, vertical: 15),
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(15))),
              child: Text(
                  hasOtherClass ? "KELAS LAIN AKTIF" : "DAFTAR SEKARANG",
                  style: const TextStyle(
                      color: Colors.white, fontWeight: FontWeight.bold)))
        ])));
  }
}