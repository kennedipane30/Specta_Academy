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
  final Color spektaRed = const Color(0xFF990000);
  final Color spektaDark = const Color(0xFF1A1A2E);
  final Color spektaGray = const Color(0xFF6C757D);
  final Color spektaLightGray = const Color(0xFFF8F9FA);
  
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
            colorScheme: const ColorScheme.light(primary: Color(0xFF990000)),
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
            colorScheme: const ColorScheme.light(primary: Color(0xFF990000)),
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
        content: Text(message),
        backgroundColor: isError ? Colors.red : Colors.green,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      ),
    );
  }

  // ✅ Perbaiki format tanggal tanpa localization
  String _formatDate(DateTime date) {
    final months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    final weekdays = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    return '${weekdays[date.weekday - 1]}, ${date.day} ${months[date.month - 1]} ${date.year}';
  }

  String _formatTime(TimeOfDay time) {
    return '${time.hour.toString().padLeft(2, '0')}:${time.minute.toString().padLeft(2, '0')} WIB';
  }

  // ✅ Perbaiki format tanggal dari API
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
      // Jika format sudah HH:MM:SS, ambil HH:MM saja
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
      case 'confirmed': return Colors.green;
      case 'rejected': return Colors.red;
      case 'pending': return Colors.orange;
      default: return Colors.grey;
    }
  }

  String _getStatusText(String status) {
    switch (status.toLowerCase()) {
      case 'confirmed': return 'Disetujui';
      case 'rejected': return 'Ditolak';
      case 'pending': return 'Menunggu';
      default: return status;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: spektaLightGray,
      appBar: AppBar(
        title: const Text(
          "Dedicated Tutor",
          style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
        ),
        backgroundColor: spektaRed,
        elevation: 0,
        centerTitle: true,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: isLoading
          ? const Center(
              child: CircularProgressIndicator(color: Color(0xFF990000)),
            )
          : RefreshIndicator(
              onRefresh: _fetchPageData,
              color: spektaRed,
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                physics: const AlwaysScrollableScrollPhysics(),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildQuotaCard(),
                    const SizedBox(height: 20),
                    _buildRequestForm(),
                    const SizedBox(height: 24),
                    _buildHistorySection(),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildQuotaCard() {
    final int usedQuota = maxQuota - remainingQuota;
    final double progress = (usedQuota / maxQuota).clamp(0.0, 1.0);

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [spektaRed, spektaRed.withOpacity(0.7)],
        ),
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: spektaRed.withOpacity(0.3),
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
              Icon(Icons.verified_user, color: Colors.white, size: 20),
              SizedBox(width: 8),
              Text(
                "Sisa Kuota Bulan Ini",
                style: TextStyle(color: Colors.white70, fontSize: 13),
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
                        fontSize: 36,
                        fontWeight: FontWeight.bold,
                        color: Colors.white,
                      ),
                    ),
                    TextSpan(
                      text: "/$maxQuota",
                      style: TextStyle(
                        fontSize: 18,
                        color: Colors.white.withOpacity(0.7),
                      ),
                    ),
                  ],
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Row(
                  children: [
                    Icon(Icons.check_circle, size: 14, color: Colors.white.withOpacity(0.9)),
                    const SizedBox(width: 4),
                    Text(
                      "Digunakan $usedQuota",
                      style: const TextStyle(color: Colors.white, fontSize: 12),
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
              backgroundColor: Colors.white.withOpacity(0.3),
              valueColor: const AlwaysStoppedAnimation<Color>(Colors.white),
              minHeight: 8,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildRequestForm() {
    final bool canSubmit = remainingQuota > 0;

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Row(
            children: [
              Icon(Icons.edit_note, color: Color(0xFF990000), size: 22),
              SizedBox(width: 8),
              Text(
                "Ajukan Permintaan",
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
              ),
            ],
          ),
          const SizedBox(height: 20),
          _buildInfoRow(
            icon: Icons.person_outline,
            label: "Nama Siswa",
            value: widget.userData['name'] ?? 'Loading...',
          ),
          const SizedBox(height: 16),
          _buildDropdownField(),
          const SizedBox(height: 16),
          _buildDateField(canSubmit),
          const SizedBox(height: 16),
          _buildTimeField(canSubmit),
          const SizedBox(height: 24),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: canSubmit ? _handlePost : null,
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed,
                disabledBackgroundColor: Colors.grey.shade300,
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                elevation: 0,
              ),
              child: Text(
                canSubmit ? "Ajukan Permintaan" : "Kuota Habis",
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoRow({required IconData icon, required String label, required String value}) {
    return Row(
      children: [
        Icon(icon, size: 18, color: spektaGray),
        const SizedBox(width: 12),
        SizedBox(
          width: 100,
          child: Text(
            label,
            style: TextStyle(color: spektaGray, fontSize: 13),
          ),
        ),
        Expanded(
          child: Text(
            ":  $value",
            style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500),
          ),
        ),
      ],
    );
  }

  Widget _buildDropdownField() {
    return Container(
      decoration: BoxDecoration(
        border: Border.all(color: Colors.grey.shade300),
        borderRadius: BorderRadius.circular(12),
      ),
      child: DropdownButtonFormField<int>(
        isExpanded: true,
        hint: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12),
          child: Text(
            "Pilih Topik",
            style: TextStyle(color: spektaGray),
          ),
        ),
        value: selectedMaterialId,
        items: materials.map<DropdownMenuItem<int>>((item) {
          return DropdownMenuItem<int>(
            value: int.tryParse(item['material_id'].toString()),
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 12),
              child: Text(item['title'] ?? "Topik"),
            ),
          );
        }).toList(),
        onChanged: remainingQuota > 0 ? (val) => setState(() => selectedMaterialId = val) : null,
        decoration: const InputDecoration(
          border: InputBorder.none,
          contentPadding: EdgeInsets.symmetric(vertical: 14),
        ),
        icon: Padding(
          padding: const EdgeInsets.only(right: 12),
          child: Icon(Icons.arrow_drop_down, color: spektaRed),
        ),
      ),
    );
  }

  Widget _buildDateField(bool enabled) {
    return InkWell(
      onTap: enabled ? _selectDate : null,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        decoration: BoxDecoration(
          border: Border.all(color: Colors.grey.shade300),
          borderRadius: BorderRadius.circular(12),
          color: Colors.white,
        ),
        child: Row(
          children: [
            Icon(Icons.calendar_today, size: 20, color: spektaGray),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                selectedDate != null ? _formatDate(selectedDate!) : "Pilih Tanggal",
                style: TextStyle(
                  color: selectedDate != null ? Colors.black : spektaGray,
                  fontSize: 14,
                ),
              ),
            ),
            if (selectedDate != null)
              IconButton(
                onPressed: () => setState(() => selectedDate = null),
                icon: Icon(Icons.close, size: 18, color: spektaGray),
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
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        decoration: BoxDecoration(
          border: Border.all(color: Colors.grey.shade300),
          borderRadius: BorderRadius.circular(12),
          color: Colors.white,
        ),
        child: Row(
          children: [
            Icon(Icons.access_time, size: 20, color: spektaGray),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                selectedTime != null ? _formatTime(selectedTime!) : "Pilih Waktu",
                style: TextStyle(
                  color: selectedTime != null ? Colors.black : spektaGray,
                  fontSize: 14,
                ),
              ),
            ),
            if (selectedTime != null)
              IconButton(
                onPressed: () => setState(() => selectedTime = null),
                icon: Icon(Icons.close, size: 18, color: spektaGray),
                padding: EdgeInsets.zero,
                constraints: const BoxConstraints(),
              ),
          ],
        ),
      ),
    );
  }

  Widget _buildHistorySection() {
    if (historyList.isEmpty) {
      return Container(
        padding: const EdgeInsets.all(40),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
        ),
        child: Column(
          children: [
            Icon(Icons.history, size: 48, color: Colors.grey.shade400),
            const SizedBox(height: 12),
            Text(
              "Belum ada riwayat pengajuan",
              style: TextStyle(color: Colors.grey.shade500),
            ),
            const SizedBox(height: 8),
            Text(
              "Ajukan permintaan tutor di atas",
              style: TextStyle(color: Colors.grey.shade400, fontSize: 12),
            ),
          ],
        ),
      );
    }

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Padding(
            padding: EdgeInsets.all(16),
            child: Row(
              children: [
                Icon(Icons.history, color: Color(0xFF990000), size: 20),
                SizedBox(width: 8),
                Text(
                  "Riwayat Pengajuan",
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                ),
              ],
            ),
          ),
          const Divider(height: 1),
          ListView.separated(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: historyList.length,
            separatorBuilder: (context, index) => const Divider(height: 1, indent: 16, endIndent: 16),
            itemBuilder: (context, index) {
              final item = historyList[index];
              final status = item['status'] ?? 'pending';
              final date = item['date'] ?? '';
              final time = item['time'] ?? '';
              
              // Cari judul materi dari materials berdasarkan material_id
              final materialId = item['material_id'];
              final material = materials.firstWhere(
                (m) => m['material_id'] == materialId,
                orElse: () => {'title': 'Materi Umum'},
              );
              final topicTitle = material['title'] ?? 'Materi Umum';

              return ListTile(
                leading: Container(
                  width: 48,
                  height: 48,
                  decoration: BoxDecoration(
                    color: _getStatusColor(status).withOpacity(0.1),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Icon(
                    status == 'confirmed' ? Icons.check_circle : 
                    status == 'rejected' ? Icons.cancel : 
                    Icons.schedule,
                    color: _getStatusColor(status),
                    size: 24,
                  ),
                ),
                title: Text(
                  topicTitle,
                  style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14),
                ),
                subtitle: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const SizedBox(height: 4),
                    Text(
                      _formatDateFromApi(date),
                      style: TextStyle(color: Colors.grey.shade600, fontSize: 12),
                    ),
                    if (time.isNotEmpty && time != '00:00:00')
                      Text(
                        _formatTimeFromApi(time),
                        style: TextStyle(color: Colors.grey.shade500, fontSize: 11),
                      ),
                  ],
                ),
                trailing: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: _getStatusColor(status).withOpacity(0.1),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    _getStatusText(status),
                    style: TextStyle(
                      color: _getStatusColor(status),
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              );
            },
          ),
        ],
      ),
    );
  }
}