@extends('layouts.spekta')
@section('title', 'Dashboard Utama')

@section('content')
<div class="container-fluid py-4">

    <!-- Welcome Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold mb-0" style="color: #990000;">Halo, Administrator!</h3>
            <p class="text-muted">Selamat datang kembali di Panel Kendali Spekta Academy.</p>
        </div>
        <div class="text-end">
            <div class="text-dark fw-bold">{{ now()->translatedFormat('d F Y') }}</div>
            <small class="text-muted">Sistem Dashboard v2.0</small>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-4 mb-5">
        <!-- Siswa Card -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; border-left: 5px solid #990000 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small text-uppercase fw-bold mb-1">Total Siswa</p>
                            <h2 class="fw-black mb-0">{{ $total_siswa }}</h2>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger">
                            <i class="fas fa-user-graduate fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pengajar Card -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; border-left: 5px solid #4e73df !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small text-uppercase fw-bold mb-1">Pengajar</p>
                            <h2 class="fw-black mb-0">{{ $total_pengajar }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendaftaran Pending -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; border-left: 5px solid #f6e05e !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small text-uppercase fw-bold mb-1">Aktivasi Kelas</p>
                            <h2 class="fw-black mb-0 text-warning">{{ $pendaftaran_pending }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                            <i class="fas fa-key fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tutor Pending -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; border-left: 5px solid #38a169 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small text-uppercase fw-bold mb-1">Request Tutor</p>
                            <h2 class="fw-black mb-0 text-success">{{ $tutor_pending }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                            <i class="fas fa-headset fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Activities -->
    <div class="row g-4">
        <!-- Log Activity -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0 text-dark">Log Aktivitas Terbaru</h5>
                    <button class="btn btn-sm btn-light border small text-muted">Lihat Semua</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead class="text-muted small">
                            <tr>
                                <th class="ps-0">Waktu</th>
                                <th>Keterangan Aktivitas</th>
                                <th class="text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-bottom-faded">
                                <td class="ps-0 small text-muted">10 Menit lalu</td>
                                <td class="small fw-semibold text-dark">Siswa "Budi" mendaftar Kelas SMA Reguler</td>
                                <td class="text-end">
                                    <span class="badge bg-success-subtle text-success px-3">SUKSES</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-0 small text-muted">1 Jam lalu</td>
                                <td class="small fw-semibold text-dark">Admin memperbarui data materi PTN</td>
                                <td class="text-end">
                                    <span class="badge bg-primary-subtle text-primary px-3">UPDATE</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Access Buttons -->
        <div class="col-lg-4">
            <div class="card border-0 bg-dark text-white p-4 h-100 shadow" style="border-radius: 15px; background: linear-gradient(135deg, #2d3436 0%, #000000 100%);">
                <h5 class="fw-bold mb-4"><i class="fas fa-rocket me-2 text-warning"></i>Akses Cepat</h5>
                <div class="d-grid gap-3">
                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-light text-start border-0 py-3 px-3 shadow-sm rounded-3">
                        <i class="fas fa-users-cog me-3"></i> Manajemen Siswa
                    </a>
                    <a href="{{ route('admin.tutor.index') }}" class="btn btn-outline-light text-start border-0 py-3 px-3 shadow-sm rounded-3">
                        <i class="fas fa-user-check me-3"></i> Konfirmasi Tutor Siswa
                    </a>
                    <a href="{{ route('admin.promo.index') }}" class="btn btn-outline-light text-start border-0 py-3 px-3 shadow-sm rounded-3">
                        <i class="fas fa-tags me-3"></i> Buat Kode Promo Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .border-bottom-faded { border-bottom: 1px solid rgba(0,0,0,0.05); }

    /* Styling Badge Modern */
    .bg-success-subtle { background-color: #e6fffa; }
    .bg-primary-subtle { background-color: #ebf4ff; }

    /* Animation Hover pada Card */
    .card { transition: all 0.3s ease; }
    .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }

    /* Sidebar/Button style custom */
    .btn-outline-light:hover {
        background-color: rgba(255,255,255,0.1) !important;
        transform: translateX(10px);
    }
</style>
@endsection
