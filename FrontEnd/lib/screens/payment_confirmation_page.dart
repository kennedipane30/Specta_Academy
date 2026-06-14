import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'midtrans_payment_page.dart';
import '../../services/auth_service.dart';

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
  // ============================================================
  // 🎨 PALET WARNA SPEKTA (KONSISTEN DENGAN TRYOUTDETAILPAGE)
  // ============================================================
  static const Color primaryRed      = Color(0xFFC5352C);
  static const Color accentTeal      = Color(0xFF2EA8AB);
  static const Color darkTeal        = Color(0xFF00696C);
  static const Color lightBlueBg     = Color(0xFFEFF4FF);
  static const Color pageBg          = Color(0xFFF1F5F9);
  static const Color textDark        = Color(0xFF0F172A);
  static const Color textDarkVariant = Color(0xFF334155);
  static const Color neutralGray     = Color(0xFF64748B);
  static const Color outlineVariant  = Color(0xFFE2BEBA);

  final TextEditingController _promoController = TextEditingController();
  final currency = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

  int discountAmount = 0;
  int finalPrice = 0;
  String? appliedPromoCode;
  bool isChecking = false;

  @override
  void initState() {
    super.initState();
    finalPrice = widget.basePrice;
  }

  Future<void> _checkPromo() async {
    String inputCode = _promoController.text.trim().toUpperCase();
    if (inputCode.isEmpty) return;

    FocusScope.of(context).unfocus();
    setState(() => isChecking = true);

    try {
      final response = await http.post(
        Uri.parse("http://10.0.2.2:8000/api/promo/check"),
        headers: {
          'Authorization': 'Bearer ${widget.token}',
          'Accept': 'application/json'
        },
        body: {
          "code": inputCode,
          "class_id": widget.classId.toString(),
        },
      );

      var data = jsonDecode(response.body);

      if (response.statusCode == 200) {
        setState(() {
          appliedPromoCode = inputCode;
          discountAmount = int.tryParse(data['discount_amount'].toString()) ?? 0;
          finalPrice = int.tryParse(data['final_price'].toString()) ?? widget.basePrice;
        });

        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: darkTeal, 
            content: const Text("✅ Kode Promo Berhasil Digunakan!"),
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          )
        );
      } else {
        setState(() {
          appliedPromoCode = null;
          discountAmount = 0;
          finalPrice = widget.basePrice;
        });
        _showError(data['message'] ?? "Kode promo tidak berlaku.");
      }
    } catch (e) {
      _showError("Gagal terhubung ke server. Periksa koneksi internet.");
    } finally {
      setState(() => isChecking = false);
    }
  }

  Future<void> _processPayment() async {
    showDialog(
      context: context, 
      barrierDismissible: false, 
      builder: (_) => const Center(child: CircularProgressIndicator(color: accentTeal))
    );

    try {
      final result = await AuthService.getSnapToken(
        classId: widget.classId,
        token: widget.token,
        promoCode: appliedPromoCode,
      );

      if (mounted) Navigator.pop(context);

      if (result != null && result['status'] == 'success') {
        String snapUrl = result['snap_url'];
        String orderId = result['order_id'];
        
        final paymentResult = await Navigator.push(
          context, 
          MaterialPageRoute(
            builder: (_) => MidtransPaymentPage(
              url: snapUrl,
              orderId: orderId,
              token: widget.token,
            )
          )
        );

        if (paymentResult == true) {
          if (mounted) Navigator.pop(context, true);
        }
      } else {
        _showError(result?['message'] ?? "Gagal memproses pembayaran");
      }
    } catch (e) {
      if (mounted) Navigator.pop(context);
      _showError("Terjadi kesalahan teknis: $e");
    }
  }

  void _showError(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        backgroundColor: primaryRed, 
        content: Text(msg, style: const TextStyle(fontWeight: FontWeight.bold)), 
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      )
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: pageBg,
      appBar: AppBar(
        flexibleSpace: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [primaryRed, accentTeal],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
        ),
        elevation: 0,
        title: const Text(
          "Konfirmasi Pembayaran", 
          style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)
        ),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white), 
          onPressed: () => Navigator.pop(context)
        ),
        centerTitle: true,
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              "Rincian Pendaftaran", 
              style: TextStyle(fontWeight: FontWeight.bold, color: neutralGray, fontSize: 12)
            ),
            const SizedBox(height: 16),
            
            Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(25),
                border: Border.all(color: outlineVariant.withOpacity(0.4)),
                boxShadow: [
                  BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 20, offset: const Offset(0, 10))
                ],
              ),
              child: Column(
                children: [
                  _buildRowItem("Program", widget.className),
                  _buildRowItem("Harga Normal", currency.format(widget.basePrice)),
                  
                  if (discountAmount > 0)
                    _buildRowItem(
                      "Potongan Promo (${appliedPromoCode})", 
                      "- ${currency.format(discountAmount)}", 
                      color: darkTeal
                    ),
                  
                  const Padding(padding: EdgeInsets.symmetric(vertical: 16), child: Divider(color: outlineVariant)),
                  
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text("TOTAL BAYAR", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: neutralGray)),
                      Text(
                        currency.format(finalPrice), 
                        style: TextStyle(fontWeight: FontWeight.bold, fontSize: 24, color: primaryRed)
                      ),
                    ],
                  )
                ],
              ),
            ),

            const SizedBox(height: 35),
            const Text("Gunakan Kode Promo", style: TextStyle(fontWeight: FontWeight.bold, color: neutralGray, fontSize: 12)),
            const SizedBox(height: 12),
            
            Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _promoController,
                    textCapitalization: TextCapitalization.characters,
                    style: const TextStyle(color: textDark, fontWeight: FontWeight.w600),
                    decoration: InputDecoration(
                      hintText: "Masukkan Kode",
                      hintStyle: TextStyle(color: neutralGray),
                      filled: true,
                      fillColor: Colors.white,
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(15), 
                        borderSide: BorderSide(color: outlineVariant)
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(15), 
                        borderSide: BorderSide(color: outlineVariant)
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(15), 
                        borderSide: const BorderSide(color: accentTeal, width: 1.5)
                      ),
                      contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 18),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                SizedBox(
                  height: 56,
                  child: ElevatedButton(
                    onPressed: isChecking ? null : _checkPromo,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: accentTeal, 
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)), 
                      padding: const EdgeInsets.symmetric(horizontal: 24),
                      elevation: 0,
                    ),
                    child: isChecking 
                      ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)) 
                      : const Text("CEK", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                  ),
                )
              ],
            ),

            const SizedBox(height: 50),
            
            SizedBox(
              width: double.infinity,
              height: 58,
              child: ElevatedButton(
                onPressed: _processPayment,
                style: ElevatedButton.styleFrom(
                  backgroundColor: accentTeal, 
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                  elevation: 8,
                  shadowColor: accentTeal.withOpacity(0.4),
                ),
                child: const Text(
                  "BAYAR SEKARANG", 
                  style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)
                ),
              ),
            )
          ],
        ),
      ),
    );
  }

  Widget _buildRowItem(String label, String value, {Color color = textDark}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Expanded(child: Text(label, style: TextStyle(color: neutralGray, fontSize: 13, fontWeight: FontWeight.w500))),
          Text(value, style: TextStyle(fontWeight: FontWeight.bold, color: color, fontSize: 14)),
        ],
      ),
    );
  }
}