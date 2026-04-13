@extends('layouts.spekta')
@section('title', 'Detail Kehadiran Siswa')

@section('content')
<div class="mb-6">
    <a href="{{ route('pengajar.absensi.index') }}" class="text-green-600 font-bold flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Daftar Kelas
    </a>
</div>

<div class="bg-white p-8 rounded-2xl shadow-md border-t-8 border-blue-600">
    <div class="flex justify-between items-center mb-8 border-b pb-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Laporan Kehadiran Siswa</h3>
            <p class="text-gray-500">Materi: <b>{{ $schedule->title }}</b></p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-400 font-semibold uppercase tracking-widest">Tanggal Absensi</p>
            <p class="text-lg font-bold text-blue-600">{{ date('d F Y') }}</p>
        </div>
    </div>

    <table class="w-full text-left">
        <thead>
            <tr class="bg-gray-50 text-gray-400 uppercase text-[10px] font-bold tracking-wider">
                <th class="p-4 border-b">Nama Siswa</th>
                <th class="p-4 border-b text-center">Status</th>
                <th class="p-4 border-b text-right">Waktu Input</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $at)
            <tr class="hover:bg-gray-50 transition">
                <td class="p-4 border-b font-bold text-gray-700">{{ $at->user->name }}</td>
                <td class="p-4 border-b text-center">
                    @if($at->status == 'hadir')
                        <span class="bg-green-100 text-green-700 px-4 py-1 rounded-full text-[10px] font-bold uppercase">Hadir</span>
                    @elseif($at->status == 'izin')
                        <span class="bg-yellow-100 text-yellow-700 px-4 py-1 rounded-full text-[10px] font-bold uppercase">Izin</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-4 py-1 rounded-full text-[10px] font-bold uppercase">Alpa</span>
                    @endif
                </td>
                <td class="p-4 border-b text-right text-xs text-gray-400 italic">
                    {{ $at->created_at->format('H:i') }} WIB
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-8 flex justify-end">
        <button onclick="window.print()" class="bg-gray-800 text-white px-6 py-2 rounded-lg font-bold text-sm flex items-center gap-2 hover:bg-black transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Laporan
        </button>
    </div>
</div>
@endsection
