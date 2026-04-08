@extends('layouts.spekta')
@section('content')
<div class="bg-white p-8 rounded-2xl shadow-md border-t-8 border-[#990000]">
    <h3 class="text-xl font-black mb-6 text-spekta uppercase tracking-tight">Kelola Jadwal Belajar</h3>

    <!-- Form Tambah -->
    <form action="{{ route('admin.jadwal.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10 bg-gray-50 p-6 rounded-2xl border border-gray-100">
        @csrf

        {{-- Pilih Program --}}
        <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-400 mb-1 ml-1 uppercase">Pilih Program</label>
            <select name="class_id" id="class-select" class="border p-2.5 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 outline-none transition" required>
                <option value="">Pilih Program...</option>
                @foreach($classes as $c)
                    <option value="{{ $c->class_modelsID }}">{{ $c->nama_program }}</option>
                @endforeach
            </select>
        </div>

        {{-- Pilih Pengajar --}}
        <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-400 mb-1 ml-1 uppercase">Pilih Pengajar</label>
            <select name="teacher_id" class="border p-2.5 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 outline-none transition" required>
                <option value="">Pilih Pengajar...</option>
                @foreach($teachers as $t)
                    <option value="{{ $t->usersID }}">{{ $t->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Dropdown Materi (Dinamis) --}}
        <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-400 mb-1 ml-1 uppercase">Materi Pelajaran</label>
            <select name="title" id="materi-select" class="border p-2.5 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 outline-none transition bg-gray-100 cursor-not-allowed" required disabled>
                <option value="">Pilih Program dahulu...</option>
            </select>
        </div>

        <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-400 mb-1 ml-1 uppercase">Tanggal</label>
            <input type="date" name="date" class="border p-2.5 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 outline-none transition" required>
        </div>

        <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-400 mb-1 ml-1 uppercase">Jam Mulai</label>
            <input type="time" name="start_time" class="border p-2.5 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 outline-none transition" required>
        </div>

        <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-400 mb-1 ml-1 uppercase">Jam Selesai</label>
            <input type="time" name="end_time" class="border p-2.5 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 outline-none transition" required>
        </div>

        <div class="md:col-span-3 mt-4">
            <button type="submit" class="w-full bg-[#990000] text-white font-black py-3 rounded-2xl shadow-lg shadow-red-100 hover:bg-red-800 transition active:scale-95 uppercase tracking-widest text-xs">
                Terbitkan Jadwal Belajar
            </button>
        </div>
    </form>

    {{-- Tabel tetap sama seperti kode Anda sebelumnya --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100 text-[10px] font-black uppercase text-gray-500 tracking-widest">
                <tr>
                    <th class="p-4 border-b">Waktu & Tanggal</th>
                    <th class="p-4 border-b">Program</th>
                    <th class="p-4 border-b">Materi & Guru</th>
                    <th class="p-4 border-b">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jadwal as $row)
                <tr class="border-b text-sm hover:bg-gray-50 transition">
                    <td class="p-4"><span class="font-black text-gray-800">{{ date('d M Y', strtotime($row->date)) }}</span><br><span class="text-xs text-gray-400 font-bold uppercase">{{ $row->start_time }} - {{ $row->end_time }} WIB</span></td>
                    <td class="p-4 font-black text-red-700 uppercase tracking-tighter">{{ $row->classModel->nama_program }}</td>
                    <td class="p-4"><span class="font-black text-gray-800 uppercase">{{ $row->title }}</span><br><span class="text-[10px] font-bold text-gray-400 uppercase">Oleh: {{ $row->teacher->name }}</span></td>
                    <td class="p-4">
                        <div class="flex items-center gap-4">
                            <a href="{{ route('admin.jadwal.edit', $row->schedulesID) }}" class="text-blue-600 font-black text-[10px] uppercase hover:underline">Edit</a>
                            <form action="{{ route('admin.jadwal.destroy', $row->schedulesID) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="text-red-600 font-black text-[10px] uppercase hover:underline" onclick="return confirm('Hapus jadwal ini?')">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- SCRIPT JAVASCRIPT UNTUK DROPDOWN DINAMIS --}}
<script>
    document.getElementById('class-select').addEventListener('change', function() {
        let classID = this.value;
        let materiSelect = document.getElementById('materi-select');

        if (classID) {
            // Aktifkan loading/status
            materiSelect.innerHTML = '<option value="">Memuat Materi...</option>';
            materiSelect.disabled = false;
            materiSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');

            // Ambil data dari server
            fetch(`/admin/get-materi/${classID}`)
                .then(response => response.json())
                .then(data => {
                    materiSelect.innerHTML = '<option value="">Pilih Materi...</option>';
                    if (data.length > 0) {
                        data.forEach(item => {
                            let option = document.createElement('option');
                            option.value = item.title;
                            option.text = item.title;
                            materiSelect.appendChild(option);
                        });
                    } else {
                        materiSelect.innerHTML = '<option value="">Tidak ada materi di kelas ini</option>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    materiSelect.innerHTML = '<option value="">Gagal memuat materi</option>';
                });
        } else {
            // Jika program dikosongkan
            materiSelect.innerHTML = '<option value="">Pilih Program dahulu...</option>';
            materiSelect.disabled = true;
            materiSelect.classList.add('bg-gray-100', 'cursor-not-allowed');
        }
    });
</script>
@endsection
