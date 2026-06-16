import 'package:flutter/material.dart';
import 'package:pdf/pdf.dart';
import 'package:pdf/widgets.dart' as pw;
import 'package:printing/printing.dart';

class ExplanationPage extends StatelessWidget {
  final List questions;

  const ExplanationPage({super.key, required this.questions});

  // ============================================================
  // 🎨 PALET WARNA SPEKTA 
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

  // Fungsi untuk menggambar dan mendownload PDF
  Future<void> _generateAndDownloadPdf(BuildContext context) async {
    // 1. Tampilkan loading dialog
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) => const Center(child: CircularProgressIndicator(color: accentTeal)),
    );

    try {
      // 2. Buat dokumen PDF kosong
      final pdf = pw.Document();

      // ✅ SOLUSI ERROR HELVETICA: Muat font Roboto dari Google Fonts
      final fontRegular = await PdfGoogleFonts.robotoRegular();
      final fontBold = await PdfGoogleFonts.robotoBold();

      // 3. Tambahkan halaman
      pdf.addPage(
        pw.MultiPage(
          pageFormat: PdfPageFormat.a4,
          margin: const pw.EdgeInsets.all(32),
          // ✅ Terapkan Font Roboto ke seluruh isi PDF agar Unicode (Simbol/Emoji) terbaca
          theme: pw.ThemeData.withFont(
            base: fontRegular,
            bold: fontBold,
          ),
          build: (pw.Context pdfContext) {
            List<pw.Widget> pdfContent = [];

            // Header Judul Dokumen PDF
            pdfContent.add(
              pw.Header(
                level: 0,
                child: pw.Text("Pembahasan Tryout - Spekta Academy", 
                  style: pw.TextStyle(fontSize: 24, fontWeight: pw.FontWeight.bold, color: PdfColors.teal900)
                ),
              ),
            );
            pdfContent.add(pw.SizedBox(height: 20));

            // Looping seluruh soal
            for (var i = 0; i < questions.length; i++) {
              final q = questions[i];
              String userAnswer = (q['user_answer'] ?? '-').toString().trim().toUpperCase();
              String correctAnswer = (q['correct_answer'] ?? '-').toString().trim().toUpperCase();
              if (userAnswer == "" || userAnswer == "NULL") userAnswer = "-";

              bool isCorrect = (userAnswer != "-") && (userAnswer == correctAnswer);
              bool isUnanswered = userAnswer == "-";

              String statusLabel = isCorrect ? "BENAR" : isUnanswered ? "TIDAK DIJAWAB" : "SALAH";
              PdfColor statusColor = isCorrect ? PdfColors.teal700 : isUnanswered ? PdfColors.grey600 : PdfColors.red700;

              // Blok per 1 Soal di dalam PDF
              pdfContent.add(
                pw.Container(
                  margin: const pw.EdgeInsets.only(bottom: 24),
                  padding: const pw.EdgeInsets.all(16),
                  decoration: pw.BoxDecoration(
                    border: pw.Border.all(color: PdfColors.grey300),
                    borderRadius: const pw.BorderRadius.all(pw.Radius.circular(8)),
                  ),
                  child: pw.Column(
                    crossAxisAlignment: pw.CrossAxisAlignment.start,
                    children: [
                      // Header Soal
                      pw.Row(
                        mainAxisAlignment: pw.MainAxisAlignment.spaceBetween,
                        children: [
                          pw.Text("SOAL #${i + 1}", style: pw.TextStyle(fontWeight: pw.FontWeight.bold, color: PdfColors.red700)),
                          pw.Text(statusLabel, style: pw.TextStyle(fontWeight: pw.FontWeight.bold, color: statusColor)),
                        ],
                      ),
                      pw.Divider(color: PdfColors.grey300),
                      pw.SizedBox(height: 8),

                      // Teks Pertanyaan (Dibersihkan dari null)
                      pw.Text(q['question']?.toString() ?? 'Pertanyaan tidak ditemukan.', style: const pw.TextStyle(fontSize: 12)),
                      pw.SizedBox(height: 12),

                      // Perbandingan Jawaban
                      pw.Container(
                        padding: const pw.EdgeInsets.all(8),
                        decoration: pw.BoxDecoration(color: PdfColors.grey100),
                        child: pw.Row(
                          children: [
                            pw.Expanded(
                              child: pw.Text("Jawaban Kamu: ${userAnswer == '-' ? 'Kosong' : 'Opsi $userAnswer'}", 
                                style: pw.TextStyle(color: isCorrect ? PdfColors.teal700 : PdfColors.red700, fontWeight: pw.FontWeight.bold, fontSize: 10)
                              ),
                            ),
                            pw.Expanded(
                              child: pw.Text("Kunci Jawaban: Opsi $correctAnswer", 
                                style: pw.TextStyle(color: PdfColors.teal700, fontWeight: pw.FontWeight.bold, fontSize: 10)
                              ),
                            ),
                          ],
                        ),
                      ),
                      pw.SizedBox(height: 12),

                      // Penjelasan
                      pw.Text("PENJELASAN:", style: pw.TextStyle(fontWeight: pw.FontWeight.bold, fontSize: 10, color: PdfColors.teal900)),
                      pw.SizedBox(height: 4),
                      pw.Text(
                        q['explanation'] != null && q['explanation'].toString().isNotEmpty
                            ? q['explanation'].toString()
                            : "Tidak ada penjelasan tertulis untuk soal ini.",
                        style: const pw.TextStyle(fontSize: 11),
                      ),
                    ],
                  ),
                ),
              );
            }
            return pdfContent;
          },
        ),
      );

      // 4. Tutup loading dialog
      if (context.mounted) Navigator.pop(context);

      // 5. Panggil fungsi bawaan HP untuk Share / Save File PDF
      await Printing.sharePdf(
        bytes: await pdf.save(), 
        filename: 'Pembahasan_Tryout_Spekta.pdf'
      );

    } catch (e) {
      // ✅ Saya tambahkan Print ini agar jika gagal lagi, error-nya terlihat jelas di terminal
      debugPrint("❌ ERROR GENERATE PDF: $e");
      
      if (context.mounted) {
        Navigator.pop(context); // Tutup loading
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text("Gagal membuat PDF"),
            backgroundColor: primaryRed,
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          )
        );
      }
    }
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
          "Pembahasan Tryout",
          style: TextStyle(
            fontWeight: FontWeight.w900,
            color: Colors.white,
            fontSize: 17,
          ),
        ),
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: questions.isEmpty
          ? _buildEmptyState()
          : ListView.builder(
              padding: const EdgeInsets.fromLTRB(16, 20, 16, 20),
              itemCount: questions.length,
              itemBuilder: (context, index) {
                final q = questions[index];

                String userAnswer =
                    (q['user_answer'] ?? '-').toString().trim().toUpperCase();
                String correctAnswer =
                    (q['correct_answer'] ?? '-').toString().trim().toUpperCase();

                if (userAnswer == "" || userAnswer == "NULL") userAnswer = "-";

                bool isCorrect =
                    (userAnswer != "-") && (userAnswer == correctAnswer);
                bool isUnanswered = userAnswer == "-";

                // Label & warna status
                String statusLabel = isCorrect
                    ? "BENAR"
                    : isUnanswered
                        ? "TIDAK DIJAWAB"
                        : "SALAH";
                Color statusColor = isCorrect
                    ? darkTeal
                    : isUnanswered
                        ? neutralGray
                        : primaryRed;
                Color statusBg = isCorrect
                    ? const Color(0xFFE2F9FC)
                    : isUnanswered
                        ? const Color(0xFFF1F5F9)
                        : const Color(0xFFFFF1F1);

                return Container(
                  margin: const EdgeInsets.only(bottom: 20),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: outlineVariant.withOpacity(0.4)),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.03),
                        blurRadius: 12,
                        offset: const Offset(0, 4),
                      ),
                    ],
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [

                      // ── Header soal ──
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 16, vertical: 12),
                        decoration: BoxDecoration(
                          color: pageBg,
                          borderRadius: const BorderRadius.vertical(
                              top: Radius.circular(20)),
                          border: Border(
                            bottom:
                                BorderSide(color: outlineVariant.withOpacity(0.3)),
                          ),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            // Nomor soal
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 10, vertical: 5),
                              decoration: BoxDecoration(
                                color: lightBlueBg,
                                borderRadius: BorderRadius.circular(8),
                                border: Border.all(
                                    color: outlineVariant.withOpacity(0.3)),
                              ),
                              child: Text(
                                "SOAL #${index + 1}",
                                style: const TextStyle(
                                  fontWeight: FontWeight.w900,
                                  color: primaryRed,
                                  fontSize: 11,
                                  letterSpacing: 0.5,
                                ),
                              ),
                            ),

                            // Badge status
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 10, vertical: 5),
                              decoration: BoxDecoration(
                                color: statusBg,
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: Text(
                                statusLabel,
                                style: TextStyle(
                                  color: statusColor,
                                  fontWeight: FontWeight.w900,
                                  fontSize: 11,
                                  letterSpacing: 0.5,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),

                      Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [

                            // ── Teks pertanyaan ──
                            Text(
                              q['question'] ?? 'Pertanyaan tidak ditemukan.',
                              style: const TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.w700,
                                height: 1.6,
                                color: textDark,
                              ),
                            ),
                            const SizedBox(height: 16),

                            // ── Jawaban kamu & kunci ──
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 14, vertical: 12),
                              decoration: BoxDecoration(
                                color: pageBg,
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(
                                    color: outlineVariant.withOpacity(0.4)),
                              ),
                              child: Row(
                                children: [
                                  _buildAnswerCircle(
                                    "Jawaban Kamu",
                                    userAnswer,
                                    isCorrect ? darkTeal : primaryRed,
                                  ),
                                  Container(
                                    width: 1,
                                    height: 40,
                                    color: outlineVariant.withOpacity(0.5),
                                    margin: const EdgeInsets.symmetric(
                                        horizontal: 14),
                                  ),
                                  _buildAnswerCircle(
                                    "Kunci Jawaban",
                                    correctAnswer,
                                    accentTeal,
                                  ),
                                ],
                              ),
                            ),
                            const SizedBox(height: 14),

                            // ── Panel pembahasan ──
                            Container(
                              width: double.infinity,
                              padding: const EdgeInsets.all(16),
                              decoration: BoxDecoration(
                                color: const Color(0xFFE2F9FC),
                                borderRadius: BorderRadius.circular(14),
                                border: Border.all(
                                    color: darkTeal.withOpacity(0.2)),
                              ),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    children: [
                                      const Icon(Icons.auto_awesome_rounded,
                                          size: 14, color: darkTeal),
                                      const SizedBox(width: 8),
                                      const Text(
                                        "PENJELASAN",
                                        style: TextStyle(
                                          fontWeight: FontWeight.w900,
                                          fontSize: 11,
                                          color: darkTeal,
                                          letterSpacing: 0.8,
                                        ),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 10),
                                  Text(
                                    q['explanation'] != null &&
                                            q['explanation']
                                                .toString()
                                                .isNotEmpty
                                        ? q['explanation']
                                        : "Tidak ada penjelasan tertulis untuk soal ini.",
                                    style: const TextStyle(
                                      fontSize: 13,
                                      height: 1.6,
                                      color: textDark,
                                      fontWeight: FontWeight.w500,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                );
              },
            ),

      // ── Bottom bar menjadi 2 Tombol ──
      bottomNavigationBar: Container(
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 28),
        decoration: BoxDecoration(
          color: Colors.white,
          border: Border(top: BorderSide(color: outlineVariant.withOpacity(0.4))),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.04),
              blurRadius: 10,
              offset: const Offset(0, -4),
            ),
          ],
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min, 
          children: [
            // TOMBOL 1: DOWNLOAD PDF
            OutlinedButton.icon(
              onPressed: () => _generateAndDownloadPdf(context),
              icon: const Icon(Icons.picture_as_pdf_rounded, color: darkTeal),
              label: const Text(
                "DOWNLOAD PEMBAHASAN (PDF)",
                style: TextStyle(
                  color: darkTeal,
                  fontWeight: FontWeight.w900,
                  fontSize: 14,
                  letterSpacing: 0.8,
                ),
              ),
              style: OutlinedButton.styleFrom(
                minimumSize: const Size(double.infinity, 52),
                side: const BorderSide(color: darkTeal, width: 2),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
              ),
            ),
            const SizedBox(height: 12),

            // TOMBOL 2: KEMBALI KE BERANDA 
            ElevatedButton(
              onPressed: () => Navigator.of(context).popUntil((route) => route.isFirst),
              style: ElevatedButton.styleFrom(
                backgroundColor: accentTeal,
                minimumSize: const Size(double.infinity, 52),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                elevation: 0,
              ),
              child: const Text(
                "KEMBALI KE BERANDA",
                style: TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.w900,
                  fontSize: 14,
                  letterSpacing: 0.8,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildAnswerCircle(String label, String value, Color color) {
    return Expanded(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            label,
            style: const TextStyle(
              fontSize: 10,
              color: neutralGray,
              fontWeight: FontWeight.w800,
              letterSpacing: 0.3,
            ),
          ),
          const SizedBox(height: 6),
          Row(
            children: [
              Container(
                width: 34,
                height: 34,
                decoration: BoxDecoration(
                  color: color,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Center(
                  child: Text(
                    value,
                    style: const TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.w900,
                      fontSize: 14,
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 10),
              Text(
                value == "-" ? "Kosong" : "Opsi $value",
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w700,
                  color: color,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(24),
            decoration: const BoxDecoration(
              color: lightBlueBg,
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.find_in_page_outlined,
              size: 52,
              color: primaryRed,
            ),
          ),
          const SizedBox(height: 16),
          const Text(
            "Data pembahasan tidak ditemukan",
            style: TextStyle(
              fontWeight: FontWeight.bold,
              color: textDarkVariant,
              fontSize: 14,
            ),
          ),
        ],
      ),
    );
  }
}