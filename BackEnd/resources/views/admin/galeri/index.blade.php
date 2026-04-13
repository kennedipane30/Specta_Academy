@extends('layouts.spekta')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold" style="color: #990000;">
                <i class="fas fa-images me-2"></i>Manajemen Galeri Kegiatan
            </h4>
            <p class="text-muted small">Unggah dan kelola foto kegiatan Spekta Academy untuk ditampilkan di aplikasi mobile.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Form Unggah Foto (Kiri) -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-cloud-upload-alt me-2 text-danger"></i>Unggah Foto Baru</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Judul Kegiatan</label>
                            <input type="text" name="judul" class="form-control form-control-sm @error('judul') is-invalid @enderror" placeholder="Contoh: Tryout Akbar 2024" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Pilih Foto</label>
                            <input type="file" name="foto" class="form-control form-control-sm @error('foto') is-invalid @enderror" accept="image/*" required>
                            <div class="form-text small">Format: JPG, PNG, JPEG. Max: 2MB.</div>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Deskripsi (Opsional)</label>
                            <textarea name="deskripsi" class="form-control form-control-sm" rows="3" placeholder="Ceritakan singkat tentang kegiatan ini..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm w-100 shadow-sm py-2">
                            <i class="fas fa-save me-1"></i> Simpan ke Galeri
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel Daftar Galeri (Kanan) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3 small fw-bold">Preview</th>
                                    <th class="py-3 small fw-bold">Judul & Deskripsi</th>
                                    <th class="py-3 small fw-bold">Tanggal</th>
                                    <th class="pe-4 py-3 small fw-bold text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($galeri as $item)
                                <tr>
                                    <td class="ps-4">
                                        <img src="{{ Storage::url($item->foto) }}" class="rounded shadow-sm" style="width: 80px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->judul }}</div>
                                        <small class="text-muted d-block text-truncate" style="max-width: 250px;">{{ $item->deskripsi ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $item->created_at->format('d M Y') }}</small>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.galeri.edit', $item->id) }}" class="btn btn-sm btn-light border">
                                                <i class="fas fa-edit text-primary"></i>
                                            </a>
                                            <form action="{{ route('admin.galeri.destroy', $item->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light border ms-1" onclick="return confirm('Hapus foto ini dari galeri?')">
                                                    <i class="fas fa-trash-alt text-danger"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted small">
                                        <i class="fas fa-folder-open fa-3x mb-3 opacity-25 d-block"></i>
                                        Belum ada foto di galeri.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling tambahan agar seragam dengan tema Spekta */
    .form-control:focus {
        border-color: #990000;
        box-shadow: 0 0 0 0.2rem rgba(153, 0, 0, 0.1);
    }
    .btn-danger {
        background-color: #990000;
        border-color: #990000;
    }
    .btn-danger:hover {
        background-color: #7a0000;
    }
    .table thead th {
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
</style>
@stop
