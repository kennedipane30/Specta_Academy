import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'midtrans_payment_page.dart';

class PaymentConfirmationPage extends StatefulWidget {
  final int classId;
  final String className;
  final int basePrice;
  final String token;
  final Map userData;

  const PaymentConfirmationPage({
    super.key,
    required this.classId,
    required this.className,
    required this.basePrice,
    required this.token,
    required this.userData,
  });

  @override
  State<PaymentConfirmationPage> createState() => _PaymentConfirmationPageState();
}

class _PaymentConfirmationPageState extends State<PaymentConfirmationPage> {
  final TextEditingController _promoController = TextEditingController();
  final currency = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

  int discountAmount = 0;
  int finalPrice = 0;
  String? appliedPromoCode;
  bool isChecking = false;

  final Color spektaRed = const Color(0xFF990000);

  @override
  void initState() {
    super.initState();
    finalPrice = widget.basePrice;
  }

  // --- FUNGSI CEK PROMO ---
  Future<void> _checkPromo() async {
    if (_promoController.text.isEmpty) return;
    setState(() => isChecking = true);

    try {
      final response = await http.post(
        Uri.parse("http://10.0.2.2:8000/api/promo/check"),
        headers: {
          'Authorization': 'Bearer ${widget.token}',
          'Accept': 'application/json'
        },
        body: {
          "code": _promoController.text.trim(),
          "class_id": widget.classId.toString(),
        },
      );

      print("Promo Status: ${response.statusCode}");
      print("Promo Data: ${response.body}");

      var data = jsonDecode(response.body);
      if (response.statusCode == 200) {
        setState(() {
          appliedPromoCode = _promoController.text.trim();
          discountAmount = data['discount_amount'];
          finalPrice = data['final_price'];
        });
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.green, content: Text("Promo Berhasil Dipasang")));
      } else {
        setState(() {
          appliedPromoCode = null;
          discountAmount = 0;
          finalPrice = widget.basePrice;
        });
        _showError(data['message'] ?? "Promo tidak valid");
      }
    } catch (e) {
      _showError("Koneksi gagal");
    } finally {
      setState(() => isChecking = false);
    }
  }

  // --- FUNGSI PROSES PEMBAYARAN ---
  Future<void> _processPayment() async {
    showDialog(context: context, barrierDismissible: false, builder: (_) => const Center(child: CircularProgressIndicator()));

    try {
      final response = await http.post(
        Uri.parse("http://10.0.2.2:8000/api/payment/snap-token"),
        headers: {
          'Authorization': 'Bearer ${widget.token}',
          'Accept': 'application/json'
        },
        body: {
          "class_id": widget.classId.toString(),
          "promo_code": appliedPromoCode ?? "", // Mengirim kode promo yang valid
        },
      );

      if (mounted) Navigator.pop(context); // Tutup loading

      // DEBUGGING: Cek hasil dari server di terminal VS Code
      print("Payment Status: ${response.statusCode}");
      print("Payment Body: ${response.body}");

      if (response.statusCode == 200) {
        var data = jsonDecode(response.body);
        String snapUrl = data['snap_url'];
        
        Navigator.push(
          context, 
          MaterialPageRoute(builder: (_) => MidtransPaymentPage(url: snapUrl))
        );
      } else {
        var errorData = jsonDecode(response.body);
        _showError(errorData['message'] ?? "Gagal mendapatkan token pembayaran");
      }
    } catch (e) {
      if (mounted) Navigator.pop(context);
      print("Error Exception: $e");
      _showError("Terjadi kesalahan koneksi ke server.");
    }
  }

  void _showError(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(backgroundColor: Colors.red, content: Text(msg)));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: spektaRed,
        elevation: 0,
        title: const Text("Konfirmasi Pembayaran", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
        leading: IconButton(icon: const Icon(Icons.arrow_back, color: Colors.white), onPressed: () => Navigator.pop(context)),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text("Rincian Pendaftaran", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey, fontSize: 12, letterSpacing: 1)),
            const SizedBox(height: 16),
            
            // CARD RINCIAN (UI Premium)
            Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: Colors.red.shade50),
                boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 20, offset: const Offset(0, 10))],
              ),
              child: Column(
                children: [
                  _buildRowItem("Program", widget.className),
                  _buildRowItem("Harga Normal", currency.format(widget.basePrice)),
                  _buildRowItem("Total Potongan", "- ${currency.format(discountAmount)}", color: Colors.green),
                  const Padding(
                    padding: EdgeInsets.symmetric(vertical: 16),
                    child: Divider(),
                  ),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text("TOTAL BAYAR", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: Colors.grey, letterSpacing: 1)),
                      Text(currency.format(finalPrice), style: TextStyle(fontWeight: FontWeight.bold, fontSize: 24, color: spektaRed)),
                    ],
                  )
                ],
              ),
            ),

            const SizedBox(height: 32),
            const Text("Gunakan Kode Promo", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey, fontSize: 12, letterSpacing: 1)),
            const SizedBox(height: 12),
            
            // INPUT PROMO
            Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _promoController,
                    textCapitalization: TextCapitalization.characters,
                    decoration: InputDecoration(
                      hintText: "Contoh: SPEKTA50",
                      filled: true,
                      fillColor: Colors.grey.shade50,
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide.none),
                      contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 18),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                SizedBox(
                  height: 56,
                  child: ElevatedButton(
                    onPressed: isChecking ? null : _checkPromo,
                    style: ElevatedButton.styleFrom(backgroundColor: Colors.black, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)), padding: const EdgeInsets.symmetric(horizontal: 24)),
                    child: isChecking 
                      ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)) 
                      : const Text("CEK", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                  ),
                )
              ],
            ),

            const SizedBox(height: 48),
            
            // TOMBOL BAYAR SEKARANG
            SizedBox(
              width: double.infinity,
              height: 56,
              child: ElevatedButton(
                onPressed: _processPayment,
                style: ElevatedButton.styleFrom(
                  backgroundColor: spektaRed, 
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                  elevation: 8,
                  shadowColor: spektaRed.withOpacity(0.3)
                ),
                child: const Text("BAYAR SEKARANG", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
              ),
            )
          ],
        ),
      ),
    );
  }

  Widget _buildRowItem(String label, String value, {Color color = Colors.black}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.grey, fontSize: 13, fontWeight: FontWeight.w500)),
          Text(value, style: TextStyle(fontWeight: FontWeight.bold, color: color, fontSize: 14)),
        ],
      ),
    );
  }
}