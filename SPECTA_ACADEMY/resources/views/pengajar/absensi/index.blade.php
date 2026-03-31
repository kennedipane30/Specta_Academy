@extends('layouts.spekta')
@section('title', 'Daftar Absensi Kelas')

@section('content')

    <!-- NOTIFIKASI RAMAH PENGGUNA (Syarat Kualitas Perangkat Lunak) -->
    @if(session('info'))
        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-8 rounded shadow-sm flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012-0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">{{ session('info') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-blue-400 hover:text-blue-600 font-bold text-xl">&times;</button>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @foreach($classes as $c)
            @php
                // Cek apakah ada jadwal mengajar hari ini untuk guru yang login
                $canAbsen = in_array($c->class_modelsID, $jadwalHariIni);
            @endphp

            <!-- CARD KELAS -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border-l-8 transition duration-300 {{ $canAbsen ? 'border-green-500 shadow-md' : 'border-gray-200 opacity-80' }}">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold {{ $canAbsen ? 'text-gray-800' : 'text-gray-400' }}">
                            {{ $c->nama_program }}
                        </h3>
                        <p class="text-gray-500 text-xs mt-1 uppercase tracking-widest font-semibold">
                            Program Spekta Academy
                        </p>
                    </div>

                    @if($canAbsen)
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                            Jadwal Aktif
                        </span>
                    @else
                        <span class="bg-gray-100 text-gray-400 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                            No Attendance
                        </span>
                    @endif
                </div>

                <div class="my-6">
                    @if($canAbsen)
                        <p class="text-gray-600 text-sm">
                            Halo Bapak/Ibu Guru, jadwal Anda sudah siap. Silakan klik tombol di bawah untuk mengisi kehadiran siswa.
                        </p>
                    @else
                        <div class="bg-gray-50 p-3 rounded-xl border border-dashed border-gray-200">
                            <p class="text-gray-400 text-sm italic">
                                Maaf, tidak ada jadwal mengajar untuk Anda di kelas ini pada hari ini ({{ date('d M Y') }}).
                            </p>
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-between mt-4">
                    @if($canAbsen)
                        <a href="{{ route('pengajar.absensi.show', $c->class_modelsID) }}"
                           class="bg-green-600 text-white px-8 py-3 rounded-xl font-bold text-sm shadow-lg hover:bg-green-700 hover:-translate-y-1 transform transition duration-200 flex items-center">
                           <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                           BUKA ABSENSI
                        </a>
                    @else
                        <div class="flex items-center text-gray-300 font-bold text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            ABSENSI TERKUNCI
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

@endsection
