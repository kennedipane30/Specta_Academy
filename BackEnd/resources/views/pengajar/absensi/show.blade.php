@extends('layouts.spekta')
@section('title', 'Fill Attendance')

@section('content')
<div class="bg-white p-8 rounded-3xl shadow-md border-t-8 border-[#990000]">

    {{-- HEADER INFO --}}
    <div class="mb-8">
        <h3 class="text-xl font-black text-gray-800 uppercase">Materi: {{ $isAssigned->title }}</h3>
        <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Tanggal: {{ date('d F Y', strtotime($isAssigned->date)) }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-6 font-bold">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-6 font-bold">{{ session('error') }}</div>
    @endif

    <form action="{{ route('pengajar.absensi.store') }}" method="POST">
        @csrf
        <input type="hidden" name="schedule_id" value="{{ $isAssigned->schedule_id }}">

        <div class="overflow-x-auto border rounded-2xl">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-[10px] font-black uppercase text-gray-400">
                    <tr>
                        <th class="p-4 border-b">Nama Siswa</th>
                        <th class="p-4 border-b text-center">Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $s)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4">
                            <p class="font-bold text-gray-800 uppercase text-xs">{{ $s->user->name }}</p>
                            <small class="text-gray-400 font-bold">NISN: {{ $s->user->student->national_id_number ?? '-' }}</small>
                        </td>
                        <td class="p-4">
                            <div class="flex justify-center gap-4">
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="status[{{ $s->user->usersID }}]" value="present" required class="text-green-600 focus:ring-green-500">
                                    <span class="text-[10px] font-black text-green-600">HADIR</span>
                                </label>
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="status[{{ $s->user->usersID }}]" value="permission" class="text-yellow-600 focus:ring-yellow-500">
                                    <span class="text-[10px] font-black text-yellow-600">IZIN</span>
                                </label>
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="status[{{ $s->user->usersID }}]" value="absent" class="text-red-600 focus:ring-red-500">
                                    <span class="text-[10px] font-black text-red-600">ALPA</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="p-10 text-center text-gray-400 italic font-bold uppercase text-xs">
                            Belum ada siswa terdaftar yang aktif di kelas ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($siswas->count() > 0)
            <button type="submit" class="w-full mt-8 bg-green-600 text-white py-4 rounded-2xl font-black shadow-lg hover:bg-green-700 transition uppercase tracking-widest text-xs">
                SIMPAN ABSENSI SEKARANG
            </button>
        @endif
    </form>
</div>
@endsection
