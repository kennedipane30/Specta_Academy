@extends('layouts.spekta')
@section('title', 'Buat Paket Soal Tryout')

@section('content')
<div class="mb-10 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-black text-gray-800 uppercase tracking-tighter">Kirim Soal Tryout</h2>
        <p class="text-sm font-bold text-[#990000] uppercase tracking-widest">
            {{ $subject_name }} • Progress Input: <span id="counter">1</span>/10
        </p>
    </div>
    <div id="min-status" class="bg-red-50 text-red-600 px-6 py-2 rounded-2xl font-black text-[10px] uppercase border border-red-100 shadow-sm transition-all duration-500">
        Wajib minimal 5 soal
    </div>
</div>

{{-- Feedback Notifikasi --}}
@if(session('success'))
    <div class="bg-green-600 text-white p-5 rounded-[25px] mb-8 shadow-xl flex items-center gap-3 animate-bounce">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span class="font-bold">{{ session('success') }}</span>
    </div>
@endif

@if(session('error') || $errors->any())
    <div class="bg-red-600 text-white p-6 rounded-[25px] mb-8 shadow-xl">
        <p class="font-black uppercase text-xs mb-2">Terjadi Kesalahan:</p>
        <ul class="list-disc ml-5 text-sm font-medium">
            @if(session('error')) <li>{{ session('error') }}</li> @endif
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif

{{-- FORM INPUT MULTI-SOAL --}}
<form action="{{ route('pengajar.tryout.store') }}" method="POST" enctype="multipart/form-data" id="tryoutForm">
    @csrf
    <input type="hidden" name="class_id" value="{{ $class_id }}">
    <input type="hidden" name="subject_name" value="{{ $subject_name }}">

    <div id="questions-container">
        @for($i = 0; $i < 10; $i++)
        <div class="question-step {{ $i > 0 ? 'hidden' : '' }}" id="step-{{ $i }}">
            <div class="bg-white p-10 rounded-[40px] shadow-sm border border-gray-100 relative mb-8">
                {{-- Badge Nomor --}}
                <div class="absolute -left-4 top-10 bg-[#990000] text-white w-12 h-12 rounded-2xl flex items-center justify-center font-black shadow-lg">
                    {{ $i + 1 }}
                </div>

                <div class="space-y-10">
                    {{-- AREA SOAL (HYBRID) --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block ml-1">Teks Pertanyaan</label>
                            <textarea name="soal[{{ $i }}][question]" id="q_text_{{ $i }}" rows="4" class="w-full bg-gray-50 border-none rounded-[30px] p-6 text-sm font-bold focus:ring-2 focus:ring-[#990000]" placeholder="Ketik soal di sini..."></textarea>
                        </div>
                        <div class="bg-gray-50 p-8 rounded-[30px] border-2 border-dashed border-gray-200 flex flex-col justify-center items-center text-center">
                            <label class="text-[10px] font-black text-gray-400 uppercase mb-3">Gambar Soal (Opsional)</label>
                            <input type="file" name="soal[{{ $i }}][q_img]" class="text-[10px] text-gray-400">
                        </div>
                    </div>

                    {{-- AREA OPSI A-D (HYBRID) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach(['a','b','c','d'] as $opt)
                        <div class="p-6 bg-white border border-gray-100 rounded-[35px] shadow-sm hover:border-[#990000] transition duration-300">
                            <label class="text-[10px] font-black text-[#990000] uppercase mb-4 block">Pilihan {{ strtoupper($opt) }}</label>
                            <input type="text" name="soal[{{ $i }}][option_{{ $opt }}]" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-xs font-bold mb-4" placeholder="Teks jawaban...">
                            <div class="flex items-center gap-3">
                                <span class="text-[9px] font-black text-gray-400 uppercase">Gambar:</span>
                                <input type="file" name="soal[{{ $i }}][{{ $opt }}_img]" class="text-[9px] text-gray-400">
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- KUNCI & PEMBAHASAN --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block ml-1">Kunci Jawaban</label>
                            <select name="soal[{{ $i }}][correct_answer]" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold">
                                @foreach(['A','B','C','D'] as $k) <option value="{{ $k }}">{{ $k }}</option> @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block ml-1">Pembahasan (Hanya Huruf & Angka)</label>
                            <textarea name="soal[{{ $i }}][explanation]" id="exp_{{ $i }}" rows="2" class="w-full bg-gray-50 border-none rounded-[20px] p-5 text-sm font-bold" placeholder="Contoh: Jawaban benar karena..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endfor
    </div>

    {{-- TOMBOL NAVIGASI --}}
    <div class="flex gap-4 mb-20">
        <button type="button" id="btn-tambah" onclick="nextQ()" class="flex-1 bg-white border-2 border-[#990000] text-[#990000] py-4 rounded-[25px] font-black text-xs uppercase tracking-widest hover:bg-red-50 transition shadow-sm">
            ➕ Tambah Soal Berikutnya
        </button>
        <button type="submit" id="btn-submit" disabled class="flex-1 bg-gray-300 text-white py-4 rounded-[25px] font-black text-xs uppercase tracking-widest cursor-not-allowed transition shadow-xl">
            🚀 Terbitkan Paket Soal
        </button>
    </div>
</form>

{{-- RIWAYAT SOAL --}}
<div class="mt-20 space-y-8 pb-32">
    <div class="flex items-center justify-between border-b border-gray-200 pb-6">
        <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">Riwayat Soal Terkirim</h3>
        <span class="bg-[#990000] text-white px-5 py-2 rounded-full text-[10px] font-black uppercase shadow-lg shadow-red-100">
            Total: {{ $existingSoal->count() }} Soal
        </span>
    </div>

    <div class="grid grid-cols-1 gap-8">
        @forelse($existingSoal as $index => $s)
        <div class="bg-white p-10 rounded-[45px] shadow-sm border border-gray-100 flex flex-col md:flex-row gap-10 hover:shadow-md transition">
            <div class="flex-shrink-0">
                <div class="w-14 h-14 bg-gray-50 text-gray-400 rounded-2xl flex items-center justify-center font-black text-xl">
                    {{ $existingSoal->count() - $index }}
                </div>
            </div>

            <div class="flex-1 space-y-6">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase mb-2">Pertanyaan:</p>
                    <p class="text-sm font-bold text-gray-800 leading-relaxed">{{ $s->question ?? '(Hanya gambar)' }}</p>
                    @if($s->question_image)
                        <img src="{{ asset('storage/tryout/images/' . $s->question_image) }}" class="mt-4 rounded-3xl max-h-56 border border-gray-100 shadow-sm">
                    @endif
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach(['a','b','c','d'] as $o)
                    @php $t = "option_$o"; $img = "option_{$o}_image"; @endphp
                    <div class="bg-gray-50 p-4 rounded-[25px] border border-gray-100">
                        <p class="text-[9px] font-black text-[#990000] uppercase mb-1">Opsi {{ strtoupper($o) }}</p>
                        <p class="text-[11px] font-bold text-gray-600">{{ $s->$t ?? '-' }}</p>
                        @if($s->$img)
                            <img src="{{ asset('storage/tryout/images/' . $s->$img) }}" class="mt-2 rounded-xl w-full h-16 object-cover border border-white shadow-sm">
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="w-full md:w-60 flex flex-col justify-between border-l border-gray-100 md:pl-10">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase mb-2">Kunci Jawaban:</p>
                    <span class="inline-block bg-green-100 text-green-600 px-6 py-2 rounded-2xl font-black text-2xl shadow-sm">
                        {{ $s->correct_answer }}
                    </span>
                </div>
                <div class="mt-8">
                    <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Status:</p>
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 bg-blue-500 rounded-full animate-pulse"></div>
                        <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest italic">Menunggu Review Admin</span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="p-20 text-center bg-gray-50 rounded-[50px] border-2 border-dashed border-gray-200">
            <p class="text-gray-400 font-black uppercase text-xs tracking-widest italic">Belum ada riwayat soal untuk subjek ini.</p>
        </div>
        @endforelse
    </div>
</div>
<script>
    let current = 0;

    function nextQ() {
        // 1. Ambil elemen input pada step yang sedang aktif
        const currentStep = document.getElementById(`step-${current}`);
        const questionText = currentStep.querySelector(`textarea[name="soal[${current}][question]"]`);
        const explanationText = currentStep.querySelector(`textarea[name="soal[${current}][explanation]"]`);
        const qImg = currentStep.querySelector(`input[name="soal[${current}][q_img]"]`);

        // 2. Validasi: Harus ada (Teks Soal ATAU Gambar) DAN wajib ada Pembahasan
        if ((questionText.value.trim() === "" && qImg.files.length === 0)) {
            alert("⚠️ Pertanyaan nomor " + (current + 1) + " (Teks atau Gambar) wajib diisi!");
            return;
        }

        if (explanationText.value.trim() === "") {
            alert("⚠️ Pembahasan untuk soal nomor " + (current + 1) + " wajib diisi!");
            explanationText.focus();
            return;
        }

        // 3. Jika valid, lanjut ke soal berikutnya
        if (current < 9) {
            current++;
            document.getElementById(`step-${current}`).classList.remove('hidden');
            document.getElementById('counter').innerText = current + 1;

            // Logika Aktifkan Tombol Submit jika sudah input minimal 5 soal
            if (current + 1 >= 5) {
                const btn = document.getElementById('btn-submit');
                btn.disabled = false;
                btn.classList.replace('bg-gray-300', 'bg-green-600');
                btn.classList.remove('cursor-not-allowed');

                const statusLabel = document.getElementById('min-status');
                statusLabel.classList.replace('text-red-600', 'text-green-600');
                statusLabel.innerText = "Syarat 5 Soal Terpenuhi ✅";
            }

            if (current === 9) document.getElementById('btn-tambah').classList.add('hidden');

            // Scroll otomatis ke nomor baru
            window.scrollTo({
                top: document.getElementById(`step-${current}`).offsetTop - 100,
                behavior: 'smooth'
            });
        }
    }
</script>
@endsection
