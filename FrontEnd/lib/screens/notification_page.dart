import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';

class NotificationPage extends StatefulWidget {
  final String token;
  const NotificationPage({super.key, required this.token});

  @override
  State<NotificationPage> createState() => _NotificationPageState();
}

class _NotificationPageState extends State<NotificationPage> {
  // GUNAKAN IP INI UNTUK EMULATOR ANDROID
  final String baseUrl = 'http://10.0.2.2:8000';
  List notifications = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    fetchNotifications();
  }

  // Perbaikan: Tambahkan pengecekan mounted untuk mencegah memory leak
  Future<void> fetchNotifications() async {
    try {
      if (!mounted) return;
      setState(() => isLoading = true);

      final response = await http.get(
        Uri.parse('$baseUrl/api/notifications'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer ${widget.token}',
        },
      );

      // ✨ CRITICAL: Cek apakah widget masih terpasang sebelum setState
      if (!mounted) return;

      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        setState(() {
          // Laravel mengirim data dalam field 'data'
          notifications = decoded['data'] ?? [];
          isLoading = false;
        });
        
        // Panggil API mark-all-read agar angka di lonceng hilang
        _markAllRead();
      } else {
        setState(() => isLoading = false);
      }
    } catch (e) {
      debugPrint("ERROR FETCH NOTIF: $e");
      if (!mounted) return; // ✨ Cek mounted di dalam catch juga
      setState(() => isLoading = false);
    }
  }

  Future<void> _markAllRead() async {
    try {
      await http.post(
        Uri.parse('$baseUrl/api/notifications/mark-all-read'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer ${widget.token}',
        },
      );
    } catch (e) {
      debugPrint("ERROR MARK READ: $e");
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text(
          "Notifikasi", 
          style: TextStyle(color: Color(0xFF9C0412), fontWeight: FontWeight.bold)
        ),
        backgroundColor: Colors.white,
        elevation: 0.5,
        iconTheme: const IconThemeData(color: Color(0xFF9C0412)),
      ),
      body: RefreshIndicator(
        color: const Color(0xFF9C0412),
        onRefresh: fetchNotifications,
        child: isLoading
            ? const Center(child: CircularProgressIndicator(color: Color(0xFF9C0412)))
            : notifications.isEmpty
                ? _buildEmptyState()
                : _buildList(),
      ),
    );
  }

  Widget _buildEmptyState() {
    return ListView( // Gunakan ListView agar RefreshIndicator tetap bekerja
      children: [
        SizedBox(height: MediaQuery.of(context).size.height * 0.3),
        const Center(
          child: Column(
            children: [
              Icon(Icons.notifications_off_outlined, size: 80, color: Colors.grey),
              SizedBox(height: 10),
              Text(
                "Belum ada notifikasi baru", 
                style: TextStyle(color: Colors.grey, fontWeight: FontWeight.w600)
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildList() {
    return ListView.builder(
      itemCount: notifications.length,
      padding: const EdgeInsets.symmetric(vertical: 10),
      itemBuilder: (context, index) {
        final item = notifications[index];
        final content = item['data']; 
        
        return Container(
          margin: const EdgeInsets.symmetric(horizontal: 15, vertical: 5),
          decoration: BoxDecoration(
            color: item['read_at'] == null ? const Color(0xFFFFF5F5) : Colors.white,
            borderRadius: BorderRadius.circular(12),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.03),
                blurRadius: 5,
                offset: const Offset(0, 2),
              )
            ],
          ),
          child: ListTile(
            leading: CircleAvatar(
              backgroundColor: const Color(0xFFFFEEEE),
              child: Icon(
                item['read_at'] == null ? Icons.notifications_active : Icons.notifications, 
                color: const Color(0xFF9C0412)
              ),
            ),
            title: Text(
              content['title'] ?? 'Notifikasi', 
              style: TextStyle(
                fontWeight: item['read_at'] == null ? FontWeight.bold : FontWeight.normal,
                fontSize: 15
              )
            ),
            subtitle: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 4),
                Text(
                  content['message'] ?? '',
                  style: const TextStyle(fontSize: 13, color: Colors.black87),
                ),
                const SizedBox(height: 6),
                Text(
                  _formatDate(item['created_at']),
                  style: const TextStyle(fontSize: 10, color: Colors.grey),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  String _formatDate(String dateStr) {
    try {
      final DateTime date = DateTime.parse(dateStr).toLocal();
      return DateFormat('dd MMM yyyy, HH:mm').format(date);
    } catch (e) {
      return dateStr;
    }
  }
}