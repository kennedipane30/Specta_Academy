@extends('layouts.spekta')
@section('title', 'Promo Management')

@section('content')
<div class="p-6 space-y-10 animate__animated animate__fadeIn">

    <!-- NOTIFIKASI -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mb-4">
        <p class="font-bold">Berhasil!</p>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm mb-4">
        <p class="font-bold">Ups! Ada kesalahan:</p>
        <ul class="list-disc ml-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight uppercase">MANAJEMEN <span class="text-[#990000]">PROMO</span></h1>
            <p class="text-gray-500 text-sm">Kelola strategi diskon untuk setiap kelas spekta.</p>
        </div>
    </div>

    <!-- FORM INPUT PROMO -->
    <div class="bg-white rounded-[2rem] shadow-sm overflow-hidden border border-gray-100 mb-12">
        <div class="bg-[#990000] px-8 py-4 flex items-center gap-2">
            <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
            <h3 class="text-white font-bold text-xs uppercase tracking-widest">Buat Kode Promo Baru</h3>
        </div>

        <form action="{{ route('admin.promo.store') }}" method="POST" class="p-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Kode Promo</label>
                    <input type="text" name="code" placeholder="SPEKTA50" class="w-full bg-gray-50 border-none p-4 rounded-2xl text-sm font-black tracking-widest focus:ring-2 focus:ring-red-500" required>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Target Kelas</label>
                    <select name="class_id" class="w-full bg-gray-50 border-none p-4 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-red-500" required>
                        <option value="">🎓 Pilih Kelas...</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->class_id }}">{{ $c->program_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Besar Diskon (Klik %/Rp)</label>
                    <div class="flex bg-gray-50 rounded-2xl overflow-hidden border border-transparent focus-within:ring-2 focus-within:ring-red-500">
                        <input type="number" name="discount_value" placeholder="Nilai" class="w-full bg-transparent border-none p-4 text-sm font-bold focus:ring-0" required>
                        <div id="btn-toggle-type" onclick="toggleDiscountType()" class="bg-gray-200 px-5 flex items-center justify-center font-black text-[#990000] cursor-pointer hover:bg-gray-300 transition min-w-[60px]">
                            <span id="display-type">%</span>
                        </div>
                    </div>
                    <input type="hidden" name="discount_type" id="input_discount_type" value="percent">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Kuota</label>
                    <input type="number" name="quota" placeholder="100" class="w-full bg-gray-50 border-none p-4 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-red-500" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Tgl Mulai</label>
                    <input type="date" name="start_date" class="w-full bg-gray-50 border-none p-4 rounded-2xl text-sm font-bold" required>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Tgl Berakhir</label>
                    <input type="date" name="end_date" class="w-full bg-gray-50 border-none p-4 rounded-2xl text-sm font-bold" required>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="w-full bg-[#990000] text-white py-4 rounded-2xl font-black uppercase text-xs tracking-widest shadow-lg hover:bg-red-800 transition transform active:scale-95 flex items-center justify-center gap-3">
                        🚀 TERBITKAN KODE PROMO
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- DAFTAR PROMO -->
    <div>
        <h3 class="text-sm font-bold text-gray-400 uppercase mb-6 tracking-widest ml-2">Promo Berjalan</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($promos as $row)
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 flex flex-col justify-between group hover:border-red-200 transition">
                <div class="flex justify-between items-start mb-4">
                    <div class="bg-red-50 px-3 py-1 rounded-full font-black text-[#990000] text-[10px] uppercase">
                        {{ $row->code }}
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-black text-gray-800">
                            {{ $row->discount_type == 'fixed' ? 'Rp '.number_format($row->discount_percent,0,',','.') : (int)$row->discount_percent.'%' }}
                        </div>
                        <div class="text-[9px] font-bold text-gray-400 uppercase">Potongan</div>
                    </div>
                </div>

                <div class="space-y-2 mb-6 border-t border-gray-50 pt-4 text-[11px]">
                    <div class="flex justify-between">
                        <span class="text-gray-400 font-bold uppercase">Program:</span>
                        <span class="text-gray-800 font-black">{{ $row->class->program_name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400 font-bold uppercase">Kuota:</span>
                        <span class="text-gray-800 font-black">{{ $row->quota }}</span>
                    </div>
                </div>

                <form action="{{ route('admin.promo.destroy', $row->promotion_id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('Hapus promo?')" class="w-full bg-gray-50 text-gray-400 py-3 rounded-xl text-[10px] font-black uppercase hover:bg-red-600 hover:text-white transition">
                        Hentikan
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    function toggleDiscountType() {
        const inputType = document.getElementById('input_discount_type');
        const displayType = document.getElementById('display-type');
        if (inputType.value === 'percent') {
            inputType.value = 'fixed';
            displayType.innerText = 'Rp';
        } else {
            inputType.value = 'percent';
            displayType.innerText = '%';
        }
    }
</script>
@endsection