@extends('layouts.spekta')

@section('title', 'Edit Banner')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-black text-gray-800">Edit Banner</h1>
        <p class="text-sm text-gray-500 mt-1">Perbarui data banner.</p>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl text-sm">
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.banners.update', $banner) }}"
          method="POST"
          enctype="multipart/form-data"
          class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Title</label>
            <input type="text" name="title"
                   value="{{ old('title', $banner->title) }}"
                   class="w-full mt-2 px-4 py-3 rounded-xl border border-gray-200">
        </div>

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Description</label>
            <textarea name="description"
                      class="w-full mt-2 px-4 py-3 rounded-xl border border-gray-200">{{ old('description', $banner->description) }}</textarea>
        </div>

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Current Image</label>
            <img src="{{ asset($banner->image_url) }}"
                 class="mt-2 w-full h-40 object-cover rounded-2xl border">
        </div>

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Ganti Image</label>
            <input type="file" name="image" class="w-full mt-2 text-sm">
        </div>

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Link</label>
            <input type="text" name="link"
                   value="{{ old('link', $banner->link) }}"
                   class="w-full mt-2 px-4 py-3 rounded-xl border border-gray-200">
        </div>

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Order</label>
            <input type="number" name="order_position"
                   value="{{ old('order_position', $banner->order_position) }}"
                   class="w-full mt-2 px-4 py-3 rounded-xl border border-gray-200">
        </div>

        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_active" value="1"
                   {{ $banner->is_active ? 'checked' : '' }}>
            <label class="text-sm font-semibold text-gray-600">Aktifkan Banner</label>
        </div>

        <div class="flex justify-between items-center pt-4">
            <a href="{{ route('admin.banners.index') }}"
               class="text-sm font-bold text-gray-500 hover:text-gray-800">
                ← Kembali
            </a>

            <button type="submit"
                    class="bg-[#990000] text-white px-6 py-3 rounded-2xl text-sm font-black shadow-lg shadow-red-200 hover:bg-red-800">
                Update Banner
            </button>
        </div>
    </form>
</div>
@endsection