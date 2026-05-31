@extends('layouts.spekta')

@section('title', 'Hasil Nilai Siswa')

@section('content')
<div class="cp-page">
    <section class="cp-header" style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            {{-- ✨ Gunakan null coalescing (??) untuk keamanan tambahan --}}
            <h1 style="font-size: 22px; font-weight: 900;">Hasil: {{ $tryout->title ?? 'Paket Tryout' }}</h1>
            <p style="color: #64748b;">Total {{ $results->count() }} siswa telah menyelesaikan ujian ini.</p>
        </div>
        <a href="{{ route('admin.scores.index') }}" class="cp-back-btn" style="text-decoration: none; color: #111827; font-weight: 800; background: #f1f5f9; padding: 10px 20px; border-radius: 12px;">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </section>

    <div class="cp-main-card" style="background: white; border-radius: 24px; padding: 25px; border: 1px solid #f1f5f9;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #f8fafc;">
                    <th style="padding: 15px; font-size: 11px; color: #94a3b8; text-transform: uppercase;">Nama Siswa</th>
                    <th style="padding: 15px; font-size: 11px; color: #94a3b8; text-transform: uppercase;">Benar</th>
                    <th style="padding: 15px; font-size: 11px; color: #94a3b8; text-transform: uppercase;">Skor Akhir</th>
                    <th style="padding: 15px; font-size: 11px; color: #94a3b8; text-transform: uppercase;">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $res)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 15px;">
                        <strong style="color: #111827; display: block;">{{ $res->user_data->name ?? 'Siswa tidak ditemukan' }}</strong>
                        <small style="color: #94a3b8;">{{ $res->user_data->email ?? '-' }}</small>
                    </td>
                    <td style="padding: 15px; font-weight: 700; color: #10b981;">{{ $res->total_correct }} Soal</td>
                    <td style="padding: 15px;">
                        <span style="background: #111827; color: white; padding: 5px 12px; border-radius: 8px; font-weight: 900; font-size: 16px;">
                            {{ $res->score }}
                        </span>
                    </td>
                    <td style="padding: 15px; color: #64748b; font-size: 12px;">
                        {{ $res->created_at ? $res->created_at->format('d M Y, H:i') : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding: 50px; text-align: center; color: #94a3b8; font-style: italic;">Belum ada siswa yang mengerjakan tryout ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection