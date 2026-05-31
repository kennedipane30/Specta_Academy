<?php

namespace App\Http\Controllers\Api\pengajar;

use App\Http\Controllers\Controller;

class DedicatedTutorController extends Controller
{
    public function index()
    {
        return response()->json(['status' => 'ok']);
    }
}