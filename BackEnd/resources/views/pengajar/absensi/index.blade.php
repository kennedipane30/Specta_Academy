@extends('layouts.spekta')
@section('title', 'Attendance List')

@section('content')

    {{-- Notifikasi Info --}}
    @if(session('info'))
        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-8 rounded shadow-sm flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012-0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                <span class="font-bold text-xs uppercase">{{ session('info') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="font-bold text-xl">&times;</button>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @foreach($classes as $c)
            @php
                // MODIFIKASI: Cek menggunakan class_id
                $canAbsen = in_array($c->class_id, $jadwalHariIni);
            @endphp

            <!-- CARD KELAS -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border-l-8 transition duration-300 {{ $canAbsen ? 'border-green-500 shadow-xl' : 'border-gray-100 opacity-80' }}">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        {{-- MODIFIKASI: Gunakan program_name --}}
                        <h3 class="text-xl font-black uppercase tracking-tight {{ $canAbsen ? 'text-gray-800' : 'text-gray-300' }}">
                            {{ $c->program_name }}
                        </h3>
                        <p class="text-gray-400 text-[10px] mt-1 uppercase tracking-widest font-black">
                            Spekta Academy Program
                        </p>
                    </div>

                    @if($canAbsen)
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest animate-pulse">
                            Active Schedule
                        </span>
                    @else
                        <span class="bg-gray-50 text-gray-300 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest">
                            No Attendance
                        </span>
                    @endif
                </div>

                <div class="my-8">
                    @if($canAbsen)
                        <p class="text-gray-500 text-sm font-medium">
                            Jadwal mengajar Anda tersedia hari ini. Silakan lakukan absensi kehadiran siswa.
                        </p>
                    @else
                        <div class="bg-gray-50 p-6 rounded-2xl border border-dashed border-gray-200 flex items-center justify-center">
                            <p class="text-gray-300 text-xs font-bold uppercase tracking-widest italic">
                                No attendance for this class
                            </p>
                        </div>
                    @endif
                </div>

                <div class="flex items-center mt-4">
                    @if($canAbsen)
                        {{-- MODIFIKASI: Gunakan class_id --}}
                        <a href="{{ route('pengajar.absensi.show', $c->class_id) }}"
                           class="bg-[#990000] text-white px-8 py-4 rounded-2xl font-black text-[10px] tracking-widest shadow-lg shadow-red-100 hover:bg-red-800 transition transform active:scale-95 flex items-center uppercase">
                           <i class="fas fa-edit mr-2"></i> Open Attendance &rarr;
                        </a>
                    @else
                        <div class="flex items-center text-gray-200 font-black text-[10px] uppercase tracking-widest">
                            <i class="fas fa-lock mr-2"></i> Attendance Locked
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

@endsection
