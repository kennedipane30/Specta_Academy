@extends('layouts.spekta')

@section('title', 'Buat Paket Soal Tryout')
@section('subtitle', 'Input soal, opsi jawaban, kunci, dan pembahasan')

@section('content')
@php
    $oldSoal = old('soal', []);
    $initialCount = max(count($oldSoal), 5);
    $totalExisting = collect($existingSoal)->count();
@endphp

<div class="tc-page">

    {{-- HEADER --}}
    <section class="tc-hero">
        <div>
            <a href="{{ route('pengajar.tryout.index') }}" class="tc-back">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>

            <span>Tryout Question Builder</span>
            <h1>Buat Paket Soal</h1>
            <p>
                {{ $class->program_name }} • {{ $subject_name }}.
                Minimal isi 5 soal lengkap sebelum diterbitkan ke admin.
            </p>
        </div>

        <div class="tc-summary">
            <div>
                <strong>{{ $totalExisting }}</strong>
                <span>Soal Terkirim</span>
            </div>

            <div>
                <strong>5</strong>
                <span>Minimal Soal</span>
            </div>
        </div>
    </section>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="tc-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="tc-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="tc-alert error">
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

    {{-- FORM --}}
    <form action="{{ route('pengajar.tryout.store') }}" method="POST" enctype="multipart/form-data" id="tryoutForm">
        @csrf

        <input type="hidden" name="class_id" value="{{ $class_id }}">
        <input type="hidden" name="subject_name" value="{{ $subject_name }}">

        <section class="tc-panel">
            <div class="tc-panel-head">
                <div>
                    <span>Question Input</span>
                    <h2>Form Input Soal Tryout</h2>
                    <p>
                        Setiap soal dapat memakai teks, gambar, atau kombinasi keduanya. Opsi A wajib diisi jika tidak memakai gambar opsi A.
                    </p>
                </div>

                <button type="button" class="tc-add-btn" id="addQuestionBtn">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Soal
                </button>
            </div>

            <div class="tc-note">
                <i class="fa-solid fa-circle-info"></i>
                <span>
                    Pembahasan hanya boleh berisi huruf, angka, spasi, serta tanda baca dasar seperti titik, koma, tanda tanya, tanda seru, dan strip.
                </span>
            </div>

            <div id="questionList" class="tc-question-list">
                @for($i = 0; $i < $initialCount; $i++)
                    <article class="tc-question-item" data-index="{{ $i }}">
                        <div class="tc-question-top">
                            <div>
                                <span>Question</span>
                                <h3>Soal {{ $i + 1 }}</h3>
                            </div>

                            <button type="button" class="tc-remove-btn" onclick="removeQuestion(this)">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>

                        <div class="tc-field">
                            <label>Pertanyaan</label>
                            <textarea name="soal[{{ $i }}][question]" rows="4" placeholder="Tulis pertanyaan di sini...">{{ old("soal.$i.question") }}</textarea>
                        </div>

                        <div class="tc-field">
                            <label>Gambar Pertanyaan</label>
                            <input type="file" name="soal[{{ $i }}][q_img]" accept="image/*">
                        </div>

                        <div class="tc-options-grid">
                            <div class="tc-option-box">
                                <label>Opsi A</label>
                                <input type="text" name="soal[{{ $i }}][option_a]" value="{{ old("soal.$i.option_a") }}" placeholder="Teks opsi A">
                                <input type="file" name="soal[{{ $i }}][a_img]" accept="image/*">
                            </div>

                            <div class="tc-option-box">
                                <label>Opsi B</label>
                                <input type="text" name="soal[{{ $i }}][option_b]" value="{{ old("soal.$i.option_b") }}" placeholder="Teks opsi B">
                                <input type="file" name="soal[{{ $i }}][b_img]" accept="image/*">
                            </div>

                            <div class="tc-option-box">
                                <label>Opsi C</label>
                                <input type="text" name="soal[{{ $i }}][option_c]" value="{{ old("soal.$i.option_c") }}" placeholder="Teks opsi C">
                                <input type="file" name="soal[{{ $i }}][c_img]" accept="image/*">
                            </div>

                            <div class="tc-option-box">
                                <label>Opsi D</label>
                                <input type="text" name="soal[{{ $i }}][option_d]" value="{{ old("soal.$i.option_d") }}" placeholder="Teks opsi D">
                                <input type="file" name="soal[{{ $i }}][d_img]" accept="image/*">
                            </div>
                        </div>

                        <div class="tc-bottom-grid">
                            <div class="tc-field">
                                <label>Kunci Jawaban</label>
                                <select name="soal[{{ $i }}][correct_answer]">
                                    <option value="">Pilih Jawaban</option>
                                    <option value="A" {{ old("soal.$i.correct_answer") == 'A' ? 'selected' : '' }}>A</option>
                                    <option value="B" {{ old("soal.$i.correct_answer") == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="C" {{ old("soal.$i.correct_answer") == 'C' ? 'selected' : '' }}>C</option>
                                    <option value="D" {{ old("soal.$i.correct_answer") == 'D' ? 'selected' : '' }}>D</option>
                                </select>
                            </div>

                            <div class="tc-field">
                                <label>Pembahasan</label>
                                <textarea name="soal[{{ $i }}][explanation]" rows="3" placeholder="Tulis pembahasan singkat...">{{ old("soal.$i.explanation") }}</textarea>
                            </div>
                        </div>
                    </article>
                @endfor
            </div>

            <div class="tc-submit-bar">
                <div>
                    <strong id="questionCount">{{ $initialCount }}</strong>
                    <span>form soal tersedia. Minimal 5 soal harus diisi lengkap.</span>
                </div>

                <button type="submit">
                    <i class="fa-solid fa-paper-plane"></i>
                    Terbitkan Paket Soal
                </button>
            </div>
        </section>
    </form>

    {{-- EXISTING QUESTIONS --}}
    <section class="tc-panel">
        <div class="tc-panel-head">
            <div>
                <span>Submitted Questions</span>
                <h2>Riwayat Soal Terkirim</h2>
                <p>Daftar soal tryout yang sudah pernah dikirim untuk program dan bidang ini.</p>
            </div>
        </div>

        <div class="tc-table-wrap">
            <table class="tc-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pertanyaan</th>
                        <th>Opsi</th>
                        <th>Kunci</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($existingSoal as $index => $row)
                        <tr>
                            <td>
                                <span class="tc-number">{{ $index + 1 }}</span>
                            </td>

                            <td>
                                <div class="tc-existing-question">
                                    <strong>{{ Str::limit($row->question ?? 'Pertanyaan berbentuk gambar', 90) }}</strong>

                                    @if($row->question_image)
                                        <a href="{{ asset('storage/tryout/images/' . $row->question_image) }}" target="_blank">
                                            Lihat gambar pertanyaan
                                        </a>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <div class="tc-option-preview">
                                    <span>A: {{ Str::limit($row->option_a ?? 'Gambar', 25) }}</span>
                                    <span>B: {{ Str::limit($row->option_b ?? 'Gambar / kosong', 25) }}</span>
                                    <span>C: {{ Str::limit($row->option_c ?? 'Gambar / kosong', 25) }}</span>
                                    <span>D: {{ Str::limit($row->option_d ?? 'Gambar / kosong', 25) }}</span>
                                </div>
                            </td>

                            <td>
                                <span class="tc-answer">{{ $row->correct_answer }}</span>
                            </td>

                            <td>
                                <span class="tc-date">
                                    {{ $row->created_at ? $row->created_at->translatedFormat('d M Y') : '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="tc-empty">
                                    <i class="fa-solid fa-folder-open"></i>
                                    <strong>Belum ada soal terkirim.</strong>
                                    <span>Soal yang sudah diterbitkan akan muncul pada tabel ini.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

</div>

<template id="questionTemplate">
    <article class="tc-question-item" data-index="__INDEX__">
        <div class="tc-question-top">
            <div>
                <span>Question</span>
                <h3>Soal __NUMBER__</h3>
            </div>

            <button type="button" class="tc-remove-btn" onclick="removeQuestion(this)">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>

        <div class="tc-field">
            <label>Pertanyaan</label>
            <textarea name="soal[__INDEX__][question]" rows="4" placeholder="Tulis pertanyaan di sini..."></textarea>
        </div>

        <div class="tc-field">
            <label>Gambar Pertanyaan</label>
            <input type="file" name="soal[__INDEX__][q_img]" accept="image/*">
        </div>

        <div class="tc-options-grid">
            <div class="tc-option-box">
                <label>Opsi A</label>
                <input type="text" name="soal[__INDEX__][option_a]" placeholder="Teks opsi A">
                <input type="file" name="soal[__INDEX__][a_img]" accept="image/*">
            </div>

            <div class="tc-option-box">
                <label>Opsi B</label>
                <input type="text" name="soal[__INDEX__][option_b]" placeholder="Teks opsi B">
                <input type="file" name="soal[__INDEX__][b_img]" accept="image/*">
            </div>

            <div class="tc-option-box">
                <label>Opsi C</label>
                <input type="text" name="soal[__INDEX__][option_c]" placeholder="Teks opsi C">
                <input type="file" name="soal[__INDEX__][c_img]" accept="image/*">
            </div>

            <div class="tc-option-box">
                <label>Opsi D</label>
                <input type="text" name="soal[__INDEX__][option_d]" placeholder="Teks opsi D">
                <input type="file" name="soal[__INDEX__][d_img]" accept="image/*">
            </div>
        </div>

        <div class="tc-bottom-grid">
            <div class="tc-field">
                <label>Kunci Jawaban</label>
                <select name="soal[__INDEX__][correct_answer]">
                    <option value="">Pilih Jawaban</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>

            <div class="tc-field">
                <label>Pembahasan</label>
                <textarea name="soal[__INDEX__][explanation]" rows="3" placeholder="Tulis pembahasan singkat..."></textarea>
            </div>
        </div>
    </article>
</template>

<style>
    .tc-page {
        width: 100%;
    }

    .tc-hero {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 24px;
        margin-bottom: 22px;
        padding: 28px 30px;
        border-radius: 24px;
        color: #fff;
        background: linear-gradient(120deg, #cf002b 0%, #85001d 52%, #182033 100%);
        box-shadow: 0 18px 38px rgba(134, 0, 24, .18);
        overflow: hidden;
        position: relative;
    }

    .tc-hero::after {
        content: "";
        width: 280px;
        height: 280px;
        border-radius: 999px;
        background: rgba(255,255,255,.09);
        position: absolute;
        right: -95px;
        top: -130px;
    }

    .tc-hero > div {
        position: relative;
        z-index: 2;
    }

    .tc-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        background: rgba(255,255,255,.13);
        border: 1px solid rgba(255,255,255,.17);
        color: #fff;
        font-size: 11px;
        font-weight: 900;
        margin-bottom: 18px;
    }

    .tc-hero span,
    .tc-panel-head span {
        display: block;
        color: rgba(255,255,255,.78);
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .16em;
        margin-bottom: 9px;
    }

    .tc-panel-head span {
        color: #d90429;
    }

    .tc-hero h1 {
        margin: 0 0 8px;
        color: #fff;
        font-size: 30px;
        font-weight: 900;
        letter-spacing: -0.04em;
        text-transform: uppercase;
    }

    .tc-hero p {
        margin: 0;
        color: rgba(255,255,255,.86);
        font-size: 13px;
        font-weight: 700;
        line-height: 1.6;
    }

    .tc-summary {
        display: flex;
        gap: 10px;
        flex-shrink: 0;
    }

    .tc-summary div {
        min-width: 105px;
        padding: 15px;
        border-radius: 18px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.17);
        text-align: center;
    }

    .tc-summary strong {
        display: block;
        color: #fff;
        font-size: 25px;
        font-weight: 900;
        line-height: 1;
    }

    .tc-summary span {
        margin: 8px 0 0;
        color: rgba(255,255,255,.76);
        letter-spacing: 0;
    }

    .tc-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 16px;
        border-radius: 15px;
        margin-bottom: 18px;
        font-size: 12px;
        font-weight: 800;
    }

    .tc-alert.success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .tc-alert.error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .tc-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .tc-panel {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 22px;
        padding: 22px;
        box-shadow: 0 14px 35px rgba(15,23,42,.05);
        margin-bottom: 22px;
    }

    .tc-panel-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 18px;
    }

    .tc-panel-head h2 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
    }

    .tc-panel-head p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
    }

    .tc-add-btn {
        border: none;
        height: 38px;
        border-radius: 12px;
        background: #d90429;
        color: #fff;
        padding: 0 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        cursor: pointer;
        font-family: inherit;
        white-space: nowrap;
    }

    .tc-note {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 13px 14px;
        border-radius: 15px;
        background: #f8fafc;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.5;
        margin-bottom: 18px;
    }

    .tc-note i {
        color: #d90429;
        margin-top: 2px;
    }

    .tc-question-list {
        display: grid;
        gap: 16px;
    }

    .tc-question-item {
        border: 1px solid #edf0f4;
        border-radius: 20px;
        padding: 18px;
        background: #fff;
    }

    .tc-question-top {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: flex-start;
        padding-bottom: 14px;
        border-bottom: 1px solid #edf0f4;
        margin-bottom: 16px;
    }

    .tc-question-top span {
        display: block;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .16em;
        margin-bottom: 6px;
    }

    .tc-question-top h3 {
        margin: 0;
        color: #111827;
        font-size: 17px;
        font-weight: 900;
    }

    .tc-remove-btn {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 12px;
        background: #fee2e2;
        color: #dc2626;
        cursor: pointer;
    }

    .tc-field {
        margin-bottom: 14px;
    }

    .tc-field label,
    .tc-option-box label {
        display: block;
        color: #374151;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: 8px;
    }

    .tc-field textarea,
    .tc-field input,
    .tc-field select,
    .tc-option-box input {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
        color: #111827;
        font-size: 12px;
        font-weight: 700;
        outline: none;
        font-family: inherit;
    }

    .tc-field textarea {
        padding: 14px;
        resize: vertical;
    }

    .tc-field input,
    .tc-field select,
    .tc-option-box input {
        height: 44px;
        padding: 0 13px;
    }

    .tc-field input[type="file"],
    .tc-option-box input[type="file"] {
        padding-top: 11px;
        font-size: 11px;
        color: #6b7280;
    }

    .tc-field textarea:focus,
    .tc-field input:focus,
    .tc-field select:focus,
    .tc-option-box input:focus {
        background: #fff;
        border-color: #fecdd3;
        box-shadow: 0 0 0 4px rgba(217, 4, 41, .08);
    }

    .tc-options-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }

    .tc-option-box {
        background: #f8fafc;
        border: 1px solid #edf0f4;
        border-radius: 16px;
        padding: 13px;
    }

    .tc-option-box input + input {
        margin-top: 8px;
    }

    .tc-bottom-grid {
        display: grid;
        grid-template-columns: 190px minmax(0, 1fr);
        gap: 14px;
        align-items: start;
    }

    .tc-submit-bar {
        margin-top: 20px;
        border-radius: 18px;
        background: #f8fafc;
        border: 1px solid #edf0f4;
        padding: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
    }

    .tc-submit-bar strong {
        color: #111827;
        font-size: 24px;
        font-weight: 900;
        line-height: 1;
    }

    .tc-submit-bar span {
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
        margin-left: 8px;
    }

    .tc-submit-bar button {
        border: none;
        height: 44px;
        border-radius: 13px;
        background: #d90429;
        color: #fff;
        padding: 0 18px;
        display: inline-flex;
        align-items: center;
        gap: 9px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        cursor: pointer;
        font-family: inherit;
    }

    .tc-table-wrap {
        overflow-x: auto;
    }

    .tc-table {
        width: 100%;
        border-collapse: collapse;
    }

    .tc-table th {
        text-align: left;
        padding: 14px 12px;
        border-bottom: 1px solid #edf0f4;
        color: #6b7280;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
        white-space: nowrap;
    }

    .tc-table td {
        padding: 16px 12px;
        border-bottom: 1px solid #edf0f4;
        vertical-align: top;
    }

    .tc-table tbody tr:hover {
        background: #fff7f9;
    }

    .tc-number,
    .tc-answer {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 900;
    }

    .tc-number {
        background: #f3f4f6;
        color: #6b7280;
    }

    .tc-answer {
        background: #dcfce7;
        color: #16a34a;
    }

    .tc-existing-question strong {
        display: block;
        color: #111827;
        font-size: 12px;
        font-weight: 900;
        line-height: 1.5;
    }

    .tc-existing-question a {
        display: inline-flex;
        margin-top: 7px;
        color: #2563eb;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .tc-option-preview {
        display: grid;
        gap: 4px;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
    }

    .tc-date {
        color: #6b7280;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .tc-empty {
        padding: 42px;
        text-align: center;
        background: #f8fafc;
        border-radius: 18px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .tc-empty i {
        width: 58px;
        height: 58px;
        margin: 0 auto 14px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: #ffe8ee;
        color: #d90429;
        font-size: 22px;
    }

    .tc-empty strong {
        display: block;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    @media (max-width: 1200px) {
        .tc-options-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 850px) {
        .tc-hero,
        .tc-panel-head,
        .tc-submit-bar {
            flex-direction: column;
            align-items: flex-start;
        }

        .tc-summary {
            width: 100%;
        }

        .tc-summary div {
            flex: 1;
        }

        .tc-options-grid,
        .tc-bottom-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    let questionIndex = {{ $initialCount }};

    const questionList = document.getElementById('questionList');
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    const questionTemplate = document.getElementById('questionTemplate').innerHTML;
    const questionCount = document.getElementById('questionCount');

    function updateQuestionLabels() {
        const items = questionList.querySelectorAll('.tc-question-item');

        items.forEach((item, index) => {
            const title = item.querySelector('.tc-question-top h3');
            if (title) {
                title.textContent = 'Soal ' + (index + 1);
            }
        });

        if (questionCount) {
            questionCount.textContent = items.length;
        }
    }

    function removeQuestion(button) {
        const items = questionList.querySelectorAll('.tc-question-item');

        if (items.length <= 5) {
            alert('Minimal harus tersedia 5 form soal.');
            return;
        }

        button.closest('.tc-question-item').remove();
        updateQuestionLabels();
    }

    addQuestionBtn.addEventListener('click', function () {
        const number = questionList.querySelectorAll('.tc-question-item').length + 1;

        let html = questionTemplate
            .replaceAll('__INDEX__', questionIndex)
            .replaceAll('__NUMBER__', number);

        questionList.insertAdjacentHTML('beforeend', html);

        questionIndex++;
        updateQuestionLabels();
    });

    document.getElementById('tryoutForm').addEventListener('submit', function (event) {
        const items = questionList.querySelectorAll('.tc-question-item');
        let filledQuestions = 0;

        items.forEach((item) => {
            const questionText = item.querySelector('textarea[name*="[question]"]');
            const questionImage = item.querySelector('input[name*="[q_img]"]');

            const hasText = questionText && questionText.value.trim() !== '';
            const hasImage = questionImage && questionImage.files.length > 0;

            if (hasText || hasImage) {
                filledQuestions++;
            }
        });

        if (filledQuestions < 5) {
            event.preventDefault();
            alert('Anda wajib mengisi minimal 5 soal. Pertanyaan bisa berupa teks atau gambar.');
        }
    });
</script>
@endsection