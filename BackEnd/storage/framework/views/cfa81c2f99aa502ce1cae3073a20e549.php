<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>Spekta Academy - <?php echo $__env->yieldContent('title', 'Dashboard'); ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --spekta-red: #d90429;
            --spekta-red-dark: #98001f;
            --spekta-maroon: #410f25;
            --spekta-navy: #071121;
            --spekta-text: #111827;
            --spekta-muted: #6b7280;
            --spekta-border: #edf0f4;
            --spekta-bg: #f5f7fb;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--spekta-bg);
            color: var(--spekta-text);
            overflow: hidden;
        }

        a { color: inherit; text-decoration: none; }
        button { font-family: inherit; }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: var(--spekta-red); border-radius: 999px; }

        .app-shell { display: flex; width: 100%; height: 100vh; overflow: hidden; background: var(--spekta-bg); }

        .sidebar {
            width: 292px; min-width: 292px; height: 100vh; color: #fff; position: relative; overflow: hidden;
            background: radial-gradient(circle at top left, rgba(255,255,255,0.16), transparent 30%),
                        linear-gradient(180deg, #df0030 0%, #8b001d 44%, #071121 100%);
            border-right: 1px solid rgba(255,255,255,0.08);
            transition: width 0.25s ease, min-width 0.25s ease, transform 0.25s ease;
            z-index: 40;
        }

        .sidebar-inner { position: relative; z-index: 1; display: flex; flex-direction: column; height: 100%; }

        .brand { padding: 34px 24px 28px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.10); }

        .brand-logo {
            width: 66px; height: 66px; margin: 0 auto 17px; display: grid; place-items: center; border-radius: 19px;
            background: #fff; color: var(--spekta-red); font-size: 32px; font-weight: 900; box-shadow: 0 18px 35px rgba(0,0,0,0.22);
        }

        .brand-title { font-size: 19px; font-weight: 900; letter-spacing: 0.26em; line-height: 1; }
        .brand-subtitle { margin-top: 8px; font-size: 9px; font-weight: 800; letter-spacing: 0.42em; opacity: 0.64; }

        .sidebar-nav { flex: 1; overflow-y: auto; padding: 22px 18px 24px; }
        .nav-section { margin-bottom: 22px; }
        .nav-heading { padding: 0 12px; margin-bottom: 10px; font-size: 10px; font-weight: 900; letter-spacing: 0.18em; color: rgba(255,255,255,0.55); text-transform: uppercase; }

        .sidebar-item {
            position: relative; min-height: 46px; display: flex; align-items: center; gap: 14px; padding: 12px 13px;
            border-radius: 12px; color: rgba(255,255,255,0.9); font-size: 13px; font-weight: 800; transition: 0.2s ease; margin-bottom: 5px;
        }

        .sidebar-item i { width: 18px; text-align: center; font-size: 14px; opacity: 0.92; }
        .sidebar-item:hover { background: rgba(255,255,255,0.10); color: #fff; transform: translateX(3px); }
        .sidebar-item.is-active { background: linear-gradient(90deg, #e4002b 0%, #ba0025 100%); color: #fff; box-shadow: 0 13px 25px rgba(80, 0, 20, 0.24); }

        .nav-badge { margin-left: auto; min-width: 22px; height: 22px; padding: 0 7px; display: inline-grid; place-items: center; border-radius: 999px; background: #fff; color: var(--spekta-red); font-size: 10px; font-weight: 900; }

        .sidebar-footer { margin: 0 18px 20px; padding: 18px; border-radius: 14px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.13); text-align: center; color: rgba(255,255,255,0.78); font-size: 11px; font-weight: 800; }

        .main-wrapper { flex: 1; min-width: 0; height: 100vh; display: flex; flex-direction: column; overflow: hidden; background: radial-gradient(circle at top right, rgba(217, 4, 41, 0.045), transparent 28%), var(--spekta-bg); }

        .topbar {
            height: 86px; flex-shrink: 0; background: rgba(255,255,255,0.96); backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--spekta-border); display: flex; align-items: center; justify-content: space-between;
            gap: 24px; padding: 0 34px; box-shadow: 0 4px 20px rgba(15, 23, 42, 0.035); z-index: 25;
        }

        .topbar-left { display: flex; align-items: center; gap: 18px; min-width: 0; }
        .sidebar-toggle { width: 42px; height: 42px; border: none; border-radius: 12px; background: transparent; color: #111827; display: grid; place-items: center; cursor: pointer; transition: 0.18s ease; }
        .sidebar-toggle:hover { background: #f1f5f9; }

        .page-title h1 { margin: 0; font-size: 15px; font-weight: 900; letter-spacing: 0.1em; color: #111827; text-transform: uppercase; }
        .page-title p { margin: 5px 0 0; font-size: 11px; font-weight: 600; color: #6b7280; }

        .topbar-right { display: flex; align-items: center; gap: 18px; margin-left: auto; }
        .profile-button { border: none; border-radius: 16px; background: transparent; display: flex; align-items: center; gap: 12px; padding: 6px 8px; cursor: pointer; transition: 0.18s ease; }
        .profile-button:hover { background: #f8fafc; }
        .profile-avatar { width: 46px; height: 46px; border-radius: 999px; display: grid; place-items: center; background: var(--spekta-red); color: #fff; font-size: 15px; font-weight: 900; box-shadow: 0 11px 24px rgba(217, 4, 41, 0.24); }

        .dropdown-panel { position: absolute; top: calc(100% + 13px); right: 0; width: 290px; background: #fff; border: 1px solid var(--spekta-border); border-radius: 17px; box-shadow: 0 24px 55px rgba(15, 23, 42, 0.14); padding: 12px; display: none; z-index: 60; }
        .dropdown-panel.show { display: block; animation: dropdownFade 0.16s ease; }

        @keyframes dropdownFade { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }

        .logout-button { width: 100%; border: none; border-radius: 12px; background: transparent; color: #dc2626; display: flex; align-items: center; gap: 12px; padding: 12px 10px; font-size: 12px; font-weight: 900; cursor: pointer; transition: 0.18s ease; }
        .logout-button:hover { background: #fef2f2; }

        .content-scroll { flex: 1; overflow-y: auto; padding: 24px; }
        .content-container { width: 100%; margin: 0 auto; }
        .mobile-backdrop { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); z-index: 35; }

        body.sidebar-collapsed .sidebar { width: 92px; min-width: 92px; }
        body.sidebar-collapsed .brand-logo { width: 54px; height: 54px; font-size: 26px; margin-bottom: 0; }
        body.sidebar-collapsed .brand-title, body.sidebar-collapsed .brand-subtitle, body.sidebar-collapsed .nav-heading, body.sidebar-collapsed .sidebar-item span, body.sidebar-collapsed .sidebar-footer, body.sidebar-collapsed .nav-badge { display: none; }
        body.sidebar-collapsed .sidebar-item { justify-content: center; }

        @media (max-width: 768px) {
            body { overflow: auto; }
            .sidebar { position: fixed; left: 0; top: 0; transform: translateX(-100%); }
            body.mobile-sidebar-open .sidebar { transform: translateX(0); }
            body.mobile-sidebar-open .mobile-backdrop { display: block; }
            .topbar { height: 74px; padding: 0 16px; }
            .profile-info, .fa-chevron-down { display: none; }
        }
    </style>
</head>

<body>
    <div class="app-shell">

        <aside class="sidebar" id="sidebar">
            <div class="sidebar-inner">
                <div class="brand">
                    <div class="brand-logo">S</div>
                    <div class="brand-title">SPEKTA</div>
                    <div class="brand-subtitle">ACADEMY</div>
                </div>

                <nav class="sidebar-nav">
                    <?php if(Auth::check() && Auth::user()->role_id == 1): ?>
                        <div class="nav-section">
                            <div class="nav-heading">Overview</div>
                            <a href="<?php echo e(route('admin.dashboard')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.dashboard') ? 'is-active' : ''); ?>">
                                <i class="fa-solid fa-house"></i> <span>Dashboard</span>
                            </a>
                        </div>

                        <div class="nav-section">
                            <div class="nav-heading">Manajemen Akademik</div>
                            <a href="<?php echo e(route('admin.siswa.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.siswa.index') ? 'is-active' : ''); ?>">
                                <i class="fa-solid fa-user-group"></i> <span>Siswa</span>
                            </a>
                            <a href="<?php echo e(route('admin.manajemen-pengajar.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.manajemen-pengajar.*') ? 'is-active' : ''); ?>">
                                <i class="fa-solid fa-chalkboard-user"></i> <span>Pengajar</span>
                            </a>
                            <a href="<?php echo e(route('admin.jadwal.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.jadwal.*') ? 'is-active' : ''); ?>">
                                <i class="fa-solid fa-calendar-days"></i> <span>Jadwal Kelas</span>
                            </a>
                            <a href="<?php echo e(route('admin.scores.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.scores.*') ? 'is-active' : ''); ?>">
                                <i class="fa-solid fa-clipboard-list"></i> <span>Rekap Nilai</span>
                            </a>
                        </div>

                        <div class="nav-section">
                            <div class="nav-heading">Pembelajaran</div>
                            <a href="<?php echo e(route('admin.assignments.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.assignments.*') ? 'is-active' : ''); ?>">
                                <i class="fa-solid fa-book-open"></i> <span>Materi</span>
                            </a>

                            <!-- ✨ MENU BARU: Master Tryout (Kurasi Soal) -->
                            <a href="<?php echo e(route('admin.tryout.index')); ?>"
                               class="sidebar-item <?php echo e(request()->routeIs('admin.tryout.*') ? 'is-active' : ''); ?>">
                                <i class="fa-solid fa-stopwatch-20"></i>
                                <span>Master Tryout</span>
                            </a>

                            <a href="<?php echo e(route('admin.classes.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.classes.*') ? 'is-active' : ''); ?>">
                                <i class="fa-solid fa-layer-group"></i> <span>Program Kelas</span>
                            </a>
                            <a href="<?php echo e(route('admin.tutor.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.tutor.*') ? 'is-active' : ''); ?>">
                                <i class="fa-solid fa-headset"></i> <span>Dedicated Tutor</span>
                            </a>
                        </div>

                        <div class="nav-section">
                            <div class="nav-heading">Promosi & Informasi</div>
                            <a href="<?php echo e(route('admin.promo.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.promo.*') ? 'is-active' : ''); ?>">
                                <i class="fa-solid fa-tags"></i> <span>Promo</span>
                            </a>
                            <a href="<?php echo e(route('admin.banners.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.banners.*') ? 'is-active' : ''); ?>">
                                <i class="fa-solid fa-image"></i> <span>Banner</span>
                            </a>
                            <a href="<?php echo e(route('admin.announcement.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.announcement.*') ? 'is-active' : ''); ?>">
                                <i class="fa-solid fa-bullhorn"></i> <span>Pengumuman</span>
                            </a>
                        </div>
                    <?php elseif(Auth::check() && Auth::user()->role_id == 2): ?>
                        <div class="nav-section">
                            <div class="nav-heading">Overview</div>
                            <a href="<?php echo e(route('pengajar.dashboard')); ?>" class="sidebar-item <?php echo e(request()->routeIs('pengajar.dashboard') ? 'is-active' : ''); ?>">
                                <i class="fa-solid fa-house"></i> <span>Dashboard</span>
                            </a>
                        </div>

                        <div class="nav-section">
                            <div class="nav-heading">Pembelajaran</div>
                            <a href="<?php echo e(route('pengajar.absensi.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('pengajar.absensi.*') ? 'is-active' : ''); ?>">
                                <i class="fa-solid fa-clipboard-check"></i> <span>Absensi Siswa</span>
                            </a>
                            <a href="<?php echo e(route('pengajar.materi.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('pengajar.materi.*') ? 'is-active' : ''); ?>">
                                <i class="fa-solid fa-book-open"></i> <span>Upload Materi</span>
                            </a>
                            <a href="<?php echo e(route('pengajar.latihan.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('pengajar.latihan.*') ? 'is-active' : ''); ?>">
                                <i class="fa-solid fa-file-pen"></i> <span>Latihan Soal</span>
                            </a>
                            <a href="<?php echo e(route('pengajar.tryout.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('pengajar.tryout.*') ? 'is-active' : ''); ?>">
                                <i class="fa-solid fa-pen-to-square"></i> <span>Setor Soal TO</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </nav>

                <div class="sidebar-footer">Spekta Academy © <?php echo e(date('Y')); ?></div>
            </div>
        </aside>

        <div class="mobile-backdrop" onclick="closeMobileSidebar()"></div>

        <div class="main-wrapper">
            <header class="topbar">
                <div class="topbar-left">
                    <button type="button" class="sidebar-toggle" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
                    <div class="page-title">
                        <h1><?php echo $__env->yieldContent('title', 'Dashboard'); ?></h1>
                        <p><?php echo $__env->yieldContent('subtitle', 'Sistem Manajemen Terpadu Spekta Academy'); ?></p>
                    </div>
                </div>

                <div class="topbar-right">
                    <div class="profile-wrap">
                        <button type="button" class="profile-button" onclick="toggleDropdown('profileDropdown')">
                            <div class="profile-avatar"><?php echo e(Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'S'); ?></div>
                            <div class="profile-info">
                                <strong><?php echo e(Auth::check() ? Auth::user()->name : 'User'); ?></strong>
                                <span><?php echo e(Auth::check() && Auth::user()->role ? ucfirst(Auth::user()->role->name) : 'User'); ?></span>
                            </div>
                            <i class="fa-solid fa-chevron-down" style="font-size: 11px; color: #6b7280;"></i>
                        </button>

                        <div class="dropdown-panel" id="profileDropdown">
                            <div class="dropdown-title">
                                <strong><?php echo e(Auth::check() ? Auth::user()->name : 'User'); ?></strong>
                                <span><?php echo e(Auth::check() ? Auth::user()->email : '-'); ?></span>
                            </div>

                            <!-- ✅ FIX: Gunakan Form POST untuk Logout agar tidak error 405 -->
                            <form action="<?php echo e(route('logout')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="logout-button">
                                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="content-scroll">
                <div class="content-container"><?php echo $__env->yieldContent('content'); ?></div>
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            if (window.innerWidth <= 768) { document.body.classList.toggle('mobile-sidebar-open'); return; }
            document.body.classList.toggle('sidebar-collapsed');
        }
        function closeMobileSidebar() { document.body.classList.remove('mobile-sidebar-open'); }
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            document.querySelectorAll('.dropdown-panel').forEach(item => { if (item.id !== id) item.classList.remove('show'); });
            if (dropdown) dropdown.classList.toggle('show');
        }
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown-panel') && !e.target.closest('.profile-button') && !e.target.closest('.icon-button')) {
                document.querySelectorAll('.dropdown-panel').forEach(item => item.classList.remove('show'));
            }
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\Windows\Documents\GitHub\Specta_Academy\BackEnd\resources\views/layouts/spekta.blade.php ENDPATH**/ ?>