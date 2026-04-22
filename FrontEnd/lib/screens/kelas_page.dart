import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
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

  @override
  void initState() {
    super.initState();
    _fetchPrograms(); 
  }

  // Ambil daftar kelas dari API
  Future<void> _fetchPrograms() async {
    try {
      final response = await http.get(
        Uri.parse('http://10.0.2.2:8000/api/classes'), 
        headers: {
          'Authorization': 'Bearer ${widget.token}',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (mounted) {
          setState(() {
            programs = data['data']; 
            isLoading = false;
          });
        }
      }
    } catch (e) {
      if (mounted) setState(() => isLoading = false);
      debugPrint("Error fetch programs: $e");
    }
  }

  // --- FUNGSI UTAMA: CEK PROFIL TERBARU SEBELUM MASUK DETAIL ---
  Future<void> _checkProfileAndNavigate(BuildContext context, Map<String, dynamic> item) async {
    // 1. Tampilkan loading overlay agar sinkronisasi terasa mantap
    showDialog(
      context: context, 
      barrierDismissible: false, 
      builder: (context) => const Center(child: CircularProgressIndicator(color: Colors.white))
    );

    try {
      // 2. Tarik data user terbaru dari server
      final response = await http.get(
        Uri.parse('http://10.0.2.2:8000/api/user'),
        headers: {
          'Authorization': 'Bearer ${widget.token}',
          'Accept': 'application/json',
        },
      );

      if (!mounted) return;
      Navigator.pop(context); // Tutup loading overlay

      if (response.statusCode == 200) {
        final latestUserData = json.decode(response.body);
        var student = latestUserData['student'];

        // 3. Validasi apakah biodata benar-benar sudah lengkap di DB
        bool isComplete = student != null &&
            student['parent_name'] != null && student['parent_name'] != "-" &&
            student['address'] != null && student['address'] != "-" &&
            student['parent_phone'] != null && student['parent_phone'] != "-";

        if (isComplete) {
          // JIKA LENGKAP -> Masuk ke Halaman Detail (Harga akan nampak)
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => ClassDetailPage(
                classId: item['class_id'], 
                className: item['program_name'], 
                token: widget.token,
                userData: latestUserData, // Gunakan data terbaru hasil fetch
              ),
            ),
          );
        } else {
          // JIKA BELUM LENGKAP -> Munculkan Dialog
          _showPremiumProfileDialog(context);
        }
      }
    } catch (e) {
      if (mounted) Navigator.pop(context);
      debugPrint("Error Sinkronisasi Profile: $e");
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Koneksi gagal, silakan coba lagi."))
      );
    }
  }

  void _showPremiumProfileDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30)),
        contentPadding: const EdgeInsets.all(25),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(color: spektaRed.withOpacity(0.05), shape: BoxShape.circle),
              child: Icon(Icons.assignment_ind_rounded, size: 60, color: spektaRed),
            ),
            const SizedBox(height: 20),
            const Text("Biodata Belum Lengkap", textAlign: TextAlign.center, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 20)),
            const SizedBox(height: 12),
            Text(
              "Untuk melihat harga dan mendaftar, harap lengkapi Alamat, Nama Orang Tua, dan No. WA Orang Tua di menu Profil.",
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.grey.shade600, fontSize: 13, height: 1.5),
            ),
            const SizedBox(height: 30),
            ElevatedButton(
              onPressed: () {
                Navigator.pop(context);
                widget.onGoToProfile(); 
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed, foregroundColor: Colors.white,
                minimumSize: const Size(double.infinity, 55), elevation: 0,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
              ),
              child: const Text("LENGKAPI SEKARANG", style: TextStyle(fontWeight: FontWeight.bold, letterSpacing: 1)),
            ),
            const SizedBox(height: 10),
            TextButton(
              onPressed: () => Navigator.pop(context),
              style: TextButton.styleFrom(foregroundColor: Colors.grey),
              child: const Text("Nanti Saja"),
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
                SliverAppBar(
                  expandedHeight: 120.0, pinned: true, elevation: 0, backgroundColor: spektaRed,
                  flexibleSpace: FlexibleSpaceBar(
                    titlePadding: const EdgeInsets.only(left: 20, bottom: 16),
                    title: const Text("Study Program", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 18)),
                    background: Container(decoration: BoxDecoration(gradient: LinearGradient(colors: [spektaRed, const Color(0xFF660000)], begin: Alignment.topLeft, end: Alignment.bottomRight))),
                  ),
                ),
                programs.isEmpty
                ? const SliverFillRemaining(child: Center(child: Text("Belum ada program tersedia.")))
                : SliverPadding(
                    padding: const EdgeInsets.fromLTRB(20, 25, 20, 100),
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

  Widget _buildProgramCard(BuildContext context, Map<String, dynamic> item) {
    String rawUrl = item['image_url'] ?? '';
    String finalImageUrl = rawUrl.replaceAll('127.0.0.1', '10.0.2.2').replaceAll('localhost', '10.0.2.2');

    return Container(
      margin: const EdgeInsets.only(bottom: 30),
      decoration: BoxDecoration(
        color: Colors.white, borderRadius: BorderRadius.circular(35),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 25, offset: const Offset(0, 12))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Stack(
            children: [
              ClipRRect(
                borderRadius: const BorderRadius.vertical(top: Radius.circular(35)),
                child: Image.network(
                  finalImageUrl, height: 200, width: double.infinity, fit: BoxFit.cover,
                  errorBuilder: (c, e, s) => Container(height: 200, color: Colors.grey[100], child: const Icon(Icons.broken_image_outlined, color: Colors.grey, size: 40)),
                ),
              ),
              Positioned(
                top: 20, right: 20,
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                  decoration: BoxDecoration(color: Colors.white.withOpacity(0.9), borderRadius: BorderRadius.circular(15), boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10)]),
                  child: Text("IDR ${item['price']}", style: TextStyle(color: spektaRed, fontWeight: FontWeight.w900, fontSize: 12)),
                ),
              ),
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
                InkWell(
                  onTap: () => _checkProfileAndNavigate(context, item),
                  borderRadius: BorderRadius.circular(20),
                  child: Container(
                    width: double.infinity, padding: const EdgeInsets.symmetric(vertical: 18),
                    decoration: BoxDecoration(gradient: LinearGradient(colors: [spektaYellow, const Color(0xFFD49E00)]), borderRadius: BorderRadius.circular(20), boxShadow: [BoxShadow(color: spektaYellow.withOpacity(0.3), blurRadius: 15, offset: const Offset(0, 8))]),
                    child: const Row(
                      mainAxisAlignment: MainAxisAlignment.center, 
                      children: [
                        Text("VIEW DETAILS", style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 14, letterSpacing: 1)),
                        SizedBox(width: 10),
                        Icon(Icons.arrow_forward_ios_rounded, color: Colors.white, size: 14),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}