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
    final Color spektaRed = const Color(0xFF990000);

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: Text(subjectName, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(20),
        itemCount: 20, 
        itemBuilder: (context, index) {
          int weekNumber = index + 1;
          var materialData = allMaterials.firstWhere(
            (m) => (m['week'].toString() == weekNumber.toString()) && 
                  (m['material_name']?.toString().toLowerCase().trim() == subjectName.toLowerCase().trim()),
            orElse: () => null,
          );

          bool isUploaded = materialData != null;

          return Container(
            margin: const EdgeInsets.only(bottom: 15),
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10)], border: Border.all(color: isUploaded ? spektaRed.withOpacity(0.2) : Colors.transparent, width: 1.5)),
            child: ListTile(
              contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
              leading: Container(width: 50, height: 50, decoration: BoxDecoration(color: isUploaded ? spektaRed.withOpacity(0.1) : Colors.grey[100], borderRadius: BorderRadius.circular(15)), child: Icon(isUploaded ? Icons.picture_as_pdf_rounded : Icons.lock_clock_rounded, color: isUploaded ? spektaRed : Colors.grey[400])),
              title: Text("Week $weekNumber", style: TextStyle(fontWeight: FontWeight.bold, color: isUploaded ? Colors.black : Colors.grey[400])),
              subtitle: Text(isUploaded ? (materialData['title'] ?? "Materi Tersedia") : "Material not yet uploaded", style: TextStyle(fontSize: 12, color: isUploaded ? Colors.grey[600] : Colors.grey[300])),
              trailing: Icon(Icons.arrow_forward_ios_rounded, size: 14, color: isUploaded ? spektaRed : Colors.grey[300]),
              onTap: () {
                if (isUploaded) {
                  // File tetap di Laravel Port 8000
                  String pdfUrl = "http://10.0.2.2:8000/storage/${materialData['file_path']}";
                  Navigator.push(context, MaterialPageRoute(builder: (context) => PdfViewerPage(pdfUrl: pdfUrl, title: "Materi Week $weekNumber - $subjectName")));
                }
              },
            ),
          );
        },
      ),
    );
  }
}