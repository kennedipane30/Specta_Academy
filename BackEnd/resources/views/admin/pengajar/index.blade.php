@extends('layouts.spekta')
@section('title', 'Manajemen Pengajar')

@section('content')
<div class="bg-white p-8 rounded-2xl shadow-md border-t-8 border-[#990000]">
    <h3 class="text-xl font-bold mb-6 text-spekta uppercase">Tambah Akun Pengajar</h3>

    <!-- Form Tambah -->
    <form action="{{ route('admin.manajemen-pengajar.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-10 bg-gray-50 p-6 rounded-xl">
        @csrf
        <input type="text" name="name" placeholder="Nama Lengkap" class="border p-2 rounded-lg" required>
        <input type="email" name="email" placeholder="Email (Gmail)" class="border p-2 rounded-lg" required>
        <input type="text" name="phone" placeholder="No HP/WA" class="border p-2 rounded-lg" required>
        <input type="password" name="password" placeholder="Password" class="border p-2 rounded-lg" required>
        <button type="submit" class="bg-spekta text-white font-bold rounded-lg py-2 col-span-1 md:col-span-4">DAFTARKAN PENGAJAR</button>
    </form>

    <!-- Tabel Daftar Pengajar -->
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-100 text-xs font-bold uppercase">
            <tr>
                <th class="p-4 border-b">Nama Pengajar</th>
                <th class="p-4 border-b">Email</th>
                <th class="p-4 border-b">No HP</th>
                <th class="p-4 border-b">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($teachers as $t)
            <tr class="border-b text-sm">
                <td class="p-4 font-bold">{{ $t->name }}</td>
                <td class="p-4">{{ $t->email }}</td>
                <td class="p-4">{{ $t->phone }}</td>
                <td class="p-4">
                    <form action="{{ route('admin.manajemen-pengajar.destroy', $t->usersID) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="text-red-600 font-bold uppercase text-xs" onclick="return confirm('Hapus akun ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
