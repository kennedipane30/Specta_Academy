@extends('layouts.spekta')
@section('title', 'Manajemen Alumni Sukses')

@section('content')
<div class="space-y-8">

    {{-- BAGIAN ATAS: HEADER & FORM TAMBAH --}}
    <div class="bg-white p-8 rounded-3xl shadow-sm border-t-8 border-[#990000] relative overflow-hidden">
        {{-- Dekorasi Latar Belakang --}}
        <div class="absolute right-0 top-0 w-32 h-32 bg-red-50 rounded-full -mr-16 -mt-16 opacity-50"></div>

        <div class="relative z-10">
            <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Tambah Alumni Berhasil</h3>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Publikasikan kisah sukses siswa Spekta Academy</p>

            <!-- NOTIFIKASI -->
            @if(session('success'))
                <div class="mt-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-xl font-bold text-xs flex items-center animate-pulse">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.alumni.store') }}" method="POST" enctype="multipart/form-data" class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf
                <div class="flex flex-col">
                    <label class="text-[10px] font-black text-gray-400 mb-2 uppercase ml-1">Nama Siswa</label>
                    <input type="text" name="nama" placeholder="Contoh: Kennedi Pane" class="w-full p-3.5 rounded-2xl bg-gray-50 border-none shadow-inner focus:ring-2 focus:ring-red-500 transition text-sm font-bold" required>
                </div>
                <div class="flex flex-col">
                    <label class="text-[10px] font-black text-gray-400 mb-2 uppercase ml-1">Berhasil Menjadi</label>
                    <input type="text" name="berhasil_menjadi" placeholder="Contoh: TNI AD" class="w-full p-3.5 rounded-2xl bg-gray-50 border-none shadow-inner focus:ring-2 focus:ring-red-500 transition text-sm font-bold" required>
                </div>
                <div class="flex flex-col">
                    <label class="text-[10px] font-black text-gray-400 mb-2 uppercase ml-1">Unggah Foto</label>
                    <div class="bg-gray-50 p-2.5 rounded-2xl shadow-inner border border-dashed border-gray-200">
                        <input type="file" name="foto" class="text-[10px] text-gray-400 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:bg-red-50 file:text-[#990000]" required>
                    </div>
                </div>
                <button class="bg-[#990000] text-white font-black py-4 rounded-2xl uppercase text-[10px] tracking-[0.2em] shadow-lg shadow-red-100 hover:bg-red-800 transition transform active:scale-95">
                    Simpan Alumni
                </button>
            </form>
        </div>
    </div>

    {{-- BAGIAN BAWAH: GRID DAFTAR ALUMNI --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        @forelse($alumni as $a)
        <div class="group bg-white rounded-[2rem] overflow-hidden shadow-sm hover:shadow-2xl hover:-translate-y-2 transition duration-500 border border-gray-100">
            {{-- Bagian Foto --}}
            <div class="relative h-64 overflow-hidden">
                <img src="{{ asset('storage/' . $a->foto) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-500"></div>

                {{-- Badge Status --}}
                <div class="absolute top-4 left-4">
                    <span class="bg-white/90 backdrop-blur-sm text-[#990000] text-[9px] font-black px-3 py-1.5 rounded-full shadow-sm uppercase tracking-tighter">
                        Spekta Alumni
                    </span>
                </div>
            </div>

            {{-- Detail Info --}}
            <div class="p-6 text-center">
                <h4 class="text-lg font-black text-gray-800 uppercase tracking-tight leading-tight mb-1">{{ $a->nama }}</h4>
                <div class="flex justify-center mb-6">
                    <span class="text-[10px] font-black text-red-600 bg-red-50 px-4 py-1.5 rounded-xl uppercase tracking-widest border border-red-100">
                        {{ $a->berhasil_menjadi }}
                    </span>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-center gap-4 pt-4 border-t border-gray-50">
                    <a href="{{ route('admin.alumni.edit', $a->alumniID) }}" class="text-blue-500 hover:text-blue-700 transition">
                        <div class="p-2 bg-blue-50 rounded-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                    </a>

                    <form action="{{ route('admin.alumni.destroy', $a->alumniID) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:text-red-700 transition" onclick="return confirm('Hapus data ini?')">
                            <div class="p-2 bg-red-50 rounded-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-20 bg-white rounded-[2rem] border border-dashed border-gray-200">
            <div class="text-gray-300 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest italic">Belum ada data alumni yang diunggah.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
