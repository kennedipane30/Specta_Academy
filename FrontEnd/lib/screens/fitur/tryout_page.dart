import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

import '../tryout_detail_page.dart';

class TryoutPage extends StatefulWidget {
  final String token;
  final Map userData;

  const TryoutPage({super.key, required this.token, required this.userData});

  @override
  State<TryoutPage> createState() => _TryoutPageState();
}

class _TryoutPageState extends State<TryoutPage>
    with SingleTickerProviderStateMixin {
  static const String baseUrl = 'http://10.0.2.2:8000';
  static const Color primaryRed = Color(0xFF9C0412);
  static const Color darkRed = Color(0xFF340506);
  static const Color textDark = Color(0xFF172033);

  late TabController _tabController;

  List _allTryouts = [];
  List _myHistory = [];

  bool _loadingAll = false;
  bool _loadingHistory = false;

  // Cek apakah siswa sudah punya kelas
  bool get _hasClass {
    // Consider class valid only when it can be parsed to an int.
    return _classId != null;
  }

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
    } else {
      debugPrint('NO VALID CLASS ID: ${widget.userData['student']?['class_id']}');
    }
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _fetchAllTryouts() async {
    setState(() => _loadingAll = true);
    try {
      final uri = Uri.parse('$baseUrl/api/tryouts').replace(
        queryParameters: _classId != null
            ? {'class_id': _classId.toString()}
            : {},
      );
      debugPrint('TRYOUT ALL REQUEST: $uri');
      final res = await http.get(uri, headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer ${widget.token}',
      });

      if (!mounted) return;

      debugPrint('TRYOUT ALL RESPONSE STATUS: ${res.statusCode}');
      debugPrint('TRYOUT ALL RESPONSE BODY: ${res.body}');

      if (res.statusCode == 200) {
        final decoded = jsonDecode(res.body);
        setState(() {
          _allTryouts = decoded['data'] ?? decoded['tryouts'] ?? [];
        });
      } else {
        debugPrint('TRYOUT ALL ERROR: ${res.statusCode} ${res.reasonPhrase}');
      }
    } catch (e) {
      debugPrint('TRYOUT ALL EXCEPTION: $e');
    } finally {
      if (mounted) setState(() => _loadingAll = false);
    }
  }

  Future<void> _fetchHistory() async {
    setState(() => _loadingHistory = true);
    try {
      final historyUri = Uri.parse('$baseUrl/api/tryouts/history');
      debugPrint('TRYOUT HISTORY REQUEST: $historyUri');

      final res = await http.get(
        historyUri,
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer ${widget.token}',
        },
      );

      if (!mounted) return;

      debugPrint('TRYOUT HISTORY RESPONSE STATUS: ${res.statusCode}');
      debugPrint('TRYOUT HISTORY RESPONSE BODY: ${res.body}');

      if (res.statusCode == 200) {
        final decoded = jsonDecode(res.body);
        setState(() {
          _myHistory = decoded['data'] ?? decoded['history'] ?? [];
        });
      } else if (res.statusCode == 404) {
        // Some backends use a different route for user history. Try fallback.
        final altUri = Uri.parse('$baseUrl/api/tryouts/my');
        debugPrint('TRYOUT HISTORY 404, trying fallback: $altUri');
        final altRes = await http.get(
          altUri,
          headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer ${widget.token}',
          },
        );
        debugPrint('TRYOUT HISTORY FALLBACK STATUS: ${altRes.statusCode}');
        debugPrint('TRYOUT HISTORY FALLBACK BODY: ${altRes.body}');
        if (altRes.statusCode == 200) {
          final decoded = jsonDecode(altRes.body);
          setState(() {
            _myHistory = decoded['data'] ?? decoded['history'] ?? decoded['tryouts'] ?? [];
          });
        } else {
          debugPrint('TRYOUT HISTORY ERROR: ${res.statusCode} ${res.reasonPhrase}');
        }
      } else {
        debugPrint('TRYOUT HISTORY ERROR: ${res.statusCode} ${res.reasonPhrase}');
      }
    } catch (e) {
      debugPrint('TRYOUT HISTORY EXCEPTION: $e');
    } finally {
      if (mounted) setState(() => _loadingHistory = false);
    }
  }

  void _openDetail(Map tryout) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => TryoutDetailPage(
          tryoutData: tryout,
          token: widget.token,
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      body: NestedScrollView(
        headerSliverBuilder: (context, innerBoxIsScrolled) => [
          SliverAppBar(
            expandedHeight: 130,
            floating: false,
            pinned: true,
            elevation: 0,
            backgroundColor: primaryRed,
            foregroundColor: Colors.white,
            flexibleSpace: FlexibleSpaceBar(
              titlePadding:
                  const EdgeInsets.only(left: 20, bottom: 56),
              title: const Text(
                'Tryout',
                style: TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.w900,
                  fontSize: 22,
                  letterSpacing: -0.5,
                ),
              ),
              background: Container(
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [Color(0xFFC50337), primaryRed, darkRed],
                  ),
                ),
                child: Align(
                  alignment: Alignment.bottomRight,
                  child: Opacity(
                    opacity: 0.08,
                    child: Icon(Icons.assignment_rounded,
                        size: 160, color: Colors.white),
                  ),
                ),
              ),
            ),
            bottom: PreferredSize(
              preferredSize: const Size.fromHeight(46),
              child: Container(
                color: primaryRed,
                child: TabBar(
                  controller: _tabController,
                  labelColor: Colors.white,
                  unselectedLabelColor: Colors.white54,
                  indicatorColor: Colors.white,
                  indicatorWeight: 3,
                  labelStyle: const TextStyle(
                    fontWeight: FontWeight.w800,
                    fontSize: 13,
                  ),
                  tabs: const [
                    Tab(text: 'Semua Tryout'),
                    Tab(text: 'Riwayat Saya'),
                  ],
                ),
              ),
            ),
          ),
        ],
        body: !_hasClass
            ? _buildNoClassState()
            : TabBarView(
                controller: _tabController,
                children: [
                  _buildAllTryouts(),
                  _buildHistory(),
                ],
              ),
      ),
    );
  }

  // Siswa belum beli kelas
  Widget _buildNoClassState() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(40),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              height: 90,
              width: 90,
              decoration: BoxDecoration(
                color: const Color(0xFFFFEEEE),
                borderRadius: BorderRadius.circular(28),
              ),
              child: const Icon(Icons.lock_rounded,
                  color: primaryRed, size: 44),
            ),
            const SizedBox(height: 24),
            const Text(
              'Akses Terkunci',
              style: TextStyle(
                color: textDark,
                fontSize: 20,
                fontWeight: FontWeight.w900,
              ),
            ),
            const SizedBox(height: 10),
            Text(
              'Kamu perlu mendaftar dan membeli kelas terlebih dahulu untuk mengakses fitur Tryout.',
              textAlign: TextAlign.center,
              style: TextStyle(
                color: Colors.grey.shade600,
                fontSize: 13,
                height: 1.6,
              ),
            ),
            const SizedBox(height: 30),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: primaryRed,
                padding: const EdgeInsets.symmetric(
                    horizontal: 32, vertical: 14),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(16),
                ),
              ),
              onPressed: () => Navigator.pop(context),
              child: const Text(
                'Daftar Kelas Sekarang',
                style: TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.w900,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  // Tab Semua Tryout
  Widget _buildAllTryouts() {
    if (_loadingAll) {
      return const Center(
          child: CircularProgressIndicator(color: primaryRed));
    }

    if (_allTryouts.isEmpty) {
      return _buildEmptyState(
        icon: Icons.assignment_outlined,
        title: 'Belum Ada Tryout',
        subtitle: 'Tryout akan segera tersedia.\nNantikan pengumuman berikutnya!',
      );
    }

    return RefreshIndicator(
      color: primaryRed,
      onRefresh: _fetchAllTryouts,
      child: ListView.builder(
        padding: const EdgeInsets.all(18),
        itemCount: _allTryouts.length,
        itemBuilder: (context, index) =>
            _buildTryoutCard(_allTryouts[index] as Map, index),
      ),
    );
  }

  // Tab Riwayat
  Widget _buildHistory() {
    if (_loadingHistory) {
      return const Center(
          child: CircularProgressIndicator(color: primaryRed));
    }

    if (_myHistory.isEmpty) {
      return _buildEmptyState(
        icon: Icons.history_rounded,
        title: 'Belum Ada Riwayat',
        subtitle: 'Hasil tryout yang kamu kerjakan\nakan muncul di sini.',
      );
    }

    return RefreshIndicator(
      color: primaryRed,
      onRefresh: _fetchHistory,
      child: ListView.builder(
        padding: const EdgeInsets.all(18),
        itemCount: _myHistory.length,
        itemBuilder: (context, index) =>
            _buildHistoryCard(_myHistory[index] as Map),
      ),
    );
  }

  Widget _buildTryoutCard(Map tryout, int index) {
    final title = tryout['title'] ?? tryout['name'] ?? 'Tryout UTBK';
    final duration = tryout['duration'] ?? '-';
    final isActive = tryout['is_active'] == true;

    return GestureDetector(
      onTap: isActive ? () => _openDetail(tryout) : null,
      child: Container(
        margin: const EdgeInsets.only(bottom: 14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(
            color: isActive
                ? const Color(0xFFFFCDD2)
                : Colors.grey.shade200,
            width: 1.2,
          ),
          boxShadow: [
            BoxShadow(
              color: isActive
                  ? primaryRed.withOpacity(0.07)
                  : Colors.black.withOpacity(0.04),
              blurRadius: 16,
              offset: const Offset(0, 6),
            ),
          ],
        ),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            children: [
              // Nomor / ikon
              Container(
                height: 52,
                width: 52,
                decoration: BoxDecoration(
                  gradient: isActive
                      ? const LinearGradient(
                          colors: [Color(0xFFB90018), primaryRed],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        )
                      : null,
                  color: isActive ? null : Colors.grey.shade100,
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Center(
                  child: Text(
                    '${index + 1}',
                    style: TextStyle(
                      color: isActive ? Colors.white : Colors.grey,
                      fontSize: 20,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                ),
              ),

              const SizedBox(width: 14),

              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Expanded(
                          child: Text(
                            title,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: TextStyle(
                              color: isActive ? textDark : Colors.grey,
                              fontSize: 14,
                              fontWeight: FontWeight.w900,
                            ),
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 9, vertical: 3),
                          decoration: BoxDecoration(
                            color: isActive
                                ? const Color(0xFFE8F5E9)
                                : Colors.grey.shade100,
                            borderRadius: BorderRadius.circular(99),
                          ),
                          child: Text(
                            isActive ? 'Aktif' : 'Tutup',
                            style: TextStyle(
                              color: isActive
                                  ? const Color(0xFF2E7D32)
                                  : Colors.grey,
                              fontSize: 10,
                              fontWeight: FontWeight.w800,
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 6),
                    Row(
                      children: [
                        Icon(Icons.timer_outlined,
                            size: 13,
                            color: Colors.grey.shade500),
                        const SizedBox(width: 4),
                        Text(
                          '$duration menit',
                          style: TextStyle(
                            color: Colors.grey.shade600,
                            fontSize: 11,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                        const SizedBox(width: 12),
                        Icon(Icons.quiz_outlined,
                            size: 13,
                            color: Colors.grey.shade500),
                        const SizedBox(width: 4),
                        Text(
                          'Simulasi UTBK',
                          style: TextStyle(
                            color: Colors.grey.shade600,
                            fontSize: 11,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              if (isActive)
                const Icon(Icons.chevron_right_rounded,
                    color: primaryRed, size: 24),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHistoryCard(Map history) {
    final title = history['title'] ?? history['tryout_name'] ?? 'Tryout';
    final score = history['score'] ?? history['nilai'] ?? '-';
    final date = history['completed_at'] ?? history['created_at'] ?? '';
    final submissionId = history['submission_id'] ?? history['id'];

    // Format tanggal sederhana
    String formattedDate = date.toString();
    if (date.toString().length >= 10) {
      formattedDate = date.toString().substring(0, 10);
    }

    final scoreNum = double.tryParse(score.toString()) ?? 0;
    final scoreColor = scoreNum >= 700
        ? const Color(0xFF2E7D32)
        : scoreNum >= 500
            ? const Color(0xFFF57C00)
            : const Color(0xFFC62828);

    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 16,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            // Score badge
            Container(
              height: 58,
              width: 58,
              decoration: BoxDecoration(
                color: scoreColor.withOpacity(0.1),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(
                    score.toString(),
                    style: TextStyle(
                      color: scoreColor,
                      fontSize: 16,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                  Text(
                    'Nilai',
                    style: TextStyle(
                      color: scoreColor.withOpacity(0.7),
                      fontSize: 9,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(width: 14),

            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                      color: textDark,
                      fontSize: 14,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                  const SizedBox(height: 5),
                  Row(
                    children: [
                      Icon(Icons.calendar_today_outlined,
                          size: 12, color: Colors.grey.shade500),
                      const SizedBox(width: 4),
                      Text(
                        formattedDate,
                        style: TextStyle(
                          color: Colors.grey.shade600,
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),

            TextButton(
              onPressed: submissionId != null
                  ? () => _openResult(submissionId)
                  : null,
              style: TextButton.styleFrom(
                foregroundColor: primaryRed,
                padding: const EdgeInsets.symmetric(
                    horizontal: 12, vertical: 6),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                  side: const BorderSide(color: primaryRed),
                ),
              ),
              child: const Text(
                'Lihat',
                style: TextStyle(
                  fontSize: 11,
                  fontWeight: FontWeight.w800,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _openResult(dynamic submissionId) {
    // TODO: Navigate to result page
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text('Hasil tryout #$submissionId'),
        backgroundColor: primaryRed,
      ),
    );
  }

  Widget _buildEmptyState({
    required IconData icon,
    required String title,
    required String subtitle,
  }) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            height: 80,
            width: 80,
            decoration: BoxDecoration(
              color: const Color(0xFFFFEEEE),
              borderRadius: BorderRadius.circular(24),
            ),
            child: Icon(icon, color: primaryRed, size: 38),
          ),
          const SizedBox(height: 20),
          Text(
            title,
            style: const TextStyle(
              color: textDark,
              fontSize: 17,
              fontWeight: FontWeight.w900,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            subtitle,
            textAlign: TextAlign.center,
            style: TextStyle(
              color: Colors.grey.shade600,
              fontSize: 13,
              height: 1.6,
            ),
          ),
        ],
      ),
    );
  }
}