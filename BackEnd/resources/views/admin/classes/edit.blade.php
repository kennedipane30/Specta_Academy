@extends('layouts.spekta')

@section('title', 'Edit Konten Kelas - Spekta Academy')

@section('content')
<div class="p-10 w-full max-w-5xl">
    <!-- Header Navigation -->
    <div class="mb-10 flex items-center justify-between animate__animated animate__fadeIn">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight uppercase italic">
                PENGATURAN <span class="text-[#990000]">DETAIL KELAS</span>
            </h1>
            <p class="text-gray-500 font-medium">Update visual dan informasi untuk program: <b>{{ $class->name }}</b></p>
        </div>
        <a href="{{ route('admin.classes.index') }}" class="text-[10px] font-black text-gray-400 uppercase hover:text-[#990000] transition-colors tracking-widest flex items-center gap-2">
            <i class="fas fa-chevron-left"></i> KEMBALI KE DAFTAR
        </a>
    </div>

    <!-- Form Edit -->
    <form action="{{ route('admin.classes.update', $class->class_id) }}" method="POST" enctype="multipart/form-data" ...>

        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            
            <!-- SISI KIRI: DATA TEKS -->
            <div class="space-y-8">
                <!-- Input Harga -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-3 tracking-widest">
                        Harga Pendaftaran (IDR)
                    </label>
                    <div class="relative group">
                        <span class="absolute left-5 top-5 font-black text-gray-400 group-focus-within:text-[#990000] transition-colors">Rp</span>
                        <input type="number" 
                               name="price" 
                               value="{{ old('price', $class->price) }}" 
                               class="w-full bg-gray-50 border-none rounded-3xl p-5 pl-14 font-black text-2xl focus:ring-2 focus:ring-[#990000] transition-all" 
                               placeholder="Contoh: 900000"
                               required>
                    </div>
                    @error('price') <p class="text-red-500 text-[10px] mt-2 font-bold">{{ $message }}</p> @enderror
                </div>

                <!-- Input Deskripsi -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-3 tracking-widest">
                        Deskripsi Program (Tampil di App Mobile)
                    </label>
                    <textarea name="description" 
                              rows="8" 
                              class="w-full bg-gray-50 border-none rounded-3xl p-6 focus:ring-2 focus:ring-[#990000] text-sm leading-relaxed text-gray-600 transition-all" 
                              placeholder="Jelaskan keunggulan kelas ini kepada calon siswa..."
                              required>{{ old('description', $class->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-[10px] mt-2 font-bold">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- SISI KANAN: VISUAL (BANNER) -->
            <div class="space-y-8 text-center">
                <label class="block text-[10px] font-black uppercase text-gray-400 mb-3 tracking-widest text-left">
                    Banner Aplikasi (Preview)
                </label>
                
                <!-- Kotak Preview Gambar -->
                <div class="relative group h-72 w-full bg-gray-100 rounded-[2.5rem] overflow-hidden border-2 border-dashed border-gray-200 transition-all hover:border-[#990000]/30">
                    <img id="preview" 
                         src="{{ $class->image_url ?? 'https://via.placeholder.com/800x450?text=Spekta+Academy+Banner' }}" 
                         class="w-full h-full object-cover">
                    
                    <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                         <span class="bg-white/90 text-gray-900 px-4 py-2 rounded-full text-[10px] font-black uppercase shadow-lg">Tampilan Saat Ini</span>
                    </div>
                </div>

                <!-- Tombol Upload -->
                <div>
                    <input type="file" name="banner_image" id="banner_input" class="hidden" accept="image/*" onchange="previewImage()">
                    <label for="banner_input" 
                           class="cursor-pointer inline-flex items-center gap-4 bg-gray-900 text-white px-10 py-5 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-[#990000] shadow-lg shadow-gray-200 hover:shadow-red-100 transition-all transform hover:-translate-y-1">
                        <i class="fas fa-cloud-upload-alt text-lg"></i> UPLOAD BANNER BARU
                    </label>
                    <p class="text-[9px] text-gray-400 mt-5 italic">
                        *Format: JPG, PNG, WEBP. Maksimal 2MB.<br>
                        Rasio ideal 16:9 untuk tampilan Mobile terbaik.
                    </p>
                    @error('banner_image') <p class="text-red-500 text-[10px] mt-2 font-bold">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="mt-12 pt-8 border-t border-gray-50 flex justify-end items-center gap-6">
            <span class="text-[10px] text-gray-400 font-medium italic">Data akan langsung tersinkronisasi ke database Midtrans & Flutter.</span>
            <button type="submit" 
                    class="bg-[#990000] text-white px-16 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-red-100 hover:bg-red-700 hover:scale-105 active:scale-95 transition-all">
                SIMPAN & AKTIFKAN PERUBAHAN
            </button>
        </div>
    </form>
</div>

<!-- Script Preview Gambar Instan -->
<script>
    function previewImage() {
        const input = document.getElementById('banner_input');
        const preview = document.getElementById('preview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection