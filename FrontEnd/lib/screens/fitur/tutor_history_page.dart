import 'dart:convert';
import 'package:flutter/material.dart';
import '../../services/tutor_service.dart';

class TutorHistoryPage extends StatefulWidget {
  const TutorHistoryPage({super.key});
  @override State<TutorHistoryPage> createState() => _TutorHistoryPageState();
}

class _TutorHistoryPageState extends State<TutorHistoryPage> {
  List history = [];
  bool loading = true;

  @override
  void initState() { super.initState(); _getH(); }

  _getH() async {
    final res = await TutorService.getTutorData("TOKEN_DARI_PREFS");
    setState(() { history = jsonDecode(res.body)['history']; loading = false; });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Histori")),
      body: loading ? const Center(child: CircularProgressIndicator()) : ListView.builder(
        itemCount: history.length,
        itemBuilder: (c, i) => ListTile(
          title: Text(history[i]['material']['nama_materi']),
          subtitle: Text("Status: ${history[i]['status']}"),
        ),
      ),
    );
  }
}