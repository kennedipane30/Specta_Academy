@extends('layouts.spekta')
@section('title', 'Manage Learning Modules')

@section('content')
<div class="bg-white p-8 rounded-3xl shadow-md border-t-8 border-[#990000]">
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            {{-- MODIFIKASI: program_name --}}
            <h3 class="text-2xl font-black uppercase text-gray-800 tracking-tight">{{ $class->program_name }}</h3>
            <p class="text-sm text-gray-500 font-medium">Upload PDF modules with specific titles for each week.</p>
        </div>
        <a href="{{ route('pengajar.materi.index') }}" class="bg-gray-100 text-gray-600 px-5 py-2 rounded-xl text-xs font-bold hover:bg-gray-200 transition">&larr; BACK</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-8 rounded-xl font-bold flex items-center shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-10">
        {{-- Subject dalam bahasa Inggris --}}
        @php $subjects = ['English Material', 'Mathematics Material', 'Psychological Test Material', 'TIU Material', 'TWK Material']; @endphp

        @foreach($subjects as $subject)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                {{-- HEADER MAPEL + FORM TAMBAH --}}
                <div class="bg-gray-50 px-6 py-4 border-b flex flex-col lg:flex-row justify-between items-center gap-4">
                    <h4 class="text-xl font-black text-gray-800 uppercase">{{ $subject }}</h4>

                    {{-- MODIFIKASI: action menggunakan class_id --}}
                    <form action="{{ route('pengajar.materi.store', $class->class_id) }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3 bg-white p-3 border rounded-xl shadow-inner w-full lg:w-auto">
                        @csrf
                        <input type="hidden" name="title" value="{{ $subject }}">

                        {{-- MODIFIKASI: name="material_name" --}}
                        <input type="text" name="material_name" placeholder="Module Title (Ex: Basic Grammar)" required class="text-[11px] border-gray-200 bg-gray-50 rounded-lg py-1.5 px-3 focus:ring-[#990000] flex-1 lg:w-48">

                        {{-- MODIFIKASI: name="week" --}}
                        <select name="week" required class="text-[11px] font-bold border-gray-200 bg-gray-50 rounded-lg py-1.5">
                            <option value="" disabled selected>Week...</option>
                            @for($i = 1; $i <= 20; $i++) <option value="{{ $i }}">Week {{ $i }}</option> @endfor
                        </select>

                        <input type="file" name="file_pdf" accept=".pdf" required class="text-[10px] w-32">

                        <button type="submit" class="bg-[#990000] text-white px-4 py-2 rounded-lg text-[10px] font-bold uppercase hover:bg-red-800 transition">ADD</button>
                    </form>
                </div>

                {{-- LIST MODUL --}}
                <div class="p-6">
                    @php
                        // MODIFIKASI: sortBy('week')
                        $uploadedItems = $materis->where('title', $subject)->whereNotNull('file_path')->sortBy('week');
                    @endphp

                    @if($uploadedItems->count() > 0)
                        <div class="space-y-3">
                            @foreach($uploadedItems as $item)
                                <div class="flex items-center justify-between p-4 bg-gray-50 border rounded-2xl hover:bg-white transition duration-200 shadow-sm">
                                    <div class="flex items-center gap-4">
                                        {{-- MODIFIKASI: $item->week --}}
                                        <div class="bg-red-100 text-[#990000] w-12 h-12 flex items-center justify-center rounded-xl font-black text-xs">
                                            W-{{ $item->week }}
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase">Week {{ $item->week }}</p>
                                            {{-- MODIFIKASI: $item->material_name --}}
                                            <h5 class="text-sm font-black text-gray-800 uppercase">{{ $item->material_name ?? 'Untitled Module' }}</h5>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="bg-white border border-[#990000] text-[#990000] px-4 py-2 rounded-xl text-[10px] font-black hover:bg-[#990000] hover:text-white transition uppercase shadow-sm">View PDF</a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 border border-dashed rounded-2xl"><p class="text-xs text-gray-400 italic">No modules uploaded yet</p></div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
