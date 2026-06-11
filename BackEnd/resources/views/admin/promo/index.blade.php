@extends('layouts.spekta')

@section('title', 'Promo Management')
@section('subtitle', 'Kelola strategi diskon Spekta Academy')

@section('content')
@php
    $promoCollection = method_exists($promos, 'getCollection') ? $promos->getCollection() : collect($promos);
    $totalPromo = method_exists($promos, 'total') ? $promos->total() : $promoCollection->count();
    $totalQuota = $promoCollection->sum('quota');

    // KALKULASI DINAMIS METRIK KE-3: Promo Aktif & Belum Kadaluarsa
    $activePromoCount = $promoCollection->filter(function($row) {
        $endDate = $row->end_date ? \Carbon\Carbon::parse($row->end_date) : null;
        $isExpired = $endDate ? now()->greaterThan($endDate->endOfDay()) : false;
        return ($row->is_active ?? true) && !$isExpired;
    })->count();
@endphp

<div class="pm-page">

    {{-- ── 1. HEADER (BREADCRUMB CAPSULE & SHARP TITLE) ── --}}
    <section class="pm-header">
        <div class="pm-header-text">
            <span class="pm-kicker">Promosi & Informasi</span>
            <h1>Manajemen Promo</h1>
            <p>Kelola strategi diskon untuk setiap program kelas Spekta Academy.</p>
        </div>

        <!-- Tombol Pemicu Buka-Tutup Form (Sangat Interaktif) -->
        <button type="button" class="pm-primary-btn" onclick="togglePromoForm()">
            <i class="fa-solid fa-plus" id="toggle-icon"></i>
            <span id="toggle-text">Buat Promo Baru</span>
        </button>
    </section>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="pm-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <div>
                <strong>Berhasil!</strong>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="pm-alert error">
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

    {{-- ── 2. STATS GRID (KINI BERJUMLAH 3 DAN SEIMBANG) ── --}}
    <section class="pm-stats">
        <!-- Total Promo -->
        <div class="pm-stat-card card-red">
            <div class="pm-stat-icon red">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div class="pm-stat-info">
                <p>Total Promo</p>
                <h2>{{ number_format($totalPromo) }}</h2>
            </div>
        </div>

        <!-- Total Kuota -->
        <div class="pm-stat-card card-teal">
            <div class="pm-stat-icon teal">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <div class="pm-stat-info">
                <p>Total Kuota</p>
                <h2>{{ number_format($totalQuota) }}</h2>
            </div>
        </div>

        <!-- Promo Aktif -->
        <div class="pm-stat-card card-orange">
            <div class="pm-stat-icon orange">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="pm-stat-info">
                <p>Promo Aktif</p>
                <h2>{{ number_format($activePromoCount) }}</h2>
            </div>
            @if($activePromoCount > 0)
                <span class="pm-pulse-dot"></span>
            @endif
        </div>
    </section>

    {{-- ── 3. FORM PANEL (COLLAPSIBLE / ANIMASI BUKA-TUTUP NYATA) ── --}}
    <section class="pm-form-panel" id="promoForm">
        <div class="pm-panel-heading">
            <div>
                <h2>Buat Kode Promo Baru</h2>
                <p>Masukkan kode promo, target kelas, besaran diskon, kuota, dan periode promo.</p>
            </div>
        </div>

        <form action="{{ route('admin.promo.store') }}" method="POST" class="pm-form" id="promoFormElement">
            @csrf

            <div class="pm-input-group">
                <label>Kode Promo</label>
                <div class="pm-input-wrap">
                    <i class="fa-solid fa-ticket"></i>
                    <input type="text" name="code" id="promoCode" value="{{ old('code') }}" placeholder="CONTOH: SPEKTA50" required style="text-transform: uppercase;">
                </div>
                <small class="error-msg" id="codeError" style="color: #e53935; font-size: 10px; display: none; margin-top: 4px; font-weight: 700;">Kode promo harus diisi</small>
            </div>

            <div class="pm-input-group">
                <label>Target Kelas</label>
                <div class="pm-input-wrap">
                    <i class="fa-solid fa-layer-group"></i>
                    <select name="class_id" id="classId" required>
                        <option value="">Pilih Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->class_id }}" {{ old('class_id') == $c->class_id ? 'selected' : '' }}>
                                {{ $c->program_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <small class="error-msg" id="classError" style="color: #e53935; font-size: 10px; display: none; margin-top: 4px; font-weight: 700;">Pilih target kelas</small>
            </div>

            <div class="pm-input-group">
                <label>Besar Diskon</label>
                <div class="pm-discount-wrap">
                    <i class="fa-solid fa-percent"></i>
                    <input type="number" name="discount_value" id="discountValue" value="{{ old('discount_value') }}" placeholder="Nilai diskon" required min="1">
                    <button type="button" id="btn-toggle-type" onclick="toggleDiscountType()" title="Klik untuk mengubah mata uang">
                        <span id="display-type">%</span>
                    </button>
                </div>
                <small class="error-msg" id="discountError" style="color: #e53935; font-size: 10px; display: none; margin-top: 4px; font-weight: 700;">Diskon harus diisi dengan angka positif</small>
            </div>
            <input type="hidden" name="discount_type" id="input_discount_type" value="{{ old('discount_type', 'percent') }}">

            <div class="pm-input-group">
                <label>Kuota Penggunaan</label>
                <div class="pm-input-wrap">
                    <i class="fa-solid fa-users"></i>
                    <input type="number" name="quota" id="quota" value="{{ old('quota', 100) }}" placeholder="Contoh: 100" min="1" required>
                </div>
                <small class="error-msg" id="quotaError" style="color: #e53935; font-size: 10px; display: none; margin-top: 4px; font-weight: 700;">Kuota minimal 1</small>
            </div>

            <div class="pm-input-group">
                <label>Tanggal Mulai</label>
                <div class="pm-input-wrap">
                    <i class="fa-regular fa-calendar"></i>
                    <input type="date" name="start_date" id="startDate" value="{{ old('start_date') }}" required min="{{ date('Y-m-d') }}">
                </div>
                <small class="error-msg" id="startDateError" style="color: #e53935; font-size: 10px; display: none; margin-top: 4px; font-weight: 700;">Tanggal mulai tidak boleh kurang dari hari ini</small>
            </div>

            <div class="pm-input-group">
                <label>Tanggal Berakhir</label>
                <div class="pm-input-wrap">
                    <i class="fa-regular fa-calendar-check"></i>
                    <input type="date" name="end_date" id="endDate" value="{{ old('end_date') }}" required>
                </div>
                <small class="error-msg" id="endDateError" style="color: #e53935; font-size: 10px; display: none; margin-top: 4px; font-weight: 700;">Tanggal berakhir harus minimal 1 hari setelah tanggal mulai</small>
            </div>

            <div class="pm-form-action">
                <button type="submit" class="pm-submit-btn">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Simpan & Terbitkan Promo
                </button>
            </div>
        </form>
    </section>

    {{-- ── 4. PROMO LIST SECTION (TAMPILAN CARD DENGAN ACCENT NEON) ── --}}
    <section class="pm-promo-panel">
        <div class="pm-panel-heading">
            <div>
                <h2>Promo Berjalan</h2>
                <p>Daftar kode promo yang sudah dibuat untuk program kelas.</p>
            </div>
        </div>

        <div class="pm-promo-grid">
            @forelse($promos as $row)
                @php
                    $discountType = $row->discount_type ?? 'percent';
                    $discountValue = $row->discount_value ?? $row->discount_percent ?? 0;

                    $discountText = $discountType === 'fixed'
                        ? 'Rp ' . number_format($discountValue, 0, ',', '.')
                        : (int) $discountValue . '%';

                    $startDate = $row->start_date ? \Carbon\Carbon::parse($row->start_date) : null;
                    $endDate = $row->end_date ? \Carbon\Carbon::parse($row->end_date) : null;

                    $isExpired = $endDate ? now()->greaterThan($endDate->endOfDay()) : false;
                    $isActive = ($row->is_active ?? true) && !$isExpired;
                @endphp

                <article class="pm-promo-card">
                    <div class="pm-promo-top">
                        <div class="pm-promo-title-area">
                            <span class="pm-code">{{ $row->code }}</span>
                            <h3>{{ $row->class->program_name ?? 'Program tidak tersedia' }}</h3>
                        </div>
                        <div class="pm-discount">
                            <strong>{{ $discountText }}</strong>
                            <small>Potongan</small>
                        </div>
                    </div>

                    <div class="pm-promo-meta">
                        <div class="meta-item">
                            <span>Kuota</span>
                            <strong>{{ number_format($row->quota ?? 0) }}</strong>
                        </div>
                        <div class="meta-item">
                            <span>Status</span>
                            @if($isActive)
                                <span class="pm-card-status active">
                                    <span class="pm-dot-wrapper">
                                        <i class="pm-dot"></i>
                                        <i class="pm-dot-pulse"></i>
                                    </span>
                                    Aktif
                                </span>
                            @else
                                <span class="pm-card-status inactive">
                                    <span class="pm-dot-wrapper">
                                        <i class="pm-dot"></i>
                                    </span>
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                        <div class="meta-item">
                            <span>Mulai</span>
                            <strong>{{ $startDate ? $startDate->translatedFormat('d M Y') : '-' }}</strong>
                        </div>
                        <div class="meta-item">
                            <span>Berakhir</span>
                            <strong>{{ $endDate ? $endDate->translatedFormat('d M Y') : '-' }}</strong>
                        </div>
                    </div>

                    <form action="{{ route('admin.promo.destroy', $row->promotion_id) }}" method="POST" onsubmit="return confirm('Hapus promo ini secara permanen?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="pm-stop-btn">
                            <i class="fa-solid fa-trash-can"></i>
                            Hapus Promo
                        </button>
                    </form>
                </article>
            @empty
                <div class="pm-empty">
                    <div class="pm-empty-icon"><i class="fa-solid fa-ticket"></i></div>
                    <strong>Belum ada promo.</strong>
                    <p>Buat kode promo pertama Anda melalui form di atas.</p>
                </div>
            @endforelse
        </div>

        @if(method_exists($promos, 'hasPages') && $promos->hasPages())
            <div class="pm-pagination">
                {{ $promos->links() }}
            </div>
        @endif
    </section>

</div>

<script>
    // FUNGSI TOGGLE FORM DENGAN ANIMASI HALUS (UX IMPROVEMENT)
    function togglePromoForm() {
        const formPanel = document.getElementById('promoForm');
        const toggleIcon = document.getElementById('toggle-icon');
        const toggleText = document.getElementById('toggle-text');

        if (formPanel.classList.contains('show')) {
            formPanel.classList.remove('show');
            toggleIcon.className = "fa-solid fa-plus";
            toggleText.innerText = "Buat Promo Baru";
        } else {
            formPanel.classList.add('show');
            toggleIcon.className = "fa-solid fa-minus";
            toggleText.innerText = "Sembunyikan Form";
            formPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

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
    .pm-page {
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
    .pm-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        gap: 20px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }
    .pm-kicker {
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
    .pm-header h1 {
        margin: 0 0 6px;
        color: var(--text-main);
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .pm-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }
    .pm-primary-btn {
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
    .pm-primary-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(229, 57, 53, 0.3);
        color: var(--spekta-white);
    }

    /* ALERTS */
    .pm-alert {
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 24px;
        display: flex;
        gap: 10px;
        align-items: center;
        font-size: 13px;
        font-weight: 800;
    }
    .pm-alert.success { background: #e6f7ed; color: #15803d; border: 1px solid #bbf7d0; }
    .pm-alert.error { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    .pm-alert strong { display: block; margin-bottom: 2px; font-weight: 800;}
    .pm-alert ul { margin: 4px 0 0; padding-left: 20px; }

    /* STATS (3 KOLOM SEIMBANG) */
    .pm-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .pm-stat-card {
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
    .pm-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.03);
    }
    .pm-stat-card.card-red:hover { border-color: var(--spekta-red); }
    .pm-stat-card.card-teal:hover { border-color: var(--spekta-teal); }
    .pm-stat-card.card-orange:hover { border-color: #d97706; }

    .pm-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-size: 16px;
    }
    .pm-stat-icon.red { background: var(--spekta-red-light); color: var(--spekta-red); }
    .pm-stat-icon.teal { background: var(--spekta-teal-light); color: var(--spekta-teal); }
    .pm-stat-icon.orange { background: rgba(217, 119, 6, 0.08); color: #d97706; }

    .pm-stat-info p {
        margin: 0 0 4px;
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .pm-stat-info h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1;
    }

    /* Indikator Denyut untuk Promo Aktif */
    .pm-pulse-dot {
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

    /* ── FORM PANEL COLLAPSIBLE (ANIMASI MELUNCUR BULAN/BINTANG) ── */
    .pm-form-panel {
        background: var(--spekta-white);
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
        margin-bottom: 24px;
        max-height: 0;
        overflow: hidden;
        padding: 0 20px;
        border-width: 0;
        opacity: 0;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease, padding 0.4s ease, border-width 0.4s ease;
    }
    .pm-form-panel.show {
        max-height: 800px;
        padding: 20px;
        border: 1px solid var(--border-soft);
        opacity: 1;
    }

    .pm-panel-heading {
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--spekta-gray-light);
    }
    .pm-panel-heading h2 {
        margin: 0 0 4px;
        font-size: 15px;
        font-weight: 800;
        color: var(--text-main);
    }
    .pm-panel-heading p {
        margin: 0;
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* FORM STYLES */
    .pm-form {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    .pm-input-group label {
        display: block;
        margin-bottom: 6px;
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.02em;
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
        color: var(--spekta-gray);
        font-size: 12px;
        pointer-events: none;
    }
    .pm-input-wrap input, .pm-input-wrap select, .pm-discount-wrap input {
        width: 100%;
        height: 40px;
        border: 1px solid var(--border-soft);
        border-radius: 10px;
        background: var(--spekta-gray-light);
        padding: 0 14px 0 38px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-main);
        font-family: inherit;
        outline: none;
        transition: all 0.25s;
    }
    .pm-input-wrap input:focus, .pm-input-wrap select:focus, .pm-discount-wrap input:focus {
        background: var(--spekta-white);
        border-color: var(--spekta-teal);
        box-shadow: 0 0 0 3px rgba(46, 168, 171, 0.12);
    }

    /* Perbaikan Input Type Diskon yang Minimalis & Elegan */
    .pm-discount-wrap input {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-right: none;
    }
    .pm-discount-wrap button {
        width: 50px;
        border: 1px solid var(--border-soft);
        border-left: none;
        background: var(--spekta-gray-light);
        color: var(--text-main);
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 12px;
    }
    .pm-discount-wrap button:hover { 
        background: var(--border-soft); 
        color: var(--spekta-red); 
    }

    .pm-form-action {
        grid-column: 1 / -1;
        display: flex;
        justify-content: flex-end;
        margin-top: 10px;
    }
    .pm-submit-btn {
        background: linear-gradient(135deg, var(--spekta-red) 0%, var(--spekta-red-dark) 100%);
        color: var(--spekta-white);
        border: none;
        padding: 11px 20px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 10px rgba(229, 57, 53, 0.15);
    }
    .pm-submit-btn:hover { 
        transform: translateY(-1px); 
        box-shadow: 0 6px 15px rgba(229, 57, 53, 0.25); 
    }

    /* PROMO GRID & CARDS (DESAIN SANGAT KECE) */
    .pm-promo-panel {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
    }

    .pm-promo-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    .pm-promo-card {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        transition: all 0.25s ease;
    }
    .pm-promo-card:hover {
        border-color: var(--spekta-gray);
        box-shadow: 0 8px 20px rgba(0,0,0,0.03);
        transform: translateY(-2px);
    }
    .pm-promo-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }
    .pm-code {
        display: inline-block;
        background: var(--spekta-red-light);
        color: var(--spekta-red-dark);
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
        border: 1px solid rgba(229, 57, 53, 0.1);
    }
    .pm-promo-title-area h3 {
        margin: 0;
        font-size: 14px;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.4;
    }
    .pm-discount { text-align: right; }
    .pm-discount strong { display: block; font-size: 20px; font-weight: 900; color: var(--spekta-red); line-height: 1;}
    .pm-discount small { font-size: 9px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;}

    .pm-promo-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        background: var(--spekta-gray-light);
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 14px;
        flex-grow: 1;
    }
    .meta-item span {
        display: block;
        font-size: 9px;
        color: var(--text-muted);
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    .meta-item strong {
        font-size: 12px;
        color: var(--text-main);
        font-weight: 700;
    }

    /* Glowing Badge Status */
    .pm-card-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 800;
    }
    .pm-dot-wrapper {
        position: relative;
        width: 5px; height: 5px;
        display: inline-block;
    }
    .pm-dot {
        width: 5px; height: 5px;
        border-radius: 99px;
        background: currentColor;
        display: block;
        position: absolute;
        left: 0; top: 0;
    }
    .pm-dot-pulse {
        width: 5px; height: 5px;
        border-radius: 99px;
        background: currentColor;
        display: block;
        position: absolute;
        left: 0; top: 0;
        opacity: 0.4;
        transform: scale(1);
        animation: dotGlow 1.8s infinite ease-in-out;
    }
    @keyframes dotGlow {
        0% { transform: scale(1); opacity: 0.8; }
        100% { transform: scale(3.2); opacity: 0; }
    }
    .pm-card-status.active { color: #15803d; }
    .pm-card-status.inactive { color: var(--spekta-gray); }

    .pm-stop-btn {
        width: 100%;
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        color: var(--text-muted);
        padding: 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .pm-stop-btn:hover {
        background: var(--spekta-red-light);
        border-color: rgba(229, 57, 53, 0.15);
        color: var(--spekta-red);
    }

    /* EMPTY STATE */
    .pm-empty {
        grid-column: 1 / -1;
        text-align: center;
        padding: 48px;
        background: var(--spekta-gray-light);
        border-radius: 12px;
        border: 1px dashed var(--border-soft);
    }
    .pm-empty-icon {
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
    .pm-empty strong { display: block; font-size: 14px; color: var(--text-main); margin-bottom: 4px; font-weight: 800;}
    .pm-empty p { margin: 0; font-size: 12px; color: var(--text-muted); font-weight: 600; }

    .error-msg {
        display: none;
    }

    /* RESPONSIVE */
    @media (max-width: 1200px) {
        .pm-promo-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .pm-header { flex-direction: column; align-items: flex-start; gap: 14px; }
        .pm-form { grid-template-columns: 1fr; }
        .pm-stats, .pm-promo-grid { grid-template-columns: 1fr; }
        .pm-form-action { justify-content: flex-start; }
        .pm-submit-btn { width: 100%; }
        .pm-form-panel.show { padding: 15px; }
    }
</style>
@endsection