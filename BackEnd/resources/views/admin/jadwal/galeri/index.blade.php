@extends('layouts.spekta')
@section('title', 'Manajemen Galeri')

@section('content')
<div class="bg-white p-8 rounded-2xl shadow-sm border-t-8 border-[#990000]">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-spekta uppercase">Galeri Kegiatan</h2>
        <!-- Form Tambah Cepat -->
        <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data" class="flex gap-2">
            @csrf
            <input type="text" name="judul" placeholder="Judul..." class="border p-2 rounded-lg text-sm" required>
            <input type="file" name="foto" class="border p-1 rounded-lg text-xs" required>
            <button class="bg-[#990000] text-white px-4 py-2 rounded-lg font-bold text-sm">UPLOAD</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        @foreach($galeri as $row)
        <div class="bg-gray-50 rounded-xl overflow-hidden shadow-sm border group">
            <img src="{{ url('/view-galeri/' . basename($row->foto)) }}" class="w-full h-32 object-cover rounded">
            <div class="p-4">
                <h4 class="font-bold text-sm">{{ $row->judul }}</h4>
                <p class="text-[10px] text-gray-400 mt-1 italic">Diunggah: {{ $row->created_at->format('d M Y') }}</p>

                <div class="flex gap-2 mt-4">
                    <!-- Tombol Edit -->
                    <a href="{{ route('admin.galeri.edit', $row->id) }}" class="bg-blue-500 text-white px-3 py-1 rounded text-[10px] font-bold uppercase">Edit</a>

                    <!-- Tombol Hapus -->
                    <form action="{{ route('admin.galeri.destroy', $row->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-[10px] font-bold uppercase" onclick="return confirm('Hapus foto ini?')">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
