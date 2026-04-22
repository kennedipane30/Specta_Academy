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

      /// 🔥 DETECT PERUBAHAN URL (INI KUNCI UTAMA)
      ..setNavigationDelegate(
        NavigationDelegate(
          onPageStarted: (url) {
            debugPrint("➡️ Loading: $url");
          },
          onPageFinished: (url) {
            debugPrint("✅ Loaded: $url");
            setState(() => isLoading = false);
          },
          onNavigationRequest: (request) {
            final url = request.url;
            debugPrint("🌐 Redirect: $url");

            /// ✅ DETEKSI SUCCESS
            if (url.contains("success") ||
                url.contains("settlement")) {
              _handleSuccess();
              return NavigationDecision.prevent;
            }

            /// ❌ DETEKSI GAGAL
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

  /// ✅ HANDLE SUCCESS
  void _handleSuccess() {
    Navigator.pop(context); // kembali ke halaman sebelumnya

    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        backgroundColor: Colors.green,
        content: Text("✅ Pembayaran berhasil!"),
      ),
    );
  }

  /// ❌ HANDLE FAILED
  void _handleFailed() {
    Navigator.pop(context);

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
      appBar: AppBar(title: const Text("Pembayaran")),
      body: Stack(
        children: [
          WebViewWidget(controller: controller),

          /// 🔄 LOADING
          if (isLoading)
            const Center(child: CircularProgressIndicator()),
        ],
      ),
    );
  }
}