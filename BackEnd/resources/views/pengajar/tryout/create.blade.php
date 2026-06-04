@extends('layouts.spekta')

@section('title', 'Input Soal TO - ' . $subjectName)

@section('content')
<div class="cp-page">
    {{-- HEADER HERO --}}
    <section class="tm-hero-header">
        <div class="tm-hero-content">
            <div class="tm-hero-text">
                <span class="tm-pre-title">TRYOUT QUESTION BUILDER</span>
                <h1 class="tm-main-title">Input Soal: {{ $subjectName }}</h1>
                <p class="tm-sub-title">Program: {{ $classModel->program_name }} (ID Kelas: #{{ $classId }})</p>
            </div>
        </div>
        <a href="{{ route('pengajar.tryout.index') }}" class="cp-back-btn">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </section>

    {{-- ALERT NOTIFIKASI --}}
    @if(session('success'))
        <div class="tm-alert-modern success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="tm-alert-modern error" style="background: #fee2e2; color: #b91c1c; border-left: 5px solid #ef4444;">
            <i class="fa-solid fa-circle-xmark"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- 1. BOX IMPORT CSV --}}
    <section class="cp-main-card mb-4" style="border: 2px dashed #d90429; background: #fff1f2; margin-bottom: 25px;">
        <div class="card-header">
            <h2 style="color: #d90429;"><i class="fa-solid fa-file-csv"></i> Import Soal via CSV</h2>
            <p>Gunakan fitur ini untuk mengunggah soal dalam jumlah banyak sekaligus.</p>
        </div>

        <form action="{{ route('pengajar.tryout.import_csv') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="class_id" value="{{ $classId }}">
            <input type="hidden" name="subject_name" value="{{ $subjectName }}">

            <div style="display: flex; gap: 15px; align-items: center;">
                <div style="flex: 1;">
                    <input type="file" name="file_csv" class="tm-input" accept=".csv" required style="padding: 10px; border: 1px solid #d90429;">
                </div>
                <button type="submit" class="cp-primary-btn" style="background: #111827; height: 48px; border-radius: 12px; color: white; padding: 0 25px; border: none; cursor: pointer; font-weight: 800; transition: 0.3s;">
                    <i class="fa-solid fa-upload"></i> MULAI IMPORT
                </button>
            </div>
            <small style="display: block; margin-top: 10px; color: #64748b;">*Pastikan urutan kolom: Mata Pelajaran, Pertanyaan, Opsi A, B, C, D, E, Kunci, Pembahasan.</small>
        </form>
    </section>

    <div class="tm-grid-layout" style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 25px;">
        
        {{-- KOLOM KIRI: FORM INPUT SOAL (MANUAL) --}}
        <section class="cp-main-card">
            <div class="card-header">
                <h2 id="form-title">Tambah Soal Baru (Manual)</h2>
                <p id="form-subtitle">Isi detail pertanyaan dan pilihan jawaban di bawah ini.</p>
            </div>

            <form action="{{ route('pengajar.tryout.store') }}" method="POST" id="soalForm">
                @csrf
                {{-- ID Hidden untuk proses Update --}}
                <input type="hidden" name="draft_id" id="draft_id">
                <input type="hidden" name="class_id" value="{{ $classId }}">
                <input type="hidden" name="subject_name" value="{{ $subjectName }}">

                <div class="form-group mb-4">
                    <label class="tm-label">Pertanyaan</label>
                    <textarea name="question" id="question" rows="5" class="tm-input" placeholder="Tulis soal di sini..." required></textarea>
                </div>

                <div class="options-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    @foreach(['a','b','c','d','e'] as $opt)
                    <div class="form-group">
                        <label class="tm-label">Opsi {{ strtoupper($opt) }}</label>
                        <input type="text" name="option_{{ $opt }}" id="option_{{ $opt }}" class="tm-input" required>
                    </div>
                    @endforeach
                    
                    <div class="form-group">
                        <label class="tm-label">Kunci Jawaban</label>
                        <select name="correct_answer" id="correct_answer" class="tm-input" required>
                            <option value="A">Opsi A</option>
                            <option value="B">Opsi B</option>
                            <option value="C">Opsi C</option>
                            <option value="D">Opsi D</option>
                            <option value="E">Opsi E</option>
                        </select>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <label class="tm-label">Pembahasan (Opsional)</label>
                    <textarea name="explanation" id="explanation" rows="3" class="tm-input" placeholder="Jelaskan cara pengerjaannya..."></textarea>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" id="btn-submit" class="cp-primary-btn" style="flex: 2; background: #d90429; color: white; border: none; padding: 15px; border-radius: 12px; font-weight: 800; cursor: pointer; margin-top: 20px; transition: 0.3s;">
                        <i class="fa-solid fa-paper-plane"></i> <span id="btn-text">Kirim Soal ke Admin</span>
                    </button>
                    
                    <button type="button" id="btn-cancel" onclick="cancelEdit()" style="display: none; flex: 1; background: #64748b; color: white; border: none; padding: 15px; border-radius: 12px; font-weight: 800; cursor: pointer; margin-top: 20px;">
                        Batal
                    </button>
                </div>
            </form>
        </section>

        {{-- KOLOM KANAN: DAFTAR SOAL TERUPLOAD --}}
        <section class="cp-main-card">
            <div class="card-header">
                <h2>Soal Terkirim ({{ $existingSoal->count() }})</h2>
                <p>Berikut adalah draf soal yang sudah Anda buat.</p>
            </div>

            <div class="soal-list-scroll" style="max-height: 800px; overflow-y: auto; padding-right: 10px;">
                @forelse($existingSoal as $index => $soal)
                    <div class="soal-item" id="soal-{{ $soal->id }}">
                        <div class="soal-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <span class="soal-number">#{{ $existingSoal->count() - $index }}</span>
                            <div style="display: flex; gap: 10px;">
                                {{-- Tombol Edit --}}
                                <button type="button" onclick="editSoal({{ $soal->toJson() }})" style="background: #eff6ff; border: none; color: #2563eb; cursor: pointer; width: 32px; height: 32px; border-radius: 8px;" title="Edit Soal">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('pengajar.tryout.destroy_draft', $soal->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background: #fef2f2; border: none; color: #dc2626; cursor: pointer; width: 32px; height: 32px; border-radius: 8px;" title="Hapus Draf" onclick="return confirm('Apakah Anda yakin ingin menghapus draf soal ini?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <p class="soal-preview" style="font-size: 13px; color: #334155; margin: 12px 0; font-weight: 500; line-height: 1.5;">
                            {!! Str::limit(strip_tags($soal->question), 120) !!}
                        </p>
                        <div class="soal-footer" style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="key-badge">Kunci: <strong>{{ $soal->correct_answer }}</strong></span>
                            <small style="color: #94a3b8; font-size: 10px;">Dibuat: {{ $soal->created_at->format('d/m H:i') }}</small>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="text-align: center; padding: 50px 20px; color: #94a3b8;">
                        <i class="fa-solid fa-file-circle-minus" style="font-size: 40px; margin-bottom: 15px; opacity: 0.3;"></i>
                        <p style="font-weight: 700;">Belum ada soal dikirim.</p>
                        <small>Silakan input manual atau import CSV.</small>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>

{{-- SCRIPT LOGIKA EDIT --}}
<script>
    function editSoal(data) {
        // 1. Ganti Tampilan Form ke Mode Edit
        document.getElementById('form-title').innerText = "Edit Draf Soal #" + data.id;
        document.getElementById('form-subtitle').innerText = "Pastikan perubahan Anda sudah benar sebelum menekan tombol perbarui.";
        document.getElementById('btn-text').innerText = "Perbarui Soal Sekarang";
        document.getElementById('btn-cancel').style.display = "block";
        document.getElementById('btn-submit').style.background = "#f59e0b"; // Oranye

        // 2. Isi Input dengan Data Terpilih
        document.getElementById('draft_id').value = data.id;
        document.getElementById('question').value = data.question;
        document.getElementById('option_a').value = data.option_a;
        document.getElementById('option_b').value = data.option_b;
        document.getElementById('option_c').value = data.option_c;
        document.getElementById('option_d').value = data.option_d;
        document.getElementById('option_e').value = data.option_e;
        document.getElementById('correct_answer').value = data.correct_answer;
        document.getElementById('explanation').value = data.explanation || "";

        // 3. Fokus ke Form (Scroll ke atas)
        window.scrollTo({ top: 150, behavior: 'smooth' });
    }

    function cancelEdit() {
        document.getElementById('soalForm').reset();
        document.getElementById('draft_id').value = "";
        document.getElementById('form-title').innerText = "Tambah Soal Baru (Manual)";
        document.getElementById('form-subtitle').innerText = "Isi detail pertanyaan dan pilihan jawaban di bawah ini.";
        document.getElementById('btn-text').innerText = "Kirim Soal ke Admin";
        document.getElementById('btn-cancel').style.display = "none";
        document.getElementById('btn-submit').style.background = "#d90429";
    }
</script>

{{-- STYLING PREMIUM --}}
<style>
    .cp-page { padding: 10px; font-family: 'Plus Jakarta Sans', sans-serif; }
    
    .tm-hero-header { 
        background: linear-gradient(135deg, #111827 0%, #1e293b 100%); 
        border-radius: 24px; padding: 35px; color: white; 
        display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .tm-main-title { font-size: 26px; font-weight: 900; margin: 8px 0; letter-spacing: -0.5px; }
    .tm-pre-title { font-size: 10px; font-weight: 800; color: #d90429; text-transform: uppercase; letter-spacing: 2px; }
    
    .cp-main-card { background: white; border-radius: 24px; padding: 30px; border: 1px solid #f1f5f9; box-shadow: 0 10px 40px rgba(0,0,0,0.02); }
    .card-header h2 { font-size: 18px; font-weight: 800; color: #1e293b; margin-bottom: 5px; }
    .card-header p { font-size: 13px; color: #64748b; margin-bottom: 25px; }

    .tm-label { display: block; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 10px; }
    .tm-input { 
        width: 100%; padding: 14px; border-radius: 14px; border: 1.5px solid #e2e8f0; 
        background: #f8fafc; font-weight: 600; font-size: 13px; outline: none; transition: 0.2s; 
    }
    .tm-input:focus { border-color: #d90429; background: white; box-shadow: 0 0 0 4px rgba(217, 4, 41, 0.05); }
    
    .soal-item { padding: 20px; background: #f8fafc; border-radius: 20px; margin-bottom: 18px; border: 1px solid #edf2f7; transition: 0.3s; }
    .soal-item:hover { border-color: #d90429; background: white; box-shadow: 0 10px 25px rgba(0,0,0,0.03); transform: translateY(-3px); }
    .soal-number { font-weight: 900; color: #d90429; font-size: 12px; background: #fff1f2; padding: 4px 10px; border-radius: 8px; }
    
    .key-badge { background: #dcfce7; color: #15803d; padding: 6px 12px; border-radius: 10px; font-size: 11px; font-weight: 800; }
    
    .tm-alert-modern { padding: 18px 25px; border-radius: 16px; margin-bottom: 25px; display: flex; align-items: center; gap: 15px; font-weight: 700; }
    .tm-alert-modern.success { background: #dcfce7; color: #166534; border-left: 6px solid #22c55e; }
    
    .cp-back-btn { background: rgba(255,255,255,0.1); padding: 10px 20px; border-radius: 12px; color: white; text-decoration: none; font-weight: 700; font-size: 12px; transition: 0.3s; }
    .cp-back-btn:hover { background: white; color: #111827; }

    .soal-list-scroll::-webkit-scrollbar { width: 6px; }
    .soal-list-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
    .soal-list-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>
@endsection