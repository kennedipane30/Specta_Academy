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

        {{-- 2. FORM MASTER UPLOAD --}}
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

            {{-- ✨ PERBAIKAN: Tambahkan enctype="multipart/form-data" agar file terkirim ✨ --}}
            <form action="{{ route('admin.tryout.upload') }}" method="POST" enctype="multipart/form-data" class="flex gap-4">
                @csrf
                <select name="class_id" class="bg-gray-50 border-none rounded-2xl px-4 text-xs font-bold focus:ring-2 focus:ring-[#990000]" required>
                    <option value="">-- Pilih Kelas Tujuan --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->class_id }}">{{ $class->program_name }}</option>
                    @endforeach
                </select>
                <input type="file" name="file_csv" class="flex-1 text-xs font-bold text-gray-400 file:bg-gray-100 file:border-none file:px-4 file:py-2 file:rounded-xl file:mr-4" required>
                <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-2xl font-black text-[10px] uppercase shadow-lg hover:bg-green-700 transition">
                    Publish Sekarang
                </button>
            </form>
        </div>
    </div>

    {{-- Alert Berhasil --}}
    @if(session('success'))
        <div class="bg-green-600 text-white p-4 rounded-2xl mb-4 font-bold text-xs uppercase shadow-lg shadow-green-100">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Alert Gagal --}}
    @if(session('error'))
        <div class="bg-red-600 text-white p-4 rounded-2xl mb-4 font-bold text-xs uppercase shadow-lg">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- 3. TABEL REVIEW DRAFT (Penyedia soal dari pengajar) --}}
    <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden mb-10">
        <div class="p-8 border-b border-gray-50 flex justify-between items-center">
            <h3 class="text-xl font-black text-gray-800 uppercase tracking-tighter">Review Kiriman Pengajar</h3>
            <span class="text-[10px] font-bold text-gray-400 uppercase bg-gray-50 px-4 py-2 rounded-full">DRAFT</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="p-6 text-[10px] font-black text-gray-400 uppercase">Kelas</th>
                        <th class="p-6 text-[10px] font-black text-gray-400 uppercase">Status Pengajar</th>
                        <th class="p-6 text-center text-[10px] font-black text-gray-400 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($classes as $c)
                    @php $count = $submissions->where('class_id', $c->class_id)->count(); @endphp
                    <tr>
                        <td class="p-6">
                            <p class="font-black text-gray-800 text-sm uppercase">{{ $c->program_name }}</p>
                            <span class="text-[10px] font-bold text-[#990000]">{{ $count }} Soal Terkirim</span>
                        </td>
                        <td class="p-6">
                            @foreach($submissions->where('class_id', $c->class_id)->unique('subject_name') as $s)
                                <span class="bg-gray-100 px-2 py-1 rounded text-[9px] font-black text-gray-500 uppercase mr-1">{{ $s->subject_name }}</span>
                            @endforeach
                        </td>
                        <td class="p-6 text-center">
                            @if($count > 0)
                            <a href="{{ route('admin.tryout.export', $c->class_id) }}" class="bg-[#990000] text-white px-4 py-2 rounded-xl font-black text-[9px] uppercase shadow-lg">Download CSV</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- 4. TABEL PAKET TERBIT (MOBILE LIVE) --}}
    <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden border-t-8 border-green-500">
        <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-green-50/30">
            <h3 class="text-xl font-black text-gray-800 uppercase tracking-tighter">🚀 Paket Terbit (Live di Mobile)</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr class="text-[10px] font-black text-gray-400 uppercase">
                        <th class="p-6">Program Kelas</th>
                        <th class="p-6">Total Soal Live</th>
                        <th class="p-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($activeTryouts as $live)
                    <tr>
                        <td class="p-6 font-black text-sm uppercase">{{ $live->classModel->program_name ?? 'N/A' }}</td>
                        <td class="p-6"><span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-black">{{ $live->total }} SOAL</span></td>
                        <td class="p-6 text-center">
                            <form action="{{ route('admin.tryout.destroy_package', $live->class_id) }}" method="POST" onsubmit="return confirm('Hapus seluruh soal paket ini dari Mobile?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 transition font-black text-[10px] uppercase">
                                    <i class="fas fa-trash mr-1"></i> Hapus Paket
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="p-10 text-center text-gray-300 uppercase font-black text-xs">Belum ada paket yang live di Mobile.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
