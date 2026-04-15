<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->get();
        return view('admin.announcement.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'image' => 'required|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        // Simpan ke storage/app/public/announcements
        $path = $request->file('image')->store('announcements', 'public');

        Announcement::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $path
        ]);

        return redirect()->route('admin.announcement.index')->with('success', 'Announcement published successfully!');
    }

    public function edit($id)
    {
        // Mencari berdasarkan announcement_id
        $announcement = Announcement::findOrFail($id);
        return view('admin.announcement.edit', compact('announcement'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = Announcement::findOrFail($id);

        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($data->image) {
                Storage::disk('public')->delete($data->image);
            }
            $data->image = $request->file('image')->store('announcements', 'public');
        }

        $data->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.announcement.index')->with('success', 'Announcement updated successfully!');
    }

    public function destroy($id)
    {
        $data = Announcement::findOrFail($id);

        if ($data->image) {
            Storage::disk('public')->delete($data->image);
        }

        $data->delete();
        return redirect()->route('admin.announcement.index')->with('success', 'Announcement deleted successfully!');
    }
}
