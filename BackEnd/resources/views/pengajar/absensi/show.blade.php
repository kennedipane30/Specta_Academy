@extends('layouts.spekta')
@section('content')

<div class="bg-white p-8 rounded-2xl shadow-md border-t-8 border-green-600">

    @if($hasAttendance)
        {{-- TAMPILAN SETELAH SIMPAN --}}
        <div class="text-center py-10">
            <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800">Absensi Berhasil Tersimpan!</h3>
            <p class="text-gray-500 mt-2">
                Absensi untuk materi <b>{{ $isAssigned->title }}</b> <br>
                Tanggal: <b>{{ date('d M Y') }}</b> telah tercatat di sistem.
            </p>

            <div class="mt-8 p-4 bg-gray-50 rounded-xl border border-dashed border-gray-300 inline-block">
                <p class="text-sm text-gray-600">
                    Ingin melihat daftar hadir lengkap?
                    <a href="{{ route('pengajar.absensi.detail', $isAssigned->schedulesID) }}" class="text-green-600 font-bold underline hover:text-green-800 transition">
                        Klik di sini
                    </a>
                </p>
            </div>

            <div class="mt-6">
                <a href="{{ route('pengajar.absensi.index') }}" class="text-gray-400 text-sm font-semibold hover:text-gray-600">
                    &larr; Kembali ke Daftar Kelas
                </a>
            </div>
        </div>

    @else
        {{-- FORM INPUT (Tampil jika belum simpan) --}}
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
                            <label class="flex items-center gap-1 text-xs cursor-pointer"><input type="radio" name="status[{{ $s->user->usersID }}]" value="hadir" checked> Hadir</label>
                            <label class="flex items-center gap-1 text-xs cursor-pointer"><input type="radio" name="status[{{ $s->user->usersID }}]" value="izin"> Izin</label>
                            <label class="flex items-center gap-1 text-xs cursor-pointer"><input type="radio" name="status[{{ $s->user->usersID }}]" value="alpa"> Alpa</label>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="submit" class="mt-8 bg-green-600 text-white px-8 py-3 rounded-xl font-bold w-full shadow-lg hover:bg-green-700 transition">
                SIMPAN ABSENSI
            </button>
        </form>
    @endif

</div>
@endsection
