<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>Spekta Academy - <?php echo $__env->yieldContent('title', 'Dashboard'); ?></title>

    <!-- KODE UNTUK MENGGANTI LOGO TAB BROWSER (FAVICON) -->
    <link rel="icon" href="<?php echo e(asset('logo.png')); ?>?v=1" type="image/png">
    <link rel="shortcut icon" href="<?php echo e(asset('logo.png')); ?>?v=1" type="image/png">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            /* Warna Baru yang Saling Mengimbangi */
            --spekta-red-dark: #c5352c;
            --spekta-red: #e53935;
            --spekta-teal: #2ea8ab;
            --spekta-teal-light: rgba(46, 168, 171, 0.08);
            --spekta-gray: #9e9e9e;
            --spekta-gray-light: #f3f4f6;
            --spekta-border: #e5e7eb;
            --spekta-bg: #f9fafb; /* Latar belakang abu-abu sangat terang */
            --spekta-text: #1f2937; /* Teks utama abu gelap */
            --spekta-muted: #6b7280; /* Teks sekunder */
            --spekta-white: #ffffff;
        }

        * { 
            box-sizing: border-box; 
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

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

        /* Scrollbar Halus & Bersih */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: var(--spekta-bg); }
        ::-webkit-scrollbar-thumb { background: var(--spekta-teal); border-radius: 999px; }

        .app-shell { display: flex; width: 100%; height: 100vh; overflow: hidden; background: var(--spekta-bg); }

        /* SIDEBAR BARU: Menggunakan Latar Putih Bersih agar Tidak Padat */
        .sidebar {
            width: 292px; min-width: 292px; height: 100vh; color: var(--spekta-text); position: relative; overflow: hidden;
            background: var(--spekta-white);
            border-right: 1px solid var(--spekta-border);
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), min-width 0.25s cubic-bezier(0.4, 0, 0.2, 1), transform 0.25s ease;
            z-index: 40;
        }

        .sidebar-inner { position: relative; z-index: 1; display: flex; flex-direction: column; height: 100%; }

        /* Area Brand / Logo */
        .brand { padding: 30px 24px 24px; text-align: center; border-bottom: 1px solid var(--spekta-border); }

        .brand-logo {
            width: 60px; height: 60px; margin: 0 auto 15px; display: grid; place-items: center; border-radius: 16px;
            background: linear-gradient(135deg, var(--spekta-red) 0%, var(--spekta-red-dark) 100%); 
            color: var(--spekta-white); font-size: 28px; font-weight: 900; 
            box-shadow: 0 10px 20px rgba(229, 57, 53, 0.25);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .brand-logo:hover { transform: scale(1.05) rotate(3deg); }

        .brand-title { font-size: 18px; font-weight: 900; letter-spacing: 0.2em; line-height: 1; color: var(--spekta-red-dark); }
        .brand-subtitle { margin-top: 6px; font-size: 9px; font-weight: 800; letter-spacing: 0.35em; color: var(--spekta-teal); }

        /* Area Navigasi */
        .sidebar-nav { flex: 1; overflow-y: auto; padding: 20px 16px 20px; }
        .nav-section { margin-bottom: 20px; }
        .nav-heading { padding: 0 12px; margin-bottom: 8px; font-size: 10px; font-weight: 800; letter-spacing: 0.12em; color: var(--spekta-gray); text-transform: uppercase; }

        /* Item Navigasi Sidebar */
        .sidebar-item {
            position: relative; min-height: 44px; display: flex; align-items: center; gap: 12px; padding: 11px 14px;
            border-radius: 10px; color: var(--spekta-muted); font-size: 13px; font-weight: 700; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); margin-bottom: 4px;
            cursor: pointer;
        }

        .sidebar-item i { width: 18px; text-align: center; font-size: 14px; color: var(--spekta-gray); transition: color 0.2s ease; }
        
        /* Hover State */
        .sidebar-item:hover { 
            background: var(--spekta-gray-light); 
            color: var(--spekta-red); 
            transform: translateX(4px); 
        }
        .sidebar-item:hover i { color: var(--spekta-red); }

        /* Active State (Merah Lembut Modern) */
        .sidebar-item.is-active { 
            background: linear-gradient(90deg, var(--spekta-red) 0%, var(--spekta-red-dark) 100%); 
            color: var(--spekta-white); 
            box-shadow: 0 6px 15px rgba(229, 57, 53, 0.2); 
        }
        .sidebar-item.is-active i { color: var(--spekta-white); }

        .nav-badge { 
            margin-left: auto; min-width: 20px; height: 20px; padding: 0 6px; display: inline-grid; place-items: center; 
            border-radius: 999px; background: var(--spekta-teal); color: var(--spekta-white); font-size: 9px; font-weight: 800; 
        }

        /* Footer Sidebar */
        .sidebar-footer { 
            margin: 0 16px 16px; padding: 12px; border-radius: 10px; 
            background: var(--spekta-gray-light); border: 1px solid var(--spekta-border); 
            text-align: center; color: var(--spekta-muted); font-size: 10px; font-weight: 700; 
        }

        /* Wrapper Konten Utama */
        .main-wrapper { 
            flex: 1; min-width: 0; height: 100vh; display: flex; flex-direction: column; overflow: hidden; 
            background: radial-gradient(circle at top right, rgba(46, 168, 171, 0.05), transparent 30%), var(--spekta-bg); 
        }

        /* Topbar Bersih */
        .topbar {
            height: 76px; flex-shrink: 0; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--spekta-border); display: flex; align-items: center; justify-content: space-between;
            gap: 20px; padding: 0 28px; box-shadow: 0 2px 12px rgba(0, 0, 0, 0.02); z-index: 25;
        }

        .topbar-left { display: flex; align-items: center; gap: 14px; min-width: 0; }
        
        .sidebar-toggle { 
            width: 38px; height: 38px; border: 1px solid var(--spekta-border); border-radius: 10px; 
            background: var(--spekta-white); color: var(--spekta-text); display: grid; place-items: center; 
            cursor: pointer; transition: all 0.2s ease; 
        }
        .sidebar-toggle:hover { background: var(--spekta-gray-light); color: var(--spekta-red); border-color: var(--spekta-red); }

        .page-title h1 { margin: 0; font-size: 14px; font-weight: 800; letter-spacing: 0.08em; color: var(--spekta-text); text-transform: uppercase; }
        .page-title p { margin: 4px 0 0; font-size: 11px; font-weight: 600; color: var(--spekta-muted); }

        /* Profile & Dropdown */
        .topbar-right { display: flex; align-items: center; gap: 16px; margin-left: auto; }
        
        .profile-button { 
            border: 1px solid var(--spekta-border); border-radius: 14px; background: var(--spekta-white); 
            display: flex; align-items: center; gap: 10px; padding: 6px 12px; cursor: pointer; 
            transition: all 0.2s ease; 
        }
        .profile-button:hover { border-color: var(--spekta-teal); background: var(--spekta-bg); }
        
        .profile-avatar { 
            width: 36px; height: 36px; border-radius: 999px; display: grid; place-items: center; 
            background: linear-gradient(135deg, var(--spekta-teal) 0%, #1e878a 100%); 
            color: var(--spekta-white); font-size: 13px; font-weight: 800; 
            box-shadow: 0 4px 10px rgba(46, 168, 171, 0.2); 
        }

        .profile-info { text-align: left; }
        .profile-info strong { display: block; font-size: 12px; font-weight: 800; color: var(--spekta-text); }
        .profile-info span { display: block; font-size: 10px; font-weight: 600; color: var(--spekta-muted); margin-top: 2px; }

        /* Dropdown Panel Modern */
        .dropdown-panel { 
            position: absolute; top: calc(100% + 10px); right: 0; width: 260px; 
            background: var(--spekta-white); border: 1px solid var(--spekta-border); border-radius: 14px; 
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08); padding: 10px; display: none; z-index: 60; 
        }
        .dropdown-panel.show { display: block; animation: dropdownFade 0.2s cubic-bezier(0.16, 1, 0.3, 1); }

        .dropdown-title { padding: 10px 12px; border-bottom: 1px solid var(--spekta-border); margin-bottom: 8px; }
        .dropdown-title strong { display: block; font-size: 12px; color: var(--spekta-text); }
        .dropdown-title span { display: block; font-size: 10px; color: var(--spekta-muted); margin-top: 2px; }

        @keyframes dropdownFade { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }

        .logout-button { 
            width: 100%; border: none; border-radius: 8px; background: transparent; color: var(--spekta-red); 
            display: flex; align-items: center; gap: 10px; padding: 10px 12px; font-size: 12px; font-weight: 800; 
            cursor: pointer; transition: all 0.2s ease; 
        }
        .logout-button:hover { background: #fef2f2; color: var(--spekta-red-dark); }

        .content-scroll { flex: 1; overflow-y: auto; padding: 24px; }
        .content-container { width: 100%; margin: 0 auto; }
        .mobile-backdrop { display: none; position: fixed; inset: 0; background: rgba(31, 41, 55, 0.2); backdrop-filter: blur(4px); z-index: 35; }

        /* Collapse State Sidebar */
        body.sidebar-collapsed .sidebar { width: 84px; min-width: 84px; }
        body.sidebar-collapsed .brand { padding: 24px 10px; }
        body.sidebar-collapsed .brand-logo { width: 44px; height: 44px; font-size: 20px; margin-bottom: 0; border-radius: 12px; }
        body.sidebar-collapsed .brand-title, 
        body.sidebar-collapsed .brand-subtitle, 
        body.sidebar-collapsed .nav-heading, 
        body.sidebar-collapsed .sidebar-item span, 
        body.sidebar-collapsed .sidebar-footer, 
        body.sidebar-collapsed .nav-badge { display: none; }
        body.sidebar-collapsed .sidebar-item { justify-content: center; width: 52px; margin: 0 auto 4px; padding: 11px 0; }

        @media (max-width: 768px) {
            body { overflow: auto; }
            .sidebar { position: fixed; left: 0; top: 0; transform: translateX(-100%); }
            body.mobile-sidebar-open .sidebar { transform: translateX(0); }
            body.mobile-sidebar-open .mobile-backdrop { display: block; }
            .topbar { height: 70px; padding: 0 16px; }
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
                    <div class="profile-wrap" style="position: relative;">
                        <button type="button" class="profile-button" onclick="toggleDropdown('profileDropdown')">
                            <div class="profile-avatar"><?php echo e(Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'S'); ?></div>
                            <div class="profile-info">
                                <strong><?php echo e(Auth::check() ? Auth::user()->name : 'User'); ?></strong>
                                <span><?php echo e(Auth::check() && Auth::user()->role ? ucfirst(Auth::user()->role->name) : 'User'); ?></span>
                            </div>
                            <i class="fa-solid fa-chevron-down" style="font-size: 11px; color: var(--spekta-muted);"></i>
                        </button>

                        <div class="dropdown-panel" id="profileDropdown">
                            <div class="dropdown-title">
                                <strong><?php echo e(Auth::check() ? Auth::user()->name : 'User'); ?></strong>
                                <span style="font-size: 10px; color: var(--spekta-muted);"><?php echo e(Auth::check() ? Auth::user()->email : '-'); ?></span>
                            </div>

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
            if (!e.target.closest('.dropdown-panel') && !e.target.closest('.profile-button')) {
                document.querySelectorAll('.dropdown-panel').forEach(item => item.classList.remove('show'));
            }
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/layouts/spekta.blade.php ENDPATH**/ ?>