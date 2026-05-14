@extends('layouts.spekta')
@section('title', 'Rekap Absensi')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">📊 Rekap Absensi</h2>
        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">{{ $subject }} - Minggu {{ $week }}</p>
    </div>
    <a href="{{ route('pengajar.absensi.weeks', [$class->class_id, $subject]) }}" class="bg-gray-100 text-gray-500 px-6 py-2 rounded-xl font-black text-[10px] uppercase hover:bg-gray-200 transition">
        &larr; Kembali
    </a>
</div>

<div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-8 border-b border-gray-50 bg-gray-50/50">
        <h3 class="text-lg font-black text-gray-800 uppercase">{{ $class->program_name }}</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b">
                    <th class="p-6">Nama Siswa</th>
                    <th class="p-6 text-center">Status Kehadiran</th>
                    <th class="p-6 text-right">Tanggal Input</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($data as $row)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="p-6">
                        <p class="font-black text-gray-800 text-sm uppercase">{{ $row->user->name ?? 'N/A' }}</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Siswa</p>
                    </td>
                    <td class="p-6 text-center">
                        @if($row->status == 'h')
                            <span class="bg-green-100 text-green-700 px-4 py-1.5 rounded-full text-[9px] font-black uppercase">Hadir</span>
                        @elseif($row->status == 'i')
                            <span class="bg-yellow-100 text-yellow-700 px-4 py-1.5 rounded-full text-[9px] font-black uppercase">Izin</span>
                        @else
                            <span class="bg-red-100 text-red-700 px-4 py-1.5 rounded-full text-[9px] font-black uppercase">Alpa</span>
                        @endif
                    </td>
                    <td class="p-6 text-right">
                        <p class="text-xs font-bold text-gray-500">{{ date('d M Y', strtotime($row->date)) }}</p>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="p-20 text-center text-gray-400 font-bold uppercase text-xs">Data absensi tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
