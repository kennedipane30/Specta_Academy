@extends('layouts.spekta')

@section('title', 'Banner Management')
@section('subtitle', 'Kelola banner carousel homepage mobile')

@section('content')
<div class="bn-page">

    {{-- HEADER --}}
    <section class="bn-header">
        <div class="bn-header-text">
            <span class="bn-kicker">Promosi & Informasi</span>
            <h1>Banner Management</h1>
            <p>Kelola banner carousel untuk homepage mobile Spekta Academy.</p>
        </div>
    </section>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="bn-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- TOTAL BANNER SUMMARY STRIP --}}
    <section class="bn-summary-strip">
        <div class="bn-summary-icon">
            <i class="fa-solid fa-images"></i>
        </div>
        <div class="bn-summary-info">
            <p>Total Keseluruhan Banner</p>
            <h2>{{ number_format($banners->total() ?? $banners->count()) }} <span>Banner Carousel</span></h2>
        </div>
    </section>

    {{-- MAIN CONTENT --}}
    <section class="bn-main-grid">

        <div class="bn-list-panel">
            <div class="bn-panel-heading">
                <div>
                    <h2>Daftar Banner</h2>
                    <p>Atur banner promosi yang muncul pada aplikasi mobile.</p>
                </div>

                <a href="{{ route('admin.banners.create') }}" class="bn-primary-btn">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Banner Baru
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
                                    <span>Link Tujuan</span>
                                    <strong>{{ $banner->link ?: 'Tidak ada link' }}</strong>
                                </div>

                                <div>
                                    <span>Urutan</span>
                                    <strong>{{ $banner->order_position ?? 0 }}</strong>
                                </div>

                                <div>
                                    <span>Tanggal Dibuat</span>
                                    <strong>{{ $banner->created_at?->translatedFormat('d M Y') ?? '-' }}</strong>
                                </div>
                            </div>

                            <div class="bn-actions">
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="edit">
                                    <i class="fa-solid fa-pen"></i>
                                    Edit
                                </a>

                                <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Hapus banner ini secara permanen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete">
                                        <i class="fa-solid fa-trash-can"></i>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="bn-empty">
                        <div class="bn-empty-icon"><i class="fa-solid fa-images"></i></div>
                        <strong>Belum ada banner.</strong>
                        <p>Tambahkan banner pertama untuk carousel homepage mobile Anda.</p>
                        <a href="{{ route('admin.banners.create') }}" class="bn-primary-btn" style="margin-top: 15px;">Tambah Banner Pertama</a>
                    </div>
                @endforelse
            </div>

            @if(method_exists($banners, 'hasPages') && $banners->hasPages())
                <div class="bn-pagination">
                    {{ $banners->links() }}
                </div>
            @endif
        </div>

    </section>
</div>

<style>
    /* BASE LAYOUT */
    .bn-page {
        width: 100%;
        font-family: 'Inter', system-ui, sans-serif;
        color: #334155;
    }

    /* HEADER */
    .bn-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        gap: 20px;
    }
    .bn-kicker {
        display: block;
        color: #d90429;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .bn-header h1 {
        margin: 0 0 8px;
        color: #0f172a;
        font-size: 28px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .bn-header p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    /* PRIMARY BUTTON */
    .bn-primary-btn {
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
        white-space: nowrap;
    }
    .bn-primary-btn:hover {
        background: #b80222;
        transform: translateY(-1px);
    }

    /* ALERTS */
    .bn-alert {
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
        display: flex;
        gap: 12px;
        align-items: center;
        font-size: 14px;
        font-weight: 700;
    }
    .bn-alert.success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }

    /* SUMMARY STRIP (TOTAL BANNER) */
    .bn-summary-strip {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px 30px;
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }
    .bn-summary-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        font-size: 28px;
        background: #fff1f2;
        color: #d90429;
        flex-shrink: 0;
    }
    .bn-summary-info p {
        margin: 0 0 4px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .bn-summary-info h2 {
        margin: 0;
        font-size: 32px;
        font-weight: 900;
        color: #0f172a;
        line-height: 1;
        display: flex;
        align-items: baseline;
        gap: 8px;
    }
    .bn-summary-info h2 span {
        font-size: 16px;
        font-weight: 600;
        color: #94a3b8;
    }

    /* MAIN PANEL (FULL WIDTH) */
    .bn-main-grid {
        display: block;
        margin-bottom: 24px;
    }
    .bn-list-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
    }
    .bn-panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 15px;
    }
    .bn-panel-heading h2 {
        margin: 0 0 6px;
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
    }
    .bn-panel-heading p {
        margin: 0;
        font-size: 14px;
        color: #64748b;
    }

    /* BANNER LIST & CARDS */
    .bn-banner-list {
        display: grid;
        gap: 20px;
    }
    .bn-banner-card {
        display: grid;
        grid-template-columns: 320px minmax(0, 1fr);
        gap: 24px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #fff;
        transition: all 0.2s ease;
    }
    .bn-banner-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
        transform: translateY(-2px);
    }

    /* CARD IMAGE */
    .bn-banner-image {
        height: 180px;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
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
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #94a3b8;
        font-size: 13px;
        font-weight: 600;
    }
    .bn-no-image i {
        font-size: 32px;
        color: #cbd5e1;
        margin-bottom: 8px;
    }
    .bn-image-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        padding: 4px 12px;
        border-radius: 8px;
        background: rgba(15, 23, 42, 0.85);
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        backdrop-filter: blur(4px);
    }

    /* CARD CONTENT */
    .bn-banner-content {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .bn-banner-title-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
    }
    .bn-banner-title-row h3 {
        margin: 0 0 8px;
        color: #0f172a;
        font-size: 18px;
        font-weight: 800;
    }
    .bn-banner-title-row p {
        margin: 0;
        color: #475569;
        font-size: 13px;
        line-height: 1.6;
    }

    /* STATUS BADGE */
    .bn-status {
        flex-shrink: 0;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .bn-status.active { background: #dcfce7; color: #16a34a; }
    .bn-status.inactive { background: #f1f5f9; color: #64748b; }

    /* META INFO GRID */
    .bn-meta-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr;
        gap: 16px;
        padding: 16px;
        border-radius: 12px;
        background: #f8fafc;
        margin: 16px 0;
    }
    .bn-meta-grid span {
        display: block;
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 4px;
    }
    .bn-meta-grid strong {
        display: block;
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* CARD ACTIONS */
    .bn-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
    }
    .bn-actions form { margin: 0; }
    .bn-actions a, .bn-actions button {
        height: 40px;
        border: none;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 16px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        text-decoration: none;
        transition: background 0.2s;
    }
    .bn-actions .edit { background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }
    .bn-actions .edit:hover { background: #e2e8f0; }
    .bn-actions .delete { background: #fef2f2; color: #dc2626; }
    .bn-actions .delete:hover { background: #fecaca; }

    /* EMPTY STATE */
    .bn-empty {
        text-align: center;
        padding: 80px 20px;
        background: #f8fafc;
        border-radius: 16px;
        border: 1px dashed #cbd5e1;
    }
    .bn-empty-icon {
        width: 72px;
        height: 72px;
        background: #fff;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 28px;
        color: #94a3b8;
        margin: 0 auto 20px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .bn-empty strong { display: block; font-size: 18px; color: #0f172a; margin-bottom: 6px;}
    .bn-empty p { margin: 0; font-size: 14px; color: #64748b; }

    .bn-pagination { margin-top: 24px; }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .bn-header { flex-direction: column; align-items: flex-start; }
        .bn-summary-strip { flex-direction: column; text-align: center; gap: 12px; padding: 20px;}
        .bn-summary-info h2 { justify-content: center;}
        .bn-banner-card { grid-template-columns: 1fr; }
        .bn-banner-image { height: 200px; }
        .bn-meta-grid { grid-template-columns: 1fr; gap: 12px; }
        .bn-panel-heading { flex-direction: column; align-items: stretch;}
        .bn-primary-btn { justify-content: center; }
    }
</style>
@endsection
