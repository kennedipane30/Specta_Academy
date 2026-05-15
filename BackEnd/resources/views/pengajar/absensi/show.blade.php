@extends('layouts.spekta')
@section('title', 'Input Absensi')

@section('content')
<div class="bg-white p-10 rounded-[40px] shadow-sm border border-gray-100 mb-20">
    {{-- HEADER INFO --}}
    <div class="flex justify-between items-center mb-10 border-b pb-8">
        <div>
            <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">
                {{ $subject }}
            </h3>
            <p class="text-sm font-bold text-[#990000] uppercase tracking-widest">
                {{ $class->program_name }} • Minggu ke-{{ $week }}
            </p>
        </div>
        <div class="text-right">
            <p class="text-[10px] font-black text-gray-400 uppercase">Waktu Input</p>
            <p class="text-lg font-black text-gray-800">{{ date('d M Y') }}</p>
        </div>
    </div>

    <form action="{{ route('pengajar.absensi.store') }}" method="POST">
        @csrf
        <input type="hidden" name="class_id" value="{{ $class->class_id }}">
        <input type="hidden" name="subject_name" value="{{ $subject }}">
        <input type="hidden" name="week" value="{{ $week }}">

        <div class="space-y-4">
            @forelse($siswas as $s)
            <div class="flex items-center justify-between p-6 bg-gray-50 rounded-[30px] border border-gray-100 hover:bg-white hover:shadow-md transition duration-300">
                {{-- INFO SISWA --}}
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-spekta text-white rounded-2xl flex items-center justify-center font-black text-lg shadow-lg shadow-red-100">
                        {{ substr($s->user->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-black text-gray-800 text-sm uppercase">{{ $s->user->name }}</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Siswa Aktif</p>
                    </div>
                </div>

                {{-- RADIO BUTTONS PROFESIONAL --}}
                <div class="flex gap-3">
                    {{-- OPSI HADIR --}}
                    <label class="cursor-pointer group">
                        <input type="radio" name="status[{{ $s->user->usersID }}]" value="h" class="hidden peer" required>
                        <div class="w-12 h-12 flex items-center justify-center rounded-full border-2 border-gray-200 font-black text-xs text-gray-400 transition-all duration-300
                                    peer-checked:bg-green-500 peer-checked:text-white peer-checked:border-green-500 peer-checked:shadow-lg peer-checked:shadow-green-100
                                    group-hover:border-green-300">
                            H
                        </div>
                    </label>

                    {{-- OPSI IZIN --}}
                    <label class="cursor-pointer group">
                        <input type="radio" name="status[{{ $s->user->usersID }}]" value="i" class="hidden peer">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full border-2 border-gray-200 font-black text-xs text-gray-400 transition-all duration-300
                                    peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-500 peer-checked:shadow-lg peer-checked:shadow-amber-100
                                    group-hover:border-amber-300">
                            I
                        </div>
                    </label>

                    {{-- OPSI ALPA --}}
                    <label class="cursor-pointer group">
                        <input type="radio" name="status[{{ $s->user->usersID }}]" value="a" class="hidden peer">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full border-2 border-gray-200 font-black text-xs text-gray-400 transition-all duration-300
                                    peer-checked:bg-red-600 peer-checked:text-white peer-checked:border-red-600 peer-checked:shadow-lg peer-checked:shadow-red-100
                                    group-hover:border-red-300">
                            A
                        </div>
                    </label>
                </div>
            </div>
            @empty
            <div class="p-20 text-center bg-gray-50 rounded-[40px] border-2 border-dashed">
                <p class="text-gray-400 font-black uppercase text-xs">Belum ada siswa aktif di kelas ini.</p>
            </div>
            @endforelse
        </div>

        @if($siswas->count() > 0)
        <button type="submit" class="w-full mt-10 bg-[#990000] text-white py-5 rounded-[25px] font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-red-100 hover:bg-red-800 transition transform active:scale-95">
            Simpan Absensi Minggu {{ $week }}
        </button>
        @endif
    </form>
</div>
@endsection
