import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';

class ModuleWeekListPage extends StatelessWidget {
  final String subjectName;
  final String token;
  final List allMaterials;

  const ModuleWeekListPage({
    super.key,
    required this.subjectName,
    required this.token,
    required this.allMaterials,
  });

  // Fungsi untuk membuka PDF di Chrome/browser eksternal
  Future<void> _openPdfInBrowser(String pdfUrl, BuildContext context) async {
    final Uri url = Uri.parse(pdfUrl);
    
    try {
      // Cek apakah bisa dibuka
      if (await canLaunchUrl(url)) {
        await launchUrl(
          url,
          mode: LaunchMode.externalApplication, // Buka di browser eksternal
          webViewConfiguration: const WebViewConfiguration(
            enableJavaScript: true,
            enableDomStorage: true,
          ),
        );
      } else {
        throw 'Tidak dapat membuka URL: $pdfUrl';
      }
    } catch (e) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text("Gagal membuka PDF: ${e.toString()}"),
            backgroundColor: Colors.red,
            duration: const Duration(seconds: 3),
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: Text(subjectName, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        centerTitle: true,
        actions: [
          IconButton(
            icon: const Icon(Icons.info_outline),
            onPressed: () {
              showDialog(
                context: context,
                builder: (context) => AlertDialog(
                  title: const Text("Informasi"),
                  content: const Text("PDF akan dibuka di browser Chrome.\nTutup browser untuk kembali ke aplikasi."),
                  actions: [
                    TextButton(
                      onPressed: () => Navigator.pop(context),
                      child: const Text("OK"),
                    ),
                  ],
                ),
              );
            },
          ),
        ],
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(20),
        itemCount: 20, // Tetap menampilkan 20 minggu
        itemBuilder: (context, index) {
          int weekNumber = index + 1;

          // ✨ LOGIKA PENCARIAN: Mencari materi di minggu ini untuk mapel ini
          var materialData = allMaterials.firstWhere(
            (m) {
              final String mSubject = (m['subject_name'] ?? m['material_name'] ?? '').toString().toLowerCase().trim();
              final String mWeek = (m['week'] ?? '0').toString();
              return mWeek == weekNumber.toString() && mSubject == subjectName.toLowerCase().trim();
            },
            orElse: () => null,
          );

          bool isAvailable = materialData != null && (materialData['file_path'] ?? '').toString().isNotEmpty;

          return Container(
            margin: const EdgeInsets.only(bottom: 12),
            decoration: BoxDecoration(
              color: Colors.white, 
              borderRadius: BorderRadius.circular(15), 
              border: Border.all(color: isAvailable ? spektaRed.withOpacity(0.2) : Colors.transparent),
              boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 5)]
            ),
            child: ListTile(
              leading: Icon(
                isAvailable ? Icons.picture_as_pdf_rounded : Icons.lock_clock_rounded, 
                color: isAvailable ? spektaRed : Colors.grey[300]
              ),
              title: Text("Minggu $weekNumber", 
                style: TextStyle(fontWeight: FontWeight.bold, color: isAvailable ? Colors.black : Colors.grey)),
              subtitle: Text(isAvailable ? (materialData['title'] ?? "Materi Tersedia") : "Materi belum diunggah"),
              trailing: const Icon(Icons.open_in_browser, size: 16), // Changed icon to indicate external
              onTap: () {
                if (isAvailable) {
                  String path = materialData['file_path'].toString();
                  
                  // Buat URL lengkap untuk akses file
                  String pdfUrl;
                  if (path.startsWith('http')) {
                    pdfUrl = path;
                  } else if (path.startsWith('/storage')) {
                    pdfUrl = "http://10.0.2.2:8000$path";
                  } else if (path.startsWith('storage')) {
                    pdfUrl = "http://10.0.2.2:8000/$path";
                  } else {
                    pdfUrl = "http://10.0.2.2:8000/storage/$path";
                  }
                  
                  // Tambahkan token jika diperlukan (opsional)
                  if (token.isNotEmpty) {
                    pdfUrl = "$pdfUrl?token=$token";
                  }
                  
                  // Buka di Chrome/browser eksternal
                  _openPdfInBrowser(pdfUrl, context);
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      content: Text("Materi minggu ini belum tersedia"), 
                      backgroundColor: Colors.orange,
                      duration: Duration(seconds: 2),
                    )
                  );
                }
              },
            ),
          );
        },
      ),
    );
  }
}