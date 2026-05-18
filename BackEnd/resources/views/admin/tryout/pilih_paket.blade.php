@extends('layouts.spekta')
@section('content')
<div class="p-6">
    <div class="mb-8">
        <a href="{{ route('admin.scores.index') }}" class="text-xs font-bold text-gray-400 hover:text-red-600 uppercase">← Kembali ke Kelas</a>
        <h1 class="text-2xl font-black text-gray-800 uppercase mt-2">Daftar Tryout: {{ $class->program_name }}</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($tryouts as $t)
        <a href="{{ route('admin.scores.result', $t->tryout_id) }}" class="bg-white p-6 rounded-[30px] border border-gray-100 shadow-sm hover:shadow-md transition">
            <h4 class="font-black text-gray-800 uppercase">{{ $t->title }}</h4>
            <p class="text-xs text-gray-400 font-bold mt-1 uppercase">{{ $t->duration }} Menit</p>
        </a>
        @empty
        <p class="text-gray-400 italic">Belum ada paket tryout di kelas ini.</p>
        @endforelse
    </div>
</div>
@endsection
