import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/tutor_service.dart';

class DedicatedTutorPage extends StatefulWidget {
  const DedicatedTutorPage({super.key});
  @override State<DedicatedTutorPage> createState() => _DedicatedTutorPageState();
}

class _DedicatedTutorPageState extends State<DedicatedTutorPage> {
  int usedQuota = 0, maxQuota = 3;
  bool isLoading = true;

  @override
  void initState() { super.initState(); _fetch(); }

  _fetch() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token') ?? '';
      final res = await TutorService.getTutorData(token);
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        setState(() {
          usedQuota = data['used_quota'] ?? 0;
          maxQuota = data['max_quota'] ?? 3;
        });
      }
    } catch (e) {
      debugPrint("Gagal load: $e");
    } finally {
      // INI KUNCINYA: Apa pun yang terjadi, spinner akan mati
      if (mounted) setState(() => isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    int rem = maxQuota - usedQuota;
    return Scaffold(
      appBar: AppBar(
        title: const Text("Tutor", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
        backgroundColor: const Color(0xFF990000),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: isLoading 
      ? const Center(child: CircularProgressIndicator(color: Color(0xFF990000))) 
      : Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Center(child: Text("Sisa Kuota: $rem / $maxQuota", style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold))),
            const SizedBox(height: 30),
            _btn("Histori Tutor", Icons.history, () => Navigator.push(context, MaterialPageRoute(builder: (c) => const TutorHistoryPage()))),
            const SizedBox(height: 20),
            _btn("Ajukan Tutor", Icons.add_task, rem <= 0 ? null : () => Navigator.push(context, MaterialPageRoute(builder: (c) => const TutorFormPage())).then((_) => _fetch())),
          ],
        ),
    );
  }

  Widget _btn(String t, IconData i, VoidCallback? f) => Opacity(
    opacity: f == null ? 0.5 : 1.0,
    child: InkWell(
      onTap: f,
      child: Container(
        width: 250, padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 5)]),
        child: Column(children: [Icon(i, size: 40, color: const Color(0xFF990000)), const SizedBox(height: 10), Text(t, style: const TextStyle(fontWeight: FontWeight.bold))]),
      ),
    ),
  );
}

// --- HALAMAN FORM ---
class TutorFormPage extends StatefulWidget {
  const TutorFormPage({super.key});
  @override State<TutorFormPage> createState() => _TutorFormPageState();
}

class _TutorFormPageState extends State<TutorFormPage> {
  final nameCtrl = TextEditingController();
  final nisnCtrl = TextEditingController();
  List tea = [], mat = [];
  bool load = true;
  int? sM, sT; DateTime? sD;

  @override
  void initState() { super.initState(); _init(); }

  _init() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token') ?? '';
      final res = await TutorService.getTutorData(token);
      
      if (res.statusCode == 200) {
        final Map<String, dynamic> data = jsonDecode(res.body);
        
        setState(() {
          // AUTOFILL NAMA & NISN
          nameCtrl.text = data['user_data']['name']?.toString() ?? "";
          nisnCtrl.text = data['user_data']['nisn']?.toString() ?? "";
          
          // ISI DROPDOWN
          tea = data['teachers'] as List? ?? [];
          mat = data['materials'] as List? ?? [];
        });
      } else {
        print("Gagal ambil data: ${res.statusCode}");
      }
    } catch (e) {
      print("Error Flutter Master: $e");
    } finally {
      if (mounted) setState(() => load = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (load) return const Scaffold(body: Center(child: CircularProgressIndicator()));
    return Scaffold(
      appBar: AppBar(title: const Text("Ajukan Tutor")),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            TextField(controller: nameCtrl, readOnly: true, decoration: const InputDecoration(labelText: "Nama Siswa", border: OutlineInputBorder(), filled: true, fillColor: Color(0xFFEEEEEE))),
            const SizedBox(height: 15),
            TextField(controller: nisnCtrl, readOnly: true, decoration: const InputDecoration(labelText: "NISN", border: OutlineInputBorder(), filled: true, fillColor: Color(0xFFEEEEEE))),
            const SizedBox(height: 15),
            DropdownButtonFormField<int>(
              hint: const Text("Pilih Materi"),
              items: mat.map((e) => DropdownMenuItem<int>(value: e['materialsID'], child: Text(e['nama_materi'] ?? "Materi"))).toList(),
              onChanged: (v) => setState(() => sM = v),
              decoration: const InputDecoration(border: OutlineInputBorder()),
            ),
            const SizedBox(height: 15),
            DropdownButtonFormField<int>(
              hint: const Text("Pilih Pengajar"),
              items: tea.map((e) => DropdownMenuItem<int>(value: e['usersID'], child: Text(e['name'] ?? "Pengajar"))).toList(),
              onChanged: (v) => setState(() => sT = v),
              decoration: const InputDecoration(border: OutlineInputBorder()),
            ),
            const SizedBox(height: 15),
            ListTile(
              tileColor: Colors.grey[100],
              title: Text(sD == null ? "Pilih Tanggal" : DateFormat('dd/MM/yyyy').format(sD!)),
              trailing: const Icon(Icons.calendar_month),
              onTap: () async {
                final d = await showDatePicker(context: context, initialDate: DateTime.now(), firstDate: DateTime.now(), lastDate: DateTime.now().add(const Duration(days: 14)));
                if(d != null) setState(() => sD = d);
              },
            ),
            const SizedBox(height: 30),
            SizedBox(
              width: double.infinity, height: 50,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF990000)),
                onPressed: (sM == null || sT == null || sD == null) ? null : () async {
                  setState(() => load = true);
                  final prefs = await SharedPreferences.getInstance();
                  final body = {'teacher_id': sT.toString(), 'material_id': sM.toString(), 'date': DateFormat('yyyy-MM-dd').format(sD!), 'time': "10:00"};
                  await TutorService.submitTutor(body, prefs.getString('token') ?? '');
                  if(mounted) Navigator.pop(context);
                },
                child: const Text("Kirim Pengajuan", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              ),
            )
          ],
        ),
      ),
    );
  }
}

// --- HALAMAN HISTORY --- (PASTI BERHASIL)
class TutorHistoryPage extends StatefulWidget {
  const TutorHistoryPage({super.key});
  @override State<TutorHistoryPage> createState() => _TutorHistoryPageState();
}

class _TutorHistoryPageState extends State<TutorHistoryPage> {
  List hist = []; bool load = true;
  @override
  void initState() { super.initState(); _fetch(); }
  _fetch() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final res = await TutorService.getTutorData(prefs.getString('token') ?? '');
      if (res.statusCode == 200) setState(() => hist = jsonDecode(res.body)['history'] ?? []);
    } catch (e) { debugPrint("Error History: $e"); } 
    finally { if (mounted) setState(() => load = false); }
  }
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Histori Tutor")),
      body: load ? const Center(child: CircularProgressIndicator()) : ListView.builder(
        padding: const EdgeInsets.all(15),
        itemCount: hist.length,
        itemBuilder: (c, i) => Card(
          margin: const EdgeInsets.only(bottom: 12),
          child: ListTile(
            title: Text(hist[i]['material']['nama_materi'] ?? "Materi", style: const TextStyle(fontWeight: FontWeight.bold)),
            subtitle: Text("Pengajar: ${hist[i]['teacher']['name']}\nJadwal: ${hist[i]['date']}"),
            trailing: Text(hist[i]['status'].toString().toUpperCase(), style: const TextStyle(color: Colors.blue, fontWeight: FontWeight.bold)),
          ),
        ),
      ),
    );
  }
}