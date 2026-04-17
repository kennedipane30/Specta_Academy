import 'package:flutter/material.dart';
import 'package:file_picker/file_picker.dart';
import 'dart:convert';
import '../../services/question_bank_service.dart';
import 'package:url_launcher/url_launcher.dart';

class QuestionSharingPage extends StatefulWidget {
  final String token;

  // Constructor menerima parameter 'token' secara wajib (required)
  const QuestionSharingPage({super.key, required this.token});

  @override State<QuestionSharingPage> createState() => _QuestionSharingPageState();
}

class _QuestionSharingPageState extends State<QuestionSharingPage> {
  List questions = [];
  bool isLoading = true;
  final Color spektaRed = const Color(0xFF990000);

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    final resp = await QuestionBankService.getAllQuestions(widget.token);
    if (resp.statusCode == 200) {
      if (mounted) {
        setState(() {
          questions = jsonDecode(resp.body)['data'];
          isLoading = false;
        });
      }
    }
  }

  void _pickAndUpload() async {
    FilePickerResult? result = await FilePicker.platform.pickFiles(
      type: FileType.custom, 
      allowedExtensions: ['pdf']
    );
    if (result != null) _showUploadDialog(result.files.single.path!);
  }

  void _showUploadDialog(String path) {
    final titleCtrl = TextEditingController();
    String? selectedSubject;

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text("Share New Question", style: TextStyle(fontWeight: FontWeight.bold)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(
              controller: titleCtrl, 
              decoration: const InputDecoration(labelText: "Document Title")
            ),
            const SizedBox(height: 10),
            DropdownButtonFormField<String>(
              hint: const Text("Select Subject"),
              items: ["TIU", "TWK", "English", "Psychology", "General"]
                  .map((e) => DropdownMenuItem(value: e, child: Text(e)))
                  .toList(),
              onChanged: (v) => selectedSubject = v,
            )
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text("Cancel")),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: spektaRed),
            onPressed: () async {
              if (titleCtrl.text.isEmpty || selectedSubject == null) return;
              Navigator.pop(context);
              
              // Menjalankan upload service
              var stream = await QuestionBankService.uploadQuestion(
                title: titleCtrl.text,
                subject: selectedSubject!,
                filePath: path,
                token: widget.token
              );
              
              if (stream.statusCode == 201) _loadData();
            }, 
            child: const Text("Upload", style: TextStyle(color: Colors.white))
          )
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text("Question Bank Hub"), 
        backgroundColor: spektaRed, 
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: isLoading 
        ? Center(child: CircularProgressIndicator(color: spektaRed))
        : RefreshIndicator(
            onRefresh: _loadData,
            child: ListView.builder(
              itemCount: questions.length,
              padding: const EdgeInsets.all(15),
              itemBuilder: (context, index) {
                var item = questions[index];
                return Card(
                  margin: const EdgeInsets.only(bottom: 12),
                  elevation: 2,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                  child: ListTile(
                    contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
                    leading: const Icon(Icons.picture_as_pdf, color: Colors.red, size: 35),
                    title: Text(item['title'], style: const TextStyle(fontWeight: FontWeight.bold)),
                    subtitle: Text("${item['subject']} • By: ${item['user']['name']}"),
                    trailing: const Icon(Icons.open_in_new, size: 20, color: Colors.grey),
                    onTap: () async {
                      final url = Uri.parse("http://10.0.2.2:8000/storage/${item['file_path']}");
                      if (await canLaunchUrl(url)) {
                        await launchUrl(url, mode: LaunchMode.externalApplication);
                      }
                    },
                  ),
                );
              },
            ),
          ),
      floatingActionButton: FloatingActionButton(
        backgroundColor: spektaRed,
        onPressed: _pickAndUpload,
        child: const Icon(Icons.upload_file, color: Colors.white),
      ),
    );
  }
}