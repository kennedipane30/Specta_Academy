import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:file_picker/file_picker.dart';
import 'package:http/http.dart' as http;
import 'package:url_launcher/url_launcher.dart';
import '../../services/question_bank_service.dart';

class QuestionSharingPage extends StatefulWidget {
  final String token;

  const QuestionSharingPage({super.key, required this.token});

  @override
  State<QuestionSharingPage> createState() => _QuestionSharingPageState();
}

class _QuestionSharingPageState extends State<QuestionSharingPage> {
  List questions = [];
  bool isLoading = true;
  
  // Warna Merah Specta Academy (Sesuai Branding)
  final Color spektaRed = const Color(0xFF9C0412);
  final String baseUrl = 'http://10.0.2.2:8000'; // IP Emulator Android

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  // 1. Fungsi Mengambil Data (Ditambahkan check 'mounted' untuk cegah memory leak)
  Future<void> _loadData() async {
    try {
      if (!mounted) return;
      setState(() => isLoading = true);
      
      final resp = await QuestionBankService.getAllQuestions(widget.token);
      
      if (!mounted) return; // Cek lagi setelah nunggu response API

      if (resp.statusCode == 200) {
        final decoded = jsonDecode(resp.body);
        setState(() {
          questions = decoded['data'] ?? [];
          isLoading = false;
        });
      } else {
        setState(() => isLoading = false);
      }
    } catch (e) {
      debugPrint("Error Load Data: $e");
      if (mounted) setState(() => isLoading = false);
    }
  }

  // 2. Fungsi Memilih File PDF
  void _pickAndUpload() async {
    FilePickerResult? result = await FilePicker.platform.pickFiles(
      type: FileType.custom, 
      allowedExtensions: ['pdf'],
    );
    
    // Jika user memilih file, baru tampilkan dialog input judul
    if (result != null && result.files.single.path != null) {
      _showUploadDialog(result.files.single.path!);
    } else {
      // Jika kosong (seperti di gambar emulator Anda), beri tahu user
      _showSnackBar("Tidak ada file yang dipilih atau file bukan PDF.");
    }
  }

  // 3. Dialog Form Input
  void _showUploadDialog(String path) {
    final titleCtrl = TextEditingController();
    String? selectedSubject;

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text("Bagikan Soal Baru", 
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(
              controller: titleCtrl, 
              decoration: const InputDecoration(
                labelText: "Judul Dokumen",
                hintText: "Contoh: Bank Soal TIU 2024",
                prefixIcon: Icon(Icons.title),
              ),
            ),
            const SizedBox(height: 15),
            DropdownButtonFormField<String>(
              decoration: const InputDecoration(
                labelText: "Pilih Mata Pelajaran",
                prefixIcon: Icon(Icons.subject),
              ),
              items: ["TIU", "TWK", "TKP", "English", "Psychology", "General"]
                  .map((e) => DropdownMenuItem(value: e, child: Text(e)))
                  .toList(),
              onChanged: (v) => selectedSubject = v,
            )
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context), 
            child: const Text("Batal", style: TextStyle(color: Colors.grey))
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: spektaRed,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))
            ),
            onPressed: () async {
              if (titleCtrl.text.isEmpty || selectedSubject == null) {
                _showSnackBar("Harap isi judul dan mata pelajaran!");
                return;
              }
              Navigator.pop(context);
              _processUpload(titleCtrl.text, selectedSubject!, path);
            }, 
            child: const Text("Upload PDF", style: TextStyle(color: Colors.white))
          )
        ],
      ),
    );
  }

  // 4. Proses Upload ke Laravel (Ditambahkan check 'mounted')
  Future<void> _processUpload(String title, String subject, String path) async {
    _showSnackBar("Sedang mengunggah soal ke Hub...");

    try {
      var stream = await QuestionBankService.uploadQuestion(
        title: title,
        subject: subject,
        filePath: path,
        token: widget.token
      );

      if (!mounted) return;

      if (stream.statusCode == 201) {
        _showSnackBar("Berhasil! Soal Anda telah dibagikan.");
        _loadData(); 
      } else if (stream.statusCode == 403) {
        _showSnackBar("Gagal: Hanya siswa aktif yang bisa berbagi soal.");
      } else {
        _showSnackBar("Gagal mengunggah soal (Status: ${stream.statusCode})");
      }
    } catch (e) {
      _showSnackBar("Terjadi kesalahan teknis saat mengunggah.");
    }
  }

  void _showSnackBar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message), 
        behavior: SnackBarBehavior.floating,
        duration: const Duration(seconds: 3),
      )
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: const Text("Question Bank Hub", 
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)), 
        backgroundColor: spektaRed, 
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
      ),
      body: isLoading 
        ? Center(child: CircularProgressIndicator(color: spektaRed))
        : RefreshIndicator(
            color: spektaRed,
            onRefresh: _loadData,
            child: questions.isEmpty 
              ? _buildEmptyState()
              : _buildListView(),
          ),
      floatingActionButton: FloatingActionButton.extended(
        backgroundColor: spektaRed,
        onPressed: _pickAndUpload,
        icon: const Icon(Icons.add_circle_outline, color: Colors.white),
        label: const Text("Share Soal", 
          style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
      ),
    );
  }

  // Perbaikan Empty State agar bisa di-refresh
  Widget _buildEmptyState() {
    return ListView(
      physics: const AlwaysScrollableScrollPhysics(),
      children: [
        SizedBox(height: MediaQuery.of(context).size.height * 0.25),
        Center(
          child: Column(
            children: [
              Icon(Icons.cloud_off_rounded, size: 80, color: Colors.grey.shade300),
              const SizedBox(height: 16),
              const Text("Belum ada soal yang dibagikan.", 
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.grey)),
              const Text("Tarik ke bawah untuk menyegarkan", 
                style: TextStyle(color: Colors.grey, fontSize: 13)),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildListView() {
    return ListView.builder(
      itemCount: questions.length,
      padding: const EdgeInsets.fromLTRB(15, 15, 15, 90),
      itemBuilder: (context, index) {
        var item = questions[index];
        String uploader = (item['user'] != null) ? item['user']['name'] : "Siswa Specta";

        return Container(
          margin: const EdgeInsets.only(bottom: 12),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(15),
            boxShadow: [
              BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 4))
            ],
          ),
          child: ListTile(
            contentPadding: const EdgeInsets.symmetric(horizontal: 18, vertical: 10),
            leading: Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: const Color(0xFFFFEEEE),
                borderRadius: BorderRadius.circular(12),
              ),
              child: const Icon(Icons.picture_as_pdf_rounded, color: Color(0xFFC62828), size: 28),
            ),
            title: Text(item['title'] ?? "Dokumen Tanpa Judul", 
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
            subtitle: Padding(
              padding: const EdgeInsets.only(top: 4),
              child: Text("$uploader • ${item['subject']}", 
                style: TextStyle(color: Colors.grey.shade600, fontSize: 12)),
            ),
            trailing: const Icon(Icons.open_in_new_rounded, color: Colors.grey, size: 20),
            onTap: () async {
              final String? path = item['file_path'];
              if (path == null || path.isEmpty) {
                _showSnackBar("File tidak tersedia.");
                return;
              }

              final url = Uri.parse("$baseUrl/storage/$path");
              if (await canLaunchUrl(url)) {
                await launchUrl(url, mode: LaunchMode.externalApplication);
              } else {
                _showSnackBar("Maaf, tidak dapat membuka dokumen ini.");
              }
            },
          ),
        );
      },
    );
  }
}