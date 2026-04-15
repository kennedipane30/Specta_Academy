import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';

class ModuleWeekListPage extends StatelessWidget {
  final String subjectName;
  final List allMaterials; 
  final String token;

  const ModuleWeekListPage({
    super.key,
    required this.subjectName,
    required this.allMaterials,
    required this.token,
  });

  @override
  Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);

    List filteredMateri = allMaterials.where((m) => m['title'] == subjectName).toList();

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: Text(subjectName.toUpperCase(), style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(20),
        itemCount: 20, 
        itemBuilder: (context, index) {
          int weekNumber = index + 1;
          
          // MODIFIKASI: Menggunakan key 'week' sesuai database baru
          var moduleData = filteredMateri.firstWhere(
            (m) => m['week'].toString() == weekNumber.toString(),
            orElse: () => null,
          );

          bool isAvailable = moduleData != null;

          return Container(
            margin: const EdgeInsets.only(bottom: 15),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(20),
              boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10, offset: const Offset(0, 4))],
              border: Border.all(color: isAvailable ? spektaRed.withOpacity(0.1) : Colors.grey.shade200),
            ),
            child: ListTile(
              contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
              leading: Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: isAvailable ? spektaRed.withOpacity(0.1) : Colors.grey.shade100,
                  borderRadius: BorderRadius.circular(15),
                ),
                child: Icon(
                  isAvailable ? Icons.description_rounded : Icons.lock_clock_outlined,
                  color: isAvailable ? spektaRed : Colors.grey,
                ),
              ),
              title: Text(
                "Week $weekNumber",
                style: TextStyle(
                  fontWeight: FontWeight.w900,
                  fontSize: 15,
                  color: isAvailable ? Colors.black87 : Colors.grey.shade400,
                ),
              ),
              subtitle: Text(
                // MODIFIKASI: Menggunakan key 'material_name'
                isAvailable ? (moduleData['material_name'] ?? "Tap to download PDF") : "Material not yet uploaded",
                style: TextStyle(fontSize: 11, color: Colors.grey.shade500),
              ),
              trailing: isAvailable
                  ? ElevatedButton(
                      onPressed: () => _downloadFile(moduleData['file_path']),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: spektaRed,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                        padding: const EdgeInsets.symmetric(horizontal: 12),
                      ),
                      child: const Text("PDF", style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold)),
                    )
                  : const Icon(Icons.chevron_right, color: Colors.grey),
            ),
          );
        },
      ),
    );
  }

  Future<void> _downloadFile(String? filePath) async {
    if (filePath == null || filePath == "") return;
    String fileName = filePath.split('/').last;
    final Uri url = Uri.parse("http://10.0.2.2:8000/view-galeri/$fileName");
    if (await canLaunchUrl(url)) {
      await launchUrl(url, mode: LaunchMode.externalApplication);
    }
  }
}