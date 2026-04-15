import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';

class PdfViewerPage extends StatelessWidget {
  final String pdfUrl;
  final String title;

  const PdfViewerPage({super.key, required this.pdfUrl, required this.title});

  Future<void> _launchPDF() async {
    final Uri url = Uri.parse(pdfUrl);
    if (!await launchUrl(url, mode: LaunchMode.externalApplication)) {
      throw 'Could not open $pdfUrl';
    }
  }

  @override
  Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(title: Text(title, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)), backgroundColor: spektaRed, foregroundColor: Colors.white, elevation: 0),
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(30.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(padding: const EdgeInsets.all(20), decoration: BoxDecoration(color: spektaRed.withOpacity(0.1), shape: BoxShape.circle), child: const Icon(Icons.picture_as_pdf_rounded, size: 100, color: spektaRed)),
              const SizedBox(height: 30),
              Text(title, textAlign: TextAlign.center, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
              const SizedBox(height: 10),
              const Text("Click the button below to read this material module using your favorite PDF Viewer.", textAlign: TextAlign.center, style: TextStyle(color: Colors.grey, fontSize: 14)),
              const SizedBox(height: 50),
              ElevatedButton.icon(
                style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 55), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)), elevation: 5),
                onPressed: _launchPDF,
                icon: const Icon(Icons.open_in_new_rounded, color: Colors.white),
                label: const Text("OPEN MODULE NOW", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
              ),
            ],
          ),
        ),
      ),
    );
  }
}