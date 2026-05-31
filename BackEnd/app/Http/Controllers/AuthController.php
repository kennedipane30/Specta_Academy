<?php

namespace App\Http\Controllers;

// IMPORT SEMUA MODEL & LIBRARY YANG DIBUTUHKAN
use App\Models\{User, Student, OtpCode, Enrollment, Material, Schedule, Tryout, Question, TryoutResult, PracticeQuestion, ClassModel};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Hash, Validator, DB, Auth, Mail, Log, Http}; 
use App\Mail\OtpMail;
use Carbon\Carbon;

class AuthController extends Controller {

    /**
     * 1. REGISTER SISWA (OTP + Profil Awal)
     */
    public function registerSiswa(Request $request): JsonResponse {
        $v = Validator::make($request->all(), [
            'name' => 'required|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|unique:users',
            'nomor_wa' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        DB::beginTransaction();
        try {
            // Simpan User
            $user = User::create([
                'name' => trim($request->name), 
                'email' => trim($request->email), 
                'phone' => $request->nomor_wa, 
                'password' => bcrypt($request->password), 
                'role_id' => 3, 
                'is_verified' => false
            ]);

            // Simpan Profil Siswa Kosong
            Student::create([
                'user_id' => $user->usersID, 
                'address' => '-', 
                'date_of_birth' => null, 
                'parent_phone' => '-', 
                'parent_name' => '-'
            ]);

            // Generate & Kirim OTP
            $otp = rand(100000, 999999);
            OtpCode::updateOrCreate(['user_id' => $user->usersID], [
                'otp' => $otp, 
                'valid_until' => Carbon::now()->addMinutes(10)
            ]);

            Mail::to($user->email)->send(new OtpMail($otp));
            DB::commit();

            return response()->json(['status' => 'success', 'name' => $user->name], 201);
        } catch (\Exception $e) { 
            DB::rollBack(); 
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500); 
        }
    }

    /**
     * 2. VERIFIKASI REGISTRASI (OTP)
     */
    public function verifyRegistration(Request $request): JsonResponse {
        $user = User::where('name', trim($request->name))->where('is_verified', false)->latest()->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);

        $otpRecord = OtpCode::where('user_id', $user->usersID)
            ->where('otp', $request->otp)
            ->where('valid_until', '>', now())
            ->first();

        if (!$otpRecord) return response()->json(['status' => 'error', 'message' => 'OTP Salah atau Kadaluarsa'], 401);

        $user->is_verified = true; 
        $user->save(); 
        $otpRecord->delete();

        return response()->json(['status' => 'success', 'message' => 'Akun Berhasil Aktif!']);
    }

    /**
     * 3. LOGIN SISTEM
     */
    public function login(Request $request): JsonResponse {
        $user = User::where('name', trim($request->name))->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Nama atau Password Salah'], 401);
        }

        if ($user->role_id == 3 && !$user->is_verified) {
            return response()->json(['status' => 'error', 'message' => 'Akun belum diverifikasi!'], 403);
        }

        return response()->json([
            'status' => 'success', 
            'token' => $user->createToken('token')->plainTextToken, 
            'user' => $user->load(['student.class'])
        ]);
    }

    /**
     * 4. GET CLASS CONTENT (Gateway Microservices + Subjects)
     * Mengambil daftar mapel lokal & materi dari Go
     */
    public function getClassContent(Request $request): JsonResponse {
        $classId = $request->class_id;
        $user = auth('sanctum')->user();

        $class = ClassModel::find($classId);
        if (!$class) return response()->json(['status' => 'error', 'message' => 'Kelas tidak ditemukan'], 404);

        // Cek status pendaftaran
        $enrollStatus = 'none';
        if ($user) {
            $enrollment = Enrollment::where('user_id', $user->usersID)->where('class_id', $classId)->first();
            if ($enrollment) $enrollStatus = $enrollment->status;
        }

        // Ambil Daftar Mapel dari tabel penugasan pengajar (Lokal Laravel)
        $subjects = DB::table('teacher_assignments')
            ->where('class_id', $classId)
            ->pluck('subject_name')->unique()->values();

        try {
            // Tarik materi dari Microservice Go Materi (Port 9001)
            $materiRes = Http::get(env('GO_MATERI_URL') . "/api/materials?class_id=$classId");
            
            return response()->json([
                'status'        => 'success',
                'enroll_status' => $enrollStatus,
                'program_name'  => $class->program_name,
                'description'   => $class->description ?? "Materi belajar lengkap.",
                'price'         => (int) $class->price, 
                'subjects'      => $subjects,
                'materi'        => $materiRes->json()['data'] ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'success', 
                'enroll_status' => $enrollStatus, 
                'subjects' => $subjects, 
                'price' => (int) $class->price,
                'message' => 'Konten microservice sedang tidak terjangkau'
            ]);
        }
    }

    /**
     * 5. CHECK PROMO (Single Use & Anti Rp 0)
     */
    public function checkPromo(Request $request): JsonResponse {
        $user = Auth::user();
        $classId = $request->class_id;
        $promoCode = strtoupper($request->code);

        $class = ClassModel::find($classId);
        if (!$class) return response()->json(['status' => 'error', 'message' => 'Kelas tidak ditemukan'], 404);

        $promo = \App\Models\Promotion::where('code', $promoCode)
            ->where('class_id', $classId)
            ->where('is_active', true)
            ->where('quota', '>', 0)
            ->first();

        if (!$promo) return response()->json(['status' => 'error', 'message' => 'Promo tidak valid'], 404);

        // Proteksi: 1 User 1 Kali Pakai
        $alreadyUsed = DB::table('payments')
            ->where('user_id', $user->usersID)
            ->where('promo_code', $promoCode)
            ->whereIn('status', ['success', 'pending'])
            ->exists();

        if ($alreadyUsed) return response()->json(['status' => 'error', 'message' => 'Promo sudah pernah dipakai'], 400);

        $potongan = ($class->price * $promo->discount_percent) / 100;
        $hargaBaru = $class->price - $potongan;

        // Minimal bayar Rp 1.000 agar Midtrans tidak error
        if ($hargaBaru < 1000) { $hargaBaru = 1000; $potongan = $class->price - 1000; }

        return response()->json([
            'status' => 'success',
            'discount_amount' => (int) $potongan, 
            'final_price' => (int) $hargaBaru
        ]);
    }

    /**
     * 6. SUBMIT TRYOUT (Sinkronisasi Laravel & Go)
     */
    public function submitTryout(Request $request): JsonResponse {
        $userAnswers = $request->input('answers'); 
        $tryoutId = $request->tryout_id;
        $user = Auth::user();
        $correctCount = 0;

        try {
            // Ambil kunci jawaban dari Microservice Go Tryout (Port 9003)
            $response = Http::get(env('GO_TRYOUT_URL') . "/api/questions?tryout_id=$tryoutId");
            $questions = $response->json()['data'] ?? [];

            if (empty($questions)) return response()->json(['status' => 'error', 'message' => 'Soal tidak ditemukan'], 404);

            foreach ($questions as $q) {
                $qId = $q['question_id'];
                if (isset($userAnswers[$qId]) && strtoupper($userAnswers[$qId]) == strtoupper($q['correct_answer'])) {
                    $correctCount++;
                }
            }

            $score = count($questions) > 0 ? round(($correctCount / count($questions)) * 100) : 0;

            // A. SIMPAN KE LARAVEL (db_spectaacademy) -> Untuk Grafik Report
            DB::table('tryout_results')->insert([
                'user_id'       => (int) $user->usersID,
                'tryout_id'     => (int) $tryoutId,
                'score'         => (int) $score,
                'total_correct' => (int) $correctCount,
                'created_at'    => now(),
                'updated_at'    => now()
            ]);

            // B. SIMPAN KE GO SERVICE (specta_tryout) -> Untuk Riwayat Detail
            try {
                Http::post(env('GO_TRYOUT_URL') . "/api/tryouts/submissions/sync", [
                    'user_id'       => (int) $user->usersID,
                    'tryout_id'     => (int) $tryoutId,
                    'score'         => (int) $score,
                    'total_correct' => (int) $correctCount,
                ]);
            } catch (\Exception $ge) {
                Log::error("Go Sync Error: " . $ge->getMessage());
            }

            return response()->json(['status' => 'success', 'score' => $score]);

        } catch (\Exception $e) {
            Log::error("Submit Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal simpan skor'], 500);
        }
    }

    /**
     * 7. UPDATE PROFILE SISWA
     */
    public function updateProfile(Request $request): JsonResponse {
        $v = Validator::make($request->all(), [
            'parent_name' => 'required', 'alamat' => 'required', 'wa_ortu' => 'required', 'nisn' => 'required', 'dob' => 'required'
        ]);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        Auth::user()->student->update([
            'parent_name' => $request->parent_name, 'address' => $request->alamat, 
            'parent_phone' => $request->wa_ortu, 'national_id_number' => $request->nisn, 'date_of_birth' => $request->dob
        ]);
        return response()->json(['status' => 'success', 'message' => 'Profil diperbarui']);
    }

    /**
     * 8. LOGOUT
     */
    public function logout(Request $request): JsonResponse {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Berhasil Logout']);
    }

    /**
     * 9. GET SISWA SCHEDULE (Jadwal Kelas Aktif)
     */
    public function getSiswaSchedule(Request $request): JsonResponse {
        $user = Auth::user();
        $classIds = $user->classes()->wherePivot('status', 'active')->pluck('enrollments.class_id');
        
        $schedules = Schedule::whereIn('class_id', $classIds)
                    ->with(['class', 'material'])
                    ->orderBy('date', 'asc')
                    ->get();
                    
        return response()->json(['status' => 'success', 'data' => $schedules]);
    }
}