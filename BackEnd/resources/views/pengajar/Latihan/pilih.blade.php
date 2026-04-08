@extends('layouts.spekta')
@section('title', 'Kelola Latihan Soal')

@section('content')
<div class="bg-white p-8 rounded-3xl shadow-md border-t-8 border-[#990000]">
    <div class="flex justify-between items-center mb-8">
        <h3 class="text-2xl font-black uppercase text-gray-800">{{ $class->nama_program }}</h3>
        <a href="{{ route('pengajar.latihan.index') }}" class="text-xs font-bold text-gray-400">&larr; KEMBALI</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-8 font-bold">{{ session('success') }}</div>
    @endif

    <div class="space-y-10">
        @php $subjects = ['Materi Bahasa Inggris', 'Materi Matematika', 'Materi Psikotes', 'Materi TIU', 'Materi TWK']; @endphp

        @foreach($subjects as $subject)
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">

                {{-- HEADER & FORM UPLOAD (SAMA DENGAN ALUR MATERI) --}}
                <div class="bg-gray-50 px-6 py-4 border-b flex flex-col lg:flex-row justify-between items-center gap-4">
                    <h4 class="text-xl font-black text-gray-800 uppercase">{{ $subject }}</h4>

                    <form action="{{ route('pengajar.latihan.store', $class->class_modelsID) }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3 bg-white p-2 border rounded-xl shadow-inner">
                        @csrf
                        <input type="hidden" name="subject" value="{{ $subject }}">

                        {{-- DROPDOWN MINGGU --}}
                        <select name="minggu" required class="text-[11px] font-bold border-none bg-gray-100 rounded-lg py-1.5 focus:ring-0">
                            <option value="" disabled selected>Pilih Minggu</option>
                            @for($i = 1; $i <= 20; $i++)
                                <option value="{{ $i }}">Minggu {{ $i }}</option>
                            @endfor
                        </select>

                        <input type="file" name="file_csv" accept=".csv" required class="text-[10px] w-40">

                        <button type="submit" class="bg-[#990000] text-white px-4 py-2 rounded-lg text-[10px] font-bold uppercase hover:bg-red-800">
                            IMPORT CSV
                        </button>
                    </form>
                </div>

                {{-- DAFTAR SOAL YANG SUDAH ADA --}}
                <div class="p-6">
                    @php $items = $latihans->where('subject', $subject)->groupBy('minggu')->sortKeys(); @endphp

                    @if($items->count() > 0)
                        <div class="space-y-3">
                            @foreach($items as $minggu => $soalGroup)
                                <div class="flex items-center justify-between p-4 bg-gray-50 border rounded-2xl">
                                    <div class="flex items-center gap-4">
                                        <div class="bg-red-100 text-[#990000] w-12 h-12 flex items-center justify-center rounded-xl font-black text-xs">W-{{ $minggu }}</div>
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase">Minggu ke-{{ $minggu }}</p>
                                            <h5 class="text-sm font-black text-gray-800 uppercase">{{ $soalGroup->count() }} Soal Tersedia</h5>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold text-green-600 uppercase bg-green-50 px-3 py-1 rounded-full tracking-tighter">Ready to Play</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center py-6 text-xs text-gray-400 italic">Belum ada latihan soal.</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
