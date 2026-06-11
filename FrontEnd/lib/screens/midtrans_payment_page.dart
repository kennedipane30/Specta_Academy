import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:path_provider/path_provider.dart';
import 'dart:io';

class MidtransPaymentPage extends StatefulWidget {
  final String url;
  final String orderId; // ✅ WAJIB: order_id dari response getSnapToken
  final String token;   // ✅ WAJIB: token user

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

            // Deteksi URL finish dari Midtrans
            if (url.contains("finish") || 
                url.contains("status_code=200") || 
                url.contains("transaction_status=settlement")) {
              _handleSuccess();
            }
          },
          onNavigationRequest: (request) {
            final url = request.url;
            debugPrint("🌐 Redirect: $url");

            // Deteksi SUCCESS
            if (url.contains("success") || 
                url.contains("settlement") || 
                url.contains("finish")) {
              _handleSuccess();
              return NavigationDecision.prevent;
            }

            // Deteksi GAGAL
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

  /// ✅ HANDLE SUCCESS - PANGGIL API UPDATE PAYMENT
  Future<void> _handleSuccess() async {
    if (isProcessing) return;
    if (!mounted) return;

    setState(() {
      isProcessing = true;
      isLoading = true;
    });

    try {
      // Panggil endpoint manual payment success
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
          // Kembali ke halaman sebelumnya dengan status sukses
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

  /// ❌ HANDLE FAILED
  void _handleFailed() {
    if (!mounted) return;
    _showMessage("❌ Pembayaran gagal / dibatalkan", isError: true);
    Navigator.pop(context, false);
  }

  void _showMessage(String message, {bool isError = false}) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        backgroundColor: isError ? Colors.red : Colors.green,
        content: Text(message),
        duration: const Duration(seconds: 3),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Pembayaran Spekta", 
          style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
        backgroundColor: const Color(0xFF990000),
        foregroundColor: Colors.white,
      ),
      body: Stack(
        children: [
          WebViewWidget(controller: controller),
          if (isLoading)
            const Center(
              child: CircularProgressIndicator(color: Color(0xFF990000)),
            ),
        ],
      ),
    );
  }
}