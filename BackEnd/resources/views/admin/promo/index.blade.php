@extends('layouts.spekta')
@section('title', 'Manajemen Promo Spekta')

@section('content')
<div class="animate__animated animate__fadeIn">
    <!-- HEADER SECTION -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight">MANAJEMEN <span class="text-spekta">PROMO</span></h1>
            <p class="text-gray-500 text-sm">Kelola strategi diskon dan banner promosi aplikasi mobile.</p>
        </div>
        @if(session('success'))
            <div class="bg-green-500 text-white px-6 py-2 rounded-2xl shadow-lg flex items-center gap-2 animate__animated animate__bounceIn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="font-bold text-xs uppercase">Berhasil Diterbitkan!</span>
            </div>
        @endif
    </div>

    <!-- FORM INPUT PROMO (MODERN DESIGN) -->
    <div class="bg-white rounded-[2rem] shadow-2xl shadow-red-100 overflow-hidden border border-gray-100 mb-12">
        <div class="bg-gradient-to-r from-[#990000] to-red-600 px-8 py-4">
            <h3 class="text-white font-bold text-sm uppercase tracking-widest flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Buat Campaign Baru
            </h3>
        </div>

        <form action="{{ route('admin.promo.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

                <!-- SISI KIRI: MEDIA & TARGET -->
                <div class="space-y-6">
                    <div class="group">
                        <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-1">1. Visual Banner</label>
                        <div class="relative border-2 border-dashed border-gray-200 rounded-2xl p-4 group-hover:border-red-400 transition-colors bg-gray-50">
                            <input type="file" name="image_banner" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-spekta hover:file:bg-red-100 cursor-pointer" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-1">2. Target Program</label>
                        <div class="relative">
                            <select name="class_id" class="w-full bg-gray-50 border-2 border-gray-100 p-4 rounded-2xl text-sm focus:border-red-500 outline-none appearance-none transition shadow-sm" required>
                                <option value="">Pilih Program Kelas...</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->class_modelsID }}">{{ $c->nama_program }}</option>
                                @endforeach
                            </select>
                            <div class="absolute right-4 top-4 pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SISI KANAN: LOGIKA DISKON -->
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-1">3. Kode Rahasia</label>
                            <input type="text" name="code" placeholder="Cth: SPEKTA77" class="w-full bg-gray-50 border-2 border-gray-100 p-4 rounded-2xl text-sm font-mono font-bold tracking-widest focus:border-red-500 outline-none transition shadow-sm" required>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-1">4. Persentase (%)</label>
                            <div class="relative">
                                <input type="number" name="discount_percent" placeholder="25" class="w-full bg-gray-50 border-2 border-gray-100 p-4 rounded-2xl text-sm focus:border-red-500 outline-none transition shadow-sm" required>
                                <span class="absolute right-4 top-4 font-bold text-gray-400">%</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-1">5. Masa Berlaku</label>
                        <div class="grid grid-cols-2 gap-4 bg-gray-50 p-2 rounded-2xl border-2 border-gray-100 shadow-sm">
                            <input type="date" name="start_date" class="bg-transparent p-2 text-xs font-bold outline-none" required>
                            <input type="date" name="end_date" class="bg-transparent p-2 text-xs font-bold outline-none border-l-2 border-gray-200" required>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full mt-10 bg-[#990000] text-white py-5 rounded-2xl font-black shadow-xl shadow-red-200 hover:bg-red-800 hover:scale-[1.01] transition duration-300 flex items-center justify-center gap-3 uppercase tracking-widest">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Aktifkan Promo ke Aplikasi Mobile
            </button>
        </form>
    </div>

    <!-- LIST SECTION -->
    <div class="flex items-center gap-4 mb-8">
        <h4 class="font-black text-gray-800 uppercase tracking-tighter text-xl">Daftar Promo Aktif</h4>
        <div class="flex-1 h-[2px] bg-gray-100"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @forelse($promos as $row)
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-xl hover:shadow-2xl transition-all duration-500 group relative">
            <!-- Banner Image -->
            <div class="h-44 overflow-hidden rounded-t-[2rem] relative">
                <img src="{{ url('/view-galeri/' . basename($row->image_banner)) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-4 left-6 text-white">
                    <span class="bg-red-600 text-[10px] font-bold px-3 py-1 rounded-full uppercase">{{ $row->classModel->nama_program ?? 'Umum' }}</span>
                </div>
            </div>

            <!-- Content Card -->
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Voucher Code</p>
                        <div class="bg-gray-50 border-2 border-dashed border-red-200 px-4 py-2 rounded-xl">
                            <span class="font-mono font-black text-spekta text-lg tracking-tighter">{{ $row->code }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Discount</p>
                        <h2 class="text-3xl font-black text-gray-800">{{ $row->discount_percent }}<span class="text-spekta">%</span></h2>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-gray-400 mb-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="text-[11px] font-bold uppercase">{{ \Carbon\Carbon::parse($row->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($row->end_date)->format('d M Y') }}</span>
                </div>

                <form action="{{ route('admin.promo.destroy', $row->promotionsID) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full bg-gray-50 text-red-500 py-3 rounded-xl text-xs font-black uppercase hover:bg-red-500 hover:text-white transition duration-300 border border-red-50" onclick="return confirm('Hapus promo ini?')">
                        Hapus Campaign
                    </button>
                </form>
            </div>
        </div>
        @empty
            <div class="md:col-span-3 text-center py-24 bg-gray-50 rounded-[3rem] border-4 border-dashed border-white">
                <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                    <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </div>
                <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">Belum Ada Campaign Promo</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
