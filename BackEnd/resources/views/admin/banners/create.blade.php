@extends('layouts.spekta')

@section('title', 'Tambah Banner')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-black text-gray-800">Tambah Banner</h1>
        <p class="text-sm text-gray-500 mt-1">Upload banner untuk carousel homepage.</p>
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

    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
        @csrf

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Title</label>
            <input type="text" name="title"
                   value="{{ old('title') }}"
                   class="w-full mt-2 px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-200">
        </div>

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Description</label>
            <textarea name="description"
                      class="w-full mt-2 px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-200">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Image</label>
            <input type="file" name="image" required
                   class="w-full mt-2 text-sm">
        </div>

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Link</label>
            <input type="text" name="link"
                   value="{{ old('link') }}"
                   class="w-full mt-2 px-4 py-3 rounded-xl border border-gray-200">
        </div>

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Order</label>
            <input type="number" name="order_position"
                   value="{{ old('order_position', 0) }}"
                   class="w-full mt-2 px-4 py-3 rounded-xl border border-gray-200">
        </div>

        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4">
            <label class="text-sm font-semibold text-gray-600">Aktifkan Banner</label>
        </div>

        <div class="flex justify-between items-center pt-4">
            <a href="{{ route('admin.banners.index') }}"
               class="text-sm font-bold text-gray-500 hover:text-gray-800">
                ← Kembali
            </a>

            <button type="submit"
                    class="bg-[#990000] text-white px-6 py-3 rounded-2xl text-sm font-black shadow-lg shadow-red-200 hover:bg-red-800">
                Simpan Banner
            </button>
        </div>
    </form>
</div>
@endsection