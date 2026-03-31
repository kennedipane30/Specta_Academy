import 'package:flutter/material.dart';
import 'edit_profile_page.dart';
import 'login_page.dart';

class AkunPage extends StatelessWidget {
  final String token;
  final Map userData; // Data profil dari Laravel

  const AkunPage({
    super.key, 
    required this.token, 
    required this.userData
  });

  @override
  Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);
    
    // Ambil data spesifik student dari Map userData
    var student = userData['student'];

    return Scaffold(
      body: Column(
        children: [
          // Header Profil Merah
          Container(
            height: 280,
            width: double.infinity,
            decoration: const BoxDecoration(
              color: spektaRed,
              borderRadius: BorderRadius.only(bottomLeft: Radius.circular(50), bottomRight: Radius.circular(50)),
            ),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const CircleAvatar(
                  radius: 50,
                  backgroundColor: Colors.white,
                  child: Icon(Icons.person, size: 60, color: spektaRed),
                ),
                const SizedBox(height: 15),
                Text(
                  userData['name'] ?? "Siswa Spekta",
                  style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold),
                ),
                Text(
                  userData['email'] ?? "",
                  style: const TextStyle(color: Colors.white70, fontSize: 14),
                ),
              ],
            ),
          ),

          const SizedBox(height: 20),

          // Tombol Lengkapi Profil
          ListTile(
            leading: const Icon(Icons.edit_note, color: spektaRed),
            title: const Text("Lengkapi Data Diri", style: TextStyle(fontWeight: FontWeight.bold)),
            subtitle: const Text("Nama Ortu, Alamat, WA Ortu"),
            trailing: const Icon(Icons.arrow_forward_ios, size: 16),
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => EditProfilePage(userData: userData, token: token),
                ),
              );
            },
          ),

          const Divider(),

          ListTile(
            leading: const Icon(Icons.phone_android, color: spektaRed),
            title: const Text("Nomor WhatsApp"),
            subtitle: Text(userData['phone'] ?? "-"),
          ),

          const Spacer(),

          // Tombol Logout
          Padding(
            padding: const EdgeInsets.all(25),
            child: ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: spektaRed,
                minimumSize: const Size(double.infinity, 55),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
              ),
              onPressed: () {
                // Balik ke Login
                Navigator.pushAndRemoveUntil(
                  context,
                  MaterialPageRoute(builder: (context) => const LoginPage()),
                  (route) => false,
                );
              },
              child: const Text("KELUAR AKUN", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ),
          const SizedBox(height: 10),
        ],
      ),
    );
  }
}