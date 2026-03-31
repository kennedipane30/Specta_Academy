@extends('layouts.spekta')
@section('content')
<div class="bg-white p-8 rounded-2xl shadow-md border-t-8 border-green-600">
    <h3 class="text-xl font-bold">Materi: {{ $isAssigned->title }}</h3>
    <p class="text-sm text-gray-500 mb-6">Tanggal: {{ date('d M Y') }}</p>

    <form action="{{ route('pengajar.absensi.store') }}" method="POST">
        @csrf
        <input type="hidden" name="schedule_id" value="{{ $isAssigned->schedulesID }}">

        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-100 uppercase text-xs">
                    <th class="p-4">Nama Siswa</th>
                    <th class="p-4 text-center">Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswas as $s)
                <tr class="border-b">
                    <td class="p-4"><b>{{ $s->user->name }}</b></td>
                    <td class="p-4 flex justify-center gap-4">
                        <label class="flex items-center gap-1 text-xs">
                            <input type="radio" name="status[{{ $s->user->usersID }}]" value="hadir" checked> Hadir
                        </label>
                        <label class="flex items-center gap-1 text-xs">
                            <input type="radio" name="status[{{ $s->user->usersID }}]" value="izin"> Izin
                        </label>
                        <label class="flex items-center gap-1 text-xs">
                            <input type="radio" name="status[{{ $s->user->usersID }}]" value="alpa"> Alpa
                        </label>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="mt-8 bg-green-600 text-white px-8 py-3 rounded-xl font-bold w-full shadow-lg">SIMPAN ABSENSI</button>
    </form>
</div>
@endsection
