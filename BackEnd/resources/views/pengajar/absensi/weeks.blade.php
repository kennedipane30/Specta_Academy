@extends('layouts.spekta')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-black text-gray-800 uppercase">{{ $class->program_name }}</h2>
    <p class="text- spekta font-bold">REKAPITULASI MINGGUAN: {{ $subject }}</p>
</div>

<div class="grid grid-cols-2 md:grid-cols-5 gap-4">
    @for($i=1; $i<=20; $i++)
        @php $isDone = in_array($i, $doneWeeks); @endphp
        <a href="{{ $isDone ? route('pengajar.absensi.recap', [$class->class_id, $subject, $i]) : route('pengajar.absensi.create', [$class->class_id, $subject, $i]) }}"
           class="p-6 rounded-3xl border-2 transition duration-300 flex flex-col items-center justify-center gap-2
           {{ $isDone ? 'bg-green-50 border-green-200 text-green-700' : 'bg-white border-gray-100 text-gray-400 hover:border-red-200' }}">
            <span class="text-[10px] font-black uppercase">Week</span>
            <span class="text-3xl font-black">{{ $i }}</span>
            <span class="text-[9px] font-bold uppercase">{{ $isDone ? 'Lihat Recap' : 'Mulai Absen' }}</span>
        </a>
    @endfor
</div>
@endsection
