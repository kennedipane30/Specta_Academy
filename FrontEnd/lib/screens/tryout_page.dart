import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

import 'tryout_detail_page.dart';

class TryoutPage extends StatefulWidget {
  final String token;
  final Map userData;

  const TryoutPage({super.key, required this.token, required this.userData});

  @override
  State<TryoutPage> createState() => _TryoutPageState();
}

class _TryoutPageState extends State<TryoutPage> {
  static const String host = '10.0.2.2';
  static const String baseUrl = 'http://$host:8000'; 
  static const String tryoutServiceUrl = 'http://$host:9002/api'; 
  
  static const Color primaryRed = Color(0xFF9C0412);
  static const Color darkRed = Color(0xFF340506);
  static const Color textDark = Color(0xFF172033);

  List _allTryouts = [];
  bool _loadingAll = false;

  bool get _hasClass => _classId != null;

  int? get _classId {
    final raw = widget.userData['student']?['class_id'];
    if (raw == null) return null;
    return int.tryParse(raw.toString());
  }

  int get _userId {
    try {
      var data = widget.userData;
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
      debugPrint("❌ Gagal membaca User ID di TryoutPage: $e");
    }
    return 0; 
  }

  @override
  void initState() {
    super.initState();
    if (_hasClass) {
      _fetchAllTryouts();
    }
  }

  Future<void> _fetchAllTryouts() async {
    if (!mounted) return;
    setState(() => _loadingAll = true);
    
    try {
      final uri = Uri.parse('$tryoutServiceUrl/tryouts').replace(
        queryParameters: {
          if (_classId != null) 'class_id': _classId.toString(),
          'user_id': _userId.toString(), 
        },
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
      }
    } catch (e) {
      debugPrint('❌ TRYOUT ALL EXCEPTION: $e');
    } finally {
      if (mounted) setState(() => _loadingAll = false);
    }
  }

  void _openDetail(Map tryout, bool isDone) {
    Navigator.push(
      context, 
      MaterialPageRoute(builder: (_) => TryoutDetailPage(
        tryoutData: tryout, 
        token: widget.token,
        isDone: isDone,
        userId: _userId, 
      )),
    ).then((_) {
      _fetchAllTryouts();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: const Text(
          'Tryout Kelas Kamu',
          style: TextStyle(fontWeight: FontWeight.w900, color: Colors.white),
        ),
        backgroundColor: primaryRed,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () => Navigator.pop(context), 
        ),
      ),
      body: !_hasClass
          ? _buildNoClassState()
          : _buildAllTryouts(),
    );
  }

  Widget _buildAllTryouts() {
    if (_loadingAll) return const Center(child: CircularProgressIndicator(color: primaryRed));
    if (_allTryouts.isEmpty) return _buildEmptyState(icon: Icons.assignment_outlined, title: 'Belum Ada Tryout', subtitle: 'Cek apakah akun Anda sudah terdaftar di kelas yang benar.');
    
    return RefreshIndicator(
      color: primaryRed, onRefresh: _fetchAllTryouts,
      child: ListView.separated(
        padding: const EdgeInsets.all(18),
        itemCount: _allTryouts.length,
        separatorBuilder: (_, __) => const SizedBox(height: 12),
        itemBuilder: (context, index) => _buildTryoutCard(_allTryouts[index] as Map, index),
      ),
    );
  }

  Widget _buildTryoutCard(Map tryout, int index) {
    final title = tryout['title'] ?? 'Tryout UTBK';
    final isActive = tryout['is_active'] != 0; 
    final isCompleted = tryout['is_done'] == true || tryout['is_done'] == 1 || tryout['is_done'] == "1" || tryout['is_done'] == "true";
    final score = tryout['score'] != null ? tryout['score'].toString() : '-';

    return GestureDetector(
      onTap: () {
        if (!isActive) return;
        // ✨ PERBAIKAN: Apapun statusnya, biarkan masuk ke Detail Page
        _openDetail(tryout, isCompleted);
      },
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
        decoration: BoxDecoration(
          color: Colors.white, 
          borderRadius: BorderRadius.circular(18),
          border: Border.all(color: Colors.grey.withOpacity(0.15)),
          boxShadow: [
            BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 8, offset: const Offset(0, 4))
          ],
        ),
        child: Row(
          children: [
            Container(
              height: 44, width: 44,
              decoration: BoxDecoration(
                color: isCompleted ? const Color(0xFFE8F5E9) : const Color(0xFFFFF3E0),
                shape: BoxShape.circle,
              ),
              child: Icon(
                isCompleted ? Icons.check_circle_rounded : Icons.radio_button_unchecked_rounded, 
                color: isCompleted ? const Color(0xFF2E7D32) : const Color(0xFFE65100), 
                size: 22,
              ),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title, 
                    maxLines: 2, 
                    overflow: TextOverflow.ellipsis, 
                    style: TextStyle(color: isActive ? textDark : Colors.grey, fontSize: 14, fontWeight: FontWeight.w900),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    isCompleted ? 'Sudah Dikerjakan' : 'Belum Dikerjakan', 
                    style: TextStyle(
                      color: isCompleted ? const Color(0xFF2E7D32) : const Color(0xFFE65100), 
                      fontSize: 12, 
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(width: 12),
            Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Text(
                  'Nilai',
                  style: TextStyle(color: Colors.grey, fontSize: 11, fontWeight: FontWeight.w500),
                ),
                const SizedBox(height: 2),
                Text(
                  score,
                  style: TextStyle(
                    color: isCompleted ? primaryRed : textDark,
                    fontSize: 18,
                    fontWeight: FontWeight.w900,
                  ),
                ),
              ],
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