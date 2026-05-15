@extends('layouts.spekta')
@section('content')
<div class="bg-white p-10 rounded-[40px] shadow-sm">
    <div class="flex justify-between items-center mb-10">
        <h3 class="text-xl font-black uppercase">Recap {{ $subject }} - Week {{ $week }}</h3>
        <a href="{{ route('pengajar.absensi.weeks', [$class->class_id, $subject]) }}" class="text-xs font-bold text-gray-400">&larr; KEMBALI</a>
    </div>

    <table class="w-full text-left">
        <thead>
            <tr class="text-[10px] font-black text-gray-400 uppercase border-b">
                <th class="pb-4">Nama Siswa</th>
                <th class="pb-4 text-center">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($data as $row)
            <tr>
                <td class="py-4 font-bold text-sm uppercase">{{ $row->student->name }}</td>
                <td class="py-4 text-center">
                    <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase
                        {{ $row->status == 'h' ? 'bg-green-100 text-green-700' : ($row->status == 'i' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                        {{ $row->status == 'h' ? 'Hadir' : ($row->status == 'i' ? 'Izin' : 'Alpa') }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
