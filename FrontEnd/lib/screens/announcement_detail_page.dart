import 'package:flutter/material.dart';

class AnnouncementDetailPage extends StatelessWidget {
  final Map data;
  const AnnouncementDetailPage({super.key, required this.data});

  @override
  Widget build(BuildContext context) {
    // 1. MEMPERBAIKI URL GAMBAR
    // Menggabungkan IP Emulator + Path Storage Laravel + Path dari Database
    String rawPath = data['image'] ?? '';
    String imageUrl = "";

    if (rawPath.isNotEmpty) {
      // Jika path sudah mengandung http, biarkan. Jika tidak, tambahkan base URL.
      imageUrl = rawPath.startsWith('http') 
          ? rawPath.replaceAll('127.0.0.1', '10.0.2.2') 
          : "http://10.0.2.2:8000/storage/$rawPath";
    }

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text("Detail Pengumuman", 
          style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
        backgroundColor: const Color(0xFF990000),
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // MENAMPILKAN GAMBAR
            if (imageUrl.isNotEmpty)
              Image.network(
                imageUrl,
                width: double.infinity,
                height: 250,
                fit: BoxFit.cover,
                errorBuilder: (context, error, stackTrace) => Container(
                  height: 200, 
                  width: double.infinity,
                  color: Colors.grey[100], 
                  child: const Icon(Icons.broken_image, size: 50, color: Colors.grey),
                ),
              )
            else
              Container(height: 200, color: Colors.grey[200], child: const Icon(Icons.image_not_supported)),

            Padding(
              padding: const EdgeInsets.all(24.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // JUDUL
                  Text(
                    data['title'] ?? 'Tanpa Judul',
                    style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w900, color: Color(0xFF333333)),
                  ),
                  const SizedBox(height: 12),
                  const Divider(thickness: 1),
                  const SizedBox(height: 12),
                  
                  // 2. MEMPERBAIKI DESKRIPSI (Gunakan key 'description' sesuai database)
                  Text(
                    data['description'] ?? 'Tidak ada deskripsi tersedia.',
                    style: TextStyle(
                      fontSize: 15, 
                      color: Colors.grey[700], 
                      height: 1.6,
                      fontWeight: FontWeight.w500
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}