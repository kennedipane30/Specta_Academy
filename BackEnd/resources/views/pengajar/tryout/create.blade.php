@extends('layouts.spekta')
@section('title', 'Kelola Tryout')

@section('content')
<div class="max-w-5xl mx-auto space-y-10">

    {{-- BAGIAN ATAS: FORM INPUT --}}
    <div class="bg-white p-10 rounded-[2.5rem] shadow-xl border-t-8 border-[#990000]">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">Setup <span class="text-[#990000]">Tryout</span></h3>
                <p class="text-gray-400 font-bold text-[10px] uppercase tracking-widest mt-1">{{ $class->program_name }}</p>
            </div>
            <a href="{{ route('pengajar.tryout.index') }}" class="text-xs font-black text-gray-400 hover:text-spekta transition">&larr; BACK</a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl font-bold">{{ session('success') }}</div>
        @endif

        <form action="{{ route('pengajar.tryout.import') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf
            <input type="hidden" name="class_id" value="{{ $class->class_id }}">

            <div class="space-y-4">
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Tryout Title</label>
                    <input type="text" name="title" placeholder="Ex: Simulasi Batch 1" class="w-full p-4 rounded-2xl bg-gray-50 border-none shadow-inner font-bold" required>
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Duration (Min)</label>
                    <input type="number" name="duration" value="60" class="w-full p-4 rounded-2xl bg-gray-50 border-none shadow-inner font-bold" required>
                </div>
            </div>

            <div class="bg-gray-50 p-6 rounded-3xl border-2 border-dashed border-gray-200 text-center flex flex-col justify-center items-center">
                <label class="text-xs font-black text-gray-600 mb-2 uppercase">Choose Question CSV</label>
                <input type="file" name="file_csv" accept=".csv" class="text-[10px]" required>
            </div>

            <button type="submit" class="md:col-span-2 bg-[#990000] text-white py-4 rounded-2xl font-black shadow-lg hover:bg-red-800 transition uppercase tracking-widest text-xs">
                🚀 Publish Tryout Now
            </button>
        </form>
    </div>

    {{-- BAGIAN BAWAH: DAFTAR TRYOUT YANG SUDAH TERBIT (SESUAI GAMBAR 3) --}}
    <div class="space-y-4">
        <div class="flex items-center gap-4">
            <h4 class="font-black text-gray-800 uppercase tracking-tight">Published Tryouts</h4>
            <div class="flex-1 h-[2px] bg-gray-100"></div>
        </div>

        <div class="grid grid-cols-1 gap-4">
            @forelse($tryouts as $t)
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between hover:shadow-md transition">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 bg-red-50 text-[#990000] rounded-2xl flex items-center justify-center shadow-sm">
                            <i class="fas fa-stopwatch text-xl"></i>
                        </div>
                        <div>
                            <h5 class="font-black text-gray-800 uppercase text-sm mb-1">{{ $t->title }}</h5>
                            <div class="flex items-center gap-4">
                                <span class="text-[10px] font-bold text-gray-400 uppercase"><i class="far fa-clock mr-1"></i> {{ $t->duration }} Min</span>
                                <span class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-lg uppercase">{{ $t->questions_count }} Questions Available</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- TOMBOL HAPUS --}}
                        <form action="{{ route('pengajar.tryout.destroy', $t->tryout_id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center hover:bg-red-600 hover:text-white transition shadow-sm" onclick="return confirm('Hapus tryout ini beserta semua soalnya?')">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 bg-gray-50 rounded-3xl border-2 border-dashed border-white">
                    <p class="text-gray-400 font-bold uppercase text-[10px] tracking-widest italic">No tryouts published for this class yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
