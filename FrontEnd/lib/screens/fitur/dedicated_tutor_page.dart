import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/auth_service.dart';
import '../../services/tutor_service.dart';

class DedicatedTutorPage extends StatefulWidget {
  const DedicatedTutorPage({super.key});

  @override
  State<DedicatedTutorPage> createState() => _DedicatedTutorPageState();
}

class _DedicatedTutorPageState extends State<DedicatedTutorPage> {
  final Color spektaRed = const Color(0xFF990000);
  int usedQuota = 0;
  int maxQuota = 3;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadQuota();
  }

  // Fungsi mengambil sisa kuota dari Backend
  Future<void> _loadQuota() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token') ?? '';
      final res = await TutorService.getTutorData(token);
      
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        setState(() {
          usedQuota = data['used_quota'] ?? 0;
          maxQuota = data['max_quota'] ?? 3;
          isLoading = false;
        });
      }
    } catch (e) {
      setState(() => isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    int remaining = maxQuota - usedQuota;

    return Scaffold(
      appBar: AppBar(
        title: const Text("Dedicated Tutor", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        backgroundColor: spektaRed,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: isLoading 
      ? const Center(child: CircularProgressIndicator())
      : Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // --- TAMPILAN KUOTA ---
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 15),
              margin: const EdgeInsets.symmetric(horizontal: 40),
              decoration: BoxDecoration(
                color: remaining > 0 ? Colors.green[50] : Colors.red[50],
                borderRadius: BorderRadius.circular(15),
                border: Border.all(color: remaining > 0 ? Colors.green : Colors.red),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.info_outline, color: remaining > 0 ? Colors.green : Colors.red),
                  const SizedBox(width: 10),
                  Text(
                    "Sisa Kuota: $remaining / $maxQuota Sesi",
                    style: TextStyle(
                      fontWeight: FontWeight.w900, 
                      fontSize: 16,
                      color: remaining > 0 ? Colors.green[800] : Colors.red[800]
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 40),

            // --- TOMBOL HISTORI ---
            _buildMenuCard(
              title: "Histori Pengajuan",
              icon: Icons.history_rounded,
              onTap: () => Navigator.push(context, MaterialPageRoute(builder: (c) => const TutorHistoryPage())),
            ),
            
            const SizedBox(height: 20),

            // --- TOMBOL AJUKAN (Nonaktif jika kuota 0) ---
            _buildMenuCard(
              title: "Ajukan Tutor",
              icon: Icons.add_task_rounded,
              isEnabled: remaining > 0,
              onTap: remaining > 0 
                ? () => Navigator.push(context, MaterialPageRoute(builder: (c) => const DedicatedTutorFormPage())).then((_) => _loadQuota())
                : null,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMenuCard({required String title, required IconData icon, required VoidCallback? onTap, bool isEnabled = true}) {
    return Opacity(
      opacity: isEnabled ? 1.0 : 0.5,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(20),
        child: Container(
          width: 250,
          padding: const EdgeInsets.all(25),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 5))],
          ),
          child: Column(
            children: [
              Icon(icon, size: 50, color: spektaRed),
              const SizedBox(height: 15),
              Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
              if (!isEnabled) const Text("(Kuota Habis)", style: TextStyle(color: Colors.red, fontSize: 12)),
            ],
          ),
        ),
      ),
    );
  }
}

// ==========================================
// HALAMAN FORM PENGAJUAN
// ==========================================
class DedicatedTutorFormPage extends StatefulWidget {
  const DedicatedTutorFormPage({super.key});
  @override State<DedicatedTutorFormPage> createState() => _DedicatedTutorFormPageState();
}

class _DedicatedTutorFormPageState extends State<DedicatedTutorFormPage> {
  final _formKey = GlobalKey<FormState>();
  Map? userData;
  List teachers = [], materials = [];
  bool isLoading = true;

  int? selTeacher, selMaterial;
  DateTime? selDate;
  TimeOfDay? selTime;

  @override
  void initState() {
    super.initState();
    _initData();
  }

  _initData() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token') ?? '';
    final profile = await AuthService.getUserProfile(token);
    final res = await TutorService.getTutorData(token);
    final data = jsonDecode(res.body);

    setState(() {
      userData = profile;
      teachers = data['teachers'];
      materials = data['materials'];
      isLoading = false;
    });
  }

  _submit() async {
    if (!_formKey.currentState!.validate() || selDate == null || selTime == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Lengkapi semua data!")));
      return;
    }

    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token') ?? '';

    final body = {
      'teacher_id': selTeacher.toString(),
      'material_id': selMaterial.toString(),
      'date': DateFormat('yyyy-MM-dd').format(selDate!),
      'time': "${selTime!.hour.toString().padLeft(2, '0')}:${selTime!.minute.toString().padLeft(2, '0')}",
    };

    final res = await TutorService.submitTutor(body, token);
    if (res.statusCode == 200) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(backgroundColor: Colors.green, content: Text("Berhasil diajukan!")));
    } else {
      final msg = jsonDecode(res.body)['message'];
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(backgroundColor: Colors.red, content: Text(msg ?? "Gagal mengajukan")));
    }
  }

  @override
  Widget build(BuildContext context) {
    if (isLoading) return const Scaffold(body: Center(child: CircularProgressIndicator()));

    return Scaffold(
      appBar: AppBar(title: const Text("Form Pengajuan Tutor")),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(25),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text("Data Siswa (Otomatis)", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey)),
              const SizedBox(height: 10),
              TextFormField(initialValue: userData?['name'], readOnly: true, decoration: const InputDecoration(labelText: "Nama Siswa", border: OutlineInputBorder())),
              const SizedBox(height: 15),
              TextFormField(initialValue: userData?['student']?['nisn'] ?? '-', readOnly: true, decoration: const InputDecoration(labelText: "NISN", border: OutlineInputBorder())),
              
              const Padding(padding: EdgeInsets.symmetric(vertical: 20), child: Divider()),

              DropdownButtonFormField<int>(
                decoration: const InputDecoration(labelText: "Pilih Materi", border: OutlineInputBorder()),
                items: materials.map((m) => DropdownMenuItem<int>(value: m['materialsID'], child: Text(m['nama_materi']))).toList(),
                onChanged: (v) => selMaterial = v,
              ),
              const SizedBox(height: 15),
              DropdownButtonFormField<int>(
                decoration: const InputDecoration(labelText: "Pilih Pengajar", border: OutlineInputBorder()),
                items: teachers.map((t) => DropdownMenuItem<int>(value: t['usersID'], child: Text(t['name']))).toList(),
                onChanged: (v) => selTeacher = v,
              ),
              const SizedBox(height: 15),
              
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      icon: const Icon(Icons.event),
                      label: Text(selDate == null ? "Tanggal" : DateFormat('dd/MM/yy').format(selDate!)),
                      onPressed: () async {
                        final d = await showDatePicker(context: context, initialDate: DateTime.now(), firstDate: DateTime.now(), lastDate: DateTime.now().add(const Duration(days: 14)));
                        if (d != null) setState(() => selDate = d);
                      },
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: OutlinedButton.icon(
                      icon: const Icon(Icons.schedule),
                      label: Text(selTime == null ? "Jam" : selTime!.format(context)),
                      onPressed: () async {
                        final t = await showTimePicker(context: context, initialTime: TimeOfDay.now());
                        if (t != null) setState(() => selTime = t);
                      },
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 40),
              SizedBox(
                width: double.infinity, height: 50,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF990000), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))),
                  onPressed: _submit,
                  child: const Text("Ajukan Sekarang", style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)),
                ),
              )
            ],
          ),
        ),
      ),
    );
  }
}

// ==========================================
// HALAMAN HISTORI
// ==========================================
class TutorHistoryPage extends StatefulWidget {
  const TutorHistoryPage({super.key});
  @override State<TutorHistoryPage> createState() => _TutorHistoryPageState();
}

class _TutorHistoryPageState extends State<TutorHistoryPage> {
  List history = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetch();
  }

  _fetch() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token') ?? '';
    final res = await TutorService.getTutorData(token);
    setState(() {
      history = jsonDecode(res.body)['history'];
      isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Histori Pengajuan")),
      body: isLoading 
      ? const Center(child: CircularProgressIndicator())
      : ListView.builder(
          padding: const EdgeInsets.all(15),
          itemCount: history.length,
          itemBuilder: (context, index) {
            final item = history[index];
            String status = item['status'].toString().toUpperCase();
            Color color = status == 'CONFIRMED' ? Colors.green : (status == 'REJECTED' ? Colors.red : Colors.orange);
            
            return Card(
              margin: const EdgeInsets.bottom(12),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
              child: ListTile(
                contentPadding: const EdgeInsets.all(15),
                title: Text(item['material']['nama_materi'], style: const TextStyle(fontWeight: FontWeight.bold)),
                subtitle: Text("Pengajar: ${item['teacher']['name']}\nJadwal: ${item['date']} | ${item['time']}"),
                trailing: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                  decoration: BoxDecoration(color: color, borderRadius: BorderRadius.circular(8)),
                  child: Text(status, style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold)),
                ),
              ),
            );
          },
        ),
    );
  }
}