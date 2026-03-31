@extends('layouts.spekta')
@section('title', 'Terbitkan Tryout Baru')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-10 rounded-3xl shadow-xl border-t-8 border-[#990000]">

    <h3 class="text-2xl font-bold text-gray-800 uppercase text-center mb-2">Input Soal Tryout</h3>
    <p class="text-center text-spekta font-bold mb-8 uppercase italic">{{ $class->nama_program }}</p>

    <!-- ALERT NOTIFIKASI SUKSES (HIJAU) -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex items-center">
            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <!-- ALERT NOTIFIKASI GAGAL (MERAH) -->
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm flex items-center">
            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 001.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('pengajar.tryout.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <input type="hidden" name="class_id" value="{{ $class->class_modelsID }}">

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Judul Tryout</label>
            <input type="text" name="title" placeholder="Misal: Simulasi SKD Batch 1"
                class="w-full border-2 border-gray-100 p-3 rounded-xl focus:border-[#990000] outline-none" required>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Durasi Pengerjaan (Menit)</label>
            <input type="number" name="duration" value="60" class="w-full border-2 border-gray-100 p-3 rounded-xl" required>
        </div>

        <div class="p-6 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 text-center">
            <label class="block text-sm font-bold text-gray-700 mb-4">PILIH FILE SOAL (CSV)</label>
            <input type="file" name="file_csv" accept=".csv" class="mx-auto" required>
            <p class="text-[10px] text-gray-400 mt-2 italic">Pastikan file dalam format .csv (Comma Delimited)</p>
        </div>

        <button type="submit" class="w-full bg-[#990000] text-white py-4 rounded-2xl font-bold shadow-lg hover:bg-red-800 transition">
            🚀 TERBITKAN TRYOUT SEKARANG
        </button>
    </form>
</div>
@endsection
