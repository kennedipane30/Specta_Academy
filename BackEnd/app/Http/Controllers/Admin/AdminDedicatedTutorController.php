<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DedicatedTutor;
use Illuminate\Http\Request;

class AdminDedicatedTutorController extends Controller {
    public function index() {
        $tutors = DedicatedTutor::with(['student.user', 'teacher', 'material'])->latest()->get();
        return view('admin.dedicated_tutor.index', compact('tutors'));
    }

    public function updateStatus(Request $request, $id) {
        DedicatedTutor::where('dedicated_tutorsID', $id)->update(['status' => $request->status]);
        return back()->with('success', 'Status updated');
    }
}
