@extends('layouts.spekta')

@section('title', 'Input Soal TO')

@section('content')
<div class="cp-page">
    {{-- HEADER --}}
    <section class="tm-hero-header">
        <div class="tm-hero-content">
            <div class="tm-hero-text">
                <span class="tm-pre-title">TRYOUT QUESTION BUILDER</span>
                <h1 class="tm-main-title">Input Soal: {{ $subjectName }}</h1>
                <p class="tm-sub-title">Kelas: {{ $classModel->program_name }}</p>
            </div>
        </div>
        <a href="{{ route('pengajar.tryout.index') }}" class="cp-back-btn">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </section>

    @if(session('success'))
        <div class="tm-alert-modern success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- ✨ 1. BOX IMPORT CSV (TAMBAHAN BARU) ✨ --}}
    <section class="cp-main-card mb-4" style="border: 2px dashed #d90429; background: #fff1f2; margin-bottom: 25px;">
        <div class="card-header">
            <h2 style="color: #d90429;"><i class="fa-solid fa-file-csv"></i> Import Soal via CSV</h2>
            <p>Unggah banyak soal sekaligus menggunakan file Excel/CSV. <a href="#" style="color: #111827; text-decoration: underline; font-weight: 800;">Download Template CSV</a></p>
        </div>

        <form action="{{ route('pengajar.tryout.import_csv') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="class_id" value="{{ $classId }}">
            <input type="hidden" name="subject_name" value="{{ $subjectName }}">

            <div style="display: flex; gap: 15px; align-items: center;">
                <div style="flex: 1;">
                    <input type="file" name="file_csv" class="tm-input" accept=".csv" required style="padding: 10px;">
                </div>
                <button type="submit" class="cp-primary-btn" style="background: #111827; height: 48px; border-radius: 12px; color: white; padding: 0 25px; border: none; cursor: pointer; font-weight: 800;">
                    <i class="fa-solid fa-upload"></i> MULAI IMPORT
                </button>
            </div>
        </form>
    </section>

    <div class="tm-grid-layout" style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 25px;">
        
        {{-- KOLOM KIRI: FORM INPUT SOAL MANUAL --}}
        <section class="cp-main-card">
            <div class="card-header">
                <h2>Tambah Soal Baru (Manual)</h2>
                <p>Isi detail pertanyaan dan pilihan jawaban di bawah ini.</p>
            </div>

            <form action="{{ route('pengajar.tryout.store') }}" method="POST">
                @csrf
                <input type="hidden" name="class_id" value="{{ $classId }}">
                <input type="hidden" name="subject_name" value="{{ $subjectName }}">

                <div class="form-group mb-4">
                    <label class="tm-label">Pertanyaan</label>
                    <textarea name="question" rows="4" class="tm-input" placeholder="Tulis soal di sini..." required></textarea>
                </div>

                <div class="options-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    @foreach(['a','b','c','d','e'] as $opt)
                    <div class="form-group">
                        <label class="tm-label">Opsi {{ strtoupper($opt) }}</label>
                        <input type="text" name="option_{{ $opt }}" class="tm-input" required>
                    </div>
                    @endforeach
                    
                    <div class="form-group">
                        <label class="tm-label">Kunci Jawaban</label>
                        <select name="correct_answer" class="tm-input" required>
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
                    <textarea name="explanation" rows="3" class="tm-input" placeholder="Jelaskan cara pengerjaannya..."></textarea>
                </div>

                <button type="submit" class="cp-primary-btn mt-4" style="width: 100%; background: #d90429; color: white; border: none; padding: 15px; border-radius: 12px; font-weight: 800; cursor: pointer; margin-top: 20px;">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Soal ke Admin
                </button>
            </form>
        </section>

        {{-- KOLOM KANAN: DAFTAR SOAL YANG SUDAH DIKIRIM --}}
        <section class="cp-main-card">
            <div class="card-header">
                <h2>Soal Terkirim ({{ $existingSoal->count() }})</h2>
                <p>Daftar draf soal yang sedang menunggu kurasi Admin.</p>
            </div>

            <div class="soal-list-scroll" style="max-height: 800px; overflow-y: auto; padding-right: 10px;">
                @forelse($existingSoal as $index => $soal)
                    <div class="soal-item">
                        <div class="soal-header" style="display: flex; justify-content: space-between;">
                            <span class="soal-number">#{{ $existingSoal->count() - $index }}</span>
                            <form action="{{ route('pengajar.tryout.destroy_draft', $soal->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer;" title="Hapus Draf" onclick="return confirm('Hapus draf soal ini?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                        <p class="soal-preview" style="font-size: 13px; margin: 10px 0;">{!! Str::limit($soal->question, 100) !!}</p>
                        <div class="soal-footer">
                            <span class="key-badge">Kunci: {{ $soal->correct_answer }}</span>
                            <small style="margin-left: 10px; color: #94a3b8;">{{ $soal->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">Belum ada soal dikirim.</div>
                @endforelse
            </div>
        </section>
    </div>
</div>

<style>
    /* Premium Page Styling */
    .cp-page { padding: 5px; font-family: 'Plus Jakarta Sans', sans-serif; }
    .tm-hero-header { background: #111827; border-radius: 24px; padding: 30px; color: white; display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    .tm-main-title { font-size: 24px; font-weight: 900; margin: 5px 0; }
    .cp-main-card { background: white; border-radius: 24px; padding: 25px; border: 1px solid #f1f5f9; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
    .tm-label { display: block; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; }
    .tm-input { width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; font-weight: 600; font-size: 13px; }
    
    .soal-item { padding: 18px; background: #f8fafc; border-radius: 20px; margin-bottom: 15px; border: 1px solid #edf2f7; transition: 0.3s; }
    .soal-item:hover { border-color: #d90429; background: white; }
    .soal-number { font-weight: 900; color: #d90429; font-size: 12px; }
    .key-badge { background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 8px; font-size: 10px; font-weight: 900; }
    
    .tm-alert-modern { padding: 15px; border-radius: 12px; background: #dcfce7; color: #15803d; font-weight: 800; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    .cp-back-btn { color: white; text-decoration: none; font-weight: 700; font-size: 13px; }
</style>
@endsection