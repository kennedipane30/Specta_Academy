@extends('layouts.spekta')
@section('title', 'Manage Practice Questions')

@section('content')
<div class="bg-white p-8 rounded-3xl shadow-md border-t-8 border-[#990000]">
    <div class="flex justify-between items-center mb-8">
        {{-- MODIFIKASI: program_name --}}
        <h3 class="text-2xl font-black uppercase text-gray-800">{{ $class->program_name }}</h3>
        <a href="{{ route('pengajar.latihan.index') }}" class="text-xs font-bold text-gray-400">&larr; BACK</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-8 font-bold">{{ session('success') }}</div>
    @endif

    <div class="space-y-10">
        @php
            $subjects = ['English Material', 'Mathematics Material', 'Psychological Test Material', 'TIU Material', 'TWK Material'];
        @endphp

        @foreach($subjects as $subject)
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="bg-gray-50 px-6 py-4 border-b flex flex-col lg:flex-row justify-between items-center gap-4">
                    <h4 class="text-xl font-black text-gray-800 uppercase">{{ $subject }}</h4>

                    {{-- MODIFIKASI: class_id --}}
                    <form action="{{ route('pengajar.latihan.store', $class->class_id) }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3 bg-white p-2 border rounded-xl shadow-inner">
                        @csrf
                        <input type="hidden" name="subject" value="{{ $subject }}">

                        <select name="week" required class="text-[11px] font-bold border-none bg-gray-100 rounded-lg py-1.5 focus:ring-0">
                            <option value="" disabled selected>Select Week</option>
                            @for($i = 1; $i <= 20; $i++)
                                <option value="{{ $i }}">Week {{ $i }}</option>
                            @endfor
                        </select>

                        <input type="file" name="file_csv" accept=".csv" required class="text-[10px] w-40">

                        <button type="submit" class="bg-[#990000] text-white px-4 py-2 rounded-lg text-[10px] font-bold uppercase hover:bg-red-800">
                            IMPORT CSV
                        </button>
                    </form>
                </div>

                <div class="p-6">
                    @php
                        $items = $practices->where('subject', $subject)->groupBy('week')->sortKeys();
                    @endphp

                    @if($items->count() > 0)
                        <div class="space-y-3">
                            @foreach($items as $week => $soalGroup)
                                <div class="flex items-center justify-between p-4 bg-gray-50 border rounded-2xl">
                                    <div class="flex items-center gap-4">
                                        <div class="bg-red-100 text-[#990000] w-12 h-12 flex items-center justify-center rounded-xl font-black text-xs">W-{{ $week }}</div>
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase">Week {{ $week }}</p>
                                            <h5 class="text-sm font-black text-gray-800 uppercase">{{ $soalGroup->count() }} Questions Available</h5>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold text-green-600 uppercase bg-green-50 px-3 py-1 rounded-full tracking-tighter">Ready to Play</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center py-6 text-xs text-gray-400 italic">No practice questions available yet.</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
