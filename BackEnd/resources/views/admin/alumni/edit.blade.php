@extends('layouts.spekta')
@section('title', 'Edit Data Alumni')

@section('content')
<div class="bg-white p-8 rounded-3xl shadow-sm border-t-8 border-[#990000]">
    <div class="flex justify-between items-center mb-8">
        <h3 class="text-xl font-black text-gray-800 uppercase">Edit Alumni</h3>
        <a href="{{ route('admin.alumni.index') }}" class="text-xs font-bold text-gray-400">&larr; KEMBALI</a>
    </div>

    <form action="{{ route('admin.alumni.update', $alumni->alumniID) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-2 uppercase">Nama Lengkap</label>
                <input type="text" name="nama" value="{{ $alumni->nama }}" class="w-full p-4 rounded-2xl bg-gray-50 border-none shadow-inner" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-2 uppercase">Berhasil Menjadi</label>
                <input type="text" name="berhasil_menjadi" value="{{ $alumni->berhasil_menjadi }}" class="w-full p-4 rounded-2xl bg-gray-50 border-none shadow-inner" required>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-400 mb-2 uppercase">Ganti Foto (Kosongkan jika tidak ingin mengubah)</label>
            <div class="flex items-center gap-6 bg-gray-50 p-6 rounded-3xl">
                <img src="{{ asset('storage/' . $alumni->foto) }}" class="w-20 h-20 object-cover rounded-xl shadow-md">
                <input type="file" name="foto" class="text-xs">
            </div>
        </div>

        <button type="submit" class="w-full bg-[#990000] text-white font-black py-4 rounded-2xl uppercase text-xs tracking-widest hover:bg-red-800 transition shadow-lg shadow-red-100">
            Simpan Perubahan
        </button>
    </form>
</div>
@endsection
