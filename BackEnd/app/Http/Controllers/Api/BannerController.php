<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::where('is_active', true)
            ->orderBy('order_position')
            ->latest()
            ->get()
            ->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'description' => $banner->description,
                    'image_url' => asset($banner->image_url),
                    'link' => $banner->link,
                    'order_position' => $banner->order_position,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Banner list',
            'data' => $banners,
        ]);
    }
}