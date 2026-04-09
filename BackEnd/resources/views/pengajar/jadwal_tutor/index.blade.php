@extends('layouts.spekta')

@section('content')
<div class="container">
    <h3>Daftar Jadwal Mengajar (Dedicated Tutor)</h3>
    <div class="row mt-4">
        @forelse($jadwal as $j)
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-left-success">
                <div class="card-body">
                    <h5 class="card-title text-primary">{{ $j->material->nama_materi }}</h5>
                    <p class="card-text">
                        <strong>Siswa:</strong> {{ $j->student->user->name }} <br>
                        <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($j->date)->format('d M Y') }} <br>
                        <strong>Jam:</strong> {{ $j->time }}
                    </p>
                    <a href="https://wa.me/{{ $j->student->user->phone }}" class="btn btn-sm btn-success">Hubungi Siswa (WA)</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12"><p class="text-muted">Belum ada jadwal tutor yang dikonfirmasi.</p></div>
        @endforelse
    </div>
</div>
@stop
