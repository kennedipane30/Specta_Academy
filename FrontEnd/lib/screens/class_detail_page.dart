import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:io';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:url_launcher/url_launcher.dart'; 
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
  List latihanSoals = []; 
  bool isLoading = true;
  bool isShowingMateri = false; 
  bool isShowingLatihan = false; 
  final Color spektaRed = const Color(0xFF990000);

  @override
  void initState() {
    super.initState();
    _fetchDetail();
  }

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
            latihanSoals = data['latihan_soals'] ?? []; 
            isLoading = false;
          });
        }
      }
    } catch (e) {
      if (mounted) setState(() => isLoading = false);
    }
  }

  void _showLockedMessage() {
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        backgroundColor: Colors.orange,
        content: Text("⚠️ Fitur Terkunci! Silakan daftar kelas ini terlebih dahulu."),
      ),
    );
  }

  void _processUpload(File image) async {
    showDialog(context: context, barrierDismissible: false, builder: (_) => Center(child: CircularProgressIndicator(color: spektaRed)));
    try {
      var streamedResp = await AuthService.joinClass(widget.classId, image.path, widget.token);
      var response = await http.Response.fromStream(streamedResp);
      if (!mounted) return;
      Navigator.pop(context);
      if (response.statusCode == 200) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.green, content: Text("Pendaftaran Berhasil! Menunggu Verifikasi Admin.")));
        _fetchDetail(); 
      }
    } catch (e) { Navigator.pop(context); }
  }

  @override
  Widget build(BuildContext context) {
    bool isRegistered = status == 'aktif';

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: Text(
          isShowingMateri ? "Materi Video" : (isShowingLatihan ? "Materi Latihan" : widget.className), 
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
                    _buildLatihanSubjectList(materi, isRegistered) // MENGGUNAKAN LIST MATERI SEBAGAI DASAR FOLDER
                  ] else ...[
                    if (tryouts.isNotEmpty) ...[
                      const Padding(padding: EdgeInsets.only(left: 20, top: 20), child: Text("Simulasi Try-Out", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16))),
                      _buildTryoutList(tryouts, isRegistered),
                    ],
                    const SizedBox(height: 20),
                    const Padding(padding: EdgeInsets.only(left: 20), child: Text("Pusat Pembelajaran", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16))),
                    
                    _buildCategoryMenu(
                      title: "Materi Video Pembelajaran",
                      subtitle: "Kumpulan video penjelasan expert",
                      icon: isRegistered ? Icons.play_circle_fill : Icons.lock_outline,
                      color: isRegistered ? Colors.blue.shade700 : Colors.grey,
                      onTap: isRegistered ? () => setState(() => isShowingMateri = true) : _showLockedMessage,
                    ),

                    _buildCategoryMenu(
                      title: "Latihan Soal Mandiri",
                      subtitle: "Asah kemampuanmu di sini",
                      icon: isRegistered ? Icons.edit_note_rounded : Icons.lock_outline,
                      color: isRegistered ? Colors.orange.shade700 : Colors.grey,
                      onTap: isRegistered ? () => setState(() => isShowingLatihan = true) : _showLockedMessage,
                    ),
                  ],
                  const SizedBox(height: 120),
                ],
              ),
            ),
      bottomNavigationBar: !isRegistered && status == 'none' ? _buildBottomAction() : null,
    );
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
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
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
            subtitle: const Text("Lihat modul per minggu", style: TextStyle(fontSize: 11)),
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
    // KITA AMBIL DARI LIST MATERI AGAR FOLDER MUNCUL SESUAI DB TABEL MATERIALS ANDA
    final List uniqueSubjects = items.map((m) => m['title']).toSet().toList();
    
    if (uniqueSubjects.isEmpty) {
      return const Center(child: Padding(padding: EdgeInsets.all(50), child: Text("Belum ada mata pelajaran tersedia")));
    }

    return ListView.builder(
      padding: const EdgeInsets.all(15),
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: uniqueSubjects.length,
      itemBuilder: (context, index) {
        String fullName = uniqueSubjects[index].toString();
        // Bersihkan nama: "Materi TIU" menjadi "TIU"
        String subjectName = fullName.replaceAll("Materi ", "");

        return Container(
          margin: const EdgeInsets.only(bottom: 12),
          decoration: BoxDecoration(
            color: const Color(0xFFFDF7F2),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: Colors.grey.shade100),
          ),
          child: ListTile(
            contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
            leading: Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
              child: const Icon(Icons.folder_special_rounded, color: Color(0xFF4CAF50), size: 28),
            ),
            title: Text("Latihan $subjectName", style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 15)),
            subtitle: const Text("Lihat latihan per minggu", style: TextStyle(fontSize: 11)),
            trailing: const Icon(Icons.arrow_forward_ios, size: 14),
            onTap: () {
              Navigator.push(context, MaterialPageRoute(builder: (c) => PracticeWeekListPage(
                subjectName: fullName, // Kirim nama lengkap agar filter di page selanjutnya akurat
                allExercises: latihanSoals,
                token: widget.token,
              )));
            },
          ),
        );
      },
    );
  }

  Widget _buildStatusBanner() {
    if (status == 'pending') {
      return Container(
        width: double.infinity, 
        padding: const EdgeInsets.all(15), 
        color: Colors.orange[50], 
        child: const Text("⌛ Menunggu verifikasi admin", textAlign: TextAlign.center, style: TextStyle(color: Colors.orange, fontWeight: FontWeight.bold))
      );
    }
    return const SizedBox();
  }

  Widget _buildBottomAction() {
    return Container(
      height: 110, padding: const EdgeInsets.all(20), 
      decoration: BoxDecoration(color: Colors.white, boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10, offset: const Offset(0, -3))]),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween, 
        children: [
          Column(mainAxisAlignment: MainAxisAlignment.center, crossAxisAlignment: CrossAxisAlignment.start, children: [
            const Text("Harga Program", style: TextStyle(color: Colors.grey, fontSize: 12)), 
            Text("Rp 900.000", style: TextStyle(color: spektaRed, fontSize: 20, fontWeight: FontWeight.bold))
          ]),
          ElevatedButton(
            onPressed: _showDaftarForm, 
            style: ElevatedButton.styleFrom(backgroundColor: spektaRed, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30))), 
            child: const Text("DAFTAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold))
          )
        ]
      )
    );
  }

  void _showDaftarForm() {
    File? imageFile;
    final nameController = TextEditingController(text: widget.userData['name']);
    final nisnController = TextEditingController(text: widget.userData['student']?['nisn'] ?? "-");
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
              const Text("Form Pendaftaran", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
              _buildField(nameController, "Nama", Icons.person, true),
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
                child: const Text("KONFIRMASI BAYAR", style: TextStyle(color: Colors.white))
              ),
              const SizedBox(height: 20),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildField(TextEditingController ctrl, String label, IconData icon, bool isReadOnly) {
    return Padding(padding: const EdgeInsets.only(top: 15), child: TextField(controller: ctrl, readOnly: isReadOnly, decoration: InputDecoration(labelText: label, prefixIcon: Icon(icon, color: spektaRed), filled: true, fillColor: isReadOnly ? Colors.grey[100] : Colors.white, border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)))));
  }
}