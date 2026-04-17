@extends('layouts.spekta')
@section('title', 'Select Class - Score Monitoring')

@section('content')
<div class="p-6">
    {{-- Header Section --}}
    <div class="mb-10 animate__animated animate__fadeInLeft">
        <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tight">Student <span class="text-[#990000]">Scores</span></h1>
        <p class="text-gray-500 text-sm">Select a class program to view detailed student performance reports.</p>
    </div>

    {{-- Penentuan Rute Dinamis --}}
    @php
        $routePrefix = Auth::user()->role_id == 1 ? 'admin.scores' : 'pengajar.tryout.nilai';
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($classes as $c)
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 hover:shadow-2xl transition duration-500 group relative overflow-hidden">
            {{-- Icon Decoration --}}
            <div class="w-16 h-16 bg-red-50 text-[#990000] rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#990000] group-hover:text-white transition-colors duration-500">
                <i class="fas fa-graduation-cap text-2xl"></i>
            </div>

            <h4 class="text-xl font-black text-gray-800 uppercase leading-tight mb-4">{{ $c->program_name }}</h4>
            <p class="text-xs text-gray-400 font-bold uppercase mb-8">Click to access all exam results</p>

            {{-- Link Dinamis menyesuaikan Role --}}
            <a href="{{ route($routePrefix . '.detail', $c->class_id) }}"
               class="block w-full text-center bg-gray-900 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-[#990000] transition shadow-lg shadow-gray-200 transform active:scale-95">
               VIEW ALL SCORES &rarr;
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
