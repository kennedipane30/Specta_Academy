import 'package:flutter/material.dart';
import 'tryout_detail_page.dart';

class TryoutListPage extends StatelessWidget {
  final List tryouts;
  final String token;
  final int userId; 

  const TryoutListPage({
    super.key, 
    required this.tryouts, 
    required this.token,
    required this.userId, 
  });

  @override
  Widget build(BuildContext context) {
    const Color spektaRed = Color(0xFF990000);

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text(
          "Pilih Paket Tryout", 
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)
        ),
        backgroundColor: spektaRed,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
      ),
      body: tryouts.isEmpty 
      ? Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.assignment_late_outlined, size: 70, color: Colors.grey[300]),
              const SizedBox(height: 10),
              const Text("Belum ada paket tryout tersedia.", style: TextStyle(color: Colors.grey)),
            ],
          ),
        )
      : ListView.builder(
          padding: const EdgeInsets.all(20),
          itemCount: tryouts.length,
          itemBuilder: (context, index) {
            final tData = tryouts[index];
            final String title = tData['title'] ?? tData['name'] ?? 'Tryout';
            final String duration = tData['duration']?.toString() ?? '120';
            
            final bool isDone = tData['is_done'] == true || tData['is_done'] == 1 || tData['is_done'] == "1";
            
            // ✨ MODIFIKASI: Ambil skor dari backend
            final String score = tData['score']?.toString() ?? '-';

            return Container(
              margin: const EdgeInsets.only(bottom: 15),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10)]
              ),
              child: ListTile(
                contentPadding: const EdgeInsets.all(15),
                leading: Container(
                  width: 50, height: 50,
                  decoration: BoxDecoration(
                    color: isDone ? Colors.green.withOpacity(0.1) : spektaRed.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(15)
                  ),
                  child: Icon(
                    isDone ? Icons.check_circle_rounded : Icons.assignment_rounded, 
                    color: isDone ? Colors.green : spektaRed
                  ),
                ),
                title: Text(
                  title, 
                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)
                ),
                subtitle: Text("$duration Menit • ${isDone ? 'Sudah Dikerjakan' : 'Belum Dikerjakan'}", 
                  style: TextStyle(color: isDone ? Colors.green : Colors.grey)
                ),
                
                // ✨ MODIFIKASI: Ubah Trailing menjadi angka Nilai
                trailing: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    const Text(
                      'Nilai',
                      style: TextStyle(color: Colors.grey, fontSize: 11, fontWeight: FontWeight.w500),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      score,
                      style: TextStyle(
                        color: isDone ? spektaRed : Colors.black87,
                        fontSize: 18,
                        fontWeight: FontWeight.w900,
                      ),
                    ),
                  ],
                ),
                
                onTap: () {
                  Navigator.push(
                    context, 
                    MaterialPageRoute(
                      builder: (context) => TryoutDetailPage(
                        tryoutData: tData,
                        token: token,
                        isDone: isDone, 
                        userId: userId, 
                      )
                    )
                  );
                },
              ),
            );
          },
        ),
    );
  }
}