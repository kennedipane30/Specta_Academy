import 'package:flutter/material.dart';

class DedicatedTutorPage extends StatelessWidget {
  const DedicatedTutorPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Dedicated Tutor", style: TextStyle(fontWeight: FontWeight.w900, color: Colors.white)),
        backgroundColor: const Color(0xFF990000),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(20),
        itemCount: 3,
        itemBuilder: (context, index) => Card(
          margin: const EdgeInsets.only(bottom: 15), // PERBAIKAN: Gunakan .only(bottom: 15)
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
          child: const ListTile(
            leading: CircleAvatar(backgroundColor: Colors.indigo, child: Icon(Icons.person, color: Colors.white)),
            title: Text("Tutor Spesialis", style: TextStyle(fontWeight: FontWeight.bold)),
            subtitle: Text("Tersedia untuk konsultasi"),
            trailing: Icon(Icons.chat_bubble_outline, color: Colors.indigo),
          ),
        ),
      ),
    );
  }
}