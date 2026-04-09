@extends('layouts.spekta')
@section('title', 'Manajemen Pengumuman')

@section('content')
<div class="space-y-8">
    <div class="bg-white p-8 rounded-[2rem] shadow-sm border-t-8 border-[#990000]">
        <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tight mb-2">Terbitkan Pengumuman</h3>
        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-8">Informasi ini akan langsung tampil di aplikasi mobile siswa</p>

        <form action="{{ route('admin.announcement.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="title" placeholder="Judul Pengumuman" class="p-3.5 rounded-2xl bg-gray-50 border-none shadow-inner w-full font-bold" required>
                <div class="bg-gray-50 p-2 rounded-2xl shadow-inner border border-dashed border-gray-200">
                    <input type="file" name="image" class="text-[10px] text-gray-400 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-red-50 file:text-[#990000]" required>
                </div>
            </div>
            <textarea name="description" rows="3" placeholder="Isi deskripsi pengumuman..." class="p-4 rounded-2xl bg-gray-50 border-none shadow-inner w-full font-medium" required></textarea>
            <button class="w-full bg-[#990000] text-white font-black py-4 rounded-2xl uppercase text-[10px] tracking-[0.2em] shadow-lg hover:bg-red-800 transition">
                Publish Pengumuman Sekarang
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($announcements as $row)
        <div class="bg-white rounded-[2rem] overflow-hidden shadow-sm border border-gray-100 flex flex-col">
            <img src="{{ asset('storage/' . $row->image) }}" class="h-48 w-full object-cover">
            <div class="p-6 flex-1">
                <h4 class="font-black text-gray-800 uppercase text-sm mb-2">{{ $row->title }}</h4>
                <p class="text-gray-500 text-xs leading-relaxed line-clamp-3 mb-6">{{ $row->description }}</p>

                <div class="flex gap-2 pt-4 border-t border-gray-50">
                    <a href="{{ route('admin.announcement.edit', $row->announcementsID) }}" class="flex-1 text-center bg-blue-50 text-blue-600 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-blue-600 hover:text-white transition">Edit</a>
                    <form action="{{ route('admin.announcement.destroy', $row->announcementsID) }}" method="POST" class="flex-1">
                        @csrf @method('DELETE')
                        <button class="w-full bg-red-50 text-red-600 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-red-600 hover:text-white transition" onclick="return confirm('Hapus?')">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
