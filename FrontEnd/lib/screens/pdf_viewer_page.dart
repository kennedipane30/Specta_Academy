import 'package:flutter/material.dart';
import 'package:pdfrx/pdfrx.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/auth_service.dart';

class PdfViewerPage extends StatefulWidget {
  final String pdfUrl;
  final String title;
  final String token; // ✨ Tambahkan parameter token

  const PdfViewerPage({
    super.key,
    required this.pdfUrl,
    required this.title,
    required this.token, // ✨ Wajib diisi
  });

  @override
  State<PdfViewerPage> createState() => _PdfViewerPageState();
}

class _PdfViewerPageState extends State<PdfViewerPage> {
  String? _localPath;
  bool _isLoading = true;
  String? _errorMessage;
  double _progress = 0;
  int _receivedBytes = 0;
  int _totalBytes = 0;
  int _retryCount = 0;

  static const Color spektaRed = Color(0xFF990000);

  @override
  void initState() {
    super.initState();
    _startDownload();
  }

  Future<void> _startDownload() async {
    debugPrint('🔗 pdfUrl yang diterima: "${widget.pdfUrl}"');

    setState(() {
      _isLoading = true;
      _errorMessage = null;
      _progress = 0;
      _receivedBytes = 0;
      _totalBytes = 0;
    });

    // 💡 Mengambil token dari widget (dikirim dari halaman sebelumnya)
    String activeToken = widget.token;

    // Fallback: Jika token dari widget kosong, baru coba ambil dari SharedPreferences
    if (activeToken.isEmpty) {
      final prefs = await SharedPreferences.getInstance();
      activeToken = prefs.getString('token') ?? '';
    }

    debugPrint('🔑 Token digunakan: ${activeToken.isNotEmpty ? "YA (${activeToken.length} chars)" : "TIDAK (KOSONG)"}');

    // 🔥 Gunakan AuthService untuk mengunduh file
    final path = await AuthService.downloadMateri(
      widget.pdfUrl,
      (received, total) {
        if (mounted) {
          setState(() {
            _receivedBytes = received;
            _totalBytes = total;
            if (total > 0) {
              _progress = received / total;
            }
          });
        }
      },
      token: activeToken, // 👈 Kirim token yang sudah dipastikan ada
      maxRetry: 3,
    );

    if (mounted) {
      if (path != null) {
        setState(() {
          _localPath = path;
          _isLoading = false;
        });
        debugPrint('✅ PDF berhasil dimuat: $path');
      } else {
        setState(() {
          _isLoading = false;
          _errorMessage = 'Gagal memuat PDF. Server menolak akses (Unauthorized) atau file tidak ditemukan.\n\n'
              'Saran:\n'
              '1. Pastikan Anda masih dalam keadaan login\n'
              '2. Periksa apakah masa aktif kelas masih berlaku\n'
              '3. Cek koneksi internet Anda';
        });
      }
    }
  }

  Future<void> _retryDownload() async {
    setState(() => _retryCount++);
    await AuthService.clearMateriCache(widget.pdfUrl);
    await _startDownload();
  }

  String _formatBytes(int bytes) {
    if (bytes <= 0) return '0 B';
    if (bytes < 1024) return '$bytes B';
    if (bytes < 1024 * 1024) return '${(bytes / 1024).toStringAsFixed(1)} KB';
    return '${(bytes / (1024 * 1024)).toStringAsFixed(1)} MB';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: Text(
          widget.title,
          style: const TextStyle(
            color: Colors.white,
            fontSize: 14,
            fontWeight: FontWeight.bold,
          ),
          overflow: TextOverflow.ellipsis,
        ),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        elevation: 0,
        actions: [
          if (!_isLoading && _localPath != null)
            IconButton(
              icon: const Icon(Icons.refresh),
              tooltip: 'Muat Ulang',
              onPressed: _retryDownload,
            ),
        ],
      ),
      body: _buildBody(),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 40),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              SizedBox(
                width: 60,
                height: 60,
                child: CircularProgressIndicator(
                  value: _progress > 0 ? _progress : null,
                  color: spektaRed,
                  strokeWidth: 5,
                  backgroundColor: spektaRed.withOpacity(0.1),
                ),
              ),
              const SizedBox(height: 24),
              Text(
                _progress > 0
                    ? '${(_progress * 100).toStringAsFixed(0)}%'
                    : 'Menghubungkan...',
                style: const TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.bold,
                  color: spektaRed,
                ),
              ),
              const SizedBox(height: 12),
              if (_totalBytes > 0)
                Text(
                  '${_formatBytes(_receivedBytes)} / ${_formatBytes(_totalBytes)}',
                  style: TextStyle(fontSize: 13, color: Colors.grey[600]),
                ),
              const SizedBox(height: 20),
              ClipRRect(
                borderRadius: BorderRadius.circular(10),
                child: LinearProgressIndicator(
                  value: _progress > 0 ? _progress : null,
                  minHeight: 8,
                  color: spektaRed,
                  backgroundColor: spektaRed.withOpacity(0.1),
                ),
              ),
              const SizedBox(height: 10),
              Text(
                'Mengunduh materi ke penyimpanan lokal...',
                style: TextStyle(fontSize: 12, color: Colors.grey[500]),
              ),
            ],
          ),
        ),
      );
    }

    if (_errorMessage != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(32),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.error_outline_rounded, size: 64, color: Colors.red),
              const SizedBox(height: 16),
              const Text(
                'Gagal Memuat PDF',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 8),
              Text(
                _errorMessage!,
                textAlign: TextAlign.center,
                style: TextStyle(fontSize: 14, color: Colors.grey[600], height: 1.5),
              ),
              const SizedBox(height: 30),
              ElevatedButton.icon(
                onPressed: _retryDownload,
                icon: const Icon(Icons.refresh_rounded),
                label: const Text('Coba Lagi'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: spektaRed,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(horizontal: 30, vertical: 12),
                ),
              ),
            ],
          ),
        ),
      );
    }

    return PdfViewer.file(
      _localPath!,
      params: const PdfViewerParams(
        maxScale: 4.0,
        enableTextSelection: true,
      ),
    );
  }
}