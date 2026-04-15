@extends('layouts.spekta')
@section('title', 'Manage Schedules')

@section('content')
<div class="bg-white p-8 rounded-2xl shadow-md border-t-8 border-[#990000]">
    <h3 class="text-xl font-black mb-6 text-spekta uppercase tracking-tight">Manage Learning Schedule</h3>

    <!-- Create Form -->
    <form action="{{ route('admin.jadwal.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10 bg-gray-50 p-6 rounded-2xl border border-gray-100">
        @csrf

        <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-400 mb-1 ml-1 uppercase">Select Program</label>
            <select name="class_id" id="class-select" class="border p-2.5 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 outline-none transition" required>
                <option value="">Select Program...</option>
                @foreach($classes as $c)
                    {{-- Menggunakan class_id dan program_name --}}
                    <option value="{{ $c->class_id }}">{{ $c->program_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-400 mb-1 ml-1 uppercase">Select Teacher</label>
            <select name="teacher_id" class="border p-2.5 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 outline-none transition" required>
                <option value="">Select Teacher...</option>
                @foreach($teachers as $t)
                    <option value="{{ $t->usersID }}">{{ $t->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-400 mb-1 ml-1 uppercase">Material Topic</label>
            <select name="title" id="materi-select" class="border p-2.5 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 outline-none transition bg-gray-100 cursor-not-allowed" required disabled>
                <option value="">Select Program first...</option>
            </select>
        </div>

        <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-400 mb-1 ml-1 uppercase">Date</label>
            <input type="date" name="date" class="border p-2.5 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 outline-none transition" required>
        </div>

        <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-400 mb-1 ml-1 uppercase">Start Time</label>
            <input type="time" name="start_time" class="border p-2.5 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 outline-none transition" required>
        </div>

        <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-400 mb-1 ml-1 uppercase">End Time</label>
            <input type="time" name="end_time" class="border p-2.5 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 outline-none transition" required>
        </div>

        <div class="md:col-span-3 mt-4">
            <button type="submit" class="w-full bg-[#990000] text-white font-black py-3 rounded-2xl shadow-lg shadow-red-100 hover:bg-red-800 transition active:scale-95 uppercase tracking-widest text-xs">
                Publish Learning Schedule
            </button>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100 text-[10px] font-black uppercase text-gray-500 tracking-widest">
                <tr>
                    <th class="p-4 border-b">Time & Date</th>
                    <th class="p-4 border-b">Program</th>
                    <th class="p-4 border-b">Topic & Teacher</th>
                    <th class="p-4 border-b">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jadwal as $row)
                <tr class="border-b text-sm hover:bg-gray-50 transition">
                    <td class="p-4">
                        <span class="font-black text-gray-800">{{ date('d M Y', strtotime($row->date)) }}</span><br>
                        <span class="text-xs text-gray-400 font-bold uppercase">{{ $row->start_time }} - {{ $row->end_time }} WIB</span>
                    </td>
                    {{-- class relation & program_name --}}
                    <td class="p-4 font-black text-red-700 uppercase tracking-tighter">{{ $row->class->program_name }}</td>
                    <td class="p-4">
                        <span class="font-black text-gray-800 uppercase">{{ $row->title }}</span><br>
                        <span class="text-[10px] font-bold text-gray-400 uppercase">By: {{ $row->teacher->name }}</span>
                    </td>
                    <td class="p-4">
                        <div class="flex items-center gap-4">
                            {{-- schedule_id --}}
                            <a href="{{ route('admin.jadwal.edit', $row->schedule_id) }}" class="text-blue-600 font-black text-[10px] uppercase hover:underline">Edit</a>
                            <form action="{{ route('admin.jadwal.destroy', $row->schedule_id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="text-red-600 font-black text-[10px] uppercase hover:underline" onclick="return confirm('Delete this schedule?')">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('class-select').addEventListener('change', function() {
        let classID = this.value;
        let materiSelect = document.getElementById('materi-select');

        if (classID) {
            materiSelect.innerHTML = '<option value="">Loading Materials...</option>';
            materiSelect.disabled = false;
            materiSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');

            fetch(`/admin/get-materi/${classID}`)
                .then(response => response.json())
                .then(data => {
                    materiSelect.innerHTML = '<option value="">Select Topic...</option>';
                    if (data.length > 0) {
                        data.forEach(item => {
                            let option = document.createElement('option');
                            option.value = item.title;
                            option.text = item.title;
                            materiSelect.appendChild(option);
                        });
                    } else {
                        materiSelect.innerHTML = '<option value="">No materials in this class</option>';
                    }
                })
                .catch(error => {
                    materiSelect.innerHTML = '<option value="">Failed to load</option>';
                });
        } else {
            materiSelect.innerHTML = '<option value="">Select Program first...</option>';
            materiSelect.disabled = true;
            materiSelect.classList.add('bg-gray-100', 'cursor-not-allowed');
        }
    });
</script>
@endsection
