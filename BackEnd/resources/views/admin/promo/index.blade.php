@extends('layouts.spekta')

@section('title', 'Promo Management')
@section('subtitle', 'Kelola strategi diskon Spekta Academy')

@section('content')
@php
    $promoCollection = method_exists($promos, 'getCollection') ? $promos->getCollection() : collect($promos);
    $totalPromo = method_exists($promos, 'total') ? $promos->total() : $promoCollection->count();
    $activePromo = $promoCollection->where('is_active', true)->count();
    $inactivePromo = $promoCollection->where('is_active', false)->count();
    $totalQuota = $promoCollection->sum('quota');
@endphp

<div class="pm-page">

    <section class="pm-header">
        <div>
            <span>Promosi & Informasi</span>
            <h1>Manajemen Promo</h1>
            <p>Kelola strategi diskon untuk setiap program kelas Spekta Academy.</p>
        </div>

        <a href="#promoForm" class="pm-primary-btn">
            <i class="fa-solid fa-plus"></i>
            Buat Promo Baru
        </a>
    </section>

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

    <section class="pm-stats">
        <div class="pm-stat-card">
            <div class="pm-stat-icon">
                <i class="fa-solid fa-tags"></i>
            </div>
            <p>Total Promo</p>
            <h2>{{ number_format($totalPromo) }}</h2>
            <div class="pm-stat-meta">
                <span class="info">Kode</span>
                <small>promo dibuat</small>
            </div>
        </div>

        <div class="pm-stat-card">
            <div class="pm-stat-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <p>Promo Aktif</p>
            <h2>{{ number_format($activePromo) }}</h2>
            <div class="pm-stat-meta">
                <span class="success">Aktif</span>
                <small>sedang berjalan</small>
            </div>
        </div>

        <div class="pm-stat-card">
            <div class="pm-stat-icon">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <p>Promo Nonaktif</p>
            <h2>{{ number_format($inactivePromo) }}</h2>
            <div class="pm-stat-meta">
                <span class="warning">Nonaktif</span>
                <small>tidak ditampilkan</small>
            </div>
        </div>

        <div class="pm-stat-card">
            <div class="pm-stat-icon">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <p>Total Kuota</p>
            <h2>{{ number_format($totalQuota) }}</h2>
            <div class="pm-stat-meta">
                <span class="info">Kuota</span>
                <small>seluruh promo</small>
            </div>
        </div>
    </section>

    <section class="pm-main-grid">

        <div class="pm-form-panel" id="promoForm">
            <div class="pm-panel-heading">
                <div>
                    <h2>Buat Kode Promo Baru</h2>
                    <p>Masukkan kode promo, target kelas, besaran diskon, kuota, dan periode promo.</p>
                </div>

                <div class="pm-heading-icon">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
            </div>

            <form action="{{ route('admin.promo.store') }}" method="POST" class="pm-form">
                @csrf

                <div class="pm-input-group">
                    <label>Kode Promo</label>
                    <div>
                        <i class="fa-solid fa-ticket"></i>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="SPEKTA50" required>
                    </div>
                </div>

                <div class="pm-input-group">
                    <label>Target Kelas</label>
                    <div>
                        <i class="fa-solid fa-layer-group"></i>
                        <select name="class_id" required>
                            <option value="">Pilih Kelas</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->class_id }}" {{ old('class_id') == $c->class_id ? 'selected' : '' }}>
                                    {{ $c->program_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pm-input-group">
                    <label>Besar Diskon</label>
                    <div class="pm-discount-wrap">
                        <i class="fa-solid fa-percent"></i>
                        <input type="number" name="discount_value" value="{{ old('discount_value') }}" placeholder="Nilai" required>
                        <button type="button" id="btn-toggle-type" onclick="toggleDiscountType()">
                            <span id="display-type">%</span>
                        </button>
                    </div>
                    <input type="hidden" name="discount_type" id="input_discount_type" value="{{ old('discount_type', 'percent') }}">
                </div>

                <div class="pm-input-group">
                    <label>Kuota</label>
                    <div>
                        <i class="fa-solid fa-users"></i>
                        <input type="number" name="quota" value="{{ old('quota', 100) }}" placeholder="100" min="1" required>
                    </div>
                </div>

                <div class="pm-input-group">
                    <label>Tanggal Mulai</label>
                    <div>
                        <i class="fa-regular fa-calendar"></i>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" required>
                    </div>
                </div>

                <div class="pm-input-group">
                    <label>Tanggal Berakhir</label>
                    <div>
                        <i class="fa-regular fa-calendar-check"></i>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" required>
                    </div>
                </div>

                <button type="submit" class="pm-submit">
                    <i class="fa-solid fa-rocket"></i>
                    Terbitkan Kode Promo
                </button>
            </form>
        </div>

        <aside class="pm-side-panel">
            <div class="pm-side-card">
                <h3>Ringkasan Promo</h3>

                <div class="pm-summary-list">
                    <div>
                        <span><i class="dot red"></i>Total Promo</span>
                        <strong>{{ number_format($totalPromo) }}</strong>
                    </div>

                    <div>
                        <span><i class="dot green"></i>Promo Aktif</span>
                        <strong>{{ number_format($activePromo) }}</strong>
                    </div>

                    <div>
                        <span><i class="dot orange"></i>Nonaktif</span>
                        <strong>{{ number_format($inactivePromo) }}</strong>
                    </div>

                    <div>
                        <span><i class="dot blue"></i>Total Kuota</span>
                        <strong>{{ number_format($totalQuota) }}</strong>
                    </div>
                </div>
            </div>

            <div class="pm-side-card">
                <h3>Aksi Cepat</h3>

                <div class="pm-quick-list">
                    <a href="{{ route('admin.promo.index') }}">
                        <div><i class="fa-solid fa-tags"></i></div>
                        <span>Kelola Promo</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>

                    <a href="{{ route('admin.banners.index') }}">
                        <div><i class="fa-solid fa-image"></i></div>
                        <span>Kelola Banner</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>

                    <a href="{{ route('admin.announcement.index') }}">
                        <div><i class="fa-solid fa-bullhorn"></i></div>
                        <span>Pengumuman</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </aside>

    </section>

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
                        <div>
                            <span class="pm-code">{{ $row->code }}</span>
                            <h3>{{ $row->class->program_name ?? 'Program tidak tersedia' }}</h3>
                        </div>

                        <div class="pm-discount">
                            <strong>{{ $discountText }}</strong>
                            <small>Potongan</small>
                        </div>
                    </div>

                    <div class="pm-promo-meta">
                        <div>
                            <span>Kuota</span>
                            <strong>{{ number_format($row->quota ?? 0) }}</strong>
                        </div>

                        <div>
                            <span>Status</span>
                            @if($isActive)
                                <strong class="active">Aktif</strong>
                            @else
                                <strong class="inactive">Nonaktif</strong>
                            @endif
                        </div>

                        <div>
                            <span>Mulai</span>
                            <strong>{{ $startDate ? $startDate->translatedFormat('d M Y') : '-' }}</strong>
                        </div>

                        <div>
                            <span>Berakhir</span>
                            <strong>{{ $endDate ? $endDate->translatedFormat('d M Y') : '-' }}</strong>
                        </div>
                    </div>

                    <div class="pm-period-bar">
                        <div class="{{ $isExpired ? 'expired' : 'active' }}"></div>
                    </div>

                    <form action="{{ route('admin.promo.destroy', $row->promotion_id) }}" method="POST" onsubmit="return confirm('Hapus promo ini?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="pm-stop-btn">
                            <i class="fa-solid fa-trash"></i>
                            Hentikan Promo
                        </button>
                    </form>
                </article>
            @empty
                <div class="pm-empty">
                    <i class="fa-solid fa-ticket"></i>
                    <strong>Belum ada promo.</strong>
                    <span>Buat kode promo pertama melalui form di atas.</span>
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

    document.addEventListener('DOMContentLoaded', function () {
        const inputType = document.getElementById('input_discount_type');
        const displayType = document.getElementById('display-type');

        if (inputType && displayType) {
            displayType.innerText = inputType.value === 'fixed' ? 'Rp' : '%';
        }
    });
</script>

<style>
    .pm-page {
        width: 100%;
    }

    .pm-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 22px;
        margin-bottom: 22px;
    }

    .pm-header span {
        display: block;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .18em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .pm-header h1 {
        margin: 0 0 7px;
        color: #111827;
        font-size: 26px;
        font-weight: 900;
        letter-spacing: -0.03em;
        text-transform: uppercase;
    }

    .pm-header p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        font-weight: 600;
    }

    .pm-primary-btn {
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

    .pm-alert {
        border-radius: 16px;
        padding: 15px 17px;
        margin-bottom: 18px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 800;
    }

    .pm-alert.success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .pm-alert.error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .pm-alert strong {
        display: block;
        margin-bottom: 3px;
    }

    .pm-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .pm-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }

    .pm-stat-card,
    .pm-form-panel,
    .pm-side-card,
    .pm-promo-panel {
        background: #fff;
        border: 1px solid #edf0f4;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .05);
    }

    .pm-stat-card {
        border-radius: 20px;
        padding: 22px;
        min-height: 150px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .pm-stat-icon {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        background: #ffe8ee;
        color: #d90429;
        border-radius: 15px;
        margin-bottom: 16px;
    }

    .pm-stat-card p {
        margin: 0 0 8px;
        color: #6b7280;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .pm-stat-card h2 {
        margin: 0 0 14px;
        color: #0f172a;
        font-size: 31px;
        font-weight: 900;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .pm-stat-meta {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pm-stat-meta span {
        height: 23px;
        display: inline-flex;
        align-items: center;
        border-radius: 8px;
        padding: 0 9px;
        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
    }

    .pm-stat-meta .success {
        background: #dcfce7;
        color: #16a34a;
    }

    .pm-stat-meta .warning {
        background: #ffedd5;
        color: #ea580c;
    }

    .pm-stat-meta .info {
        background: #dbeafe;
        color: #2563eb;
    }

    .pm-stat-meta small {
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .pm-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 330px;
        gap: 22px;
        align-items: start;
        margin-bottom: 22px;
    }

    .pm-form-panel,
    .pm-side-card,
    .pm-promo-panel {
        border-radius: 22px;
        padding: 22px;
    }

    .pm-panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 22px;
    }

    .pm-panel-heading h2 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
    }

    .pm-panel-heading p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
    }

    .pm-heading-icon {
        width: 48px;
        height: 48px;
        display: grid;
        place-items: center;
        border-radius: 16px;
        background: #ffe8ee;
        color: #d90429;
        flex-shrink: 0;
    }

    .pm-form {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .pm-input-group label {
        display: block;
        margin-bottom: 8px;
        color: #374151;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .pm-input-group div {
        position: relative;
    }

    .pm-input-group i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 13px;
    }

    .pm-input-group input,
    .pm-input-group select {
        width: 100%;
        height: 48px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
        outline: none;
        color: #111827;
        font-size: 13px;
        font-weight: 700;
        font-family: inherit;
        padding: 0 15px 0 42px;
    }

    .pm-input-group input:focus,
    .pm-input-group select:focus {
        background: #fff;
        border-color: #fecdd3;
        box-shadow: 0 0 0 4px rgba(217, 4, 41, .08);
    }

    .pm-discount-wrap {
        display: grid;
        grid-template-columns: 1fr 72px;
    }

    .pm-discount-wrap input {
        border-radius: 14px 0 0 14px;
        border-right: none;
    }

    .pm-discount-wrap button {
        height: 48px;
        border: none;
        border-radius: 0 14px 14px 0;
        background: #d90429;
        color: #fff;
        font-size: 12px;
        font-weight: 900;
        cursor: pointer;
        font-family: inherit;
    }

    .pm-submit {
        grid-column: 1 / -1;
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

    .pm-side-panel {
        display: grid;
        gap: 22px;
    }

    .pm-side-card h3 {
        margin: 0 0 18px;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
    }

    .pm-summary-list {
        display: grid;
        gap: 14px;
    }

    .pm-summary-list div {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        color: #374151;
        font-size: 12px;
        font-weight: 800;
    }

    .pm-summary-list span {
        display: inline-flex;
        align-items: center;
        gap: 9px;
    }

    .pm-summary-list strong {
        color: #111827;
        font-weight: 900;
    }

    .dot {
        width: 9px;
        height: 9px;
        border-radius: 999px;
        display: inline-block;
    }

    .dot.red { background: #d90429; }
    .dot.green { background: #16a34a; }
    .dot.orange { background: #ea580c; }
    .dot.blue { background: #2563eb; }

    .pm-quick-list {
        display: grid;
        gap: 12px;
    }

    .pm-quick-list a {
        display: grid;
        grid-template-columns: 42px 1fr 12px;
        gap: 12px;
        align-items: center;
        padding: 12px;
        border: 1px solid #edf0f4;
        border-radius: 15px;
        color: inherit;
        transition: .2s ease;
    }

    .pm-quick-list a:hover {
        background: #fff7f9;
        border-color: #fecdd3;
    }

    .pm-quick-list div {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border-radius: 13px;
        background: #ffe8ee;
        color: #d90429;
    }

    .pm-quick-list span {
        color: #111827;
        font-size: 12px;
        font-weight: 900;
    }

    .pm-quick-list a > i {
        color: #64748b;
        font-size: 11px;
    }

    .pm-promo-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .pm-promo-card {
        border: 1px solid #edf0f4;
        border-radius: 20px;
        padding: 18px;
        background:
            radial-gradient(circle at top right, rgba(217, 4, 41, .08), transparent 34%),
            #fff;
        transition: .2s ease;
    }

    .pm-promo-card:hover {
        border-color: #fecdd3;
        box-shadow: 0 14px 30px rgba(15, 23, 42, .06);
        transform: translateY(-2px);
    }

    .pm-promo-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 18px;
    }

    .pm-code {
        display: inline-flex;
        height: 27px;
        align-items: center;
        border-radius: 999px;
        padding: 0 11px;
        background: #fff1f2;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .pm-promo-card h3 {
        margin: 0;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
        line-height: 1.35;
    }

    .pm-discount {
        text-align: right;
        flex-shrink: 0;
    }

    .pm-discount strong {
        display: block;
        color: #111827;
        font-size: 25px;
        font-weight: 900;
        line-height: 1;
    }

    .pm-discount small {
        color: #9ca3af;
        font-size: 9px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .pm-promo-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        padding: 14px;
        border-radius: 16px;
        background: #f8fafc;
        margin-bottom: 16px;
    }

    .pm-promo-meta span {
        display: block;
        color: #9ca3af;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: .05em;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .pm-promo-meta strong {
        display: block;
        color: #111827;
        font-size: 12px;
        font-weight: 900;
    }

    .pm-promo-meta strong.active {
        color: #16a34a;
    }

    .pm-promo-meta strong.inactive {
        color: #ea580c;
    }

    .pm-period-bar {
        height: 7px;
        border-radius: 999px;
        background: #f3f4f6;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .pm-period-bar div {
        height: 100%;
        width: 70%;
        border-radius: 999px;
    }

    .pm-period-bar div.active {
        background: linear-gradient(90deg, #d90429, #fb7185);
    }

    .pm-period-bar div.expired {
        width: 100%;
        background: #d1d5db;
    }

    .pm-stop-btn {
        width: 100%;
        height: 42px;
        border: none;
        border-radius: 13px;
        background: #f8fafc;
        color: #6b7280;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        cursor: pointer;
        transition: .2s ease;
        font-family: inherit;
    }

    .pm-stop-btn:hover {
        background: #d90429;
        color: #fff;
    }

    .pm-empty {
        grid-column: 1 / -1;
        padding: 45px;
        text-align: center;
        background: #f8fafc;
        border-radius: 18px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .pm-empty i {
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

    .pm-empty strong {
        display: block;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    .pm-pagination {
        margin-top: 18px;
    }

    @media (max-width: 1450px) {
        .pm-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .pm-main-grid {
            grid-template-columns: 1fr;
        }

        .pm-side-panel {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .pm-promo-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .pm-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .pm-stats,
        .pm-side-panel,
        .pm-promo-grid {
            grid-template-columns: 1fr;
        }

        .pm-form {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection