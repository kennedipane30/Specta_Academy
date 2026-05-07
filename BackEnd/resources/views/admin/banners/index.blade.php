@extends('layouts.spekta')

@section('title', 'Banner Management')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-2xl font-bold text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black text-gray-800">Banner Management</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola banner carousel untuk homepage mobile.</p>
        </div>

        <a href="{{ route('admin.banners.create') }}"
           class="bg-[#990000] text-white px-5 py-3 rounded-2xl text-xs font-black shadow-lg shadow-red-200 hover:bg-red-800 transition">
            + Tambah Banner
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-[11px] font-black text-gray-500 uppercase">Gambar</th>
                    <th class="px-6 py-4 text-left text-[11px] font-black text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-4 text-left text-[11px] font-black text-gray-500 uppercase">Link</th>
                    <th class="px-6 py-4 text-left text-[11px] font-black text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-4 text-left text-[11px] font-black text-gray-500 uppercase">Urutan</th>
                    <th class="px-6 py-4 text-right text-[11px] font-black text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($banners as $banner)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <img src="{{ asset($banner->image_url) }}"
                                 class="w-36 h-20 object-cover rounded-2xl border border-gray-100">
                        </td>

                        <td class="px-6 py-4">
                            <p class="font-black text-gray-800">{{ $banner->title ?? '-' }}</p>
                            <p class="text-xs text-gray-400 line-clamp-1">{{ $banner->description ?? '-' }}</p>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $banner->link ?? '-' }}
                        </td>

                        <td class="px-6 py-4">
                            @if($banner->is_active)
                                <span class="bg-green-50 text-green-700 px-3 py-1 rounded-full text-[10px] font-black uppercase">
                                    Aktif
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-[10px] font-black uppercase">
                                    Nonaktif
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 font-bold text-gray-600">
                            {{ $banner->order_position }}
                        </td>

                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.banners.edit', $banner) }}"
                                   class="px-4 py-2 rounded-xl bg-yellow-50 text-yellow-700 text-xs font-black hover:bg-yellow-100">
                                    Edit
                                </a>

                                <form action="{{ route('admin.banners.destroy', $banner) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus banner ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="px-4 py-2 rounded-xl bg-red-50 text-red-700 text-xs font-black hover:bg-red-100">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-14 text-center">
                            <p class="text-gray-400 font-bold">Belum ada banner.</p>
                            <a href="{{ route('admin.banners.create') }}"
                               class="inline-block mt-4 bg-[#990000] text-white px-5 py-3 rounded-2xl text-xs font-black">
                                Tambah Banner Pertama
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $banners->links() }}
    </div>
</div>
@endsection