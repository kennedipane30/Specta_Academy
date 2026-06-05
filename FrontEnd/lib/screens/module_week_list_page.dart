import 'package:flutter/material.dart';
import 'pdf_viewer_page.dart';

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
              trailing: const Icon(Icons.arrow_forward_ios_rounded, size: 12),
              onTap: () {
                if (isAvailable) {
                  String path = materialData['file_path'].toString();
                  // Arahkan ke host Laravel untuk file storage
                  String pdfUrl = path.startsWith('http') ? path : "http://10.0.2.2:8000/storage/$path";
                  
                  Navigator.push(context, MaterialPageRoute(
                    builder: (context) => PdfViewerPage(pdfUrl: pdfUrl, title: "$subjectName - Minggu $weekNumber", token: token)
                  ));
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text("Materi minggu ini belum tersedia"), backgroundColor: Colors.orange)
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