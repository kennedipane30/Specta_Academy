@extends('layouts.spekta')
@section('title', 'Kelola Latihan ' . $subject_name)

@section('content')
<div class="space-y-10">
    {{-- ALERT --}}
    @if(session('success'))
        <div class="bg-green-600 text-white p-4 rounded-2xl mb-4 font-bold text-xs uppercase shadow-lg shadow-green-100">
            {{ session('success') }}
        </div>
    @endif

    {{-- FORM UPLOAD --}}
    <div class="bg-white p-10 rounded-[40px] shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-xl font-black text-gray-800 uppercase">Import Latihan ({{ $subject_name }})</h3>
            <a href="{{ route('pengajar.latihan.index') }}" class="text-xs font-bold text-gray-400 uppercase tracking-widest">&larr; Kembali</a>
        </div>

        <form action="{{ route('pengajar.latihan.store', $class->class_id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
            @csrf
            <input type="hidden" name="subject" value="{{ $subject_name }}">
            <div>
                <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block ml-1">Pilih Minggu</label>
                <select name="week" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold" required>
                    @for($i=1; $i<=20; $i++)
                        <option value="{{ $i }}">Minggu Ke-{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block ml-1">File CSV</label>
                <input type="file" name="file_csv" class="w-full text-xs font-bold text-gray-400" required>
            </div>
            <button type="submit" class="bg-blue-600 text-white p-4 rounded-2xl font-black text-[10px] uppercase shadow-lg hover:bg-blue-700 transition">
                Proses Import
            </button>
        </form>
    </div>

    {{-- TABEL RINGKASAN --}}
    <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 bg-gray-50/30">
            <h3 class="text-lg font-black text-gray-800 uppercase tracking-tighter">Ringkasan Soal Terupload</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50">
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        <th class="p-6">Pertemuan</th>
                        <th class="p-6">Status Konten</th>
                        <th class="p-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($practices as $p)
                    <tr class="hover:bg-gray-50/30 transition">
                        <td class="p-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center font-black text-xs">
                                    {{ $p->week }}
                                </div>
                                <span class="font-black text-gray-800 text-sm uppercase">Minggu ke-{{ $p->week }}</span>
                            </div>
                        </td>
                        <td class="p-6">
                            <span class="bg-green-50 text-green-600 px-4 py-1.5 rounded-full text-[10px] font-black uppercase shadow-sm">
                                {{ $p->total_soal }} Soal Tersedia
                            </span>
                        </td>
                        <td class="p-6 text-center">
                            <form action="{{ route('pengajar.latihan.destroy_week', [$class->class_id, $subject_name, $p->week]) }}" method="POST" onsubmit="return confirm('Hapus semua soal di Minggu ke-{{ $p->week }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 transition p-2">
                                    <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="p-20 text-center text-gray-300 font-bold uppercase text-xs italic">Belum ada data yang diunggah.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
