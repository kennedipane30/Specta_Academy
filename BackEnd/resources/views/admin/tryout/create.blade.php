@extends('layouts.admin')

@section('title', 'Buat Paket Tryout')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white">
            <h4 class="mb-0">
                <i class="fas fa-file-upload"></i> Buat Paket Tryout
            </h4>
            <small>Upload soal tryout per mata pelajaran menggunakan file Excel/CSV</small>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('admin.tryout.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Judul Tryout <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   placeholder="Contoh: TO UTBK 2025 - Gelombang 1" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kelas <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->class_id }}" {{ old('class_id') == $class->class_id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Durasi (menit) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" 
                                   value="{{ old('duration_minutes', 120) }}" required min="30">
                            @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="2" placeholder="Deskripsi singkat tentang tryout ini">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Harga</label>
                            <select name="is_free" class="form-select" id="is_free">
                                <option value="1">Gratis</option>
                                <option value="0">Berbayar</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" id="price_container" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Harga (Rp)</label>
                            <input type="number" name="price" class="form-control" value="{{ old('price', 0) }}" min="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Maksimal Percobaan</label>
                            <input type="number" name="max_attempts" class="form-control" value="{{ old('max_attempts', 1) }}" min="1">
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="fw-bold mb-3">
                    <i class="fas fa-upload text-danger"></i> Upload Soal per Mata Pelajaran
                </h5>
                <p class="text-muted small">Setiap mata pelajaran diupload dalam file Excel/CSV terpisah</p>

                <div id="file-inputs-container">
                    <div class="file-input-group row mb-3 p-3 border rounded">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Mata Pelajaran</label>
                            <input type="text" name="subjects[]" class="form-control" placeholder="Contoh: Matematika" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">File Excel/CSV</label>
                            <input type="file" name="excel_files[]" class="form-control" accept=".xlsx,.csv" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Jumlah Soal</label>
                            <input type="number" name="jumlah_soal[]" class="form-control" placeholder="Otomatis" readonly disabled>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-success w-100" onclick="addFileInput()">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-3">
                    <h6><i class="fas fa-info-circle"></i> Format Excel yang diterima:</h6>
                    <table class="table table-bordered table-sm mt-2 bg-white">
                        <thead>
                            <tr><th>No</th><th>Pertanyaan</th><th>Gambar Pertanyaan</th><th>Opsi A</th><th>Opsi B</th><th>Opsi C</th><th>Opsi D</th><th>Kunci Jawaban</th><th>Pembahasan</th><th>Poin</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>1</td><td>Nilai x dari...</td><td>url_gambar.jpg</td><td>2</td><td>4</td><td>6</td><td>8</td><td>A</td><td>Gunakan rumus...</td><td>1</td></tr>
                        </tbody>
                    </table>
                    <a href="{{ route('admin.tryout.download-template') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-download"></i> Download Template Excel
                    </a>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-danger btn-lg px-5">
                        <i class="fas fa-cloud-upload-alt"></i> Upload & Buat Paket Tryout
                    </button>
                    <a href="{{ route('admin.tryout.index') }}" class="btn btn-secondary btn-lg px-4">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let fileCount = 1;

function addFileInput() {
    fileCount++;
    const container = document.getElementById('file-inputs-container');
    const newDiv = document.createElement('div');
    newDiv.className = 'file-input-group row mb-3 p-3 border rounded';
    newDiv.innerHTML = `
        <div class="col-md-3">
            <label class="form-label fw-bold">Mata Pelajaran</label>
            <input type="text" name="subjects[]" class="form-control" placeholder="Contoh: Matematika" required>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold">File Excel/CSV</label>
            <input type="file" name="excel_files[]" class="form-control" accept=".xlsx,.csv" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">Jumlah Soal</label>
            <input type="number" name="jumlah_soal[]" class="form-control" placeholder="Otomatis" readonly disabled>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-danger w-100" onclick="this.closest('.file-input-group').remove()">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
    `;
    container.appendChild(newDiv);
}

// Tampilkan/sembunyikan field harga
document.getElementById('is_free').addEventListener('change', function() {
    const priceContainer = document.getElementById('price_container');
    if (this.value === '0') {
        priceContainer.style.display = 'block';
    } else {
        priceContainer.style.display = 'none';
    }
});
</script>
@endsection