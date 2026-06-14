import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:fl_chart/fl_chart.dart'; 
import '../services/auth_service.dart';
import 'announcement_detail_page.dart'; // ✅ Import halaman detail

class ReportPage extends StatefulWidget {
  final String token;
  final Map userData; 
  final VoidCallback onGoToHome;

  const ReportPage({
    super.key, 
    required this.token,
    required this.userData,
    required this.onGoToHome,
  });

  @override
  State<ReportPage> createState() => _ReportPageState();
}

class _ReportPageState extends State<ReportPage> {
  bool isLoading = true;
  
  List announcements = [];
  List<FlSpot> chartData = []; 
  List<String> tryoutTitles = []; 

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

  @override
  void initState() {
    super.initState();
    _fetchReportData();
  }

  /// ✅ Perbaiki URL gambar
  String _fixImageUrl(String path) {
    if (path.isEmpty) return '';
    
    // Jika sudah full URL, return langsung
    if (path.startsWith('http://') || path.startsWith('https://')) {
      return path;
    }
    
    // Bersihkan path dari leading slash
    String cleanPath = path;
    if (cleanPath.startsWith('/')) {
      cleanPath = cleanPath.substring(1);
    }
    
    // Jika sudah mengandung storage/, langsung gunakan
    if (cleanPath.startsWith('storage/')) {
      return 'http://10.0.2.2:8000/' + cleanPath;
    }
    
    // Jika path dimulai dengan announcements/
    if (cleanPath.startsWith('announcements/')) {
      return 'http://10.0.2.2:8000/storage/' + cleanPath;
    }
    
    // Default: simpan di folder announcements
    return 'http://10.0.2.2:8000/storage/announcements/' + cleanPath;
  }

  // Ambil User ID
  int _getUserId() {
    try {
      var data = widget.userData;
      if (data.containsKey('data') && data['data'] is Map) {
        var inner = data['data'];
        if (inner['id'] != null) return int.parse(inner['id'].toString());
        if (inner['usersID'] != null) return int.parse(inner['usersID'].toString());
        if (inner['user_id'] != null) return int.parse(inner['user_id'].toString());
      }
      if (data.containsKey('usersID') && data['usersID'] != null) return int.parse(data['usersID'].toString());
      if (data.containsKey('user_id') && data['user_id'] != null) return int.parse(data['user_id'].toString());
      if (data.containsKey('user') && data['user'] != null) {
        if (data['user'].containsKey('id')) return int.parse(data['user']['id'].toString());
        if (data['user'].containsKey('usersID')) return int.parse(data['user']['usersID'].toString());
      }
      if (data.containsKey('student') && data['student'] != null) {
        if (data['student'].containsKey('user_id')) return int.parse(data['student']['user_id'].toString());
      }
      if (data.containsKey('id') && data['id'] != null) return int.parse(data['id'].toString());
    } catch (e) {
      debugPrint("❌ Gagal parse ID: $e");
    }
    return 0;
  }

  Future<void> _fetchReportData() async {
    try {
      final annRes = await AuthService.getAnnouncements(widget.token).catchError((_) => http.Response('[]', 500));
      
      int currentUserId = _getUserId();
      debugPrint("📊 Mengambil riwayat untuk User ID: $currentUserId");

      final reportRes = await AuthService.getTryoutHistory(widget.token, currentUserId);

      if (mounted) {
        setState(() {
          // Parsing Pengumuman
          if (annRes.statusCode == 200) {
            final annDecoded = jsonDecode(annRes.body);
            announcements = annDecoded is List ? annDecoded : (annDecoded['data'] ?? []);
          }

          // Parsing Riwayat Nilai
          if (reportRes.statusCode == 200) {
            final decoded = jsonDecode(reportRes.body);
            List history = decoded is List ? decoded : (decoded['data'] ?? []);
            
            chartData.clear();
            tryoutTitles.clear();
            
            if (history.isNotEmpty) {
              // Balik urutan agar data terlama di kiri, terbaru di kanan
              history = history.reversed.toList();
              int count = history.length > 7 ? 7 : history.length;
              
              for (var i = 0; i < count; i++) {
                double score = double.parse((history[i]['score'] ?? 0).toString());
                chartData.add(FlSpot(i.toDouble(), score));
                
                // Gunakan 'title' sesuai JSON response dari Golang
                String title = history[i]['title'] ?? history[i]['tryout_name'] ?? 'TO ${i+1}';
                tryoutTitles.add(title.length > 6 ? '${title.substring(0,6)}...' : title);
              }
            }
          }
          isLoading = false;
        });
      }
    } catch (e) {
      debugPrint("❌ Error Fetching Report: $e");
      if (mounted) setState(() => isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: pageBg,
      appBar: AppBar(
        flexibleSpace: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [primaryRed, accentTeal],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
        ),
        title: const Text("Learning Report", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
      ),
      body: isLoading 
        ? const Center(child: CircularProgressIndicator(color: accentTeal))
        : RefreshIndicator(
            onRefresh: _fetchReportData,
            color: accentTeal,
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              physics: const AlwaysScrollableScrollPhysics(),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Icon(Icons.campaign_rounded, color: accentTeal),
                      const SizedBox(width: 10),
                      const Text("Pengumuman Terbaru", style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: textDark)),
                    ],
                  ),
                  const SizedBox(height: 15),
                  
                  // ✅ GAMBAR BISA DIKLIK - LANGSUNG KE DETAIL
                  if (announcements.isNotEmpty)
                    GestureDetector(
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => AnnouncementDetailPage(
                              data: announcements[0],
                            ),
                          ),
                        );
                      },
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(15),
                        child: Image.network(
                          (announcements[0]['image_url']?.toString() ?? '').isNotEmpty 
                              ? announcements[0]['image_url'].toString()
                              : _fixImageUrl(announcements[0]['image']?.toString() ?? ''),
                          height: 180,
                          width: double.infinity,
                          fit: BoxFit.cover,
                          loadingBuilder: (context, child, loadingProgress) {
                            if (loadingProgress == null) return child;
                            return Container(
                              height: 180,
                              width: double.infinity,
                              color: Colors.grey[200],
                              child: Center(
                                child: CircularProgressIndicator(strokeWidth: 2, color: accentTeal),
                              ),
                            );
                          },
                          errorBuilder: (context, error, stackTrace) => Container(
                            height: 180,
                            width: double.infinity,
                            color: Colors.grey[200],
                            child: const Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(Icons.broken_image, color: Colors.grey, size: 40),
                                SizedBox(height: 8),
                                Text("Gambar tidak tersedia", style: TextStyle(color: Colors.grey, fontSize: 12)),
                              ],
                            ),
                          ),
                        ),
                      ),
                    )
                  else 
                    Container(
                      height: 180,
                      width: double.infinity,
                      decoration: BoxDecoration(color: Colors.grey[200], borderRadius: BorderRadius.circular(15)),
                      child: const Center(child: Text("Tidak ada pengumuman", style: TextStyle(color: Colors.grey))),
                    ),

                  const SizedBox(height: 40),

                  Row(
                    children: [
                      Icon(Icons.auto_graph_rounded, color: accentTeal),
                      const SizedBox(width: 10),
                      const Text("Statistik Belajar (7 Terakhir)", style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: textDark)),
                    ],
                  ),
                  const SizedBox(height: 5),
                  const Text("Grafik hasil perkembangan nilai kamu", style: TextStyle(color: neutralGray, fontSize: 12)),
                  const SizedBox(height: 20),

                  Container(
                    height: 250,
                    width: double.infinity,
                    padding: const EdgeInsets.fromLTRB(15, 30, 20, 10),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 15, offset: const Offset(0, 5))]
                    ),
                    child: chartData.isEmpty
                      ? const Center(child: Text("Belum ada data nilai.", style: TextStyle(color: neutralGray)))
                      : LineChart(
                          LineChartData(
                            gridData: FlGridData(
                              show: true, 
                              drawVerticalLine: false, 
                              getDrawingHorizontalLine: (value) => FlLine(color: Colors.grey[200]!, strokeWidth: 1)
                            ),
                            titlesData: FlTitlesData(
                              show: true,
                              rightTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
                              topTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
                              bottomTitles: AxisTitles(
                                sideTitles: SideTitles(
                                  showTitles: true,
                                  reservedSize: 30,
                                  getTitlesWidget: (value, meta) {
                                    int index = value.toInt();
                                    if (index >= 0 && index < tryoutTitles.length) {
                                      return SideTitleWidget(
                                        axisSide: meta.axisSide,
                                        child: Padding(
                                          padding: const EdgeInsets.only(top: 8.0),
                                          child: Text(tryoutTitles[index], style: const TextStyle(fontSize: 10, color: neutralGray)),
                                        ),
                                      );
                                    }
                                    return const Text('');
                                  },
                                ),
                              ),
                              leftTitles: AxisTitles(
                                sideTitles: SideTitles(
                                  showTitles: true,
                                  reservedSize: 40,
                                  getTitlesWidget: (value, meta) => SideTitleWidget(
                                    axisSide: meta.axisSide, 
                                    child: Text("${value.toInt()}", style: const TextStyle(fontSize: 10, color: neutralGray))
                                  ),
                                ),
                              ),
                            ),
                            borderData: FlBorderData(show: false),
                            minX: 0,
                            maxX: chartData.length > 1 ? (chartData.length - 1).toDouble() : 1.0,
                            minY: 0,
                            maxY: 100, 
                            lineBarsData: [
                              LineChartBarData(
                                spots: chartData.length == 1 
                                    ? [chartData[0], FlSpot(1.0, chartData[0].y)]
                                    : chartData,
                                isCurved: true,
                                color: accentTeal,
                                barWidth: 3,
                                isStrokeCapRound: true,
                                dotData: const FlDotData(show: true),
                                belowBarData: BarAreaData(
                                  show: true,
                                  color: accentTeal.withOpacity(0.15),
                                ),
                              ),
                            ],
                          ),
                        ),
                  ),
                  
                  const SizedBox(height: 30),
                  
                  if (chartData.isEmpty)
                    Center(
                      child: TextButton.icon(
                        onPressed: widget.onGoToHome, 
                        icon: Icon(Icons.arrow_back, color: accentTeal), 
                        label: Text("Kembali ke Beranda", style: TextStyle(color: accentTeal, fontWeight: FontWeight.bold))
                      ),
                    ),
                    
                  const SizedBox(height: 50),
                ],
              ),
            ),
          ),
    );
  }
}