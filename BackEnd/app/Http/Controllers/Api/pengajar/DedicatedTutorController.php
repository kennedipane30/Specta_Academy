<?php

namespace App\Http\Controllers\Api\pengajar;

use App\Http\Controllers\Controller;
use App\Models\DedicatedTutor;
use App\Models\Material;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;

class DedicatedTutorController extends Controller 
{
    // Batas kuota per bulan
    protected $max_quota = 3;

    /**
     * 1. GET DATA UNTUK DASHBOARD DEDICATED TUTOR
     */
    public function index() 
    {
        try {
            $user = Auth::user();
            $student = Student::where('user_id', $user->usersID)->first();

            if (!$student) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Profil siswa tidak ditemukan atau belum terdaftar di kelas.'
                ], 404);
            }

            // Ambil Riwayat (Relasi antar database berjalan lancar di Eloquent)
            $history = DedicatedTutor::with('material:material_id,title')
                        ->where('student_id', $student->student_id)
                        ->latest()
                        ->get();

            // Hitung Penggunaan Kuota Bulan Ini
            $used_this_month = DedicatedTutor::where('student_id', $student->student_id)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->whereYear('created_at', Carbon::now()->year)
                                ->count();

            // Ambil Daftar Topik dari Database Materi (pgsql_materi)
            $topics = [];
            if ($student->class_id) {
                $topics = Material::where('class_id', $student->class_id)
                            ->select('material_id', 'title')
                            ->get();
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'student_name' => $user->name,
                    'quota' => [
                        'max' => $this->max_quota,
                        'used' => $used_this_month,
                        'remaining' => max(0, $this->max_quota - $used_this_month)
                    ],
                    'topics' => $topics, 
                    'history' => $history 
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 2. SIMPAN PENGAJUAN TUTOR BARU
     */
    public function store(Request $request) 
    {
        try {
            $materialModel = new Material();
            $connectionName = $materialModel->getConnectionName(); // 'pgsql_materi'
            $tableName = $materialModel->getTable();           // 'materials'
            $primaryKey = $materialModel->getKeyName();         // 'material_id'

            // Validasi manual ke database materi sebelum insert
            $validator = Validator::make($request->all(), [
                'material_id' => "required|exists:{$connectionName}.{$tableName},{$primaryKey}",
                'date'        => 'required|date|after:today',
                'time'        => 'nullable|string'
            ], [
                'material_id.exists' => "Materi tidak ditemukan di database materi kami.",
                'date.after'         => 'Tanggal pengajuan minimal adalah untuk besok hari.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $user = Auth::user();
            $student = Student::where('user_id', $user->usersID)->first();

            if (!$student) {
                return response()->json(['status' => 'error', 'message' => 'Siswa tidak ditemukan.'], 404);
            }

            // Cek Kuota
            $used_this_month = DedicatedTutor::where('student_id', $student->student_id)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->whereYear('created_at', Carbon::now()->year)
                                ->count();

            if ($used_this_month >= $this->max_quota) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Maaf, kuota Dedicated Tutor Anda bulan ini sudah habis (Maks 3x).'
                ], 403);
            }

            // SIMPAN DATA
            // Karena Foreign Key di DB sudah dihapus (Langkah 1), ini tidak akan error lagi.
            $newRequest = DedicatedTutor::create([
                'student_id'  => $student->student_id,
                'material_id' => $request->material_id, // Disimpan sebagai ID (Logic Link)
                'date'        => $request->date,
                'time'        => $request->time ?? '16:00:00',
                'status'      => 'pending'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil! Permintaan tutor telah dikirim.',
                'data' => $newRequest
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }
}