@extends('layouts.spekta')

@section('title', 'Manajemen Program - Spekta Academy')
@section('subtitle', 'Katalog program kelas Spekta Academy')

@section('content')
@php
    $classCollection = method_exists($classes, 'getCollection') ? $classes->getCollection() : collect($classes);
    $totalProgram = method_exists($classes, 'total') ? $classes->total() : $classCollection->count();
    $totalInvestasi = $classCollection->sum('price');
    $avgPrice = $classCollection->count() > 0 ? round($classCollection->avg('price')) : 0;
@endphp

<div class="cp-page">

    <section class="cp-header">
        <div>
            <span>Spekta Control Center</span>
            <h1>Katalog Program</h1>
            <p>
                Manajemen pusat untuk konten aplikasi mobile. Perubahan harga, deskripsi, dan visual akan disinkronkan ke aplikasi siswa.
            </p>
        </div>

        <a href="{{ route('admin.classes.create') }}" class="cp-primary-btn">
            <i class="fa-solid fa-plus"></i>
            Tambah Program Baru
        </a>
    </section>

    @if(session('success'))
        <div class="cp-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <div>
                <strong>Berhasil!</strong>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <section class="cp-stats">
        <div class="cp-stat-card">
            <div class="cp-stat-icon">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <p>Total Program</p>
            <h2>{{ number_format($totalProgram) }}</h2>
            <div class="cp-stat-meta">
                <span class="info">Live</span>
                <small>di aplikasi</small>
            </div>
        </div>

        <div class="cp-stat-card">
            <div class="cp-stat-icon">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <p>Rata-rata Harga</p>
            <h2>Rp {{ number_format($avgPrice, 0, ',', '.') }}</h2>
            <div class="cp-stat-meta">
                <span class="success">Investasi</span>
                <small>per program</small>
            </div>
        </div>

        <div class="cp-stat-card">
            <div class="cp-stat-icon">
                <i class="fa-solid fa-image"></i>
            </div>
            <p>Visual Program</p>
            <h2>{{ number_format($classCollection->filter(fn($item) => !empty($item->image_url) || !empty($item->image))->count()) }}</h2>
            <div class="cp-stat-meta">
                <span class="info">Banner</span>
                <small>tersedia</small>
            </div>
        </div>

        <div class="cp-stat-card">
            <div class="cp-stat-icon">
                <i class="fa-solid fa-signal"></i>
            </div>
            <p>Status Sinkron</p>
            <h2>Aktif</h2>
            <div class="cp-stat-meta">
                <span class="success">Realtime</span>
                <small>mobile app</small>
            </div>
        </div>
    </section>

    <section class="cp-main-panel">
        <div class="cp-panel-heading">
            <div>
                <h2>Daftar Program Kelas</h2>
                <p>Kelola nama program, harga, deskripsi, dan visual yang tampil di aplikasi.</p>
            </div>

            <a href="{{ route('admin.classes.create') }}" class="cp-small-btn">
                <i class="fa-solid fa-plus"></i>
                Tambah
            </a>
        </div>

        <div class="cp-program-grid">
            @forelse($classes as $item)
                @php
                    $imageUrl = $item->image_url ?? null;

                    if (!$imageUrl && !empty($item->image)) {
                        if (\Illuminate\Support\Str::startsWith($item->image, ['http://', 'https://'])) {
                            $imageUrl = $item->image;
                        } elseif (\Illuminate\Support\Str::startsWith($item->image, ['storage/'])) {
                            $imageUrl = asset($item->image);
                        } else {
                            $imageUrl = asset('storage/' . ltrim($item->image, '/'));
                        }
                    } elseif ($imageUrl && !\Illuminate\Support\Str::startsWith($imageUrl, ['http://', 'https://'])) {
                        $imageUrl = asset($imageUrl);
                    }
                @endphp

                <article class="cp-program-card">
                    <div class="cp-program-image">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $item->program_name }}">
                        @else
                            <div class="cp-no-image">
                                <i class="fa-solid fa-image"></i>
                                <span>No Image</span>
                            </div>
                        @endif

                        <div class="cp-price-badge">
                            <span>Investasi</span>
                            <strong>Rp {{ number_format($item->price ?? 0, 0, ',', '.') }}</strong>
                        </div>

                        <div class="cp-live-badge">
                            <i class="fa-solid fa-signal"></i>
                            Live on App
                        </div>
                    </div>

                    <div class="cp-program-body">
                        <div class="cp-title-row">
                            <div>
                                <span>ID: {{ $item->class_id }}</span>
                                <h3>{{ $item->program_name }}</h3>
                            </div>
                        </div>

                        <div class="cp-info-box">
                            <span>Informasi Program</span>
                            <p>
                                {{ $item->description
                                    ? \Illuminate\Support\Str::limit($item->description, 150)
                                    : 'Deskripsi program belum dikonfigurasi. Harap lengkapi detail untuk menarik minat siswa mendaftar.'
                                }}
                            </p>
                        </div>

                        <div class="cp-actions">
                            <a href="{{ route('admin.classes.edit', $item->class_id) }}" class="edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                                Konfigurasi
                            </a>

                            <form action="{{ route('admin.classes.destroy', $item->class_id) }}" method="POST" onsubmit="return confirm('Hapus program ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="cp-empty">
                    <i class="fa-solid fa-layer-group"></i>
                    <strong>Belum ada program kelas.</strong>
                    <span>Tambahkan program pertama agar tampil pada aplikasi mobile siswa.</span>
                    <a href="{{ route('admin.classes.create') }}">Tambah Program</a>
                </div>
            @endforelse
        </div>

        @if(method_exists($classes, 'hasPages') && $classes->hasPages())
            <div class="cp-pagination">
                {{ $classes->links() }}
            </div>
        @endif
    </section>

</div>

<style>
    .cp-page { width: 100%; }

    .cp-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 22px;
        margin-bottom: 22px;
    }

    .cp-header span {
        display: block;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .18em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .cp-header h1 {
        margin: 0 0 8px;
        color: #111827;
        font-size: 30px;
        font-weight: 900;
        letter-spacing: -0.04em;
        text-transform: uppercase;
    }

    .cp-header p {
        max-width: 850px;
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.6;
    }

    .cp-primary-btn {
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

    .cp-alert {
        border-radius: 16px;
        padding: 15px 17px;
        margin-bottom: 18px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 800;
    }

    .cp-alert.success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .cp-alert strong {
        display: block;
        margin-bottom: 3px;
    }

    .cp-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }

    .cp-stat-card,
    .cp-main-panel {
        background: #fff;
        border: 1px solid #edf0f4;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .05);
    }

    .cp-stat-card {
        border-radius: 20px;
        padding: 22px;
        min-height: 150px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .cp-stat-icon {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        background: #ffe8ee;
        color: #d90429;
        border-radius: 15px;
        margin-bottom: 16px;
    }

    .cp-stat-card p {
        margin: 0 0 8px;
        color: #6b7280;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .cp-stat-card h2 {
        margin: 0 0 14px;
        color: #0f172a;
        font-size: 25px;
        font-weight: 900;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .cp-stat-meta {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cp-stat-meta span {
        height: 23px;
        display: inline-flex;
        align-items: center;
        border-radius: 8px;
        padding: 0 9px;
        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
    }

    .cp-stat-meta .success {
        background: #dcfce7;
        color: #16a34a;
    }

    .cp-stat-meta .info {
        background: #dbeafe;
        color: #2563eb;
    }

    .cp-stat-meta small {
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .cp-main-panel {
        border-radius: 22px;
        padding: 22px;
    }

    .cp-panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 20px;
    }

    .cp-panel-heading h2 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
    }

    .cp-panel-heading p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
    }

    .cp-small-btn {
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

    .cp-program-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 22px;
    }

    .cp-program-card {
        display: grid;
        grid-template-columns: 280px minmax(0, 1fr);
        gap: 18px;
        padding: 14px;
        border: 1px solid #edf0f4;
        border-radius: 24px;
        background: #fff;
        transition: .2s ease;
    }

    .cp-program-card:hover {
        border-color: #fecdd3;
        box-shadow: 0 16px 35px rgba(15, 23, 42, .07);
        transform: translateY(-2px);
    }

    .cp-program-image {
        height: 255px;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        background: #f8fafc;
    }

    .cp-program-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: .45s ease;
    }

    .cp-program-card:hover .cp-program-image img {
        transform: scale(1.06);
    }

    .cp-no-image {
        width: 100%;
        height: 100%;
        display: grid;
        place-items: center;
        color: #9ca3af;
        font-size: 12px;
        font-weight: 800;
    }

    .cp-no-image i {
        display: block;
        color: #d90429;
        font-size: 30px;
        margin-bottom: 8px;
    }

    .cp-price-badge {
        position: absolute;
        top: 16px;
        left: 16px;
        padding: 10px 13px;
        border-radius: 14px;
        background: rgba(255, 255, 255, .92);
        backdrop-filter: blur(8px);
        box-shadow: 0 12px 22px rgba(15, 23, 42, .12);
    }

    .cp-price-badge span {
        display: block;
        color: #6b7280;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .12em;
        text-transform: uppercase;
        margin-bottom: 3px;
    }

    .cp-price-badge strong {
        color: #111827;
        font-size: 12px;
        font-weight: 900;
    }

    .cp-live-badge {
        position: absolute;
        left: 16px;
        bottom: 16px;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        background: rgba(217, 4, 41, .92);
        color: #fff;
        font-size: 9px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .cp-program-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-width: 0;
        padding: 10px 6px;
    }

    .cp-title-row span {
        display: block;
        color: #9ca3af;
        font-size: 10px;
        font-weight: 900;
        margin-bottom: 8px;
    }

    .cp-title-row h3 {
        margin: 0;
        color: #111827;
        font-size: 20px;
        font-weight: 900;
        text-transform: uppercase;
        line-height: 1.2;
        letter-spacing: -0.03em;
    }

    .cp-info-box {
        margin: 18px 0;
        padding: 16px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #edf0f4;
    }

    .cp-info-box span {
        display: block;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-bottom: 8px;
    }

    .cp-info-box p {
        margin: 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.55;
    }

    .cp-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .cp-actions form {
        margin: 0;
    }

    .cp-actions a,
    .cp-actions button {
        height: 40px;
        border: none;
        border-radius: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 14px;
        font-size: 11px;
        font-weight: 900;
        cursor: pointer;
        font-family: inherit;
    }

    .cp-actions .edit {
        flex: 1;
        background: #111827;
        color: #fff;
    }

    .cp-actions .delete {
        width: 42px;
        background: #fee2e2;
        color: #dc2626;
    }

    .cp-empty {
        grid-column: 1 / -1;
        padding: 45px;
        text-align: center;
        background: #f8fafc;
        border-radius: 18px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .cp-empty i {
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

    .cp-empty strong {
        display: block;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    .cp-empty span {
        display: block;
        margin-bottom: 16px;
    }

    .cp-empty a {
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

    .cp-pagination {
        margin-top: 18px;
    }

    @media (max-width: 1450px) {
        .cp-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .cp-program-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 900px) {
        .cp-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .cp-stats {
            grid-template-columns: 1fr;
        }

        .cp-program-card {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection