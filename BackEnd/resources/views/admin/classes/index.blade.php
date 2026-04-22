@extends('layouts.spekta')
@section('title', 'Manajemen Program - Spekta Academy')

@section('content')
<div class="p-10 w-full min-h-screen bg-[#FAFAFA]">

    <!-- HEADER SECTION -->
    <div class="flex flex-col lg:flex-row justify-between items-end mb-16 gap-8 animate__animated animate__fadeIn">
        <div class="max-w-2xl">
            <div class="flex items-center gap-2 mb-4">
                <span class="h-[2px] w-12 bg-[#990000]"></span>
                <span class="text-[10px] font-black tracking-[0.5em] text-[#990000] uppercase">Spekta Control Center</span>
            </div>
            <h1 class="text-5xl font-black text-gray-900 leading-none uppercase tracking-tighter italic">
                KATALOG <span class="text-[#990000]">PROGRAM</span>
            </h1>
            <p class="text-gray-400 mt-4 font-medium text-sm leading-relaxed">
                Manajemen pusat untuk konten aplikasi mobile. Perubahan pada harga, deskripsi, dan visual akan disinkronkan secara real-time ke seluruh perangkat siswa.
            </p>
        </div>

        <div class="flex items-center gap-4">
            <!-- TOMBOL TAMBAH PROGRAM -->
            <a href="{{ route('admin.classes.create') }}" class="bg-gray-900 text-white px-10 py-6 rounded-3xl shadow-2xl hover:bg-[#990000] transition-all duration-500 flex items-center gap-4 group">
                <div class="bg-white/10 p-2 rounded-lg group-hover:rotate-90 transition-transform">
                    <i class="fas fa-plus text-xs"></i>
                </div>
                <span class="text-[11px] font-black uppercase tracking-[0.2em]">Tambah Program Baru</span>
            </a>
            
            <a href="{{ route('admin.dashboard') }}" class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all text-gray-400 hover:text-[#990000]">
                <i class="fas fa-th-large"></i>
            </a>
        </div>
    </div>

    <!-- NOTIFIKASI SUKSES -->
    @if(session('success'))
    <div class="mb-10 p-6 bg-white border-l-4 border-green-500 rounded-[2rem] shadow-sm animate__animated animate__fadeInRight">
        <div class="flex items-center gap-4">
            <div class="bg-green-100 p-3 rounded-xl">
                <i class="fas fa-check text-green-600"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">System Notification</p>
                <p class="text-xs font-bold text-gray-800 uppercase">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- CARDS GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        @foreach($classes as $item)
        <div class="bg-white rounded-[4rem] p-4 shadow-sm border border-gray-50 hover:shadow-2xl transition-all duration-700 group animate__animated animate__fadeInUp">
            <div class="flex flex-col md:flex-row gap-8">
                
                <!-- SISI VISUAL (GAMBAR) -->
                <div class="w-full md:w-2/5 h-72 md:h-auto relative overflow-hidden rounded-[3rem]">
                    <img src="{{ $item->image_url ?? 'https://via.placeholder.com/800x1000?text=No+Image' }}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                    
                    <!-- TAG HARGA MELAYANG -->
                    <div class="absolute top-6 left-6">
                        <div class="bg-white/90 backdrop-blur-md px-5 py-3 rounded-2xl shadow-xl">
                            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1 text-center">Investasi</p>
                            <p class="text-sm font-black text-gray-900">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- BADGE STATUS -->
                    <div class="absolute bottom-6 left-6">
                        <span class="bg-[#990000] text-white text-[8px] font-black px-4 py-2 rounded-full uppercase tracking-[0.2em] shadow-lg">
                            <i class="fas fa-signal mr-2"></i> Live on App
                        </span>
                    </div>
                </div>

                <!-- SISI INFORMASI -->
                <div class="w-full md:w-3/5 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <h3 class="text-2xl font-black text-gray-800 uppercase italic leading-tight">{{ $item->program_name }}</h3>
                            <span class="text-[10px] font-bold text-gray-200">ID:{{ $item->class_id }}</span>
                        </div>

                        <div class="space-y-4 mb-8">
                            <div class="flex items-center gap-3">
                                <div class="w-1 h-4 bg-[#990000] rounded-full"></div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Informasi Program</p>
                            </div>
                            <p class="text-gray-500 text-xs leading-relaxed line-clamp-4 italic">
                                {{ $item->description ?? 'Deskripsi program belum dikonfigurasi. Harap lengkapi detail untuk menarik minat siswa mendaftar.' }}
                            </p>
                        </div>
                    </div>

                    <!-- TOMBOL AKSI: EDIT & DELETE -->
                    <div class="flex items-center gap-3 mt-6">
                        <!-- EDIT -->
                        <a href="{{ route('admin.classes.edit', $item->class_id) }}" 
                           class="flex-1 bg-gray-50 hover:bg-gray-900 group/btn text-gray-900 hover:text-white py-6 rounded-[2rem] font-black text-[10px] uppercase tracking-[0.3em] transition-all flex items-center justify-center gap-4">
                            KONFIGURASI <i class="fas fa-edit text-[8px] group-hover/btn:rotate-12 transition-transform"></i>
                        </a>
                        
                        <!-- DELETE (DENGAN KONFIRMASI) -->
                        <form action="{{ route('admin.classes.destroy', $item->class_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus program ini? Data yang terhapus tidak dapat dikembalikan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-50 hover:bg-[#990000] p-6 rounded-[1.5rem] text-[#990000] hover:text-white transition-all shadow-sm group/del">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        @endforeach
    </div>

    <!-- STATUS FOOTER -->
    <div class="mt-24 border-t border-gray-100 pt-12 flex flex-col md:flex-row justify-between items-center gap-6 opacity-60">
        <div class="flex items-center gap-4">
            <img src="https://ui-avatars.com/api/?name=Admin+Spekta&background=990000&color=fff" class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
            <div>
                <p class="text-[10px] font-black text-gray-900 uppercase">Administrator Mode</p>
                <p class="text-[9px] text-gray-400 font-bold uppercase">Last Sync: {{ now()->format('H:i') }} Server Time</p>
            </div>
        </div>
        <p class="text-[9px] font-bold text-gray-300 uppercase tracking-[0.5em]">Spekta Academy Indonesia &copy; 2026</p>
    </div>

</div>

<style>
    /* Tipografi Inter sesuai standar Dashboard Admin Spekta */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;900&display=swap');
    body { font-family: 'Inter', sans-serif; overflow-x: hidden; }

    /* Custom Scrollbar minimalis */
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #990000; border-radius: 10px; }
    
    /* Animasi Hover Halus */
    .group:hover img {
        filter: brightness(1.1);
    }
</style>
@endsection