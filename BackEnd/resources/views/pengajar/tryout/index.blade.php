@extends('layouts.spekta')
@section('title', 'Kirim Soal Tryout')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">⏱️ Kirim Soal Tryout</h2>
    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Input soal per-mata pelajaran untuk digabungkan oleh Admin</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach($assignments as $assign)
    <div class="bg-white p-8 rounded-[30px] shadow-sm border-l-[10px] border-orange-500 hover:shadow-xl transition">
        <h3 class="text-xl font-black text-gray-800 uppercase">{{ $assign->classModel->program_name }}</h3>
        <p class="text-orange-600 font-black text-xs mt-1 mb-6 italic uppercase">
            Bidang: {{ $assign->subject_name }}
        </p>

        {{-- PERBAIKAN: Gunakan nama route yang baru kita buat di web.php --}}
        <a href="{{ route('pengajar.tryout.create', [$assign->class_id, $assign->subject_name]) }}"
           class="bg-orange-500 text-white px-6 py-3 rounded-2xl font-black text-[10px] inline-block shadow-lg shadow-orange-100 hover:bg-orange-600 uppercase transition">
           Input Soal {{ $assign->subject_name }}
        </a>
    </div>
    @endforeach
</div>
@endsection
