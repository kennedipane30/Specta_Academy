@extends('layouts.spekta')
@section('title', 'Dashboard Administrasi')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-spekta">
        <p class="text-gray-500 text-sm">Siswa Aktif</p>
        <h3 class="text-3xl font-bold">{{ $total_siswa }}</h3>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-spekta">
        <p class="text-gray-500 text-sm">Pengajar</p>
        <h3 class="text-3xl font-bold">{{ $total_pengajar }}</h3>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-yellow-500">
        <p class="text-gray-500 text-sm">Pembayaran Pending</p>
        <h3 class="text-3xl font-bold text-yellow-600">5</h3>
    </div>
</div>

<div class="bg-white p-8 rounded-xl shadow-sm">
    <h3 class="text-spekta font-bold text-xl mb-4">Log Aktivitas Terbaru</h3>
    <table class="w-full text-left">
        <thead>
            <tr class="border-b">
                <th class="py-2">Waktu</th>
                <th>Aksi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b">
                <td class="py-3 text-sm">10 Menit lalu</td>
                <td class="text-sm">Siswa "Budi" mendaftar Kelas SMA</td>
                <td><span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Sukses</span></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
