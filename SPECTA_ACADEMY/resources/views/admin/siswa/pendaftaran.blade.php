@extends('layouts.spekta')
@section('title', 'Tambah Kelas - Daftar Tunggu')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-[#990000]">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 text-xs font-bold uppercase">
                <th class="p-4 border-b">Nama Siswa</th>
                <th class="p-4 border-b">Gmail</th>
                <th class="p-4 border-b">Program Dipilih</th>
                <th class="p-4 border-b">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr class="hover:bg-gray-50 text-sm">
                <td class="p-4 border-b font-bold">{{ $row->user->name }}</td>
                <td class="p-4 border-b">{{ $row->user->email }}</td>
                <td class="p-4 border-b text-red-700 font-bold uppercase">{{ $row->classModel->nama_program }}</td>
                <td class="p-4 border-b">
                    <!-- Tombol untuk melihat Detail A atau B -->
                    <a href="{{ route('admin.siswa.form_aktivasi', $row->enrollmentsID) }}"
                       class="bg-[#990000] text-white px-4 py-2 rounded-lg text-xs font-bold shadow-md hover:bg-red-800 transition">
                       TAMBAHKAN SISWA
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="p-10 text-center text-gray-400 italic">Tidak ada siswa yang menunggu pendaftaran kelas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
