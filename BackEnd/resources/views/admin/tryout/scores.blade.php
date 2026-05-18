@extends('layouts.spekta')
@section('content')
<div class="p-6">
    <div class="mb-8">
        <a href="{{ route('admin.scores.pilih_tryout', $tryout->class_id) }}" class="text-xs font-bold text-gray-400 hover:text-red-600 uppercase">← Kembali ke Daftar Paket</a>
        <h1 class="text-2xl font-black text-gray-800 uppercase mt-2">Rekap Nilai: {{ $tryout->title }}</h1>
    </div>

    <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr class="text-[10px] font-black text-gray-400 uppercase">
                    <th class="p-6">No</th>
                    <th class="p-6">Nama Siswa</th>
                    <th class="p-6 text-center">Jawaban Benar</th>
                    <th class="p-6 text-center">Skor Akhir</th>
                    <th class="p-6 text-right">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($results as $index => $res)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="p-6 text-sm font-bold text-gray-300">#{{ $index + 1 }}</td>
                    <td class="p-6">
                        <p class="font-black text-gray-800 text-sm uppercase">{{ optional($res->user_data)->name ?? 'N/A' }}</p>
                    </td>
                    <td class="p-6 text-center">
                        <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-[10px] font-black">{{ $res->total_correct }} SOAL</span>
                    </td>
                    <td class="p-6 text-center font-black text-xl {{ $res->score >= 70 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $res->score }}
                    </td>
                    <td class="p-6 text-right text-[10px] font-black uppercase text-gray-400">
                        {{ $res->created_at->format('d M Y | H:i') }} WIB
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
