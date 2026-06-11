import 'package:flutter/material.dart';
import 'dart:convert';

class AnnouncementDetailPage extends StatelessWidget {
  final Map data;
  
  const AnnouncementDetailPage({super.key, required this.data});

  /// ✅ Perbaiki URL gambar (sama seperti di ReportPage)
  String _fixImageUrl(String path) {
    if (path.isEmpty) return '';
    
    if (path.startsWith('http://') || path.startsWith('https://')) {
      return path.replaceAll('127.0.0.1', '10.0.2.2');
    }
    
    String cleanPath = path;
    if (cleanPath.startsWith('/')) {
      cleanPath = cleanPath.substring(1);
    }
    
    if (cleanPath.startsWith('storage/')) {
      return 'http://10.0.2.2:8000/' + cleanPath;
    }
    
    if (cleanPath.startsWith('announcements/')) {
      return 'http://10.0.2.2:8000/storage/' + cleanPath;
    }
    
    return 'http://10.0.2.2:8000/storage/announcements/' + cleanPath;
  }

  @override
  Widget build(BuildContext context) {
    // Ambil URL gambar
    String imageUrl = '';
    if (data.containsKey('image_url') && data['image_url'] != null && data['image_url'].toString().isNotEmpty) {
      imageUrl = data['image_url'].toString();
    } else if (data.containsKey('image') && data['image'] != null && data['image'].toString().isNotEmpty) {
      imageUrl = _fixImageUrl(data['image'].toString());
    }
    
    // Ambil judul dan deskripsi
    String title = data['title']?.toString() ?? 'Tanpa Judul';
    String description = data['description']?.toString() ?? 'Tidak ada deskripsi tersedia.';
    String createdAt = data['created_at']?.toString() ?? '';
    
    // Format tanggal
    String formattedDate = '';
    if (createdAt.isNotEmpty) {
      try {
        DateTime dateTime = DateTime.parse(createdAt);
        formattedDate = '${dateTime.day} ${_getMonthName(dateTime.month)} ${dateTime.year}';
      } catch (e) {
        formattedDate = createdAt;
      }
    }

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text(
          "Detail Pengumuman", 
          style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)
        ),
        backgroundColor: const Color(0xFF990000),
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // ✅ MENAMPILKAN GAMBAR (bisa diklik untuk zoom - opsional)
            if (imageUrl.isNotEmpty)
              GestureDetector(
                onTap: () {
                  // Opsional: Tampilkan gambar dalam dialog zoom
                  _showImageZoom(context, imageUrl);
                },
                child: Image.network(
                  imageUrl,
                  width: double.infinity,
                  height: 250,
                  fit: BoxFit.cover,
                  loadingBuilder: (context, child, loadingProgress) {
                    if (loadingProgress == null) return child;
                    return Container(
                      height: 250,
                      width: double.infinity,
                      color: Colors.grey[200],
                      child: const Center(
                        child: CircularProgressIndicator(strokeWidth: 2),
                      ),
                    );
                  },
                  errorBuilder: (context, error, stackTrace) => Container(
                    height: 250, 
                    width: double.infinity,
                    color: Colors.grey[100],
                    child: const Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.broken_image, size: 50, color: Colors.grey),
                        SizedBox(height: 8),
                        Text("Gambar tidak tersedia", style: TextStyle(color: Colors.grey)),
                      ],
                    ),
                  ),
                ),
              )
            else
              Container(
                height: 200, 
                color: Colors.grey[200], 
                child: const Icon(Icons.image_not_supported, size: 50, color: Colors.grey),
              ),

            Padding(
              padding: const EdgeInsets.all(24.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // ✅ JUDUL
                  Text(
                    title,
                    style: const TextStyle(
                      fontSize: 22, 
                      fontWeight: FontWeight.w900, 
                      color: Color(0xFF333333),
                      height: 1.3,
                    ),
                  ),
                  
                  const SizedBox(height: 12),
                  
                  // ✅ TANGGAL (jika ada)
                  if (formattedDate.isNotEmpty)
                    Row(
                      children: [
                        Icon(Icons.calendar_today, size: 14, color: Colors.grey[500]),
                        const SizedBox(width: 6),
                        Text(
                          formattedDate,
                          style: TextStyle(color: Colors.grey[500], fontSize: 12),
                        ),
                      ],
                    ),
                  
                  const SizedBox(height: 12),
                  const Divider(thickness: 1, color: Color(0xFFEEEEEE)),
                  const SizedBox(height: 16),
                  
                  // ✅ DESKRIPSI
                  Text(
                    description,
                    style: TextStyle(
                      fontSize: 15, 
                      color: Colors.grey[700], 
                      height: 1.6,
                      fontWeight: FontWeight.w500,
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

  /// Opsional: Tampilkan gambar dalam mode zoom
  void _showImageZoom(BuildContext context, String imageUrl) {
    showDialog(
      context: context,
      builder: (context) => Dialog(
        backgroundColor: Colors.transparent,
        insetPadding: EdgeInsets.zero,
        child: Stack(
          children: [
            Center(
              child: InteractiveViewer(
                minScale: 0.5,
                maxScale: 4.0,
                child: Image.network(
                  imageUrl,
                  fit: BoxFit.contain,
                  width: double.infinity,
                  height: double.infinity,
                ),
              ),
            ),
            Positioned(
              top: 40,
              right: 20,
              child: IconButton(
                icon: const Icon(Icons.close, color: Colors.white, size: 30),
                onPressed: () => Navigator.pop(context),
              ),
            ),
          ],
        ),
      ),
    );
  }

  String _getMonthName(int month) {
    const months = [
      'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    return months[month - 1];
  }
}