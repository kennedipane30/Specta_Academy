@extends('layouts.spekta')

@section('title', 'Manajemen Penugasan Materi')
@section('subtitle', 'Atur relasi pengajar, program kelas, dan mata pelajaran')

@section('content')
@php
    $assignmentCollection = collect($assignments);
    $subjectCollection = collect($subjects);
    $classCollection = collect($classes);

    $totalAssignments = $assignmentCollection->count();
    $assignedTeachers = $assignmentCollection->pluck('user_id')->filter()->unique()->count();
    $assignedClasses = $assignmentCollection->pluck('class_id')->filter()->unique()->count();
    $coveredSubjects = $assignmentCollection->pluck('subject_name')->filter()->unique()->count();

    $totalSlots = max($classCollection->count() * $subjectCollection->count(), 1);
    $coveragePercent = min(round(($totalAssignments / $totalSlots) * 100), 100);

    $subjectCoverage = $subjectCollection->map(function ($subject) use ($assignmentCollection) {
        return [
            'name' => $subject,
            'total' => $assignmentCollection->where('subject_name', $subject)->count(),
        ];
    })->sortByDesc('total')->values();

    $latestAssignments = $assignmentCollection->sortByDesc('created_at')->take(5);
    $subjectGridCount = max($subjectCollection->count(), 1);
@endphp

<div class="am-page">

    {{-- HERO --}}
    <section class="am-hero">
        <div class="am-hero-content">
            <div class="am-hero-kicker">
                <i class="fa-solid fa-layer-group"></i>
                <span>Learning Assignment Center</span>
            </div>

            <h1>Penugasan Materi</h1>

            <p>
                Atur pengajar untuk setiap program kelas dan mata pelajaran agar pembelajaran lebih terstruktur.
            </p>

            <div class="am-hero-tags">
                <span>
                    <i class="fa-solid fa-user-check"></i>
                    {{ number_format($assignedTeachers) }} Pengajar
                </span>

                <span>
                    <i class="fa-solid fa-book-open-reader"></i>
                    {{ number_format($coveredSubjects) }} Mapel
                </span>

                <span>
                    <i class="fa-solid fa-layer-group"></i>
                    {{ number_format($assignedClasses) }} Program
                </span>
            </div>
        </div>

        <div class="am-coverage-card">
            <div class="am-meter-circle" style="--progress: {{ $coveragePercent }}%;">
                <div>
                    <strong>{{ $coveragePercent }}%</strong>
                    <span>Coverage</span>
                </div>
            </div>

            <div class="am-coverage-info">
                <strong>{{ $totalAssignments }} / {{ $totalSlots }}</strong>
                <span>slot penugasan terisi</span>
            </div>
        </div>
    </section>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="am-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <div>
                <strong>Berhasil!</strong>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="am-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Gagal!</strong>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="am-alert error">
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

    {{-- SUMMARY STRIP --}}
    <section class="am-strip">
        <div>
            <span>Total Penugasan</span>
            <strong>{{ number_format($totalAssignments) }}</strong>
        </div>

        <div>
            <span>Pengajar Terlibat</span>
            <strong>{{ number_format($assignedTeachers) }}</strong>
        </div>

        <div>
            <span>Program Terisi</span>
            <strong>{{ number_format($assignedClasses) }}</strong>
        </div>

        <div>
            <span>Mapel Tercover</span>
            <strong>{{ number_format($coveredSubjects) }}</strong>
        </div>
    </section>

    {{-- MAIN LAYOUT --}}
    <section class="am-main-grid">

        {{-- FORM CONSOLE --}}
        <div class="am-console">
            <div class="am-section-title">
                <div>
                    <span>Assignment Console</span>
                    <h2>Tambah Penugasan Pengajar</h2>
                    <p>Tentukan pengajar, program kelas, dan mata pelajaran yang akan diampu.</p>
                </div>

                <div class="am-title-icon">
                    <i class="fa-solid fa-user-check"></i>
                </div>
            </div>

            <form action="{{ route('admin.assignments.store') }}" method="POST" class="am-form">
                @csrf

                <div class="am-field">
                    <label>Pilih Pengajar</label>
                    <div>
                        <i class="fa-solid fa-chalkboard-user"></i>
                        <select name="teacher_id" required>
                            <option value="">Pilih Pengajar</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->usersID }}" {{ old('teacher_id') == $teacher->usersID ? 'selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="am-field">
                    <label>Pilih Program Kelas</label>
                    <div>
                        <i class="fa-solid fa-layer-group"></i>
                        <select name="class_id" required>
                            <option value="">Pilih Kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->class_id }}" {{ old('class_id') == $class->class_id ? 'selected' : '' }}>
                                    {{ $class->program_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="am-field">
                    <label>Mata Pelajaran</label>
                    <div>
                        <i class="fa-solid fa-book-open-reader"></i>
                        <select name="subject_name" required>
                            <option value="">Pilih Subjek</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject }}" {{ old('subject_name') == $subject ? 'selected' : '' }}>
                                    {{ $subject }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit" class="am-submit">
                    <i class="fa-solid fa-paper-plane"></i>
                    Tugaskan Sekarang
                </button>
            </form>
        </div>

        {{-- SUBJECT COVERAGE --}}
        <aside class="am-coverage">
            <div class="am-section-title compact">
                <div>
                    <span>Subject Coverage</span>
                    <h2>Pemetaan Mapel</h2>
                </div>
            </div>

            <div class="am-coverage-list">
                @forelse($subjectCoverage as $subject)
                    @php
                        $maxSubject = max($subjectCoverage->max('total'), 1);
                        $barWidth = round(($subject['total'] / $maxSubject) * 100);
                    @endphp

                    <div class="am-coverage-row">
                        <div class="am-coverage-head">
                            <span>{{ $subject['name'] }}</span>
                            <strong>{{ $subject['total'] }}</strong>
                        </div>
                        <div class="am-bar">
                            <em style="width: {{ $barWidth }}%"></em>
                        </div>
                    </div>
                @empty
                    <div class="am-empty-small">
                        Belum ada data mata pelajaran.
                    </div>
                @endforelse
            </div>
        </aside>

    </section>

    {{-- MATRIX SECTION --}}
    <section class="am-matrix-panel">
        <div class="am-section-title">
            <div>
                <span>Assignment Matrix</span>
                <h2>Peta Penugasan Program & Mata Pelajaran</h2>
                <p>Lihat slot pengajar per program dan mata pelajaran secara cepat.</p>
            </div>
        </div>

        <div class="am-matrix-scroll">
            <div class="am-matrix">
                <div class="am-matrix-head">
                    <div class="am-class-col">Program Kelas</div>

                    @foreach($subjects as $subject)
                        <div>{{ $subject }}</div>
                    @endforeach
                </div>

                @forelse($classes as $class)
                    <div class="am-matrix-row">
                        <div class="am-class-col">
                            <strong>{{ $class->program_name }}</strong>
                            <span>ID: {{ $class->class_id }}</span>
                        </div>

                        @foreach($subjects as $subject)
                            @php
                                $slot = $assignmentCollection
                                    ->where('class_id', $class->class_id)
                                    ->firstWhere('subject_name', $subject);
                            @endphp

                            <div class="am-slot {{ $slot ? 'filled' : 'empty' }}">
                                @if($slot)
                                    <i class="fa-solid fa-check"></i>
                                    <span>{{ $slot->user->name ?? 'Pengajar' }}</span>
                                @else
                                    <i class="fa-solid fa-minus"></i>
                                    <span>Kosong</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div class="am-empty">
                        <i class="fa-solid fa-layer-group"></i>
                        <strong>Belum ada program kelas.</strong>
                        <span>Tambahkan program kelas terlebih dahulu.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- DETAIL LIST --}}
    <section class="am-detail-grid">

        <div class="am-table-panel">
            <div class="am-section-title">
                <div>
                    <span>Assignment Records</span>
                    <h2>Daftar Penugasan Aktif</h2>
                    <p>Semua relasi pengajar, kelas, dan mata pelajaran yang sedang digunakan.</p>
                </div>
            </div>

            <div class="am-table-wrap">
                <table class="am-table">
                    <thead>
                        <tr>
                            <th>Pengajar</th>
                            <th>Program Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($assignmentCollection->sortByDesc('created_at') as $assign)
                            <tr>
                                <td>
                                    <div class="am-teacher">
                                        <div class="am-teacher-avatar">
                                            {{ strtoupper(substr($assign->user->name ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $assign->user->name ?? 'N/A' }}</strong>
                                            <span>NIP: {{ str_pad($assign->user_id ?? 0, 6, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="am-class-badge">
                                        {{ $assign->classModel->program_name ?? 'N/A' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="am-subject-badge">
                                        {{ $assign->subject_name }}
                                    </span>
                                </td>

                                <td>
                                    {{ $assign->created_at ? $assign->created_at->translatedFormat('d M Y') : '-' }}
                                </td>

                                <td>
                                    <form action="{{ route('admin.assignments.destroy', $assign->id) }}" method="POST" onsubmit="return confirm('Hapus penugasan ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="am-delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="am-empty">
                                        <i class="fa-solid fa-user-slash"></i>
                                        <strong>Belum ada penugasan pengajar.</strong>
                                        <span>Gunakan form di atas untuk membuat penugasan pertama.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TIMELINE --}}
        <aside class="am-timeline">
            <div class="am-section-title compact">
                <div>
                    <span>Recent Activity</span>
                    <h2>Aktivitas Terbaru</h2>
                </div>
            </div>

            <div class="am-timeline-list">
                @forelse($latestAssignments as $item)
                    <div class="am-timeline-item">
                        <i></i>
                        <div>
                            <strong>{{ $item->user->name ?? 'Pengajar' }}</strong>
                            <span>
                                Ditugaskan pada {{ $item->subject_name }} untuk {{ $item->classModel->program_name ?? 'kelas' }}.
                            </span>
                            <small>{{ $item->created_at ? $item->created_at->diffForHumans() : '-' }}</small>
                        </div>
                    </div>
                @empty
                    <div class="am-empty-small">
                        Belum ada aktivitas penugasan.
                    </div>
                @endforelse
            </div>
        </aside>

    </section>
</div>

<style>
    .am-page {
        width: 100%;
    }

    .am-hero {
        position: relative;
        overflow: hidden;
        background:
            linear-gradient(120deg, #cf002b 0%, #85001d 48%, #241827 100%);
        color: #fff;
        border-radius: 24px;
        padding: 34px 36px;
        margin-bottom: 22px;
        display: grid;
        grid-template-columns: minmax(0, 1fr) 260px;
        align-items: center;
        gap: 28px;
        box-shadow: 0 18px 38px rgba(134, 0, 24, .22);
    }

    .am-hero::before {
        content: "";
        position: absolute;
        width: 340px;
        height: 340px;
        right: -130px;
        top: -155px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .10);
    }

    .am-hero::after {
        content: "";
        position: absolute;
        width: 230px;
        height: 230px;
        right: 80px;
        bottom: -150px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .07);
    }

    .am-hero-content {
        position: relative;
        z-index: 2;
    }

    .am-hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        min-height: 28px;
        padding: 0 11px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .16);
        margin-bottom: 16px;
    }

    .am-hero-kicker i {
        font-size: 11px;
        color: #fff;
    }

    .am-hero-kicker span {
        display: block;
        margin: 0;
        color: rgba(255, 255, 255, .88);
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .am-hero-content h1 {
        margin: 0 0 10px;
        color: #fff;
        font-size: 34px;
        font-weight: 900;
        line-height: 1.05;
        text-transform: uppercase;
        letter-spacing: -0.045em;
    }

    .am-hero-content p {
        margin: 0;
        max-width: 760px;
        color: rgba(255, 255, 255, .88);
        font-size: 14px;
        font-weight: 600;
        line-height: 1.65;
    }

    .am-hero-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 22px;
    }

    .am-hero-tags span {
        min-height: 34px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0 13px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .14);
        color: #fff;
        font-size: 11px;
        font-weight: 900;
    }

    .am-hero-tags i {
        font-size: 11px;
    }

    .am-coverage-card {
        position: relative;
        z-index: 2;
        justify-self: end;
        width: 230px;
        min-height: 190px;
        border-radius: 24px;
        padding: 22px;
        background: rgba(255, 255, 255, .14);
        border: 1px solid rgba(255, 255, 255, .18);
        backdrop-filter: blur(14px);
        display: grid;
        place-items: center;
        text-align: center;
    }

    .am-meter-circle {
        width: 118px;
        height: 118px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        background:
            conic-gradient(#ffffff var(--progress), rgba(255,255,255,.25) 0);
        padding: 10px;
    }

    .am-meter-circle > div {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        display: grid;
        place-items: center;
        align-content: center;
        background: #fff;
        color: #111827;
    }

    .am-meter-circle strong {
        display: block;
        color: #0f172a;
        font-size: 28px;
        font-weight: 900;
        line-height: 1;
        letter-spacing: -0.05em;
    }

    .am-meter-circle span {
        display: block;
        margin-top: 6px;
        color: #6b7280;
        font-size: 9px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .am-coverage-info {
        margin-top: 14px;
    }

    .am-coverage-info strong {
        display: block;
        color: #fff;
        font-size: 16px;
        font-weight: 900;
    }

    .am-coverage-info span {
        display: block;
        margin-top: 4px;
        color: rgba(255, 255, 255, .78);
        font-size: 11px;
        font-weight: 700;
    }

    .am-alert {
        border-radius: 16px;
        padding: 15px 17px;
        margin-bottom: 18px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 800;
    }

    .am-alert.success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .am-alert.error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .am-alert strong {
        display: block;
        margin-bottom: 3px;
    }

    .am-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .am-strip {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        background: #fff;
        border: 1px solid #edf0f4;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .05);
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 22px;
    }

    .am-strip div {
        padding: 20px 22px;
        border-right: 1px solid #edf0f4;
    }

    .am-strip div:last-child {
        border-right: none;
    }

    .am-strip span {
        display: block;
        color: #6b7280;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-bottom: 8px;
    }

    .am-strip strong {
        color: #111827;
        font-size: 27px;
        font-weight: 900;
        letter-spacing: -0.04em;
    }

    .am-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 22px;
        align-items: stretch;
        margin-bottom: 22px;
    }

    .am-console,
    .am-coverage,
    .am-matrix-panel,
    .am-table-panel,
    .am-timeline {
        background: #fff;
        border: 1px solid #edf0f4;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .05);
        border-radius: 22px;
        padding: 22px;
    }

    .am-section-title {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 22px;
    }

    .am-section-title.compact {
        margin-bottom: 18px;
    }

    .am-section-title span {
        display: block;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .18em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .am-section-title h2 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .am-section-title p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
    }

    .am-title-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        background: #ffe8ee;
        color: #d90429;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .am-form {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr)) 180px;
        gap: 14px;
        align-items: end;
    }

    .am-field label {
        display: block;
        color: #374151;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: 8px;
    }

    .am-field div {
        position: relative;
    }

    .am-field i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 13px;
    }

    .am-field select {
        width: 100%;
        height: 48px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
        padding: 0 15px 0 42px;
        color: #111827;
        font-size: 12px;
        font-weight: 800;
        outline: none;
        font-family: inherit;
    }

    .am-field select:focus {
        background: #fff;
        border-color: #fecdd3;
        box-shadow: 0 0 0 4px rgba(217, 4, 41, .08);
    }

    .am-submit {
        height: 48px;
        border: none;
        border-radius: 14px;
        background: #d90429;
        color: #fff;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 9px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        cursor: pointer;
        font-family: inherit;
        box-shadow: 0 14px 28px rgba(217, 4, 41, .20);
    }

    .am-coverage-list {
        display: grid;
        gap: 15px;
    }

    .am-coverage-head {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 7px;
        color: #374151;
        font-size: 12px;
        font-weight: 900;
    }

    .am-coverage-head strong {
        color: #d90429;
    }

    .am-bar {
        height: 9px;
        background: #f3f4f6;
        border-radius: 999px;
        overflow: hidden;
    }

    .am-bar em {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #d90429, #fb7185);
    }

    .am-matrix-panel {
        margin-bottom: 22px;
    }

    .am-matrix-scroll {
        overflow-x: auto;
        padding-bottom: 4px;
    }

    .am-matrix {
        min-width: 980px;
        display: grid;
        gap: 10px;
    }

    .am-matrix-head,
    .am-matrix-row {
        display: grid;
        grid-template-columns: 220px repeat({{ $subjectGridCount }}, minmax(150px, 1fr));
        gap: 10px;
    }

    .am-matrix-head div {
        min-height: 44px;
        border-radius: 14px;
        background: #111827;
        color: #fff;
        display: flex;
        align-items: center;
        padding: 0 14px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .am-class-col {
        position: sticky;
        left: 0;
        z-index: 2;
    }

    .am-matrix-row .am-class-col {
        background: #fff7f9;
        border: 1px solid #fecdd3;
        border-radius: 15px;
        padding: 13px 14px;
    }

    .am-matrix-row .am-class-col strong {
        display: block;
        color: #111827;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        line-height: 1.3;
    }

    .am-matrix-row .am-class-col span {
        display: block;
        color: #6b7280;
        font-size: 10px;
        font-weight: 800;
        margin-top: 4px;
    }

    .am-slot {
        min-height: 62px;
        border-radius: 15px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 5px;
        padding: 11px 12px;
        font-size: 11px;
        font-weight: 800;
    }

    .am-slot i {
        width: 22px;
        height: 22px;
        border-radius: 999px;
        display: grid;
        place-items: center;
        font-size: 9px;
    }

    .am-slot.filled {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    .am-slot.filled i {
        background: #16a34a;
        color: #fff;
    }

    .am-slot.empty {
        background: #f8fafc;
        border: 1px dashed #d1d5db;
        color: #9ca3af;
    }

    .am-slot.empty i {
        background: #e5e7eb;
        color: #6b7280;
    }

    .am-detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 22px;
        align-items: start;
    }

    .am-table-wrap {
        overflow-x: auto;
    }

    .am-table {
        width: 100%;
        border-collapse: collapse;
    }

    .am-table th {
        padding: 14px 12px;
        border-bottom: 1px solid #edf0f4;
        color: #6b7280;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .06em;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .am-table td {
        padding: 14px 12px;
        border-bottom: 1px solid #edf0f4;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        vertical-align: middle;
        white-space: nowrap;
    }

    .am-table tbody tr:hover {
        background: #fff7f9;
    }

    .am-teacher {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .am-teacher-avatar {
        width: 38px;
        height: 38px;
        border-radius: 999px;
        display: grid;
        place-items: center;
        background: #ffe8ee;
        color: #d90429;
        font-weight: 900;
    }

    .am-teacher strong {
        display: block;
        color: #111827;
        font-size: 12px;
        font-weight: 900;
    }

    .am-teacher span {
        display: block;
        color: #6b7280;
        font-size: 10px;
        font-weight: 700;
        margin-top: 2px;
    }

    .am-class-badge,
    .am-subject-badge {
        display: inline-flex;
        height: 28px;
        align-items: center;
        padding: 0 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .am-class-badge {
        background: #fff1f2;
        color: #d90429;
    }

    .am-subject-badge {
        background: #dbeafe;
        color: #2563eb;
    }

    .am-delete {
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 11px;
        background: #fee2e2;
        color: #dc2626;
        cursor: pointer;
    }

    .am-timeline-list {
        position: relative;
        display: grid;
        gap: 0;
    }

    .am-timeline-list::before {
        content: "";
        position: absolute;
        left: 8px;
        top: 8px;
        bottom: 8px;
        width: 2px;
        background: #fee2e2;
    }

    .am-timeline-item {
        position: relative;
        display: grid;
        grid-template-columns: 20px 1fr;
        gap: 12px;
        padding-bottom: 18px;
    }

    .am-timeline-item > i {
        position: relative;
        z-index: 1;
        width: 18px;
        height: 18px;
        border-radius: 999px;
        background: #d90429;
        border: 4px solid #ffe8ee;
        margin-top: 2px;
    }

    .am-timeline-item strong {
        display: block;
        color: #111827;
        font-size: 12px;
        font-weight: 900;
    }

    .am-timeline-item span {
        display: block;
        color: #6b7280;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.45;
        margin-top: 4px;
    }

    .am-timeline-item small {
        display: block;
        color: #9ca3af;
        font-size: 10px;
        font-weight: 800;
        margin-top: 7px;
    }

    .am-empty,
    .am-empty-small {
        padding: 32px;
        text-align: center;
        background: #f8fafc;
        border-radius: 16px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .am-empty i {
        width: 56px;
        height: 56px;
        margin: 0 auto 14px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: #ffe8ee;
        color: #d90429;
        font-size: 22px;
    }

    .am-empty strong {
        display: block;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    @media (max-width: 1450px) {
        .am-main-grid,
        .am-detail-grid {
            grid-template-columns: 1fr;
        }

        .am-strip {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .am-form {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 1000px) {
        .am-hero {
            grid-template-columns: 1fr;
        }

        .am-coverage-card {
            justify-self: start;
            width: 100%;
            max-width: 260px;
        }
    }

    @media (max-width: 900px) {
        .am-hero {
            padding: 28px;
        }

        .am-hero-content h1 {
            font-size: 27px;
        }

        .am-strip {
            grid-template-columns: 1fr;
        }

        .am-strip div {
            border-right: none;
            border-bottom: 1px solid #edf0f4;
        }

        .am-strip div:last-child {
            border-bottom: none;
        }
    }
</style>
@endsection