@extends('layouts.spekta')
@section('title', 'Waiting List - Enrollment')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-[#990000]">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 text-xs font-bold uppercase">
                <th class="p-4 border-b">Student Name</th>
                <th class="p-4 border-b">Email</th>
                <th class="p-4 border-b">Selected Program</th>
                <th class="p-4 border-b">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr class="hover:bg-gray-50 text-sm">
                <td class="p-4 border-b font-bold">{{ $row->user->name }}</td>
                <td class="p-4 border-b">{{ $row->user->email }}</td>
                {{-- program_name & relasi class --}}
                <td class="p-4 border-b text-red-700 font-bold uppercase">{{ $row->class->program_name }}</td>
                <td class="p-4 border-b">
                    {{-- enrollment_id --}}
                    <a href="{{ route('admin.siswa.form_aktivasi', $row->enrollment_id) }}"
                       class="bg-[#990000] text-white px-4 py-2 rounded-lg text-xs font-bold shadow-md hover:bg-red-800 transition">
                       ADD STUDENT
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="p-10 text-center text-gray-400 italic">No students waiting for enrollment.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
