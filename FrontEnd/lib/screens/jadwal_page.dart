import 'package:flutter/material.dart';
import 'package:table_calendar/table_calendar.dart';
import '../services/auth_service.dart';
import 'dart:convert';

class JadwalPage extends StatefulWidget {
  final String token;
  const JadwalPage({super.key, required this.token});

  @override
  State<JadwalPage> createState() => _JadwalPageState();
}

class _JadwalPageState extends State<JadwalPage> {
  DateTime _focusedDay = DateTime.now();
  DateTime? _selectedDay;
  List _allSchedules = [];
  List _selectedEvents = [];
  bool _isLoading = true;
  final Color spektaRed = const Color(0xFF990000);

  @override
  void initState() {
    super.initState();
    _selectedDay = _focusedDay;
    _fetchJadwal();
  }

  _fetchJadwal() async {
    try {
      var resp = await AuthService.getSiswaSchedule(widget.token);
      if (resp.statusCode == 200) {
        setState(() {
          _allSchedules = jsonDecode(resp.body)['data'];
          _isLoading = false;
          _filterEvents(_selectedDay!);
        });
      }
    } catch (e) { debugPrint("Error fetching schedule: $e"); }
  }

  void _filterEvents(DateTime date) {
    String formattedDate = "${date.year}-${date.month.toString().padLeft(2, '0')}-${date.day.toString().padLeft(2, '0')}";
    setState(() {
      _selectedEvents = _allSchedules.where((s) => s['date'] == formattedDate).toList();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(title: const Text("Learning Schedule", style: TextStyle(fontWeight: FontWeight.bold)), backgroundColor: spektaRed, foregroundColor: Colors.white, elevation: 0),
      body: _isLoading
          ? Center(child: CircularProgressIndicator(color: spektaRed))
          : Column(
              children: [
                TableCalendar(
                  firstDay: DateTime.utc(2024, 1, 1),
                  lastDay: DateTime.utc(2030, 12, 31),
                  focusedDay: _focusedDay,
                  headerStyle: const HeaderStyle(formatButtonVisible: false, titleCentered: true),
                  selectedDayPredicate: (day) => isSameDay(_selectedDay, day),
                  onDaySelected: (selectedDay, focusedDay) {
                    setState(() { _selectedDay = selectedDay; _focusedDay = focusedDay; });
                    _filterEvents(selectedDay);
                  },
                  calendarStyle: CalendarStyle(
                    selectedDecoration: BoxDecoration(color: spektaRed, shape: BoxShape.circle),
                    todayDecoration: BoxDecoration(color: spektaRed.withOpacity(0.3), shape: BoxShape.circle),
                    markerDecoration: BoxDecoration(color: spektaRed, shape: BoxShape.circle),
                  ),
                  eventLoader: (day) {
                    String d = "${day.year}-${day.month.toString().padLeft(2, '0')}-${day.day.toString().padLeft(2, '0')}";
                    return _allSchedules.where((s) => s['date'] == d).toList();
                  },
                ),
                const SizedBox(height: 10),
                Expanded(
                  child: Container(
                    width: double.infinity,
                    padding: const EdgeInsets.only(top: 40, left: 30, right: 30),
                    decoration: BoxDecoration(color: spektaRed, borderRadius: const BorderRadius.only(topLeft: Radius.circular(50), topRight: Radius.circular(50))),
                    child: _selectedEvents.isEmpty
                        ? const Center(child: Text("No schedule for today", style: TextStyle(color: Colors.white70)))
                        : ListView.builder(
                            itemCount: _selectedEvents.length,
                            itemBuilder: (context, index) {
                              var item = _selectedEvents[index];
                              return _buildAgendaItem(
                                  item['start_time'],
                                  item['end_time'],
                                  item['title'],
                                  // MODIFIKASI: Relasi class dan program_name
                                  item['class']?['program_name'] ?? "Spekta Program");
                            },
                          ),
                  ),
                )
              ],
            ),
    );
  }

  Widget _buildAgendaItem(String start, String end, String title, String className) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 25),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Column(children: [
            Text(start.substring(0, 5), style: const TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.bold)),
            Text(end.substring(0, 5), style: const TextStyle(color: Colors.white60, fontSize: 12)),
          ]),
          const SizedBox(width: 20),
          Container(width: 2, height: 60, color: Colors.white24),
          const SizedBox(width: 20),
          Expanded(
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(className.toUpperCase(), style: const TextStyle(color: Colors.yellow, fontSize: 10, fontWeight: FontWeight.bold, letterSpacing: 1.2)),
              const SizedBox(height: 4),
              Text(title, style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
              const Text("Don't be late!", style: TextStyle(color: Colors.white54, fontSize: 11, fontStyle: FontStyle.italic)),
            ]),
          ),
        ],
      ),
    );
  }
}