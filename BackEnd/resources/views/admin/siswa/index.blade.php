@extends('layouts.spekta')
@section('title', 'Student Management')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-md">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 text-xs font-bold uppercase">
            <tr>
                <th class="p-4 border-b">Student Name</th>
                <th class="p-4 border-b">Email</th>
                <th class="p-4 border-b">National ID (NISN)</th>
                <th class="p-4 border-b">Account Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $s)
            <tr class="text-sm">
                <td class="p-4 border-b">{{ $s->name }}</td>
                <td class="p-4 border-b">{{ $s->email }}</td>
                <td class="p-4 border-b">{{ $s->student->national_id_number ?? '-' }}</td>
                <td class="p-4 border-b">
                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-[10px] font-bold">REGISTERED</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
