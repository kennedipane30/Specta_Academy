@extends('layouts.spekta')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold" style="color: #990000;">
                <i class="fas fa-chalkboard-teacher me-2"></i>Jadwal Mengajar (Dedicated Tutor)
            </h4>
            <p class="text-muted">Daftar permintaan tutor dari siswa yang telah dikonfirmasi oleh Admin.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabel Jadwal -->
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Data Siswa</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted">Materi</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted">Jadwal Belajar</th>
                            <th class="text-center py-3 text-uppercase small fw-bold text-muted">Status</th>
                            <th class="pe-4 py-3 text-uppercase small fw-bold text-muted text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- GANTI $classes MENJADI $jadwal SESUAI CONTROLLER --}}
                        @forelse($jadwal as $j)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">
                                        {{ strtoupper(substr($j->student->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $j->student->user->name ?? 'N/A' }}</div>
                                        <small class="text-muted">NISN: {{ $j->student->nisn }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border border-primary-subtle px-3 py-2">
                                    <i class="fas fa-book me-1"></i> {{ $j->material->title }}
                                </span>
                            </td>
                            <td>
                                <div class="small fw-bold"><i class="far fa-calendar-alt text-danger me-1"></i> {{ \Carbon\Carbon::parse($j->date)->format('d M Y') }}</div>
                                <div class="text-muted small"><i class="far fa-clock me-1"></i> {{ substr($j->time, 0, 5) }} WIB</div>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-success-subtle text-success border border-success px-3">
                                    TERKONFIRMASI
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                {{-- Tombol untuk menghubungi siswa via WhatsApp jika nomor hp tersedia --}}
                                @if($j->student->user->phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $j->student->user->phone) }}"
                                       target="_blank" class="btn btn-sm btn-outline-success shadow-sm">
                                        <i class="fab fa-whatsapp me-1"></i> Hubungi Siswa
                                    </a>
                                @else
                                    <span class="text-muted small italic">No. HP Kosong</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="opacity-50">
                                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                    <p class="mb-0">Belum ada jadwal tutor yang ditugaskan untuk Anda.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-circle {
        width: 38px; height: 38px; background-color: #990000; color: white;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 13px;
    }
    .bg-success-subtle { background-color: #e6fffa !important; }
    .table thead th { font-size: 11px; letter-spacing: 0.8px; border-bottom: 0; }
</style>
@stop
