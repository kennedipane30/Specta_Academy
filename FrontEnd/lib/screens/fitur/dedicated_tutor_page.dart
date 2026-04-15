import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../services/tutor_service.dart';

class DedicatedTutorPage extends StatefulWidget {
  final String token;
  final Map userData;

  const DedicatedTutorPage({
    super.key, 
    required this.token, 
    required this.userData
  });

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

  // Mengambil data materi dan riwayat dari API
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
          }
          if (resHistory.statusCode == 200) {
            historyList = jsonDecode(resHistory.body)['data'] ?? [];
          }
          isLoading = false;
        });
      }
    } catch (e) {
      debugPrint("Error Load: $e");
      if (mounted) setState(() => isLoading = false);
    }
  }

  // Mengirim pengajuan ke server
  Future<void> _handlePost() async {
    if (selectedMaterialId == null || selectedDate == null) {
      _showSnack("Please select topic and date!", isError: true);
      return;
    }

    setState(() => isLoading = true);
    try {
      final body = {
        'material_id': selectedMaterialId,
        'date': DateFormat('yyyy-MM-dd').format(selectedDate!),
      };

      final res = await TutorService.submitTutor(body, widget.token);
      if (res.statusCode == 201 || res.statusCode == 200) {
        _showSnack("Request submitted! Waiting for teacher assignment.");
        setState(() {
          selectedMaterialId = null;
          selectedDate = null;
        });
        _fetchPageData(); // Refresh riwayat
      } else {
        final msg = jsonDecode(res.body)['message'] ?? "Request failed";
        _showSnack(msg, isError: true);
        setState(() => isLoading = false);
      }
    } catch (e) {
      _showSnack("Connection error", isError: true);
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
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text("Dedicated Tutor", style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: spektaRed, foregroundColor: Colors.white,
      ),
      body: isLoading 
        ? Center(child: CircularProgressIndicator(color: spektaRed))
        : RefreshIndicator(
            onRefresh: _fetchPageData,
            child: SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildFormCard(),
                  const SizedBox(height: 30),
                  const Text("Submission History", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 15),
                  _buildHistoryList(),
                  const SizedBox(height: 50),
                ],
              ),
            ),
          ),
    );
  }

  Widget _buildFormCard() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white, borderRadius: BorderRadius.circular(15),
        boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10)]
      ),
      child: Column(
        children: [
          TextField(
            controller: TextEditingController(text: widget.userData['name']),
            readOnly: true,
            decoration: const InputDecoration(labelText: "Student Name", prefixIcon: Icon(Icons.person), border: OutlineInputBorder()),
          ),
          const SizedBox(height: 15),
          
          // --- DROPDOWN MATERI (SINKRON DENGAN material_id) ---
          DropdownButtonFormField<int>(
            isExpanded: true,
            hint: const Text("Select Topic"),
            value: selectedMaterialId,
            items: materials.map<DropdownMenuItem<int>>((item) {
              return DropdownMenuItem<int>(
                // MODIFIKASI: Menggunakan 'material_id' (English) bukan materialsID
                value: item['material_id'], 
                child: Text(item['title'] ?? "Topic"),
              );
            }).toList(),
            onChanged: (val) => setState(() => selectedMaterialId = val),
            decoration: const InputDecoration(border: OutlineInputBorder(), prefixIcon: Icon(Icons.book)),
          ),
          const SizedBox(height: 15),

          ListTile(
            shape: RoundedRectangleBorder(side: const BorderSide(color: Colors.grey), borderRadius: BorderRadius.circular(5)),
            leading: const Icon(Icons.calendar_month, color: Color(0xFF990000)),
            title: Text(selectedDate == null ? "Select Learning Date" : DateFormat('dd MMMM yyyy').format(selectedDate!)),
            onTap: () async {
              final d = await showDatePicker(
                context: context,
                initialDate: DateTime.now().add(const Duration(days: 1)),
                firstDate: DateTime.now(),
                lastDate: DateTime.now().add(const Duration(days: 30)),
              );
              if (d != null) setState(() => selectedDate = d);
            },
          ),
          const SizedBox(height: 25),

          SizedBox(
            width: double.infinity, height: 55,
            child: ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed, 
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))
              ),
              onPressed: _handlePost,
              child: const Text("SEND REQUEST", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          )
        ],
      ),
    );
  }

  Widget _buildHistoryList() {
    if (historyList.isEmpty) return const Center(child: Padding(padding: EdgeInsets.all(20), child: Text("No submission history yet.")));

    return ListView.builder(
      shrinkWrap: true, physics: const NeverScrollableScrollPhysics(),
      itemCount: historyList.length,
      itemBuilder: (context, i) {
        var h = historyList[i];
        bool confirmed = h['status'] == 'confirmed';
        return Card(
          margin: const EdgeInsets.only(bottom: 12),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
          child: ListTile(
            title: Text(h['material']?['title'] ?? "Topic", style: const TextStyle(fontWeight: FontWeight.bold)),
            subtitle: Text("Date: ${h['date']}\nTeacher: ${h['teacher'] != null ? h['teacher']['name'] : 'Waiting for Admin'}"),
            trailing: Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
              decoration: BoxDecoration(
                color: confirmed ? Colors.green.withOpacity(0.1) : Colors.orange.withOpacity(0.1),
                borderRadius: BorderRadius.circular(8)
              ),
              child: Text(
                h['status'].toString().toUpperCase(),
                style: TextStyle(color: confirmed ? Colors.green : Colors.orange, fontSize: 10, fontWeight: FontWeight.bold),
              ),
            ),
          ),
        );
      },
    );
  }
}