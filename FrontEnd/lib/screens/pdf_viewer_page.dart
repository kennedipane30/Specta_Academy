import 'package:flutter/material.dart';
import 'package:pdfrx/pdfrx.dart'; 

class PdfViewerPage extends StatelessWidget {
  final String pdfUrl;
  final String title;

  const PdfViewerPage({super.key, required this.pdfUrl, required this.title});

  @override
  Widget build(BuildContext context) {
    final Color spektaRed = const Color(0xFF990000);

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
      body: PdfViewer.uri(
        Uri.parse(pdfUrl),
        params: PdfViewerParams(
          // 1. Indikator Loading (Sudah benar dan tidak error)
          loadingBannerBuilder: (context, bytesLoaded, totalBytes) {
            return Center(
              child: CircularProgressIndicator(
                value: totalBytes != null ? bytesLoaded / totalBytes : null,
                backgroundColor: Colors.grey[200],
                color: spektaRed,
              ),
            );
          },
          // 2. ✨ PERBAIKAN: Gunakan 'errorBannerBuilder' dengan 4 parameter
          errorBannerBuilder: (context, error, stackTrace, documentRef) {
            return Center(
              child: Padding(
                padding: const EdgeInsets.all(20.0),
                child: Text(
                  'Gagal memuat PDF: $error',
                  textAlign: TextAlign.center,
                  style: const TextStyle(color: Colors.red, fontWeight: FontWeight.bold),
                ),
              ),
            );
          },
        ),
      ),
    );
  }
}