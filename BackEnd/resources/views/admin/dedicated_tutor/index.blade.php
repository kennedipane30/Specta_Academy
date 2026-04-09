@extends('layouts.spekta')

@section('content')
<div class="container">
    <h3>Konfirmasi Pengajuan Dedicated Tutor</h3>
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Nama Siswa (NISN)</th>
                <th>Materi</th>
                <th>Pengajar</th>
                <th>Jadwal (Tgl/Jam)</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tutors as $t)
            <tr>
                <td>{{ $t->student->user->name }} ({{ $t->student->nisn }})</td>
                <td>{{ $t->material->nama_materi }}</td>
                <td>{{ $t->teacher->name }}</td>
                <td>{{ $t->date }} / {{ $t->time }}</td>
                <td>
                    <span class="badge {{ $t->status == 'pending' ? 'bg-warning' : ($t->status == 'confirmed' ? 'bg-success' : 'bg-danger') }}">
                        {{ strtoupper($t->status) }}
                    </span>
                </td>
                <td>
                    @if($t->status == 'pending')
                    <form action="{{ route('admin.tutor.update', $t->dedicated_tutorsID) }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="status" value="confirmed">
                        <button class="btn btn-sm btn-success">Konfirmasi</button>
                    </form>
                    <form action="{{ route('admin.tutor.update', $t->dedicated_tutorsID) }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <button class="btn btn-sm btn-danger">Tolak</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@stop
