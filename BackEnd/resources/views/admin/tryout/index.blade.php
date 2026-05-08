@extends('layouts.spekta')
@section('title', 'Manajemen Master Tryout')

@section('content')
<div class="space-y-10">

    {{-- 1. HEADER STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-[#990000] to-red-800 p-8 rounded-[40px] text-white shadow-xl">
            <h4 class="text-[10px] font-black uppercase tracking-widest opacity-60">Total Kiriman Soal</h4>
            <p class="text-4xl font-black mt-2">{{ $submissions->count() }}</p>
            <p class="text-[10px] font-bold mt-4 uppercase">Siap untuk dikurasi & dieksport</p>
        </div>

        {{-- 2. FORM MASTER UPLOAD (Langkah Terakhir ke Mobile) --}}
        <div class="md:col-span-2 bg-white p-8 rounded-[40px] shadow-sm border border-gray-100">
            <div class="flex items-center gap-4 mb-6">
                <div class="bg-green-100 text-green-600 p-3 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-gray-800 uppercase leading-none">Publish ke Mobile</h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Upload CSV Master yang sudah Anda kurasi</p>
                </div>
            </div>

            <form action="{{ route('admin.tryout.upload') }}" method="POST" enctype="multipart/form-data" class="flex gap-4">
                @csrf
                <select name="class_id" class="bg-gray-50 border-none rounded-2xl px-4 text-xs font-bold focus:ring-2 focus:ring-[#990000]" required>
                    <option value="">-- Pilih Kelas Tujuan --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->class_id }}">{{ $class->program_name }}</option>
                    @endforeach
                </select>
                <input type="file" name="file_csv" class="flex-1 text-xs font-bold text-gray-400 file:bg-gray-100 file:border-none file:px-4 file:py-2 file:rounded-xl file:mr-4" required>
                <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-2xl font-black text-[10px] uppercase shadow-lg shadow-green-100 hover:bg-green-700 transition">
                    Publish Sekarang
                </button>
            </form>
        </div>
    </div>

    {{-- 3. TABEL REVIEW & EXPORT --}}
    <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex justify-between items-center">
            <h3 class="text-xl font-black text-gray-800 uppercase tracking-tighter">Review Kiriman Pengajar</h3>
            <span class="text-[10px] font-bold text-gray-400 uppercase bg-gray-50 px-4 py-2 rounded-full">Kumpulan Soal Draft</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pengajar</th>
                        <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kelas & Subjek</th>
                        <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Export CSV Bidang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    {{-- Kita grouping berdasarkan kelas agar Admin bisa download gabungan --}}
                    @foreach($classes as $c)
                    @php
                        $countClassSoal = $submissions->where('class_id', $c->class_id)->count();
                    @endphp
                    <tr class="hover:bg-gray-50/30 transition">
                        <td class="p-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-red-50 rounded-2xl flex items-center justify-center font-black text-[#990000]">
                                    {{ $countClassSoal }}
                                </div>
                                <div>
                                    <p class="font-black text-gray-800 text-sm uppercase">Total {{ $countClassSoal }} Soal</p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Menunggu Kurasi</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-6">
                            <p class="font-black text-[#990000] text-sm uppercase">{{ $c->program_name }}</p>
                            <div class="flex flex-wrap gap-2 mt-2">
                                {{-- Menampilkan siapa saja pengajar yang sudah isi di kelas ini --}}
                                @foreach($submissions->where('class_id', $c->class_id)->unique('subject_name') as $sub)
                                    <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-md text-[9px] font-bold uppercase tracking-tighter">
                                        {{ $sub->subject_name }}: {{ $sub->user->name }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="p-6 text-center">
                            @if($countClassSoal > 0)
                                <a href="{{ route('admin.tryout.export', $c->class_id) }}"
                                   class="inline-flex items-center gap-2 bg-[#990000] text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase shadow-lg shadow-red-100 hover:bg-red-800 transition transform active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Download CSV Gabungan
                                </a>
                            @else
                                <span class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">Belum ada soal</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
