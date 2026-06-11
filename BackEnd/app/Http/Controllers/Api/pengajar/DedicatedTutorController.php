<?php

namespace App\Http\Controllers\Api\pengajar;

use App\Http\Controllers\Controller;
use App\Models\DedicatedTutor;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DedicatedTutorController extends Controller
{
    protected $max_quota = 3;

    /**
     * Ambil topik dari Microservice Materi
     */
    private function getTopicsFromMicroservice(int $classId): array
    {
        try {
            $goUrl = env('GO_MATERI_URL', 'http://localhost:9001');
            $response = Http::timeout(5)->get("$goUrl/api/materials", [
                'class_id' => $classId
            ]);

            if ($response->successful()) {
                $materials = $response->json()['data'] ?? [];
                $topics = [];
                foreach ($materials as $material) {
                    $topics[] = [
                        'material_id' => $material['material_id'],
                        'title' => $material['title'] ?? $material['subject_name'] ?? 'Materi'
                    ];
                }
                // Hapus duplikat berdasarkan title
                $unique = [];
                foreach ($topics as $topic) {
                    if (!in_array($topic['title'], array_column($unique, 'title'))) {
                        $unique[] = $topic;
                    }
                }
                return $unique;
            }
        } catch (\Exception $e) {
            Log::warning("Gagal mengambil topics dari microservice: " . $e->getMessage());
        }
        return [];
    }

    public function index()
    {
        try {
            $user = Auth::user();
            $student = Student::where('user_id', $user->usersID)->first();

            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Profil siswa tidak ditemukan.'
                ], 404);
            }

            // Ambil Riwayat (tanpa relasi material)
            $history = DedicatedTutor::where('student_id', $student->student_id)
                        ->latest()
                        ->get()
                        ->map(function($item) {
                            return [
                                'id' => $item->dedicated_tutor_id,
                                'date' => $item->date,
                                'time' => $item->time,
                                'status' => $item->status,
                                'material_id' => $item->material_id,
                            ];
                        });

            // Hitung Penggunaan Kuota Bulan Ini
            $used_this_month = DedicatedTutor::where('student_id', $student->student_id)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->whereYear('created_at', Carbon::now()->year)
                                ->count();

            // Ambil Daftar Topik dari Microservice
            $topics = [];
            if ($student->class_id) {
                $topics = $this->getTopicsFromMicroservice($student->class_id);
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

    public function store(Request $request)
    {
        try {
            $request->validate([
                'material_id' => 'required|integer',
                'date'        => 'required|date|after:today',
                'time'        => 'nullable|string'
            ], [
                'date.after' => 'Tanggal pengajuan minimal adalah untuk besok hari.'
            ]);

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
            $newRequest = DedicatedTutor::create([
                'student_id'  => $student->student_id,
                'material_id' => $request->material_id,
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
