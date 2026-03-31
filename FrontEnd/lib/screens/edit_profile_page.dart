import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'package:intl/intl.dart';
import 'dart:convert';

class EditProfilePage extends StatefulWidget {
  final Map userData; 
  final String token;
  const EditProfilePage({super.key, required this.userData, required this.token});

  @override State<EditProfilePage> createState() => _EditProfilePageState();
}

class _EditProfilePageState extends State<EditProfilePage> {
  final _parentCtrl = TextEditingController();
  final _alamatCtrl = TextEditingController();
  final _waOrtuCtrl = TextEditingController();
  final _nisnCtrl = TextEditingController();
  final _dobCtrl = TextEditingController();
  final Color spektaRed = const Color(0xFF990000);

  @override void initState() {
    super.initState();
    // Isi otomatis jika data sudah ada
    if (widget.userData['student'] != null) {
      var s = widget.userData['student'];
      _parentCtrl.text = s['parent_name'] ?? "";
      _alamatCtrl.text = s['school'] ?? "";
      _waOrtuCtrl.text = s['wa_ortu'] ?? "";
      _nisnCtrl.text = s['nisn'] ?? "";
      _dobCtrl.text = s['dob'] ?? "";
    }
  }

  // Fungsi Pilih Tanggal (DatePicker)
  Future<void> _selectDate() async {
    DateTime? picked = await showDatePicker(
      context: context,
      initialDate: DateTime(2005),
      firstDate: DateTime(1990),
      lastDate: DateTime.now(),
    );
    if (picked != null) {
      setState(() => _dobCtrl.text = DateFormat('yyyy-MM-dd').format(picked));
    }
  }

  void _handleSave() async {
    if (_parentCtrl.text.isEmpty || _alamatCtrl.text.isEmpty || _nisnCtrl.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Isi semua data!")));
      return;
    }

    showDialog(context: context, builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000))));

    var resp = await AuthService.updateProfile({
      'parent_name': _parentCtrl.text,
      'alamat': _alamatCtrl.text,
      'wa_ortu': _waOrtuCtrl.text,
      'nisn': _nisnCtrl.text,
      'dob': _dobCtrl.text,
    }, widget.token);

    if (!mounted) return;
    Navigator.pop(context); // Tutup Loading

    if (resp.statusCode == 200) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.green, content: Text("Data diri anda berhasil dilengkapi")));
      Navigator.pop(context, true); // Balik ke halaman Akun
    } else {
      final err = jsonDecode(resp.body);
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(backgroundColor: Colors.red, content: Text(err['message'])));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Lengkapi Data Diri"), backgroundColor: spektaRed, foregroundColor: Colors.white),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(25),
        child: Column(
          children: [
            // INFO AKUN (READ ONLY)
            Container(
              padding: const EdgeInsets.all(15),
              decoration: BoxDecoration(color: Colors.grey[100], borderRadius: BorderRadius.circular(15)),
              child: Column(children: [
                ListTile(leading: const Icon(Icons.email_outlined), title: const Text("Gmail"), subtitle: Text(widget.userData['email'] ?? "-")),
                const Divider(),
                ListTile(leading: const Icon(Icons.phone_android), title: const Text("Nomor WhatsApp"), subtitle: Text(widget.userData['phone'] ?? "-")),
              ]),
            ),
            const SizedBox(height: 30),
            
            // INPUT MANUAL
            _buildInput(_nisnCtrl, "NISN Siswa", Icons.numbers),
            _buildInput(_parentCtrl, "Nama Orang Tua", Icons.person_outline),
            _buildInput(_alamatCtrl, "Alamat Lengkap", Icons.location_on_outlined),
            _buildInput(_waOrtuCtrl, "WhatsApp Orang Tua", Icons.phone),
            
            // Tanggal Lahir (DatePicker)
            TextField(
              controller: _dobCtrl, readOnly: true, onTap: _selectDate,
              decoration: const InputDecoration(labelText: "Tanggal Lahir", prefixIcon: Icon(Icons.calendar_month, color: Color(0xFF990000))),
            ),
            
            const SizedBox(height: 50),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 55), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
              onPressed: _handleSave,
              child: const Text("SIMPAN DATA", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            )
          ],
        ),
      ),
    );
  }

  Widget _buildInput(TextEditingController ctrl, String label, IconData icon) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 15),
      child: TextField(controller: ctrl, decoration: InputDecoration(labelText: label, prefixIcon: Icon(icon, color: spektaRed))),
    );
  }
}