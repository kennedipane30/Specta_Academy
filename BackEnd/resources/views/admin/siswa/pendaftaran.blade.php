@extends('layouts.spekta')

@section('title', 'Waiting List Enrollment')
@section('subtitle', 'Verifikasi pendaftaran kelas siswa Spekta Academy')

@section('content')
<div class="enrollment-page">

    <section class="enrollment-header">
        <div>
            <span>Konfirmasi Kelas</span>
            <h1>Waiting List Enrollment</h1>
            <p>Periksa bukti pembayaran dan aktifkan program kelas siswa yang masih menunggu verifikasi.</p>
        </div>

        <div class="header-count">
            <i class="fa-solid fa-clock"></i>
            <div>
                <strong>{{ number_format($totalPending ?? $data->count()) }}</strong>
                <small>Pending</small>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="success-alert">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <section class="enrollment-card">
        <div class="card-heading">
            <h2>Daftar Pendaftaran Pending</h2>
            <a href="{{ route('admin.siswa.index') }}">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Siswa
            </a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Selected Program</th>
                        <th>Tanggal Daftar</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($data as $row)
                        <tr>
                            <td>
                                <div class="student-profile">
                                    <div class="avatar">
                                        {{ strtoupper(substr($row->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $row->user->name ?? '-' }}</strong>
                                        <span>NIS: {{ $row->user->student->national_id_number ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>

                            <td>{{ $row->user->email ?? '-' }}</td>
                            <td>
                                <span class="program-pill">
                                    {{ $row->class->program_name ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $row->created_at?->translatedFormat('d M Y, H:i') }}</td>

                            <td class="text-right">
                                <a href="{{ route('admin.siswa.form_aktivasi', $row->enrollment_id) }}" class="activate-button">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Audit & Aktivasi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <strong>Tidak ada enrollment pending.</strong>
                                    <span>Semua pendaftaran kelas sudah diverifikasi.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<style>
    .enrollment-header {
        background: linear-gradient(120deg, #c90025 0%, #7b001b 48%, #351225 100%);
        color: #fff;
        border-radius: 22px;
        padding: 30px 34px;
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        box-shadow: 0 18px 35px rgba(134, 0, 24, .22);
    }

    .enrollment-header span {
        display: block;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .18em;
        text-transform: uppercase;
        opacity: .78;
        margin-bottom: 8px;
    }

    .enrollment-header h1 {
        margin: 0 0 8px;
        font-size: 25px;
        font-weight: 900;
    }

    .enrollment-header p {
        margin: 0;
        font-size: 13px;
        font-weight: 500;
        opacity: .9;
    }

    .header-count {
        display: flex;
        align-items: center;
        gap: 14px;
        border-left: 1px solid rgba(255,255,255,.25);
        padding-left: 28px;
    }

    .header-count i {
        width: 46px;
        height: 46px;
        display: grid;
        place-items: center;
        border-radius: 14px;
        border: 1px solid rgba(255,255,255,.45);
    }

    .header-count strong {
        display: block;
        font-size: 25px;
        font-weight: 900;
    }

    .header-count small {
        display: block;
        font-size: 11px;
        font-weight: 700;
        opacity: .84;
    }

    .success-alert {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
        border-radius: 16px;
        padding: 15px 17px;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 800;
    }

    .enrollment-card {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 22px;
        box-shadow: 0 14px 35px rgba(15,23,42,.05);
        padding: 22px;
    }

    .card-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .card-heading h2 {
        margin: 0;
        color: #111827;
        font-size: 17px;
        font-weight: 900;
    }

    .card-heading a {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #d90429;
        font-size: 12px;
        font-weight: 900;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        padding: 14px 12px;
        border-bottom: 1px solid #edf0f4;
        color: #6b7280;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .06em;
        text-transform: uppercase;
        text-align: left;
        white-space: nowrap;
    }

    td {
        padding: 15px 12px;
        border-bottom: 1px solid #edf0f4;
        color: #374151;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
    }

    tbody tr:hover {
        background: #fff7f9;
    }

    .student-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar {
        width: 40px;
        height: 40px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: #ffe8ee;
        color: #d90429;
        font-weight: 900;
    }

    .student-profile strong {
        display: block;
        color: #111827;
        font-size: 13px;
        font-weight: 900;
    }

    .student-profile span {
        display: block;
        color: #6b7280;
        font-size: 10px;
        font-weight: 700;
        margin-top: 3px;
    }

    .program-pill {
        display: inline-flex;
        border-radius: 999px;
        background: #ffe8ee;
        color: #d90429;
        padding: 7px 11px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .text-right {
        text-align: right;
    }

    .activate-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 38px;
        padding: 0 14px;
        border-radius: 11px;
        background: #d90429;
        color: #fff;
        font-size: 11px;
        font-weight: 900;
        box-shadow: 0 12px 25px rgba(217, 4, 41, .18);
    }

    .empty-state {
        padding: 34px;
        text-align: center;
        background: #f8fafc;
        border-radius: 14px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .empty-state i {
        width: 54px;
        height: 54px;
        margin: 0 auto 14px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: #dcfce7;
        color: #16a34a;
        font-size: 20px;
    }

    .empty-state strong {
        display: block;
        color: #111827;
        font-size: 14px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    @media (max-width: 768px) {
        .enrollment-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-count {
            border-left: none;
            padding-left: 0;
        }

        .card-heading {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection