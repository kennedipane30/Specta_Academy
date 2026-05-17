<?php

namespace App\Http\Controllers;

// IMPORT SEMUA MODEL & LIBRARY
use App\Models\{User, Student, OtpCode, Enrollment, Material, Schedule, Tryout, Question, TryoutResult, PracticeQuestion, ClassModel};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Hash, Validator, DB, Auth, Mail, Log, Http}; // ✨ Tambahkan Http
use App\Mail\OtpMail;
use Carbon\Carbon;

class AuthController extends Controller {

    // ... (Fungsi registerSiswa, verifyRegistration, login tetap sama) ...

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
            $user = User::create(['name' => trim($request->name), 'email' => trim($request->email), 'phone' => $request->nomor_wa, 'password' => bcrypt($request->password), 'role_id' => 3, 'is_verified' => false]);
            Student::create(['user_id' => $user->usersID, 'address' => '-', 'date_of_birth' => null, 'parent_phone' => '-', 'parent_name' => '-']);
            $otp = rand(100000, 999999);
            OtpCode::updateOrCreate(['user_id' => $user->usersID], ['otp' => $otp, 'valid_until' => Carbon::now()->addMinutes(10)]);
            Mail::to($user->email)->send(new OtpMail($otp));
            DB::commit();
            return response()->json(['status' => 'success', 'name' => $user->name], 201);
        } catch (\Exception $e) { DB::rollBack(); return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500); }
    }

    public function verifyRegistration(Request $request): JsonResponse {
        $user = User::where('name', trim($request->name))->where('is_verified', false)->latest()->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        $otpRecord = OtpCode::where('user_id', $user->usersID)->where('otp', $request->otp)->where('valid_until', '>', now())->first();
        if (!$otpRecord) return response()->json(['status' => 'error', 'message' => 'OTP Salah'], 401);
        $user->is_verified = true; $user->save(); $otpRecord->delete();
        return response()->json(['status' => 'success', 'message' => 'Akun Berhasil Aktif!']);
    }

    public function login(Request $request): JsonResponse {
        $user = User::where('name', trim($request->name))->first();
        if (!$user || !Hash::check($request->password, $user->password)) return response()->json(['status' => 'error', 'message' => 'Nama atau Password Salah'], 401);
        if ($user->role_id == 3 && !$user->is_verified) return response()->json(['status' => 'error', 'message' => 'Akun belum diverifikasi!'], 403);
        return response()->json(['status' => 'success', 'token' => $user->createToken('token')->plainTextToken, 'user' => $user->load(['student.class'])]);
    }

    /**
     * ✨ 4. GET CLASS CONTENT (GATEWAY: Ambil dari 3 Microservices)
     */
public function getClassContent(Request $request): JsonResponse {
    $classId = $request->class_id;

    try {
        $materiRes   = Http::get(env('GO_MATERI_URL') . "/api/materials?class_id=$classId");
        $tryoutRes   = Http::get(env('GO_TRYOUT_URL') . "/api/tryouts?class_id=$classId");
        $practiceRes = Http::get(env('GO_PRACTICE_URL') . "/api/practice?class_id=$classId");

        // DEBUG: Cek di terminal Laravel apakah data Tryout ada isinya
        // \Log::info("Data Tryout dari Go:", [$tryoutRes->json()]);

        return response()->json([
            'status'        => 'success',
            'enroll_status' => 'active',
            'materi'        => $materiRes->json()['data'] ?? [],
            'tryouts'       => $tryoutRes->json()['data'] ?? [], // ✨ Key ini wajib 'tryouts'
            'practice_questions' => $practiceRes->json()['data'] ?? [],
            'description'   => "Materi belajar tersedia."
        ]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'Microservice Offline'], 500);
    }
}
    public function getMaterials(Request $request): JsonResponse {
        $response = Http::get(env('GO_MATERI_URL') . "/api/materials?class_id=" . $request->class_id);
        return response()->json($response->json());
    }

    public function getQuestions(Request $request): JsonResponse {
        $response = Http::get(env('GO_TRYOUT_URL') . "/api/questions?tryout_id=" . $request->tryout_id);
        return response()->json($response->json());
    }

    public function submitTryout(Request $request): JsonResponse {
    $userAnswers = $request->input('answers'); // Format: {"6": "A", "7": "B"}
    $tryoutId = $request->tryout_id;
    $correctCount = 0;

    try {
        // 1. Ambil kunci jawaban dari Microservice Go
        $response = Http::get(env('GO_TRYOUT_URL') . "/api/questions?tryout_id=$tryoutId");
        $questions = $response->json()['data'] ?? [];

        if (empty($questions)) {
            return response()->json(['status' => 'error', 'message' => 'Soal tidak ditemukan di Go Service'], 404);
        }

        foreach ($questions as $q) {
            // ✨ Pastikan mengambil ID yang benar dari JSON Go (biasanya question_id)
            $qId = $q['question_id'];

            if (isset($userAnswers[$qId]) && $userAnswers[$qId] == $q['correct_answer']) {
                $correctCount++;
            }
        }

        $totalQuestions = count($questions);
        $score = ($correctCount / $totalQuestions) * 100;

        // 2. Simpan ke Database
        $result = TryoutResult::create([
            'user_id'       => Auth::user()->usersID, // Pastikan menggunakan usersID
            'tryout_id'     => $tryoutId,
            'score'         => (int)$score,
            'total_correct' => $correctCount
        ]);

        return response()->json(['status' => 'success', 'score' => $score]);

    } catch (\Exception $e) {
        // ✨ TULIS ERROR KE LOG AGAR BISA DIDEBUG
        Log::error("Gagal Submit Tryout: " . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}
    // ... (Fungsi updateProfile, joinClass, logout, checkPromo, getSiswaSchedule tetap sama) ...
    public function updateProfile(Request $request): JsonResponse {
        $v = Validator::make($request->all(), ['parent_name' => 'required', 'alamat' => 'required', 'wa_ortu' => 'required', 'nisn' => 'required', 'dob' => 'required']);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);
        Auth::user()->student->update(['parent_name' => $request->parent_name, 'address' => $request->alamat, 'parent_phone' => $request->wa_ortu, 'national_id_number' => $request->nisn, 'date_of_birth' => $request->dob]);
        return response()->json(['status' => 'success', 'message' => 'Profil diperbarui']);
    }

    public function joinClass(Request $request): JsonResponse {
        $request->validate(['class_id' => 'required', 'payment_proof' => 'required|image']);
        $path = $request->file('payment_proof')->store('proofs', 'public');
        Enrollment::create(['user_id' => Auth::id(), 'class_id' => $request->class_id, 'payment_proof' => $path, 'status' => 'pending']);
        return response()->json(['status' => 'success', 'message' => 'Pembayaran sedang dikonfirmasi.']);
    }

    public function logout(Request $request): JsonResponse {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Berhasil Logout']);
    }

    public function checkPromo(Request $request): JsonResponse {
        $class = ClassModel::find($request->class_id);
        if (!$class) return response()->json(['status' => 'error', 'message' => 'Kelas tidak ditemukan'], 404);
        $promo = \App\Models\Promotion::where('code', strtoupper($request->code))->where('class_id', $request->class_id)->where('is_active', true)->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->first();
        if (!$promo) return response()->json(['status' => 'error', 'message' => 'Promo tidak valid'], 404);
        $potongan = $class->price * ($promo->discount_percent / 100);
        return response()->json(['status' => 'success', 'discount' => $potongan, 'new_price' => $class->price - $potongan]);
    }

/**
     * 11. GET SCHEDULE
     */
    public function getSiswaSchedule(Request $request): JsonResponse {
        /** @var \App\Models\User $user */ // ✨ Tambahkan baris ini untuk menghilangkan error merah
        $user = Auth::user();

        // Pastikan model User Anda memiliki fungsi public function classes()
        $classIds = $user->classes()
                    ->wherePivot('status', 'active')
                    ->pluck('enrollments.class_id');

        $schedules = Schedule::whereIn('class_id', $classIds)
                    ->with(['class', 'material'])
                    ->orderBy('date', 'asc')
                    ->get();

        return response()->json(['status' => 'success', 'data' => $schedules]);
    }
}
