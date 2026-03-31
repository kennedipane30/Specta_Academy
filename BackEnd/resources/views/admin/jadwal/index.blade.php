@extends('layouts.spekta')
@section('content')
<div class="bg-white p-8 rounded-2xl shadow-md border-t-8 border-[#990000]">
    <h3 class="text-xl font-bold mb-6 text-spekta uppercase">Kelola Jadwal Belajar</h3>

    <!-- Form Tambah -->
    <form action="{{ route('admin.jadwal.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10 bg-gray-50 p-6 rounded-xl">
        @csrf
        <select name="class_id" class="border p-2 rounded-lg" required>
            <option value="">Pilih Program...</option>
            @foreach($classes as $c) <option value="{{ $c->class_modelsID }}">{{ $c->nama_program }}</option> @endforeach
        </select>
        <select name="teacher_id" class="border p-2 rounded-lg" required>
            <option value="">Pilih Pengajar...</option>
            @foreach($teachers as $t) <option value="{{ $t->usersID }}">{{ $t->name }}</option> @endforeach
        </select>
        <input type="text" name="title" placeholder="Materi (Contoh: Psikologi)" class="border p-2 rounded-lg" required>
        <input type="date" name="date" class="border p-2 rounded-lg" required>
        <input type="time" name="start_time" class="border p-2 rounded-lg" required>
        <input type="time" name="end_time" class="border p-2 rounded-lg" required>
        <button type="submit" class="bg-spekta text-white font-bold rounded-lg py-2">TERBITKAN JADWAL</button>
    </form>

    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-100 text-xs font-bold uppercase">
            <tr>
                <th class="p-4 border-b">Waktu & Tanggal</th>
                <th class="p-4 border-b">Program</th>
                <th class="p-4 border-b">Materi & Guru</th>
                <th class="p-4 border-b">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwal as $row)
            <tr class="border-b text-sm">
                <td class="p-4"><b>{{ $row->date }}</b><br>{{ $row->start_time }} - {{ $row->end_time }}</td>
                <td class="p-4 font-bold text-red-700">{{ $row->classModel->nama_program }}</td>
                <td class="p-4"><b>{{ $row->title }}</b><br><small>Oleh: {{ $row->teacher->name }}</small></td>
                <td class="p-4">
                    <a href="{{ route('admin.jadwal.edit', $row->schedulesID) }}" class="text-blue-600 font-bold mr-2">Edit</a>
                    <form action="{{ route('admin.jadwal.destroy', $row->schedulesID) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button class="text-red-600 font-bold uppercase text-xs">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
