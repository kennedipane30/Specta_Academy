@extends('layouts.spekta')
@section('title', 'Management - Announcement')

@section('content')
<div class="p-6 space-y-10 animate__animated animate__fadeIn">

    {{-- FORM SECTION --}}
    <div class="bg-white p-10 rounded-[3rem] shadow-xl shadow-gray-100 border border-gray-50">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-1.5 h-8 bg-[#990000] rounded-full"></div>
            <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Create New Announcement</h3>
        </div>

        @if(session('success'))
            <div class="bg-green-50 text-green-600 p-4 rounded-2xl mb-6 font-bold text-sm border border-green-100 flex items-center gap-3">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.announcement.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Announcement Title</label>
                    <input type="text" name="title" placeholder="Enter headline..." class="w-full p-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-[#990000] transition font-bold" required>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Cover Image</label>
                    <div class="bg-gray-50 p-3 rounded-2xl border-2 border-dashed border-gray-200">
                        <input type="file" name="image" class="text-xs font-bold text-gray-500" required>
                    </div>
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Full Description</label>
                <textarea name="description" rows="4" placeholder="Write the announcement details here..." class="w-full p-6 rounded-3xl bg-gray-50 border-none focus:ring-2 focus:ring-[#990000] transition font-medium" required></textarea>
            </div>

            <button type="submit" class="w-full bg-[#990000] text-white py-5 rounded-2xl font-black uppercase text-xs tracking-[0.2em] shadow-lg shadow-red-100 hover:bg-red-800 transition transform active:scale-[0.98]">
                Publish Announcement Now
            </button>
        </form>
    </div>

    {{-- LIST SECTION --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach($announcements as $row)
        <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-sm border border-gray-100 flex flex-col group hover:shadow-2xl transition-all duration-500">
            <div class="relative h-56 overflow-hidden">
                <img src="{{ asset('storage/' . $row->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            </div>
            <div class="p-8 flex-1">
                <h4 class="font-black text-gray-800 uppercase text-sm mb-3 tracking-tight">{{ $row->title }}</h4>
                <p class="text-gray-400 text-xs leading-relaxed line-clamp-3 mb-8 font-medium">{{ $row->description }}</p>

                <div class="flex gap-3 pt-6 border-t border-gray-50">
                    <a href="{{ route('admin.announcement.edit', $row->announcement_id) }}" class="flex-1 text-center bg-gray-50 text-gray-600 py-3 rounded-xl text-[10px] font-black uppercase hover:bg-blue-600 hover:text-white transition shadow-sm">Edit</a>

                    <form action="{{ route('admin.announcement.destroy', $row->announcement_id) }}" method="POST" class="flex-1">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full bg-red-50 text-red-600 py-3 rounded-xl text-[10px] font-black uppercase hover:bg-red-600 hover:text-white transition shadow-sm" onclick="return confirm('Permanently delete this announcement?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
