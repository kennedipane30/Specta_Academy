@extends('layouts.spekta')
@section('title', 'Teacher Management')

@section('content')
<div class="space-y-8">

    {{-- CARD FORM ADD TEACHER --}}
    <div class="bg-white p-8 rounded-[2rem] shadow-sm border-t-8 border-[#990000]">
        <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tight mb-2">Add Teacher Account</h3>
        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-8">Register new teaching staff for Spekta Portal</p>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl text-xs font-bold">
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl text-xs font-bold animate-pulse">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.manajemen-pengajar.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 p-6 rounded-3xl border border-gray-100 shadow-inner">
            @csrf
            <div class="flex flex-col">
                <label class="text-[10px] font-black text-gray-400 mb-1 ml-1 uppercase">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Kennedi Pane" class="p-3.5 rounded-2xl border-none shadow-sm text-sm font-bold focus:ring-2 focus:ring-red-500 transition" required>
            </div>
            <div class="flex flex-col">
                <label class="text-[10px] font-black text-gray-400 mb-1 ml-1 uppercase">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="teacher@gmail.com" class="p-3.5 rounded-2xl border-none shadow-sm text-sm font-bold focus:ring-2 focus:ring-red-500 transition" required>
            </div>
            <div class="flex flex-col">
                <label class="text-[10px] font-black text-gray-400 mb-1 ml-1 uppercase">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="0812..." class="p-3.5 rounded-2xl border-none shadow-sm text-sm font-bold focus:ring-2 focus:ring-red-500 transition" required>
            </div>
            <div class="flex flex-col">
                <label class="text-[10px] font-black text-gray-400 mb-1 ml-1 uppercase">Password</label>
                <input type="password" name="password" placeholder="••••••••" class="p-3.5 rounded-2xl border-none shadow-sm text-sm font-bold focus:ring-2 focus:ring-red-500 transition" required>
            </div>

            <button type="submit" class="md:col-span-4 bg-[#990000] text-white font-black py-4 rounded-2xl uppercase text-[10px] tracking-[0.2em] shadow-lg shadow-red-100 hover:bg-red-800 transition transform active:scale-95 mt-2">
                Register Teacher Now
            </button>
        </form>
    </div>

    {{-- TEACHER LIST TABLE --}}
    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-[10px] font-black uppercase text-gray-400 tracking-widest">
                    <tr>
                        <th class="p-5 border-b">Teacher Name</th>
                        <th class="p-5 border-b">Email Address</th>
                        <th class="p-5 border-b">Phone</th>
                        <th class="p-5 border-b text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $t)
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                        <td class="p-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-red-100 text-[#990000] rounded-full flex items-center justify-center font-black text-xs">
                                    {{ substr($t->name, 0, 1) }}
                                </div>
                                <span class="font-black text-gray-800 uppercase text-sm tracking-tight">{{ $t->name }}</span>
                            </div>
                        </td>
                        <td class="p-5 font-medium text-gray-500 text-sm italic">{{ $t->email }}</td>
                        <td class="p-5 font-bold text-gray-700 text-sm">{{ $t->phone }}</td>
                        <td class="p-5 text-center">
                            {{-- Tetap usersID karena migrasi user tidak berubah --}}
                            <form action="{{ route('admin.manajemen-pengajar.destroy', $t->usersID) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-red-50 text-red-600 p-2.5 rounded-xl hover:bg-red-600 hover:text-white transition shadow-sm" onclick="return confirm('Delete this account?')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-20 text-center text-gray-300 font-bold uppercase text-[10px] tracking-widest italic">
                            No teacher accounts registered
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
