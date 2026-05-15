import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'screens/class_detail_page.dart'; 

class SelectClassPage extends StatefulWidget {
  final String token;
  final Map userData;

  const SelectClassPage({super.key, required this.token, required this.userData});

  @override
  State<SelectClassPage> createState() => _SelectClassPageState();
}

class _SelectClassPageState extends State<SelectClassPage> {
  
  // 1. FUNGSI MAPPING (Memetakan ID Database ke File Lokal)
  String _getProgramImage(dynamic id) {
    // Konversi dynamic ke int agar switch-case bekerja akurat
    int classId = int.tryParse(id.toString()) ?? 0;
    
    switch (classId) {
      case 1:
        return 'assets/images/abdi_negara.png';
      case 2:
        return 'assets/images/ptn_unhan.png';
      case 3:
        return 'assets/images/reguler.png';
      case 4:
        return 'assets/images/favorit.png';
      default:
        // Jika ID tidak dikenal (misal ID = 10), munculkan gambar pertama sebagai default
        return 'assets/images/abdi_negara.png'; 
    }
  }

  // Fungsi mengambil data dari API Laravel
  Future<List<dynamic>> fetchClasses() async {
    try {
      final response = await http.get(
        Uri.parse('http://10.0.2.2:8000/api/classes'), 
        headers: {
          'Authorization': 'Bearer ${widget.token}',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        var result = json.decode(response.body);
        return result['data']; 
      } else {
        throw Exception('Gagal memuat katalog kelas');
      }
    } catch (e) {
      throw Exception('Kesalahan Koneksi: $e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text("Study Program", 
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        backgroundColor: const Color(0xFF990000),
        elevation: 0,
        centerTitle: true,
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
            padding: const EdgeInsets.all(18),
            itemCount: classes.length,
            itemBuilder: (context, index) => _buildClassCard(classes[index]),
          );
        },
      ),
    );
  }

  Widget _buildClassCard(Map item) {
    // Mendeteksi ID dari API (antisipasi jika namanya 'id' atau 'class_id')
    final dynamic rawId = item['class_id'] ?? item['id'];
    int id = int.tryParse(rawId.toString()) ?? 0;

    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(25),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.06), 
            blurRadius: 15,
            offset: const Offset(0, 8),
          )
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // MENAMPILKAN GAMBAR ASSET LOKAL (BUKAN NETWORK)
          ClipRRect(
            borderRadius: const BorderRadius.vertical(top: Radius.circular(25)),
            child: Image.asset(
              _getProgramImage(id), 
              height: 200,
              width: double.infinity,
              fit: BoxFit.cover,
              // Error handling jika file fisik di folder assets hilang
              errorBuilder: (context, error, stackTrace) => Container(
                height: 200,
                color: Colors.grey[200],
                child: const Icon(Icons.broken_image, size: 50, color: Colors.grey),
              ),
            ),
          ),
          
          Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text("OFFICIAL ACADEMY PROGRAM", 
                  style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey, letterSpacing: 1.1)),
                const SizedBox(height: 5),
                Text(
                  item['program_name'] ?? "No Name", 
                  style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)
                ),
                const SizedBox(height: 8),
                Text(
                  item['description'] ?? "Segera bergabung dan raih impianmu.", 
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(color: Colors.grey, fontSize: 13, height: 1.4),
                ),
                const SizedBox(height: 20),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      "Rp ${item['price']}", 
                      style: const TextStyle(color: Color(0xFF990000), fontWeight: FontWeight.bold, fontSize: 18),
                    ),
                    ElevatedButton(
                      onPressed: () {
                        Navigator.push(context, MaterialPageRoute(builder: (_) => ClassDetailPage(
                          classId: id,
                          className: item['program_name'] ?? "",
                          token: widget.token,
                          userData: widget.userData,
                        )));
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFFF1B401),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                        elevation: 0,
                      ),
                      child: const Text("VIEW DETAILS", 
                        style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold, fontSize: 12)),
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