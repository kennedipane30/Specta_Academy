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
      final resMateri = await TutorService.getTutorData(widget.token);
      final resHistory = await TutorService.getTutorHistory(widget.token);

      if (mounted) {
        setState(() {
          if (resMateri.statusCode == 200) {
            materials = jsonDecode(resMateri.body)['materials'] ?? [];
          } else {
            debugPrint("Gagal Load Materi: ${resMateri.body}");
          }
          
          if (resHistory.statusCode == 200) {
            historyList = jsonDecode(resHistory.body)['data'] ?? [];
          }
          isLoading = false;
        });
      }
    } catch (e) {
      debugPrint("Error Jaringan: $e");
      if (mounted) setState(() => isLoading = false);
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
        'material_id': selectedMaterialId,
        'date': DateFormat('yyyy-MM-dd').format(selectedDate!),
      };

      final res = await TutorService.submitTutor(body, widget.token);
      if (res.statusCode == 201) {
        _showSnack("Pengajuan berhasil dikirim!");
        setState(() { selectedMaterialId = null; selectedDate = null; });
        _fetchPageData(); 
      } else {
        final msg = jsonDecode(res.body)['message'] ?? "Gagal mengirim pengajuan";
        _showSnack(msg, isError: true);
        setState(() => isLoading = false);
      }
    } catch (e) {
      _showSnack("Terjadi kesalahan koneksi", isError: true);
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
    int remaining = 3 - historyList.length;

    return Scaffold(
      appBar: AppBar(title: const Text("Dedicated Tutor"), backgroundColor: spektaRed),
      body: isLoading 
        ? Center(child: CircularProgressIndicator(color: spektaRed))
        : RefreshIndicator(
            onRefresh: _fetchPageData,
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildFormCard(remaining),
                  const SizedBox(height: 30),
                  const Text("Submission History", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
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
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10)]),
      child: Column(
        children: [
          TextField(
            controller: TextEditingController(text: widget.userData['name']),
            readOnly: true,
            decoration: const InputDecoration(labelText: "Student Name", border: OutlineInputBorder()),
          ),
          const SizedBox(height: 15),
          DropdownButtonFormField<int>(
            isExpanded: true,
            hint: const Text("Select Topic"),
            value: selectedMaterialId,
            items: materials.map<DropdownMenuItem<int>>((item) {
              return DropdownMenuItem<int>(
                value: item['material_id'], 
                child: Text(item['title'] ?? "Topic"),
              );
            }).toList(),
            onChanged: quota > 0 ? (val) => setState(() => selectedMaterialId = val) : null,
            decoration: const InputDecoration(border: OutlineInputBorder()),
          ),
          const SizedBox(height: 15),
          ListTile(
            enabled: quota > 0,
            shape: RoundedRectangleBorder(side: const BorderSide(color: Colors.grey), borderRadius: BorderRadius.circular(5)),
            title: Text(selectedDate == null ? "Select Date" : DateFormat('dd MMMM yyyy').format(selectedDate!)),
            trailing: const Icon(Icons.calendar_today),
            onTap: () async {
              final d = await showDatePicker(context: context, initialDate: DateTime.now().add(const Duration(days: 1)), firstDate: DateTime.now(), lastDate: DateTime.now().add(const Duration(days: 30)));
              if (d != null) setState(() => selectedDate = d);
            },
          ),
          const SizedBox(height: 15),
          Text("Sisa Kuota: $quota/3", style: TextStyle(color: quota > 0 ? Colors.green : Colors.red, fontWeight: FontWeight.bold)),
          const SizedBox(height: 20),
          ElevatedButton(
            onPressed: quota > 0 ? _handlePost : null,
            style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 55)),
            child: const Text("SEND REQUEST", style: TextStyle(color: Colors.white)),
          )
        ],
      ),
    );
  }

  Widget _buildHistoryList() {
    if (historyList.isEmpty) return const Center(child: Text("Belum ada riwayat."));
    return ListView.builder(
      shrinkWrap: true, physics: const NeverScrollableScrollPhysics(),
      itemCount: historyList.length,
      itemBuilder: (context, i) {
        var h = historyList[i];
        return Card(
          child: ListTile(
            title: Text(h['material']?['title'] ?? "Materi"),
            subtitle: Text("Tanggal: ${h['date']} - Status: ${h['status']}"),
          ),
        );
      },
    );
  }
}