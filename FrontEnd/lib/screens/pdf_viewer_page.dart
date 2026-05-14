import 'package:flutter/material.dart';
import 'package:syncfusion_flutter_pdfviewer/pdfviewer.dart';

class PdfViewerPage extends StatelessWidget {
  final String pdfUrl;
  final String title;

  const PdfViewerPage({
    super.key, 
    required this.pdfUrl, 
    required this.title
  });

  @override
  Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: Text(
          title, 
          style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 15)
        ),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      // ✨ Widget SF PDF Viewer untuk menampilkan PDF dari URL secara langsung
      body: SfPdfViewer.network(
        pdfUrl,
        onDocumentLoadFailed: (PdfDocumentLoadFailedDetails details) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              backgroundColor: Colors.red,
              content: Text("Gagal memuat dokumen: ${details.description}"),
            ),
          );
        },
      ),
    );
  }
}