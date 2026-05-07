@extends('layouts.spekta')
@section('title', 'Manajemen Penugasan Materi')

@section('content')
<div class="space-y-10">

    {{-- FORM INPUT PENUGASAN --}}
    <div class="bg-white p-10 rounded-[40px] shadow-sm border border-gray-100">
        <div class="mb-8">
            <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">Tambah Penugasan Pengajar</h3>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Tentukan pengajar untuk mata pelajaran spesifik</p>
        </div>

        <form action="{{ route('admin.assignments.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Pilih Pengajar</label>
                <select name="teacher_id" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-[#990000] transition" required>
                    <option value="">-- Pilih Pengajar --</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->usersID }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Pilih Kelas</label>
                <select name="class_id" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-[#990000] transition" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->class_id }}">{{ $class->program_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Mata Pelajaran</label>
                <select name="subject_name" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-[#990000] transition" required>
                    <option value="">-- Pilih Subjek --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject }}">{{ $subject }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-[#990000] text-white p-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-800 shadow-lg shadow-red-100 transition transform active:scale-95">
                Tugaskan Sekarang
            </button>
        </form>
    </div>

    {{-- TABEL DATA PENUGASAN --}}
    <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pengajar</th>
                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kelas</th>
                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Mata Pelajaran</th>
                    <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($assignments as $assign)
                <tr class="hover:bg-gray-50/30 transition">
                    <td class="p-6">
                        <p class="font-black text-gray-800 text-sm uppercase">{{ $assign->user->name ?? 'N/A' }}</p>
                    </td>
                    <td class="p-6">
                        <span class="bg-red-50 text-[#990000] px-3 py-1 rounded-full text-[10px] font-black uppercase">{{ $assign->classModel->program_name ?? 'N/A' }}</span>
                    </td>
                    <td class="p-6">
                        <p class="font-bold text-gray-600 text-sm italic"># {{ $assign->subject_name }}</p>
                    </td>
                    <td class="p-6 text-center">
                        <form action="{{ route('admin.assignments.destroy', $assign->id) }}" method="POST" onsubmit="return confirm('Hapus penugasan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 transition">
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-10 text-center text-gray-400 font-bold uppercase text-xs">Belum ada penugasan pengajar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
