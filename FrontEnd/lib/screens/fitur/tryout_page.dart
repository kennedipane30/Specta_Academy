import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

import '../tryout_detail_page.dart';
import '../explanation_page.dart'; // Pastikan file ini sudah dibuat

class TryoutPage extends StatefulWidget {
  final String token;
  final Map userData;

  const TryoutPage({super.key, required this.token, required this.userData});

  @override
  State<TryoutPage> createState() => _TryoutPageState();
}

class _TryoutPageState extends State<TryoutPage>
    with SingleTickerProviderStateMixin {
  // Gunakan 10.0.2.2 untuk emulator, atau IP Laptop Anda untuk HP fisik
  static const String baseUrl = 'http://10.0.2.2:8000';
  static const Color primaryRed = Color(0xFF9C0412);
  static const Color darkRed = Color(0xFF340506);
  static const Color textDark = Color(0xFF172033);

  late TabController _tabController;

  List _allTryouts = [];
  List _myHistory = [];

  bool _loadingAll = false;
  bool _loadingHistory = false;

  // Mendapatkan Class ID Siswa dari data login secara otomatis
  bool get _hasClass => _classId != null;

  int? get _classId {
    final raw = widget.userData['student']?['class_id'];
    if (raw == null) return null;
    return int.tryParse(raw.toString());
  }

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    if (_hasClass) {
      _fetchAllTryouts();
      _fetchHistory();
    }
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  /// FUNGSI 1: Ambil Daftar TO Berdasarkan Kelas
  Future<void> _fetchAllTryouts() async {
    if (!mounted) return;
    setState(() => _loadingAll = true);
    try {
      final uri = Uri.parse('$baseUrl/api/tryouts').replace(
        queryParameters: _classId != null ? {'class_id': _classId.toString()} : {},
      );
      final res = await http.get(uri, headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer ${widget.token}',
      });

      if (res.statusCode == 200) {
        final decoded = jsonDecode(res.body);
        if (mounted) {
          setState(() {
            _allTryouts = decoded['data'] ?? [];
          });
        }
      } else {
        debugPrint("API Error Index: ${res.statusCode}");
      }
    } catch (e) {
      debugPrint('TRYOUT ALL EXCEPTION: $e');
    } finally {
      if (mounted) setState(() => _loadingAll = false);
    }
  }

  /// FUNGSI 2: Ambil Riwayat Nilai
  Future<void> _fetchHistory() async {
    if (!mounted) return;
    setState(() => _loadingHistory = true);
    try {
      final historyUri = Uri.parse('$baseUrl/api/tryouts/history');
      final res = await http.get(historyUri, headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer ${widget.token}',
      });

      if (res.statusCode == 200) {
        final decoded = jsonDecode(res.body);
        
        // DEBUG: Cetak response API untuk melihat struktur data
        debugPrint("📦 HISTORY API RESPONSE: ${res.body}");
        
        if (mounted) {
          setState(() {
            _myHistory = decoded['data'] ?? [];
          });
          
          // DEBUG: Cetak sample data pertama jika ada
          if (_myHistory.isNotEmpty) {
            debugPrint("🔍 Sample history pertama: ${_myHistory.first}");
            debugPrint("🔍 Key yang tersedia: ${(_myHistory.first as Map).keys.join(', ')}");
          }
        }
      } else {
        debugPrint("❌ History API Error: ${res.statusCode}");
      }
    } catch (e) {
      debugPrint('❌ TRYOUT HISTORY EXCEPTION: $e');
    } finally {
      if (mounted) setState(() => _loadingHistory = false);
    }
  }

  /// FUNGSI 3: Ambil Data Pembahasan (Integrasi ke ExplanationPage)
  Future<void> _openResult(dynamic submissionId) async {
    debugPrint("🚀 Membuka pembahasan untuk ID: $submissionId");
    
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => const Center(child: CircularProgressIndicator(color: primaryRed)),
    );

    try {
      final response = await http.get(
        Uri.parse('$baseUrl/api/tryouts/results/$submissionId'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer ${widget.token}',
        },
      );

      if (!mounted) return;
      Navigator.pop(context); // Tutup loading

      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        final List questionsWithAnswers = decoded['data'] ?? [];
        
        debugPrint("✅ Berhasil memuat ${questionsWithAnswers.length} soal untuk pembahasan");

        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => ExplanationPage(questions: questionsWithAnswers),
          ),
        );
      } else {
        debugPrint("❌ Gagal memuat pembahasan: ${response.statusCode}");
        debugPrint("❌ Response body: ${response.body}");
        _showErrorSnackBar("Gagal memuat detail pembahasan.");
      }
    } catch (e) {
      debugPrint("❌ Error di _openResult: $e");
      if (mounted) Navigator.pop(context);
      _showErrorSnackBar("Terjadi kesalahan koneksi server.");
    }
  }

  /// DIALOG PROTEKSI 1x KERJA
  void _showAlreadyDoneDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)),
        title: const Row(
          children: [
            Icon(Icons.lock_clock_rounded, color: primaryRed),
            SizedBox(width: 10),
            Text("Akses Terkunci", style: TextStyle(fontWeight: FontWeight.bold)),
          ],
        ),
        content: const Text("Anda sudah menyelesaikan Tryout ini. Skor Anda sudah tersimpan dan tidak dapat diubah lagi. Silakan buka menu 'Riwayat Saya' untuk melihat pembahasan."),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.pop(context);
              _tabController.animateTo(1); // Pindah ke tab Riwayat otomatis
            },
            child: const Text("LIHAT RIWAYAT", style: TextStyle(color: primaryRed, fontWeight: FontWeight.bold)),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text("TUTUP", style: TextStyle(color: Colors.grey)),
          ),
        ],
      ),
    );
  }

  void _showErrorSnackBar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: Colors.red),
    );
  }

  void _openDetail(Map tryout) {
    Navigator.push(
      context, 
      MaterialPageRoute(builder: (_) => TryoutDetailPage(tryoutData: tryout, token: widget.token)),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      body: NestedScrollView(
        headerSliverBuilder: (context, innerBoxIsScrolled) => [
          SliverAppBar(
            expandedHeight: 130, pinned: true, elevation: 0,
            backgroundColor: primaryRed, foregroundColor: Colors.white,
            flexibleSpace: FlexibleSpaceBar(
              titlePadding: const EdgeInsets.only(left: 20, bottom: 56),
              title: const Text('Tryout', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 22, letterSpacing: -0.5)),
              background: Container(
                decoration: const BoxDecoration(gradient: LinearGradient(colors: [Color(0xFFC50337), primaryRed, darkRed])),
                child: const Align(alignment: Alignment.bottomRight, child: Opacity(opacity: 0.08, child: Icon(Icons.assignment_rounded, size: 160, color: Colors.white))),
              ),
            ),
            bottom: PreferredSize(
              preferredSize: const Size.fromHeight(46),
              child: Container(
                color: primaryRed,
                child: TabBar(
                  controller: _tabController,
                  labelColor: Colors.white, unselectedLabelColor: Colors.white54,
                  indicatorColor: Colors.white, indicatorWeight: 3,
                  labelStyle: const TextStyle(fontWeight: FontWeight.w800, fontSize: 13),
                  tabs: const [Tab(text: 'Semua Tryout'), Tab(text: 'Riwayat Saya')],
                ),
              ),
            ),
          ),
        ],
        body: !_hasClass
            ? _buildNoClassState()
            : TabBarView(
                controller: _tabController,
                children: [_buildAllTryouts(), _buildHistory()],
              ),
      ),
    );
  }

  Widget _buildAllTryouts() {
    if (_loadingAll) return const Center(child: CircularProgressIndicator(color: primaryRed));
    if (_allTryouts.isEmpty) return _buildEmptyState(icon: Icons.assignment_outlined, title: 'Belum Ada Tryout', subtitle: 'Cek apakah akun Anda sudah terdaftar di kelas yang benar.');
    
    return RefreshIndicator(
      color: primaryRed, onRefresh: _fetchAllTryouts,
      child: ListView.builder(
        padding: const EdgeInsets.all(18),
        itemCount: _allTryouts.length,
        itemBuilder: (context, index) => _buildTryoutCard(_allTryouts[index] as Map, index),
      ),
    );
  }

  Widget _buildHistory() {
    if (_loadingHistory) return const Center(child: CircularProgressIndicator(color: primaryRed));
    if (_myHistory.isEmpty) return _buildEmptyState(icon: Icons.history_rounded, title: 'Belum Ada Riwayat', subtitle: 'Hasil pengerjaan Anda akan muncul di sini.');
    
    return RefreshIndicator(
      color: primaryRed, onRefresh: _fetchHistory,
      child: ListView.builder(
        padding: const EdgeInsets.all(18),
        itemCount: _myHistory.length,
        itemBuilder: (context, index) => _buildHistoryCard(_myHistory[index] as Map),
      ),
    );
  }

  /// CARD TRYOUT: Logika Enabled/Disabled
  Widget _buildTryoutCard(Map tryout, int index) {
    final title = tryout['title'] ?? 'Tryout UTBK';
    final duration = tryout['duration'] ?? '-';
    final isActive = tryout['is_active'] == true || tryout['is_active'] == 1;
    final isCompleted = tryout['is_completed'] ?? false;

    return GestureDetector(
      onTap: () {
        if (!isActive) return;
        if (isCompleted) {
          _showAlreadyDoneDialog();
        } else {
          _openDetail(tryout);
        }
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 14),
        decoration: BoxDecoration(
          color: Colors.white, borderRadius: BorderRadius.circular(20),
          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 16, offset: const Offset(0, 6))],
        ),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            children: [
              Container(
                height: 52, width: 52,
                decoration: BoxDecoration(
                  color: isCompleted ? Colors.grey.shade400 : (isActive ? primaryRed : Colors.grey.shade200),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Center(
                  child: Text('${index + 1}', style: TextStyle(color: isActive ? Colors.white : Colors.grey, fontSize: 20, fontWeight: FontWeight.w900)),
                ),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(title, style: TextStyle(color: isActive ? textDark : Colors.grey, fontSize: 14, fontWeight: FontWeight.w900)),
                    Row(
                      children: [
                        Text('$duration menit • Simulasi UTBK', style: TextStyle(color: Colors.grey.shade600, fontSize: 11)),
                        if (isCompleted) ...[
                          const SizedBox(width: 8),
                          const Icon(Icons.check_circle, color: Colors.green, size: 12),
                          const Text(" SELESAI", style: TextStyle(color: Colors.green, fontSize: 11, fontWeight: FontWeight.bold)),
                        ]
                      ],
                    ),
                  ],
                ),
              ),
              Icon(isCompleted ? Icons.lock_outline_rounded : Icons.chevron_right_rounded, color: isCompleted ? Colors.grey : primaryRed, size: 24),
            ],
          ),
        ),
      ),
    );
  }

  /// CARD RIWAYAT: Tombol Pembahasan (DIPERBAIKI - Menggunakan result_id)
  Widget _buildHistoryCard(Map history) {
    // 🔥 PERBAIKAN: Menggunakan result_id (dari API Laravel)
    // Mencoba beberapa kemungkinan key yang umum digunakan
    final dynamic submissionId = history['result_id'] ?? 
                                  history['id'] ?? 
                                  history['submission_id'];
    
    final title = history['title'] ?? history['tryout_name'] ?? 'Tryout';
    final score = history['score'] ?? '-';
    final date = history['completed_at'] ?? history['created_at'] ?? '';

    String formattedDate = date.toString().length >= 10 
        ? date.toString().substring(0, 10) 
        : date.toString();

    // DEBUG: Cetak ID yang ditemukan
    debugPrint("📋 Build History Card - FOUND ID: $submissionId, Title: $title");

    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      decoration: BoxDecoration(
        color: Colors.white, borderRadius: BorderRadius.circular(20),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 16, offset: const Offset(0, 6))],
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            Container(
              height: 58, width: 58,
              decoration: BoxDecoration(color: primaryRed.withOpacity(0.1), borderRadius: BorderRadius.circular(16)),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(score.toString(), style: const TextStyle(color: primaryRed, fontSize: 16, fontWeight: FontWeight.w900)),
                  Text('Nilai', style: TextStyle(color: primaryRed.withOpacity(0.7), fontSize: 9, fontWeight: FontWeight.w700)),
                ],
              ),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: const TextStyle(color: textDark, fontSize: 14, fontWeight: FontWeight.w900)),
                  Text(formattedDate, style: TextStyle(color: Colors.grey.shade600, fontSize: 11)),
                ],
              ),
            ),
            // 🔥 Tombol Pembahasan - sekarang dengan ID yang benar
            ElevatedButton(
              onPressed: () {
                if (submissionId != null && submissionId.toString().isNotEmpty) {
                  _openResult(submissionId);
                } else {
                  // Debug print untuk membantu mencari tahu kenapa ID null
                  debugPrint("❌ ERROR: ID Riwayat Kosong! Data lengkap: $history");
                  debugPrint("❌ Key yang tersedia: ${history.keys.join(', ')}");
                  _showErrorSnackBar("ID Riwayat tidak valid. Silakan tarik ke bawah untuk refresh.");
                }
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.white, 
                foregroundColor: primaryRed, 
                elevation: 0,
                side: const BorderSide(color: primaryRed, width: 1.2),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              ),
              child: const Text('Pembahasan', 
                style: TextStyle(fontSize: 10, fontWeight: FontWeight.w800)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildNoClassState() {
    return Center(child: Padding(padding: const EdgeInsets.all(40), child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
      const Icon(Icons.lock_rounded, color: primaryRed, size: 60),
      const SizedBox(height: 24),
      const Text('Akses Terkunci', style: TextStyle(color: textDark, fontSize: 20, fontWeight: FontWeight.w900)),
      const SizedBox(height: 10),
      const Text('Akun Anda belum terdaftar di kelas manapun. Silakan hubungi Admin Spekta.', textAlign: TextAlign.center),
      const SizedBox(height: 30),
      ElevatedButton(style: ElevatedButton.styleFrom(backgroundColor: primaryRed, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16))), onPressed: () => Navigator.pop(context), child: const Text('KEMBALI', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900)))
    ])));
  }

  Widget _buildEmptyState({required IconData icon, required String title, required String subtitle}) {
    return Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
      Icon(icon, color: primaryRed.withOpacity(0.2), size: 60),
      const SizedBox(height: 20),
      Text(title, style: const TextStyle(color: textDark, fontSize: 17, fontWeight: FontWeight.w900)),
      Padding(padding: const EdgeInsets.symmetric(horizontal: 40), child: Text(subtitle, textAlign: TextAlign.center, style: const TextStyle(color: Colors.grey, fontSize: 13)))
    ]));
  }
}