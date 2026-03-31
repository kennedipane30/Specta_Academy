@extends('layouts.spekta')
@section('title', 'Upload Materi PDF')

@section('content')
<div class="bg-white p-8 rounded-3xl shadow-md border-t-8 border-[#990000]">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h3 class="text-2xl font-bold uppercase text-gray-800">{{ $class->nama_program }}</h3>
            <p class="text-sm text-gray-500">Silakan kelola file PDF untuk setiap mata pelajaran.</p>
        </div>
        <a href="{{ route('pengajar.materi.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-gray-200 transition">
            &larr; KEMBALI
        </a>
    </div>

    <!-- NOTIFIKASI BERHASIL -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-8 rounded-xl font-bold flex items-center animate-pulse">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        @forelse($materis as $m)
            <div class="flex flex-col md:flex-row md:items-center justify-between p-6 bg-gray-50 rounded-2xl border border-gray-200 hover:shadow-sm transition">

                <!-- SISI KIRI: Nama Materi & Status -->
                <div class="flex items-center gap-4">
                    <div class="bg-white p-3 rounded-xl text-[#990000] shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-800">{{ $m->title }}</h4>

                        {{-- LOGIKA STATUS: BERUBAH JIKA FILE ADA --}}
                        @if($m->file_path)
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] bg-green-500 text-white px-2 py-0.5 rounded-full font-black uppercase tracking-tighter animate-bounce">File Ready</span>
                                <a href="{{ asset('storage/' . $m->file_path) }}" target="_blank" class="text-[#990000] font-bold text-xs underline flex items-center hover:text-red-700">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    Buka Materi PDF
                                </a>
                            </div>
                        @else
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] bg-red-500 text-white px-2 py-0.5 rounded-full font-black uppercase tracking-tighter">Belum Ada File</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- SISI KANAN: Form Upload -->
                <div class="mt-4 md:mt-0 bg-white p-3 rounded-2xl border border-gray-100 flex items-center shadow-inner">
                    <form action="{{ route('pengajar.materi.store', $m->materialsID) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-3">
                        @csrf
                        <input type="file" name="file_pdf" class="text-[10px] text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:bg-red-50 file:text-[#990000] hover:file:bg-red-100" accept=".pdf" required>
                        <button type="submit" class="bg-[#990000] text-white px-4 py-2 rounded-xl text-[11px] font-bold hover:bg-red-800 transition shadow-md">
                            {{ $m->file_path ? 'UPDATE PDF' : 'UPLOAD PDF' }}
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="text-center py-20 text-gray-400 italic">Data materi belum tersedia.</div>
        @endforelse
    </div>
</div>
@endsection
