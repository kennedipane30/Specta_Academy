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
  
  // ============================================================
  // 🎨 PALET WARNA SPEKTA (KONSISTEN DENGAN TRYOUTDETAILPAGE)
  // ============================================================
  static const Color primaryRed      = Color(0xFFC5352C);
  static const Color accentTeal      = Color(0xFF2EA8AB);
  static const Color darkTeal        = Color(0xFF00696C);
  static const Color lightBlueBg     = Color(0xFFEFF4FF);
  static const Color pageBg          = Color(0xFFF1F5F9);
  static const Color textDark        = Color(0xFF0F172A);
  static const Color textDarkVariant = Color(0xFF334155);
  static const Color neutralGray     = Color(0xFF64748B);
  static const Color outlineVariant  = Color(0xFFE2BEBA);
  
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
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: textDark)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(
              controller: titleCtrl, 
              decoration: InputDecoration(
                labelText: "Judul Dokumen",
                labelStyle: const TextStyle(color: neutralGray),
                hintText: "Contoh: Bank Soal TIU 2024",
                prefixIcon: Icon(Icons.title, color: accentTeal),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide(color: outlineVariant),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide(color: accentTeal, width: 1.5),
                ),
              ),
            ),
            const SizedBox(height: 15),
            DropdownButtonFormField<String>(
              decoration: InputDecoration(
                labelText: "Pilih Mata Pelajaran",
                labelStyle: const TextStyle(color: neutralGray),
                prefixIcon: Icon(Icons.subject, color: accentTeal),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide(color: outlineVariant),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide(color: accentTeal, width: 1.5),
                ),
              ),
              dropdownColor: Colors.white,
              items: ["TIU", "TWK", "TKP", "English", "Psychology", "General"]
                  .map((e) => DropdownMenuItem(value: e, child: Text(e, style: const TextStyle(color: textDark))))
                  .toList(),
              onChanged: (v) => selectedSubject = v,
            )
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context), 
            child: const Text("Batal", style: TextStyle(color: neutralGray))
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: accentTeal,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              elevation: 0,
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
        content: Text(message, style: const TextStyle(fontWeight: FontWeight.bold)), 
        backgroundColor: accentTeal,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        duration: const Duration(seconds: 3),
      )
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: pageBg,
      appBar: AppBar(
        flexibleSpace: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [primaryRed, accentTeal],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
        ),
        title: const Text(
          "Question Bank Hub", 
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.white)
        ), 
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: isLoading 
        ? Center(child: CircularProgressIndicator(color: accentTeal))
        : RefreshIndicator(
            color: accentTeal,
            onRefresh: _loadData,
            child: questions.isEmpty 
              ? _buildEmptyState()
              : _buildListView(),
          ),
      floatingActionButton: FloatingActionButton.extended(
        backgroundColor: accentTeal,
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
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: accentTeal.withOpacity(0.08),
                  shape: BoxShape.circle,
                ),
                child: Icon(Icons.cloud_off_rounded, size: 60, color: accentTeal),
              ),
              const SizedBox(height: 16),
              const Text(
                "Belum ada soal yang dibagikan.", 
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: textDark)
              ),
              Text(
                "Tarik ke bawah untuk menyegarkan", 
                style: TextStyle(color: neutralGray, fontSize: 13)
              ),
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
            border: Border.all(color: outlineVariant.withOpacity(0.4)),
            boxShadow: [
              BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10, offset: const Offset(0, 4))
            ],
          ),
          child: ListTile(
            contentPadding: const EdgeInsets.symmetric(horizontal: 18, vertical: 10),
            leading: Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: accentTeal.withOpacity(0.08),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(Icons.picture_as_pdf_rounded, color: accentTeal, size: 28),
            ),
            title: Text(item['title'] ?? "Dokumen Tanpa Judul", 
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: textDark)),
            subtitle: Padding(
              padding: const EdgeInsets.only(top: 4),
              child: Text("$uploader • ${item['subject']}", 
                style: TextStyle(color: neutralGray, fontSize: 12)),
            ),
            trailing: Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: accentTeal.withOpacity(0.1),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Icon(Icons.open_in_new_rounded, color: accentTeal, size: 16),
            ),
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