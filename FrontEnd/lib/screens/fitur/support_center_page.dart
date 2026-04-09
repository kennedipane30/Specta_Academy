import 'package:flutter/material.dart';

class SupportCenterPage extends StatelessWidget {
  const SupportCenterPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Pusat Bantuan", style: TextStyle(fontWeight: FontWeight.w900, color: Colors.white)),
        backgroundColor: const Color(0xFF990000),
      ),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          const Text("Pertanyaan Populer", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
          const SizedBox(height: 20),
          _buildFaqItem("Bagaimana cara akses tryout?"),
          _buildFaqItem("Cara download materi PDF?"),
          _buildFaqItem("Lupa kata sandi akun?"),
          const SizedBox(height: 30),
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(color: Colors.blueGrey.shade50, borderRadius: BorderRadius.circular(20)),
            child: const Row(
              children: [
                Icon(Icons.headset_mic_rounded, size: 40, color: Colors.blueGrey),
                SizedBox(width: 15),
                Expanded(child: Text("Masih butuh bantuan? Chat Customer Service kami.", style: TextStyle(fontWeight: FontWeight.bold))),
              ],
            ),
          )
        ],
      ),
    );
  }

  Widget _buildFaqItem(String title) {
    return Card(
      margin: const EdgeInsets.only(bottom: 10),
      child: ListTile(
        title: Text(title, style: const TextStyle(fontSize: 14)),
        trailing: const Icon(Icons.chevron_right),
      ),
    );
  }
}