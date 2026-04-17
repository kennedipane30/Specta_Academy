@extends('layouts.spekta')
@section('title', 'Teaching Schedule') {{-- Tambahkan title jika perlu --}}

@section('content')
<div class="bg-white p-8 rounded-2xl shadow-md border-t-8 border-[#990000]">
    <h3 class="text-xl font-bold mb-4 uppercase tracking-tight">Your Teaching Schedule</h3>
    <p class="text-gray-500 mb-8 text-sm italic">This page displays the list of classes where you are assigned to teach.</p>

    <div class="space-y-4">
        @forelse($jadwal as $j)
        <div class="flex items-center p-6 bg-red-50 rounded-2xl border-l-8 border-[#990000] shadow-sm">
            <div class="text-center pr-8 border-r border-red-200">
                {{-- Format tanggal tetap sama --}}
                <h4 class="text-2xl font-bold text-[#990000]">{{ date('d', strtotime($j->date)) }}</h4>
                <p class="text-xs uppercase font-bold">{{ date('M Y', strtotime($j->date)) }}</p>
            </div>
            <div class="pl-8 flex-1">
                <h5 class="text-lg font-black text-gray-800 uppercase">{{ $j->title }}</h5>

                {{-- PERBAIKAN: Gunakan relasi 'class' dan atribut 'program_name' --}}
                <p class="text-sm text-red-700 font-bold uppercase">
                    {{ $j->class->program_name ?? 'Program Not Found' }}
                </p>

                <div class="mt-2 text-xs font-bold text-gray-500 flex items-center gap-2">
                    <i class="far fa-clock"></i> {{ date('H:i', strtotime($j->start_time)) }} - {{ date('H:i', strtotime($j->end_time)) }} WIB
                </div>
            </div>
            <div class="bg-white px-4 py-2 rounded-xl text-[#990000] font-black text-[10px] shadow-sm uppercase tracking-widest">
                Ready to Teach
            </div>
        </div>
        @empty
        <div class="text-center p-20 text-gray-400 italic font-bold">
            No teaching schedule available for you yet.
        </div>
        @endforelse
    </div>
</div>
@endsection
