import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';

class MidtransPaymentPage extends StatefulWidget {
  final String url;

  const MidtransPaymentPage({super.key, required this.url});

  @override
  State<MidtransPaymentPage> createState() => _MidtransPaymentPageState();
}

class _MidtransPaymentPageState extends State<MidtransPaymentPage> {
  late final WebViewController controller;
  bool isLoading = true;

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

            // ✨ DETEKSI URL FINISH DARI MIDTRANS
            // Biasanya Midtrans redirect ke URL yang mengandung 'finish' jika sukses
            if (url.contains("finish") || url.contains("status_code=200") || url.contains("transaction_status=settlement")) {
              _handleSuccess();
            }
          },
          onNavigationRequest: (request) {
            final url = request.url;
            debugPrint("🌐 Redirect: $url");

            // ✅ DETEKSI SUCCESS (Redirect Request)
            if (url.contains("success") || 
                url.contains("settlement") || 
                url.contains("finish")) {
              _handleSuccess();
              return NavigationDecision.prevent;
            }

            // ❌ DETEKSI GAGAL
            if (url.contains("failed") ||
                url.contains("error") ||
                url.contains("cancel") ||
                url.contains("status_code=202")) {
              _handleFailed();
              return NavigationDecision.prevent;
            }

            return NavigationDecision.navigate;
          },
        ),
      )
      ..loadRequest(Uri.parse(widget.url));
  }

  /// ✅ HANDLE SUCCESS
  void _handleSuccess() {
    if (!mounted) return;

    // ✨ PENTING: Mengirim 'true' agar halaman sebelumnya tahu pembayaran sukses
    Navigator.pop(context, true); 

    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        backgroundColor: Colors.green,
        content: Text("✅ Pembayaran berhasil! Menyiapkan materi..."),
        duration: Duration(seconds: 3),
      ),
    );
  }

  /// ❌ HANDLE FAILED
  void _handleFailed() {
    if (!mounted) return;

    // Mengirim 'false' atau null
    Navigator.pop(context, false);

    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        backgroundColor: Colors.red,
        content: Text("❌ Pembayaran gagal / dibatalkan"),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Pembayaran Spekta", style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
        backgroundColor: const Color(0xFF990000),
        foregroundColor: Colors.white,
      ),
      body: Stack(
        children: [
          WebViewWidget(controller: controller),

          /// 🔄 LOADING INDICATOR
          if (isLoading)
            const Center(
              child: CircularProgressIndicator(color: Color(0xFF990000)),
            ),
        ],
      ),
    );
  }
}