import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:io';
import 'dart:convert';
import 'package:http/http.dart' as http;
import '../services/auth_service.dart';
import 'tryout_detail_page.dart'; 
import 'module_week_list_page.dart'; 
import 'practice_week_list_page.dart'; 

class ClassDetailPage extends StatefulWidget {
  final int classId;
  final String className;
  final String token;
  final Map userData;

  const ClassDetailPage({
    super.key,
    required this.classId,
    required this.className,
    required this.token,
    required this.userData,
  });

  @override
  State<ClassDetailPage> createState() => _ClassDetailPageState();
}

class _ClassDetailPageState extends State<ClassDetailPage> {
  String status = "none";
  List materi = [];
  List tryouts = []; 
  List practiceQuestions = []; 
  bool isLoading = true;
  bool isShowingMateri = false; 
  bool isShowingLatihan = false; 
  final Color spektaRed = const Color(0xFF990000);

  @override
  void initState() {
    super.initState();
    _fetchDetail();
  }

  // 1. Fungsi Ambil Data dari API Laravel
  Future<void> _fetchDetail() async {
    try {
      var resp = await AuthService.getClassContent(widget.classId, widget.token);
      if (resp.statusCode == 200) {
        var data = jsonDecode(resp.body);
        if (mounted) {
          setState(() {
            status = data['enroll_status'] ?? "none";
            materi = data['materi'] ?? [];
            tryouts = data['tryouts'] ?? []; 
            // MODIFIKASI: Menggunakan key 'practice_questions' (Bahasa Inggris)
            practiceQuestions = data['practice_questions'] ?? []; 
            isLoading = false;
          });
        }
      }
    } catch (e) {
      if (mounted) setState(() => isLoading = false);
    }
  }

  // 2. Fungsi Proses Konfirmasi Pembayaran
  void _processUpload(File image) async {
    showDialog(
      context: context, 
      barrierDismissible: false, 
      builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000)))
    );

    try {
      var streamedResp = await AuthService.joinClass(widget.classId, image.path, widget.token);
      var response = await http.Response.fromStream(streamedResp);

      if (!mounted) return;
      Navigator.pop(context); // Tutup Loading

      if (response.statusCode == 200 || response.statusCode == 201) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            backgroundColor: Colors.green, 
            content: Text("✅ Payment Processing! Please wait for admin verification.")
          )
        );
        _fetchDetail(); // Refresh halaman agar status berubah jadi 'pending'
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(backgroundColor: Colors.red, content: Text("Failed to send enrollment data."))
        );
      }
    } catch (e) { 
      Navigator.pop(context); 
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Connection Error!"))
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    // MODIFIKASI: Menggunakan 'active' (Bahasa Inggris) sesuai perbaikan Database
    bool isRegistered = status == 'active';

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: Text(
          isShowingMateri ? "Video Materials" : (isShowingLatihan ? "Practice Materials" : widget.className), 
          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)
        ),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        leading: (isShowingMateri || isShowingLatihan) 
          ? IconButton(
              icon: const Icon(Icons.arrow_back), 
              onPressed: () => setState(() {
                isShowingMateri = false;
                isShowingLatihan = false;
              })
            ) 
          : null,
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: spektaRed))
          : SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildStatusBanner(),
                  if (isShowingMateri) ...[
                    _buildMateriList(materi, isRegistered)
                  ] else if (isShowingLatihan) ...[
                    _buildLatihanSubjectList(materi, isRegistered)
                  ] else ...[
                    if (tryouts.isNotEmpty) ...[
                      const Padding(padding: EdgeInsets.only(left: 20, top: 20), child: Text("Try-Out Simulation", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16))),
                      _buildTryoutList(tryouts, isRegistered),
                    ],
                    const SizedBox(height: 20),
                    const Padding(padding: EdgeInsets.only(left: 20), child: Text("Learning Center", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16))),
                    
                    _buildCategoryMenu(
                      title: "Learning Video Materials",
                      subtitle: "Collection of expert explanation videos",
                      icon: isRegistered ? Icons.play_circle_fill : Icons.lock_outline,
                      color: isRegistered ? Colors.blue.shade700 : Colors.grey,
                      onTap: isRegistered ? () => setState(() => isShowingMateri = true) : _showLockedMessage,
                    ),

                    _buildCategoryMenu(
                      title: "Self-Practice Questions",
                      subtitle: "Sharpen your skills here",
                      icon: isRegistered ? Icons.edit_note_rounded : Icons.lock_outline,
                      color: isRegistered ? Colors.orange.shade700 : Colors.grey,
                      onTap: isRegistered ? () => setState(() => isShowingLatihan = true) : _showLockedMessage,
                    ),
                  ],
                  const SizedBox(height: 120),
                ],
              ),
            ),
      // Tombol daftar hanya muncul jika status 'none'
      bottomNavigationBar: status == 'none' ? _buildBottomAction() : null,
    );
  }

  // 3. Form Pendaftaran (Form Pembayaran)
  void _showDaftarForm() {
    File? imageFile;
    final nameController = TextEditingController(text: widget.userData['name']);
    
    // PERBAIKAN: Menggunakan key 'national_id_number' agar 909 muncul (bukan strip)
    var student = widget.userData['student'];
    final String nisnValue = student != null ? (student['national_id_number']?.toString() ?? "-") : "-";
    final nisnController = TextEditingController(text: nisnValue);

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(25))),
      builder: (context) => StatefulBuilder(
        builder: (context, setModalState) => Padding(
          padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom, left: 25, right: 25, top: 25),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Text("Enrollment Form", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
              _buildField(nameController, "Name", Icons.person, true),
              _buildField(nisnController, "NISN", Icons.numbers, true),
              const SizedBox(height: 20),
              InkWell(
                onTap: () async {
                  final picked = await ImagePicker().pickImage(source: ImageSource.gallery);
                  if (picked != null) setModalState(() => imageFile = File(picked.path));
                },
                child: Container(
                  height: 120, width: double.infinity,
                  decoration: BoxDecoration(border: Border.all(color: Colors.grey), borderRadius: BorderRadius.circular(15)),
                  child: imageFile == null ? const Icon(Icons.add_a_photo) : Image.file(imageFile!, fit: BoxFit.cover),
                ),
              ),
              const SizedBox(height: 20),
              ElevatedButton(
                onPressed: imageFile == null ? null : () { Navigator.pop(context); _processUpload(imageFile!); },
                style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 50)),
                child: const Text("CONFIRM PAYMENT", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold))
              ),
              const SizedBox(height: 20),
            ],
          ),
        ),
      ),
    );
  }

  // --- WIDGET HELPER ---

  Widget _buildStatusBanner() {
    if (status == 'pending') {
      return Container(
        width: double.infinity, 
        padding: const EdgeInsets.all(15), 
        color: Colors.orange[50], 
        child: const Text("⌛ Payment verification in progress by admin", textAlign: TextAlign.center, style: TextStyle(color: Colors.orange, fontWeight: FontWeight.bold))
      );
    }
    return const SizedBox();
  }

  void _showLockedMessage() {
    String msg = status == 'pending' 
        ? "⌛ Your enrollment is being verified." 
        : "⚠️ Please enroll in this class first.";
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(backgroundColor: Colors.orange, content: Text(msg)));
  }

  Widget _buildCategoryMenu({required String title, required String subtitle, required IconData icon, required Color color, required VoidCallback onTap}) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 15, vertical: 10),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(20),
        child: Container(
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: Colors.white, borderRadius: BorderRadius.circular(20),
            boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10)],
            border: Border.all(color: Colors.grey.shade200)
          ),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(color: color.withOpacity(0.1), borderRadius: BorderRadius.circular(15)),
                child: Icon(icon, color: color, size: 30),
              ),
              const SizedBox(width: 20),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                    Text(subtitle, style: const TextStyle(color: Colors.grey, fontSize: 12)),
                  ],
                ),
              ),
              const Icon(Icons.arrow_forward_ios, size: 14, color: Colors.grey),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildTryoutList(List items, bool isRegistered) {
    return ListView.builder(
      padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 10),
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: items.length,
      itemBuilder: (context, index) {
        return Card(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
          child: ListTile(
            leading: Icon(isRegistered ? Icons.assignment : Icons.lock_outline, color: isRegistered ? Colors.orange : Colors.grey),
            title: Text(items[index]['title'], style: TextStyle(fontWeight: FontWeight.bold, color: isRegistered ? Colors.black87 : Colors.grey)),
            onTap: isRegistered 
              ? () => Navigator.push(context, MaterialPageRoute(builder: (_) => TryoutDetailPage(tryoutData: items[index], token: widget.token))) 
              : _showLockedMessage,
          ),
        );
      },
    );
  }

  Widget _buildMateriList(List items, bool isRegistered) {
    final List uniqueSubjects = items.map((m) => m['title']).toSet().toList();
    return ListView.builder(
      padding: const EdgeInsets.all(15),
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: uniqueSubjects.length,
      itemBuilder: (context, index) {
        String subjectName = uniqueSubjects[index];
        return Card(
          margin: const EdgeInsets.only(bottom: 12),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
          child: ListTile(
            leading: const Icon(Icons.folder_special_rounded, color: Colors.green),
            title: Text(subjectName, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.black87)),
            subtitle: const Text("View weekly modules", style: TextStyle(fontSize: 11)),
            trailing: const Icon(Icons.arrow_forward_ios, size: 14),
            onTap: isRegistered 
              ? () => Navigator.push(context, MaterialPageRoute(builder: (context) => ModuleWeekListPage(subjectName: subjectName, allMaterials: materi, token: widget.token)))
              : _showLockedMessage,
          ),
        );
      },
    );
  }

  Widget _buildLatihanSubjectList(List items, bool isRegistered) {
    final List uniqueSubjects = items.map((m) => m['title']).toSet().toList();
    if (uniqueSubjects.isEmpty) return const Center(child: Padding(padding: EdgeInsets.all(50), child: Text("No subjects available")));

    return ListView.builder(
      padding: const EdgeInsets.all(15),
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: uniqueSubjects.length,
      itemBuilder: (context, index) {
        String fullName = uniqueSubjects[index].toString();
        String subjectName = fullName.replaceAll("Materi ", "").replaceAll("Material ", "");

        return Container(
          margin: const EdgeInsets.only(bottom: 12),
          decoration: BoxDecoration(color: const Color(0xFFFDF7F2), borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.grey.shade100)),
          child: ListTile(
            contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
            leading: Container(padding: const EdgeInsets.all(10), decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)), child: const Icon(Icons.folder_special_rounded, color: Color(0xFF4CAF50), size: 28)),
            title: Text("$subjectName Practice", style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 15)),
            subtitle: const Text("View weekly practice questions", style: TextStyle(fontSize: 11)),
            trailing: const Icon(Icons.arrow_forward_ios, size: 14),
            onTap: () {
              Navigator.push(context, MaterialPageRoute(builder: (c) => PracticeWeekListPage(
                subjectName: fullName,
                allExercises: practiceQuestions,
                token: widget.token,
              )));
            },
          ),
        );
      },
    );
  }

  Widget _buildBottomAction() {
    return Container(
      height: 110, padding: const EdgeInsets.all(20), 
      decoration: BoxDecoration(color: Colors.white, boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10, offset: const Offset(0, -3))]),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween, 
        children: [
          Column(mainAxisAlignment: MainAxisAlignment.center, crossAxisAlignment: CrossAxisAlignment.start, children: [
            const Text("Program Price", style: TextStyle(color: Colors.grey, fontSize: 12)), 
            Text("Rp 900.000", style: TextStyle(color: spektaRed, fontSize: 20, fontWeight: FontWeight.bold))
          ]),
          ElevatedButton(
            onPressed: _showDaftarForm, 
            style: ElevatedButton.styleFrom(backgroundColor: spektaRed, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30))), 
            child: const Text("ENROLL NOW", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold))
          )
        ]
      )
    );
  }

  Widget _buildField(TextEditingController ctrl, String label, IconData icon, bool isReadOnly) {
    return Padding(padding: const EdgeInsets.only(top: 15), child: TextField(controller: ctrl, readOnly: isReadOnly, decoration: InputDecoration(labelText: label, prefixIcon: Icon(icon, color: spektaRed), filled: true, fillColor: isReadOnly ? Colors.grey[100] : Colors.white, border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)))));
  }
}