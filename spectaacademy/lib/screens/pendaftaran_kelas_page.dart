import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:io';
import 'package:http/http.dart' as http;
import '../services/auth_service.dart';

class PendaftaranKelasPage extends StatefulWidget {
  final int classId;
  final String className;
  final String token;
  final Map userData;

  const PendaftaranKelasPage({
    super.key, 
    required this.classId, 
    required this.className, 
    required this.token, 
    required this.userData
  });

  @override
  State<PendaftaranKelasPage> createState() => _PendaftaranKelasPageState();
}

class _PendaftaranKelasPageState extends State<PendaftaranKelasPage> {
  File? _imageFile;
  final Color spektaRed = const Color(0xFF990000);

  // Ambil data otomatis dari userData (Mata Kuliah: Kualitas Perangkat Lunak)
  late TextEditingController _nameCtrl;
  late TextEditingController _nisnCtrl;

  @override
  void initState() {
    super.initState();
    _nameCtrl = TextEditingController(text: widget.userData['name']);
    _nisnCtrl = TextEditingController(text: widget.userData['student']['nisn'] ?? "-");
  }

  Future<void> _pickImage() async {
    final picker = ImagePicker();
    final pickedFile = await picker.pickImage(source: ImageSource.gallery);
    if (pickedFile != null) setState(() => _imageFile = File(pickedFile.path));
  }

  void _submitData() async {
    if (_imageFile == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Unggah bukti transfer terlebih dahulu!")));
      return;
    }

    showDialog(context: context, barrierDismissible: false, builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000))));

    var streamedResp = await AuthService.joinClass(widget.classId, _imageFile!.path, widget.token);
    var response = await http.Response.fromStream(streamedResp);

    if (!mounted) return;
    Navigator.pop(context); // Tutup loading

    if (response.statusCode == 200) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.green, content: Text("Pendaftaran Berhasil! Mohon tunggu verifikasi admin.")));
      Navigator.pop(context); // Kembali ke Detail Kelas
    } else {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.red, content: Text("Gagal mengirim data.")));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Konfirmasi Pendaftaran"), backgroundColor: spektaRed, foregroundColor: Colors.white),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(25),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // 1. DATA OTOMATIS (READ ONLY)
            const Text("Data Pendaftar", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)),
            const SizedBox(height: 10),
            _buildReadOnlyField(_nameCtrl, "Nama Lengkap", Icons.person),
            _buildReadOnlyField(_nisnCtrl, "NISN Siswa", Icons.numbers),
            
            const SizedBox(height: 30),

            // 2. NOMOR REKENING PUSAT
            const Text("Informasi Pembayaran", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)),
            const SizedBox(height: 10),
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(color: Colors.red[50], borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.red.shade200)),
              child: Column(
                children: [
                  const Text("Silakan transfer ke Rekening Pusat Spekta:", style: TextStyle(fontSize: 12)),
                  const SizedBox(height: 5),
                  Text("BANK MANDIRI: 123-456-7890", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 20, color: spektaRed)),
                  const Text("a/n Spekta Academy Indonesia", style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                ],
              ),
            ),

            const SizedBox(height: 20),

            // 3. UPLOAD BUKTI (Di bawah Rekening)
            const Text("Upload Bukti Transfer", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)),
            const SizedBox(height: 10),
            InkWell(
              onTap: _pickImage,
              child: Container(
                height: 180, width: double.infinity,
                decoration: BoxDecoration(color: Colors.grey[100], borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade300)),
                child: _imageFile == null 
                  ? Column(mainAxisAlignment: MainAxisAlignment.center, children: [Icon(Icons.add_a_photo_outlined, size: 50, color: spektaRed), const Text("Klik untuk pilih foto struk", style: TextStyle(fontSize: 12, color: Colors.grey))])
                  : ClipRRect(borderRadius: BorderRadius.circular(15), child: Image.file(_imageFile!, fit: BoxFit.cover)),
              ),
            ),

            const SizedBox(height: 40),

            // 4. TOMBOL DAFTAR
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 55), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
              onPressed: _submitData,
              child: const Text("DAFTAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
            const SizedBox(height: 30),
          ],
        ),
      ),
    );
  }

  Widget _buildReadOnlyField(TextEditingController ctrl, String label, IconData icon) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 15),
      child: TextField(
        controller: ctrl,
        readOnly: true,
        decoration: InputDecoration(
          labelText: label,
          prefixIcon: Icon(icon, color: spektaRed),
          filled: true,
          fillColor: Colors.grey[100],
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
        ),
      ),
    );
  }
}