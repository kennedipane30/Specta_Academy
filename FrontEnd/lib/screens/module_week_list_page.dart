import 'package:flutter/material.dart';
import 'package:spectaacademy/screens/pdf_viewer_page.dart'; // Sesuaikan path jika berbeda

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

    debugPrint("Target Subject: $subjectName");
    debugPrint("Total Materials Received: ${allMaterials.length}");

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: Text(
          subjectName, 
          style: const TextStyle(
            fontWeight: FontWeight.bold, 
            color: Colors.white, 
            fontSize: 18
          )
        ),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(20),
        itemCount: 20, // Menampilkan 20 minggu
        itemBuilder: (context, index) {
          int weekNumber = index + 1;

          // Cari apakah ada materi yang cocok dengan Minggu dan Mata Pelajaran ini
          var materialData = allMaterials.firstWhere(
            (m) {
              final String mSubject = (m['subject_name'] ?? m['material_name'] ?? m['MaterialName'] ?? '').toString().toLowerCase().trim();
              final String mWeek = (m['week'] ?? m['Week'] ?? '').toString();
              return mWeek == weekNumber.toString() && mSubject == subjectName.toLowerCase().trim();
            },
            orElse: () => null,
          );

          bool isUploaded = materialData != null;

          return Container(
            margin: const EdgeInsets.only(bottom: 15),
            decoration: BoxDecoration(
              color: Colors.white, 
              borderRadius: BorderRadius.circular(20), 
              boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10)],
              border: Border.all(
                color: isUploaded ? spektaRed.withOpacity(0.2) : Colors.transparent, 
                width: 1.5
              )
            ),
            child: ListTile(
              contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
              leading: Container(
                width: 50, 
                height: 50, 
                decoration: BoxDecoration(
                  color: isUploaded ? spektaRed.withOpacity(0.1) : Colors.grey[100], 
                  borderRadius: BorderRadius.circular(15)
                ), 
                child: Icon(
                  isUploaded ? Icons.picture_as_pdf_rounded : Icons.lock_clock_rounded, 
                  color: isUploaded ? spektaRed : Colors.grey[400]
                )
              ),
              title: Text(
                "Week $weekNumber", 
                style: TextStyle(
                  fontWeight: FontWeight.bold, 
                  color: isUploaded ? Colors.black : Colors.grey[400]
                )
              ),
              subtitle: Text(
                isUploaded 
                  ? (materialData['title'] ?? materialData['Title'] ?? "Materi Tersedia") 
                  : "Material not yet uploaded", 
                style: TextStyle(
                  fontSize: 12, 
                  color: isUploaded ? Colors.grey[600] : Colors.grey[300]
                )
              ),
              trailing: Icon(
                Icons.arrow_forward_ios_rounded, 
                size: 14, 
                color: isUploaded ? spektaRed : Colors.grey[300]
              ),
              onTap: () {
                if (isUploaded) {
                  String path = (materialData['file_path'] ?? materialData['FilePath'] ?? '').toString();
                  
                  if (path.isEmpty) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(
                        content: Text("Path file tidak valid"),
                        backgroundColor: Colors.red,
                      ),
                    );
                    return;
                  }
                  
                  // Pastikan URL mengarah ke storage Laravel
                  String pdfUrl = "http://10.0.2.2:8000/storage/$path";
                  
                  debugPrint("📄 Opening PDF: $pdfUrl");
                  debugPrint("🔑 Sending Token: ${token.isNotEmpty ? 'YES' : 'NO'}");
                  
                  // ✨ PERBAIKAN: Menambahkan parameter 'token'
                  Navigator.push(
                    context, 
                    MaterialPageRoute(
                      builder: (context) => PdfViewerPage(
                        pdfUrl: pdfUrl, 
                        title: "Week $weekNumber - $subjectName",
                        token: token, // 👈 Token sekarang dikirim ke PdfViewerPage
                      ),
                    ),
                  );
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      content: Text("Materi untuk minggu ini belum tersedia"),
                      backgroundColor: Colors.orange,
                    ),
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