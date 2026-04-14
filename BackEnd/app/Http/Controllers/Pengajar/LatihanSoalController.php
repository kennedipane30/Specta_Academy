<?php
namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\LatihanSoal;
use Illuminate\Http\Request;

class LatihanSoalController extends Controller {
    public function index()
    {
        // Mengambil semua kelas
        $classes = \App\Models\ClassModel::all();

        // Memanggil view yang baru saja kita buat
        return view('pengajar.latihan.index', compact('classes'));
    }

    public function pilihLatihan($class_id) {
        $class = ClassModel::findOrFail($class_id);
        $latihans = LatihanSoal::where('class_id', $class_id)->get();
        return view('pengajar.latihan.pilih', compact('class', 'latihans'));
    }

    public function storeCSV(Request $request, $class_id) {
    $request->validate([
        'subject'  => 'required',
        'minggu'   => 'required|integer|min:1|max:20',
        'file_csv' => 'required'
    ]);

    $file = $request->file('file_csv');
    $handle = fopen($file->getRealPath(), "r");

    // Ambil header baris pertama
    fgetcsv($handle, 2000, ";");

    while (($row = fgetcsv($handle, 2000, ";")) !== FALSE) {
        // Pengecekan: Jika kolom pertanyaan (row 0) kosong, lewati baris ini
        if (!isset($row[0]) || empty(trim($row[0]))) {
            continue;
        }

        \App\Models\LatihanSoal::create([
            'class_id'      => $class_id,
            'subject'       => $request->subject,
            'minggu'        => $request->minggu,
            'pertanyaan'    => $row[0],
            'opsi_a'        => $row[1] ?? '-',
            'opsi_b'        => $row[2] ?? '-',
            'opsi_c'        => $row[3] ?? '-',
            'opsi_d'        => $row[4] ?? '-',
            'jawaban_benar' => strtoupper(trim($row[5] ?? 'A')),
            'pembahasan'    => $row[6] ?? null,
        ]);
    }

    fclose($handle);
    return back()->with('success', 'Latihan Soal berhasil di-import!');
}
}
