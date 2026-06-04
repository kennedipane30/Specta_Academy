import 'dart:async';
import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

import '../services/auth_service.dart';

import 'fitur/about_academy_page.dart';
import 'fitur/support_center_page.dart';
import 'fitur/question_sharing_page.dart';
import 'fitur/dedicated_tutor_page.dart';
import 'fitur/consultation_page.dart';
import 'fitur/tryout_page.dart';
import 'notification_page.dart'; // ✨ TAMBAHKAN IMPORT INI

import 'class_detail_page.dart';
import 'subject_list_page.dart';

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

  static const Color primaryRed = Color(0xFF9C0412);
  static const Color deepRed = Color(0xFF520102);
  static const Color darkRed = Color(0xFF340506);
  static const Color accentRed = Color(0xFFC50337);
  static const Color pageBg = Color(0xFFFAFAFA);
  static const Color textDark = Color(0xFF172033);

  Map? currentData;

  List bannerData = [];
  List tryoutData = [];
  List scheduleData = [];

  bool isLoadingTryout = false;
  bool isEnrolled = false;
  bool isLoadingBanner = false;
  bool isLoadingSchedule = false;
  
  // ✨ NOTIFIKASI: State untuk angka notifikasi
  int unreadNotifications = 0;
  bool isLoadingNotifications = false;

  int activeBannerIndex = 0;

  late PageController _bannerController;
  Timer? _bannerTimer;

  @override
  void initState() {
    super.initState();

    currentData = widget.userData;
    _bannerController = PageController(viewportFraction: 0.88);

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
    await Future.wait([
      refreshUserData(),
      fetchBanners(),
      fetchTryouts(),
      fetchSchedules(),
      fetchNotificationCount(),
    ]);
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
      } else {
        debugPrint('BANNER API ERROR: Status ${response.statusCode}');
      }
    } catch (e) {
      debugPrint('BANNER ERROR: $e');
    } finally {
      if (mounted) {
        setState(() => isLoadingBanner = false);
      }
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
      } else {
        debugPrint('TRYOUT API ERROR: Status ${response.statusCode}');
      }
    } catch (e) {
      debugPrint('TRYOUT ERROR: $e');
    } finally {
      if (mounted) {
        setState(() => isLoadingTryout = false);
      }
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
          scheduleData = decoded['data'] ?? decoded['schedules'] ?? [];
        });
      } else {
        debugPrint('SCHEDULE API ERROR: Status ${response.statusCode}');
      }
    } catch (e) {
      debugPrint('SCHEDULE ERROR: $e');
    } finally {
      if (mounted) {
        setState(() => isLoadingSchedule = false);
      }
    }
  }

  // ✨ NOTIFIKASI: Fungsi untuk mengambil jumlah notifikasi dari API
  Future<void> fetchNotificationCount() async {
    try {
      setState(() => isLoadingNotifications = true);
      
      final response = await http.get(
        Uri.parse('$baseUrl/api/notifications/unread-count'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer ${widget.token}',
        },
      );

      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        setState(() {
          unreadNotifications = decoded['unread_count'] ?? 0;
        });
      } else {
        debugPrint('NOTIFICATION API ERROR: Status ${response.statusCode}');
      }
    } catch (e) {
      debugPrint('NOTIFICATION COUNT ERROR: $e');
    } finally {
      if (mounted) {
        setState(() => isLoadingNotifications = false);
      }
    }
  }

  // ✨ NOTIFIKASI: Fungsi untuk handle klik notifikasi (DIPERBAIKI)
  void _handleNotificationClick() {
    // Navigasi ke halaman daftar notifikasi
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => NotificationPage(
          token: widget.token,
        ),
      ),
    ).then((_) {
      // Refresh jumlah notifikasi saat kembali dari halaman notifikasi
      fetchNotificationCount();
    });
  }

  void _startBannerAutoSlide() {
    _bannerTimer?.cancel();

    if (bannerData.length <= 1) return;

    _bannerTimer = Timer.periodic(
      const Duration(seconds: 4),
      (_) {
        if (!mounted || !_bannerController.hasClients || bannerData.isEmpty) {
          return;
        }

        final nextIndex = (activeBannerIndex + 1) % bannerData.length;

        _bannerController.animateToPage(
          nextIndex,
          duration: const Duration(milliseconds: 450),
          curve: Curves.easeOutCubic,
        );
      },
    );
  }

  void _updateEnrollmentStatus() {
    if (currentData != null && currentData!['student'] != null) {
      isEnrolled = currentData!['student']['class_id'] != null;
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

    if (path.startsWith('http://127.0.0.1:8000')) {
      return path.replaceFirst('http://127.0.0.1:8000', baseUrl);
    }

    if (path.startsWith('http://localhost:8000')) {
      return path.replaceFirst('http://localhost:8000', baseUrl);
    }

    if (path.startsWith('http://') || path.startsWith('https://')) {
      return path;
    }

    if (path.startsWith('/storage/')) {
      return '$baseUrl$path';
    }

    if (path.startsWith('storage/')) {
      return '$baseUrl/$path';
    }

    if (path.startsWith('/')) {
      return '$baseUrl$path';
    }

    return '$baseUrl/storage/$path';
  }

  dynamic _firstExisting(Map item, List<String> keys) {
    for (final key in keys) {
      if (item.containsKey(key) &&
          item[key] != null &&
          item[key].toString().trim().isNotEmpty) {
        return item[key];
      }
    }

    return null;
  }

  // --- Bagian fungsi _handleLearningMaterials di HomePage ---

  Future<void> _handleLearningMaterials() async {
    final student = currentData?['student'];

    if (student == null || student['class_id'] == null) {
      _showWarning('Kamu belum terdaftar di kelas mana pun. Daftar kelas dulu ya!');
      return;
    }

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) => const Center(
        child: CircularProgressIndicator(color: primaryRed),
      ),
    );

    try {
      final classId = int.parse(student['class_id'].toString());
      final response = await AuthService.getClassContent(classId, widget.token);

      if (!mounted) return;
      Navigator.pop(context); // Tutup loading

      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        
        // ✨ Ambil harga dari response gateway
        final int classPrice = int.tryParse(decoded['price']?.toString() ?? '0') ?? 0;

        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => ClassDetailPage(
              classId: classId,
              className: decoded['program_name'] ?? "Materi Saya",
              price: classPrice,
              token: widget.token,
              userData: currentData!,
            ),
          ),
        );
      }
    } catch (e) {
      if (mounted) Navigator.pop(context);
      _showWarning("Gagal memuat materi kelas. Pastikan koneksi stabil.");
    }
  }

  void _handleTryout() {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (c) => TryoutPage(
          token: widget.token,
          userData: currentData ?? widget.userData,
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: pageBg,
      body: RefreshIndicator(
        color: primaryRed,
        onRefresh: refreshAllData,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.only(bottom: 120),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildHeader(),

              const SizedBox(height: 18),

              _buildBannerSection(),

              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 22),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const SizedBox(height: 20),

                    _sectionTitle(
                      title: 'Jadwal Hari Ini',
                      action: 'Lihat Semua',
                      onTap: null,
                    ),

                    const SizedBox(height: 12),

                    _buildScheduleWidget(),

                    const SizedBox(height: 28),

                    _sectionTitle(
                      title: 'Lanjutkan Belajar',
                      action: 'Lihat Semua',
                      onTap: _handleLearningMaterials,
                    ),

                    const SizedBox(height: 14),

                    _buildContinueLearningCard(),

                    const SizedBox(height: 28),

                    _sectionTitle(
                      title: 'Tryout',
                      action: 'Lihat Semua',
                      onTap: _handleTryout,
                    ),

                    const SizedBox(height: 14),

                    _buildTryoutSection(),

                    const SizedBox(height: 28),

                    const Text(
                      'Menu Utama',
                      style: TextStyle(
                        color: textDark,
                        fontSize: 21,
                        fontWeight: FontWeight.w900,
                        letterSpacing: -0.5,
                      ),
                    ),

                    const SizedBox(height: 14),

                    _buildMainMenuGrid(),

                    const SizedBox(height: 28),

                    _sectionTitle(
                      title: 'Kelas Mendatang',
                      action: 'Lihat Semua',
                    ),

                    const SizedBox(height: 14),

                    _buildUpcomingClassCard(),

                    const SizedBox(height: 16),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHeader() {
    final name = _safeText(
      currentData?['name'],
      fallback: widget.userName,
    );

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.fromLTRB(22, 52, 22, 30),
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            accentRed,
            primaryRed,
            darkRed,
          ],
        ),
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(34),
          bottomRight: Radius.circular(34),
        ),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          _buildAvatar(),

          const SizedBox(width: 13),

          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Text(
                  'Welcome back,',
                  style: TextStyle(
                    color: Colors.white70,
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                  ),
                ),

                const SizedBox(height: 4),

                Text(
                  name,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 24,
                    height: 1.1,
                    fontWeight: FontWeight.w900,
                    letterSpacing: -0.4,
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(width: 10),

          _buildGlassButton(Icons.search_rounded),

          const SizedBox(width: 8),

          Stack(
            clipBehavior: Clip.none,
            children: [
              GestureDetector(
                onTap: () => _handleNotificationClick(),
                child: _buildGlassButton(Icons.notifications_none_rounded),
              ),
              
              if (unreadNotifications > 0)
                Positioned(
                  top: -6,
                  right: -4,
                  child: GestureDetector(
                    onTap: () => _handleNotificationClick(),
                    child: Container(
                      height: 22,
                      width: 22,
                      alignment: Alignment.center,
                      decoration: const BoxDecoration(
                        color: Color(0xFFFF2D2D),
                        shape: BoxShape.circle,
                        border: Border.fromBorderSide(
                          BorderSide(color: Colors.white, width: 1.5)
                        ),
                      ),
                      child: Text(
                        unreadNotifications > 9 ? '9+' : unreadNotifications.toString(),
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 10,
                          fontWeight: FontWeight.w900,
                        ),
                      ),
                    ),
                  ),
                ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildAvatar() {
    final rawAvatar = currentData?['photo'] ??
        currentData?['avatar'] ??
        currentData?['profile_photo'];

    final avatarUrl = _imageUrl(rawAvatar);

    return Stack(
      clipBehavior: Clip.none,
      children: [
        Container(
          height: 58,
          width: 58,
          padding: const EdgeInsets.all(3),
          decoration: BoxDecoration(
            color: Colors.white,
            shape: BoxShape.circle,
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.18),
                blurRadius: 14,
                offset: const Offset(0, 6),
              ),
            ],
          ),
          child: ClipOval(
            child: avatarUrl.isEmpty
                ? Container(
                    color: const Color(0xFFFFF1F1),
                    child: const Icon(
                      Icons.person_rounded,
                      color: primaryRed,
                      size: 31,
                    ),
                  )
                : Image.network(
                    avatarUrl,
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) {
                      return Container(
                        color: const Color(0xFFFFF1F1),
                        child: const Icon(
                          Icons.person_rounded,
                          color: primaryRed,
                          size: 31,
                        ),
                      );
                    },
                  ),
          ),
        ),

        Positioned(
          right: 0,
          bottom: 1,
          child: Container(
            height: 15,
            width: 15,
            decoration: BoxDecoration(
              color: const Color(0xFF22C55E),
              shape: BoxShape.circle,
              border: Border.all(
                color: Colors.white,
                width: 3,
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildGlassButton(IconData icon) {
    return Container(
      height: 44,
      width: 44,
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.12),
        borderRadius: BorderRadius.circular(18),
        border: Border.all(
          color: Colors.white.withOpacity(0.13),
        ),
      ),
      child: Icon(
        icon,
        color: Colors.white,
        size: 23,
      ),
    );
  }

  Widget _buildBannerSection() {
    if (isLoadingBanner) {
      return Container(
        height: 165,
        margin: const EdgeInsets.symmetric(horizontal: 22),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(26),
        ),
        child: const Center(
          child: CircularProgressIndicator(
            color: primaryRed,
          ),
        ),
      );
    }

    if (bannerData.isEmpty) {
      return Container(
        height: 165,
        margin: const EdgeInsets.symmetric(horizontal: 22),
        decoration: BoxDecoration(
          gradient: const LinearGradient(
            colors: [
              primaryRed,
              deepRed,
            ],
          ),
          borderRadius: BorderRadius.circular(26),
        ),
        child: const Center(
          child: Text(
            'Banner belum tersedia',
            style: TextStyle(
              color: Colors.white,
              fontWeight: FontWeight.w800,
            ),
          ),
        ),
      );
    }

    return Column(
      children: [
        SizedBox(
          height: 168,
          child: PageView.builder(
            controller: _bannerController,
            itemCount: bannerData.length,
            onPageChanged: (index) {
              setState(() {
                activeBannerIndex = index;
              });
            },
            itemBuilder: (context, index) {
              final item = bannerData[index] as Map;

              final imagePath = _firstExisting(
                item,
                [
                  'image_url',
                  'image',
                  'banner',
                  'photo',
                  'thumbnail',
                ],
              );

              final imageUrl = _imageUrl(imagePath);

              print('IMAGE PATH = $imagePath');
              print('IMAGE URL  = $imageUrl');

              return Container(
                margin: const EdgeInsets.symmetric(
                  horizontal: 7,
                  vertical: 6,
                ),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(26),
                  boxShadow: [
                    BoxShadow(
                      color: primaryRed.withOpacity(0.13),
                      blurRadius: 18,
                      offset: const Offset(0, 8),
                    ),
                  ],
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(26),
                  child: imageUrl.isEmpty
                      ? Container(
                          color: const Color(0xFFE5E7EB),
                          child: const Icon(
                            Icons.image_rounded,
                            color: Colors.grey,
                            size: 38,
                          ),
                        )
                      // : Image.network(
                      //     imageUrl,
                      //     fit: BoxFit.cover,
                      //     errorBuilder: (_, __, ___) {
                      //       return Container(
                      //         color: const Color(0xFFE5E7EB),
                      //         child: const Icon(
                      //           Icons.image_rounded,
                      //           color: Colors.grey,
                      //           size: 38,
                      //         ),
                      //       );
                      //     },
                      //   ),
                     : Image.network(
  imageUrl,
  fit: BoxFit.cover,
  headers: const {
    'User-Agent': 'Flutter',
  },
  errorBuilder: (context, error, stackTrace) {
    print('BANNER ERROR = $error');
    return const Icon(Icons.error);
  },
)
                ),
              );
            },
          ),
        ),

        const SizedBox(height: 6),

        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: List.generate(
            bannerData.length,
            (index) {
              final active = index == activeBannerIndex;

              return AnimatedContainer(
                duration: const Duration(milliseconds: 250),
                margin: const EdgeInsets.symmetric(horizontal: 3),
                height: 7,
                width: active ? 22 : 7,
                decoration: BoxDecoration(
                  color: active ? primaryRed : Colors.grey.shade300,
                  borderRadius: BorderRadius.circular(99),
                ),
              );
            },
          ),
        ),
      ],
    );
  }

  Widget _sectionTitle({
    required String title,
    required String action,
    VoidCallback? onTap,
  }) {
    return Row(
      children: [
        Expanded(
          child: Text(
            title,
            style: const TextStyle(
              color: textDark,
              fontSize: 19,
              fontWeight: FontWeight.w900,
              letterSpacing: -0.4,
            ),
          ),
        ),

        GestureDetector(
          onTap: onTap,
          child: Row(
            children: [
              Text(
                action,
                style: const TextStyle(
                  color: primaryRed,
                  fontSize: 12,
                  fontWeight: FontWeight.w900,
                ),
              ),

              const SizedBox(width: 5),

              const Icon(
                Icons.arrow_forward_ios_rounded,
                color: primaryRed,
                size: 13,
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildScheduleWidget() {
    if (isLoadingSchedule) {
      return Container(
        height: 90,
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(23),
        ),
        child: const Center(
          child: CircularProgressIndicator(color: primaryRed),
        ),
      );
    }

    if (scheduleData.isEmpty) {
      return Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(23),
          border: Border.all(color: const Color(0xFFFFE0E3)),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.03),
              blurRadius: 12,
              offset: const Offset(0, 6),
            ),
          ],
        ),
        child: Row(
          children: [
            Container(
              height: 46,
              width: 46,
              decoration: BoxDecoration(
                color: const Color(0xFFFFEEEE),
                borderRadius: BorderRadius.circular(15),
              ),
              child: const Icon(
                Icons.calendar_today_outlined,
                color: primaryRed,
                size: 22,
              ),
            ),
            const SizedBox(width: 14),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Tidak ada jadwal hari ini',
                  style: TextStyle(
                    color: textDark,
                    fontSize: 13,
                    fontWeight: FontWeight.w800,
                  ),
                ),
                const SizedBox(height: 3),
                Text(
                  'Jadwal dari guru akan muncul di sini',
                  style: TextStyle(
                    color: Colors.grey.shade500,
                    fontSize: 11,
                    fontWeight: FontWeight.w500,
                  ),
                ),
              ],
            ),
          ],
        ),
      );
    }

    final displayList = scheduleData.take(3).toList();

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(23),
        border: Border.all(color: const Color(0xFFFFCDD2), width: 1.2),
        boxShadow: [
          BoxShadow(
            color: primaryRed.withOpacity(0.07),
            blurRadius: 18,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Icon(Icons.calendar_month_rounded,
                  color: primaryRed, size: 15),
              const SizedBox(width: 6),
              Text(
                _getTodayLabel(),
                style: const TextStyle(
                  color: primaryRed,
                  fontSize: 11,
                  fontWeight: FontWeight.w800,
                ),
              ),
              const Spacer(),
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: const Color(0xFFFFEEEE),
                  borderRadius: BorderRadius.circular(99),
                ),
                child: Text(
                  '${scheduleData.length} jadwal',
                  style: const TextStyle(
                    color: primaryRed,
                    fontSize: 10,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ],
          ),

          const SizedBox(height: 12),

          ...displayList.asMap().entries.map((entry) {
            final index = entry.key;
            final item = entry.value as Map;
            final isLast = index == displayList.length - 1;

            return _buildScheduleItem(item, isLast: isLast);
          }),

          if (scheduleData.length > 3) ...[
            const SizedBox(height: 8),
            Center(
              child: Text(
                '+ ${scheduleData.length - 3} jadwal lainnya',
                style: TextStyle(
                  color: Colors.grey.shade500,
                  fontSize: 11,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildScheduleItem(Map item, {bool isLast = false}) {
    final subject = item['subject_name'] ?? 'Mata Pelajaran';
    final teacherName = item['teacher_name'] ?? '';
    final startTime = item['start_time'] ?? '';
    final endTime = item['end_time'] ?? '';
    final statusLabel = item['status_label'] ?? 'TERJADWAL';
    final statusColor = item['status_color'] ?? 'blue';

    Color dotColor;
    Color badgeBg;
    Color badgeText;
    String badgeLabel;

    switch (statusColor) {
      case 'green':
        dotColor = const Color(0xFF3B6D11);
        badgeBg = const Color(0xFFEAF3DE);
        badgeText = const Color(0xFF27500A);
        badgeLabel = 'Sedang berlangsung';
        break;
      case 'grey':
        dotColor = Colors.grey.shade500;
        badgeBg = const Color(0xFFF1EFE8);
        badgeText = const Color(0xFF444441);
        badgeLabel = 'Selesai';
        break;
      default:
        dotColor = const Color(0xFF185FA5);
        badgeBg = const Color(0xFFE6F1FB);
        badgeText = const Color(0xFF0C447C);
        badgeLabel = 'Terjadwal';
    }

    return Column(
      children: [
        Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Column(
              children: [
                Container(
                  height: 10,
                  width: 10,
                  margin: const EdgeInsets.only(top: 4),
                  decoration: BoxDecoration(
                    color: dotColor,
                    shape: BoxShape.circle,
                  ),
                ),
                if (!isLast)
                  Container(
                    width: 1.5,
                    height: 38,
                    color: const Color(0xFFFFCDD2),
                  ),
              ],
            ),

            const SizedBox(width: 12),

            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Expanded(
                        child: Text(
                          subject,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                            color: textDark,
                            fontSize: 13,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(
                          color: badgeBg,
                          borderRadius: BorderRadius.circular(99),
                        ),
                        child: Text(
                          badgeLabel,
                          style: TextStyle(
                            color: badgeText,
                            fontSize: 9,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(height: 3),

                  Row(
                    children: [
                      if (startTime.isNotEmpty) ...[
                        Icon(Icons.access_time_rounded,
                            size: 11, color: Colors.grey.shade500),
                        const SizedBox(width: 3),
                        Text(
                          endTime.isNotEmpty
                              ? '$startTime – $endTime'
                              : startTime,
                          style: TextStyle(
                            color: Colors.grey.shade600,
                            fontSize: 10,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                        const SizedBox(width: 8),
                      ],
                      if (teacherName.isNotEmpty) ...[
                        Icon(Icons.person_outline_rounded,
                            size: 11, color: Colors.grey.shade500),
                        const SizedBox(width: 3),
                        Expanded(
                          child: Text(
                            teacherName,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: TextStyle(
                              color: Colors.grey.shade600,
                              fontSize: 10,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ),
                      ],
                    ],
                  ),

                  if (!isLast) const SizedBox(height: 8),
                ],
              ),
            ),
          ],
        ),
      ],
    );
  }

  String _getTodayLabel() {
    final now = DateTime.now();
    final days = [
      'Minggu', 'Senin', 'Selasa', 'Rabu',
      'Kamis', 'Jumat', 'Sabtu'
    ];
    final months = [
      'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
      'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
    ];
    return '${days[now.weekday % 7]}, ${now.day} ${months[now.month - 1]} ${now.year}';
  }

  Widget _buildContinueLearningCard() {
    final className =
        currentData?['student']?['class']?['program_name'] ??
        'TPS Kuantitatif UTBK 2024';

    return Container(
      height: 172,
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Color(0xFFB90018),
            primaryRed,
            darkRed,
          ],
        ),
        borderRadius: BorderRadius.circular(23),
        boxShadow: [
          BoxShadow(
            color: primaryRed.withOpacity(0.20),
            blurRadius: 18,
            offset: const Offset(0, 9),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            height: 148,
            width: 88,
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(18),
              gradient: const LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [
                  Color(0xFF210206),
                  primaryRed,
                ],
              ),
              border: Border.all(
                color: Colors.white.withOpacity(0.18),
              ),
            ),
            child: Stack(
              children: [
                const Positioned(
                  top: 13,
                  left: 10,
                  right: 8,
                  child: Text(
                    'TPS\nKUANTITATIF',
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 12,
                      height: 1.0,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                ),
                Center(
                  child: Container(
                    height: 39,
                    width: 39,
                    decoration: const BoxDecoration(
                      color: Colors.white,
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(
                      Icons.play_arrow_rounded,
                      color: primaryRed,
                      size: 28,
                    ),
                  ),
                ),
                Positioned(
                  right: 0,
                  bottom: 10,
                  child: Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 8,
                      vertical: 5,
                    ),
                    decoration: const BoxDecoration(
                      color: Color(0xFFFF2D2D),
                      borderRadius: BorderRadius.horizontal(
                        left: Radius.circular(99),
                      ),
                    ),
                    child: const Text(
                      '75%',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 9,
                        fontWeight: FontWeight.w900,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(width: 12),

          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 5,
                  ),
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(99),
                    border: Border.all(
                      color: Colors.white.withOpacity(0.28),
                    ),
                  ),
                  child: const Text(
                    'Kelas',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 10,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                ),

                const SizedBox(height: 8),

                Text(
                  className,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 14.5,
                    fontWeight: FontWeight.w900,
                  ),
                ),

                const SizedBox(height: 6),

                const Text(
                  'Bab 3 - Persamaan Kuadrat',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                  ),
                ),

                const SizedBox(height: 13),

                Row(
                  children: [
                    Expanded(
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(99),
                        child: LinearProgressIndicator(
                          value: 0.75,
                          minHeight: 6,
                          backgroundColor: Colors.white24,
                          valueColor: const AlwaysStoppedAnimation(
                            Color(0xFFFF2D2D),
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),
                    const Text(
                      '12/16',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 10,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                  ],
                ),

                const SizedBox(height: 14),

                Align(
                  alignment: Alignment.centerRight,
                  child: InkWell(
                    onTap: _openClassIfEnrolled,
                    borderRadius: BorderRadius.circular(99),
                    child: Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 13,
                        vertical: 7,
                      ),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(99),
                      ),
                      child: const Text(
                        'Lanjutkan',
                        style: TextStyle(
                          color: primaryRed,
                          fontSize: 10.5,
                          fontWeight: FontWeight.w900,
                        ),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTryoutSection() {
    if (isLoadingTryout) {
      return Container(
        height: 130,
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(23),
        ),
        child: const Center(
          child: CircularProgressIndicator(color: primaryRed),
        ),
      );
    }

    if (tryoutData.isEmpty) {
      return _buildTryoutEmptyCard();
    }

    final firstTryout = tryoutData.first as Map;
    return _buildTryoutHighlightCard(firstTryout);
  }

  Widget _buildTryoutEmptyCard() {
    return GestureDetector(
      onTap: _handleTryout,
      child: Container(
        padding: const EdgeInsets.all(18),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(23),
          border: Border.all(color: const Color(0xFFFFE0E3)),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.04),
              blurRadius: 16,
              offset: const Offset(0, 8),
            ),
          ],
        ),
        child: Row(
          children: [
            Container(
              height: 54,
              width: 54,
              decoration: BoxDecoration(
                color: const Color(0xFFFFEEEE),
                borderRadius: BorderRadius.circular(18),
              ),
              child: const Icon(
                Icons.assignment_outlined,
                color: primaryRed,
                size: 28,
              ),
            ),

            const SizedBox(width: 14),

            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Tryout Belum Tersedia',
                    style: TextStyle(
                      color: textDark,
                      fontSize: 14,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'Pantau terus untuk jadwal tryout berikutnya',
                    style: TextStyle(
                      color: Colors.grey.shade600,
                      fontSize: 11,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
            ),

            const Icon(
              Icons.chevron_right_rounded,
              color: primaryRed,
              size: 24,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTryoutHighlightCard(Map tryout) {
    final title = tryout['title'] ?? tryout['name'] ?? 'Tryout UTBK';
    final description =
        tryout['description'] ?? tryout['subtitle'] ?? 'Simulasi ujian lengkap';
    final totalSoal =
        tryout['total_questions'] ?? tryout['soal_count'] ?? '-';
    final duration = tryout['duration'] ?? tryout['waktu'] ?? '-';
    final isFree =
        (tryout['is_free'] == true || tryout['price'] == 0);

    return GestureDetector(
      onTap: _handleTryout,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(23),
          border: Border.all(color: const Color(0xFFFFCDD2), width: 1.2),
          boxShadow: [
            BoxShadow(
              color: primaryRed.withOpacity(0.08),
              blurRadius: 18,
              offset: const Offset(0, 8),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: primaryRed,
                    borderRadius: BorderRadius.circular(99),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: const [
                      Icon(Icons.local_fire_department_rounded,
                          color: Colors.white, size: 12),
                      SizedBox(width: 4),
                      Text(
                        'Mulai Sekarang',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 10,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                    ],
                  ),
                ),

                const Spacer(),

                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: isFree
                        ? const Color(0xFFE8F5E9)
                        : const Color(0xFFFFF8E1),
                    borderRadius: BorderRadius.circular(99),
                  ),
                  child: Text(
                    isFree ? 'Gratis' : 'Berbayar',
                    style: TextStyle(
                      color: isFree
                          ? const Color(0xFF2E7D32)
                          : const Color(0xFFF57C00),
                      fontSize: 10,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                ),
              ],
            ),

            const SizedBox(height: 10),

            Text(
              title,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(
                color: textDark,
                fontSize: 15,
                fontWeight: FontWeight.w900,
              ),
            ),

            const SizedBox(height: 4),

            Text(
              description,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: TextStyle(
                color: Colors.grey.shade600,
                fontSize: 11,
                fontWeight: FontWeight.w600,
              ),
            ),

            const SizedBox(height: 12),

            Row(
              children: [
                _tryoutMetaChip(Icons.quiz_outlined, '$totalSoal soal'),
                const SizedBox(width: 8),
                _tryoutMetaChip(Icons.timer_outlined, '$duration menit'),
                const Spacer(),
                Container(
                  padding: const EdgeInsets.symmetric(
                      horizontal: 16, vertical: 7),
                  decoration: BoxDecoration(
                    color: primaryRed,
                    borderRadius: BorderRadius.circular(99),
                  ),
                  child: const Text(
                    'Mulai',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 11,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _tryoutMetaChip(IconData icon, String label) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 5),
      decoration: BoxDecoration(
        color: const Color(0xFFF5F5F5),
        borderRadius: BorderRadius.circular(99),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 12, color: Colors.grey.shade700),
          const SizedBox(width: 4),
          Text(
            label,
            style: TextStyle(
              fontSize: 10,
              fontWeight: FontWeight.w700,
              color: Colors.grey.shade700,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMainMenuGrid() {
    final menus = [
      {
        'title': 'Learning\nMaterials',
        'subtitle': 'Materi lengkap',
        'icon': Icons.menu_book_rounded,
        'gradient': [
          const Color(0xFFFF512F),
          const Color(0xFFFF8A65),
        ],
      },
      {
        'title': 'Dedicated\nTutor',
        'subtitle': 'Tutor pilihan',
        'icon': Icons.person_rounded,
        'gradient': [
          const Color(0xFF5B45F1),
          const Color(0xFF8B7CF6),
        ],
      },
      {
        'title': 'Tryout',
        'subtitle': 'Simulasi ujian',
        'icon': Icons.assignment_outlined,
        'gradient': [
          const Color(0xFFD32F2F),
          const Color(0xFFEF9A9A),
        ],
      },
      {
        'title': 'Question\nBank',
        'subtitle': 'Bank soal',
        'icon': Icons.history_edu_rounded,
        'gradient': [
          const Color(0xFF00A873),
          const Color(0xFF4ADE80),
        ],
      },
      {
        'title': 'About\nSpekta',
        'subtitle': 'Tentang kami',
        'icon': Icons.info_outline_rounded,
        'gradient': [
          const Color(0xFF1769E8),
          const Color(0xFF60A5FA),
        ],
      },
      {
        'title': 'Consultation',
        'subtitle': 'Konsultasi',
        'icon': Icons.chat_rounded,
        'gradient': [
          const Color(0xFFE0003D),
          const Color(0xFFFF4D6D),
        ],
      },
      {
        'title': 'Support\nCenter',
        'subtitle': 'Pusat bantuan',
        'icon': Icons.support_agent_rounded,
        'gradient': [
          const Color(0xFF475569),
          const Color(0xFF94A3B8),
        ],
      },
    ];

    return GridView.builder(
      shrinkWrap: true,
      padding: EdgeInsets.zero,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: menus.length,
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        mainAxisSpacing: 13,
        crossAxisSpacing: 13,
        childAspectRatio: 2.35,
      ),
      itemBuilder: (context, index) {
        final item = menus[index];
        final gradients = item['gradient'] as List<Color>;

        return InkWell(
          borderRadius: BorderRadius.circular(21),
          onTap: () {
            _handleMenuTap(
              item['title'].toString().replaceAll('\n', ' '),
            );
          },
          child: Container(
            padding: const EdgeInsets.all(11),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(21),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.045),
                  blurRadius: 14,
                  offset: const Offset(0, 7),
                ),
              ],
            ),
            child: Row(
              children: [
                Container(
                  height: 48,
                  width: 48,
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      colors: gradients,
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                    ),
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: Icon(
                    item['icon'] as IconData,
                    color: Colors.white,
                    size: 25,
                  ),
                ),

                const SizedBox(width: 10),

                Expanded(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        item['title'].toString(),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                          color: textDark,
                          fontSize: 12.5,
                          height: 1.05,
                          fontWeight: FontWeight.w900,
                        ),
                      ),

                      const SizedBox(height: 5),

                      Text(
                        item['subtitle'].toString(),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: TextStyle(
                          color: Colors.grey.shade600,
                          fontSize: 10,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildUpcomingClassCard() {
    return Container(
      padding: const EdgeInsets.all(13),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(23),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.045),
            blurRadius: 16,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 68,
            height: 86,
            decoration: BoxDecoration(
              color: const Color(0xFFFFEEEE),
              borderRadius: BorderRadius.circular(19),
            ),
            child: const Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  'JUN',
                  style: TextStyle(
                    color: primaryRed,
                    fontSize: 11,
                    fontWeight: FontWeight.w900,
                  ),
                ),
                SizedBox(height: 4),
                Text(
                  '10',
                  style: TextStyle(
                    color: textDark,
                    fontSize: 26,
                    fontWeight: FontWeight.w900,
                  ),
                ),
                Text(
                  'Sabtu',
                  style: TextStyle(
                    color: Colors.grey,
                    fontSize: 11,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(width: 13),

          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 9,
                    vertical: 4,
                  ),
                  decoration: BoxDecoration(
                    color: const Color(0xFFEEE7FF),
                    borderRadius: BorderRadius.circular(99),
                  ),
                  child: const Text(
                    'LIVE CLASS',
                    style: TextStyle(
                      color: Color(0xFF5B21B6),
                      fontSize: 9,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                ),

                const SizedBox(height: 7),

                const Text(
                  'Matematika Dasar',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    color: textDark,
                    fontSize: 15,
                    fontWeight: FontWeight.w900,
                  ),
                ),

                const SizedBox(height: 5),

                Row(
                  children: [
                    Icon(
                      Icons.access_time_rounded,
                      color: Colors.grey.shade600,
                      size: 15,
                    ),
                    const SizedBox(width: 5),
                    Text(
                      '19.00 - 20.30 WIB',
                      style: TextStyle(
                        color: Colors.grey.shade700,
                        fontSize: 12,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ],
                ),

                const SizedBox(height: 4),

                Text(
                  'Bersama Kak Alif',
                  style: TextStyle(
                    color: Colors.grey.shade600,
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }



  void _openClassIfEnrolled() {
    _handleLearningMaterials();
  }

  void _handleMenuTap(String title) {
    switch (title) {
      case 'Learning Materials':
        _handleLearningMaterials();
        break;

      case 'Dedicated Tutor':
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (c) => DedicatedTutorPage(
              token: widget.token,
              userData: currentData ?? widget.userData,
            ),
          ),
        );
        break;

      case 'Tryout':
        _handleTryout();
        break;

      case 'Question Bank':
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (c) => QuestionSharingPage(
              token: widget.token,
            ),
          ),
        );
        break;

      case 'About Spekta':
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (c) => const AboutAcademyPage(),
          ),
        );
        break;

      case 'Consultation':
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (c) => const ConsultationPage(),
          ),
        );
        break;

      case 'Support Center':
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (c) => const SupportCenterPage(),
          ),
        );
        break;
    }
  }

  void _showWarning(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        backgroundColor: primaryRed,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(14),
        ),
        content: Text(message),
      ),
    );
  }
}


