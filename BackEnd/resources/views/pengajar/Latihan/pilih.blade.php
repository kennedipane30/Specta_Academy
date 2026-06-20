@extends('layouts.spekta')

@section('head')
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
@endsection

@section('title', 'Kelola Latihan ' . $subject_name)
@section('subtitle', 'Import dan kelola latihan soal mingguan')

@section('content')
@php
    $practiceCollection = collect($practices);
    $practiceByWeek = $practiceCollection->keyBy('week');
    $filledWeeks = $practiceCollection->pluck('week')->unique()->count();
    $progress = round(($filledWeeks / 20) * 100);
    $totalQuestions = $practiceCollection->sum('total_soal');

    // ✨ MODIFIKASI PENGAMANAN VARIABEL DRAFT
    $drafts = session()->get($draftKey ?? 'default_key', []);
    if (!is_array($drafts)) {
        $drafts = [];
    }

    $draftCount = count($drafts);
    $isDraftValid = $draftCount >= 5;

    // Pastikan $activeDraftWeek tidak error jika draft kosong
    $activeDraftWeek = null;
    if ($draftCount > 0 && isset($drafts[0]['week'])) {
        $activeDraftWeek = $drafts[0]['week'];
    }
@endphp

<div class="pq-page">

    {{-- ── 1. HEADER MINIMALIS DENGAN PROGRESS BAR GLOWING TEAL ── --}}
    <section class="pq-detail-header">
        <div class="pq-header-left">
            <a href="{{ route('pengajar.latihan.index') }}" class="pq-back-btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <span class="pq-breadcrumb-capsule">Practice Weekly Manager</span>
            <h1>{{ $subject_name }}</h1>
            <p>{{ $class->program_name }} • Kelola latihan soal untuk 20 minggu pertemuan.</p>
        </div>

        <div class="pq-progress-box">
            <strong>{{ $filledWeeks }}/20</strong>
            <span>Minggu Terisi</span>
            <div class="progress-bar-wrap">
                <em style="width: {{ $progress }}%"></em>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="pq-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="pq-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="pq-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Data belum valid.</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ── 2. UPLOAD PANEL (CSV) DIBINGKAI PUTUS-PUTUS ── --}}
    <section class="pq-upload-panel csv-box">
        <div class="pq-upload-head">
            <div>
                <h2 style="color: var(--spekta-red-dark);"><i class="fa-solid fa-file-csv"></i> Import Latihan via CSV</h2>
                <p>Gunakan fitur ini untuk mengunggah soal dalam jumlah banyak sekaligus.</p>
            </div>
        </div>

        <form action="{{ route('pengajar.latihan.store', $class->class_id) }}" method="POST" enctype="multipart/form-data" class="pq-upload-form">
            @csrf
            <input type="hidden" name="subject" value="{{ $subject_name }}">

            <div class="pq-field">
                <label>Pilih Minggu</label>
                <div class="pq-input-wrap">
                    <i class="fa-solid fa-calendar-week"></i>
                    <select name="week" required>
                        @for($i = 1; $i <= 20; $i++)
                            <option value="{{ $i }}" {{ old('week') == $i ? 'selected' : '' }}>
                                Minggu Ke-{{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="pq-field file">
                <label>File CSV</label>
                <div class="pq-input-wrap">
                    <i class="fa-solid fa-file-csv"></i>
                    <input type="file" name="file_csv" accept=".csv,text/csv" required>
                </div>
            </div>

            <button type="submit" class="pq-submit" style="background: #1f2937; box-shadow: none;">
                <i class="fa-solid fa-upload"></i> MULAI IMPORT
            </button>
        </form>
    </section>

    {{-- ── ✨ MODIFIKASI: INPUT SOAL MANUAL (2 KOLOM, TANPA OPSI E) ── --}}
    <section class="pq-manual-grid">

        <!-- KOLOM KIRI: FORM INPUT SOAL -->
        <div class="pq-manual-card">
            <div class="pq-panel-head">
                <div>
                    <h2>Tambah Soal Baru (Manual)</h2>
                    <p>Isi detail pertanyaan untuk menyicil soal (Soal Ke-{{ $draftCount + 1 }}).</p>
                </div>
            </div>

            <form action="{{ route('pengajar.latihan.store_draft', $class->class_id) }}" method="POST">
                @csrf
                <input type="hidden" name="subject" value="{{ $subject_name }}">

                <div class="pq-field" style="margin-bottom: 15px;">
                    <label>Pilih Minggu Pembelajaran</label>
                    <div class="pq-input-wrap">
                        <i class="fa-solid fa-calendar-week"></i>
                        @if($activeDraftWeek)
                            <!-- Jika sudah ada draf, kunci minggu agar tidak tercampur -->
                            <input type="hidden" name="week" value="{{ $activeDraftWeek }}">
                            <select disabled style="background: #e5e7eb; cursor: not-allowed;">
                                <option>Terkunci di Minggu Ke-{{ $activeDraftWeek }} (Selesaikan draf)</option>
                            </select>
                        @else
                            <select name="week" required>
                                @for($i = 1; $i <= 20; $i++)
                                    <option value="{{ $i }}">Minggu Ke-{{ $i }}</option>
                                @endfor
                            </select>
                        @endif
                    </div>
                </div>

                <div class="pq-field">
                    <label>PERTANYAAN</label>
                    <textarea name="question" rows="3" placeholder="Tulis soal di sini..." required></textarea>
                </div>

                <div class="pq-option-grid">
                    <div class="pq-field">
                        <label>OPSI A</label>
                        <input type="text" name="option_a" required>
                    </div>
                    <div class="pq-field">
                        <label>OPSI B</label>
                        <input type="text" name="option_b" required>
                    </div>
                    <div class="pq-field">
                        <label>OPSI C</label>
                        <input type="text" name="option_c" required>
                    </div>
                    <div class="pq-field">
                        <label>OPSI D</label>
                        <input type="text" name="option_d" required>
                    </div>
                </div>

                <div class="pq-option-grid">
                    <!-- ✨ MODIFIKASI: Menambahkan HINT Sesuai CSV -->
                    <div class="pq-field">
                        <label>KATA KUNCI (HINT)</label>
                        <input type="text" name="hint" placeholder="Opsional...">
                    </div>
                    <div class="pq-field">
                        <label>KUNCI JAWABAN</label>
                        <select name="correct_answer" required>
                            <option value="A">Opsi A</option>
                            <option value="B">Opsi B</option>
                            <option value="C">Opsi C</option>
                            <option value="D">Opsi D</option>
                            <!-- Opsi E Dihapus Sesuai CSV -->
                        </select>
                    </div>
                </div>

                <div class="pq-field">
                    <label>PEMBAHASAN (Opsional)</label>
                    <textarea name="explanation" rows="2" placeholder="Jelaskan cara pengerjaannya..."></textarea>
                </div>

                <div class="pq-form-actions" style="margin-top: 20px;">
                    @if(!$isDraftValid)
                        {{-- Jika draf masih di bawah 5, tombolnya panjang penuh --}}
                        <button type="submit" class="btn-full red">
                            <i class="fa-solid fa-paper-plane"></i> Simpan ke Draf (Soal Ke-{{ $draftCount + 1 }})
                        </button>
                    @else
                        {{-- Jika draf >= 5, tombol terbitkan muncul --}}
                        <button type="submit" class="btn-half outline">
                            <i class="fa-solid fa-plus"></i> Tambah Soal Lagi
                        </button>
                    @endif
                </div>
            </form>

            @if($isDraftValid)
                <form action="{{ route('pengajar.latihan.publish_draft', $class->class_id) }}" method="POST" style="margin-top: 10px;">
                    @csrf
                    <input type="hidden" name="subject" value="{{ $subject_name }}">
                    <button type="submit" class="btn-full green" onclick="return confirm('Terbitkan {{ $draftCount }} soal ini sekarang?')">
                        <i class="fa-solid fa-rocket"></i> TERBITKAN {{ $draftCount }} SOAL INI KE SISWA
                    </button>
                </form>
            @endif
        </div>

        <!-- KOLOM KANAN: DRAFT LIST -->
        <div class="pq-manual-card">
            <div class="pq-panel-head">
                <div>
                    <h2>Soal Terkirim ({{ $draftCount }})</h2>
                    <p>Draf soal Anda (Minimal 5 soal untuk terbit).</p>
                </div>
                <div class="draft-indicator {{ $isDraftValid ? 'valid' : 'invalid' }}">
                    {{ $draftCount }}/5
                </div>
            </div>

            <div class="pq-draft-list">
                <!-- ✨ PENGAMANAN LOOPING DRAFT -->
                @if(isset($drafts) && is_array($drafts))
                    @forelse($drafts as $index => $draft)
                        @if(is_array($draft))
                            <div class="draft-item">
                                <div class="draft-number">{{ $index + 1 }}</div>
                                <div class="draft-content">
                                    <strong>{{ Str::limit($draft['question'] ?? 'Soal', 40) }}</strong>
                                    <span>Kunci: {{ $draft['correct_answer'] ?? '-' }}</span>
                                </div>
                                <a href="{{ route('pengajar.latihan.delete_draft', ['class_id' => $class->class_id, 'draft_id' => $draft['id'] ?? '0', 'subject' => $subject_name]) }}"
                                   class="draft-delete"
                                   onclick="return confirm('Hapus draf soal ini?')">
                                   <i class="fa-solid fa-xmark"></i>
                                </a>
                            </div>
                        @endif
                    @empty
                        <div class="pq-empty" style="padding: 20px;">
                            <div class="pq-empty-icon" style="width:30px; height:30px; font-size:12px;"><i class="fa-solid fa-file-circle-question"></i></div>
                            <strong>Belum ada draf soal.</strong>
                            <span style="font-size:10px;">Isi form di samping untuk mulai mencicil.</span>
                        </div>
                    @endforelse
                @endif
            </div>
        </div>

    </section>

    {{-- ── 3. WEEK OVERVIEW (RINGKASAN DOTS MINGGU 1-20) ── --}}
    <section class="pq-week-panel">
        <div class="pq-panel-head">
            <div>
                <h2>Ringkasan Soal Terupload</h2>
                <p>Pantau minggu yang sudah memiliki bank soal dan kelola data latihan per pertemuan.</p>
            </div>
        </div>

        <!-- Dots Mingguan Berpendar -->
        <div class="pq-week-strip">
            @for($i = 1; $i <= 20; $i++)
                @php $hasPractice = $practiceByWeek->has($i); @endphp

                <div class="pq-week-dot {{ $hasPractice ? 'filled' : '' }}">
                    <span>{{ $i }}</span>
                </div>
            @endfor
        </div>

        {{-- Tabel Manajemen Latihan --}}
        <div class="pq-table-wrap">
            <table class="pq-table">
                <thead>
                    <tr>
                        <th>Pertemuan</th>
                        <th>Status Konten</th>
                        <th>Jumlah Soal</th>
                        <th>Keterangan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($practices as $p)
                        <tr>
                            <td>
                                <span class="pq-week-badge">
                                    Minggu {{ $p->week }}
                                </span>
                            </td>

                            <td>
                                <span class="pq-content-status active">
                                    <span class="pq-dot-wrapper">
                                        <i class="pq-dot"></i>
                                        <i class="pq-dot-pulse"></i>
                                    </span>
                                    Tersedia
                                </span>
                            </td>

                            <td>
                                <div class="pq-question-count">
                                    <strong>{{ number_format($p->total_soal) }}</strong>
                                    <span>soal</span>
                                </div>
                            </td>

                            <td>
                                <span class="pq-note-text">
                                    Latihan minggu ke-{{ $p->week }} sudah dapat digunakan siswa.
                                </span>
                            </td>

                            <td class="text-right">
                                <form action="{{ route('pengajar.latihan.destroy_week', [
                                    'class_id' => $class->class_id,
                                    'subject_name' => $subject_name,
                                    'week' => $p->week
                                ]) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus semua soal di Minggu ke-{{ $p->week }}?')"
                                      style="display: inline-flex;">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="pq-delete">
                                        <i class="fa-solid fa-trash-can"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="pq-empty">
                                    <div class="pq-empty-icon"><i class="fa-solid fa-file-circle-plus"></i></div>
                                    <strong>Belum ada latihan soal yang diunggah.</strong>
                                    <span>Gunakan form import di atas untuk mengunggah bank soal pertama.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

</div>

<style>
    /* STANDAR COLOR VARIABLE (DIBAWA DARI SEBELUMNYA) */
    :root {
        --spekta-red-dark: #c5352c;
        --spekta-red: #e53935;
        --spekta-teal: #2ea8ab;
        --spekta-teal-light: rgba(46, 168, 171, 0.08);
        --spekta-red-light: rgba(229, 57, 53, 0.06);
        --spekta-gray: #9e9e9e;
        --spekta-gray-light: #f3f4f6;
        --spekta-white: #ffffff;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --border-soft: #e5e7eb;
    }

    .pq-page {
        width: 100%;
        font-family: 'Montserrat', sans-serif;
        color: var(--text-main);
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* CSS LAMA TETAP DIPERTAHANKAN DI SINI */
    .pq-detail-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; gap: 20px; border-bottom: 1px solid var(--border-soft); padding-bottom: 20px; }
    .pq-back-btn { display: inline-flex; align-items: center; gap: 6px; border: 1px solid var(--border-soft); background: var(--spekta-white); color: var(--text-muted); border-radius: 8px; padding: 6px 12px; font-size: 11px; font-weight: 800; text-decoration: none; transition: all 0.2s; margin-bottom: 12px; }
    .pq-back-btn:hover { background: var(--spekta-gray-light); color: var(--text-main); border-color: var(--spekta-gray); }
    .pq-breadcrumb-capsule { display: inline-block; background: var(--spekta-red-light); color: var(--spekta-red-dark); font-size: 10px; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; padding: 4px 10px; border-radius: 6px; margin-bottom: 8px; }
    .pq-detail-header h1 { margin: 0 0 6px; color: var(--text-main); font-size: 24px; font-weight: 900; letter-spacing: -0.02em; }
    .pq-detail-header p { margin: 0; color: var(--text-muted); font-size: 13px; font-weight: 600; }
    .pq-progress-box { width: 200px; flex-shrink: 0; padding: 14px; border-radius: 12px; background: var(--spekta-white); border: 1px solid var(--border-soft); box-shadow: 0 2px 10px rgba(0,0,0,0.01); }
    .pq-progress-box strong { display: block; font-size: 22px; font-weight: 900; color: var(--text-main); line-height: 1; }
    .pq-progress-box span { display: block; margin: 4px 0 10px; color: var(--text-muted); font-size: 10px; font-weight: 800; text-transform: uppercase; }
    .progress-bar-wrap { height: 6px; border-radius: 999px; background: var(--spekta-gray-light); overflow: hidden; }
    .progress-bar-wrap em { display: block; height: 100%; border-radius: 999px; background: var(--spekta-teal); box-shadow: 0 0 8px rgba(46,168,171,0.3); transition: width 0.6s ease; }
    .pq-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 12px; margin-bottom: 24px; font-size: 13px; font-weight: 800; }
    .pq-alert.success { background: #e6f7ed; color: #15803d; border: 1px solid #bbf7d0; }
    .pq-alert.error { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    .pq-alert ul { margin: 4px 0 0; padding-left: 18px; }

    .pq-upload-panel, .pq-week-panel { background: var(--spekta-white); border: 1px solid var(--border-soft); border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.01); margin-bottom: 24px; }

    .csv-box { border: 2px dashed #fca5a5; background: #fffcfc; }

    .pq-upload-head, .pq-panel-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 18px; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--spekta-gray-light); }
    .pq-upload-head h2, .pq-panel-head h2 { margin: 0; color: var(--text-main); font-size: 15px; font-weight: 800; }
    .pq-upload-head p, .pq-panel-head p { margin: 4px 0 0; color: var(--text-muted); font-size: 11px; font-weight: 600; }
    .pq-total-box { min-width: 100px; padding: 10px; border-radius: 10px; background: var(--spekta-gray-light); border: 1px solid var(--border-soft); text-align: center; }
    .pq-total-box strong { display: block; color: var(--text-main); font-size: 20px; font-weight: 900; line-height: 1; }
    .pq-total-box span { margin-top: 2px; color: var(--text-muted); font-size: 9px; font-weight: 800; text-transform: uppercase; }
    .pq-upload-form { display: grid; grid-template-columns: 180px minmax(0, 1fr) 160px; gap: 15px; align-items: end; }
    .pq-field label { display: block; color: var(--text-muted); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px; }
    .pq-input-wrap { position: relative; display: flex; }
    .pq-input-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--spekta-gray); font-size: 12px; pointer-events: none; }
    .pq-field select, .pq-field input, .pq-field textarea { width: 100%; border: 1px solid var(--border-soft); border-radius: 10px; background: var(--spekta-gray-light); padding: 0 14px; color: var(--text-main); font-size: 12px; font-weight: 600; outline: none; font-family: inherit; transition: all 0.25s; }
    .pq-field select, .pq-field input { height: 40px; padding-left: 38px; }
    .pq-field textarea { padding: 12px; resize: vertical; }
    .pq-field input[type="file"] { padding-top: 10px; }
    .pq-field select:focus, .pq-field input:focus, .pq-field textarea:focus { background: var(--spekta-white); border-color: var(--spekta-teal); box-shadow: 0 0 0 3px rgba(46, 168, 171, 0.12); }
    .pq-submit { height: 40px; border: none; border-radius: 10px; background: linear-gradient(135deg, var(--spekta-red) 0%, var(--spekta-red-dark) 100%); color: var(--spekta-white); display: inline-flex; justify-content: center; align-items: center; gap: 8px; font-size: 12px; font-weight: 800; text-transform: uppercase; cursor: pointer; font-family: inherit; box-shadow: 0 4px 10px rgba(229, 57, 53, 0.15); transition: all 0.2s ease; }
    .pq-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 15px rgba(229, 57, 53, 0.25); }

    .pq-manual-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px; align-items: flex-start;}
    .pq-manual-card { background: var(--spekta-white); border: 1px solid var(--border-soft); border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.01); }
    .pq-option-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }

    .btn-full { width: 100%; height: 44px; border: none; border-radius: 10px; font-weight: 800; font-size: 12px; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 8px; transition: 0.2s; }
    .btn-full.red { background: var(--spekta-red); color: white; }
    .btn-full.green { background: #16a34a; color: white; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2); }
    .btn-full.green:hover { background: #15803d; transform: translateY(-2px); }

    .btn-half { width: 100%; height: 44px; border: none; border-radius: 10px; font-weight: 800; font-size: 12px; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 8px; transition: 0.2s; }
    .btn-half.outline { background: transparent; border: 2px solid var(--spekta-teal); color: var(--spekta-teal); }
    .btn-half.outline:hover { background: var(--spekta-teal-light); }

    .pq-draft-list { display: flex; flex-direction: column; gap: 10px; }
    .draft-item { display: flex; align-items: center; gap: 10px; padding: 12px; border-radius: 10px; background: #f8fafc; border: 1px solid var(--border-soft); }
    .draft-number { width: 24px; height: 24px; background: var(--spekta-teal); color: white; border-radius: 6px; display: grid; place-items: center; font-size: 10px; font-weight: 900; flex-shrink: 0; }
    .draft-content { flex: 1; }
    .draft-content strong { display: block; font-size: 11px; color: var(--text-main); font-weight: 700; margin-bottom: 2px; line-height: 1.3;}
    .draft-content span { font-size: 10px; color: var(--spekta-teal); font-weight: 800; }
    .draft-delete { color: #ef4444; background: #fee2e2; width: 24px; height: 24px; display: grid; place-items: center; border-radius: 6px; text-decoration: none; transition: 0.2s; flex-shrink: 0;}
    .draft-delete:hover { background: #ef4444; color: white; }

    .draft-indicator { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; }
    .draft-indicator.invalid { background: #fee2e2; color: #b91c1c; }
    .draft-indicator.valid { background: #dcfce7; color: #15803d; }

    .pq-week-strip { display: grid; grid-template-columns: repeat(20, minmax(0, 1fr)); gap: 6px; padding: 12px; border-radius: 12px; background: var(--spekta-gray-light); border: 1px solid var(--border-soft); margin-bottom: 18px; }
    .pq-week-dot { min-height: 30px; display: grid; place-items: center; border-radius: 8px; background: var(--spekta-white); border: 1px solid var(--border-soft); }
    .pq-week-dot span { color: var(--spekta-gray); font-size: 10px; font-weight: 800; }
    .pq-week-dot.filled { background: #e6f7ed; border-color: #bbf7d0; }
    .pq-week-dot.filled span { color: #15803d; }
    .pq-table-wrap { overflow-x: auto; }
    .pq-table { width: 100%; border-collapse: collapse; min-width: 750px; }
    .pq-table th { text-align: left; padding: 12px 14px; border-bottom: 2px solid var(--spekta-gray-light); color: var(--text-muted); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; white-space: nowrap; }
    .pq-table td { padding: 14px; border-bottom: 1px solid var(--spekta-gray-light); vertical-align: middle; }
    .pq-table tbody tr:hover { background: #fafbfc; }
    .pq-week-badge { display: inline-flex; align-items: center; height: 24px; padding: 0 10px; border-radius: 6px; background: var(--spekta-red-light); color: var(--spekta-red-dark); font-size: 10px; font-weight: 800; text-transform: uppercase; white-space: nowrap; }
    .pq-content-status { display: inline-flex; align-items: center; gap: 5px; font-size: 10px; font-weight: 800; text-transform: uppercase; }
    .pq-dot-wrapper { position: relative; width: 5px; height: 5px; display: inline-block; }
    .pq-dot { width: 5px; height: 5px; border-radius: 99px; background: currentColor; display: block; position: absolute; left: 0; top: 0; }
    .pq-dot-pulse { width: 5px; height: 5px; border-radius: 99px; background: currentColor; display: block; position: absolute; left: 0; top: 0; opacity: 0.4; transform: scale(1); animation: dotGlow 1.8s infinite ease-in-out; }
    @keyframes dotGlow { 0% { transform: scale(1); opacity: 0.8; } 100% { transform: scale(3.2); opacity: 0; } }
    .pq-content-status.active { color: #15803d; }
    .pq-question-count strong { color: var(--text-main); font-size: 15px; font-weight: 900; }
    .pq-question-count span { color: var(--text-muted); font-size: 11px; font-weight: 800; margin-left: 4px; }
    .pq-note-text { color: var(--text-muted); font-size: 12px; font-weight: 600; }
    .pq-delete { height: 30px; border: none; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; padding: 0 10px; background: var(--spekta-red-light); color: var(--spekta-red); font-size: 10px; font-weight: 800; text-transform: uppercase; cursor: pointer; font-family: inherit; transition: all 0.2s; }
    .pq-delete:hover { background: #fecaca; color: #991b1b; transform: scale(1.05); }
    .pq-empty { padding: 40px; text-align: center; color: var(--text-muted); font-size: 11px; font-weight: 700; display: flex; flex-direction: column; align-items: center; gap: 8px; }
    .pq-empty-icon { width: 48px; height: 48px; margin: 0 auto 8px; display: grid; place-items: center; border-radius: 50%; background: var(--spekta-gray-light); color: var(--spekta-gray); font-size: 18px; }
    .pq-empty strong { display: block; color: var(--text-main); font-size: 14px; font-weight: 800; margin-bottom: 4px; }
    .text-right { text-align: right; }

    @media (max-width: 1200px) {
        .pq-manual-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection
