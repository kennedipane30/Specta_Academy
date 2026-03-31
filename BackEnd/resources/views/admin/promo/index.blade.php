@extends('layouts.spekta')
@section('title', 'Manajemen Promo')

@section('content')
<div class="bg-white p-8 rounded-2xl shadow-md border-t-8 border-[#990000]">
    <h3 class="text-xl font-bold text-spekta mb-6 uppercase">Terbitkan Promo Baru</h3>

    <!-- FORM INPUT PROMO -->
    <form action="{{ route('admin.promo.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl mb-10">
        @csrf
        <div>
            <label class="block text-sm font-bold mb-1 text-gray-600">Pilih Banner Promo</label>
            <input type="file" name="image_banner" class="w-full border p-2 rounded-lg bg-white" required>
        </div>
        <div>
            <label class="block text-sm font-bold mb-1 text-gray-600">Kode Rahasia (Hanya Admin Tahu)</label>
            <input type="text" name="code" placeholder="Misal: SPEKTA77" class="w-full border p-2 rounded-lg" required>
        </div>
        <div>
            <label class="block text-sm font-bold mb-1 text-gray-600">Nilai Diskon (%)</label>
            <input type="number" name="discount_percent" placeholder="Contoh: 25" class="w-full border p-2 rounded-lg" required>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-xs font-bold text-gray-500">Tgl Mulai</label>
                <input type="date" name="start_date" class="w-full border p-2 rounded-lg" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500">Tgl Selesai</label>
                <input type="date" name="end_date" class="w-full border p-2 rounded-lg" required>
            </div>
        </div>
        <button type="submit" class="bg-[#990000] text-white py-3 rounded-xl font-bold md:col-span-2 shadow-lg hover:bg-red-800 transition">
            Daftarkan Promo Ke Sistem
        </button>
    </form>

    <hr class="mb-8">

    <!-- TABEL REKAP PROMO -->
    <h4 class="font-bold text-gray-700 mb-4">Daftar Promo Aktif</h4>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($promos as $row)
        <div class="border rounded-2xl overflow-hidden shadow-sm relative bg-white">
            <img src="{{ asset('storage/' . $row->image_banner) }}" class="h-32 w-full object-cover">
            <div class="p-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-[10px] font-bold">{{ $row->code }}</span>
                    <span class="text-lg font-black text-spekta">{{ $row->discount_percent }}% OFF</span>
                </div>
                <p class="text-[10px] text-gray-400 italic">Berlaku: {{ $row->start_date }} s/d {{ $row->end_date }}</p>

                <form action="{{ route('admin.promo.destroy', $row->promotionsID) }}" method="POST" class="mt-4">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 font-bold text-[10px] uppercase hover:underline" onclick="return confirm('Hapus promo ini?')">Hapus Promo</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
