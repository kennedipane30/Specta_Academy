@extends('layouts.spekta')
@section('title', 'Schedule Management')

@section('content')
<div class="p-6 space-y-10">
    <div class="bg-white p-8 rounded-[2rem] shadow-sm border-t-8 border-[#990000]">
        <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tight mb-6 text-spekta">Manage Learning Schedule</h3>

        <!-- FORM TAMBAH JADWAL -->
        {{-- PASTIKAN ACTION ADALAH admin.jadwal.store --}}
        <form action="{{ route('admin.jadwal.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 bg-gray-50 p-8 rounded-3xl border border-gray-100">
            @csrf

            <div class="flex flex-col">
                <label class="text-[10px] font-black text-gray-400 mb-1 ml-1 uppercase">Select Program</label>
                <select name="class_id" id="class-select" class="p-3.5 rounded-2xl border-none shadow-sm text-sm font-bold focus:ring-2 focus:ring-red-500 transition" required>
                    <option value="">Choose Program...</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->class_id }}">{{ $c->program_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-[10px] font-black text-gray-400 mb-1 ml-1 uppercase">Select Teacher</label>
                <select name="teacher_id" class="p-3.5 rounded-2xl border-none shadow-sm text-sm font-bold focus:ring-2 focus:ring-red-500 transition" required>
                    <option value="">Choose Teacher...</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->usersID }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-[10px] font-black text-gray-400 mb-1 ml-1 uppercase">Material Topic</label>
                <select name="title" id="materi-select" class="p-3.5 rounded-2xl border-none shadow-sm text-sm font-bold focus:ring-2 focus:ring-red-500 transition bg-white" required disabled>
                    <option value="">Select Program first...</option>
                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-[10px] font-black text-gray-400 mb-1 ml-1 uppercase">Date</label>
                <input type="date" name="date" class="p-3.5 rounded-2xl border-none shadow-sm text-sm font-bold focus:ring-2 focus:ring-red-500 transition" required>
            </div>

            <div class="flex flex-col">
                <label class="text-[10px] font-black text-gray-400 mb-1 ml-1 uppercase">Start Time</label>
                <input type="time" name="start_time" class="p-3.5 rounded-2xl border-none shadow-sm text-sm font-bold focus:ring-2 focus:ring-red-500 transition" required>
            </div>

            <div class="flex flex-col">
                <label class="text-[10px] font-black text-gray-400 mb-1 ml-1 uppercase">End Time</label>
                <input type="time" name="end_time" class="p-3.5 rounded-2xl border-none shadow-sm text-sm font-bold focus:ring-2 focus:ring-red-500 transition" required>
            </div>

            <button type="submit" class="md:col-span-3 bg-[#990000] text-white font-black py-4 rounded-2xl shadow-lg uppercase text-xs tracking-widest hover:bg-red-800 transition transform active:scale-95">
                Publish Learning Schedule
            </button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100 text-[10px] font-black uppercase text-gray-500 tracking-widest">
                    <tr>
                        <th class="p-4 border-b">Time & Date</th>
                        <th class="p-4 border-b">Program</th>
                        <th class="p-4 border-b">Subject & Teacher</th>
                        <th class="p-4 border-b">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwal as $row)
                    <tr class="border-b text-sm hover:bg-gray-50 transition">
                        <td class="p-4"><span class="font-black text-gray-800">{{ date('d M Y', strtotime($row->date)) }}</span><br><span class="text-xs text-gray-400 font-bold">{{ $row->start_time }} - {{ $row->end_time }} WIB</span></td>
                        <td class="p-4 font-black text-red-700 uppercase tracking-tighter">{{ $row->class->program_name }}</td>
                        <td class="p-4"><span class="font-black text-gray-800 uppercase">{{ $row->title }}</span><br><span class="text-[10px] font-bold text-gray-400 uppercase">By: {{ $row->teacher->name }}</span></td>
                        <td class="p-4">
                            <form action="{{ route('admin.jadwal.destroy', $row->schedule_id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="text-red-600 font-black text-[10px] uppercase hover:underline" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('class-select').addEventListener('change', function() {
        let classID = this.value;
        let materiSelect = document.getElementById('materi-select');

        if (classID) {
            materiSelect.disabled = false;
            materiSelect.innerHTML = '<option value="">Loading...</option>';

            fetch(`/admin/get-materi/${classID}`)
                .then(response => response.json())
                .then(data => {
                    materiSelect.innerHTML = '<option value="">Select Material...</option>';
                    data.forEach(item => {
                        let option = document.createElement('option');
                        option.value = item.title;
                        option.text = item.title;
                        materiSelect.appendChild(option);
                    });
                });
        }
    });
</script>
@endsection
