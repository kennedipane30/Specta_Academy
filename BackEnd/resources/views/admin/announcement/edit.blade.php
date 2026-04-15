@extends('layouts.spekta')
@section('title', 'Edit Announcement')

@section('content')
<div class="p-6 min-h-screen flex items-center justify-center">
    <div class="bg-white p-12 rounded-[3.5rem] shadow-2xl shadow-gray-200 border border-gray-50 w-full max-w-3xl animate__animated animate__zoomIn">
        <div class="flex justify-between items-center mb-12">
            <h3 class="text-3xl font-black text-gray-800 uppercase tracking-tighter">Edit <span class="text-[#990000]">Post</span></h3>
            <a href="{{ route('admin.announcement.index') }}" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-[#990000] transition">&larr; Back to List</a>
        </div>

        <form action="{{ route('admin.announcement.update', $announcement->announcement_id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf @method('PUT')

            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Headline Title</label>
                <input type="text" name="title" value="{{ $announcement->title }}" class="w-full p-5 rounded-2xl bg-gray-50 border-none shadow-inner font-bold text-gray-700" required>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Content Details</label>
                <textarea name="description" rows="6" class="w-full p-6 rounded-3xl bg-gray-50 border-none shadow-inner font-medium text-gray-600" required>{{ $announcement->description }}</textarea>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Visual Media</label>
                <div class="flex items-center gap-8 p-4 bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                    <img src="{{ asset('storage/' . $announcement->image) }}" class="w-28 h-28 object-cover rounded-2xl shadow-lg">
                    <div class="flex-1">
                        <p class="text-[10px] text-gray-400 mb-2 italic">Select a new file if you want to replace the current image.</p>
                        <input type="file" name="image" class="text-[10px] font-bold">
                    </div>
                </div>
            </div>

            <button class="w-full bg-gray-900 text-white font-black py-5 rounded-2xl shadow-xl hover:bg-[#990000] transition duration-500 uppercase text-xs tracking-widest">
                Update Announcement Data
            </button>
        </form>
    </div>
</div>
@endsection
