import 'package:flutter/material.dart';
import 'package:carousel_slider/carousel_slider.dart';
import 'package:fl_chart/fl_chart.dart';
import 'dart:convert';
import '../services/auth_service.dart';
import 'announcement_detail_page.dart';

class ReportPage extends StatefulWidget {
  final String token;
  final Map userData;
  final VoidCallback onGoToHome; // ✨ Tambahkan callback untuk balik ke Home

  const ReportPage({
    super.key, 
    required this.token, 
    required this.userData,
    required this.onGoToHome, // ✨ Wajib diisi
  });

  @override
  State<ReportPage> createState() => _ReportPageState();
}

class _ReportPageState extends State<ReportPage> {
  final Color spektaRed = const Color(0xFF990000);
  
  // State untuk Data Dinamis
  List<double> dynamicScores = [];
  double avgScore = 0.0;
  double maxScore = 0.0;
  bool isChartLoading = true;

  @override
  void initState() {
    super.initState();
    _loadAllData();
  }

  Future<void> _loadAllData() async {
    await _fetchChartData();
  }

  // FUNGSI AMBIL DATA NILAI DARI API
  Future<void> _fetchChartData() async {
    try {
      final resp = await AuthService.getLearningReport(widget.token);
      if (resp.statusCode == 200) {
        List<dynamic> data = jsonDecode(resp.body)['data'];
        
        if (data.isNotEmpty) {
          List<double> scores = data.map((item) => double.parse(item['score'].toString())).toList();
          
          setState(() {
            dynamicScores = scores;
            avgScore = scores.reduce((a, b) => a + b) / scores.length;
            maxScore = scores.reduce((a, b) => a > b ? a : b);
            isChartLoading = false;
          });
        } else {
          setState(() => isChartLoading = false);
        }
      }
    } catch (e) {
      debugPrint("Chart Error: $e");
      setState(() => isChartLoading = false);
    }
  }

  // FUNGSI AMBIL PENGUMUMAN
  Future<List<dynamic>> fetchAnnouncements() async {
    final resp = await AuthService.getAnnouncements(widget.token);
    if (resp.statusCode == 200) {
      return jsonDecode(resp.body)['data'] ?? [];
    }
    return [];
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFBFBFB),
      appBar: AppBar(
        // ✨ TAMPILAN HEADER YANG LEBIH PENDEK & RAPI
        title: const Text("Learning Report", 
          style: TextStyle(fontWeight: FontWeight.w900, color: Colors.white, fontSize: 18)),
        backgroundColor: spektaRed,
        elevation: 0,
        centerTitle: true,
        // ✨ TAMBAHKAN IKON PANAH KEMBALI
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white, size: 28),
          onPressed: widget.onGoToHome, // Balik ke Tab Home
        ),
      ),
      body: RefreshIndicator(
        onRefresh: _loadAllData,
        child: SingleChildScrollView(
          padding: const EdgeInsets.only(bottom: 120),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildSectionHeader("Pengumuman Terbaru", Icons.campaign_rounded),
              _buildAnnouncementSection(),

              const SizedBox(height: 30),
              
              _buildSectionHeader("Statistik Belajar (7 Terakhir)", Icons.auto_graph_rounded),
              const Padding(
                padding: EdgeInsets.symmetric(horizontal: 24),
                child: Text("Grafik hasil perkembangan nilai kamu", style: TextStyle(color: Colors.grey, fontSize: 12)),
              ),
              const SizedBox(height: 20),
              
              // TAMPILKAN CHART DINAMIS
              _buildLineChartCard(),

              const SizedBox(height: 25),
              _buildScoreSummary(),
            ],
          ),
        ),
      ),
    );
  }

  // --- WIDGET PENGUMUMAN ---
  Widget _buildAnnouncementSection() {
    return FutureBuilder<List<dynamic>>(
      future: fetchAnnouncements(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) return const SizedBox(height: 180, child: Center(child: CircularProgressIndicator()));
        if (!snapshot.hasData || snapshot.data!.isEmpty) return const Center(child: Text("Tidak ada pengumuman"));

        return CarouselSlider(
          options: CarouselOptions(height: 180, autoPlay: true, enlargeCenterPage: true, viewportFraction: 0.88),
          items: snapshot.data!.map((item) {
            String imgUrl = (item['image'] ?? '').toString().replaceAll('127.0.0.1', '10.0.2.2');
            if (!imgUrl.startsWith('http')) imgUrl = "http://10.0.2.2:8000/storage/$imgUrl";

            return InkWell(
              onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => AnnouncementDetailPage(data: item))),
              child: Container(
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(25),
                  image: DecorationImage(image: NetworkImage(imgUrl), fit: BoxFit.cover),
                  boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10, offset: const Offset(0, 5))],
                ),
              ),
            );
          }).toList(),
        );
      },
    );
  }

  // --- WIDGET CHART DINAMIS ---
  Widget _buildLineChartCard() {
    if (isChartLoading) return const SizedBox(height: 250, child: Center(child: CircularProgressIndicator()));
    if (dynamicScores.isEmpty) {
      return Container(
        height: 200, margin: const EdgeInsets.all(24),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(30)),
        child: const Center(child: Text("Belum ada data nilai.", style: TextStyle(color: Colors.grey))),
      );
    }

    return Container(
      height: 250, margin: const EdgeInsets.symmetric(horizontal: 24),
      padding: const EdgeInsets.fromLTRB(10, 25, 25, 10),
      decoration: BoxDecoration(
        color: Colors.white, borderRadius: BorderRadius.circular(30),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 24)],
      ),
      child: LineChart(
        LineChartData(
          gridData: const FlGridData(show: true, drawVerticalLine: false),
          titlesData: FlTitlesData(
            rightTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
            topTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
            bottomTitles: AxisTitles(
              sideTitles: SideTitles(
                showTitles: true,
                getTitlesWidget: (value, meta) {
                  return Text("Ke-${value.toInt() + 1}", style: const TextStyle(fontSize: 9, color: Colors.grey, fontWeight: FontWeight.bold));
                },
              ),
            ),
          ),
          borderData: FlBorderData(show: false),
          minX: 0, maxX: (dynamicScores.length - 1).toDouble(), minY: 0, maxY: 100,
          lineBarsData: [
            LineChartBarData(
              spots: dynamicScores.asMap().entries.map((e) => FlSpot(e.key.toDouble(), e.value)).toList(),
              isCurved: true, color: spektaRed, barWidth: 5, isStrokeCapRound: true,
              dotData: const FlDotData(show: true),
              belowBarData: BarAreaData(show: true, gradient: LinearGradient(
                begin: Alignment.topCenter, end: Alignment.bottomCenter,
                colors: [spektaRed.withOpacity(0.2), spektaRed.withOpacity(0.0)],
              )),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildScoreSummary() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Row(
        children: [
          _statCard("Nilai Tertinggi", maxScore.toStringAsFixed(0)),
          const SizedBox(width: 15),
          _statCard("Rata-rata", avgScore.toStringAsFixed(1)),
        ],
      ),
    );
  }

  Widget _buildSectionHeader(String title, IconData icon) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(24, 25, 24, 8),
      child: Row(children: [Icon(icon, color: spektaRed, size: 20), const SizedBox(width: 8), Text(title, style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w800))]),
    );
  }

  Widget _statCard(String label, String value) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(22), border: Border.all(color: Colors.grey[100]!)),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(label.toUpperCase(), style: TextStyle(fontSize: 10, color: Colors.grey[500], fontWeight: FontWeight.bold)),
          const SizedBox(height: 4),
          Text(value, style: TextStyle(fontSize: 18, color: spektaRed, fontWeight: FontWeight.w900)),
        ]),
      ),
    );
  }
}