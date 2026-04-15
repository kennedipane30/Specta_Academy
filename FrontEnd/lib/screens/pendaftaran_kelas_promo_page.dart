import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:io';
import 'dart:convert';
import 'package:http/http.dart' as http;
import '../services/auth_service.dart';

class PendaftaranKelasPromoPage extends StatefulWidget {
  final int classId;
  final String className;
  final String token;
  final Map userData;

  const PendaftaranKelasPromoPage({
    super.key, 
    required this.classId, 
    required this.className, 
    required this.token, 
    required this.userData
  });

  @override State<PendaftaranKelasPromoPage> createState() => _PendaftaranKelasPromoPageState();
}

class _PendaftaranKelasPromoPageState extends State<PendaftaranKelasPromoPage> {
  final TextEditingController _promoCtrl = TextEditingController();
  File? _imageFile;
  int _price = 900000; 
  bool _isUnlocked = false;
  final Color spektaRed = const Color(0xFF990000);
  final Color promoOrange = Colors.orange.shade900;

  void _checkVoucher() async {
    if (_promoCtrl.text.isEmpty) return;

    showDialog(context: context, barrierDismissible: false, builder: (_) => const Center(child: CircularProgressIndicator()));

    var resp = await AuthService.checkPromoCode(_promoCtrl.text, widget.classId, 900000, widget.token);
    
    if (!mounted) return;
    Navigator.pop(context); 

    if (resp.statusCode == 200) {
      var data = jsonDecode(resp.body);
      setState(() { 
        _price = data['new_price']; 
        _isUnlocked = true; 
      });
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(backgroundColor: Colors.green, content: Text("🎉 Promo Code Activated!"))
      );
    } else {
      final errorData = jsonDecode(resp.body);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(backgroundColor: Colors.red, content: Text(errorData['message'] ?? "Invalid Promo Code!"))
      );
    }
  }

  void _submitFinal() async {
    if (_imageFile == null) return;
    showDialog(context: context, barrierDismissible: false, builder: (_) => const Center(child: CircularProgressIndicator(color: Colors.white)));
    
    var streamedResp = await AuthService.joinClassPromo(
      classId: widget.classId,
      promoCode: _promoCtrl.text,
      filePath: _imageFile!.path,
      token: widget.token,
    );

    var response = await http.Response.fromStream(streamedResp);
    
    if (!mounted) return;
    Navigator.pop(context); 

    if (response.statusCode == 200) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(backgroundColor: Colors.green, content: Text("PROMO Enrollment Successful!")));
      Navigator.pop(context);
    }
  }

  @override Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text("PROMO Enrollment Path", style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)), 
        backgroundColor: promoOrange, 
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(25),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text("Your Selected Program:", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)),
            const SizedBox(height: 10),
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: spektaRed,
                borderRadius: BorderRadius.circular(20),
                boxShadow: [BoxShadow(color: spektaRed.withOpacity(0.3), blurRadius: 10, offset: const Offset(0, 5))],
              ),
              child: Row(
                children: [
                  const Icon(Icons.stars, color: Colors.yellow, size: 30),
                  const SizedBox(width: 15),
                  Expanded(
                    child: Text(
                      widget.className.toUpperCase(),
                      style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.w900, letterSpacing: 1),
                    ),
                  ),
                ],
              ),
            ),
            
            const SizedBox(height: 30),
            const Text("Enter Secret Promo Code:", style: TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            TextField(
              controller: _promoCtrl,
              readOnly: _isUnlocked,
              decoration: InputDecoration(
                hintText: "Example: SPEKTAVIP77",
                prefixIcon: Icon(Icons.vpn_key_outlined, color: promoOrange),
                suffixIcon: IconButton(
                  icon: Icon(_isUnlocked ? Icons.verified : Icons.send_rounded, color: _isUnlocked ? Colors.green : promoOrange),
                  onPressed: _isUnlocked ? null : _checkVoucher,
                ),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(15)),
              ),
            ),

            const SizedBox(height: 30),

            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.grey[100],
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: _isUnlocked ? Colors.green : Colors.grey.shade300, width: 2)
              ),
              child: Column(
                children: [
                  const Text("TOTAL PAYMENT BILL", style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.grey)),
                  const SizedBox(height: 5),
                  Text(
                    "Rp $_price", 
                    style: TextStyle(fontSize: 32, fontWeight: FontWeight.w900, color: _isUnlocked ? Colors.green : Colors.black87)
                  ),
                  if (_isUnlocked) const Text("Discount Applied! ✅", style: TextStyle(color: Colors.green, fontSize: 12, fontWeight: FontWeight.bold)),
                ],
              ),
            ),

            if (_isUnlocked) ...[
              const SizedBox(height: 40),
              const Text("Upload Your Payment Proof:", style: TextStyle(fontWeight: FontWeight.bold)),
              const SizedBox(height: 15),
              InkWell(
                onTap: () async {
                  final picked = await ImagePicker().pickImage(source: ImageSource.gallery);
                  if (picked != null) setState(() => _imageFile = File(picked.path));
                },
                child: Container(
                  height: 180, width: double.infinity,
                  decoration: BoxDecoration(
                    color: Colors.grey[50],
                    border: Border.all(color: Colors.grey.shade300),
                    borderRadius: BorderRadius.circular(20)
                  ),
                  child: _imageFile == null 
                    ? Column(mainAxisAlignment: MainAxisAlignment.center, children: [Icon(Icons.add_a_photo_outlined, size: 50, color: promoOrange), const Text("Click to select receipt photo", style: TextStyle(fontSize: 12, color: Colors.grey))])
                    : ClipRRect(borderRadius: BorderRadius.circular(20), child: Image.file(_imageFile!, fit: BoxFit.cover)),
                ),
              ),
              const SizedBox(height: 30),
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: spektaRed, 
                  minimumSize: const Size(double.infinity, 60),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))
                ),
                onPressed: _imageFile == null ? null : _submitFinal,
                child: const Text("CONFIRM PROMO ENROLLMENT", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              ),
              const SizedBox(height: 50),
            ]
          ],
        ),
      ),
    );
  }
}