@extends('layouts.spekta')
@section('title', 'Buat Paket Soal')

@section('content')
<div class="mb-10 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-black text-gray-800 uppercase tracking-tighter">Kirim Soal Tryout</h2>
        <p class="text-sm font-bold text-[#990000] uppercase tracking-widest">{{ $subject_name }} • Soal: <span id="counter">1</span>/10</p>
    </div>
    <div id="min-status" class="bg-red-50 text-red-600 px-6 py-2 rounded-2xl font-black text-[10px] uppercase border border-red-100">
        Wajib minimal 5 soal
    </div>
</div>

{{-- Alert Error --}}
@if(session('error'))
    <div class="bg-red-600 text-white p-4 rounded-2xl mb-6 text-sm font-bold shadow-lg">⚠️ {{ session('error') }}</div>
@endif

<form action="{{ route('pengajar.tryout.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="class_id" value="{{ $class_id }}">
    <input type="hidden" name="subject_name" value="{{ $subject_name }}">

    <div id="questions-container">
        @for($i = 0; $i < 10; $i++)
        <div class="question-step {{ $i > 0 ? 'hidden' : '' }}" id="step-{{ $i }}">
            <div class="bg-white p-10 rounded-[40px] shadow-sm border border-gray-100 relative mb-8">
                <div class="absolute -left-4 top-10 bg-[#990000] text-white w-12 h-12 rounded-2xl flex items-center justify-center font-black shadow-lg">{{ $i + 1 }}</div>

                <div class="space-y-10">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block">Isi Pertanyaan (Teks)</label>
                            <textarea name="soal[{{ $i }}][question]" rows="4" class="w-full bg-gray-50 border-none rounded-[30px] p-6 text-sm font-bold focus:ring-2 focus:ring-[#990000]"></textarea>
                        </div>
                        <div class="bg-gray-50 p-8 rounded-[30px] border-2 border-dashed flex flex-col justify-center items-center text-center">
                            <label class="text-[10px] font-black text-gray-400 uppercase mb-3">Atau Gambar Soal</label>
                            <input type="file" name="soal[{{ $i }}][q_img]" class="text-[10px] text-gray-400">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach(['a','b','c','d'] as $opt)
                        <div class="p-6 bg-white border border-gray-100 rounded-[35px] shadow-sm">
                            <label class="text-[10px] font-black text-[#990000] uppercase mb-4 block">Pilihan {{ strtoupper($opt) }}</label>
                            <input type="text" name="soal[{{ $i }}][option_{{ $opt }}]" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-xs font-bold mb-4" placeholder="Teks jawaban...">
                            <input type="file" name="soal[{{ $i }}][{{ $opt }}_img]" class="text-[9px] text-gray-400">
                        </div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block">Kunci</label>
                            <select name="soal[{{ $i }}][correct_answer]" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold">
                                <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block">Pembahasan (Huruf & Angka)</label>
                            <textarea name="soal[{{ $i }}][explanation]" rows="2" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endfor
    </div>

    <div class="flex gap-4 mb-20">
        <button type="button" id="btn-tambah" onclick="nextQ()" class="flex-1 bg-white border-2 border-[#990000] text-[#990000] py-4 rounded-[25px] font-black text-xs uppercase transition">➕ Tambah Soal</button>
        <button type="submit" id="btn-submit" disabled class="flex-1 bg-gray-300 text-white py-4 rounded-[25px] font-black text-xs uppercase cursor-not-allowed transition">🚀 Terbitkan Paket Soal</button>
    </div>
</form>

<script>
    let current = 0;
    function nextQ() {
        if (current < 9) {
            current++;
            document.getElementById(`step-${current}`).classList.remove('hidden');
            document.getElementById('counter').innerText = current + 1;
            if (current + 1 >= 5) {
                const btn = document.getElementById('btn-submit');
                btn.disabled = false;
                btn.classList.replace('bg-gray-300', 'bg-green-600');
                btn.classList.remove('cursor-not-allowed');
                document.getElementById('min-status').classList.replace('text-red-600', 'text-green-600');
                document.getElementById('min-status').innerText = "Syarat Terpenuhi";
            }
            if (current === 9) document.getElementById('btn-tambah').classList.add('hidden');
            window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        }
    }
</script>
@endsection
