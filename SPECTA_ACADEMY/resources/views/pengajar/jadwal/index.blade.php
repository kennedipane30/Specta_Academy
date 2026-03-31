@extends('layouts.spekta')
@section('content')
<div class="bg-white p-8 rounded-2xl shadow-md border-t-8 border-[#990000]">
    <h3 class="text-xl font-bold mb-4">Jadwal Mengajar Anda</h3>
    <p class="text-gray-500 mb-8 text-sm italic">Halaman ini menampilkan daftar kelas di mana Anda ditugaskan mengajar.</p>

    <div class="space-y-4">
        @forelse($jadwal as $j)
        <div class="flex items-center p-6 bg-red-50 rounded-2xl border-l-8 border-spekta shadow-sm">
            <div class="text-center pr-8 border-r border-red-200">
                <h4 class="text-2xl font-bold text-spekta">{{ date('d', strtotime($j->date)) }}</h4>
                <p class="text-xs uppercase">{{ date('M Y', strtotime($j->date)) }}</p>
            </div>
            <div class="pl-8 flex-1">
                <h5 class="text-lg font-bold">{{ $j->title }}</h5>
                <p class="text-sm text-gray-600 uppercase">{{ $j->classModel->nama_program }}</p>
                <div class="mt-2 text-xs font-bold text-gray-500 flex items-center gap-2">
                    🕒 {{ $j->start_time }} - {{ $j->end_time }} WIB
                </div>
            </div>
            <div class="bg-white px-4 py-2 rounded-xl text-spekta font-bold text-xs shadow-sm">SIAP MENGAJAR</div>
        </div>
        @empty
        <div class="text-center p-20 text-gray-400 italic">Belum ada jadwal mengajar untuk Anda.</div>
        @endforelse
    </div>
</div>
@endsection
