import 'dart:async';
import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:url_launcher/url_launcher.dart'; 
import 'package:cached_network_image/cached_network_image.dart';

import '../services/auth_service.dart';

import 'fitur/about_academy_page.dart';
import 'fitur/support_center_page.dart';
import 'fitur/question_sharing_page.dart';
import 'fitur/dedicated_tutor_page.dart';
import 'fitur/consultation_page.dart';
import 'banner_detail_page.dart'; 
import 'tryout_page.dart';

// ✅ IMPORT HALAMAN BARU YANG DIBUTUHKAN
import 'subject_list_page.dart';
import 'practice_subject_list_page.dart';

class HomePage extends StatefulWidget {
  final String userName;
  final String token;
  final Map userData;

  const HomePage({
    super.key,
    required this.userName,
    required this.token,
    required this.userData,
  });

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  static const String baseUrl = 'http://10.0.2.2:8000';

  static const Color primaryRed = Color(0xFFC5352C);
  static const Color brightRed = Color(0xFFE53935);
  static const Color accentTeal = Color(0xFF2EA8AB);
  static const Color darkTeal = Color(0xFF00696C);
  static const Color lightBlueBg = Color(0xFFEFF4FF);
  static const Color pageBg = Color(0xFFF1F5F9);
  static const Color textDark = Color(0xFF0F172A);
  static const Color textDarkVariant = Color(0xFF334155);
  static const Color outlineVariant = Color(0xFFE2BEBA);
  static const Color neutralGray = Color(0xFF64748B);
  static const Color lightGray = Color(0xFFE2E8F0);

  Map? currentData;

  List bannerData = [];
  List tryoutData = [];
  List scheduleData = []; 
  List upcomingData = []; 

  Map? latestTryoutResult;
  double progressPercentage = 0.0;
  int currentScore = 0;
  int maxScore = 1000;
  String improvementText = "0% improvement";
  bool isImprovement = true;
  bool isLoadingReport = false;

  bool isLoadingTryout = false;
  bool isEnrolled = false;
  bool isLoadingBanner = false;
  bool isLoadingSchedule = false;

  int activeBannerIndex = 0;

  late PageController _bannerController;
  Timer? _bannerTimer;

  bool _hasShownNetworkError = false; 

  @override
  void initState() {
    super.initState();
    currentData = widget.userData;
    _bannerController = PageController(viewportFraction: 0.92);

    _updateEnrollmentStatus();
    refreshAllData();
  }

  @override
  void dispose() {
    _bannerTimer?.cancel();
    _bannerController.dispose();
    super.dispose();
  }

  Future<void> refreshAllData() async {
    _hasShownNetworkError = false; 
    await Future.wait([
      refreshUserData(),
      fetchBanners(),
      fetchTryouts(),
      fetchSchedules(),
      fetchLearningReport(),
    ]);
  }

  void _showGlobalNetworkError() {
    if (!_hasShownNetworkError && mounted) {
      _hasShownNetworkError = true;
      _showWarning("Mohon maaf sistem sedang sibuk");
    }
  }

  Future<void> refreshUserData() async {
    try {
      final newData = await AuthService.getUserProfile(widget.token);
      if (!mounted) return;
      if (newData != null) {
        setState(() {
          currentData = newData;
          _updateEnrollmentStatus();
        });
      }
    } catch (e) {
      debugPrint('USER DATA ERROR: $e');
    }
  }

  Future<void> fetchLearningReport() async {
    try {
      setState(() => isLoadingReport = true);
      final response = await http.get(
        Uri.parse('$baseUrl/api/learning-report'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer ${widget.token}',
        },
      );
      if (!mounted) return;
      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        final List results = decoded['data'] ?? [];
        
        if (results.isNotEmpty) {
          final latest = results.last;
          
          final score = int.tryParse(latest['score']?.toString() ?? '0') ?? 0;
          final maxSc = int.tryParse(latest['max_score']?.toString() ?? '1000') ?? 1000;
          
          double percent = maxSc > 0 ? (score / maxSc) : 0.0;
          if (percent > 1.0) percent = 1.0;

          String impText = "0% change";
          bool isImp = true;

          if (results.length > 1) {
            final prev = results[results.length - 2];
            final prevScore = int.tryParse(prev['score']?.toString() ?? '0') ?? 0;
            final difference = score - prevScore;
            
            if (prevScore > 0) {
              final percentageDiff = ((difference / prevScore) * 100).round();
              if (percentageDiff >= 0) {
                impText = "+$percentageDiff% improvement";
                isImp = true;
              } else {
                impText = "$percentageDiff% decline";
                isImp = false;
              }
            } else if (difference > 0) {
              impText = "+$difference score points";
              isImp = true;
            }
          } else {
            impText = "First tryout completed";
            isImp = true;
          }

          setState(() {
            latestTryoutResult = latest;
            currentScore = score;
            maxScore = maxSc;
            progressPercentage = percent;
            improvementText = impText;
            isImprovement = isImp;
          });
        }
      } 
    } catch (e) {
      debugPrint('LEARNING REPORT ERROR: $e');
    } finally {
      if (mounted) setState(() => isLoadingReport = false);
    }
  }

  Future<void> fetchBanners() async {
    try {
      setState(() => isLoadingBanner = true);
      final response = await http.get(
        Uri.parse('$baseUrl/api/banners'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer ${widget.token}',
        },
      );
      if (!mounted) return;
      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        setState(() {
          bannerData = decoded['data'] ?? decoded['banners'] ?? [];
          activeBannerIndex = 0;
        });
        _startBannerAutoSlide();
      } 
    } catch (e) {
      debugPrint('BANNER ERROR: $e');
    } finally {
      if (mounted) setState(() => isLoadingBanner = false);
    }
  }

  Future<void> fetchTryouts() async {
    try {
      setState(() => isLoadingTryout = true);
      final response = await http.get(
        Uri.parse('$baseUrl/api/tryouts'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer ${widget.token}',
        },
      );
      if (!mounted) return;
      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        setState(() {
          tryoutData = decoded['data'] ?? decoded['tryouts'] ?? [];
        });
      } 
    } catch (e) {
      debugPrint('TRYOUT ERROR: $e');
    } finally {
      if (mounted) setState(() => isLoadingTryout = false);
    }
  }

  Future<void> fetchSchedules() async {
    try {
      setState(() => isLoadingSchedule = true);
      final response = await http.get(
        Uri.parse('$baseUrl/api/schedules/today'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer ${widget.token}',
        },
      );
      if (!mounted) return;
      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        setState(() {
          if (decoded['data'] != null && decoded['data'] is Map) {
             scheduleData = decoded['data']['today'] ?? [];
             upcomingData = decoded['data']['upcoming'] ?? [];
          } else {
             scheduleData = [];
             upcomingData = [];
          }
        });
      } 
    } catch (e) {
      debugPrint('SCHEDULE ERROR: $e');
    } finally {
      if (mounted) setState(() => isLoadingSchedule = false);
    }
  }

  void _startBannerAutoSlide() {
    _bannerTimer?.cancel();
    if (bannerData.length <= 1) return;
    _bannerTimer = Timer.periodic(
      const Duration(seconds: 4),
      (_) {
        if (!mounted || !_bannerController.hasClients || bannerData.isEmpty) return;
        final nextIndex = (activeBannerIndex + 1) % bannerData.length;
        _bannerController.animateToPage(
          nextIndex, duration: const Duration(milliseconds: 450), curve: Curves.easeOutCubic,
        );
      },
    );
  }

  Future<void> _handleBannerClick(Map bannerItem, String imageUrl) async {
    final String? link = bannerItem['link']?.toString().trim();

    if (link != null && link.isNotEmpty) {
      final Uri url = Uri.parse(link);
      if (await canLaunchUrl(url)) {
        await launchUrl(url, mode: LaunchMode.externalApplication);
      } else {
        _showWarning("Tidak dapat membuka link promo ini.");
      }
    } else {
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (context) => BannerDetailPage(
            bannerData: bannerItem,
            imageUrl: imageUrl,
          ),
        ),
      );
    }
  }

  void _updateEnrollmentStatus() {
    if (currentData?['student'] != null && currentData!['student']['class_id'] != null) {
      isEnrolled = true;
    } else if (widget.userData['student'] != null && widget.userData['student']['class_id'] != null) {
      isEnrolled = true;
    } else {
      isEnrolled = false;
    }
  }

  String _safeText(dynamic value, {String fallback = '-'}) {
    if (value == null) return fallback;
    final text = value.toString().trim();
    if (text.isEmpty) return fallback;
    return text;
  }

  String _imageUrl(dynamic rawPath) {
    if (rawPath == null) return '';
    var path = rawPath.toString().trim();
    if (path.isEmpty) return '';
    path = path.replaceAll('\\', '/');

    if (path.startsWith('http://127.0.0.1:8000')) return path.replaceFirst('http://127.0.0.1:8000', baseUrl);
    if (path.startsWith('http://localhost:8000')) return path.replaceFirst('http://localhost:8000', baseUrl);
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    if (path.startsWith('/storage/')) return '$baseUrl$path';
    if (path.startsWith('storage/')) return '$baseUrl/$path';
    if (path.startsWith('/')) return '$baseUrl$path';
    return '$baseUrl/storage/$path';
  }

  dynamic _firstExisting(Map item, List<String> keys) {
    for (final key in keys) {
      if (item.containsKey(key) && item[key] != null && item[key].toString().trim().isNotEmpty) {
        return item[key];
      }
    }
    return null;
  }

  // ✅ 1. PERUBAHAN FUNGSI KLIK MATERI (LANGSUNG KE SUBJECT LIST PAGE)
  Future<void> _handleLearningMaterials() async {
    final student = currentData?['student'] ?? widget.userData['student'];
    if (student == null || student['class_id'] == null) {
      _showWarning('Kamu belum terdaftar di kelas mana pun. Daftar kelas dulu ya!');
      return;
    }

    showDialog(
      context: context, 
      barrierDismissible: false, 
      builder: (_) => const Center(child: CircularProgressIndicator(color: primaryRed))
    );

    try {
      final classId = int.parse(student['class_id'].toString());
      
      // ✅ Tambahkan .timeout agar loading tidak nyangkut selamanya
      final response = await AuthService.getClassContent(classId, widget.token).timeout(const Duration(seconds: 8));

      if (!mounted) return;
      Navigator.pop(context); // Tutup Loading

      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        
        // Ekstrak List Materi dari JSON Response
        List listMateri = [];
        if (decoded is List) {
          listMateri = decoded;
        } else if (decoded is Map) {
          listMateri = decoded['materi'] ?? decoded['data'] ?? [];
        }

        // Langsung lompat ke SubjectListPage
        Navigator.push(
          context, 
          MaterialPageRoute(
            builder: (context) => SubjectListPage(
              classId: classId, 
              className: decoded is Map ? (decoded['program_name'] ?? "Materi Belajar") : "Materi Belajar", 
              token: widget.token,
              materi: listMateri, 
            ),
          ),
        );
      } else {
        _showWarning("Mohon maaf sistem sedang sibuk");
      }
    } catch (e) {
      if (mounted) Navigator.pop(context); // Tutup Loading
      _showWarning("Mohon maaf sistem sedang sibuk");
    }
  }

  // ✅ 2. FUNGSI BARU UNTUK FITUR LATIHAN SOAL
  Future<void> _handlePracticeQuestions() async {
    final student = currentData?['student'] ?? widget.userData['student'];
    if (student == null || student['class_id'] == null) {
      _showWarning('Kamu belum terdaftar di kelas mana pun.');
      return;
    }

    showDialog(
      context: context, 
      barrierDismissible: false, 
      builder: (_) => const Center(child: CircularProgressIndicator(color: primaryRed))
    );

    try {
      final classId = int.parse(student['class_id'].toString());
      
      // Mengambil data latihan soal (menggunakan endpoint getTryouts seperti pada kode lama)
      final response = await AuthService.getTryouts(widget.token, classId: classId).timeout(const Duration(seconds: 8));

      if (!mounted) return;
      Navigator.pop(context); // Tutup Loading

      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        List practiceData = decoded is List ? decoded : (decoded['data'] ?? []);

        if (practiceData.isEmpty) {
          _showWarning("Latihan soal belum tersedia.");
          return;
        }

        // Arahkan ke Halaman Daftar Latihan Soal
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => PracticeSubjectListPage(
              allExercises: practiceData,
              token: widget.token,
            ),
          ),
        );
      } else {
        _showWarning("Mohon maaf sistem sedang sibuk");
      }
    } catch (e) {
      if (mounted) Navigator.pop(context); // Tutup Loading
      _showWarning("Mohon maaf sistem sedang sibuk");
    }
  }

  void _handleTryout() {
    Navigator.push(context, MaterialPageRoute(builder: (c) => TryoutPage(token: widget.token, userData: widget.userData)));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: pageBg,
      body: SafeArea(
        bottom: false,
        child: RefreshIndicator(
          color: primaryRed,
          onRefresh: refreshAllData,
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 80),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _buildHeader(),
                const SizedBox(height: 12), 
                _buildBannerSection(),
                const SizedBox(height: 18), 
                _buildBentoGrid(),
                const SizedBox(height: 24), 
                _sectionTitle(title: 'Jadwal Hari Ini', onTap: null),
                const SizedBox(height: 10),
                _buildScheduleWidget(),

                if (upcomingData.isNotEmpty) ...[
                  const SizedBox(height: 24),
                  _sectionTitle(title: 'Kelas Mendatang', onTap: null),
                  const SizedBox(height: 10),
                  _buildUpcomingClassesList(),
                ],
                const SizedBox(height: 24),
                _buildUtilityCardsRow(),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildHeader() {
    final name = _safeText(currentData?['name'] ?? widget.userData['name'], fallback: widget.userName);

    return Container(
      width: double.infinity,
      margin: const EdgeInsets.only(bottom: 6),
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 18),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [primaryRed, accentTeal],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: primaryRed.withOpacity(0.2),
            blurRadius: 12,
            offset: const Offset(0, 5),
          )
        ],
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          _buildAvatar(),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  'Welcome back,',
                  style: TextStyle(
                    color: Colors.white.withOpacity(0.85),
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                    letterSpacing: 0.2,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  name,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 20,
                    fontWeight: FontWeight.w900,
                    letterSpacing: -0.5,
                  ),
                ),
                const SizedBox(height: 4),
                Row(
                  children: [
                    Container(
                      width: 6,
                      height: 6,
                      decoration: const BoxDecoration(
                        color: Color(0xFF4ADE80), 
                        shape: BoxShape.circle,
                      ),
                    ),
                    const SizedBox(width: 6),
                    Text(
                      'Ready to learn today?',
                      style: TextStyle(
                        color: Colors.white.withOpacity(0.75),
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAvatar() {
    final photoUrl = currentData?['photo_url'] ?? '';
    
    return Stack(
      clipBehavior: Clip.none,
      children: [
        Container(
          height: 50, 
          width: 50, 
          padding: const EdgeInsets.all(2.0),
          decoration: BoxDecoration(
            color: Colors.white, 
            shape: BoxShape.circle, 
            border: Border.all(color: Colors.white, width: 2.0), 
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.12),
                blurRadius: 6,
                offset: const Offset(0, 3),
              )
            ]
          ),
          child: ClipOval(
            child: photoUrl.isNotEmpty
                ? CachedNetworkImage(
                    imageUrl: photoUrl,
                    fit: BoxFit.cover,
                    placeholder: (context, url) => Container(
                      color: lightBlueBg,
                      child: const Icon(Icons.person, color: primaryRed, size: 26),
                    ),
                    errorWidget: (context, url, error) => Container(
                      color: lightBlueBg,
                      child: const Icon(Icons.person, color: primaryRed, size: 26),
                    ),
                  )
                : Container(
                    color: lightBlueBg,
                    child: const Icon(Icons.person, color: primaryRed, size: 26),
                  ),
          ),
        ),
        Positioned(
          bottom: 0,
          right: 0,
          child: Container(
            width: 12,
            height: 12,
            decoration: BoxDecoration(
              color: const Color(0xFF2EC55E),
              shape: BoxShape.circle,
              border: Border.all(color: Colors.white, width: 2),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildBannerSection() {
    if (isLoadingBanner) {
      return Container(
        height: 150, 
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16)), 
        child: const Center(child: CircularProgressIndicator(color: primaryRed))
      );
    }

    if (bannerData.isEmpty) {
      return Container(
        height: 142, 
        width: double.infinity,
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(16),
          gradient: const LinearGradient(
            colors: [primaryRed, brightRed],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          boxShadow: [
            BoxShadow(
              color: primaryRed.withOpacity(0.20), 
              blurRadius: 10,
              offset: const Offset(0, 4),
            )
          ],
        ),
        child: _buildDefaultBannerContent("Informasi Akademik", "banner belum di input"),
      );
    }

    return Column(
      children: [
        SizedBox(
          height: 160, 
          child: PageView.builder(
            controller: _bannerController, 
            itemCount: bannerData.length,
            onPageChanged: (index) => setState(() => activeBannerIndex = index),
            itemBuilder: (context, index) {
              final item = bannerData[index] as Map;
              final imagePath = _firstExisting(item, ['image_url', 'image', 'banner', 'photo', 'thumbnail']);
              final imageUrl = _imageUrl(imagePath);

              return GestureDetector(
                onTap: () => _handleBannerClick(item, imageUrl),
                child: Container(
                  margin: const EdgeInsets.symmetric(horizontal: 4, vertical: 4),
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(16), 
                    boxShadow: [
                      BoxShadow(
                        color: primaryRed.withOpacity(0.2), 
                        blurRadius: 10, 
                        offset: const Offset(0, 4)
                      )
                    ]
                  ),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(16),
                    child: imageUrl.isNotEmpty
                        ? CachedNetworkImage(
                            imageUrl: imageUrl,
                            fit: BoxFit.cover,
                            width: double.infinity,
                            height: double.infinity,
                            errorWidget: (context, url, error) => Container(
                              decoration: const BoxDecoration(
                                gradient: LinearGradient(
                                  colors: [primaryRed, brightRed],
                                  begin: Alignment.topLeft,
                                  end: Alignment.bottomRight,
                                ),
                              ),
                              child: _buildBannerOverlayContent(item),
                            ),
                            placeholder: (context, url) => Container(
                              color: Colors.white,
                              child: const Center(child: CircularProgressIndicator(color: primaryRed)),
                            ),
                          )
                        : Container(
                            decoration: const BoxDecoration(
                              gradient: LinearGradient(
                                  colors: [primaryRed, brightRed],
                                  begin: Alignment.topLeft,
                                  end: Alignment.bottomRight,
                                ),
                            ),
                            child: _buildBannerOverlayContent(item),
                          ),
                  ),
                ),
              );
            },
          ),
        ),
        const SizedBox(height: 6),
        Row(
          mainAxisAlignment: MainAxisAlignment.center, 
          children: List.generate(bannerData.length, (index) {
            final active = index == activeBannerIndex;
            return AnimatedContainer(
              duration: const Duration(milliseconds: 250), 
              margin: const EdgeInsets.symmetric(horizontal: 2), 
              height: 5, 
              width: active ? 16 : 5, 
              decoration: BoxDecoration(
                color: active ? primaryRed : Colors.grey.shade300, 
                borderRadius: BorderRadius.circular(99)
              )
            );
          }),
        ),
      ],
    );
  }

  Widget _buildDefaultBannerContent(String title, String subtitle) {
    return Stack(
      children: [
        Positioned(
          right: -24,
          bottom: -24,
          child: Container(
            width: 110,
            height: 110,
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.08),
              shape: BoxShape.circle,
            ),
          ),
        ),
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.campaign_rounded, color: Colors.white, size: 28),
              const SizedBox(height: 8),
              Text(
                title,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 3),
              Text(
                subtitle,
                style: const TextStyle(
                  color: Colors.white70,
                  fontSize: 12,
                  fontWeight: FontWeight.bold, 
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildBannerOverlayContent(Map item) {
    final title = _safeText(item['title'], fallback: "Master Calculus with\nSpekta Elite");
    final subtitle = _safeText(item['description'], fallback: "Unlock advanced modules.");

    return Stack(
      children: [
        Container(color: Colors.black.withOpacity(0.25)),
        Positioned(
          right: -32,
          bottom: -32,
          child: Container(
            width: 120,
            height: 120,
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.08),
              shape: BoxShape.circle,
            ),
          ),
        ),
        Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(6),
                ),
                child: const Text(
                  "PREMIUM ACCESS",
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 9,
                    fontWeight: FontWeight.bold,
                    letterSpacing: 1.2
                  ),
                ),
              ),
              const SizedBox(height: 6),
              Text(
                title,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  height: 1.15
                ),
              ),
              const SizedBox(height: 4),
              Text(
                subtitle,
                style: const TextStyle(color: Colors.white70, fontSize: 11, fontWeight: FontWeight.bold),
              ),
            ],
          ),
        ),
      ],
    );
  }

  // ✅ 3. TAMBAH FITUR LATIHAN SOAL KE DALAM BENTO GRID
  Widget _buildBentoGrid() {
    final bentoItems = [
      {
        'title': 'Materi', 
        'icon': Icons.import_contacts, 
        'bgColor': const Color(0xFFE2F9FC),
        'borderColor': const Color(0xFFBFEFF5),
        'iconColor': const Color(0xFF00696C),
        'action': () => _handleLearningMaterials()
      },
      {
        'title': 'Latihan Soal', // FITUR BARU!
        'icon': Icons.quiz_rounded, 
        'bgColor': const Color(0xFFFFF7ED), // Orange muda pastel
        'borderColor': const Color(0xFFFFEDD5),
        'iconColor': const Color(0xFFEA580C), // Orange tegas
        'action': () => _handlePracticeQuestions()
      },
      {
        'title': 'Tryout', 
        'icon': Icons.assignment, 
        'bgColor': const Color(0xFFFFF1F1),
        'borderColor': const Color(0xFFFCD3D1),
        'iconColor': primaryRed,
        'action': () => _handleTryout()
      },
      {
        'title': 'Tutor', 
        'icon': Icons.school, 
        'bgColor': const Color(0xFFF8FAFC),
        'borderColor': const Color(0xFFE2E8F0),
        'iconColor': neutralGray,
        'action': () => Navigator.push(context, MaterialPageRoute(builder: (c) => DedicatedTutorPage(token: widget.token, userData: widget.userData)))
      },
      {
        'title': 'Bank Soal', 
        'icon': Icons.menu_book, 
        'bgColor': const Color(0xFFEFF4FF),
        'borderColor': const Color(0xFFD0E1FF),
        'iconColor': const Color(0xFF1D4ED8),
        'action': () => Navigator.push(context, MaterialPageRoute(builder: (c) => QuestionSharingPage(token: widget.token)))
      },
    ];

    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: bentoItems.length,
      padding: EdgeInsets.zero, 
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        crossAxisSpacing: 14,
        mainAxisSpacing: 14, 
        childAspectRatio: 1.6,
      ),
      itemBuilder: (context, index) {
        final item = bentoItems[index];
        final bgColor = item['bgColor'] as Color;
        final borderColor = item['borderColor'] as Color;
        final iconColor = item['iconColor'] as Color;

        return InkWell(
          onTap: item['action'] as VoidCallback,
          borderRadius: BorderRadius.circular(20),
          child: Ink(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
            decoration: BoxDecoration(
              color: bgColor,
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: borderColor),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.015),
                  blurRadius: 10,
                  offset: const Offset(0, 4),
                )
              ],
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(item['icon'] as IconData, color: iconColor, size: 24),
                const SizedBox(height: 10),
                Text(
                  item['title'] as String,
                  style: const TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w900,
                    color: textDark,
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildUtilityCardsRow() {
    final utilities = [
      {
        'title': 'CONSULT',
        'icon': Icons.chat_bubble_outline_rounded,
        'action': () => Navigator.push(context, MaterialPageRoute(builder: (c) => const ConsultationPage())),
      },
      {
        'title': 'ABOUT',
        'icon': Icons.info_outline_rounded,
        'action': () => Navigator.push(context, MaterialPageRoute(builder: (c) => const AboutAcademyPage())),
      },
      {
        'title': 'SUPPORT',
        'icon': Icons.support_agent_rounded,
        'action': () => Navigator.push(context, MaterialPageRoute(builder: (c) => const SupportCenterPage())),
      },
    ];

    return Row(
      children: List.generate(utilities.length, (index) {
        final item = utilities[index];
        
        return Expanded(
          child: Container(
            margin: EdgeInsets.only(
              right: index == utilities.length - 1 ? 0 : 12,
            ),
            height: 84,
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: outlineVariant.withOpacity(0.5)),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.015),
                  blurRadius: 10,
                  offset: const Offset(0, 4),
                )
              ],
            ),
            child: InkWell(
              onTap: item['action'] as VoidCallback,
              borderRadius: BorderRadius.circular(16),
              child: Padding(
                padding: const EdgeInsets.symmetric(vertical: 12),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(item['icon'] as IconData, color: primaryRed, size: 22),
                    const SizedBox(height: 8),
                    Text(
                      item['title'] as String,
                      style: const TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.w900,
                        color: textDark,
                        letterSpacing: 0.5,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        );
      }),
    );
  }

  Widget _sectionTitle({required String title, VoidCallback? onTap}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          title, 
          style: const TextStyle(
            color: textDark, 
            fontSize: 15.5, 
            fontWeight: FontWeight.w900, 
            letterSpacing: -0.4
          )
        ),
        if (onTap != null)
          GestureDetector(
            onTap: onTap,
            child: const Row(
              children: [
                Text(
                  'See All', 
                  style: TextStyle(color: primaryRed, fontSize: 12, fontWeight: FontWeight.bold)
                ),
                SizedBox(width: 4), 
                Icon(Icons.arrow_forward_ios_rounded, color: primaryRed, size: 10),
              ],
            ),
          ),
      ],
    );
  }

  Widget _buildScheduleWidget() {
    if (isLoadingSchedule) {
      return Container(
        height: 80, 
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16)), 
        child: const Center(child: CircularProgressIndicator(color: primaryRed))
      );
    }

    if (scheduleData.isEmpty) {
      return Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        decoration: BoxDecoration(
          color: Colors.white, 
          borderRadius: BorderRadius.circular(16), 
          border: Border.all(color: outlineVariant.withOpacity(0.5)), 
          boxShadow: [
            BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 10, offset: const Offset(0, 4))
          ]
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: lightBlueBg, 
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: outlineVariant.withOpacity(0.2)),
              ), 
              child: const Icon(Icons.calendar_today_outlined, color: primaryRed, size: 18) 
            ),
            const SizedBox(width: 12),
            const Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Tidak ada jadwal terdekat', 
                  style: TextStyle(color: textDark, fontSize: 13, fontWeight: FontWeight.w900) 
                ),
                SizedBox(height: 2),
                Text(
                  'Jadwal belajar baru akan muncul di sini', 
                  style: TextStyle(color: textDarkVariant, fontSize: 11, fontWeight: FontWeight.bold) 
                ),
              ],
            ),
          ],
        ),
      );
    }

    final displayList = scheduleData.take(3).toList();

    return ListView.separated(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: displayList.length,
      padding: EdgeInsets.zero, 
      separatorBuilder: (context, index) => const SizedBox(height: 10), 
      itemBuilder: (context, index) {
        final item = displayList[index] as Map;
        
        final String day = item['day_date']?.toString() ?? DateTime.now().day.toString();
        final String month = item['month_name']?.toString().substring(0, 3) ?? "JAN";
        
        final String subject = item['subject_name'] ?? 'Mata Pelajaran';
        final String teacher = item['teacher_name'] ?? 'Guru Pengajar';
        final String startTime = item['start_time'] ?? '';
        final String endTime = item['end_time'] ?? '';

        return Container(
          padding: const EdgeInsets.all(10), 
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: outlineVariant.withOpacity(0.4)),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.02),
                blurRadius: 10,
                offset: const Offset(0, 4),
              )
            ],
          ),
          child: Row(
            children: [
              Container(
                width: 54, 
                padding: const EdgeInsets.symmetric(vertical: 6, horizontal: 2),
                decoration: BoxDecoration(
                  color: lightBlueBg,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: outlineVariant.withOpacity(0.3)),
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text(
                      month.toUpperCase(),
                      style: const TextStyle(color: textDarkVariant, fontSize: 9, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      day,
                      style: const TextStyle(color: textDark, fontSize: 16, fontWeight: FontWeight.w900, height: 1.1),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      subject,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(color: textDark, fontSize: 13, fontWeight: FontWeight.w900),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      teacher,
                      style: const TextStyle(color: textDarkVariant, fontSize: 11, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 3),
                    Row(
                      children: [
                        const Icon(Icons.access_time, size: 12, color: textDarkVariant),
                        const SizedBox(width: 4),
                        Text(
                          endTime.isNotEmpty ? '$startTime - $endTime' : startTime,
                          style: const TextStyle(color: textDarkVariant, fontSize: 10, fontWeight: FontWeight.w900),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              const Icon(Icons.chevron_right_rounded, color: primaryRed, size: 20),
            ],
          ),
        );
      },
    );
  }

  Widget _buildUpcomingClassesList() {
    return ListView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      padding: EdgeInsets.zero, 
      itemCount: upcomingData.length > 3 ? 3 : upcomingData.length, 
      itemBuilder: (context, index) {
        final item = upcomingData[index];
        final monthName = item['month_name'] ?? 'MTH';
        final dayDate = item['day_date'] ?? '00';
        final dayName = item['day_name'] ?? 'Hari';
        final subject = item['subject_name'] ?? 'Pelajaran';
        final time = '${item['start_time']} - ${item['end_time']}';
        final teacher = item['teacher_name'] ?? '';

        return Container(
          margin: const EdgeInsets.only(bottom: 10),
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: outlineVariant.withOpacity(0.4)),
            boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.015), blurRadius: 10, offset: const Offset(0, 4))],
          ),
          child: Row(
            children: [
              Container(
                width: 54, 
                padding: const EdgeInsets.symmetric(vertical: 6, horizontal: 2),
                decoration: BoxDecoration(
                  color: lightBlueBg, 
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min, 
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text(
                      monthName, 
                      style: const TextStyle(color: primaryRed, fontSize: 9, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      dayDate, 
                      style: const TextStyle(color: textDark, fontSize: 16, fontWeight: FontWeight.w900, height: 1.1),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      dayName, 
                      style: const TextStyle(color: textDarkVariant, fontSize: 8, fontWeight: FontWeight.bold),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(subject, maxLines: 1, overflow: TextOverflow.ellipsis, style: const TextStyle(color: textDark, fontSize: 13, fontWeight: FontWeight.w900)),
                    const SizedBox(height: 2),
                    Text('Bersama $teacher', style: const TextStyle(color: textDarkVariant, fontSize: 11, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 3),
                    Row(
                      children: [
                        const Icon(Icons.access_time_rounded, color: textDarkVariant, size: 12),
                        const SizedBox(width: 4),
                        Text('$time WIB', style: const TextStyle(color: textDarkVariant, fontSize: 10, fontWeight: FontWeight.w900)),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  void _showWarning(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        backgroundColor: primaryRed, 
        behavior: SnackBarBehavior.floating, 
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)), 
        content: Text(message)
      )
    );
  }
}