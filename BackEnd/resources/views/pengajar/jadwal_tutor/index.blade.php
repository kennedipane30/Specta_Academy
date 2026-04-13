@extends('layouts.spekta')
@section('title', 'Daftar Absensi Kelas')

@section('content')

    {{-- Notifikasi Info jika redirect dari show --}}
    @if(session('info'))
        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-8 rounded shadow-sm flex items-center justify-between">
            <span class="font-medium">{{ session('info') }}</span>
            <button onclick="this.parentElement.remove()" class="font-bold text-xl">&times;</button>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @foreach($classes as $c)
            @php
                $canAbsen = in_array($c->class_modelsID, $jadwalHariIni);
            @endphp

            <div class="bg-white p-6 rounded-3xl shadow-sm border-l-8 transition duration-300 {{ $canAbsen ? 'border-green-500 shadow-md' : 'border-gray-200' }}">
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
                            Jadwal mengajar tersedia. Silakan klik tombol di bawah untuk mengisi absensi.
                        </p>
                    @else
                        <div class="bg-gray-50 p-4 rounded-xl border border-dashed border-gray-200 text-center">
                            <p class="text-gray-400 text-sm italic font-medium">
                                Tidak ada absensi untuk kelas ini
                            </p>
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-between mt-4">
                    @if($canAbsen)
                        <a href="{{ route('pengajar.absensi.show', $c->class_modelsID) }}"
                           class="bg-green-600 text-white px-8 py-3 rounded-xl font-bold text-sm shadow-lg hover:bg-green-700 transform transition duration-200 flex items-center">
                           BUKA ABSENSI
                        </a>
                    @else
                        <div class="flex items-center text-gray-300 font-bold text-xs uppercase tracking-tighter">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Absensi Terkunci
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

@endsection
