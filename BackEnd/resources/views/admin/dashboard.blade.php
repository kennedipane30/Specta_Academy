@extends('layouts.spekta')

@section('title', 'Dashboard Administrator')
@section('subtitle', 'Sistem Manajemen Terpadu Spekta Academy')

@section('content')
<div class="spekta-dashboard">

    <section class="welcome-card">
        <div>
            <h1>Selamat datang kembali, {{ Auth::user()->name }}! 👋</h1>
            <p>Kelola akademi dengan mudah dan pantau aktivitas secara real-time.</p>
        </div>

        <div class="welcome-date">
            <div class="date-icon">
                <i class="fa-regular fa-calendar"></i>
            </div>
            <div>
                <strong>{{ now()->translatedFormat('l, d F Y') }}</strong>
                <span>{{ now()->format('H:i') }} WIB</span>
            </div>
        </div>
    </section>

    <section class="stats-grid">
        @foreach($stat_cards as $card)
            <a href="{{ $card['route'] }}" class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid {{ $card['icon'] }}"></i>
                </div>

                <p>{{ $card['title'] }}</p>
                <h2>{{ number_format($card['value']) }}</h2>

                <div class="stat-meta">
                    <span class="badge badge-{{ $card['badge_type'] }}">
                        {{ $card['badge'] }}
                    </span>

                    @if(!empty($card['badge_text']))
                        <small>{{ $card['badge_text'] }}</small>
                    @endif
                </div>
            </a>
        @endforeach
    </section>

    <section class="analytics-row">
        <div class="panel analytics-panel">
            <div class="panel-header">
                <h3>Analitik Pertumbuhan Siswa</h3>

                <div class="filter-pills">
                    <button type="button">7 Hari</button>
                    <button type="button" class="active">30 Hari</button>
                    <button type="button">90 Hari</button>
                    <button type="button">
                        <i class="fa-solid fa-ellipsis"></i>
                    </button>
                </div>
            </div>

            <div class="line-chart-wrap">
                <canvas id="studentGrowthChart"></canvas>
            </div>
        </div>
    </section>

    <section class="summary-grid">
        <div class="panel distribution-panel">
            <div class="panel-header">
                <h3>Distribusi Aktivitas</h3>
            </div>

            <div class="distribution-content">
                <div class="donut-wrap">
                    <canvas id="activityDistributionChart"></canvas>

                    <div class="donut-center">
                        <span>Total Aktivitas</span>
                        <strong>{{ number_format($total_distribusi) }}</strong>
                    </div>
                </div>

                <div class="distribution-list">
                    @foreach($distribusi_aktivitas as $index => $item)
                        @php
                            $percentage = $total_distribusi > 0
                                ? round(($item['value'] / $total_distribusi) * 100)
                                : 0;
                        @endphp

                        <div>
                            <span>
                                <i class="dot dot-{{ $index }}"></i>
                                {{ $item['label'] }}
                            </span>
                            <strong>{{ $percentage }}%</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="panel tasks-panel">
            <div class="panel-header">
                <h3>
                    Tugas Menunggu
                    <span class="count-pill">{{ $total_tugas_menunggu }}</span>
                </h3>

                <a href="{{ route('admin.siswa.pendaftaran') }}">Lihat semua</a>
            </div>

            <div class="task-list">
                @foreach($tugas_menunggu as $task)
                    <a href="{{ $task['route'] }}" class="task-item">
                        <div class="task-icon">
                            <i class="fa-solid {{ $task['icon'] }}"></i>
                        </div>

                        <div class="task-text">
                            <strong>{{ $task['title'] }}</strong>
                            <span>{{ $task['subtitle'] }}</span>
                        </div>

                        <div class="task-count">{{ $task['count'] }}</div>

                        <i class="fa-solid fa-chevron-right task-arrow"></i>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bottom-grid">
        <div class="panel log-panel">
            <div class="panel-header">
                <h3>Log Aktivitas Sistem</h3>
                <a href="#">Lihat semua</a>
            </div>

            <div class="activity-list">
                @forelse($log_aktivitas as $log)
                    <div class="activity-item">
                        <div class="activity-avatar avatar-{{ $log['type'] }}">
                            {{ $log['initial'] }}
                        </div>

                        <div class="activity-text">
                            <strong>{{ $log['title'] }}</strong>
                            <span>{{ $log['description'] }}</span>
                        </div>

                        <div class="activity-right">
                            <span class="status status-{{ $log['type'] }}">
                                {{ $log['status'] }}
                            </span>
                            <small>{{ $log['time']->diffForHumans() }}</small>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        Belum ada aktivitas terbaru.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="panel quick-panel">
            <div class="panel-header">
                <h3>Kontrol Cepat</h3>
            </div>

            <div class="quick-grid">
                <a href="{{ route('admin.siswa.index') }}" class="quick-action">
                    <div>
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <span>Tambah Siswa</span>
                </a>

                <a href="{{ route('admin.assignments.index') }}" class="quick-action">
                    <div>
                        <i class="fa-solid fa-upload"></i>
                    </div>
                    <span>Upload Materi</span>
                </a>

                <a href="{{ route('admin.tryout.index') }}" class="quick-action">
                    <div>
                        <i class="fa-solid fa-clipboard-list"></i>
                    </div>
                    <span>Buat Tryout</span>
                </a>

                <a href="{{ route('admin.announcement.create') }}" class="quick-action">
                    <div>
                        <i class="fa-solid fa-bullhorn"></i>
                    </div>
                    <span>Tambah Pengumuman</span>
                </a>
            </div>
        </div>

        <div class="panel promo-panel">
            <div class="panel-header">
                <h3>Informasi & Promosi</h3>
                <a href="{{ route('admin.promo.index') }}">Lihat semua</a>
            </div>

            <div class="promo-list">
                @forelse($informasi_promosi as $info)
                    <div class="promo-item">
                        <div class="promo-thumb">
                            @if(!empty($info['image']))
                                @php
                                    $image = $info['image'];

                                    if (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])) {
                                        $imageUrl = $image;
                                    } elseif (\Illuminate\Support\Str::startsWith($image, ['storage/'])) {
                                        $imageUrl = asset($image);
                                    } else {
                                        $imageUrl = asset('storage/' . ltrim($image, '/'));
                                    }
                                @endphp

                                <img src="{{ $imageUrl }}" alt="{{ $info['title'] }}">
                            @else
                                <i class="fa-solid fa-bullhorn"></i>
                            @endif
                        </div>

                        <div class="promo-text">
                            <strong>{{ $info['title'] }}</strong>
                            <span>{{ \Illuminate\Support\Str::limit($info['description'], 48) }}</span>

                            <div>
                                <em>{{ $info['status'] }}</em>
                                <small>{{ $info['date'] }}</small>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        Belum ada promo, banner, atau pengumuman aktif.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const chartLabels = @json($chart_labels);
    const siswaBaruData = @json($chart_siswa_baru);
    const aktivitasHarianData = @json($chart_aktivitas_harian);
    const totalSiswaData = @json($chart_total_siswa);

    const distribusiLabels = @json(collect($distribusi_aktivitas)->pluck('label'));
    const distribusiValues = @json(collect($distribusi_aktivitas)->pluck('value'));

    const lineCtx = document.getElementById('studentGrowthChart');

    if (lineCtx) {
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Siswa Baru',
                        data: siswaBaruData,
                        borderColor: '#d90429',
                        backgroundColor: 'rgba(217, 4, 41, 0.08)',
                        tension: 0.35,
                        borderWidth: 3,
                        pointRadius: 2,
                        fill: false,
                    },
                    {
                        label: 'Aktif Harian',
                        data: aktivitasHarianData,
                        borderColor: '#111827',
                        backgroundColor: 'rgba(17, 24, 39, 0.08)',
                        tension: 0.35,
                        borderWidth: 3,
                        pointRadius: 2,
                        borderDash: [6, 5],
                        fill: false,
                    },
                    {
                        label: 'Total Siswa',
                        data: totalSiswaData,
                        borderColor: '#ff7b9c',
                        backgroundColor: 'rgba(255, 123, 156, 0.08)',
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: 0,
                        borderDash: [8, 6],
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'start',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            color: '#6b7280',
                            font: {
                                size: 11,
                                weight: '600'
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280',
                            maxTicksLimit: 8,
                            font: {
                                size: 10,
                                weight: '600'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#eef2f7'
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 10,
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });
    }

    const donutCtx = document.getElementById('activityDistributionChart');

    if (donutCtx) {
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: distribusiLabels,
                datasets: [
                    {
                        data: distribusiValues,
                        backgroundColor: ['#d90429', '#111827', '#ff8fab', '#d1d5db'],
                        borderWidth: 0,
                        cutout: '68%'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
</script>

<style>
    :root {
        --spekta-red: #d90429;
        --spekta-dark-red: #7a0019;
        --spekta-maroon: #3b1024;
        --spekta-navy: #111827;
        --soft-bg: #f8fafc;
        --text-main: #111827;
        --text-muted: #6b7280;
        --border-soft: #edf0f4;
    }

    .spekta-dashboard {
        width: 100%;
    }

    .welcome-card {
        background: linear-gradient(120deg, #c90025 0%, #7b001b 48%, #351225 100%);
        color: white;
        border-radius: 22px;
        padding: 28px 34px;
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        box-shadow: 0 18px 35px rgba(134, 0, 24, 0.22);
        overflow: hidden;
        position: relative;
    }

    .welcome-card::after {
        content: "";
        position: absolute;
        width: 240px;
        height: 240px;
        right: -80px;
        top: -110px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 999px;
    }

    .welcome-card h1 {
        font-size: 22px;
        font-weight: 900;
        margin: 0 0 8px;
        letter-spacing: -0.02em;
    }

    .welcome-card p {
        margin: 0;
        font-size: 13px;
        opacity: 0.92;
        font-weight: 500;
    }

    .welcome-date {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 16px;
        border-left: 1px solid rgba(255, 255, 255, 0.24);
        padding-left: 34px;
        min-width: 260px;
    }

    .date-icon {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border: 1px solid rgba(255,255,255,0.5);
        border-radius: 12px;
        font-size: 22px;
    }

    .welcome-date strong,
    .welcome-date span {
        display: block;
    }

    .welcome-date strong {
        font-size: 13px;
        font-weight: 900;
    }

    .welcome-date span {
        font-size: 12px;
        opacity: 0.82;
        margin-top: 2px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }

    .stat-card {
        background: white;
        border: 1px solid var(--border-soft);
        border-radius: 20px;
        padding: 22px;
        min-height: 160px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.05);
        transition: 0.25s ease;
        color: inherit;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.09);
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        background: #ffe8ee;
        color: var(--spekta-red);
        border-radius: 15px;
        display: grid;
        place-items: center;
        font-size: 18px;
        margin-bottom: 18px;
    }

    .stat-card p {
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #6b7280;
        margin: 0 0 8px;
    }

    .stat-card h2 {
        font-size: 29px;
        line-height: 1;
        font-weight: 900;
        color: var(--text-main);
        margin: 0 0 12px;
    }

    .stat-meta {
        display: flex;
        align-items: center;
        gap: 7px;
        flex-wrap: wrap;
    }

    .badge {
        font-size: 10px;
        font-weight: 900;
        border-radius: 8px;
        padding: 5px 8px;
    }

    .badge-success {
        background: #dcfce7;
        color: #16a34a;
    }

    .badge-danger {
        background: #fee2e2;
        color: #dc2626;
    }

    .badge-info {
        background: #dbeafe;
        color: #2563eb;
    }

    .badge-warning {
        background: #ffedd5;
        color: #ea580c;
    }

    .stat-meta small {
        color: #6b7280;
        font-size: 11px;
        font-weight: 600;
    }

    .analytics-row {
        margin-bottom: 22px;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: 1fr 1.25fr;
        gap: 22px;
        margin-bottom: 22px;
    }

    .bottom-grid {
        display: grid;
        grid-template-columns: 1.45fr 0.75fr 1fr;
        gap: 22px;
    }

    .panel {
        background: white;
        border: 1px solid var(--border-soft);
        border-radius: 22px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.05);
        padding: 22px;
    }

    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .panel-header h3 {
        font-size: 16px;
        font-weight: 900;
        color: var(--text-main);
        margin: 0;
    }

    .panel-header a {
        color: var(--spekta-red);
        font-size: 11px;
        font-weight: 900;
    }

    .filter-pills {
        display: flex;
        gap: 8px;
    }

    .filter-pills button {
        border: 1px solid var(--border-soft);
        background: #fff;
        color: #6b7280;
        font-size: 11px;
        font-weight: 800;
        padding: 8px 14px;
        border-radius: 9px;
        cursor: pointer;
    }

    .filter-pills button.active {
        background: var(--spekta-red);
        color: #fff;
        border-color: var(--spekta-red);
    }

    .line-chart-wrap {
        height: 310px;
        border-top: 1px solid var(--border-soft);
        padding-top: 16px;
        position: relative;
    }

    .distribution-panel,
    .tasks-panel {
        min-height: 280px;
    }

    .distribution-content {
        display: grid;
        grid-template-columns: 210px 1fr;
        align-items: center;
        gap: 26px;
    }

    .donut-wrap {
        height: 185px;
        position: relative;
    }

    .donut-center {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        text-align: center;
        pointer-events: none;
    }

    .donut-center span {
        display: block;
        color: #6b7280;
        font-size: 10px;
        font-weight: 700;
    }

    .donut-center strong {
        display: block;
        color: var(--text-main);
        font-size: 22px;
        font-weight: 900;
    }

    .distribution-list {
        display: grid;
        gap: 13px;
    }

    .distribution-list div {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        color: #4b5563;
        font-weight: 700;
    }

    .distribution-list strong {
        color: var(--text-main);
    }

    .dot {
        width: 9px;
        height: 9px;
        border-radius: 99px;
        display: inline-block;
        margin-right: 8px;
    }

    .dot-0 {
        background: #d90429;
    }

    .dot-1 {
        background: #111827;
    }

    .dot-2 {
        background: #ff8fab;
    }

    .dot-3 {
        background: #d1d5db;
    }

    .count-pill {
        display: inline-grid;
        place-items: center;
        min-width: 27px;
        height: 27px;
        border-radius: 999px;
        background: #ffe8ee;
        color: var(--spekta-red);
        font-size: 11px;
        margin-left: 8px;
    }

    .task-list,
    .activity-list,
    .promo-list {
        display: grid;
        gap: 14px;
    }

    .task-item {
        display: grid;
        grid-template-columns: 38px 1fr 30px 12px;
        align-items: center;
        gap: 12px;
        color: inherit;
        padding: 6px 0;
    }

    .task-icon {
        width: 38px;
        height: 38px;
        display: grid;
        place-items: center;
        background: #ffe8ee;
        color: var(--spekta-red);
        border-radius: 12px;
    }

    .task-text strong,
    .activity-text strong,
    .promo-text strong {
        display: block;
        font-size: 13px;
        font-weight: 900;
        color: var(--text-main);
        margin-bottom: 3px;
    }

    .task-text span,
    .activity-text span,
    .promo-text span {
        display: block;
        font-size: 11px;
        color: #6b7280;
        font-weight: 600;
        line-height: 1.35;
    }

    .task-count {
        width: 28px;
        height: 28px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: #ffe8ee;
        color: var(--spekta-red);
        font-size: 12px;
        font-weight: 900;
    }

    .task-arrow {
        color: #64748b;
        font-size: 11px;
    }

    .activity-item {
        display: grid;
        grid-template-columns: 42px 1fr auto;
        align-items: center;
        gap: 14px;
        padding-bottom: 13px;
        border-bottom: 1px solid var(--border-soft);
    }

    .activity-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .activity-avatar {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
    }

    .avatar-success {
        background: #dcfce7;
        color: #16a34a;
    }

    .avatar-warning {
        background: #ffedd5;
        color: #ea580c;
    }

    .avatar-info {
        background: #dbeafe;
        color: #2563eb;
    }

    .activity-right {
        text-align: right;
    }

    .status {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 8px;
        font-size: 9px;
        font-weight: 900;
        margin-bottom: 7px;
    }

    .status-success {
        background: #dcfce7;
        color: #16a34a;
    }

    .status-warning {
        background: #ffedd5;
        color: #ea580c;
    }

    .status-info {
        background: #dbeafe;
        color: #2563eb;
    }

    .activity-right small {
        display: block;
        font-size: 10px;
        color: #6b7280;
        font-weight: 600;
    }

    .quick-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .quick-action {
        min-height: 98px;
        border: 1px solid var(--border-soft);
        border-radius: 14px;
        display: grid;
        place-items: center;
        text-align: center;
        padding: 14px;
        color: inherit;
        transition: 0.2s ease;
    }

    .quick-action:hover {
        border-color: #fecdd3;
        background: #fff5f7;
        transform: translateY(-2px);
    }

    .quick-action div {
        width: 46px;
        height: 46px;
        display: grid;
        place-items: center;
        background: #ffe8ee;
        color: var(--spekta-red);
        border-radius: 999px;
        margin-bottom: 9px;
        font-size: 18px;
    }

    .quick-action span {
        font-size: 11px;
        font-weight: 900;
        color: var(--text-main);
    }

    .promo-item {
        display: grid;
        grid-template-columns: 64px 1fr;
        gap: 14px;
        padding-bottom: 13px;
        border-bottom: 1px solid var(--border-soft);
    }

    .promo-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .promo-thumb {
        width: 64px;
        height: 58px;
        border-radius: 10px;
        background: linear-gradient(135deg, #d90429, #3b1024);
        color: #fff;
        overflow: hidden;
        display: grid;
        place-items: center;
    }

    .promo-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .promo-text div {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 7px;
    }

    .promo-text em {
        font-style: normal;
        background: #dcfce7;
        color: #16a34a;
        font-size: 9px;
        font-weight: 900;
        text-transform: uppercase;
        border-radius: 7px;
        padding: 4px 7px;
    }

    .promo-text small {
        font-size: 10px;
        color: #6b7280;
        font-weight: 700;
    }

    .empty-state {
        padding: 22px;
        text-align: center;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
        background: #f8fafc;
        border-radius: 14px;
    }

    @media (max-width: 1536px) {
        .stats-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 1280px) {
        .summary-grid,
        .bottom-grid {
            grid-template-columns: 1fr;
        }

        .distribution-content {
            grid-template-columns: 1fr;
        }

        .donut-wrap {
            height: 185px;
        }
    }

    @media (max-width: 768px) {
        .welcome-card {
            flex-direction: column;
            align-items: flex-start;
        }

        .welcome-date {
            border-left: none;
            padding-left: 0;
            min-width: unset;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .filter-pills {
            display: none;
        }

        .line-chart-wrap {
            height: 260px;
        }

        .activity-item {
            grid-template-columns: 42px 1fr;
        }

        .activity-right {
            grid-column: 2 / 3;
            text-align: left;
        }
    }
</style>
@endsection