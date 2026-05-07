@extends('layouts.spekta')
@section('title', 'Import Soal ' . $subject_name)

@section('content')
<div class="bg-white p-8 rounded-[30px] shadow-sm border border-gray-100 mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-black text-gray-800 uppercase leading-none">Import Latihan CSV</h3>
            <p class="text- spekta font-black text-xs uppercase mt-1">BIDANG: {{ $subject_name }}</p>
        </div>
        <a href="{{ route('pengajar.latihan.index') }}" class="text-xs font-bold text-gray-400 uppercase">&larr; Kembali</a>
    </div>

    <form action="{{ route('pengajar.latihan.store', $class->class_id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
        @csrf
        {{-- MODIFIKASI: Input hidden agar subject_name otomatis terisi --}}
        <input type="hidden" name="subject" value="{{ $subject_name }}">

        <div>
            <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block ml-1">Minggu Ke- (1-20)</label>
            <select name="week" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold" required>
                @for($i=1; $i<=20; $i++)
                    <option value="{{ $i }}">Minggu Ke-{{ $i }}</option>
                @endfor
            </select>
        </div>

        <div>
            <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block ml-1">Pilih File CSV</label>
            <input type="file" name="file_csv" class="w-full text-xs font-bold" required>
        </div>

        <button type="submit" class="bg-blue-600 text-white p-4 rounded-2xl font-black text-[10px] uppercase shadow-lg shadow-blue-100 hover:bg-blue-800 transition">
            Proses Import Soal
        </button>
    </form>
</div>

{{-- Tabel daftar soal yang sudah terupload bisa ditaruh di bawah sini --}}

@endsection
