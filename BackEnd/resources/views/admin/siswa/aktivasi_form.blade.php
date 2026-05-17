@extends('layouts.spekta')

@section('title', 'Activation Form')
@section('subtitle', 'Audit dan aktivasi kelas siswa')

@section('content')
<div class="activation-page">

    <section class="activation-header">
        <div>
            <span>Enrollment Audit</span>
            <h1>{{ $enroll->user->name }}</h1>
            <p>Periksa data profil siswa, bukti pembayaran, dan tentukan masa aktif kelas.</p>
        </div>

        <a href="{{ route('admin.siswa.pendaftaran') }}">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </section>

    @if($errors->any())
        <div class="error-alert">
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

    <section class="activation-grid">

        <div class="profile-card">
            <div class="card-heading">
                <h2>Student Profile</h2>
                <span class="status-pill {{ $enroll->status }}">{{ ucfirst($enroll->status) }}</span>
            </div>

            <div class="student-identity">
                <div class="avatar">
                    {{ strtoupper(substr($enroll->user->name, 0, 1)) }}
                </div>
                <div>
                    <strong>{{ $enroll->user->name }}</strong>
                    <span>{{ $enroll->user->email }}</span>
                </div>
            </div>

            <div class="info-list">
                <div>
                    <span>Program Dipilih</span>
                    <strong>{{ $enroll->class->program_name ?? '-' }}</strong>
                </div>

                <div>
                    <span>National ID / NISN</span>
                    <strong>{{ $enroll->user->student->national_id_number ?? '-' }}</strong>
                </div>

                <div>
                    <span>Alamat</span>
                    <strong>{{ $enroll->user->student->address ?? '-' }}</strong>
                </div>

                <div>
                    <span>Tanggal Lahir</span>
                    <strong>{{ $enroll->user->student->date_of_birth ?? '-' }}</strong>
                </div>

                <div>
                    <span>Nama Orang Tua</span>
                    <strong>{{ $enroll->user->student->parent_name ?? '-' }}</strong>
                </div>

                <div>
                    <span>Nomor Orang Tua</span>
                    <strong>{{ $enroll->user->student->parent_phone ?? '-' }}</strong>
                </div>
            </div>
        </div>

        <div class="payment-card">
            <div class="card-heading">
                <h2>Payment Proof</h2>
                <span>{{ $enroll->created_at?->translatedFormat('d M Y') }}</span>
            </div>

            <div class="payment-proof">
                @if(!empty($enroll->payment_proof))
                    <img src="{{ asset('storage/' . $enroll->payment_proof) }}" alt="Payment Proof">
                @else
                    <div class="no-proof">
                        <i class="fa-solid fa-image"></i>
                        Tidak ada bukti pembayaran.
                    </div>
                @endif
            </div>

            <form action="{{ route('admin.siswa.proses_aktivasi', $enroll->enrollment_id) }}" method="POST" class="activation-form">
                @csrf

                <label for="durasi">Set Active Period</label>

                <div class="duration-input">
                    <input
                        type="number"
                        id="durasi"
                        name="durasi"
                        value="{{ old('durasi', 30) }}"
                        min="1"
                        required
                    >
                    <span>Hari</span>
                </div>

                <button type="submit">
                    <i class="fa-solid fa-circle-check"></i>
                    Confirm & Activate
                </button>
            </form>
        </div>

    </section>
</div>

<style>
    .activation-header {
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

    .activation-header span {
        display: block;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .18em;
        text-transform: uppercase;
        opacity: .78;
        margin-bottom: 8px;
    }

    .activation-header h1 {
        margin: 0 0 8px;
        font-size: 25px;
        font-weight: 900;
    }

    .activation-header p {
        margin: 0;
        font-size: 13px;
        font-weight: 500;
        opacity: .9;
    }

    .activation-header a {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        height: 42px;
        padding: 0 15px;
        border-radius: 12px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.22);
        color: #fff;
        font-size: 12px;
        font-weight: 900;
    }

    .error-alert {
        border-radius: 16px;
        padding: 15px 17px;
        margin-bottom: 18px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 700;
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .error-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .activation-grid {
        display: grid;
        grid-template-columns: .9fr 1.1fr;
        gap: 22px;
        align-items: start;
    }

    .profile-card,
    .payment-card {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 22px;
        box-shadow: 0 14px 35px rgba(15,23,42,.05);
        padding: 24px;
    }

    .card-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }

    .card-heading h2 {
        margin: 0;
        color: #111827;
        font-size: 17px;
        font-weight: 900;
    }

    .card-heading > span {
        color: #6b7280;
        font-size: 11px;
        font-weight: 800;
    }

    .status-pill {
        display: inline-flex;
        border-radius: 999px;
        padding: 7px 11px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .status-pill.pending {
        background: #ffedd5;
        color: #ea580c;
    }

    .status-pill.active {
        background: #dcfce7;
        color: #16a34a;
    }

    .status-pill.expired {
        background: #fee2e2;
        color: #dc2626;
    }

    .student-identity {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px;
        border-radius: 16px;
        background: #f8fafc;
        margin-bottom: 18px;
    }

    .avatar {
        width: 54px;
        height: 54px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: #ffe8ee;
        color: #d90429;
        font-size: 18px;
        font-weight: 900;
    }

    .student-identity strong {
        display: block;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
    }

    .student-identity span {
        display: block;
        margin-top: 4px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .info-list {
        display: grid;
        gap: 13px;
    }

    .info-list div {
        padding-bottom: 13px;
        border-bottom: 1px solid #edf0f4;
    }

    .info-list div:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .info-list span {
        display: block;
        color: #6b7280;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .06em;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .info-list strong {
        display: block;
        color: #111827;
        font-size: 13px;
        font-weight: 800;
        line-height: 1.45;
    }

    .payment-proof {
        width: 100%;
        min-height: 330px;
        background: #f8fafc;
        border: 1px dashed #d1d5db;
        border-radius: 18px;
        overflow: hidden;
        display: grid;
        place-items: center;
        margin-bottom: 20px;
    }

    .payment-proof img {
        width: 100%;
        height: 100%;
        max-height: 420px;
        object-fit: contain;
        background: #fff;
    }

    .no-proof {
        color: #6b7280;
        font-size: 13px;
        font-weight: 800;
        display: grid;
        place-items: center;
        gap: 9px;
    }

    .no-proof i {
        font-size: 28px;
        color: #d90429;
    }

    .activation-form {
        border-radius: 18px;
        background: #fff7f9;
        border: 1px solid #fecdd3;
        padding: 18px;
    }

    .activation-form label {
        display: block;
        color: #374151;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: 9px;
    }

    .duration-input {
        display: grid;
        grid-template-columns: 1fr 80px;
        margin-bottom: 14px;
    }

    .duration-input input {
        height: 46px;
        border: 1px solid #e5e7eb;
        border-radius: 13px 0 0 13px;
        background: #fff;
        padding: 0 14px;
        color: #111827;
        font-size: 13px;
        font-weight: 800;
        outline: none;
    }

    .duration-input span {
        height: 46px;
        display: grid;
        place-items: center;
        border-radius: 0 13px 13px 0;
        background: #d90429;
        color: #fff;
        font-size: 12px;
        font-weight: 900;
    }

    .activation-form button {
        width: 100%;
        height: 48px;
        border: none;
        border-radius: 14px;
        background: #16a34a;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        font-size: 12px;
        font-weight: 900;
        cursor: pointer;
        box-shadow: 0 14px 28px rgba(22, 163, 74, .2);
    }

    @media (max-width: 1024px) {
        .activation-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .activation-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection