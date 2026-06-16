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

  // ============================================================
  // 🎨 PALET WARNA SPEKTA (KONSISTEN DENGAN TRYOUTDETAILPAGE)
  // ============================================================
  static const Color primaryRed      = Color(0xFFC5352C);
  static const Color accentTeal      = Color(0xFF2EA8AB);
  static const Color darkTeal        = Color(0xFF00696C);
  static const Color lightBlueBg     = Color(0xFFEFF4FF);
  static const Color pageBg          = Color(0xFFF1F5F9);
  static const Color textDark        = Color(0xFF0F172A);
  static const Color textDarkVariant = Color(0xFF334155);
  static const Color neutralGray     = Color(0xFF64748B);
  static const Color outlineVariant  = Color(0xFFE2BEBA);
  static const Color spektaYellow    = Color(0xFFF5A623);
  static const Color softRed         = Color(0xFFFEE2E2);

  final currency = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

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

      if (matRes.statusCode >= 500 || tryoutRes.statusCode >= 500 || pracRes.statusCode >= 500) {
         _showWarningSnack("Mohon maaf sistem sedang sibuk");
      }

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
      if (mounted) {
        setState(() => isLoading = false);
        _showWarningSnack("Mohon maaf sistem sedang sibuk");
      }
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
      ).then((_) {
          _fetchDetail();
      });
    }
  }

  void _showWarningSnack(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        backgroundColor: accentTeal,
        content: Text(msg, style: const TextStyle(fontWeight: FontWeight.bold)),
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))));
  }

  @override
  Widget build(BuildContext context) {
    dynamic userRegisteredClassId = currentLocalUserData['student']?['class_id'];
    bool isActive = (status == 'active' || userRegisteredClassId?.toString() == widget.classId.toString());
    bool hasOtherClass = userRegisteredClassId != null && userRegisteredClassId.toString() != widget.classId.toString();

    final subjectsCount = materi
        .map((e) => (e['subject_name'] ?? e['material_name'] ?? e['subject'] ?? '').toString())
        .where((s) => s.isNotEmpty)
        .toSet()
        .length;

    return Scaffold(
      backgroundColor: pageBg,
      body: isLoading
          ? const Center(child: CircularProgressIndicator(color: accentTeal))
          : RefreshIndicator(
              onRefresh: _fetchDetail,
              color: accentTeal,
              child: CustomScrollView(
                slivers: [
                  _buildSliverAppBar(),
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 24.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildStatusBadge(isActive),
                          const SizedBox(height: 16),
                          Text(
                            displayClassName,
                            style: const TextStyle(
                              fontSize: 28,
                              fontWeight: FontWeight.w900,
                              color: textDark,
                              letterSpacing: -0.8,
                            ),
                          ),
                          const SizedBox(height: 28),
                          
                          _buildTitleSection("Tentang Kelas", "💡"),
                          const SizedBox(height: 12),
                          Container(
                            width: double.infinity,
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(color: outlineVariant.withOpacity(0.5)),
                            ),
                            child: Text(
                              description.isEmpty ? "Kelas bimbingan belajar premium Spekta Academy." : description,
                              style: const TextStyle(
                                fontSize: 14.5,
                                color: textDarkVariant,
                                height: 1.6,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ),
                          const SizedBox(height: 32),
                          
                          _buildTitleSection("Kurikulum & Fitur Belajar", "🎯"),
                          const SizedBox(height: 16),
                          
                          _buildFeatureButton(
                            icon: Icons.import_contacts_rounded,
                            title: "Materi Video & PDF",
                            subtitle: subjectsCount == 0
                                ? "Materi segera hadir"
                                : "$subjectsCount Mata Pelajaran tersedia",
                            onTap: _navigateToMaterials,
                            isLocked: !isActive,
                            color: accentTeal,
                          ),
                          _buildFeatureButton(
                            icon: Icons.check_circle_outline_rounded,
                            title: "Latihan Soal Mingguan",
                            subtitle: practiceQuestions.isEmpty
                                ? "Belum tersedia"
                                : "${practiceQuestions.length} Soal tersedia",
                            onTap: _navigateToPractice, 
                            isLocked: !isActive,
                            color: accentTeal,
                          ),
                          _buildFeatureButton(
                            icon: Icons.assignment_turned_in_rounded,
                            title: "Simulasi Tryout",
                            subtitle: tryouts.isEmpty
                                ? "Belum tersedia"
                                : "${tryouts.length} Paket Tryout tersedia",
                            onTap: _navigateToTryouts,
                            isLocked: !isActive,
                            color: accentTeal,
                          ),
                          const SizedBox(height: 100),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
      bottomNavigationBar: isActive ? null : _buildPremiumBottomBar(hasOtherClass),
    );
  }

  Widget _buildSliverAppBar() {
    return SliverAppBar(
      expandedHeight: 280.0,
      pinned: true,
      elevation: 0,
      backgroundColor: accentTeal,
      leading: Padding(
        padding: const EdgeInsets.all(8.0),
        child: CircleAvatar(
          backgroundColor: Colors.black.withOpacity(0.35),
          child: IconButton(
            icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white, size: 18),
            onPressed: () => Navigator.pop(context),
          ),
        ),
      ),
      flexibleSpace: FlexibleSpaceBar(
        background: Stack(
          fit: StackFit.expand,
          children: [
            Image.asset(
              _getLocalAsset(),
              fit: BoxFit.cover,
              errorBuilder: (context, error, stackTrace) => Container(
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    colors: [accentTeal, darkTeal],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                ),
              ),
            ),
            const DecoratedBox(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [Colors.transparent, Color(0x7F000000)],
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTitleSection(String title, String emoji) {
    return Row(
      children: [
        Text(emoji, style: const TextStyle(fontSize: 18)),
        const SizedBox(width: 8),
        Text(
          title,
          style: const TextStyle(
            fontSize: 16.5, 
            fontWeight: FontWeight.w900, 
            color: textDark,
            letterSpacing: -0.3,
          ),
        ),
      ],
    );
  }

  Widget _buildStatusBadge(bool enrolled) {
    String txt = enrolled ? "TERDAFTAR" : "TERSEDIA KELAS";
    Color col = enrolled ? darkTeal : accentTeal;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
          color: col.withOpacity(0.08),
          borderRadius: BorderRadius.circular(10),
          border: Border.all(color: col.withOpacity(0.3), width: 1.5)),
      child: Text(
        txt,
        style: TextStyle(
          color: col,
          fontWeight: FontWeight.w900,
          fontSize: 10,
          letterSpacing: 1.2,
        ),
      ),
    );
  }

  Widget _buildFeatureButton(
      {required IconData icon,
      required String title,
      required String subtitle,
      required VoidCallback onTap,
      bool isLocked = true,
      Color color = accentTeal}) {
    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: outlineVariant.withOpacity(0.4)),
          boxShadow: [
            BoxShadow(
                color: Colors.black.withOpacity(0.02),
                blurRadius: 12,
                offset: const Offset(0, 4))
          ]),
      child: ListTile(
        onTap: isLocked
            ? () => _showWarningSnack("Selesaikan pembayaran untuk akses fitur.")
            : onTap,
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        leading: Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
                color: isLocked ? Colors.grey[100] : color.withOpacity(0.08),
                borderRadius: BorderRadius.circular(12)),
            child: Icon(
                isLocked ? Icons.lock_outline_rounded : icon,
                color: isLocked ? neutralGray : color,
                size: 24,
            )),
        title: Text(
          title,
          style: TextStyle(
              fontWeight: FontWeight.w800,
              fontSize: 15,
              color: isLocked ? neutralGray : textDark,
          ),
        ),
        subtitle: Padding(
          padding: const EdgeInsets.only(top: 4.0),
          child: Text(
            subtitle, 
            style: const TextStyle(fontSize: 11.5, color: neutralGray, fontWeight: FontWeight.w600),
          ),
        ),
        trailing: const Icon(Icons.arrow_forward_ios_rounded, size: 14, color: neutralGray),
      ),
    );
  }

  Widget _buildPremiumBottomBar(bool hasOtherClass) {
    return Container(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
        decoration: BoxDecoration(
            color: Colors.white,
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.06), 
                blurRadius: 20, 
                offset: const Offset(0, -4),
              )
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
                      const Text(
                        "Total Biaya Kelas:",
                        style: TextStyle(color: neutralGray, fontSize: 11, fontWeight: FontWeight.bold),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        currency.format(basePrice),
                        style: const TextStyle(
                          color: accentTeal,
                          fontSize: 20,
                          fontWeight: FontWeight.w900,
                          letterSpacing: -0.5,
                        ),
                      ),
                    ],
                  ),
                ),
                ElevatedButton(
                  onPressed: hasOtherClass
                      ? () => _showWarningSnack("Anda sudah memiliki kelas aktif lainnya.")
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
                      backgroundColor: hasOtherClass ? neutralGray : accentTeal,
                      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
                      elevation: 0,
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(14),
                      ),
                  ),
                  child: Text(
                      hasOtherClass ? "KELAS LAIN AKTIF" : "DAFTAR SEKARANG",
                      style: const TextStyle(
                          color: Colors.white, 
                          fontWeight: FontWeight.w900,
                          fontSize: 13,
                          letterSpacing: 0.5,
                      ),
                  ),
                ),
              ],
            ),
        ),
    );
  }
}