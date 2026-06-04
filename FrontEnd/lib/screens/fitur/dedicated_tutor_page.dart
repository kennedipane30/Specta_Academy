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
  
  List materials = [];    
  List historyList = [];  
  int remainingQuota = 0; 
  bool isLoading = true;

  int? selectedMaterialId;
  DateTime? selectedDate;

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

  Future<void> _handlePost() async {
    if (selectedMaterialId == null || selectedDate == null) {
      _showSnack("Harap pilih topik dan tanggal!", isError: true);
      return;
    }

    setState(() => isLoading = true);
    try {
      final body = {
        // Memastikan material_id dikirim sebagai integer
        'material_id': selectedMaterialId, 
        'date': DateFormat('yyyy-MM-dd').format(selectedDate!),
        'time': '16:00:00',
      };

      final res = await TutorService.submitTutor(body, widget.token);
      final resData = jsonDecode(res.body);

      if (res.statusCode == 201) {
        _showSnack("Pengajuan berhasil dikirim!");
        setState(() { 
          selectedMaterialId = null; 
          selectedDate = null; 
        });
        _fetchPageData(); 
      } else {
        // Menampilkan pesan error dari Laravel (seperti "material id is invalid")
        String msg = resData['message'] ?? "Gagal mengirim pengajuan";
        if (resData['errors'] != null) {
           msg = resData['errors'].toString();
        }
        _showSnack(msg, isError: true);
        setState(() => isLoading = false);
      }
    } catch (e) {
      _showSnack("Terjadi kesalahan sistem", isError: true);
      setState(() => isLoading = false);
    }
  }

  void _showSnack(String m, {bool isError = false}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(m), backgroundColor: isError ? Colors.red : Colors.green)
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Dedicated Tutor", style: TextStyle(color: Colors.white)),
        backgroundColor: spektaRed,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: isLoading 
        ? Center(child: CircularProgressIndicator(color: spektaRed))
        : RefreshIndicator(
            onRefresh: _fetchPageData,
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildFormCard(remainingQuota),
                  const SizedBox(height: 30),
                  const Text("Submission History", 
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 15),
                  _buildHistoryList(),
                ],
              ),
            ),
          ),
    );
  }

  Widget _buildFormCard(int quota) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white, 
        borderRadius: BorderRadius.circular(20), 
        boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 10)]
      ),
      child: Column(
        children: [
          TextField(
            controller: TextEditingController(text: widget.userData['name']),
            readOnly: true,
            decoration: const InputDecoration(
              labelText: "Student Name", 
              border: OutlineInputBorder(),
              prefixIcon: Icon(Icons.person)
            ),
          ),
          const SizedBox(height: 15),
          DropdownButtonFormField<int>(
            isExpanded: true,
            hint: const Text("Select Topic"),
            value: selectedMaterialId,
            items: materials.map<DropdownMenuItem<int>>((item) {
              return DropdownMenuItem<int>(
                // Pastikan value adalah ID (int), bukan string judul
                value: int.tryParse(item['material_id'].toString()), 
                child: Text(item['title'] ?? "Topic"),
              );
            }).toList(),
            onChanged: quota > 0 ? (val) => setState(() => selectedMaterialId = val) : null,
            decoration: const InputDecoration(
              border: OutlineInputBorder(),
              prefixIcon: Icon(Icons.topic)
            ),
          ),
          const SizedBox(height: 15),
          ListTile(
            enabled: quota > 0,
            shape: RoundedRectangleBorder(
              side: const BorderSide(color: Colors.grey), 
              borderRadius: BorderRadius.circular(5)
            ),
            title: Text(selectedDate == null 
              ? "Select Date" 
              : DateFormat('dd MMMM yyyy').format(selectedDate!)),
            trailing: const Icon(Icons.calendar_today),
            onTap: () async {
              final d = await showDatePicker(
                context: context, 
                initialDate: DateTime.now().add(const Duration(days: 1)), 
                firstDate: DateTime.now(), 
                lastDate: DateTime.now().add(const Duration(days: 30))
              );
              if (d != null) setState(() => selectedDate = d);
            },
          ),
          const SizedBox(height: 15),
          Text("Sisa Kuota: $quota/3", 
            style: TextStyle(
              color: quota > 0 ? Colors.green : Colors.red, 
              fontWeight: FontWeight.bold,
              fontSize: 16
            )),
          const SizedBox(height: 20),
          ElevatedButton(
            onPressed: quota > 0 ? _handlePost : null,
            style: ElevatedButton.styleFrom(
              backgroundColor: spektaRed, 
              disabledBackgroundColor: Colors.grey,
              minimumSize: const Size(double.infinity, 55),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))
            ),
            child: const Text("SEND REQUEST", 
              style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)),
          )
        ],
      ),
    );
  }

  Widget _buildHistoryList() {
    if (historyList.isEmpty) {
      return const Center(child: Text("Belum ada riwayat pengajuan."));
    }
    return ListView.builder(
      shrinkWrap: true, 
      physics: const NeverScrollableScrollPhysics(),
      itemCount: historyList.length,
      itemBuilder: (context, i) {
        var h = historyList[i];
        String topicTitle = h['material']?['title'] ?? "Materi Umum";
        return Card(
          margin: const EdgeInsets.only(bottom: 10),
          child: ListTile(
            title: Text(topicTitle, style: const TextStyle(fontWeight: FontWeight.bold)),
            subtitle: Text("Tanggal: ${h['date']}"),
            trailing: Text(
              h['status'].toUpperCase(),
              style: TextStyle(
                color: _getStatusColor(h['status']), 
                fontWeight: FontWeight.bold
              ),
            ),
          ),
        );
      },
    );
  }

  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'pending': return Colors.orange;
      case 'confirmed': return Colors.green;
      case 'rejected': return Colors.red;
      default: return Colors.grey;
    }
  }
}