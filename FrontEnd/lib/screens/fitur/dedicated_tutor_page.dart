import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../services/tutor_service.dart';

class DedicatedTutorPage extends StatefulWidget {
  final String token;
  final Map userData;
  const DedicatedTutorPage({super.key, required this.token, required this.userData});

  @override
  State<DedicatedTutorPage> createState() => _DedicatedTutorPageState();
}

class _DedicatedTutorPageState extends State<DedicatedTutorPage> {
  // ============================================================
  // 🎨 PALET WARNA BARU SPEKTA GEN-Z (KONTRAS TINGGI, CLEAN, PREMIUM)
  // ============================================================
  static const Color primaryRed = Color(0xFFC5352C);       // Merah Spekta
  static const Color brightRed = Color(0xFFE53935);        // Aksen Merah Terang
  static const Color accentTeal = Color(0xFF2EA8AB);       // Teal Estetik
  static const Color darkTeal = Color(0xFF00696C);         // Teal Gelap
  static const Color pageBg = Color(0xFFF8FAFC);           // Slate 50 (Abu Terang Bersih)
  static const Color textDark = Color(0xFF0F172A);         // Slate 900
  static const Color textDarkVariant = Color(0xFF334155);  // Slate 700
  static const Color neutralGray = Color(0xFF64748B);      // Slate 500
  static const Color outlineVariant = Color(0xFFE2E8F0);   // Border Abu Halus
  static const Color lightBlueBg = Color(0xFFEFF4FF);      // Latar Ikon
  
  List materials = [];    
  List historyList = [];  
  int remainingQuota = 0; 
  int maxQuota = 3;
  bool isLoading = true;

  int? selectedMaterialId;
  DateTime? selectedDate;
  TimeOfDay? selectedTime;

  @override
  void initState() {
    super.initState();
    _fetchPageData();
  }

  Future<void> _fetchPageData() async {
    if (!mounted) return;
    setState(() => isLoading = true);
    try {
      final response = await TutorService.getTutorHistory(widget.token);

      if (mounted) {
        if (response.statusCode == 200) {
          final Map<String, dynamic> responseData = jsonDecode(response.body);
          final Map<String, dynamic> apiData = responseData['data']; 

          setState(() {
            historyList = apiData['history'] ?? [];
            materials = apiData['topics'] ?? [];
            remainingQuota = int.parse(apiData['quota']['remaining'].toString());
            maxQuota = int.parse(apiData['quota']['max'].toString());
            isLoading = false;
          });
        } else {
          _showSnack("Gagal memuat data", isError: true);
          setState(() => isLoading = false);
        }
      }
    } catch (e) {
      if (mounted) {
        _showSnack("Kesalahan koneksi", isError: true);
        setState(() => isLoading = false);
      }
    }
  }

  Future<void> _selectDate() async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now().add(const Duration(days: 1)),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 90)),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(primary: primaryRed),
          ),
          child: child!,
        );
      },
    );
    if (picked != null) {
      setState(() {
        selectedDate = picked;
      });
    }
  }

  Future<void> _selectTime() async {
    final TimeOfDay? picked = await showTimePicker(
      context: context,
      initialTime: const TimeOfDay(hour: 9, minute: 0),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(primary: primaryRed),
          ),
          child: child!,
        );
      },
    );
    if (picked != null) {
      setState(() {
        selectedTime = picked;
      });
    }
  }

  Future<void> _handlePost() async {
    if (selectedMaterialId == null) {
      _showSnack("Silakan pilih topik terlebih dahulu!", isError: true);
      return;
    }
    if (selectedDate == null) {
      _showSnack("Silakan pilih tanggal!", isError: true);
      return;
    }
    if (selectedTime == null) {
      _showSnack("Silakan pilih waktu!", isError: true);
      return;
    }

    setState(() => isLoading = true);
    try {
      final body = {
        'material_id': selectedMaterialId,
        'date': DateFormat('yyyy-MM-dd').format(selectedDate!),
        'time': '${selectedTime!.hour.toString().padLeft(2, '0')}:${selectedTime!.minute.toString().padLeft(2, '0')}:00',
      };

      final res = await TutorService.submitTutor(body, widget.token);
      final resData = jsonDecode(res.body);

      if (res.statusCode == 201) {
        _showSnack("Pengajuan berhasil dikirim!");
        setState(() { 
          selectedMaterialId = null; 
          selectedDate = null;
          selectedTime = null;
        });
        _fetchPageData(); 
      } else {
        String msg = resData['message'] ?? "Gagal mengirim pengajuan";
        _showSnack(msg, isError: true);
        setState(() => isLoading = false);
      }
    } catch (e) {
      _showSnack("Terjadi kesalahan sistem", isError: true);
      setState(() => isLoading = false);
    }
  }

  void _showSnack(String message, {bool isError = false}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message, style: const TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: isError ? primaryRed : const Color(0xFF10B981),
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      ),
    );
  }

  String _formatDate(DateTime date) {
    final months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    final weekdays = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    return '${weekdays[date.weekday - 1]}, ${date.day} ${months[date.month - 1]} ${date.year}';
  }

  String _formatTime(TimeOfDay time) {
    return '${time.hour.toString().padLeft(2, '0')}:${time.minute.toString().padLeft(2, '0')} WIB';
  }

  String _formatDateFromApi(String dateString) {
    try {
      DateTime date = DateTime.parse(dateString);
      final months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
      return '${date.day} ${months[date.month - 1]} ${date.year}';
    } catch (e) {
      return dateString.split('T')[0];
    }
  }

  String _formatTimeFromApi(String timeString) {
    if (timeString.isEmpty || timeString == '00:00:00') return '';
    try {
      if (timeString.contains(':')) {
        final parts = timeString.split(':');
        return '${parts[0]}:${parts[1]} WIB';
      }
      return timeString;
    } catch (e) {
      return timeString;
    }
  }

  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'confirmed': return const Color(0xFF10B981); // Hijau pastel
      case 'rejected': return primaryRed;               // Merah pastel
      case 'pending': return Colors.orange;             // Oranye pastel
      default: return Colors.grey;
    }
  }

  String _getStatusText(String status) {
    switch (status.toLowerCase()) {
      case 'confirmed': return 'Selesai';
      case 'rejected': return 'Ditolak';
      case 'pending': return 'Menunggu';
      default: return status;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: pageBg,
      body: isLoading
          ? const Center(
              child: CircularProgressIndicator(color: primaryRed),
            )
          : RefreshIndicator(
              onRefresh: _fetchPageData,
              color: primaryRed,
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: Stack(
                  children: [
                    // 1. HEADER GRADIENT MELENGKUNG (Tinggi disesuaikan menjadi 310)
                    _buildCurvedHeader(),

                    // 2. KONTEN BODY MELAYANG (Geser awal top padding dari 210 menjadi 265 agar tidak menabrak)
                    Padding(
                      padding: const EdgeInsets.fromLTRB(16, 265, 16, 40),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildRequestForm(),
                          const SizedBox(height: 24),
                          _buildHistorySection(),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
    );
  }

  // WIDGET HEADER LENGKUNG PREMIUM DENGAN GLASS-CARD KUOTA
  Widget _buildCurvedHeader() {
    return Container(
      height: 310, // Ditingkatkan menjadi 310 agar kelengkungan bawah terlihat rapi
      width: double.infinity,
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          colors: [primaryRed, accentTeal],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.vertical(
          bottom: Radius.circular(40),
        ),
      ),
      padding: const EdgeInsets.fromLTRB(16, 48, 16, 20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              CircleAvatar(
                backgroundColor: Colors.white.withOpacity(0.15),
                child: IconButton(
                  icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white, size: 16),
                  onPressed: () => Navigator.pop(context),
                ),
              ),
              const Text(
                "Dedicated Tutor",
                style: TextStyle(
                  color: Colors.white,
                  fontSize: 18,
                  fontWeight: FontWeight.w900,
                  letterSpacing: -0.5,
                ),
              ),
              const SizedBox(width: 40), // Penyeimbang horizontal
            ],
          ),
          const SizedBox(height: 18),
          _buildQuotaCard(),
        ],
      ),
    );
  }

  // KARTU KUOTA BERGAYA GLASSMORPHISM SANGAT ESTETIK
  Widget _buildQuotaCard() {
    final int usedQuota = maxQuota - remainingQuota;
    final double progress = (usedQuota / maxQuota).clamp(0.0, 1.0);

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.15),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.white.withOpacity(0.2)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(Icons.verified_user_rounded, color: Colors.white.withOpacity(0.9), size: 18),
              const SizedBox(width: 8),
              Text(
                "Sisa Kuota Bulan Ini",
                style: TextStyle(
                  color: Colors.white.withOpacity(0.9), 
                  fontSize: 11,
                  fontWeight: FontWeight.bold,
                  letterSpacing: 0.5,
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              RichText(
                text: TextSpan(
                  children: [
                    TextSpan(
                      text: "$remainingQuota",
                      style: const TextStyle(
                        fontSize: 32,
                        fontWeight: FontWeight.w900,
                        color: Colors.white,
                      ),
                    ),
                    TextSpan(
                      text: "/$maxQuota",
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w700,
                        color: Colors.white.withOpacity(0.6),
                      ),
                    ),
                  ],
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Row(
                  children: [
                    Icon(Icons.check_circle_rounded, size: 12, color: Colors.white.withOpacity(0.9)),
                    const SizedBox(width: 4),
                    Text(
                      "Digunakan $usedQuota",
                      style: const TextStyle(color: Colors.white, fontSize: 10.5, fontWeight: FontWeight.bold),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          ClipRRect(
            borderRadius: BorderRadius.circular(10),
            child: LinearProgressIndicator(
              value: progress,
              backgroundColor: Colors.white.withOpacity(0.25),
              valueColor: const AlwaysStoppedAnimation<Color>(Colors.white),
              minHeight: 6,
            ),
          ),
        ],
      ),
    );
  }

  // FORM REQUEST PENGAJUAN
  Widget _buildRequestForm() {
    final bool canSubmit = remainingQuota > 0;

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: outlineVariant.withOpacity(0.4)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 15,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Row(
            children: [
              Icon(Icons.edit_note_rounded, color: primaryRed, size: 24),
              SizedBox(width: 8),
              Text(
                "Ajukan Permintaan",
                style: TextStyle(fontSize: 16.5, fontWeight: FontWeight.w900, color: textDark),
              ),
            ],
          ),
          const SizedBox(height: 20),
          
          // Nama Siswa Box
          const Text(
            "Nama Siswa",
            style: TextStyle(fontSize: 11.5, fontWeight: FontWeight.bold, color: neutralGray),
          ),
          const SizedBox(height: 6),
          Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            decoration: BoxDecoration(
              color: lightBlueBg,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: const Color(0xFFDBE5FF)),
            ),
            child: Text(
              widget.userData['name'] ?? 'Kennedi Pane',
              style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: textDark),
            ),
          ),
          const SizedBox(height: 16),
          
          // Pilih Topik Dropdown
          const Text(
            "Pilih Topik",
            style: TextStyle(fontSize: 11.5, fontWeight: FontWeight.bold, color: neutralGray),
          ),
          const SizedBox(height: 6),
          _buildDropdownField(),
          const SizedBox(height: 16),
          
          // Tanggal & Waktu Row
          Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      "Tanggal",
                      style: TextStyle(fontSize: 11.5, fontWeight: FontWeight.bold, color: neutralGray),
                    ),
                    const SizedBox(height: 6),
                    _buildDateField(canSubmit),
                  ],
                ),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      "Waktu",
                      style: TextStyle(fontSize: 11.5, fontWeight: FontWeight.bold, color: neutralGray),
                    ),
                    const SizedBox(height: 6),
                    _buildTimeField(canSubmit),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),
          
          // Submit Button
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: canSubmit ? _handlePost : null,
              style: ElevatedButton.styleFrom(
                backgroundColor: primaryRed,
                disabledBackgroundColor: Colors.grey.shade300,
                padding: const EdgeInsets.symmetric(vertical: 14),
                elevation: 0,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
              child: Text(
                canSubmit ? "Ajukan Permintaan" : "Kuota Habis",
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 14,
                  fontWeight: FontWeight.w900,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  // DROPDOWN TOPIK PREMIUM
  Widget _buildDropdownField() {
    return Container(
      decoration: BoxDecoration(
        border: Border.all(color: outlineVariant),
        borderRadius: BorderRadius.circular(12),
        color: Colors.white,
      ),
      child: DropdownButtonFormField<int>(
        isExpanded: true,
        hint: const Padding(
          padding: EdgeInsets.symmetric(horizontal: 12),
          child: Text(
            "Pilih Topik",
            style: TextStyle(color: neutralGray, fontSize: 13, fontWeight: FontWeight.w600),
          ),
        ),
        value: selectedMaterialId,
        items: materials.map<DropdownMenuItem<int>>((item) {
          return DropdownMenuItem<int>(
            value: int.tryParse(item['material_id'].toString()),
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 12),
              child: Text(
                item['title'] ?? "Topik",
                style: const TextStyle(fontSize: 13.5, fontWeight: FontWeight.w700, color: textDark),
              ),
            ),
          );
        }).toList(),
        onChanged: remainingQuota > 0 ? (val) => setState(() => selectedMaterialId = val) : null,
        decoration: const InputDecoration(
          border: InputBorder.none,
          contentPadding: EdgeInsets.symmetric(vertical: 14),
        ),
        icon: const Padding(
          padding: EdgeInsets.only(right: 12),
          child: Icon(Icons.arrow_drop_down_rounded, color: primaryRed, size: 24),
        ),
      ),
    );
  }

  Widget _buildDateField(bool enabled) {
    return InkWell(
      onTap: enabled ? _selectDate : null,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 13),
        decoration: BoxDecoration(
          border: Border.all(color: outlineVariant),
          borderRadius: BorderRadius.circular(12),
          color: Colors.white,
        ),
        child: Row(
          children: [
            Expanded(
              child: Text(
                selectedDate != null ? _formatDate(selectedDate!) : "Pilih Tanggal",
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: TextStyle(
                  color: selectedDate != null ? textDark : neutralGray,
                  fontSize: 13,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ),
            if (selectedDate == null)
              const Icon(Icons.calendar_today_rounded, size: 16, color: neutralGray)
            else
              IconButton(
                onPressed: () => setState(() => selectedDate = null),
                icon: const Icon(Icons.close_rounded, size: 16, color: neutralGray),
                padding: EdgeInsets.zero,
                constraints: const BoxConstraints(),
              ),
          ],
        ),
      ),
    );
  }

  Widget _buildTimeField(bool enabled) {
    return InkWell(
      onTap: enabled ? _selectTime : null,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 13),
        decoration: BoxDecoration(
          border: Border.all(color: outlineVariant),
          borderRadius: BorderRadius.circular(12),
          color: Colors.white,
        ),
        child: Row(
          children: [
            Expanded(
              child: Text(
                selectedTime != null ? _formatTime(selectedTime!) : "Pilih Waktu",
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: TextStyle(
                  color: selectedTime != null ? textDark : neutralGray,
                  fontSize: 13,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ),
            if (selectedTime == null)
              const Icon(Icons.access_time_rounded, size: 16, color: neutralGray)
            else
              IconButton(
                onPressed: () => setState(() => selectedTime = null),
                icon: const Icon(Icons.close_rounded, size: 16, color: neutralGray),
                padding: EdgeInsets.zero,
                constraints: const BoxConstraints(),
              ),
          ],
        ),
      ),
    );
  }

  // RIWAYAT PENGAJUAN TUTOR PRIVATE BERGAYA BENTO CARD
  Widget _buildHistorySection() {
    if (historyList.isEmpty) {
      return Container(
        padding: const EdgeInsets.all(40),
        width: double.infinity,
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: outlineVariant.withOpacity(0.4)),
        ),
        child: const Column(
          children: [
            Icon(Icons.history_rounded, size: 48, color: neutralGray),
            const SizedBox(height: 12),
            Text(
              "Belum ada riwayat pengajuan",
              style: TextStyle(color: textDarkVariant, fontSize: 13, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 4),
            Text(
              "Ajukan permintaan bimbingan privat di atas",
              style: TextStyle(color: neutralGray, fontSize: 11, fontWeight: FontWeight.w500),
            ),
          ],
        ),
      );
    }

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: outlineVariant.withOpacity(0.4)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.015),
            blurRadius: 10,
            offset: const Offset(0, 4),
          )
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Padding(
            padding: EdgeInsets.all(16),
            child: Row(
              children: [
                Icon(Icons.history_rounded, color: primaryRed, size: 20),
                SizedBox(width: 8),
                Text(
                  "Riwayat Pengajuan",
                  style: TextStyle(fontSize: 15, fontWeight: FontWeight.w900, color: textDark),
                ),
              ],
            ),
          ),
          const Divider(height: 1, color: outlineVariant),
          ListView.separated(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: historyList.length,
            padding: EdgeInsets.zero,
            separatorBuilder: (context, index) => const Divider(height: 1, color: outlineVariant),
            itemBuilder: (context, index) {
              final item = historyList[index];
              final status = item['status'] ?? 'pending';
              final date = item['date'] ?? '';
              final time = item['time'] ?? '';
              
              final materialId = item['material_id'];
              final material = materials.firstWhere(
                (m) => m['material_id'] == materialId,
                orElse: () => {'title': 'Materi Umum'},
              );
              final topicTitle = material['title'] ?? 'Materi Umum';

              // Tentukan lencana warna pastel transparan sesuai status
              Color badgeColor;
              Color badgeText;
              if (status.toString().toLowerCase() == 'confirmed') {
                badgeColor = const Color(0xFFE8F5E9); // Hijau pastel
                badgeText = const Color(0xFF2E7D32);
              } else if (status.toString().toLowerCase() == 'rejected') {
                badgeColor = const Color(0xFFFEE2E2); // Merah pastel
                badgeText = const Color(0xFF991B1B);
              } else {
                badgeColor = const Color(0xFFFFF8E1); // Oranye pastel
                badgeText = const Color(0xFFFFA000);
              }

              // Ikon topik dinamis yang berubah sesuai nama mata pelajaran
              IconData topicIcon = Icons.school_rounded;
              if (topicTitle.toLowerCase().contains('psych') || topicTitle.toLowerCase().contains('psiko')) {
                topicIcon = Icons.psychology_rounded;
              } else if (topicTitle.toLowerCase().contains('calculus') || topicTitle.toLowerCase().contains('math')) {
                topicIcon = Icons.calculate_rounded;
              }

              return Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                child: Row(
                  children: [
                    Container(
                      width: 52,
                      height: 52,
                      decoration: BoxDecoration(
                        color: badgeColor,
                        borderRadius: BorderRadius.circular(14),
                      ),
                      child: Icon(
                        topicIcon,
                        color: badgeText,
                        size: 24,
                      ),
                    ),
                    const SizedBox(width: 14),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Expanded(
                                child: Text(
                                  topicTitle,
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                  style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 13.5, color: textDark),
                                ),
                              ),
                              const SizedBox(width: 8),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                decoration: BoxDecoration(
                                  color: badgeColor,
                                  borderRadius: BorderRadius.circular(20),
                                ),
                                child: Text(
                                  _getStatusText(status),
                                  style: TextStyle(
                                    color: badgeText,
                                    fontSize: 10,
                                    fontWeight: FontWeight.w900,
                                    letterSpacing: 0.2,
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 6),
                          Row(
                            children: [
                              const Icon(Icons.calendar_today_rounded, size: 12, color: neutralGray),
                              const SizedBox(width: 4),
                              Text(
                                _formatDateFromApi(date),
                                style: const TextStyle(color: neutralGray, fontSize: 11, fontWeight: FontWeight.bold),
                              ),
                              if (time.isNotEmpty && time != '00:00:00') ...[
                                const SizedBox(width: 8),
                                const Icon(Icons.access_time_rounded, size: 12, color: neutralGray),
                                const SizedBox(width: 4),
                                Text(
                                  _formatTimeFromApi(time),
                                  style: const TextStyle(color: neutralGray, fontSize: 11, fontWeight: FontWeight.bold),
                                ),
                              ]
                            ],
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              );
            },
          ),
        ],
      ),
    );
  }
}
