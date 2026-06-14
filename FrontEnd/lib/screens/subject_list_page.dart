import 'package:flutter/material.dart';
import 'module_week_list_page.dart';

class SubjectListPage extends StatelessWidget {
  final int classId;
  final String className;
  final String token;
  final List materi;

  const SubjectListPage({
    super.key,
    required this.classId,
    required this.className,
    required this.token,
    required this.materi,
  });

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
  static const Color lightGray       = Color(0xFFE2E8F0);
  static const Color cardWhite       = Color(0xFFFFFFFF);

  // HELPER UNTUK MENENTUKAN IKON DAN WARNA ASYIK UNTUK TIAP MATAPELAJARAN UNIK
  Map<String, dynamic> _getSubjectStyle(String subjectName) {
    final s = subjectName.toLowerCase();
    
    if (s.contains('tiu')) {
      return {
        'color': accentTeal,
        'icon': Icons.psychology_rounded,
      };
    } else if (s.contains('psych') || s.contains('psiko')) {
      return {
        'color': accentTeal,
        'icon': Icons.insights_rounded,
      };
    } else if (s.contains('math') || s.contains('matematika')) {
      return {
        'color': accentTeal,
        'icon': Icons.calculate_rounded,
      };
    } else if (s.contains('twk')) {
      return {
        'color': accentTeal,
        'icon': Icons.flag_rounded,
      };
    } else if (s.contains('tkp')) {
      return {
        'color': accentTeal,
        'icon': Icons.gavel_rounded,
      };
    }
    
    // Default Fallback
    return {
      'color': accentTeal,
      'icon': Icons.import_contacts_rounded,
    };
  }

  @override
  Widget build(BuildContext context) {
    // Ambil mata pelajaran unik
    final subjects = materi
        .map((e) {
          return (e['subject_name'] ?? e['material_name'] ?? e['title'] ?? '').toString();
        })
        .where((name) => name.isNotEmpty) 
        .toSet() 
        .toList();

    return Scaffold(
      backgroundColor: pageBg,
      appBar: AppBar(
        title: const Text(
          "Pilih Mata Pelajaran", 
          style: TextStyle(
            fontWeight: FontWeight.w900, 
            color: Colors.white,
            letterSpacing: -0.5,
            fontSize: 18,
          ),
        ),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
        leading: Padding(
          padding: const EdgeInsets.all(8.0),
          child: CircleAvatar(
            backgroundColor: Colors.white.withOpacity(0.15),
            child: IconButton(
              icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white, size: 16),
              onPressed: () => Navigator.pop(context),
            ),
          ),
        ),
        flexibleSpace: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [primaryRed, accentTeal],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.vertical(
              bottom: Radius.circular(20),
            ),
          ),
        ),
      ),
      body: subjects.isEmpty 
      ? Center(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 24.0),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Container(
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    color: accentTeal.withOpacity(0.08),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(Icons.import_contacts_rounded, size: 52, color: accentTeal),
                ),
                const SizedBox(height: 18),
                const Text(
                  "Materi Belum Tersedia", 
                  style: TextStyle(
                    color: textDark,
                    fontSize: 16,
                    fontWeight: FontWeight.w900,
                    letterSpacing: -0.3,
                  ),
                ),
                const SizedBox(height: 6),
                const Text(
                  "Sabar ya, tim akademis Spekta sedang menyiapkan materi belajar terbaik buat kamu!", 
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: neutralGray,
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    height: 1.5,
                  ),
                ),
              ],
            ),
          ),
        )
      : ListView.builder(
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
          itemCount: subjects.length,
          itemBuilder: (context, index) {
            final sName = subjects[index];
            final style = _getSubjectStyle(sName);
            
            return Container(
              margin: const EdgeInsets.only(bottom: 14),
              decoration: BoxDecoration(
                color: cardWhite, 
                borderRadius: BorderRadius.circular(22), 
                border: Border.all(color: outlineVariant.withOpacity(0.4)),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.02), 
                    blurRadius: 12,
                    offset: const Offset(0, 4),
                  )
                ],
              ),
              child: ListTile(
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                leading: Container(
                  width: 52, 
                  height: 52, 
                  decoration: BoxDecoration(
                    color: (style['color'] as Color).withOpacity(0.08), 
                    borderRadius: BorderRadius.circular(14),
                  ), 
                  child: Icon(
                    style['icon'] as IconData, 
                    color: style['color'] as Color,
                    size: 24,
                  ),
                ),
                title: Text(
                  sName, 
                  style: const TextStyle(
                    fontWeight: FontWeight.w900, 
                    fontSize: 16,
                    color: textDark,
                    letterSpacing: -0.3,
                  ),
                ),
                subtitle: const Padding(
                  padding: EdgeInsets.only(top: 4.0),
                  child: Text(
                    "Lihat materi 20 minggu",
                    style: TextStyle(
                      fontSize: 11.5,
                      color: neutralGray,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
                trailing: Container(
                  width: 36,
                  height: 36,
                  decoration: BoxDecoration(
                    color: accentTeal,
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: const Icon(
                    Icons.play_arrow_rounded, 
                    size: 20,
                    color: Colors.white,
                  ),
                ),
                onTap: () {
                  Navigator.push(context, MaterialPageRoute(
                    builder: (context) => ModuleWeekListPage(
                      subjectName: sName, 
                      token: token, 
                      allMaterials: materi,
                    ),
                  ));
                },
              ),
            );
          },
        ),
    );
  }
}