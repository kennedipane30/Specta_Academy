@extends('layouts.spekta')
@section('title', 'Promo Management')

@section('content')
<div class="p-6 space-y-10 animate__animated animate__fadeIn">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight">MARKETING <span class="text-[#990000]">PROMO</span></h1>
            <p class="text-gray-500 text-sm">Manage discount strategies for mobile app.</p>
        </div>
    </div>

    <!-- FORM INPUT PROMO -->
    <div class="bg-white rounded-[2rem] shadow-sm overflow-hidden border border-gray-100 mb-12">
        <div class="bg-[#990000] px-8 py-4">
            <h3 class="text-white font-bold text-sm uppercase tracking-widest">Create New Campaign</h3>
        </div>

        {{-- PASTIKAN ACTION ADALAH admin.promo.store --}}
        <form action="{{ route('admin.promo.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">1. Visual Banner</label>
                        <input type="file" name="image_banner" class="w-full text-xs font-bold text-gray-500" required>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">2. Target Program</label>
                        <select name="class_id" class="w-full bg-gray-50 border-none p-4 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-red-500 transition" required>
                            <option value="">Choose Class...</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->class_id }}">{{ $c->program_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">3. Voucher Code</label>
                            <input type="text" name="code" placeholder="Ex: SPEKTA25" class="w-full bg-gray-50 border-none p-4 rounded-2xl text-sm font-black tracking-widest focus:ring-2 focus:ring-red-500" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">4. Percent (%)</label>
                            <input type="number" name="discount_percent" placeholder="25" class="w-full bg-gray-50 border-none p-4 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-red-500" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">5. Validity Date</label>
                        <div class="grid grid-cols-2 gap-4 bg-gray-50 p-2 rounded-2xl">
                            <input type="date" name="start_date" class="bg-transparent p-2 text-xs font-bold border-none" required>
                            <input type="date" name="end_date" class="bg-transparent p-2 text-xs font-bold border-none" required>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full mt-10 bg-[#990000] text-white py-5 rounded-2xl font-black uppercase text-xs tracking-[0.2em] shadow-lg hover:bg-red-800 transition transform active:scale-[0.98]">
                Activate Campaign Now
            </button>
        </form>
    </div>

    <!-- LIST SECTION -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach($promos as $row)
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden flex flex-col group">
            <img src="{{ url('/view-galeri/' . basename($row->image_banner)) }}" class="h-44 w-full object-cover">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Code</span>
                        <div class="font-black text-[#990000] text-lg">{{ $row->code }}</div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Disc</span>
                        <div class="font-black text-3xl">{{ $row->discount_percent }}%</div>
                    </div>
                </div>
                <p class="text-[10px] font-bold text-gray-400 mb-6 italic uppercase">Program: {{ $row->class->program_name }}</p>

                <form action="{{ route('admin.promo.destroy', $row->promotion_id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full bg-red-50 text-red-600 py-3 rounded-xl text-[10px] font-black uppercase hover:bg-red-600 hover:text-white transition" onclick="return confirm('Delete?')">Delete</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
