import 'package:flutter/material.dart';
import 'tentang_spekta_page.dart';
import 'info_program_page.dart';
import 'hubungi_kami_page.dart';

class SemuaFiturPage extends StatelessWidget {
  const SemuaFiturPage({super.key});

  @override
  Widget build(BuildContext context) {
    final List<Map<String, dynamic>> allMenus = [
      {'title': 'Tentang Spekta', 'icon': Icons.info_outline, 'color': Colors.blue, 'page': const TentangSpektaPage()},
      {'title': 'Abdi Negara', 'icon': Icons.security, 'color': Colors.red, 'page': const InfoProgramPage(title: 'Abdi Negara')},
      {'title': 'PTN / UNHAN', 'icon': Icons.school, 'color': Colors.orange, 'page': const InfoProgramPage(title: 'PTN / UNHAN')},
      {'title': 'SMA Favorit', 'icon': Icons.star_outline, 'color': Colors.purple, 'page': const InfoProgramPage(title: 'SMA Favorit')},
      {'title': 'SMA/SMP Reguler', 'icon': Icons.book_outlined, 'color': Colors.green, 'page': const InfoProgramPage(title: 'SMP/SMA Reguler')},
      {'title': 'Tenaga Pengajar', 'icon': Icons.people, 'color': Colors.teal, 'page': const InfoProgramPage(title: 'Tenaga Pengajar')},
      {'title': 'Alumni', 'icon': Icons.history_edu, 'color': Colors.brown, 'page': const InfoProgramPage(title: 'Alumni')},
      {'title': 'Hubungi Kami', 'icon': Icons.headset_mic, 'color': Colors.blueGrey, 'page': const HubungiKamiPage()},
    ];

    return Scaffold(
      appBar: AppBar(title: const Text("Semua Fitur"), backgroundColor: const Color(0xFF990000)),
      body: GridView.builder(
        padding: const EdgeInsets.all(20),
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 3, 
          mainAxisSpacing: 25, 
          crossAxisSpacing: 10
        ),
        itemCount: allMenus.length,
        itemBuilder: (context, index) {
          var item = allMenus[index];
          return InkWell(
            onTap: () => Navigator.push(context, MaterialPageRoute(builder: (c) => item['page'])),
            child: Column(
              children: [
                CircleAvatar(
                  radius: 30,
                  backgroundColor: item['color'].withOpacity(0.1),
                  child: Icon(item['icon'], color: item['color']),
                ),
                const SizedBox(height: 8),
                Text(item['title'], textAlign: TextAlign.center, style: const TextStyle(fontSize: 11)),
              ],
            ),
          );
        },
      ),
    );
  }
}