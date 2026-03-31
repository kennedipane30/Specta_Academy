@extends('layouts.spekta')
@section('title', 'Dashboard Pengajar')

@section('content')
<div class="bg-white p-10 rounded-2xl shadow-sm border-t-8 border-spekta">
    <h1 class="text-3xl font-bold text-gray-800">Selamat Datang, Bapak/Ibu Guru!</h1>
    <p class="text-gray-600 mt-4">Di portal ini, Anda dapat mengelola materi pembelajaran, melakukan absensi, dan memantau perkembangan nilai tryout siswa Spekta Academy.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-10">
        <div class="p-6 bg-red-50 rounded-xl border border-red-100 flex items-center space-x-4">
            <div class="text-3xl">📝</div>
            <div>
                <h4 class="font-bold">Absensi Hari Ini</h4>
                <a href="/pengajar/absensi" class="text-spekta text-sm font-bold underline">Klik untuk Absen</a>
            </div>
        </div>
        <div class="p-6 bg-red-50 rounded-xl border border-red-100 flex items-center space-x-4">
            <div class="text-3xl">📚</div>
            <div>
                <h4 class="font-bold">Materi Terupload</h4>
                <p class="text-sm text-gray-500">12 File PDF & Video</p>
            </div>
        </div>
    </div>
</div>
@endsection
