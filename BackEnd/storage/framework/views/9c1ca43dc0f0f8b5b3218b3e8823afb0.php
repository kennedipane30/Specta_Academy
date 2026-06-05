<?php $__env->startSection('title', 'Management - Announcement'); ?>
<?php $__env->startSection('subtitle', 'Sistem Manajemen Pengumuman Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $announcementCollection = method_exists($announcements, 'getCollection') ? $announcements->getCollection() : collect($announcements);
    $totalAnnouncement = method_exists($announcements, 'total') ? $announcements->total() : $announcementCollection->count();
    $latestAnnouncement = $announcementCollection->sortByDesc('created_at')->first();
?>

<div class="an-page">

    <section class="an-header">
        <div>
            <span>Promosi & Informasi</span>
            <h1>Management Announcement</h1>
            <p>Buat, kelola, dan publikasikan pengumuman untuk pengguna Spekta Academy.</p>
        </div>

        <a href="#announcementForm" class="an-primary-btn">
            <i class="fa-solid fa-plus"></i>
            Buat Pengumuman
        </a>
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
        <div class="an-stat-card">
            <div class="an-stat-icon">
                <i class="fa-solid fa-bullhorn"></i>
            </div>
            <p>Total Pengumuman</p>
            <h2><?php echo e(number_format($totalAnnouncement)); ?></h2>
            <div class="an-stat-meta">
                <span class="info">Publikasi</span>
                <small>semua data</small>
            </div>
        </div>

        <div class="an-stat-card">
            <div class="an-stat-icon">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
            <p>Bulan Ini</p>
            <h2><?php echo e(number_format($announcementCollection->filter(fn($item) => $item->created_at && $item->created_at->isCurrentMonth())->count())); ?></h2>
            <div class="an-stat-meta">
                <span class="success">Aktif</span>
                <small>posting terbaru</small>
            </div>
        </div>

        <div class="an-stat-card">
            <div class="an-stat-icon">
                <i class="fa-solid fa-image"></i>
            </div>
            <p>Dengan Gambar</p>
            <h2><?php echo e(number_format($announcementCollection->filter(fn($item) => !empty($item->image))->count())); ?></h2>
            <div class="an-stat-meta">
                <span class="info">Media</span>
                <small>cover image</small>
            </div>
        </div>

        <div class="an-stat-card">
            <div class="an-stat-icon">
                <i class="fa-solid fa-clock"></i>
            </div>
            <p>Terakhir Dibuat</p>
            <h2><?php echo e($latestAnnouncement?->created_at ? $latestAnnouncement->created_at->diffForHumans(null, true) : '-'); ?></h2>
            <div class="an-stat-meta">
                <span class="warning">Update</span>
                <small>pengumuman</small>
            </div>
        </div>
    </section>

    <section class="an-main-grid">

        <div class="an-form-panel" id="announcementForm">
            <div class="an-panel-heading">
                <div>
                    <h2>Create New Announcement</h2>
                    <p>Isi judul, gambar sampul, dan deskripsi pengumuman yang akan ditampilkan.</p>
                </div>

                <div class="an-heading-icon">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
            </div>

            <form action="<?php echo e(route('admin.announcement.store')); ?>" method="POST" enctype="multipart/form-data" class="an-form">
                <?php echo csrf_field(); ?>

                <div class="an-input-group">
                    <label>Announcement Title</label>
                    <div>
                        <i class="fa-solid fa-heading"></i>
                        <input
                            type="text"
                            name="title"
                            value="<?php echo e(old('title')); ?>"
                            placeholder="Enter headline..."
                            required
                        >
                    </div>
                </div>

                <div class="an-input-group">
                    <label>Cover Image</label>
                    <div>
                        <i class="fa-solid fa-upload"></i>
                        <input
                            type="file"
                            name="image"
                            id="announcementImageInput"
                            accept="image/*"
                            required
                        >
                    </div>
                </div>

                <div class="an-input-group full">
                    <label>Full Description</label>
                    <textarea
                        name="description"
                        rows="5"
                        placeholder="Write the announcement details here..."
                        required
                    ><?php echo e(old('description')); ?></textarea>
                </div>

                <button type="submit" class="an-submit">
                    <i class="fa-solid fa-paper-plane"></i>
                    Publish Announcement Now
                </button>
            </form>
        </div>

        <aside class="an-preview-panel">
            <div class="an-preview-heading">
                <h3>Preview Cover</h3>
                <p>Pratinjau gambar pengumuman yang akan diunggah.</p>
            </div>

            <div class="an-preview-image" id="announcementPreviewBox">
                <div>
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
                        <p><?php echo e(\Illuminate\Support\Str::limit($row->description, 150)); ?></p>

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
                                    <i class="fa-solid fa-trash"></i>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="an-empty">
                    <i class="fa-solid fa-bullhorn"></i>
                    <strong>Belum ada pengumuman.</strong>
                    <span>Buat pengumuman pertama melalui form di atas.</span>
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
    const announcementImageInput = document.getElementById('announcementImageInput');
    const announcementPreviewBox = document.getElementById('announcementPreviewBox');

    if (announcementImageInput && announcementPreviewBox) {
        announcementImageInput.addEventListener('change', function () {
            const file = this.files[0];

            if (!file) return;

            const reader = new FileReader();

            reader.onload = function (event) {
                announcementPreviewBox.innerHTML = `<img src="${event.target.result}" alt="Preview Announcement">`;
            };

            reader.readAsDataURL(file);
        });
    }
</script>

<style>
    .an-page {
        width: 100%;
    }

    .an-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 22px;
        margin-bottom: 22px;
    }

    .an-header span {
        display: block;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .18em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .an-header h1 {
        margin: 0 0 7px;
        color: #111827;
        font-size: 26px;
        font-weight: 900;
        letter-spacing: -0.03em;
        text-transform: uppercase;
    }

    .an-header p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        font-weight: 600;
    }

    .an-primary-btn {
        min-height: 46px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #d90429;
        color: #fff;
        border-radius: 12px;
        padding: 0 18px;
        font-size: 12px;
        font-weight: 900;
        box-shadow: 0 14px 28px rgba(217, 4, 41, .22);
        white-space: nowrap;
    }

    .an-alert {
        border-radius: 16px;
        padding: 15px 17px;
        margin-bottom: 18px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 800;
    }

    .an-alert.success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .an-alert.error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .an-alert strong {
        display: block;
        margin-bottom: 3px;
    }

    .an-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .an-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }

    .an-stat-card,
    .an-form-panel,
    .an-preview-panel,
    .an-list-panel {
        background: #fff;
        border: 1px solid #edf0f4;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .05);
    }

    .an-stat-card {
        border-radius: 20px;
        padding: 22px;
        min-height: 150px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .an-stat-icon {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        background: #ffe8ee;
        color: #d90429;
        border-radius: 15px;
        margin-bottom: 16px;
    }

    .an-stat-card p {
        margin: 0 0 8px;
        color: #6b7280;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .an-stat-card h2 {
        margin: 0 0 14px;
        color: #0f172a;
        font-size: 31px;
        font-weight: 900;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .an-stat-meta {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .an-stat-meta span {
        height: 23px;
        display: inline-flex;
        align-items: center;
        border-radius: 8px;
        padding: 0 9px;
        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
    }

    .an-stat-meta .success {
        background: #dcfce7;
        color: #16a34a;
    }

    .an-stat-meta .warning {
        background: #ffedd5;
        color: #ea580c;
    }

    .an-stat-meta .info {
        background: #dbeafe;
        color: #2563eb;
    }

    .an-stat-meta small {
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .an-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 390px;
        gap: 22px;
        align-items: start;
        margin-bottom: 22px;
    }

    .an-form-panel,
    .an-preview-panel,
    .an-list-panel {
        border-radius: 22px;
        padding: 22px;
    }

    .an-panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 22px;
    }

    .an-panel-heading h2,
    .an-preview-heading h3 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
    }

    .an-panel-heading p,
    .an-preview-heading p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
    }

    .an-heading-icon {
        width: 48px;
        height: 48px;
        display: grid;
        place-items: center;
        border-radius: 16px;
        background: #ffe8ee;
        color: #d90429;
        flex-shrink: 0;
    }

    .an-form {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .an-input-group.full,
    .an-submit {
        grid-column: 1 / -1;
    }

    .an-input-group label {
        display: block;
        margin-bottom: 8px;
        color: #374151;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .an-input-group div {
        position: relative;
    }

    .an-input-group i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 13px;
    }

    .an-input-group input,
    .an-input-group textarea {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
        outline: none;
        color: #111827;
        font-size: 13px;
        font-weight: 700;
        font-family: inherit;
    }

    .an-input-group input {
        height: 48px;
        padding: 0 15px 0 42px;
    }

    .an-input-group input[type="file"] {
        padding-top: 13px;
    }

    .an-input-group textarea {
        resize: vertical;
        padding: 14px 15px;
        line-height: 1.5;
    }

    .an-input-group input:focus,
    .an-input-group textarea:focus {
        background: #fff;
        border-color: #fecdd3;
        box-shadow: 0 0 0 4px rgba(217, 4, 41, .08);
    }

    .an-submit {
        height: 50px;
        border: none;
        border-radius: 14px;
        background: linear-gradient(90deg, #d90429, #ef233c);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        cursor: pointer;
        font-family: inherit;
        box-shadow: 0 14px 28px rgba(217, 4, 41, .20);
        margin-top: 4px;
    }

    .an-preview-panel {
        position: sticky;
        top: 20px;
    }

    .an-preview-heading {
        margin-bottom: 18px;
    }

    .an-preview-image {
        width: 100%;
        height: 230px;
        border-radius: 18px;
        border: 1px dashed #d1d5db;
        background: #f8fafc;
        overflow: hidden;
        display: grid;
        place-items: center;
        text-align: center;
        color: #6b7280;
        font-size: 12px;
        font-weight: 800;
    }

    .an-preview-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .an-preview-image i {
        display: block;
        color: #d90429;
        font-size: 30px;
        margin-bottom: 8px;
    }

    .an-preview-note {
        margin-top: 16px;
        display: flex;
        gap: 10px;
        padding: 14px;
        border-radius: 14px;
        background: #fff7f9;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.5;
    }

    .an-preview-note i {
        color: #d90429;
        margin-top: 2px;
    }

    .an-card-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .an-card {
        border: 1px solid #edf0f4;
        border-radius: 22px;
        background: #fff;
        overflow: hidden;
        transition: .2s ease;
    }

    .an-card:hover {
        border-color: #fecdd3;
        box-shadow: 0 16px 35px rgba(15, 23, 42, .07);
        transform: translateY(-2px);
    }

    .an-card-image {
        height: 205px;
        background: #f8fafc;
        position: relative;
        overflow: hidden;
    }

    .an-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: .4s ease;
    }

    .an-card:hover .an-card-image img {
        transform: scale(1.05);
    }

    .an-no-image {
        width: 100%;
        height: 100%;
        display: grid;
        place-items: center;
        color: #9ca3af;
        font-size: 12px;
        font-weight: 800;
    }

    .an-date-badge {
        position: absolute;
        left: 14px;
        bottom: 14px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 0 12px;
        background: rgba(17, 24, 39, .82);
        color: #fff;
        font-size: 10px;
        font-weight: 900;
        backdrop-filter: blur(8px);
    }

    .an-card-body {
        padding: 20px;
    }

    .an-card-body h3 {
        margin: 0 0 9px;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
        line-height: 1.35;
        text-transform: uppercase;
    }

    .an-card-body p {
        margin: 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.55;
        min-height: 58px;
    }

    .an-card-meta {
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid #edf0f4;
        color: #9ca3af;
        font-size: 11px;
        font-weight: 800;
    }

    .an-card-meta span {
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .an-actions {
        margin-top: 16px;
        display: flex;
        gap: 10px;
    }

    .an-actions form {
        flex: 1;
        margin: 0;
    }

    .an-actions a,
    .an-actions button {
        width: 100%;
        height: 40px;
        border: none;
        border-radius: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 11px;
        font-weight: 900;
        cursor: pointer;
        font-family: inherit;
    }

    .an-actions .edit {
        flex: 1;
        background: #ffedd5;
        color: #ea580c;
    }

    .an-actions .delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .an-empty {
        grid-column: 1 / -1;
        padding: 45px;
        text-align: center;
        background: #f8fafc;
        border-radius: 18px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .an-empty i {
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

    .an-empty strong {
        display: block;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    .an-pagination {
        margin-top: 18px;
    }

    @media (max-width: 1450px) {
        .an-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .an-main-grid {
            grid-template-columns: 1fr;
        }

        .an-preview-panel {
            position: static;
        }

        .an-card-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .an-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .an-stats,
        .an-card-grid {
            grid-template-columns: 1fr;
        }

        .an-form {
            grid-template-columns: 1fr;
        }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/announcement/index.blade.php ENDPATH**/ ?>