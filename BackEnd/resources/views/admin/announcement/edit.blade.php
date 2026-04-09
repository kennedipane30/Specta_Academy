@extends('layouts.spekta')
@section('title', 'Edit Pengumuman')

@section('content')
<div class="bg-white p-10 rounded-[2.5rem] shadow-sm border-t-8 border-[#990000] max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h3 class="text-2xl font-black text-gray-800 uppercase">Edit Pengumuman</h3>
        <a href="{{ route('admin.announcement.index') }}" class="text-[10px] font-black text-gray-400 uppercase tracking-widest">&larr; Batal</a>
    </div>

    <form action="{{ route('admin.announcement.update', $announcement->announcementsID) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        <div>
            <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Judul</label>
            <input type="text" name="title" value="{{ $announcement->title }}" class="w-full p-4 rounded-2xl bg-gray-50 border-none shadow-inner font-bold mt-1" required>
        </div>

        <div>
            <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Deskripsi</label>
            <textarea name="description" rows="5" class="w-full p-4 rounded-2xl bg-gray-50 border-none shadow-inner font-medium mt-1" required>{{ $announcement->description }}</textarea>
        </div>

        <div>
            <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Ganti Gambar (Opsional)</label>
            <div class="flex items-center gap-6 bg-gray-50 p-4 rounded-2xl mt-1">
                <img src="{{ asset('storage/' . $announcement->image) }}" class="w-24 h-24 object-cover rounded-xl shadow-md">
                <input type="file" name="image" class="text-[10px]">
            </div>
        </div>

        <button class="w-full bg-[#990000] text-white font-black py-4 rounded-2xl shadow-lg shadow-red-100 uppercase text-xs tracking-widest hover:bg-red-800 transition">
            Simpan Perubahan
        </button>
    </form>
</div>
@endsection
