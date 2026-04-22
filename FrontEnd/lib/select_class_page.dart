import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'class_detail_page.dart'; // Import halaman detail yang sudah kita buat sebelumnya

class SelectClassPage extends StatefulWidget {
  final String token;
  final Map userData;

  const SelectClassPage({super.key, required this.token, required this.userData});

  @override
  State<SelectClassPage> createState() => _SelectClassPageState();
}

class _SelectClassPageState extends State<SelectClassPage> {
  
  // Fungsi mengambil data dari API Laravel
  Future<List<dynamic>> fetchClasses() async {
    final response = await http.get(
      Uri.parse('http://10.0.2.2:8000/api/classes'), // 10.0.2.2 untuk emulator
      headers: {
        'Authorization': 'Bearer ${widget.token}',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      var result = json.decode(response.body);
      return result['data']; // Mengambil array 'data' dari JSON
    } else {
      throw Exception('Gagal memuat katalog kelas');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text("Select Class Program", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        backgroundColor: const Color(0xFF990000),
        elevation: 0,
      ),
      body: FutureBuilder<List<dynamic>>(
        future: fetchClasses(),
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator(color: Color(0xFF990000)));
          } else if (snapshot.hasError) {
            return Center(child: Text("Error: ${snapshot.error}"));
          } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return const Center(child: Text("Belum ada program kelas tersedia."));
          }

          final classes = snapshot.data!;

          return ListView.builder(
            padding: const EdgeInsets.all(15),
            itemCount: classes.length,
            itemBuilder: (context, index) {
              final item = classes[index];
              return _buildClassCard(item);
            },
          );
        },
      ),
    );
  }

  Widget _buildClassCard(Map item) {
    // Memperbaiki URL agar terbaca di emulator (127.0.0.1 -> 10.0.2.2)
    String imageUrl = (item['image_url'] ?? '').replaceAll('127.0.0.1', '10.0.2.2');

    return Container(
      margin: const EdgeInsets.bottom(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(25),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 15)],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // BANNER GAMBAR DARI ADMIN
          ClipRRect(
            borderRadius: const BorderRadius.vertical(top: Radius.circular(25)),
            child: Image.network(
              imageUrl,
              height: 200,
              width: double.infinity,
              fit: BoxFit.cover,
              errorBuilder: (c, e, s) => Container(
                height: 200, color: Colors.grey[200], 
                child: const Icon(Icons.broken_image, color: Colors.grey)
              ),
            ),
          ),
          
          Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(item['program_name'], style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
                const SizedBox(height: 8),
                Text(
                  item['description'] ?? 'Deskripsi program...',
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(color: Colors.grey[600], fontSize: 13),
                ),
                const SizedBox(height: 15),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      "Rp ${item['price']}",
                      style: const TextStyle(color: Color(0xFF990000), fontWeight: FontWeight.bold, fontSize: 18),
                    ),
                    ElevatedButton(
                      onPressed: () {
                        // PINDAH KE HALAMAN DETAIL YANG DINAMIS
                        Navigator.push(context, MaterialPageRoute(builder: (_) => ClassDetailPage(
                          classId: item['class_id'],
                          className: item['program_name'],
                          token: widget.token,
                          userData: widget.userData,
                        )));
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFFF1B401),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                      ),
                      child: const Text("View Details", style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold)),
                    )
                  ],
                )
              ],
            ),
          )
        ],
      ),
    );
  }
}