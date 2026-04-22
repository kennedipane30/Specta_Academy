@extends('layouts.spekta')
@section('content')
<div class="p-10 w-full max-w-5xl">
    <div class="mb-10">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight uppercase italic">TAMBAH <span class="text-[#990000]">PROGRAM BARU</span></h1>
        <p class="text-gray-500 font-medium">Data ini akan otomatis muncul di aplikasi mobile siswa.</p>
    </div>

    <!-- TAMPILAN ERROR VALIDASI -->
    @if ($errors->any())
        <div class="mb-8 p-5 bg-red-50 border-l-4 border-red-500 rounded-2xl animate__animated animate__shakeX">
            <div class="flex items-center gap-3 mb-2">
                <i class="fas fa-exclamation-triangle text-red-500"></i>
                <p class="text-xs font-black text-red-700 uppercase tracking-widest">Terjadi Kesalahan Input:</p>
            </div>
            <ul class="list-disc ml-8">
                @foreach ($errors->all() as $error)
                    <li class="text-red-600 text-[10px] font-bold uppercase">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.classes.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-12 rounded-[3rem] shadow-xl border border-gray-100">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- SISI KIRI -->
            <div class="space-y-8">
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-3 tracking-widest">Nama Program</label>
                    <input type="text" name="program_name" value="{{ old('program_name') }}" class="w-full bg-gray-50 border-none rounded-3xl p-5 font-bold focus:ring-2 focus:ring-[#990000]" placeholder="Contool: PTN & UNHAN" required>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-3 tracking-widest">Harga Investasi (IDR)</label>
                    <div class="relative">
                        <span class="absolute left-5 top-5 font-black text-gray-400">Rp</span>
                        <input type="number" name="price" value="{{ old('price') }}" class="w-full bg-gray-50 border-none rounded-3xl p-5 pl-14 font-black text-2xl focus:ring-2 focus:ring-[#990000]" placeholder="900000" required>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-3 tracking-widest">Deskripsi Aplikasi</label>
                    <textarea name="description" rows="6" class="w-full bg-gray-50 border-none rounded-3xl p-6 focus:ring-2 focus:ring-[#990000] text-sm leading-relaxed text-gray-600" placeholder="Tuliskan deskripsi lengkap program..." required>{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- SISI KANAN -->
            <div class="space-y-8 text-center">
                <label class="block text-[10px] font-black uppercase text-gray-400 mb-3 tracking-widest text-left">Visual Banner</label>
                
                <div class="relative group h-64 w-full bg-gray-100 rounded-[2.5rem] overflow-hidden border-2 border-dashed border-gray-200">
                    <img id="preview" src="https://via.placeholder.com/800x450?text=Preview+Tampilan+Mobile" class="w-full h-full object-cover">
                </div>

                <div>
                    <input type="file" name="banner_image" id="banner_input" class="hidden" onchange="previewImage()" required>
                    <label for="banner_input" class="cursor-pointer inline-flex items-center gap-4 bg-gray-900 text-white px-10 py-5 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-[#990000] transition-all">
                        <i class="fas fa-cloud-upload-alt text-lg"></i> PILIH GAMBAR BANNER
                    </label>
                    <p class="text-[9px] text-gray-400 mt-4 italic">*Wajib Upload Gambar. Maksimal 2MB.</p>
                </div>
            </div>
        </div>

        <div class="mt-12 pt-8 border-t flex justify-end gap-4">
            <a href="{{ route('admin.classes.index') }}" class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Batal</a>
            <button type="submit" class="bg-[#990000] text-white px-16 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-red-200 hover:scale-105 transition-all">
                PUBLIKASIKAN PROGRAM
            </button>
        </div>
    </form>
</div>

<script>
    function previewImage() {
        const input = document.getElementById('banner_input');
        const preview = document.getElementById('preview');
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) { preview.src = e.target.result; }
        if (file) { reader.readAsDataURL(file); }
    }
</script>
@endsection