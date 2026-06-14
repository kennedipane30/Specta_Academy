<?php $__env->startSection('title', 'Dashboard Administrator'); ?>
<?php $__env->startSection('subtitle', 'Sistem Manajemen Terpadu Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<?php
    // 1. FILTER KARTU STATISTIK UTAMA (Menghapus Kelas Aktif / Live Class)
    $filteredStats = collect($stat_cards)->filter(function($card) {
        $title = strtolower($card['title']);
        return !str_contains($title, 'kelas aktif') && !str_contains($title, 'live class');
    })->values();

    // 2. FILTER DATA DISTRIBUSI AKTIVITAS (Menghapus Kelas Aktif / Live Class)
    $filteredDistribusi = collect($distribusi_aktivitas)->filter(function($item) {
        $label = strtolower($item['label']);
        return !str_contains($label, 'kelas aktif') && !str_contains($label, 'live');
    })->values();

    // Hitung ulang total distribusi setelah dibersihkan dari "Kelas Aktif"
    $totalDistribusiFiltered = $filteredDistribusi->sum('value');


    // ------------------------------------------------------------------------
    // 3. PERHITUNGAN MATEMATIS DINAMIS DARI DATA GRAFIK SISTEM (TREN REAL-TIME)
    // ------------------------------------------------------------------------
    
    // A. Perhitungan Dinamis: Siswa Baru Terdaftar
    $siswaBaruCollection = collect($chart_siswa_baru);
    $totalSiswaBaru = $siswaBaruCollection->sum(); // Total pendaftaran baru di periode ini
    
    $halfPeriod = ceil($siswaBaruCollection->count() / 2);
    
    // Hitung persentase kenaikan/penurunan dibanding paruh pertama periode
    $firstHalfSiswaBaru = $siswaBaruCollection->take($halfPeriod)->sum();
    $secondHalfSiswaBaru = $siswaBaruCollection->slice($halfPeriod)->sum();
    $siswaBaruTrend = 0;
    if ($firstHalfSiswaBaru > 0) {
        $siswaBaruTrend = round((($secondHalfSiswaBaru - $firstHalfSiswaBaru) / $firstHalfSiswaBaru) * 100);
    } elseif ($secondHalfSiswaBaru > 0) {
        $siswaBaruTrend = 100; // Kenaikan 100% jika sebelumnya 0
    }

    // B. Perhitungan Dinamis: Rata-Rata Keaktifan Harian
    $aktifHarianCollection = collect($chart_aktivitas_harian);
    $avgAktifHarian = round($aktifHarianCollection->average() ?? 0); // Nilai rata-rata real-time
    
    $firstHalfAktif = $aktifHarianCollection->take($halfPeriod)->average() ?? 0;
    $secondHalfAktif = $aktifHarianCollection->slice($halfPeriod)->average() ?? 0;
    $aktifHarianTrend = 0;
    if ($firstHalfAktif > 0) {
        $aktifHarianTrend = round((($secondHalfAktif - $firstHalfAktif) / $firstHalfAktif) * 100);
    } elseif ($secondHalfAktif > 0) {
        $aktifHarianTrend = 100;
    }

    // C. Perhitungan Dinamis: Total Akumulasi Siswa
    $totalSiswaCollection = collect($chart_total_siswa);
    // Mengambil data terupdate (index terakhir) dari grafik total siswa
    $latestTotalSiswa = $totalSiswaCollection->last() ?? ($filteredStats->firstWhere('title', 'TOTAL SISWA')['value'] ?? 0);
    $firstTotalSiswa = $totalSiswaCollection->first() ?? 0;
    $totalSiswaTrend = 0;
    if ($firstTotalSiswa > 0) {
        $totalSiswaTrend = round((($latestTotalSiswa - $firstTotalSiswa) / $firstTotalSiswa) * 100);
    }
?>

<div class="spekta-dashboard">

    <!-- 1. BANNER SELAMAT DATANG -->
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Selamat datang kembali, <span><?php echo e(Auth::user()->name); ?>!</span> 👋</h1>
            <p>Kelola akademi dengan mudah dan pantau aktivitas secara real-time.</p>
        </div>

        <div class="welcome-date">
            <div class="date-icon">
                <i class="fa-regular fa-calendar"></i>
            </div>
            <div>
                <strong><?php echo e(now()->translatedFormat('l, d F Y')); ?></strong>
                <span><?php echo e(now()->format('H:i')); ?> WIB</span>
            </div>
        </div>
    </section>

    <!-- 2. KARTU STATISTIK UTAMA (5 KOLOM) -->
    <section class="stats-grid">
        <?php $__currentLoopData = $filteredStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $colorClass = $index % 2 === 0 ? 'color-teal' : 'color-red';
            ?>
            <a href="<?php echo e($card['route']); ?>" class="stat-card <?php echo e($colorClass); ?>">
                <div class="stat-icon-wrap">
                    <div class="stat-icon">
                        <i class="fa-solid <?php echo e($card['icon']); ?>"></i>
                    </div>
                </div>

                <div class="stat-info">
                    <p><?php echo e($card['title']); ?></p>
                    <h2><?php echo e(number_format($card['value'])); ?></h2>
                </div>

                <div class="stat-meta">
                    <span class="badge badge-<?php echo e($card['badge_type']); ?>">
                        <?php echo e($card['badge']); ?>

                    </span>

                    <?php if(!empty($card['badge_text'])): ?>
                        <small><?php echo e($card['badge_text']); ?></small>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>

    <!-- 3. GRAFIK PERTUMBUHAN SISWA (DATA KINI 100% DINAMIS DARI CONTROLLER) -->
    <section class="analytics-row">
        <div class="panel analytics-panel">
            <div class="analytics-header-row">
                <div class="panel-header-text">
                    <h3>Analitik Pertumbuhan & Aktivitas Siswa</h3>
                    <p class="panel-subtitle">Pantau performa pendaftaran dan keaktifan harian secara real-time</p>
                </div>

                <!-- Segmented Control Filter -->
                <div class="segmented-control" id="timeFilterGroup">
                    <button type="button" class="control-btn" data-days="7">7 Hari</button>
                    <button type="button" class="control-btn active" data-days="30">30 Hari</button>
                    <button type="button" class="control-btn" data-days="90">90 Hari</button>
                </div>
            </div>

            <!-- LEGENDA RINGKASAN DATA: KINI DINAMIS DAN INTERAKTIF SENSITIF DATA -->
            <div class="chart-summary-row">
                <!-- A. Card Keaktifan Harian Dinamis -->
                <div class="summary-indicator-card teal-accent">
                    <div class="indicator-header">
                        <span class="dot-indicator dot-teal"></span>
                        <span class="indicator-title">Keaktifan Harian (Rata-Rata)</span>
                    </div>
                    <div class="indicator-value">
                        <?php echo e(number_format($avgAktifHarian)); ?>

                        <?php if($aktifHarianTrend > 0): ?>
                            <span class="indicator-trend up"><i class="fa-solid fa-arrow-up"></i> +<?php echo e($aktifHarianTrend); ?>%</span>
                        <?php elseif($aktifHarianTrend < 0): ?>
                            <span class="indicator-trend down"><i class="fa-solid fa-arrow-down"></i> <?php echo e($aktifHarianTrend); ?>%</span>
                        <?php else: ?>
                            <span class="indicator-trend neutral"><i class="fa-solid fa-minus"></i> 0%</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- B. Card Siswa Baru Terdaftar Dinamis -->
                <div class="summary-indicator-card red-accent">
                    <div class="indicator-header">
                        <span class="dot-indicator dot-red"></span>
                        <span class="indicator-title">Siswa Baru Terdaftar (Total)</span>
                    </div>
                    <div class="indicator-value">
                        +<?php echo e(number_format($totalSiswaBaru)); ?>

                        <?php if($siswaBaruTrend > 0): ?>
                            <span class="indicator-trend up"><i class="fa-solid fa-arrow-up"></i> +<?php echo e($siswaBaruTrend); ?>%</span>
                        <?php elseif($siswaBaruTrend < 0): ?>
                            <span class="indicator-trend down"><i class="fa-solid fa-arrow-down"></i> <?php echo e($siswaBaruTrend); ?>%</span>
                        <?php else: ?>
                            <span class="indicator-trend neutral"><i class="fa-solid fa-minus"></i> 0%</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- C. Card Total Akumulasi Dinamis -->
                <div class="summary-indicator-card gray-accent">
                    <div class="indicator-header">
                        <span class="dot-indicator dot-gray"></span>
                        <span class="indicator-title">Total Akumulasi Siswa</span>
                    </div>
                    <div class="indicator-value">
                        <?php echo e(number_format($latestTotalSiswa)); ?>

                        <?php if($totalSiswaTrend > 0): ?>
                            <span class="indicator-trend up"><i class="fa-solid fa-arrow-up"></i> +<?php echo e($totalSiswaTrend); ?>%</span>
                        <?php elseif($totalSiswaTrend < 0): ?>
                            <span class="indicator-trend down"><i class="fa-solid fa-arrow-down"></i> <?php echo e($totalSiswaTrend); ?>%</span>
                        <?php else: ?>
                            <span class="indicator-trend neutral">Stabil</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Canvas Grafik -->
            <div class="line-chart-wrap">
                <canvas id="studentGrowthChart"></canvas>
            </div>
        </div>
    </section>

    <!-- 4. SUMMARY ROW (DISTRIBUSI & TUGAS) -->
    <section class="summary-grid">
        <!-- Panel Distribusi Aktivitas -->
        <div class="panel distribution-panel">
            <div class="panel-header">
                <h3>Distribusi Aktivitas</h3>
            </div>

            <div class="distribution-content">
                <div class="donut-wrap">
                    <canvas id="activityDistributionChart"></canvas>

                    <div class="donut-center">
                        <span>Total Aktivitas</span>
                        <strong><?php echo e(number_format($totalDistribusiFiltered)); ?></strong>
                    </div>
                </div>

                <div class="distribution-list">
                    <?php $__currentLoopData = $filteredDistribusi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $percentage = $totalDistribusiFiltered > 0
                                ? round(($item['value'] / $totalDistribusiFiltered) * 100)
                                : 0;
                        ?>

                        <div class="distribution-item">
                            <span>
                                <i class="dot dot-<?php echo e($index); ?>"></i>
                                <?php echo e($item['label']); ?>

                            </span>
                            <strong><?php echo e($percentage); ?>%</strong>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <!-- Panel Tugas Menunggu -->
        <div class="panel tasks-panel">
            <div class="panel-header">
                <h3>
                    Tugas Menunggu
                    <span class="count-pill"><?php echo e($total_tugas_menunggu); ?></span>
                </h3>

                <a href="<?php echo e(route('admin.siswa.pendaftaran')); ?>" class="view-all-link">Lihat semua <i class="fa-solid fa-arrow-right-long"></i></a>
            </div>

            <div class="task-list">
                <?php $__currentLoopData = $tugas_menunggu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($task['route']); ?>" class="task-item">
                        <div class="task-icon">
                            <i class="fa-solid <?php echo e($task['icon']); ?>"></i>
                        </div>

                        <div class="task-text">
                            <strong><?php echo e($task['title']); ?></strong>
                            <span><?php echo e($task['subtitle']); ?></span>
                        </div>

                        <div class="task-right">
                            <div class="task-count"><?php echo e($task['count']); ?></div>
                            <i class="fa-solid fa-chevron-right task-arrow"></i>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    <!-- 5. BOTTOM GRID -->
    <section class="bottom-grid">
        <!-- Log Aktivitas Sistem -->
        <div class="panel log-panel">
            <div class="panel-header">
                <h3>Log Aktivitas Sistem</h3>
                <a href="#" class="view-all-link">Lihat semua <i class="fa-solid fa-arrow-right-long"></i></a>
            </div>

            <div class="activity-list">
                <?php $__empty_1 = true; $__currentLoopData = $log_aktivitas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="activity-item">
                        <div class="activity-avatar avatar-<?php echo e($log['type']); ?>">
                            <?php echo e($log['initial']); ?>

                        </div>

                        <div class="activity-text">
                            <strong><?php echo e($log['title']); ?></strong>
                            <span><?php echo e($log['description']); ?></span>
                        </div>

                        <div class="activity-right">
                            <span class="status status-<?php echo e($log['type']); ?>">
                                <?php echo e($log['status']); ?>

                            </span>
                            <small><?php echo e($log['time']->diffForHumans()); ?></small>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state">
                        Belum ada aktivitas terbaru.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kontrol Cepat -->
        <div class="panel quick-panel">
            <div class="panel-header">
                <h3>Kontrol Cepat</h3>
            </div>

            <div class="quick-grid">
                <a href="<?php echo e(route('admin.siswa.index')); ?>" class="quick-action q-teal">
                    <div class="quick-icon">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <span>Tambah Siswa</span>
                </a>

                <a href="<?php echo e(route('admin.assignments.index')); ?>" class="quick-action q-red">
                    <div class="quick-icon">
                        <i class="fa-solid fa-upload"></i>
                    </div>
                    <span>Upload Materi</span>
                </a>

                <a href="<?php echo e(route('admin.tryout.index')); ?>" class="quick-action q-red-dark">
                    <div class="quick-icon">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </div>
                    <span>Buat Tryout</span>
                </a>

                <a href="<?php echo e(route('admin.announcement.create')); ?>" class="quick-action q-gray">
                    <div class="quick-icon">
                        <i class="fa-solid fa-bullhorn"></i>
                    </div>
                    <span>Tambah Pengumuman</span>
                </a>
            </div>
        </div>

        <!-- Informasi & Promosi -->
        <div class="panel promo-panel">
            <div class="panel-header">
                <h3>Informasi & Promosi</h3>
                <a href="<?php echo e(route('admin.promo.index')); ?>" class="view-all-link">Lihat semua <i class="fa-solid fa-arrow-right-long"></i></a>
            </div>

            <div class="promo-list">
                <?php $__empty_1 = true; $__currentLoopData = $informasi_promosi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="promo-item">
                        <div class="promo-thumb">
                            <?php if(!empty($info['image'])): ?>
                                <?php
                                    $image = $info['image'];

                                    if (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])) {
                                        $imageUrl = $image;
                                    } elseif (\Illuminate\Support\Str::startsWith($image, ['storage/'])) {
                                        $imageUrl = asset($image);
                                    } else {
                                        $imageUrl = asset('storage/' . ltrim($image, '/'));
                                    }
                                ?>

                                <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($info['title']); ?>">
                            <?php else: ?>
                                <i class="fa-solid fa-bullhorn"></i>
                            <?php endif; ?>
                        </div>

                        <div class="promo-text">
                            <strong><?php echo e($info['title']); ?></strong>
                            <span><?php echo e(\Illuminate\Support\Str::limit($info['description'], 48)); ?></span>

                            <div class="promo-meta-row">
                                <em><?php echo e($info['status']); ?></em>
                                <small><i class="fa-regular fa-clock"></i> <?php echo e($info['date']); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state">
                        Belum ada promo, banner, atau pengumuman aktif.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const chartLabels = <?php echo json_encode($chart_labels, 15, 512) ?>;
    const siswaBaruData = <?php echo json_encode($chart_siswa_baru, 15, 512) ?>;
    const aktivitasHarianData = <?php echo json_encode($chart_aktivitas_harian, 15, 512) ?>;
    const totalSiswaData = <?php echo json_encode($chart_total_siswa, 15, 512) ?>;

    const distribusiLabels = <?php echo json_encode($filteredDistribusi->pluck('label'), 15, 512) ?>;
    const distribusiValues = <?php echo json_encode($filteredDistribusi->pluck('value'), 15, 512) ?>;

    // FILTER CONTROL DINAMIS (SEGMENTED CONTROL STYLE)
    document.addEventListener("DOMContentLoaded", function () {
        const filterButtons = document.querySelectorAll("#timeFilterGroup .control-btn");
        const urlParams = new URLSearchParams(window.location.search);
        const currentDays = urlParams.get('days') || '30';

        filterButtons.forEach(button => {
            if (button.getAttribute("data-days") === currentDays) {
                button.classList.add("active");
            } else {
                button.classList.remove("active");
            }

            button.addEventListener("click", function () {
                const days = this.getAttribute("data-days");
                window.location.href = "<?php echo e(route('admin.dashboard')); ?>?days=" + days;
            });
        });
    });

    const lineCtx = document.getElementById('studentGrowthChart');

    if (lineCtx) {
        const ctx = lineCtx.getContext('2d');

        // MEMBUAT EFEK GRADASI AREA HALUS PADA GRAFIK
        const redGradient = ctx.createLinearGradient(0, 0, 0, 240);
        redGradient.addColorStop(0, 'rgba(229, 57, 53, 0.12)'); 
        redGradient.addColorStop(1, 'rgba(229, 57, 53, 0.00)');

        const tealGradient = ctx.createLinearGradient(0, 0, 0, 240);
        tealGradient.addColorStop(0, 'rgba(46, 168, 171, 0.12)'); 
        tealGradient.addColorStop(1, 'rgba(46, 168, 171, 0.00)');

        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Siswa Baru',
                        data: siswaBaruData,
                        borderColor: '#e53935',
                        backgroundColor: redGradient, 
                        tension: 0.38,
                        borderWidth: 3,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#e53935',
                        fill: true,
                    },
                    {
                        label: 'Aktif Harian',
                        data: aktivitasHarianData,
                        borderColor: '#2ea8ab',
                        backgroundColor: tealGradient, 
                        tension: 0.38,
                        borderWidth: 3,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#2ea8ab',
                        borderDash: [6, 4],
                        fill: true,
                    },
                    {
                        label: 'Total Siswa',
                        data: totalSiswaData,
                        borderColor: '#9e9e9e',
                        backgroundColor: 'transparent',
                        tension: 0.38,
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
                        display: false 
                    },
                    tooltip: {
                        backgroundColor: '#ffffff',
                        titleColor: '#1f2937',
                        bodyColor: '#4b5563',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 6,
                        usePointStyle: true,
                        titleFont: {
                            family: 'Montserrat',
                            weight: '700',
                            size: 11
                        },
                        bodyFont: {
                            family: 'Montserrat',
                            weight: '600',
                            size: 11
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#9e9e9e',
                            maxTicksLimit: 8,
                            font: {
                                size: 10,
                                weight: '600',
                                family: 'Montserrat'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            color: '#9e9e9e',
                            font: {
                                size: 10,
                                weight: '600',
                                family: 'Montserrat'
                            }
                        }
                    }
                }
            }
        });
    }

    // INSTANSIASI GRAFIK DOUGHNUT
    const donutCtx = document.getElementById('activityDistributionChart');

    if (donutCtx) {
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: distribusiLabels,
                datasets: [
                    {
                        data: distribusiValues,
                        backgroundColor: ['#2ea8ab', '#e53935', '#c5352c', '#9e9e9e'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        cutout: '72%'
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

    .spekta-dashboard {
        width: 100%;
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* 1. WELCOME CARD */
    .welcome-card {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-left: 5px solid var(--spekta-teal);
        color: var(--text-main);
        border-radius: 16px;
        padding: 24px 30px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        position: relative;
        overflow: hidden;
    }

    .welcome-card::after {
        content: "";
        position: absolute;
        width: 200px;
        height: 200px;
        right: -60px;
        top: -60px;
        background: linear-gradient(135deg, rgba(46, 168, 171, 0.05) 0%, rgba(229, 57, 53, 0.03) 100%);
        border-radius: 999px;
        pointer-events: none;
    }

    .welcome-card h1 {
        font-size: 20px;
        font-weight: 800;
        margin: 0 0 6px;
        letter-spacing: -0.01em;
        color: var(--text-main);
    }

    .welcome-card h1 span { color: var(--spekta-red-dark); }
    .welcome-card p { margin: 0; font-size: 13px; color: var(--text-muted); font-weight: 500; }

    .welcome-date {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 14px;
        border-left: 1px solid var(--border-soft);
        padding-left: 24px;
        min-width: 220px;
    }

    .date-icon {
        width: 40px;
        height: 40px;
        display: grid;
        place-items: center;
        background: var(--spekta-teal-light);
        color: var(--spekta-teal);
        border-radius: 10px;
        font-size: 18px;
    }

    .welcome-date strong, .welcome-date span { display: block; }
    .welcome-date strong { font-size: 13px; font-weight: 800; color: var(--text-main); }
    .welcome-date span { font-size: 11px; color: var(--text-muted); font-weight: 600; margin-top: 2px; }

    /* 2. STATS GRID */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 14px;
        padding: 18px;
        min-height: 148px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.01);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        color: inherit;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        border-color: var(--spekta-gray);
        box-shadow: 0 10px 24px rgba(0,0,0,0.04);
    }

    .stat-card.color-teal:hover { border-color: var(--spekta-teal); }
    .stat-card.color-red:hover { border-color: var(--spekta-red); }

    .stat-icon-wrap { display: flex; justify-content: flex-start; margin-bottom: 12px; }
    .stat-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-size: 14px;
    }

    .color-teal .stat-icon { background: var(--spekta-teal-light); color: var(--spekta-teal); }
    .color-red .stat-icon { background: var(--spekta-red-light); color: var(--spekta-red); }

    .stat-card p {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        margin: 0 0 4px;
    }

    .stat-card h2 {
        font-size: 24px;
        line-height: 1;
        font-weight: 800;
        color: var(--text-main);
        margin: 0 0 8px;
    }

    .stat-meta { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .badge { font-size: 9px; font-weight: 800; border-radius: 6px; padding: 3px 6px; }
    .badge-success { background: #e6f7ed; color: #15803d; }
    .badge-danger { background: #fee2e2; color: #b91c1c; }
    .badge-info { background: #e0f2fe; color: #0369a1; }
    .badge-warning { background: #fef3c7; color: #b45309; }
    .stat-meta small { color: var(--text-muted); font-size: 10px; font-weight: 600; }

    /* 3. SEGMENTED CONTROL & HEADER */
    .analytics-row { margin-bottom: 24px; }
    
    .analytics-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 20px;
    }

    .panel-header-text h3 {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-main);
        margin: 0 0 4px 0;
        letter-spacing: -0.01em;
    }

    .panel-subtitle {
        font-size: 11px;
        color: var(--text-muted);
        margin: 0;
        font-weight: 600;
    }

    .segmented-control {
        display: flex;
        background: var(--spekta-gray-light);
        padding: 4px;
        border-radius: 10px;
        border: 1px solid var(--border-soft);
    }

    .control-btn {
        border: none;
        background: transparent;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 800;
        padding: 6px 14px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .control-btn.active {
        background: var(--spekta-white);
        color: var(--text-main);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    /* KARTU LEGENDA RINGKASAN BARU (SUMMARY GRID) */
    .chart-summary-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin: 0 0 24px 0;
    }

    .summary-indicator-card {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        padding: 14px 18px;
        transition: all 0.25s ease;
    }

    .summary-indicator-card:hover {
        border-color: var(--spekta-gray);
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }

    .indicator-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .dot-indicator {
        width: 8px;
        height: 8px;
        border-radius: 99px;
    }

    .dot-teal { background: var(--spekta-teal); box-shadow: 0 0 8px rgba(46,168,171,0.4); }
    .dot-red { background: var(--spekta-red); box-shadow: 0 0 8px rgba(229,57,53,0.4); }
    .dot-gray { background: var(--spekta-gray); }

    .indicator-title {
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
    }

    .indicator-value {
        font-size: 20px;
        font-weight: 900;
        color: var(--text-main);
        display: flex;
        align-items: baseline;
        gap: 8px;
    }

    .indicator-trend {
        font-size: 10px;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }

    .indicator-trend.up { background: #e6f7ed; color: #15803d; }
    .indicator-trend.down { background: #fef2f2; color: #b91c1c; }
    .indicator-trend.neutral { background: var(--spekta-gray-light); color: var(--text-muted); }

    .line-chart-wrap {
        height: 280px;
        border-top: 1px solid var(--border-soft);
        padding-top: 20px;
        position: relative;
    }

    /* 4. SUMMARY & BOTTOM GRID */
    .summary-grid { display: grid; grid-template-columns: 1fr 1.25fr; gap: 24px; margin-bottom: 24px; }
    .bottom-grid { display: grid; grid-template-columns: 1.4fr 0.8fr 1fr; gap: 24px; }

    .panel {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
        padding: 20px;
    }

    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .panel-header h3 {
        font-size: 14px;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
        letter-spacing: -0.01em;
    }

    .view-all-link {
        color: var(--spekta-teal);
        font-size: 11px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s ease;
    }
    .view-all-link:hover { color: var(--spekta-red); transform: translateX(2px); }

    /* 5. DISTRIBUSI */
    .distribution-panel, .tasks-panel { min-height: 280px; }
    .distribution-content { display: grid; grid-template-columns: 180px 1fr; align-items: center; gap: 20px; }
    .donut-wrap { height: 160px; position: relative; }

    .donut-center {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        pointer-events: none;
    }

    .donut-center span { display: block; color: var(--text-muted); font-size: 9px; font-weight: 700; text-transform: uppercase; }
    .donut-center strong { display: block; color: var(--text-main); font-size: 18px; font-weight: 800; margin-top: 2px; }

    .distribution-list { display: grid; gap: 10px; }
    .distribution-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 700;
        padding: 6px 0;
        border-bottom: 1px dashed var(--border-soft);
    }
    .distribution-item:last-child { border-bottom: none; }
    .distribution-item strong { color: var(--text-main); }

    .dot { width: 8px; height: 8px; border-radius: 99px; display: inline-block; margin-right: 6px; }
    .dot-0 { background: #2ea8ab; }
    .dot-1 { background: #e53935; }
    .dot-2 { background: #c5352c; }
    .dot-3 { background: #9e9e9e; }

    .count-pill {
        display: inline-grid;
        place-items: center;
        min-width: 24px;
        height: 24px;
        padding: 0 6px;
        border-radius: 999px;
        background: var(--spekta-red-light);
        color: var(--spekta-red);
        font-size: 11px;
        font-weight: 800;
        margin-left: 6px;
    }

    .task-list, .activity-list, .promo-list { display: grid; gap: 12px; }
    .task-item {
        display: grid;
        grid-template-columns: 36px 1fr auto;
        align-items: center;
        gap: 12px;
        color: inherit;
        padding: 8px 10px;
        border-radius: 10px;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }

    .task-item:hover { background: var(--spekta-gray-light); border-color: var(--border-soft); transform: translateX(4px); }
    .task-icon {
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        background: var(--spekta-teal-light);
        color: var(--spekta-teal);
        border-radius: 10px;
        font-size: 14px;
    }

    .task-text strong, .activity-text strong, .promo-text strong { display: block; font-size: 12px; font-weight: 800; color: var(--text-main); margin-bottom: 2px; }
    .task-text span, .activity-text span, .promo-text span { display: block; font-size: 10px; color: var(--text-muted); font-weight: 600; line-height: 1.3; }

    .task-right { display: flex; align-items: center; gap: 10px; }
    .task-count {
        min-width: 24px;
        height: 24px;
        padding: 0 4px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: var(--spekta-gray-light);
        color: var(--text-main);
        font-size: 10px;
        font-weight: 800;
    }

    .task-arrow { color: var(--spekta-gray); font-size: 10px; }

    /* LOG AKTIVITAS */
    .activity-item {
        display: grid;
        grid-template-columns: 36px 1fr auto;
        align-items: center;
        gap: 12px;
        padding-bottom: 10px;
        border-bottom: 1px dashed var(--border-soft);
    }
    .activity-item:last-child { border-bottom: none; padding-bottom: 0; }

    .activity-avatar {
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
    }
    .avatar-success { background: #e6f7ed; color: #15803d; }
    .avatar-warning { background: #fef3c7; color: #b45309; }
    .avatar-info { background: #e0f2fe; color: #0369a1; }

    .activity-right { text-align: right; }
    .status {
        display: inline-block;
        padding: 3px 6px;
        border-radius: 5px;
        font-size: 8px;
        font-weight: 800;
        margin-bottom: 4px;
    }
    .status-success { background: #e6f7ed; color: #15803d; }
    .status-warning { background: #fef3c7; color: #b45309; }
    .status-info { background: #e0f2fe; color: #0369a1; }
    .activity-right small { display: block; font-size: 9px; color: var(--text-muted); font-weight: 600; }

    /* KONTROL CEPAT */
    .quick-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .quick-action {
        min-height: 94px;
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 12px;
        color: inherit;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .quick-icon {
        width: 38px;
        height: 38px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        margin-bottom: 8px;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .quick-action.q-teal .quick-icon { background: var(--spekta-teal-light); color: var(--spekta-teal); }
    .quick-action.q-red .quick-icon { background: var(--spekta-red-light); color: var(--spekta-red); }
    .quick-action.q-red-dark .quick-icon { background: rgba(197, 53, 44, 0.08); color: var(--spekta-red-dark); }
    .quick-action.q-gray .quick-icon { background: var(--spekta-gray-light); color: var(--text-muted); }

    .quick-action:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .quick-action.q-teal:hover { border-color: var(--spekta-teal); }
    .quick-action.q-red:hover { border-color: var(--spekta-red); }
    .quick-action.q-red-dark:hover { border-color: var(--spekta-red-dark); }
    .quick-action.q-gray:hover { border-color: var(--spekta-gray); }
    .quick-action:hover .quick-icon { transform: scale(1.1); }
    .quick-action span { font-size: 11px; font-weight: 800; color: var(--text-main); }

    /* PROMO */
    .promo-item { display: grid; grid-template-columns: 56px 1fr; gap: 12px; padding-bottom: 10px; border-bottom: 1px dashed var(--border-soft); }
    .promo-item:last-child { border-bottom: none; padding-bottom: 0; }
    .promo-thumb {
        width: 56px; height: 50px; border-radius: 8px;
        background: linear-gradient(135deg, var(--spekta-teal), var(--spekta-red-dark));
        color: var(--spekta-white); overflow: hidden; display: grid; place-items: center;
    }
    .promo-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .promo-meta-row { display: flex; align-items: center; gap: 8px; margin-top: 6px; }
    .promo-text em { font-style: normal; background: #e6f7ed; color: #15803d; font-size: 8px; font-weight: 800; text-transform: uppercase; border-radius: 4px; padding: 2px 5px; }
    .promo-text small { font-size: 9px; color: var(--text-muted); font-weight: 700; display: flex; align-items: center; gap: 3px; }

    .empty-state { padding: 16px; text-align: center; color: var(--text-muted); font-size: 11px; font-weight: 700; background: var(--spekta-gray-light); border-radius: 10px; }

    /* RESPONSIVE LAYOUT */
    @media (max-width: 1536px) {
        .stats-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (max-width: 1280px) {
        .summary-grid, .bottom-grid { grid-template-columns: 1fr; }
        .distribution-content { grid-template-columns: 1fr; }
        .donut-wrap { height: 160px; }
        .chart-summary-row { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .welcome-card { flex-direction: column; align-items: flex-start; padding: 20px; }
        .welcome-date { border-left: none; padding-left: 0; min-width: unset; }
        .stats-grid { grid-template-columns: 1fr; }
        .analytics-header-row { flex-direction: column; align-items: flex-start; gap: 12px; }
        .line-chart-wrap { height: 240px; }
        .activity-item { grid-template-columns: 36px 1fr; }
        .activity-right { grid-column: 2 / 3; text-align: left; margin-top: 4px; }
    }
    /* test hapus disini kenn */
</style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>