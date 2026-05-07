@extends('layouts.spekta')
@section('title', 'Upload ' . $subject_name)

@section('content')
<div class="space-y-8">
    <div class="bg-white p-8 rounded-[30px] shadow-sm border border-gray-100">
        <h3 class="text-xl font-black text-gray-800 uppercase mb-6">Upload Modul ({{ $subject_name }})</h3>

        <form action="{{ route('pengajar.materi.store', $class->class_id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <input type="hidden" name="material_name" value="{{ $subject_name }}">

            <div>
                <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block ml-1">Minggu Ke-</label>
                <select name="week" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold" required>
                    @for($i=1; $i<=20; $i++)
                        <option value="{{ $i }}">Minggu {{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="md:col-span-1">
                <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block ml-1">Judul Materi</label>
                <input type="text" name="title" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold" placeholder="Judul Modul" required>
            </div>

            <div>
                <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block ml-1">File PDF</label>
                <input type="file" name="file_pdf" class="w-full text-xs" required>
            </div>

            <button type="submit" class="bg-[#990000] text-white p-4 rounded-2xl font-black text-[10px] uppercase hover:bg-red-800 transition">
                Simpan Materi
            </button>
        </form>
    </div>

    <div class="bg-white rounded-[30px] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase">Minggu</th>
                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase">Judul</th>
                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($materis as $m)
                <tr>
                    <td class="p-6 font-black text-[#990000] text-sm">MG-{{ $m->week }}</td>
                    <td class="p-6 font-bold text-gray-800 text-sm uppercase">{{ $m->title }}</td>
                    <td class="p-6">
                        <a href="{{ asset('storage/'.$m->file_path) }}" target="_blank" class="text-blue-600 font-bold text-[10px] underline uppercase">Download PDF</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
