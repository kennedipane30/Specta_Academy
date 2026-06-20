<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;

class BannerController extends Controller
{
    public function index(): JsonResponse
    {
        $banners = Banner::where('is_active', true)
            ->orderBy('order_position')
            ->latest()
            ->get()
            ->map(function ($banner) {
                return [
                    'id'             => $banner->id,
                    'order_position' => $banner->order_position,
                    'title'          => $banner->title,
                    'description'    => $banner->description,

                    // 🔥 PERBAIKAN: Hapus asset(), panggil langsung image_url dinamisnya!
                    'image_url'      => $banner->image_url,

                    'link'           => $banner->link,
                    'order_position' => $banner->order_position,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Banner list',
            'data'    => $banners,
        ]);
    }
}
