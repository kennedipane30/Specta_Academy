import 'package:flutter/material.dart';

class HubungiKamiPage extends StatelessWidget {
  const HubungiKamiPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Hubungi Kami"), backgroundColor: const Color(0xFF990000)),
      body: ListView(
        padding: const EdgeInsets.all(15),
        children: [
          _buildContactItem(Icons.chat, "WhatsApp", Colors.green),
          _buildContactItem(Icons.camera_alt, "Instagram", Colors.purple),
          _buildContactItem(Icons.video_collection, "TikTok", Colors.black),
          _buildContactItem(Icons.facebook, "Facebook", Colors.blue),
        ],
      ),
    );
  }

  Widget _buildContactItem(IconData icon, String label, Color color) {
    return Card(
      child: ListTile(
        leading: Icon(icon, color: color),
        title: Text(label),
        trailing: const Icon(Icons.arrow_forward_ios, size: 16),
        onTap: () { /* Tambahkan fungsi buka link sosmed di sini */ },
      ),
    );
  }
}