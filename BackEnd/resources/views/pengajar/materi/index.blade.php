@extends('layouts.spekta')
@section('title', 'Select Material Program')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">📚 Material Management</h2>
    <p class="text-gray-500">Select a class program to manage material PDF files.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach($classes as $c)
    <div class="bg-white p-6 rounded-3xl shadow-sm border-l-8 border-[#990000] hover:shadow-md transition">
        {{-- MODIFIKASI: program_name --}}
        <h3 class="text-xl font-bold text-gray-800">{{ $c->program_name }}</h3>
        <p class="text-gray-500 text-sm mb-4">Click to view subject list and upload modules.</p>

        {{-- MODIFIKASI: class_id --}}
        <a href="{{ route('pengajar.materi.pilih', $c->class_id) }}"
           class="bg-[#990000] text-white px-6 py-2 rounded-lg font-bold text-sm inline-block shadow-sm hover:bg-red-800">
           SELECT THIS CLASS
        </a>
    </div>
    @endforeach
</div>
@endsection
