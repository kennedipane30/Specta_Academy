@extends('layouts.spekta')
@section('title', 'Scores - ' . $class->program_name)

@section('content')
<div class="p-6 animate__animated animate__fadeIn">

    @php
        $isAdmin = Auth::user()->role_id == 1;
        $actionRoute = $isAdmin ? route('admin.scores.pdf_selected') : route('pengajar.tryout.nilai.pdf_selected');
        $backRoute = $isAdmin ? route('admin.scores.index') : route('pengajar.tryout.nilai');
    @endphp

    {{-- BUNGKUS DENGAN FORM UNTUK MENGIRIM DATA CHECKBOX --}}
    <form action="{{ $actionRoute }}" method="POST">
        @csrf
        <input type="hidden" name="class_id" value="{{ $class->class_id }}">

        {{-- HEADER ACTION --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-800 uppercase tracking-tight">{{ $class->program_name }}</h1>
                <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Select results to export into PDF report</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ $backRoute }}" class="bg-gray-100 text-gray-500 px-6 py-3 rounded-2xl text-[10px] font-black uppercase hover:bg-gray-200 transition">Back</a>
                <button type="submit" class="bg-[#990000] text-white px-8 py-3 rounded-2xl text-[10px] font-black uppercase shadow-lg shadow-red-100 flex items-center gap-2 hover:bg-red-800 transition">
                    <i class="fas fa-file-pdf"></i> EXPORT SELECTED TO PDF
                </button>
            </div>
        </div>

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-6 font-bold text-xs">{{ session('error') }}</div>
        @endif

        @forelse($tryouts as $tryout)
        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-100 border border-gray-100 overflow-hidden mb-12">
            <div class="bg-gray-900 p-8 flex justify-between items-center">
                <div>
                    <h3 class="text-white font-black uppercase tracking-tight text-lg">{{ $tryout->title }}</h3>
                    <span class="text-red-500 text-[10px] font-black uppercase tracking-widest">Simulation Exam Group</span>
                </div>
                {{-- Tombol Pilih Semua khusus tryout ini --}}
                <button type="button" onclick="selectAllInGroup('{{ $tryout->tryout_id }}')" class="text-[9px] text-gray-400 font-bold uppercase hover:text-white transition">Select All in this Tryout</button>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-gray-400 uppercase border-b border-gray-50">
                                <th class="px-4 py-4 w-10">Pick</th>
                                <th class="px-4 py-4">Student Name</th>
                                <th class="px-4 py-4 text-center">Correct</th>
                                <th class="px-4 py-4 text-center">Final Score</th>
                                <th class="px-4 py-4 text-right">Completion Date</th>
                            </tr>
                        </thead>
                        <tbody id="group-{{ $tryout->tryout_id }}">
                            @forelse($tryout->results as $result)
                            <tr class="border-b border-gray-50 hover:bg-red-50/30 transition">
                                <td class="px-4 py-5">
                                    <input type="checkbox" name="selected_results[]" value="{{ $result->tryout_result_id }}" class="w-5 h-5 rounded border-gray-300 text-[#990000] focus:ring-[#990000]">
                                </td>
                                <td class="px-4 py-5">
                                    <p class="font-black text-gray-800 uppercase text-xs">{{ $result->user->name }}</p>
                                    <small class="text-gray-400 font-bold">NISN: {{ $result->user->student->national_id_number ?? '-' }}</small>
                                </td>
                                <td class="px-4 py-5 text-center text-sm font-bold text-gray-500">
                                    {{ $result->total_correct }} / {{ $tryout->questions->count() }}
                                </td>
                                <td class="px-4 py-5 text-center">
                                    <div class="inline-block bg-white border-2 {{ $result->score >= 70 ? 'border-green-500 text-green-600' : 'border-[#990000] text-[#990000]' }} px-4 py-1 rounded-xl font-black text-sm">
                                        {{ $result->score }}
                                    </div>
                                </td>
                                <td class="px-4 py-5 text-right text-[10px] font-black text-gray-400">
                                    {{ $result->created_at->format('d M Y') }}<br>
                                    <span class="text-gray-300">{{ $result->created_at->format('H:i') }} WIB</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-10 text-gray-300 italic text-xs font-bold uppercase">No one has taken this tryout yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-20 bg-white rounded-[3rem] text-gray-400 italic font-bold uppercase text-xs border-4 border-dashed border-gray-50">No tryouts found in this class.</div>
        @endforelse
    </form>
</div>

<script>
    // Fungsi untuk mencentang semua checkbox dalam satu grup tryout
    function selectAllInGroup(groupId) {
        const checkboxes = document.querySelectorAll(`#group-${groupId} input[type="checkbox"]`);
        checkboxes.forEach(cb => cb.checked = !cb.checked);
    }
</script>
@endsection
