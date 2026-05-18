@extends('layouts.spekta')
@section('content')
<div class="p-6">
    <h1 class="text-2xl font-black text-gray-800 uppercase mb-8">Pilih Program Kelas</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($classes as $c)
        <a href="{{ route('admin.scores.pilih_tryout', $c->class_id) }}" class="group bg-white p-8 rounded-[40px] shadow-sm border border-gray-100 hover:border-[#990000] transition-all">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Program</p>
            <h3 class="text-lg font-black text-gray-800 group-hover:text-[#990000] uppercase leading-tight">{{ $c->program_name }}</h3>
        </a>
        @endforeach
    </div>
</div>
@endsection
