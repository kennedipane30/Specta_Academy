import 'package:flutter/material.dart';
import 'dart:convert';
import 'dart:async'; // ✨ MODIFIKASI: Import async untuk menjalankan Timer otomatis
import 'package:http/http.dart' as http;
import 'package:fl_chart/fl_chart.dart'; 
import '../services/auth_service.dart';
import 'announcement_detail_page.dart'; 

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
  
  Timer? _autoRefreshTimer; // ✨ MODIFIKASI: Variabel Timer

  // ============================================================
  // 🎨 PALET WARNA SPEKTA
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
    _fetchReportData(); // Ambil data pertama kali dengan loading
    
    // ✨ MODIFIKASI: Jalankan Auto-Refresh diam-diam setiap 5 detik
    // Jadi saat user selesai ujian dan buka tab ini, nilainya sudah terupdate!
    _autoRefreshTimer = Timer.periodic(const Duration(seconds: 5), (_) {
      _fetchReportData(isSilent: true);
    });
  }

  @override
  void dispose() {
    _autoRefreshTimer?.cancel(); // ✨ MODIFIKASI: Matikan timer jika halaman ditutup
    super.dispose();
  }

  String _fixImageUrl(String path) {
    if (path.isEmpty) return '';
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    
    String cleanPath = path;
    if (cleanPath.startsWith('/')) cleanPath = cleanPath.substring(1);
    if (cleanPath.startsWith('storage/')) return 'http://10.0.2.2:8000/' + cleanPath;
    if (cleanPath.startsWith('announcements/')) return 'http://10.0.2.2:8000/storage/' + cleanPath;
    return 'http://10.0.2.2:8000/storage/announcements/' + cleanPath;
  }

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

  // ✨ MODIFIKASI: Menambahkan parameter isSilent agar tidak muncul loading saat auto-refresh
  Future<void> _fetchReportData({bool isSilent = false}) async {
    try {
      final annRes = await AuthService.getAnnouncements(widget.token).catchError((_) => http.Response('[]', 500));
      
      int currentUserId = _getUserId();
      final reportRes = await AuthService.getTryoutHistory(widget.token, currentUserId).catchError((_) => http.Response('[]', 500));

      if (!isSilent && (annRes.statusCode >= 500 || reportRes.statusCode >= 500)) {
         if (mounted) {
           ScaffoldMessenger.of(context).showSnackBar(SnackBar(
             content: const Text("Mohon maaf sistem sedang sibuk", style: TextStyle(fontWeight: FontWeight.bold)),
             backgroundColor: primaryRed,
             behavior: SnackBarBehavior.floating,
             shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))
           ));
         }
      }

      if (mounted) {
        setState(() {
          if (annRes.statusCode == 200) {
            final annDecoded = jsonDecode(annRes.body);
            announcements = annDecoded is List ? annDecoded : (annDecoded['data'] ?? []);
          }

          if (reportRes.statusCode == 200) {
            final decoded = jsonDecode(reportRes.body);
            List history = decoded is List ? decoded : (decoded['data'] ?? []);
            
            // Simpan data ke list penampung sementara agar grafik tidak berkedip saat update
            List<FlSpot> tempChartData = [];
            List<String> tempTitles = [];
            
            if (history.isNotEmpty) {
              history = history.reversed.toList();
              int count = history.length > 7 ? 7 : history.length;
              
              for (var i = 0; i < count; i++) {
                double score = double.parse((history[i]['score'] ?? 0).toString());
                tempChartData.add(FlSpot(i.toDouble(), score));
                
                String title = history[i]['title'] ?? history[i]['tryout_name'] ?? 'TO ${i+1}';
                tempTitles.add(title.length > 6 ? '${title.substring(0,6)}...' : title);
              }
            }
            
            // Timpa data lama dengan data baru yang didapat
            chartData = tempChartData;
            tryoutTitles = tempTitles;
          }
          if (!isSilent) isLoading = false;
        });
      }
    } catch (e) {
      debugPrint("❌ Error Fetching Report: $e");
      if (mounted && !isSilent) {
        setState(() => isLoading = false);
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          content: const Text("Mohon maaf sistem sedang sibuk", style: TextStyle(fontWeight: FontWeight.bold)),
          backgroundColor: primaryRed,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))
        ));
      }
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
            onRefresh: () => _fetchReportData(isSilent: false),
            color: accentTeal,
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              physics: const AlwaysScrollableScrollPhysics(),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      const Icon(Icons.campaign_rounded, color: accentTeal),
                      const SizedBox(width: 10),
                      const Text("Pengumuman Terbaru", style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: textDark)),
                    ],
                  ),
                  const SizedBox(height: 15),
                  
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
                              child: const Center(
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
                      const Icon(Icons.auto_graph_rounded, color: accentTeal),
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
                                  interval: 1, 
                                  getTitlesWidget: (value, meta) {
                                    int index = value.toInt();
                                    if (value % 1 == 0 && index >= 0 && index < tryoutTitles.length) {
                                      return SideTitleWidget(
                                        axisSide: meta.axisSide,
                                        space: 8,
                                        child: Text(tryoutTitles[index], style: const TextStyle(fontSize: 10, color: neutralGray)),
                                      );
                                    }
                                    return const SizedBox.shrink();
                                  },
                                ),
                              ),
                              leftTitles: AxisTitles(
                                sideTitles: SideTitles(
                                  showTitles: true,
                                  reservedSize: 40,
                                  interval: 20, 
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
                        icon: const Icon(Icons.arrow_back, color: accentTeal), 
                        label: const Text("Kembali ke Beranda", style: TextStyle(color: accentTeal, fontWeight: FontWeight.bold))
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