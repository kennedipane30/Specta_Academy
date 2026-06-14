import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:path_provider/path_provider.dart';
import 'dart:io';

class MidtransPaymentPage extends StatefulWidget {
  final String url;
  final String orderId;
  final String token;

  const MidtransPaymentPage({
    super.key, 
    required this.url,
    required this.orderId,
    required this.token,
  });

  @override
  State<MidtransPaymentPage> createState() => _MidtransPaymentPageState();
}

class _MidtransPaymentPageState extends State<MidtransPaymentPage> {
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

  late final WebViewController controller;
  bool isLoading = true;
  bool isProcessing = false;

  @override
  void initState() {
    super.initState();

    controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setNavigationDelegate(
        NavigationDelegate(
          onPageStarted: (url) {
            debugPrint("➡️ Loading: $url");
            setState(() => isLoading = true);
          },
          onPageFinished: (url) {
            debugPrint("✅ Loaded: $url");
            setState(() => isLoading = false);

            if (url.contains("finish") || 
                url.contains("status_code=200") || 
                url.contains("transaction_status=settlement")) {
              _handleSuccess();
            }
          },
          onNavigationRequest: (request) {
            final url = request.url;
            debugPrint("🌐 Redirect: $url");

            if (url.contains("success") || 
                url.contains("settlement") || 
                url.contains("finish")) {
              _handleSuccess();
              return NavigationDecision.prevent;
            }

            if (url.contains("failed") ||
                url.contains("error") ||
                url.contains("cancel")) {
              _handleFailed();
              return NavigationDecision.prevent;
            }

            return NavigationDecision.navigate;
          },
        ),
      )
      ..loadRequest(Uri.parse(widget.url));
  }

  Future<void> _handleSuccess() async {
    if (isProcessing) return;
    if (!mounted) return;

    setState(() {
      isProcessing = true;
      isLoading = true;
    });

    try {
      final response = await http.post(
        Uri.parse('http://10.0.2.2:8000/api/payment/manual-success'),
        headers: {
          'Authorization': 'Bearer ${widget.token}',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'order_id': widget.orderId,
        }),
      );

      debugPrint("📡 manual-success: ${response.statusCode}");
      debugPrint("📡 Body: ${response.body}");

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          _showMessage("✅ Pembayaran berhasil! Kelas sudah aktif.", isError: false);
          Navigator.pop(context, true);
        } else {
          _showMessage("⚠️ ${data['message']}", isError: true);
          Navigator.pop(context, false);
        }
      } else {
        _showMessage("❌ Gagal mengupdate status pembayaran", isError: true);
        Navigator.pop(context, false);
      }

    } catch (e) {
      debugPrint("❌ Error: $e");
      _showMessage("❌ Error koneksi: $e", isError: true);
      Navigator.pop(context, false);
    } finally {
      if (mounted) {
        setState(() {
          isProcessing = false;
          isLoading = false;
        });
      }
    }
  }

  void _handleFailed() {
    if (!mounted) return;
    _showMessage("❌ Pembayaran gagal / dibatalkan", isError: true);
    Navigator.pop(context, false);
  }

  void _showMessage(String message, {bool isError = false}) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        backgroundColor: isError ? primaryRed : darkTeal,
        content: Text(message, style: const TextStyle(fontWeight: FontWeight.bold)),
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        duration: const Duration(seconds: 3),
      ),
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
        title: const Text(
          "Pembayaran Spekta", 
          style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.white)
        ),
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
      ),
      body: Stack(
        children: [
          WebViewWidget(controller: controller),
          if (isLoading)
            const Center(
              child: CircularProgressIndicator(color: accentTeal),
            ),
        ],
      ),
    );
  }
}