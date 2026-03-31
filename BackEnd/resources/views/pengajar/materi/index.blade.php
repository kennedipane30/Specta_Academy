@extends('layouts.spekta')
@section('title', 'Pilih Program Materi')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">📚 Manajemen Materi</h2>
    <p class="text-gray-500">Pilih program kelas untuk mengelola file PDF materi.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach($classes as $c)
    <div class="bg-white p-6 rounded-3xl shadow-sm border-l-8 border-[#990000] hover:shadow-md transition">
        <h3 class="text-xl font-bold text-gray-800">{{ $c->nama_program }}</h3>
        <p class="text-gray-500 text-sm mb-4">Klik untuk melihat daftar mata pelajaran dan upload modul.</p>

        <a href="{{ route('pengajar.materi.pilih', $c->class_modelsID) }}"
           class="bg-[#990000] text-white px-6 py-2 rounded-lg font-bold text-sm inline-block shadow-sm hover:bg-red-800">
           PILIH KELAS INI
        </a>
    </div>
    @endforeach
</div>
@endsection
