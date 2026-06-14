<?php $__env->startSection('title', 'Management - Announcement'); ?>
<?php $__env->startSection('subtitle', 'Sistem Manajemen Pengumuman Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $announcementCollection = method_exists($announcements, 'getCollection') ? $announcements->getCollection() : collect($announcements);
    $totalAnnouncement = method_exists($announcements, 'total') ? $announcements->total() : $announcementCollection->count();

    // METRIK KE-3 DINAMIS: Pengumuman Hari Ini agar Grid Seimbang 3 Kolom
    $todayAnnouncements = $announcementCollection->filter(function($item) {
        return $item->created_at && $item->created_at->isToday();
    })->count();
?>

<div class="an-page">

    
    <section class="an-header">
        <div class="an-header-text">
            <span class="an-kicker">Promosi & Informasi</span>
            <h1>Management Announcement</h1>
            <p>Buat, kelola, dan publikasikan pengumuman untuk pengguna Spekta Academy.</p>
        </div>

        <!-- Tombol Pemicu Buka-Tutup Form (Sangat Interaktif) -->
        <button type="button" class="an-primary-btn" onclick="toggleAnnouncementForm()">
            <i class="fa-solid fa-plus" id="toggle-icon"></i>
            <span id="toggle-text">Buat Pengumuman</span>
        </button>
    </section>

    
    <?php if(session('success')): ?>
        <div class="an-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <div>
                <strong>Berhasil!</strong>
                <span><?php echo e(session('success')); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="an-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Data belum valid.</strong>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    
    <section class="an-stats">
        <!-- Total Pengumuman -->
        <div class="an-stat-card card-red">
            <div class="an-stat-icon red">
                <i class="fa-solid fa-bullhorn"></i>
            </div>
            <div class="an-stat-info">
                <p>Total Pengumuman</p>
                <h2><?php echo e(number_format($totalAnnouncement)); ?></h2>
            </div>
        </div>

        <!-- Pengumuman Bulan Ini -->
        <div class="an-stat-card card-teal">
            <div class="an-stat-icon teal">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
            <div class="an-stat-info">
                <p>Bulan Ini</p>
                <h2><?php echo e(number_format($announcementCollection->filter(fn($item) => $item->created_at && $item->created_at->isCurrentMonth())->count())); ?></h2>
            </div>
        </div>

        <!-- Pengumuman Hari Ini -->
        <div class="an-stat-card card-orange">
            <div class="an-stat-icon orange">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div class="an-stat-info">
                <p>Hari Ini</p>
                <h2><?php echo e(number_format($todayAnnouncements)); ?></h2>
            </div>
            <?php if($todayAnnouncements > 0): ?>
                <span class="an-pulse-dot"></span>
            <?php endif; ?>
        </div>
    </section>

    
    <!-- ID dipindahkan ke Grid utama agar Form dan Preview menyusut/hilang bersama-sama -->
    <section class="an-main-grid" id="announcementFormSection">
        
        <!-- Panel Form -->
        <div class="an-form-panel">
            <div class="an-panel-heading">
                <div>
                    <h2>Create New Announcement</h2>
                    <p>Isi judul, gambar sampul, dan deskripsi pengumuman yang akan ditampilkan.</p>
                </div>
            </div>

            <form action="<?php echo e(route('admin.announcement.store')); ?>" method="POST" enctype="multipart/form-data" class="an-form">
                <?php echo csrf_field(); ?>

                <div class="an-input-group full">
                    <label>Announcement Title</label>
                    <div class="an-input-wrap">
                        <i class="fa-solid fa-heading"></i>
                        <input type="text" name="title" value="<?php echo e(old('title')); ?>" placeholder="Enter headline..." required>
                    </div>
                </div>

                <div class="an-input-group full">
                    <label>Cover Image</label>
                    <div class="an-input-wrap">
                        <i class="fa-solid fa-upload"></i>
                        <input type="file" name="image" id="announcementImageInput" accept="image/*" required>
                    </div>
                </div>

                <div class="an-input-group full">
                    <label>Full Description</label>
                    <div class="an-input-wrap no-icon">
                        <textarea name="description" rows="5" placeholder="Write the announcement details here..." required><?php echo e(old('description')); ?></textarea>
                    </div>
                </div>

                <div class="an-form-action">
                    <button type="submit" class="an-submit-btn">
                        <i class="fa-solid fa-paper-plane"></i>
                        Publish Announcement Now
                    </button>
                </div>
            </form>
        </div>

        <!-- Panel Pratinjau Gambar Real-Time -->
        <aside class="an-preview-panel">
            <div class="an-panel-heading">
                <h2>Preview Cover</h2>
                <p>Pratinjau gambar pengumuman yang akan diunggah.</p>
            </div>

            <div class="an-preview-image" id="announcementPreviewBox">
                <div class="an-preview-empty">
                    <i class="fa-solid fa-image"></i>
                    <span>Preview gambar akan tampil di sini</span>
                </div>
            </div>

            <div class="an-preview-note">
                <i class="fa-solid fa-circle-info"></i>
                <span>Gunakan gambar yang jelas agar pengumuman terlihat menarik di aplikasi.</span>
            </div>
        </aside>

    </section>

    
    <section class="an-list-panel">
        <div class="an-panel-heading">
            <div>
                <h2>Daftar Pengumuman</h2>
                <p>Kelola semua pengumuman yang sudah dipublikasikan.</p>
            </div>
        </div>

        <div class="an-card-grid">
            <?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <article class="an-card">
                    <div class="an-card-image">
                        <?php if($row->image): ?>
                            <img src="<?php echo e(asset('storage/' . $row->image)); ?>" alt="<?php echo e($row->title); ?>">
                        <?php else: ?>
                            <div class="an-no-image">
                                <i class="fa-solid fa-image"></i>
                                <span>No Image</span>
                            </div>
                        <?php endif; ?>

                        <div class="an-date-badge">
                            <?php echo e($row->created_at?->translatedFormat('d M Y') ?? '-'); ?>

                        </div>
                    </div>

                    <div class="an-card-body">
                        <h3><?php echo e($row->title); ?></h3>
                        <p><?php echo e(\Illuminate\Support\Str::limit($row->description, 120)); ?></p>

                        <div class="an-card-meta">
                            <span>
                                <i class="fa-regular fa-clock"></i>
                                <?php echo e($row->created_at?->diffForHumans() ?? '-'); ?>

                            </span>
                        </div>

                        <div class="an-actions">
                            <a href="<?php echo e(route('admin.announcement.edit', $row->announcement_id)); ?>" class="edit">
                                <i class="fa-solid fa-pen"></i>
                                Edit
                            </a>

                            <form action="<?php echo e(route('admin.announcement.destroy', $row->announcement_id)); ?>" method="POST" onsubmit="return confirm('Hapus pengumuman ini secara permanen?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="delete">
                                    <i class="fa-solid fa-trash-can"></i>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="an-empty">
                    <div class="an-empty-icon"><i class="fa-solid fa-bullhorn"></i></div>
                    <strong>Belum ada pengumuman.</strong>
                    <p>Buat pengumuman pertama Anda melalui form di atas.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if(method_exists($announcements, 'hasPages') && $announcements->hasPages()): ?>
            <div class="an-pagination">
                <?php echo e($announcements->links()); ?>

            </div>
        <?php endif; ?>
    </section>

</div>

<script>
    // FUNGSI TOGGLE SELURUH GRID FORM & PREVIEW SECARA SERENTAK (NO MORE EMPTY SPACE)
    function toggleAnnouncementForm() {
        const formSection = document.getElementById('announcementFormSection');
        const toggleIcon = document.getElementById('toggle-icon');
        const toggleText = document.getElementById('toggle-text');

        if (formSection.classList.contains('show')) {
            formSection.classList.remove('show');
            toggleIcon.className = "fa-solid fa-plus";
            toggleText.innerText = "Buat Pengumuman";
        } else {
            formSection.classList.add('show');
            toggleIcon.className = "fa-solid fa-minus";
            toggleText.innerText = "Sembunyikan Form";
            formSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const announcementImageInput = document.getElementById('announcementImageInput');
        const announcementPreviewBox = document.getElementById('announcementPreviewBox');

        if (announcementImageInput && announcementPreviewBox) {
            announcementImageInput.addEventListener('change', function () {
                const file = this.files[0];

                if (!file) {
                    announcementPreviewBox.innerHTML = `
                        <div class="an-preview-empty">
                            <i class="fa-solid fa-image"></i>
                            <span>Preview gambar akan tampil di sini</span>
                        </div>`;
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (event) {
                    announcementPreviewBox.innerHTML = `<img src="${event.target.result}" alt="Preview Announcement" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">`;
                };
                reader.readAsDataURL(file);
            });
        }
    });
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

    /* BASE LAYOUT */
    .an-page {
        width: 100%;
        font-family: 'Montserrat', sans-serif;
        color: var(--text-main);
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* HEADER */
    .an-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        gap: 20px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }
    .an-kicker {
        display: inline-block;
        background: var(--spekta-red-light);
        color: var(--spekta-red-dark);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 6px;
        margin-bottom: 8px;
    }
    .an-header h1 {
        margin: 0 0 6px;
        color: var(--text-main);
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .an-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }
    .an-primary-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--spekta-red) 0%, var(--spekta-red-dark) 100%);
        color: var(--spekta-white);
        border: none;
        border-radius: 12px;
        padding: 12px 18px;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 6px 15px rgba(229, 57, 53, 0.2);
        cursor: pointer;
    }
    .an-primary-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(229, 57, 53, 0.3);
        color: var(--spekta-white);
    }

    /* ALERTS */
    .an-alert {
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 24px;
        display: flex;
        gap: 10px;
        align-items: center;
        font-size: 13px;
        font-weight: 800;
    }
    .an-alert.success { background: #e6f7ed; color: #15803d; border: 1px solid #bbf7d0; }
    .an-alert.error { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    .an-alert strong { display: block; margin-bottom: 2px; font-weight: 800;}
    .an-alert ul { margin: 4px 0 0; padding-left: 20px; }

    /* STATS (3 KOLOM SEIMBANG) */
    .an-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .an-stat-card {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        transition: all 0.2s ease;
        position: relative;
    }
    .an-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.03);
    }
    .an-stat-card.card-red:hover { border-color: var(--spekta-red); }
    .an-stat-card.card-teal:hover { border-color: var(--spekta-teal); }
    .an-stat-card.card-orange:hover { border-color: #d97706; }

    .an-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-size: 16px;
    }
    .an-stat-icon.red { background: var(--spekta-red-light); color: var(--spekta-red); }
    .an-stat-icon.teal { background: var(--spekta-teal-light); color: var(--spekta-teal); }
    .an-stat-icon.orange { background: rgba(217, 119, 6, 0.08); color: #d97706; }

    .an-stat-info p {
        margin: 0 0 4px;
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .an-stat-info h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1;
    }

    /* Indikator denyut */
    .an-pulse-dot {
        position: absolute;
        top: 14px; right: 14px;
        width: 6px; height: 6px;
        background: #d97706;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.7);
        animation: pulseOrange 1.5s infinite;
    }
    @keyframes pulseOrange {
        0% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(217, 119, 6, 0); }
        100% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0); }
    }

    /* MAIN GRID (FORM & PREVIEW COLLAPSIBLE TOGETHER) */
    .an-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 350px;
        gap: 24px;
        align-items: start;
        
        /* LOGIKA COLLAPSIBLE DI-APLIKASIKAN PADA MAIN GRID */
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        margin-bottom: 0;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease, margin-bottom 0.4s ease;
    }
    .an-main-grid.show {
        max-height: 1000px; /* Batas tinggi aman untuk Form + Preview */
        opacity: 1;
        margin-bottom: 24px;
    }
    
    .an-form-panel, .an-preview-panel {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
    }

    .an-preview-panel {
        position: sticky;
        top: 24px;
    }
    .an-panel-heading {
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--spekta-gray-light);
    }
    .an-panel-heading h2 {
        margin: 0 0 4px;
        font-size: 15px;
        font-weight: 800;
        color: var(--text-main);
    }
    .an-panel-heading p {
        margin: 0;
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* FORM STYLES */
    .an-form {
        display: grid;
        gap: 15px;
    }
    .an-input-group label {
        display: block;
        margin-bottom: 6px;
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .an-input-wrap {
        position: relative;
        display: flex;
    }
    .an-input-wrap i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--spekta-gray);
        font-size: 12px;
        pointer-events: none;
    }
    .an-input-wrap input,
    .an-input-wrap textarea {
        width: 100%;
        border: 1px solid var(--border-soft);
        border-radius: 10px;
        background: var(--spekta-gray-light);
        font-size: 12px;
        color: var(--text-main);
        font-family: inherit;
        outline: none;
        transition: all 0.25s;
    }
    .an-input-wrap input {
        height: 40px;
        padding: 0 14px 0 38px;
    }
    .an-input-wrap input[type="file"] {
        padding-top: 10px;
    }
    .an-input-wrap.no-icon textarea {
        padding: 12px 14px;
        resize: vertical;
    }
    .an-input-wrap input:focus,
    .an-input-wrap textarea:focus {
        background: var(--spekta-white);
        border-color: var(--spekta-teal);
        box-shadow: 0 0 0 3px rgba(46, 168, 171, 0.12);
    }
    .an-form-action {
        display: flex;
        justify-content: flex-end;
        margin-top: 8px;
    }
    .an-submit-btn {
        background: linear-gradient(135deg, var(--spekta-red) 0%, var(--spekta-red-dark) 100%);
        color: var(--spekta-white);
        border: none;
        padding: 11px 20px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        box-shadow: 0 4px 10px rgba(229, 57, 53, 0.15);
    }
    .an-submit-btn:hover { 
        transform: translateY(-1px); 
        box-shadow: 0 6px 15px rgba(229, 57, 53, 0.25); 
    }

    /* PREVIEW PANEL STYLES */
    .an-preview-image {
        width: 100%;
        height: 180px;
        border-radius: 12px;
        border: 1px dashed var(--spekta-gray);
        background: var(--spekta-gray-light);
        display: grid;
        place-items: center;
        margin-bottom: 16px;
    }
    .an-preview-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 700;
    }
    .an-preview-empty i {
        font-size: 24px;
        margin-bottom: 6px;
        color: var(--spekta-gray);
    }
    .an-preview-note {
        display: flex;
        gap: 8px;
        padding: 12px;
        border-radius: 10px;
        background: #fff7f9;
        color: var(--text-muted);
        font-size: 11px;
        line-height: 1.5;
        font-weight: 600;
    }
    .an-preview-note i { color: var(--spekta-red); margin-top: 2px; }

    /* LIST PANEL & CARDS */
    .an-list-panel {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
    }
    .an-card-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    .an-card {
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        background: var(--spekta-white);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.25s ease;
    }
    .an-card:hover {
        border-color: var(--spekta-gray);
        box-shadow: 0 8px 20px rgba(0,0,0,0.03);
        transform: translateY(-2px);
    }
    .an-card-image {
        height: 150px;
        background: var(--spekta-gray-light);
        position: relative;
        overflow: hidden;
        border-bottom: 1px solid var(--border-soft);
    }
    .an-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .an-no-image {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 700;
    }
    .an-no-image i { font-size: 24px; margin-bottom: 6px; color: var(--spekta-gray); }
    .an-date-badge {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(31, 41, 55, 0.85);
        color: var(--spekta-white);
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
        backdrop-filter: blur(4px);
    }
    .an-card-body {
        padding: 16px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .an-card-body h3 {
        margin: 0 0 6px;
        font-size: 14px;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.4;
    }
    .an-card-body p {
        margin: 0;
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.6;
        flex-grow: 1;
        font-weight: 600;
    }
    .an-card-meta {
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid var(--spekta-gray-light);
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 700;
        display: flex;
        align-items: center;
    }
    .an-card-meta span { display: flex; align-items: center; gap: 4px; }

    .an-actions {
        display: flex;
        gap: 8px;
        margin-top: 14px;
    }
    .an-actions form { flex: 1; margin: 0; }
    .an-actions a, .an-actions button {
        width: 100%;
        height: 32px;
        border: none;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
        font-family: inherit;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .an-actions .edit { background: var(--spekta-gray-light); color: var(--text-main); border: 1px solid var(--border-soft); }
    .an-actions .edit:hover { background: var(--border-soft); }
    .an-actions .delete { background: var(--spekta-red-light); color: var(--spekta-red); }
    .an-actions .delete:hover { background: #fecaca; }

    /* EMPTY STATE */
    .an-empty {
        grid-column: 1 / -1;
        text-align: center;
        padding: 48px;
        background: var(--spekta-gray-light);
        border-radius: 12px;
        border: 1px dashed var(--border-soft);
    }
    .an-empty-icon {
        width: 48px;
        height: 48px;
        background: var(--spekta-white);
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 18px;
        color: var(--spekta-gray);
        margin: 0 auto 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }
    .an-empty strong { display: block; font-size: 14px; color: var(--text-main); margin-bottom: 4px; font-weight: 800;}
    .an-empty p { margin: 0; font-size: 12px; color: var(--text-muted); font-weight: 600; }

    .an-pagination { margin-top: 20px; }

    /* RESPONSIVE */
    @media (max-width: 1200px) {
        .an-card-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 900px) {
        .an-header { flex-direction: column; align-items: flex-start; gap: 14px; }
        .an-stats { grid-template-columns: 1fr; }
        .an-main-grid { grid-template-columns: 1fr; }
        .an-preview-panel { position: static; }
        .an-card-grid { grid-template-columns: 1fr; }
        .an-form-action { justify-content: flex-start; }
        .an-submit-btn { width: 100%; justify-content: center;}
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/announcement/index.blade.php ENDPATH**/ ?>