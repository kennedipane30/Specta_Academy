@extends('layouts.spekta')
@section('title', 'Edit Galeri')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-lg border-t-8 border-blue-500">
    <h2 class="text-xl font-bold mb-6">Edit Foto Galeri</h2>
    <form action="{{ route('admin.galeri.update', $item->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="mb-4">
            <label class="block font-bold mb-1">Judul Foto</label>
            <input type="text" name="judul" value="{{ $item->judul }}" class="w-full border p-2 rounded-lg">
        </div>
        <div class="mb-4">
            <label class="block font-bold mb-1">Foto Saat Ini</label>
            <img src="{{ asset('storage/'.$item->foto) }}" class="h-32 rounded mb-2">
            <input type="file" name="foto" class="w-full border p-2 rounded-lg text-sm">
            <p class="text-[10px] text-gray-500">*Kosongkan jika tidak ingin ganti foto</p>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg font-bold">SIMPAN PERUBAHAN</button>
            <a href="{{ route('admin.galeri.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg font-bold">BATAL</a>
        </div>
    </form>
</div>
@endsection
