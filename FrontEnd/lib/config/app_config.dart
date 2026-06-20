// // lib/app_config.dart

// class AppConfig {
//   static const String host = '3.107.184.92'; 

//   static const String baseUrl = 'http://$host/api';
//   static const String storageUrl = 'http://$host/storage';

//   static const String materiUrl   = 'http://$host/api/materi';
//   static const String tryoutUrl   = 'http://$host/api/tryout';
//   static const String practiceUrl = 'http://$host/api/practice';
// }
// lib/app_config.dart

// lib/app_config.dart

class AppConfig {
  // IP khusus agar emulator Android bisa mendeteksi localhost laptop Anda
  static const String host = '10.0.2.2'; 

  // 🔥 PERBAIKAN UTAMA: Menggunakan ${host} agar port :8000 tidak hilang saat dicompile
  static const String baseUrl    = 'http://${host}:8000/api'; 
  static const String storageUrl = 'http://${host}:8000/storage';

  // DIARAHKAN LANGSUNG KE PORT MICROSERVICE GOLANG LOKAL ANDA:
  static const String materiUrl   = 'http://${host}:9001/api'; 
  static const String tryoutUrl   = 'http://${host}:9002/api'; 
  static const String practiceUrl = 'http://${host}:9003/api';
}