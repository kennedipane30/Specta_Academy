import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:io';
import 'dart:convert';
import 'package:http/http.dart' as http;
import '../services/auth_service.dart';
import 'tryout_detail_page.dart'; 

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
  bool isLoading = true;
  final Color spektaRed = const Color(0xFF990000);

  @override
  void initState() {
    super.initState();
    _fetchDetail();
  }

  // 1. Fungsi Ambil Konten (Beda ID Beda Isi)
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
          });
        }
      }
    } catch (e) {
      debugPrint("Error fetch detail: $e");
    } finally {
      // Pastikan loading berhenti apapun yang terjadi
      if (mounted) {
        setState(() {
          isLoading = false;
        });
      }
    }
  }

  // 2. Fungsi Kirim Data & Upload Bukti ke Laravel
  void _processUpload(File image) async {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) => Center(child: CircularProgressIndicator(color: spektaRed)),
    );

    try {
      var streamedResp = await AuthService.joinClass(widget.classId, image.path, widget.token);
      var response = await http.Response.fromStream(streamedResp);

      if (!mounted) return;
      Navigator.pop(context); // Tutup Loading

      if (response.statusCode == 200) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(backgroundColor: Colors.green, content: Text("Pendaftaran Berhasil! Menunggu Verifikasi Admin."))
        );
        _fetchDetail(); // Refresh gembok materi
      } else {
        final errorData = jsonDecode(response.body);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(backgroundColor: Colors.red, content: Text(errorData['message'] ?? "Gagal mendaftar"))
        );
      }
    } catch (e) {
      if (!mounted) return;
      Navigator.pop(context);
      debugPrint("Error: $e");
    }
  }

  // 3. Form Pendaftaran (Nama & NISN Otomatis)
  void _showDaftarForm() {
    File? _imageFile;
    final nameController = TextEditingController(text: widget.userData['name']);
    final nisnController = TextEditingController(text: widget.userData['student']?['nisn'] ?? "-");

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(25))),
      builder: (context) => StatefulBuilder(
        builder: (context, setModalState) => Padding(
          padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom, left: 25, right: 25, top: 25),
          child: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Text("Konfirmasi Pendaftaran", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                const Divider(),
                
                _buildField(nameController, "Nama Pendaftar", Icons.person, true),
                _buildField(nisnController, "NISN Siswa", Icons.numbers, true),
                
                const SizedBox(height: 20),

                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(15),
                  decoration: BoxDecoration(color: Colors.red[50], borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.red.shade200)),
                  child: Column(
                    children: [
                      const Text("Silakan transfer ke Rekening Pusat:", style: TextStyle(fontSize: 12)),
                      Text("BANK MANDIRI: 123-456-7890", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: spektaRed)),
                      const Text("a/n Spekta Academy Indonesia", style: TextStyle(fontSize: 12)),
                    ],
                  ),
                ),
                
                const SizedBox(height: 20),

                InkWell(
                  onTap: () async {
                    final picker = ImagePicker();
                    final picked = await picker.pickImage(source: ImageSource.gallery);
                    if (picked != null) setModalState(() => _imageFile = File(picked.path));
                  },
                  child: Container(
                    height: 150, width: double.infinity,
                    decoration: BoxDecoration(color: Colors.grey[100], borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade300)),
                    child: _imageFile == null 
                      ? Column(mainAxisAlignment: MainAxisAlignment.center, children: [Icon(Icons.add_photo_alternate, size: 40, color: spektaRed), const Text("Klik Upload Bukti Transfer", style: TextStyle(fontSize: 12))])
                      : ClipRRect(borderRadius: BorderRadius.circular(15), child: Image.file(_imageFile!, fit: BoxFit.cover)),
                  ),
                ),

                const SizedBox(height: 25),
                ElevatedButton(
                  style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 55), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
                  onPressed: _imageFile == null ? null : () {
                    Navigator.pop(context); 
                    _processUpload(_imageFile!); 
                  }, 
                  child: const Text("KONFIRMASI PEMBAYARAN", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                ),
                const SizedBox(height: 30),
              ],
            ),
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    bool isRegistered = status == 'aktif';

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: Text(widget.className, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: spektaRed))
          : SingleChildScrollView( 
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildStatusBanner(),
                  
                  // --- BAGIAN TRYOUT (HANYA TAMPIL JIKA ADA DATA) ---
                  if (tryouts.isNotEmpty) ...[
                    const Padding(
                      padding: EdgeInsets.only(left: 20, top: 20),
                      child: Text("Simulasi Try-Out", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                    ),
                    _buildListContent(tryouts, isRegistered, Icons.assignment, Colors.orange, true),
                  ],

                  // --- BAGIAN MATERI VIDEO ---
                  const Padding(
                    padding: EdgeInsets.only(left: 20, top: 20),
                    child: Text("Materi Video Pembelajaran", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                  ),
                  _buildListContent(materi, isRegistered, Icons.play_circle_fill, Colors.green, false),
                  
                  const SizedBox(height: 120), 
                ],
              ),
            ),
      bottomNavigationBar: !isRegistered && status == 'none'
          ? _buildBottomAction()
          : null,
    );
  }

  Widget _buildListContent(List items, bool isRegistered, IconData activeIcon, Color activeColor, bool isTryout) {
    if (items.isEmpty) return const Padding(padding: EdgeInsets.all(20), child: Text("Belum ada konten tersedia."));
    
    return ListView.builder(
      padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 10),
      shrinkWrap: true, 
      physics: const NeverScrollableScrollPhysics(),
      itemCount: items.length,
      itemBuilder: (context, index) {
        return Card(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
          child: ListTile(
            leading: Icon(
              isRegistered ? activeIcon : Icons.lock_outline,
              color: isRegistered ? activeColor : Colors.grey,
            ),
            title: Text(items[index]['title'], style: const TextStyle(fontWeight: FontWeight.bold)),
            subtitle: Text(isRegistered ? "Klik untuk akses" : "Akses Terkunci"),
            onTap: isRegistered ? () {
              if (isTryout) {
                Navigator.push(context, MaterialPageRoute(
                  builder: (context) => TryoutDetailPage(tryoutData: items[index], token: widget.token)
                ));
              } else {
                // Aksi buka Video Materi
              }
            } : () {
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text("Silakan daftar untuk melihat materi!"))
              );
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
        child: const Text("⌛ Menunggu verifikasi admin", textAlign: TextAlign.center, style: TextStyle(color: Colors.orange, fontWeight: FontWeight.bold)),
      );
    }
    return const SizedBox();
  }

  Widget _buildBottomAction() {
    return Container(
      height: 110,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.1), blurRadius: 10, offset: const Offset(0, -3))],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Text("Harga Program", style: TextStyle(color: Colors.grey, fontSize: 12)),
              Text("Rp 900.000", style: TextStyle(color: spektaRed, fontSize: 20, fontWeight: FontWeight.bold)),
            ],
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: spektaRed, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30))),
            onPressed: _showDaftarForm, 
            child: const Text("DAFTAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          )
        ],
      ),
    );
  }

  Widget _buildField(TextEditingController ctrl, String label, IconData icon, bool isReadOnly) {
    return Padding(
      padding: const EdgeInsets.only(top: 15),
      child: TextField(
        controller: ctrl,
        readOnly: isReadOnly,
        decoration: InputDecoration(
          labelText: label,
          prefixIcon: Icon(icon, color: spektaRed),
          filled: true,
          fillColor: isReadOnly ? Colors.grey[100] : Colors.white,
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
        ),
      ),
    );
  }
}