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
  final _addressCtrl = TextEditingController(); // Modifikasi: alamat -> address
  final _parentPhoneCtrl = TextEditingController(); // Modifikasi: wa_ortu -> parent_phone
  final _nisnCtrl = TextEditingController();
  final _dobCtrl = TextEditingController();
  final Color spektaRed = const Color(0xFF990000);

  @override void initState() {
    super.initState();
    if (widget.userData['student'] != null) {
      var s = widget.userData['student'];
      // MODIFIKASI: Mengambil data dari key Bahasa Inggris (Model Student baru)
      _parentCtrl.text = s['parent_name'] ?? "";
      _addressCtrl.text = s['address'] ?? "";             // school -> address
      _parentPhoneCtrl.text = s['parent_phone'] ?? "";    // wa_ortu -> parent_phone
      _nisnCtrl.text = s['national_id_number'] ?? "";    // nisn -> national_id_number
      _dobCtrl.text = s['date_of_birth'] ?? "";         // dob -> date_of_birth
    }
  }

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
    if (_parentCtrl.text.isEmpty || _addressCtrl.text.isEmpty || _nisnCtrl.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Please fill all required fields!")));
      return;
    }

    showDialog(context: context, builder: (_) => const Center(child: CircularProgressIndicator(color: Color(0xFF990000))));

    // Mengirim data (Key disamakan dengan request di AuthController Laravel)
    var resp = await AuthService.updateProfile({
      'parent_name': _parentCtrl.text,
      'alamat': _addressCtrl.text,
      'wa_ortu': _parentPhoneCtrl.text,
      'nisn': _nisnCtrl.text,
      'dob': _dobCtrl.text,
    }, widget.token);

    if (!mounted) return;
    Navigator.pop(context); 

    if (resp.statusCode == 200) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.green, content: Text("Profile data successfully updated")));
      Navigator.pop(context, true); 
    } else {
      final err = jsonDecode(resp.body);
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(backgroundColor: Colors.red, content: Text(err['message'])));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Complete Profile Data"), backgroundColor: spektaRed, foregroundColor: Colors.white),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(25),
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(15),
              decoration: BoxDecoration(color: Colors.grey[100], borderRadius: BorderRadius.circular(15)),
              child: Column(children: [
                ListTile(leading: const Icon(Icons.email_outlined), title: const Text("Gmail"), subtitle: Text(widget.userData['email'] ?? "-")),
                const Divider(),
                ListTile(leading: const Icon(Icons.phone_android), title: const Text("WhatsApp Number"), subtitle: Text(widget.userData['phone'] ?? "-")),
              ]),
            ),
            const SizedBox(height: 30),
            
            _buildInput(_nisnCtrl, "Student ID (NISN)", Icons.numbers),
            _buildInput(_parentCtrl, "Parent Name", Icons.person_outline),
            _buildInput(_addressCtrl, "Full Address", Icons.location_on_outlined),
            _buildInput(_parentPhoneCtrl, "Parent WhatsApp Number", Icons.phone),
            
            TextField(
              controller: _dobCtrl, readOnly: true, onTap: _selectDate,
              decoration: const InputDecoration(labelText: "Date of Birth", prefixIcon: Icon(Icons.calendar_month, color: Color(0xFF990000))),
            ),
            
            const SizedBox(height: 50),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: spektaRed, minimumSize: const Size(double.infinity, 55), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
              onPressed: _handleSave,
              child: const Text("SAVE DATA", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
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