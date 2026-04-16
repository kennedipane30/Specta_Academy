import 'package:flutter/material.dart';
import 'dart:convert';
import '../services/auth_service.dart';
import 'edit_profile_page.dart';
import 'login_page.dart';

class AkunPage extends StatefulWidget {
  final String token;
  final Map userData;

  const AkunPage({super.key, required this.token, required this.userData});

  @override
  State<AkunPage> createState() => _AkunPageState();
}

class _AkunPageState extends State<AkunPage> {
  late Map currentData;
  bool isLoading = false;
  final Color spektaRed = const Color(0xFF990000);

  @override
  void initState() {
    super.initState();
    currentData = widget.userData;
    _refreshProfile();
  }

  Future<void> _refreshProfile() async {
    if (!mounted) return;
    setState(() => isLoading = true);
    
    try {
      final response = await AuthService.getUserProfile(widget.token);
      if (response != null && response['user'] != null) {
        setState(() {
          // Laravel mengirim data di dalam { 'user': { ... } }
          currentData = response['user'];
        });
      }
    } catch (e) {
      debugPrint("Refresh Error: $e");
    } finally {
      if (mounted) setState(() => isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    // MODIFIKASI: Laravel mengubah camelCase 'tryoutResults' menjadi snake_case 'tryout_results'
    List results = currentData['tryout_results'] ?? [];

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: RefreshIndicator(
        onRefresh: _refreshProfile,
        color: spektaRed,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(
            children: [
              _buildHeader(),
              Padding(
                padding: const EdgeInsets.all(25),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text("Account Settings", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
                    const SizedBox(height: 15),
                    _buildMenuTile(
                      "Complete Personal Data", 
                      "Update address and parent info", 
                      Icons.badge_outlined, 
                      () => Navigator.push(context, MaterialPageRoute(builder: (context) => EditProfilePage(userData: currentData, token: widget.token)))
                    ),
                    const SizedBox(height: 35),
                    const Text("My Tryout Results", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
                    const SizedBox(height: 15),

                    // LIST NILAI
                    isLoading && results.isEmpty
                        ? const Center(child: CircularProgressIndicator())
                        : results.isEmpty
                            ? _buildEmptyState()
                            : ListView.builder(
                                shrinkWrap: true,
                                physics: const NeverScrollableScrollPhysics(),
                                itemCount: results.length,
                                itemBuilder: (context, index) {
                                  var res = results[index];
                                  return Card(
                                    margin: const EdgeInsets.only(bottom: 12),
                                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                                    child: ListTile(
                                      leading: const CircleAvatar(backgroundColor: Color(0xFFFFEEEE), child: Icon(Icons.stars, color: Colors.red)),
                                      title: Text(res['tryout']?['title'] ?? "Tryout Simulation", style: const TextStyle(fontWeight: FontWeight.bold)),
                                      subtitle: Text("Score: ${res['score']}", style: TextStyle(color: spektaRed, fontWeight: FontWeight.bold, fontSize: 16)),
                                      trailing: Text(res['created_at'].toString().substring(0, 10), style: const TextStyle(fontSize: 10, color: Colors.grey)),
                                    ),
                                  );
                                },
                              ),
                    const SizedBox(height: 30),
                    Center(
                      child: TextButton.icon(
                        onPressed: () => Navigator.pushAndRemoveUntil(context, MaterialPageRoute(builder: (c) => const LoginPage()), (r) => false),
                        icon: const Icon(Icons.logout, color: Colors.redAccent),
                        label: const Text("SIGN OUT", style: TextStyle(color: Colors.redAccent, fontWeight: FontWeight.bold)),
                      ),
                    ),
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
    return Container(
      height: 220, width: double.infinity,
      decoration: BoxDecoration(color: spektaRed, borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(50), bottomRight: Radius.circular(50))),
      child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
        const CircleAvatar(radius: 40, backgroundColor: Colors.white, child: Icon(Icons.person, size: 50, color: Color(0xFF990000))),
        const SizedBox(height: 10),
        Text(currentData['name'] ?? "Student", style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold)),
        Text(currentData['email'] ?? "", style: const TextStyle(color: Colors.white70)),
      ]),
    );
  }

  Widget _buildMenuTile(String t, String s, IconData i, VoidCallback o) {
    return InkWell(onTap: o, child: Container(padding: const EdgeInsets.all(18), decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20)), child: Row(children: [Icon(i, color: spektaRed), const SizedBox(width: 15), Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [Text(t, style: const TextStyle(fontWeight: FontWeight.bold)), Text(s, style: const TextStyle(color: Colors.grey, fontSize: 11))])), const Icon(Icons.arrow_forward_ios, size: 14, color: Colors.grey)])));
  }

  Widget _buildEmptyState() {
    return Container(
      width: double.infinity, padding: const EdgeInsets.all(30),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(25)),
      child: const Column(children: [
        Icon(Icons.hourglass_empty, color: Colors.grey),
        SizedBox(height: 10),
        Text("No results found. Pull down to refresh.", style: TextStyle(color: Colors.grey, fontSize: 12, fontStyle: FontStyle.italic)), // FIX: FontStyle
      ]),
    );
  }
}