@extends('layouts.spekta')
@section('title', 'Select Class for Tryout')

@section('content')
<div class="bg-white p-8 rounded-3xl shadow-md border-t-8 border-[#990000]">
    <div class="mb-8">
        <h3 class="text-2xl font-black uppercase text-gray-800 tracking-tight">Select Tryout Program</h3>
        <p class="text-sm text-gray-500 font-medium">Please select a class to create a new Tryout event.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($classes as $c)
            <div class="group relative bg-gray-50 rounded-3xl border border-gray-100 overflow-hidden hover:shadow-2xl transition duration-500">
                <div class="h-32 bg-[#990000] relative overflow-hidden">
                    <div class="p-6">
                        <span class="bg-white/20 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase">Tryout Event</span>
                    </div>
                </div>

                <div class="p-6">
                    {{-- FIX: Menggunakan program_name --}}
                    <h4 class="text-lg font-black text-gray-800 uppercase leading-tight mb-4 h-12">
                        {{ $c->program_name }}
                    </h4>

                    {{-- FIX: Menggunakan class_id --}}
                    <a href="{{ route('pengajar.tryout.pilih', $c->class_id) }}"
                       class="block w-full text-center bg-[#990000] text-white py-3 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-red-800 transition shadow-lg shadow-red-100">
                        CREATE NEW TRYOUT &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-20 text-gray-400 italic font-bold">Class data not available.</div>
        @endforelse
    </div>
</div>
@endsection
