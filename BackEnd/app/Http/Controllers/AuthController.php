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
     * 1. REGISTER SISWA (Dukungan Daftar Ulang)
     */
    public function registerSiswa(Request $request): JsonResponse {
        $v = Validator::make($request->all(), [
            'name' => 'required|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email',
            'nomor_wa' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        DB::beginTransaction();
        try {
            $user = User::where('email', trim($request->email))->first();

            if ($user) {
                if ($user->is_verified) {
                    return response()->json(['status' => 'error', 'message' => 'Email ini sudah terdaftar dan aktif.'], 422);
                }
                $user->update([
                    'name' => trim($request->name),
                    'phone' => $request->nomor_wa,
                    'password' => bcrypt($request->password),
                ]);
            } else {
                $user = User::create([
                    'name' => trim($request->name),
                    'email' => trim($request->email),
                    'phone' => $request->nomor_wa,
                    'password' => bcrypt($request->password),
                    'role_id' => 3,
                    'is_verified' => false
                ]);

                Student::create([
                    'user_id' => $user->usersID,
                    'address' => '-',
                    'date_of_birth' => null,
                    'parent_phone' => '-',
                    'parent_name' => '-'
                ]);
            }

            $otp = rand(100000, 999999);
            OtpCode::updateOrCreate(['user_id' => $user->usersID], [
                'otp' => $otp,
                'valid_until' => Carbon::now()->addMinutes(10)
            ]);

            Mail::to($user->email)->send(new OtpMail($otp));
            DB::commit();

            return response()->json(['status' => 'success', 'name' => $user->name, 'message' => 'Silakan verifikasi email Anda'], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 2. RESEND OTP
     */
    public function resendOtp(Request $request): JsonResponse {
        $user = User::where('name', trim($request->name))->where('is_verified', false)->latest()->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);

        try {
            $otp = rand(100000, 999999);
            OtpCode::updateOrCreate(['user_id' => $user->usersID], ['otp' => $otp, 'valid_until' => Carbon::now()->addMinutes(10)]);
            Mail::to($user->email)->send(new OtpMail($otp));
            return response()->json(['status' => 'success', 'message' => 'OTP Baru berhasil dikirim!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengirim OTP'], 500);
        }
    }

    /**
     * 3. VERIFY REGISTRATION
     */
    public function verifyRegistration(Request $request): JsonResponse {
        $user = User::where('name', trim($request->name))->where('is_verified', false)->latest()->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);

        $otpRecord = OtpCode::where('user_id', $user->usersID)->where('otp', $request->otp)->where('valid_until', '>', now())->first();
        if (!$otpRecord) return response()->json(['status' => 'error', 'message' => 'OTP Salah atau Kadaluarsa'], 401);

        $user->is_verified = true;
        $user->save();
        $otpRecord->delete();

        return response()->json(['status' => 'success', 'message' => 'Akun Berhasil Aktif!']);
    }

    /**
     * 4. LOGIN
     */
    public function login(Request $request): JsonResponse {
        $user = User::where('name', trim($request->name))->orWhere('email', trim($request->name))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Credential Salah'], 401);
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
     * 5. GET CLASS CONTENT (Gateway Microservices + Subjects)
     */
    // BackEnd/app/Http/Controllers/AuthController.php

public function getClassContent(Request $request): JsonResponse {
    $classId = $request->class_id;
    $class = ClassModel::find($classId); // Ambil data kelas dari DB Utama

    if (!$class) return response()->json(['status' => 'error', 'message' => 'Kelas tidak ditemukan'], 404);

    try {
        // Ambil data dari Microservices Go
        $materiRes   = Http::get(env('GO_MATERI_URL') . "/api/materials?class_id=$classId");
        $tryoutRes   = Http::get(env('GO_TRYOUT_URL') . "/api/tryouts?class_id=$classId");
        $practiceRes = Http::get(env('GO_PRACTICE_URL') . "/api/practice?class_id=$classId");

        return response()->json([
            'status'        => 'success',
            'enroll_status' => 'active',
            'program_name'  => $class->program_name, // ✨ Kirim nama kelas asli
            'price'         => $class->price,
            'description'   => $class->description ?? "Materi belajar tersedia.",
            'materi'        => $materiRes->json()['data'] ?? [],
            'tryouts'       => $tryoutRes->json()['data'] ?? [],
            'practice_questions' => $practiceRes->json()['data'] ?? [],
        ]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'Microservice Offline'], 500);
    }
}

    /**
     * 6. CHECK PROMO
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

        if (!$promo) return response()->json(['status' => 'error', 'message' => 'Promo tidak valid atau kuota habis'], 404);

        $alreadyUsed = DB::table('payments')->where('user_id', $user->usersID)->where('promo_code', $promoCode)->whereIn('status', ['success', 'pending'])->exists();
        if ($alreadyUsed) return response()->json(['status' => 'error', 'message' => 'Promo sudah pernah digunakan'], 400);

        // Hitung diskon (mendukung persen & nominal fixed)
        if ($promo->discount_type == 'percent') {
            $potongan = ($class->price * $promo->discount_percent) / 100;
        } else {
            $potongan = $promo->discount_percent; // Nilai nominal
        }

        $hargaBaru = max(1000, $class->price - $potongan);

        return response()->json([
            'status' => 'success',
            'discount_amount' => (int) ($class->price - $hargaBaru),
            'final_price' => (int) $hargaBaru
        ]);
    }

    /**
     * 7. SUBMIT TRYOUT (Sinkronisasi Laravel & Go)
     */
    public function submitTryout(Request $request): JsonResponse {
        $userAnswers = $request->input('answers');
        $tryoutId = $request->tryout_id;
        $user = Auth::user();
        $correctCount = 0;

        try {
            $response = Http::get(env('GO_TRYOUT_URL') . "/api/questions?tryout_id=$tryoutId");
            $questions = $response->json()['data'] ?? [];

            foreach ($questions as $q) {
                $qId = $q['question_id'];
                if (isset($userAnswers[$qId]) && strtoupper($userAnswers[$qId]) == strtoupper($q['correct_answer'])) $correctCount++;
            }

            $score = count($questions) > 0 ? round(($correctCount / count($questions)) * 100) : 0;

            DB::table('tryout_results')->insert([
                'user_id' => $user->usersID, 'tryout_id' => $tryoutId, 'score' => $score,
                'total_correct' => $correctCount, 'created_at' => now(), 'updated_at' => now()
            ]);

            return response()->json(['status' => 'success', 'score' => (int)$score]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal simpan skor'], 500);
        }
    }

    /**
     * 8. UPDATE PROFILE, LOGOUT, GET SCHEDULE
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

    public function logout(Request $request): JsonResponse {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Berhasil Logout']);
    }

    public function getSiswaSchedule(Request $request): JsonResponse {
        $user = Auth::user();
        // Ambil kelas yang enrollments-nya aktif
        $classIds = DB::table('enrollments')->where('user_id', $user->usersID)->where('status', 'active')->pluck('class_id');

        $schedules = Schedule::whereIn('class_id', $classIds)
                    ->with(['class', 'subject']) // Load relasi subject agar nampak nama mapelnya
                    ->orderBy('date', 'asc')
                    ->get();

        return response()->json(['status' => 'success', 'data' => $schedules]);
    }
}
