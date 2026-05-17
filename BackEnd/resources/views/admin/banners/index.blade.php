@extends('layouts.spekta')

@section('title', 'Banner Management')
@section('subtitle', 'Kelola banner carousel homepage mobile')

@section('content')
<div class="bn-page">

    <section class="bn-header">
        <div>
            <span>Promosi & Informasi</span>
            <h1>Banner Management</h1>
            <p>Kelola banner carousel untuk homepage mobile Spekta Academy.</p>
        </div>

        <a href="{{ route('admin.banners.create') }}" class="bn-primary-btn">
            <i class="fa-solid fa-plus"></i>
            Tambah Banner
        </a>
    </section>

    @if(session('success'))
        <div class="bn-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <section class="bn-stats">
        <div class="bn-stat-card">
            <div class="bn-stat-icon">
                <i class="fa-solid fa-images"></i>
            </div>
            <p>Total Banner</p>
            <h2>{{ number_format($banners->total() ?? $banners->count()) }}</h2>
            <div class="bn-stat-meta">
                <span class="info">Carousel</span>
                <small>homepage mobile</small>
            </div>
        </div>

        <div class="bn-stat-card">
            <div class="bn-stat-icon">
                <i class="fa-solid fa-toggle-on"></i>
            </div>
            <p>Banner Aktif</p>
            <h2>{{ number_format($banners->where('is_active', true)->count()) }}</h2>
            <div class="bn-stat-meta">
                <span class="success">Aktif</span>
                <small>ditampilkan</small>
            </div>
        </div>

        <div class="bn-stat-card">
            <div class="bn-stat-icon">
                <i class="fa-solid fa-toggle-off"></i>
            </div>
            <p>Banner Nonaktif</p>
            <h2>{{ number_format($banners->where('is_active', false)->count()) }}</h2>
            <div class="bn-stat-meta">
                <span class="warning">Nonaktif</span>
                <small>disembunyikan</small>
            </div>
        </div>

        <div class="bn-stat-card">
            <div class="bn-stat-icon">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <p>Urutan Tertinggi</p>
            <h2>{{ number_format($banners->max('order_position') ?? 0) }}</h2>
            <div class="bn-stat-meta">
                <span class="info">Order</span>
                <small>posisi banner</small>
            </div>
        </div>
    </section>

    <section class="bn-main-grid">

        <div class="bn-list-panel">
            <div class="bn-panel-heading">
                <div>
                    <h2>Daftar Banner</h2>
                    <p>Atur banner promosi yang muncul pada aplikasi mobile.</p>
                </div>

                <a href="{{ route('admin.banners.create') }}" class="bn-small-action">
                    <i class="fa-solid fa-plus"></i>
                    Tambah
                </a>
            </div>

            <div class="bn-banner-list">
                @forelse($banners as $banner)
                    @php
                        $imageUrl = $banner->image_url ?? null;

                        if (!$imageUrl && !empty($banner->image)) {
                            if (\Illuminate\Support\Str::startsWith($banner->image, ['http://', 'https://'])) {
                                $imageUrl = $banner->image;
                            } elseif (\Illuminate\Support\Str::startsWith($banner->image, ['storage/'])) {
                                $imageUrl = asset($banner->image);
                            } else {
                                $imageUrl = asset('storage/' . ltrim($banner->image, '/'));
                            }
                        } elseif ($imageUrl && !\Illuminate\Support\Str::startsWith($imageUrl, ['http://', 'https://'])) {
                            $imageUrl = asset($imageUrl);
                        }
                    @endphp

                    <article class="bn-banner-card">
                        <div class="bn-banner-image">
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $banner->title ?? 'Banner' }}">
                            @else
                                <div class="bn-no-image">
                                    <i class="fa-solid fa-image"></i>
                                    <span>No Image</span>
                                </div>
                            @endif

                            <div class="bn-image-badge">
                                #{{ $banner->order_position ?? 0 }}
                            </div>
                        </div>

                        <div class="bn-banner-content">
                            <div class="bn-banner-title-row">
                                <div>
                                    <h3>{{ $banner->title ?? 'Tanpa Judul' }}</h3>
                                    <p>{{ $banner->description ? \Illuminate\Support\Str::limit($banner->description, 145) : 'Tidak ada deskripsi banner.' }}</p>
                                </div>

                                @if($banner->is_active)
                                    <span class="bn-status active">Aktif</span>
                                @else
                                    <span class="bn-status inactive">Nonaktif</span>
                                @endif
                            </div>

                            <div class="bn-meta-grid">
                                <div>
                                    <span>Link</span>
                                    <strong>{{ $banner->link ?: '-' }}</strong>
                                </div>

                                <div>
                                    <span>Urutan</span>
                                    <strong>{{ $banner->order_position ?? 0 }}</strong>
                                </div>

                                <div>
                                    <span>Dibuat</span>
                                    <strong>{{ $banner->created_at?->translatedFormat('d M Y') ?? '-' }}</strong>
                                </div>
                            </div>

                            <div class="bn-actions">
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="edit">
                                    <i class="fa-solid fa-pen"></i>
                                    Edit
                                </a>

                                <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Hapus banner ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="delete">
                                        <i class="fa-solid fa-trash"></i>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="bn-empty">
                        <i class="fa-solid fa-images"></i>
                        <strong>Belum ada banner.</strong>
                        <span>Tambahkan banner pertama untuk carousel homepage mobile.</span>
                        <a href="{{ route('admin.banners.create') }}">Tambah Banner Pertama</a>
                    </div>
                @endforelse
            </div>

            @if(method_exists($banners, 'hasPages') && $banners->hasPages())
                <div class="bn-pagination">
                    {{ $banners->links() }}
                </div>
            @endif
        </div>

        <aside class="bn-side-panel">
            <div class="bn-side-card">
                <h3>Ringkasan Banner</h3>

                <div class="bn-summary-list">
                    <div>
                        <span><i class="dot red"></i>Total Banner</span>
                        <strong>{{ number_format($banners->total() ?? $banners->count()) }}</strong>
                    </div>

                    <div>
                        <span><i class="dot green"></i>Banner Aktif</span>
                        <strong>{{ number_format($banners->where('is_active', true)->count()) }}</strong>
                    </div>

                    <div>
                        <span><i class="dot gray"></i>Banner Nonaktif</span>
                        <strong>{{ number_format($banners->where('is_active', false)->count()) }}</strong>
                    </div>

                    <div>
                        <span><i class="dot blue"></i>Urutan Tertinggi</span>
                        <strong>{{ number_format($banners->max('order_position') ?? 0) }}</strong>
                    </div>
                </div>
            </div>

            <div class="bn-side-card">
                <h3>Aksi Cepat</h3>

                <div class="bn-quick-list">
                    <a href="{{ route('admin.banners.create') }}">
                        <div>
                            <i class="fa-solid fa-plus"></i>
                        </div>
                        <span>Tambah Banner</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>

                    <a href="{{ route('admin.promo.index') }}">
                        <div>
                            <i class="fa-solid fa-tags"></i>
                        </div>
                        <span>Kelola Promo</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>

                    <a href="{{ route('admin.announcement.index') }}">
                        <div>
                            <i class="fa-solid fa-bullhorn"></i>
                        </div>
                        <span>Pengumuman</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </aside>

    </section>
</div>

<style>
    .bn-page {
        width: 100%;
    }

    .bn-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 22px;
        margin-bottom: 22px;
    }

    .bn-header span {
        display: block;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .18em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .bn-header h1 {
        margin: 0 0 7px;
        color: #111827;
        font-size: 26px;
        font-weight: 900;
        letter-spacing: -0.03em;
    }

    .bn-header p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        font-weight: 600;
    }

    .bn-primary-btn {
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

    .bn-alert {
        border-radius: 16px;
        padding: 15px 17px;
        margin-bottom: 18px;
        display: flex;
        gap: 12px;
        align-items: center;
        font-size: 13px;
        font-weight: 800;
    }

    .bn-alert.success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .bn-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }

    .bn-stat-card,
    .bn-list-panel,
    .bn-side-card {
        background: #fff;
        border: 1px solid #edf0f4;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .05);
    }

    .bn-stat-card {
        border-radius: 20px;
        padding: 22px;
        min-height: 150px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .bn-stat-icon {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        background: #ffe8ee;
        color: #d90429;
        border-radius: 15px;
        margin-bottom: 16px;
    }

    .bn-stat-card p {
        margin: 0 0 8px;
        color: #6b7280;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .bn-stat-card h2 {
        margin: 0 0 14px;
        color: #0f172a;
        font-size: 31px;
        font-weight: 900;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .bn-stat-meta {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .bn-stat-meta span {
        height: 23px;
        display: inline-flex;
        align-items: center;
        border-radius: 8px;
        padding: 0 9px;
        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
    }

    .bn-stat-meta .success {
        background: #dcfce7;
        color: #16a34a;
    }

    .bn-stat-meta .warning {
        background: #ffedd5;
        color: #ea580c;
    }

    .bn-stat-meta .info {
        background: #dbeafe;
        color: #2563eb;
    }

    .bn-stat-meta small {
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .bn-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 330px;
        gap: 22px;
        align-items: start;
    }

    .bn-list-panel {
        border-radius: 22px;
        padding: 22px;
    }

    .bn-panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 18px;
    }

    .bn-panel-heading h2 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
    }

    .bn-panel-heading p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
    }

    .bn-small-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 38px;
        padding: 0 13px;
        border-radius: 11px;
        background: #fff1f2;
        color: #d90429;
        font-size: 11px;
        font-weight: 900;
    }

    .bn-banner-list {
        display: grid;
        gap: 16px;
    }

    .bn-banner-card {
        display: grid;
        grid-template-columns: 310px minmax(0, 1fr);
        gap: 18px;
        padding: 14px;
        border: 1px solid #edf0f4;
        border-radius: 18px;
        background: #fff;
        transition: .2s ease;
    }

    .bn-banner-card:hover {
        border-color: #fecdd3;
        box-shadow: 0 14px 30px rgba(15, 23, 42, .06);
        transform: translateY(-2px);
    }

    .bn-banner-image {
        height: 170px;
        border-radius: 15px;
        overflow: hidden;
        position: relative;
        background: #f8fafc;
    }

    .bn-banner-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .bn-no-image {
        width: 100%;
        height: 100%;
        display: grid;
        place-items: center;
        color: #9ca3af;
        font-size: 12px;
        font-weight: 800;
    }

    .bn-no-image i {
        font-size: 28px;
        color: #d90429;
        margin-bottom: 6px;
    }

    .bn-image-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        min-width: 34px;
        height: 28px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: rgba(17, 24, 39, .82);
        color: #fff;
        font-size: 11px;
        font-weight: 900;
        backdrop-filter: blur(8px);
    }

    .bn-banner-content {
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 14px;
    }

    .bn-banner-title-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
    }

    .bn-banner-title-row h3 {
        margin: 0 0 7px;
        color: #111827;
        font-size: 16px;
        font-weight: 900;
    }

    .bn-banner-title-row p {
        margin: 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
    }

    .bn-status {
        flex-shrink: 0;
        height: 27px;
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 0 10px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .bn-status.active {
        background: #dcfce7;
        color: #16a34a;
    }

    .bn-status.inactive {
        background: #f3f4f6;
        color: #6b7280;
    }

    .bn-meta-grid {
        display: grid;
        grid-template-columns: 1.4fr .6fr .8fr;
        gap: 12px;
        padding: 13px;
        border-radius: 14px;
        background: #f8fafc;
    }

    .bn-meta-grid span {
        display: block;
        color: #9ca3af;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .05em;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .bn-meta-grid strong {
        display: block;
        color: #111827;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .bn-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 9px;
    }

    .bn-actions form {
        margin: 0;
    }

    .bn-actions a,
    .bn-actions button {
        height: 36px;
        border: none;
        border-radius: 11px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 13px;
        font-size: 11px;
        font-weight: 900;
        cursor: pointer;
        font-family: inherit;
    }

    .bn-actions .edit {
        background: #ffedd5;
        color: #ea580c;
    }

    .bn-actions .delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .bn-side-panel {
        display: grid;
        gap: 22px;
    }

    .bn-side-card {
        border-radius: 22px;
        padding: 20px;
    }

    .bn-side-card h3 {
        margin: 0 0 18px;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
    }

    .bn-summary-list {
        display: grid;
        gap: 14px;
    }

    .bn-summary-list div {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        color: #374151;
        font-size: 12px;
        font-weight: 800;
    }

    .bn-summary-list span {
        display: inline-flex;
        align-items: center;
        gap: 9px;
    }

    .bn-summary-list strong {
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
    .dot.gray { background: #9ca3af; }
    .dot.blue { background: #2563eb; }

    .bn-quick-list {
        display: grid;
        gap: 12px;
    }

    .bn-quick-list a {
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

    .bn-quick-list a:hover {
        background: #fff7f9;
        border-color: #fecdd3;
    }

    .bn-quick-list div {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border-radius: 13px;
        background: #ffe8ee;
        color: #d90429;
    }

    .bn-quick-list span {
        color: #111827;
        font-size: 12px;
        font-weight: 900;
    }

    .bn-quick-list a > i {
        color: #64748b;
        font-size: 11px;
    }

    .bn-empty {
        padding: 45px;
        text-align: center;
        background: #f8fafc;
        border-radius: 18px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .bn-empty i {
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

    .bn-empty strong {
        display: block;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    .bn-empty span {
        display: block;
        margin-bottom: 16px;
    }

    .bn-empty a {
        display: inline-flex;
        align-items: center;
        height: 40px;
        padding: 0 15px;
        border-radius: 12px;
        background: #d90429;
        color: #fff;
        font-size: 11px;
        font-weight: 900;
    }

    .bn-pagination {
        margin-top: 18px;
    }

    @media (max-width: 1450px) {
        .bn-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .bn-main-grid {
            grid-template-columns: 1fr;
        }

        .bn-side-panel {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .bn-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .bn-stats,
        .bn-side-panel {
            grid-template-columns: 1fr;
        }

        .bn-banner-card {
            grid-template-columns: 1fr;
        }

        .bn-meta-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection