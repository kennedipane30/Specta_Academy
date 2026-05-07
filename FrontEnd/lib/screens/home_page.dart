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

import 'class_detail_page.dart';

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

  bool isEnrolled = false;
  bool isLoadingBanner = false;

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
      if (mounted) {
        setState(() => isLoadingBanner = false);
      }
    }
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
                    const SizedBox(height: 12),

                    _sectionTitle(
                      title: 'Lanjutkan Belajar',
                      action: 'Lihat Semua',
                    ),

                    const SizedBox(height: 14),

                    _buildContinueLearningCard(),

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

                    _buildAnnouncementCard(),
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
              _buildGlassButton(Icons.notifications_none_rounded),
              Positioned(
                top: -6,
                right: -4,
                child: Container(
                  height: 22,
                  width: 22,
                  alignment: Alignment.center,
                  decoration: const BoxDecoration(
                    color: Color(0xFFFF2D2D),
                    shape: BoxShape.circle,
                  ),
                  child: const Text(
                    '3',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 11,
                      fontWeight: FontWeight.w900,
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
                      : Image.network(
                          imageUrl,
                          fit: BoxFit.cover,
                          errorBuilder: (_, __, ___) {
                            return Container(
                              color: const Color(0xFFE5E7EB),
                              child: const Icon(
                                Icons.image_rounded,
                                color: Colors.grey,
                                size: 38,
                              ),
                            );
                          },
                        ),
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
    );
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

  Widget _buildMainMenuGrid() {
    final menus = [
      {
        'title': 'Learning\nMaterials',
        'subtitle': 'Materi lengkap',
        'icon': Icons.menu_book_rounded,
        'gradient': [
          Color(0xFFFF512F),
          Color(0xFFFF8A65),
        ],
      },
      {
        'title': 'Dedicated\nTutor',
        'subtitle': 'Tutor pilihan',
        'icon': Icons.person_rounded,
        'gradient': [
          Color(0xFF5B45F1),
          Color(0xFF8B7CF6),
        ],
      },
      {
        'title': 'Question\nBank',
        'subtitle': 'Bank soal',
        'icon': Icons.history_edu_rounded,
        'gradient': [
          Color(0xFF00A873),
          Color(0xFF4ADE80),
        ],
      },
      {
        'title': 'About\nSpekta',
        'subtitle': 'Tentang kami',
        'icon': Icons.info_outline_rounded,
        'gradient': [
          Color(0xFF1769E8),
          Color(0xFF60A5FA),
        ],
      },
      {
        'title': 'Consultation',
        'subtitle': 'Konsultasi',
        'icon': Icons.chat_rounded,
        'gradient': [
          Color(0xFFE0003D),
          Color(0xFFFF4D6D),
        ],
      },
      {
        'title': 'Support\nCenter',
        'subtitle': 'Pusat bantuan',
        'icon': Icons.support_agent_rounded,
        'gradient': [
          Color(0xFF475569),
          Color(0xFF94A3B8),
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

  Widget _buildAnnouncementCard() {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [
            Color(0xFFFFEEEE),
            Colors.white,
          ],
        ),
        borderRadius: BorderRadius.circular(21),
      ),
      child: Row(
        children: [
          Container(
            height: 48,
            width: 48,
            decoration: BoxDecoration(
              color: primaryRed.withOpacity(0.12),
              borderRadius: BorderRadius.circular(16),
            ),
            child: const Icon(
              Icons.campaign_rounded,
              color: primaryRed,
              size: 25,
            ),
          ),

          const SizedBox(width: 12),

          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Pengumuman',
                  style: TextStyle(
                    color: textDark,
                    fontSize: 14,
                    fontWeight: FontWeight.w900,
                  ),
                ),
                const SizedBox(height: 3),
                Text(
                  'Jadwal Tryout UTBK Gelombang 2 telah tersedia!',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    color: Colors.grey.shade700,
                    fontSize: 11.5,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ],
            ),
          ),

          Icon(
            Icons.chevron_right_rounded,
            color: Colors.grey.shade500,
            size: 25,
          ),
        ],
      ),
    );
  }

  void _openClassIfEnrolled() {
    if (isEnrolled && currentData?['student'] != null) {
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (context) => ClassDetailPage(
            classId: int.parse(
              currentData!['student']['class_id'].toString(),
            ),
            className: currentData!['student']['class']?['program_name'] ??
                'Spekta Class',
            token: widget.token,
            userData: currentData!,
          ),
        ),
      );
    } else {
      _showWarning(
        'Kamu belum terdaftar di kelas mana pun. Daftar kelas dulu ya!',
      );
    }
  }

  void _handleMenuTap(String title) {
    switch (title) {
      case 'Learning Materials':
        _openClassIfEnrolled();
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