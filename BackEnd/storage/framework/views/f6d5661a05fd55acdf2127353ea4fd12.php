<?php $__env->startSection('title', 'Promo Management'); ?>
<?php $__env->startSection('subtitle', 'Kelola strategi diskon Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $promoCollection = method_exists($promos, 'getCollection') ? $promos->getCollection() : collect($promos);
    $totalPromo = method_exists($promos, 'total') ? $promos->total() : $promoCollection->count();
    $totalQuota = $promoCollection->sum('quota');
?>

<div class="pm-page">

    
    <section class="pm-header">
        <div class="pm-header-text">
            <span class="pm-kicker">Promosi & Informasi</span>
            <h1>Manajemen Promo</h1>
            <p>Kelola strategi diskon untuk setiap program kelas Spekta Academy.</p>
        </div>

        <a href="#promoForm" class="pm-primary-btn">
            <i class="fa-solid fa-plus"></i>
            Buat Promo Baru
        </a>
    </section>

    
    <?php if(session('success')): ?>
        <div class="pm-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <div>
                <strong>Berhasil!</strong>
                <span><?php echo e(session('success')); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="pm-alert error">
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

    
    <section class="pm-stats">
        <div class="pm-stat-card">
            <div class="pm-stat-icon">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div class="pm-stat-info">
                <p>Total Promo</p>
                <h2><?php echo e(number_format($totalPromo)); ?></h2>
            </div>
        </div>

        <div class="pm-stat-card">
            <div class="pm-stat-icon info-icon">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <div class="pm-stat-info">
                <p>Total Kuota</p>
                <h2><?php echo e(number_format($totalQuota)); ?></h2>
            </div>
        </div>
    </section>

    
    <section class="pm-form-panel" id="promoForm">
        <div class="pm-panel-heading">
            <div>
                <h2>Buat Kode Promo Baru</h2>
                <p>Masukkan kode promo, target kelas, besaran diskon, kuota, dan periode promo.</p>
            </div>
        </div>

        <form action="<?php echo e(route('admin.promo.store')); ?>" method="POST" class="pm-form" id="promoFormElement">
            <?php echo csrf_field(); ?>

            <div class="pm-input-group">
                <label>Kode Promo</label>
                <div class="pm-input-wrap">
                    <i class="fa-solid fa-ticket"></i>
                    <input type="text" name="code" id="promoCode" value="<?php echo e(old('code')); ?>" placeholder="CONTOH: SPEKTA50" required style="text-transform: uppercase;">
                </div>
                <small class="error-msg" id="codeError" style="color: #b91c1c; font-size: 10px; display: none; margin-top: 4px;">Kode promo harus diisi</small>
            </div>

            <div class="pm-input-group">
                <label>Target Kelas</label>
                <div class="pm-input-wrap">
                    <i class="fa-solid fa-layer-group"></i>
                    <select name="class_id" id="classId" required>
                        <option value="">Pilih Kelas</option>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->class_id); ?>" <?php echo e(old('class_id') == $c->class_id ? 'selected' : ''); ?>>
                                <?php echo e($c->program_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <small class="error-msg" id="classError" style="color: #b91c1c; font-size: 10px; display: none; margin-top: 4px;">Pilih target kelas</small>
            </div>

            <div class="pm-input-group">
                <label>Besar Diskon</label>
                <div class="pm-discount-wrap">
                    <i class="fa-solid fa-percent"></i>
                    <input type="number" name="discount_value" id="discountValue" value="<?php echo e(old('discount_value')); ?>" placeholder="Nilai diskon" required min="1">
                    <button type="button" id="btn-toggle-type" onclick="toggleDiscountType()">
                        <span id="display-type">%</span>
                    </button>
                </div>
                <small class="error-msg" id="discountError" style="color: #b91c1c; font-size: 10px; display: none; margin-top: 4px;">Diskon harus diisi dengan angka positif</small>
            </div>
            <input type="hidden" name="discount_type" id="input_discount_type" value="<?php echo e(old('discount_type', 'percent')); ?>">

            <div class="pm-input-group">
                <label>Kuota Penggunaan</label>
                <div class="pm-input-wrap">
                    <i class="fa-solid fa-users"></i>
                    <input type="number" name="quota" id="quota" value="<?php echo e(old('quota', 100)); ?>" placeholder="Contoh: 100" min="1" required>
                </div>
                <small class="error-msg" id="quotaError" style="color: #b91c1c; font-size: 10px; display: none; margin-top: 4px;">Kuota minimal 1</small>
            </div>

            <div class="pm-input-group">
                <label>Tanggal Mulai</label>
                <div class="pm-input-wrap">
                    <i class="fa-regular fa-calendar"></i>
                    <input type="date" name="start_date" id="startDate" value="<?php echo e(old('start_date')); ?>" required min="<?php echo e(date('Y-m-d')); ?>">
                </div>
                <small class="error-msg" id="startDateError" style="color: #b91c1c; font-size: 10px; display: none; margin-top: 4px;">Tanggal mulai tidak boleh kurang dari hari ini</small>
            </div>

            <div class="pm-input-group">
                <label>Tanggal Berakhir</label>
                <div class="pm-input-wrap">
                    <i class="fa-regular fa-calendar-check"></i>
                    <input type="date" name="end_date" id="endDate" value="<?php echo e(old('end_date')); ?>" required>
                </div>
                <small class="error-msg" id="endDateError" style="color: #b91c1c; font-size: 10px; display: none; margin-top: 4px;">Tanggal berakhir harus minimal 1 hari setelah tanggal mulai</small>
            </div>

            <div class="pm-form-action">
                <button type="submit" class="pm-submit-btn">
                    Simpan & Terbitkan Promo
                </button>
            </div>
        </form>
    </section>

    
    <section class="pm-promo-panel">
        <div class="pm-panel-heading">
            <div>
                <h2>Promo Berjalan</h2>
                <p>Daftar kode promo yang sudah dibuat untuk program kelas.</p>
            </div>
        </div>

        <div class="pm-promo-grid">
            <?php $__empty_1 = true; $__currentLoopData = $promos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $discountType = $row->discount_type ?? 'percent';
                    $discountValue = $row->discount_value ?? $row->discount_percent ?? 0;

                    $discountText = $discountType === 'fixed'
                        ? 'Rp ' . number_format($discountValue, 0, ',', '.')
                        : (int) $discountValue . '%';

                    $startDate = $row->start_date ? \Carbon\Carbon::parse($row->start_date) : null;
                    $endDate = $row->end_date ? \Carbon\Carbon::parse($row->end_date) : null;

                    $isExpired = $endDate ? now()->greaterThan($endDate->endOfDay()) : false;
                    $isActive = ($row->is_active ?? true) && !$isExpired;
                ?>

                <article class="pm-promo-card">
                    <div class="pm-promo-top">
                        <div class="pm-promo-title-area">
                            <span class="pm-code"><?php echo e($row->code); ?></span>
                            <h3><?php echo e($row->class->program_name ?? 'Program tidak tersedia'); ?></h3>
                        </div>
                        <div class="pm-discount">
                            <strong><?php echo e($discountText); ?></strong>
                            <small>Potongan</small>
                        </div>
                    </div>

                    <div class="pm-promo-meta">
                        <div class="meta-item">
                            <span>Kuota</span>
                            <strong><?php echo e(number_format($row->quota ?? 0)); ?></strong>
                        </div>
                        <div class="meta-item">
                            <span>Status</span>
                            <?php if($isActive): ?>
                                <strong class="status-active">Aktif</strong>
                            <?php else: ?>
                                <strong class="status-inactive">Nonaktif</strong>
                            <?php endif; ?>
                        </div>
                        <div class="meta-item">
                            <span>Mulai</span>
                            <strong><?php echo e($startDate ? $startDate->translatedFormat('d M Y') : '-'); ?></strong>
                        </div>
                        <div class="meta-item">
                            <span>Berakhir</span>
                            <strong><?php echo e($endDate ? $endDate->translatedFormat('d M Y') : '-'); ?></strong>
                        </div>
                    </div>

                    <form action="<?php echo e(route('admin.promo.destroy', $row->promotion_id)); ?>" method="POST" onsubmit="return confirm('Hapus promo ini secara permanen?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="pm-stop-btn">
                            <i class="fa-solid fa-trash-can"></i>
                            Hapus Promo
                        </button>
                    </form>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="pm-empty">
                    <div class="pm-empty-icon"><i class="fa-solid fa-ticket"></i></div>
                    <strong>Belum ada promo.</strong>
                    <p>Buat kode promo pertama Anda melalui form di atas.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if(method_exists($promos, 'hasPages') && $promos->hasPages()): ?>
            <div class="pm-pagination">
                <?php echo e($promos->links()); ?>

            </div>
        <?php endif; ?>
    </section>

</div>

<script>
    // Fungsi toggle tipe diskon
    function toggleDiscountType() {
        const inputType = document.getElementById('input_discount_type');
        const displayType = document.getElementById('display-type');

        if (!inputType || !displayType) return;

        if (inputType.value === 'percent') {
            inputType.value = 'fixed';
            displayType.innerText = 'Rp';
        } else {
            inputType.value = 'percent';
            displayType.innerText = '%';
        }
    }

    // Validasi sebelum submit
    document.addEventListener('DOMContentLoaded', function () {
        // Set initial display for discount type
        const inputType = document.getElementById('input_discount_type');
        const displayType = document.getElementById('display-type');
        if (inputType && displayType) {
            displayType.innerText = inputType.value === 'fixed' ? 'Rp' : '%';
        }

        // Ambil elemen-elemen yang diperlukan
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        const promoCodeInput = document.getElementById('promoCode');
        const classSelect = document.getElementById('classId');
        const discountValueInput = document.getElementById('discountValue');
        const quotaInput = document.getElementById('quota');
        const form = document.getElementById('promoFormElement');

        const startDateError = document.getElementById('startDateError');
        const endDateError = document.getElementById('endDateError');
        const codeError = document.getElementById('codeError');
        const classError = document.getElementById('classError');
        const discountError = document.getElementById('discountError');
        const quotaError = document.getElementById('quotaError');

        // Set min date untuk start_date (tidak boleh kurang dari hari ini)
        const today = new Date().toISOString().split('T')[0];
        if (startDateInput) {
            startDateInput.setAttribute('min', today);
        }

        // Fungsi validasi tanggal mulai (tidak boleh kurang dari hari ini)
        function validateStartDate() {
            const startDate = startDateInput.value;
            if (!startDate) return true;

            if (startDate < today) {
                startDateError.style.display = 'block';
                return false;
            } else {
                startDateError.style.display = 'none';
                return true;
            }
        }

        // Fungsi validasi tanggal berakhir (harus minimal 1 hari setelah tanggal mulai)
        function validateEndDate() {
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;

            if (!startDate || !endDate) return true;

            // Hitung tanggal minimal berakhir (start_date + 1 hari)
            const startDateObj = new Date(startDate);
            const minEndDate = new Date(startDateObj);
            minEndDate.setDate(startDateObj.getDate() + 1);

            const endDateObj = new Date(endDate);

            // Reset jam untuk perbandingan yang akurat
            minEndDate.setHours(0, 0, 0, 0);
            endDateObj.setHours(0, 0, 0, 0);

            if (endDateObj < minEndDate) {
                endDateError.style.display = 'block';
                return false;
            } else {
                endDateError.style.display = 'none';
                return true;
            }
        }

        // Fungsi validasi kode promo
        function validatePromoCode() {
            const code = promoCodeInput.value.trim();
            if (!code) {
                codeError.style.display = 'block';
                return false;
            } else {
                codeError.style.display = 'none';
                return true;
            }
        }

        // Fungsi validasi kelas
        function validateClass() {
            const classId = classSelect.value;
            if (!classId) {
                classError.style.display = 'block';
                return false;
            } else {
                classError.style.display = 'none';
                return true;
            }
        }

        // Fungsi validasi diskon
        function validateDiscount() {
            const discount = discountValueInput.value;
            if (!discount || discount <= 0) {
                discountError.style.display = 'block';
                return false;
            } else {
                discountError.style.display = 'none';
                return true;
            }
        }

        // Fungsi validasi kuota
        function validateQuota() {
            const quota = quotaInput.value;
            if (!quota || quota < 1) {
                quotaError.style.display = 'block';
                return false;
            } else {
                quotaError.style.display = 'none';
                return true;
            }
        }

        // Event listener untuk validasi real-time
        if (startDateInput) {
            startDateInput.addEventListener('change', function() {
                validateStartDate();
                validateEndDate(); // re-validasi end date jika start date berubah

                // Set min attribute untuk end date agar tidak bisa memilih tanggal yang sama atau sebelum start_date
                const startDate = startDateInput.value;
                if (startDate && endDateInput) {
                    const startDateObj = new Date(startDate);
                    const minEndDateObj = new Date(startDateObj);
                    minEndDateObj.setDate(startDateObj.getDate() + 1);
                    const minEndDateStr = minEndDateObj.toISOString().split('T')[0];
                    endDateInput.setAttribute('min', minEndDateStr);
                }
            });
        }

        if (endDateInput) {
            endDateInput.addEventListener('change', validateEndDate);
        }

        if (promoCodeInput) promoCodeInput.addEventListener('input', validatePromoCode);
        if (classSelect) classSelect.addEventListener('change', validateClass);
        if (discountValueInput) discountValueInput.addEventListener('input', validateDiscount);
        if (quotaInput) quotaInput.addEventListener('input', validateQuota);

        // Set initial min end date jika start date sudah terisi (misal dari old value)
        if (startDateInput && startDateInput.value && endDateInput) {
            const startDate = startDateInput.value;
            const startDateObj = new Date(startDate);
            const minEndDateObj = new Date(startDateObj);
            minEndDateObj.setDate(startDateObj.getDate() + 1);
            const minEndDateStr = minEndDateObj.toISOString().split('T')[0];
            endDateInput.setAttribute('min', minEndDateStr);
        }

        // Validasi sebelum submit
        if (form) {
            form.addEventListener('submit', function(e) {
                const isStartDateValid = validateStartDate();
                const isEndDateValid = validateEndDate();
                const isCodeValid = validatePromoCode();
                const isClassValid = validateClass();
                const isDiscountValid = validateDiscount();
                const isQuotaValid = validateQuota();

                if (!isStartDateValid || !isEndDateValid || !isCodeValid || !isClassValid || !isDiscountValid || !isQuotaValid) {
                    e.preventDefault();
                    let errorMsg = 'Harap periksa kembali data yang Anda masukkan:\n';
                    if (!isCodeValid) errorMsg += '- Kode promo tidak boleh kosong\n';
                    if (!isClassValid) errorMsg += '- Pilih target kelas\n';
                    if (!isDiscountValid) errorMsg += '- Diskon harus diisi dengan angka positif\n';
                    if (!isQuotaValid) errorMsg += '- Kuota minimal 1\n';
                    if (!isStartDateValid) errorMsg += '- Tanggal mulai tidak boleh kurang dari hari ini\n';
                    if (!isEndDateValid) errorMsg += '- Tanggal berakhir harus minimal 1 hari setelah tanggal mulai\n';
                    alert(errorMsg);
                }
            });
        }
    });
</script>

<style>
    /* BASE LAYOUT */
    .pm-page {
        width: 100%;
        font-family: 'Inter', system-ui, sans-serif;
        color: #334155;
    }

    /* HEADER */
    .pm-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        gap: 20px;
    }
    .pm-kicker {
        display: block;
        color: #d90429;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .pm-header h1 {
        margin: 0 0 8px;
        color: #0f172a;
        font-size: 28px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .pm-header p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }
    .pm-primary-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #d90429;
        color: #fff;
        border-radius: 12px;
        padding: 12px 20px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(217, 4, 41, 0.2);
    }
    .pm-primary-btn:hover {
        background: #b80222;
        transform: translateY(-1px);
    }

    /* ALERTS */
    .pm-alert {
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 14px;
    }
    .pm-alert.success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .pm-alert.error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .pm-alert strong { display: block; margin-bottom: 4px; font-weight: 700;}
    .pm-alert ul { margin: 4px 0 0; padding-left: 20px; }

    /* STATS */
    .pm-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    .pm-stat-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }
    .pm-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 20px;
        background: #fef2f2;
        color: #d90429;
    }
    .info-icon { background: #eff6ff; color: #2563eb; }
    .pm-stat-info p {
        margin: 0 0 4px;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .pm-stat-info h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    /* PANELS (FORM & LIST) */
    .pm-form-panel, .pm-promo-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 24px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
    }
    .pm-panel-heading {
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    .pm-panel-heading h2 {
        margin: 0 0 6px;
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }
    .pm-panel-heading p {
        margin: 0;
        font-size: 13px;
        color: #64748b;
    }

    /* FORM STYLES */
    .pm-form {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    .pm-input-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
    }
    .pm-input-wrap, .pm-discount-wrap {
        position: relative;
        display: flex;
    }
    .pm-input-wrap i, .pm-discount-wrap > i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 14px;
        pointer-events: none;
    }
    .pm-input-wrap input, .pm-input-wrap select, .pm-discount-wrap input {
        width: 100%;
        height: 46px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #f8fafc;
        padding: 0 14px 0 40px;
        font-size: 14px;
        color: #1e293b;
        font-family: inherit;
        outline: none;
        transition: all 0.2s;
    }
    .pm-input-wrap input:focus, .pm-input-wrap select:focus, .pm-discount-wrap input:focus {
        background: #fff;
        border-color: #d90429;
        box-shadow: 0 0 0 3px rgba(217, 4, 41, 0.1);
    }

    /* Discount special input */
    .pm-discount-wrap input {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-right: none;
    }
    .pm-discount-wrap button {
        width: 60px;
        border: 1px solid #d90429;
        background: #d90429;
        color: #fff;
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
        font-weight: 800;
        cursor: pointer;
        transition: background 0.2s;
    }
    .pm-discount-wrap button:hover { background: #b80222; }

    .pm-form-action {
        grid-column: 1 / -1;
        display: flex;
        justify-content: flex-end;
        margin-top: 10px;
    }
    .pm-submit-btn {
        background: #d90429;
        color: #fff;
        border: none;
        padding: 14px 28px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
    }
    .pm-submit-btn:hover { background: #b80222; }

    /* PROMO GRID & CARDS */
    .pm-promo-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    .pm-promo-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .pm-promo-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
        transform: translateY(-2px);
    }
    .pm-promo-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
    }
    .pm-code {
        display: inline-block;
        background: #fef2f2;
        color: #b91c1c;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
        border: 1px solid #fecaca;
    }
    .pm-promo-title-area h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.4;
    }
    .pm-discount { text-align: right; }
    .pm-discount strong { display: block; font-size: 22px; font-weight: 900; color: #d90429; line-height: 1;}
    .pm-discount small { font-size: 10px; color: #64748b; font-weight: 600; text-transform: uppercase;}

    .pm-promo-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        background: #f8fafc;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 16px;
        flex-grow: 1;
    }
    .meta-item span {
        display: block;
        font-size: 10px;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    .meta-item strong {
        font-size: 13px;
        color: #1e293b;
        font-weight: 700;
    }
    .status-active { color: #059669 !important; }
    .status-inactive { color: #ea580c !important; }

    .pm-stop-btn {
        width: 100%;
        background: #fff;
        border: 1px solid #e2e8f0;
        color: #64748b;
        padding: 10px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .pm-stop-btn:hover {
        background: #fef2f2;
        border-color: #fecaca;
        color: #dc2626;
    }

    /* EMPTY STATE */
    .pm-empty {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        background: #f8fafc;
        border-radius: 16px;
        border: 1px dashed #cbd5e1;
    }
    .pm-empty-icon {
        width: 64px;
        height: 64px;
        background: #fff;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 24px;
        color: #94a3b8;
        margin: 0 auto 16px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .pm-empty strong { display: block; font-size: 16px; color: #1e293b; margin-bottom: 4px;}
    .pm-empty p { margin: 0; font-size: 14px; color: #64748b; }

    .error-msg {
        display: block;
    }

    /* RESPONSIVE */
    @media (max-width: 1200px) {
        .pm-promo-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .pm-header { flex-direction: column; align-items: flex-start; }
        .pm-form { grid-template-columns: 1fr; }
        .pm-stats, .pm-promo-grid { grid-template-columns: 1fr; }
        .pm-form-action { justify-content: flex-start; }
        .pm-submit-btn { width: 100%; }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/promo/index.blade.php ENDPATH**/ ?>