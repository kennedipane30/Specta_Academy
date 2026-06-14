<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi {{ $subject }} - Minggu {{ $week }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', 'DejaVu Sans', sans-serif;
            background: white;
            padding: 30px;
            line-height: 1.4;
            font-size: 12px;
        }

        /* Container Utama */
        .pdf-container {
            max-width: 100%;
            margin: 0 auto;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e53935;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #c5352c;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 12px;
            color: #2ea8ab;
            font-weight: bold;
        }

        /* Info Card - Menggunakan Tabel */
        .info-card {
            background: #f5f5f5;
            padding: 12px;
            margin-bottom: 20px;
            border-left: 3px solid #e53935;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 5px 8px;
            font-size: 11px;
        }

        .info-label {
            font-weight: bold;
            color: #666;
            width: 120px;
        }

        .info-value {
            color: #333;
            font-weight: normal;
        }

        /* STATISTIK - Menggunakan TABEL untuk memastikan horizontal */
        .stats-wrapper {
            margin-bottom: 25px;
        }

        .stats-table {
            width: 100%;
            border-collapse: collapse;
        }

        .stats-table td {
            padding: 0 10px;
        }

        .stat-box {
            text-align: center;
            padding: 15px 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: white;
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            display: block;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
            margin-top: 5px;
        }

        .stat-hadir .stat-number { color: #28a745; }
        .stat-izin .stat-number { color: #ff9800; }
        .stat-alpa .stat-number { color: #dc3545; }

        /* Tabel Data Siswa */
        .table-wrapper {
            margin: 20px 0 30px;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .attendance-table th {
            background: #f5f5f5;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            border-bottom: 2px solid #ddd;
        }

        .attendance-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .col-no {
            width: 50px;
            text-align: center;
        }

        .col-name {
            width: 40%;
        }

        .col-status {
            width: 25%;
        }

        .col-date {
            width: 30%;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-hadir {
            background: #d4edda;
            color: #155724;
        }

        .badge-izin {
            background: #fff3cd;
            color: #856404;
        }

        .badge-alpa {
            background: #f8d7da;
            color: #721c24;
        }

        /* TANDA TANGAN - Menggunakan TABEL untuk horizontal */
        .signature-wrapper {
            margin-top: 40px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            text-align: center;
            padding: 0 20px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            margin-top: 40px;
            padding-top: 5px;
            border-top: 1px solid #333;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        .signature-name {
            font-weight: bold;
            font-size: 11px;
            margin-top: 8px;
        }

        .signature-title {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
        }

        /* Footer */
        .page-footer {
            margin-top: 40px;
            padding-top: 10px;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #eee;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="pdf-container">

        {{-- HEADER --}}
        <div class="header">
            <div class="logo">SPEKTA ACADEMY</div>
            <div class="subtitle">LAPORAN REKAPITULASI ABSENSI MINGGUAN</div>
        </div>

        {{-- INFO KELAS --}}
        <div class="info-card">
            <table class="info-table">
                <tr>
                    <td class="info-label">KELAS / PROGRAM</td>
                    <td class="info-value">{{ $class->program_name }}</td>
                    <td class="info-label">PERIODE</td>
                    <td class="info-value">{{ date('Y') }}</td>
                </tr>
                <tr>
                    <td class="info-label">MATA PELAJARAN</td>
                    <td class="info-value">{{ $subject }}</td>
                    <td class="info-label">MINGGU KE-</td>
                    <td class="info-value">{{ $week }}</td>
                </tr>
                <tr>
                    <td class="info-label">TANGGAL CETAK</td>
                    <td class="info-value">{{ date('d F Y') }}</td>
                    <td class="info-label">TOTAL SISWA</td>
                    <td class="info-value">{{ $total }} Orang</td>
                </tr>
            </table>
        </div>

        {{-- STATISTIK - MENGGUNAKAN TABEL UNTUK MEMASTIKAN HORIZONTAL --}}
        <div class="stats-wrapper">
            <table class="stats-table">
                <tr>
                    <td width="33%">
                        <div class="stat-box stat-hadir">
                            <span class="stat-number">{{ $hadir }}</span>
                            <span class="stat-label">HADIR</span>
                        </div>
                    </td>
                    <td width="33%">
                        <div class="stat-box stat-izin">
                            <span class="stat-number">{{ $izin }}</span>
                            <span class="stat-label">IZIN</span>
                        </div>
                    </td>
                    <td width="33%">
                        <div class="stat-box stat-alpa">
                            <span class="stat-number">{{ $alpa }}</span>
                            <span class="stat-label">ALPA</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- TABEL DAFTAR SISWA --}}
        <div class="table-wrapper">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th class="col-no">NO</th>
                        <th class="col-name">NAMA SISWA</th>
                        <th class="col-status">STATUS</th>
                        <th class="col-date">TANGGAL INPUT</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $row)
                    <tr>
                        <td class="col-no text-center">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="col-name">{{ $row->user->name ?? '-' }}</td>
                        <td class="col-status">
                            @if($row->status == 'h')
                                <span class="status-badge badge-hadir">HADIR</span>
                            @elseif($row->status == 'i')
                                <span class="status-badge badge-izin">IZIN</span>
                            @else
                                <span class="status-badge badge-alpa">ALPA</span>
                            @endif
                        </td>
                        <td class="col-date">{{ $row->date ? date('d/m/Y', strtotime($row->date)) : '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center" style="padding: 30px; color: #999;">
                            Tidak ada data absensi untuk minggu ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- TANDA TANGAN - MENGGUNAKAN TABEL UNTUK HORIZONTAL --}}
        <div class="signature-wrapper">
            <table class="signature-table">
                <tr>
                    <td width="50%">
                        <div class="signature-box">
                            <div class="signature-line"></div>
                            <div class="signature-name">Kepala Program</div>
                            <div class="signature-title">Mengetahui</div>
                        </div>
                    </td>
                    <td width="50%">
                        <div class="signature-box">
                            <div class="signature-line"></div>
                            <div class="signature-name">{{ Auth::user()->name ?? 'Pengajar' }}</div>
                            <div class="signature-title">Pengajar</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- FOOTER --}}
        <div class="page-footer">
            Dokumen ini dicetak secara otomatis dari sistem Spekta Academy<br>
            {{ date('d/m/Y H:i:s') }} WIB
        </div>

    </div>
</body>
</html>
