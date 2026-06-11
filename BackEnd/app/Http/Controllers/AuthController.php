<?php

namespace App\Http\Controllers;

// IMPORT SEMUA MODEL & LIBRARY YANG DIBUTUHKAN
use App\Models\{User, Student, OtpCode, Enrollment, Schedule, ClassModel};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Hash, Validator, DB, Auth, Mail, Log, Http, Storage};
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
    public function getClassContent(Request $request): JsonResponse {
        $classId = $request->class_id;
        $class = ClassModel::find($classId);

        if (!$class) return response()->json(['status' => 'error', 'message' => 'Kelas tidak ditemukan'], 404);

        try {
            // Ambil data dari Microservices Go
            $materiRes   = Http::get(env('GO_MATERI_URL') . "/api/materials?class_id=$classId");
            $tryoutRes   = Http::get(env('GO_TRYOUT_URL') . "/api/tryouts?class_id=$classId");
            $practiceRes = Http::get(env('GO_PRACTICE_URL') . "/api/practice?class_id=$classId");

            return response()->json([
                'status'        => 'success',
                'enroll_status' => 'active',
                'program_name'  => $class->program_name,
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

        if ($promo->discount_type == 'percent') {
            $potongan = ($class->price * $promo->discount_percent) / 100;
        } else {
            $potongan = $promo->discount_percent;
        }

        $hargaBaru = max(1000, $class->price - $potongan);

        return response()->json([
            'status' => 'success',
            'discount_amount' => (int) ($class->price - $hargaBaru),
            'final_price' => (int) $hargaBaru
        ]);
    }

    /**
     * 7. SUBMIT TRYOUT (via Microservice Go)
     */
    public function submitTryout(Request $request): JsonResponse {
        $userAnswers = $request->input('answers');
        $tryoutId = $request->tryout_id;
        $user = Auth::user();

        $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');

        try {
            $response = Http::post("$goUrl/api/tryouts/$tryoutId/submit", [
                'tryout_id' => (int) $tryoutId,
                'user_id'   => (int) $user->usersID,
                'answers'   => $userAnswers
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'status' => 'success',
                    'score' => $data['score'] ?? 0,
                    'correct' => $data['correct'] ?? 0
                ]);
            }

            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan tryout'], 500);

        } catch (\Exception $e) {
            Log::error("Submit Tryout Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Microservice tidak merespon'], 500);
        }
    }

    /**
     * 8. GET TRYOUT HISTORY (via Microservice Go)
     */
    public function getTryoutHistory(Request $request): JsonResponse {
        $user = Auth::user();

        $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');

        try {
            $response = Http::get("$goUrl/api/tryouts/history", [
                'user_id' => $user->usersID
            ]);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $response->json()['data'] ?? []
                ]);
            }

            return response()->json(['status' => 'error', 'data' => []], 500);

        } catch (\Exception $e) {
            Log::error("Get Tryout History Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'data' => []], 500);
        }
    }

    /**
     * 9. UPDATE PROFILE
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
     * 10. LOGOUT
     */
    public function logout(Request $request): JsonResponse {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Berhasil Logout']);
    }

    /**
     * 11. GET SISWA SCHEDULE
     */
    public function getSiswaSchedule(Request $request): JsonResponse {
        $user = Auth::user();
        $classIds = DB::table('enrollments')->where('user_id', $user->usersID)->where('status', 'active')->pluck('class_id');

        $schedules = Schedule::whereIn('class_id', $classIds)
                    ->with(['class', 'subject'])
                    ->orderBy('date', 'asc')
                    ->get();

        return response()->json(['status' => 'success', 'data' => $schedules]);
    }

    /**
     * 12. GET PROFILE
     */
    public function getProfile(Request $request): JsonResponse {
        Log::info('=== getProfile() DIPANGGIL ===');

        $user = Auth::user();
        $student = DB::table('students')->where('user_id', $user->usersID)->first();

        $enrolledClasses = [];
        $joinedDate = '-';

        $enrollments = DB::table('enrollments')
            ->where('user_id', $user->usersID)
            ->where('status', 'active')
            ->get();

        foreach ($enrollments as $enrollment) {
            $classData = DB::table('classes')->where('class_id', $enrollment->class_id)->first();
            if ($classData) {
                $enrolledClasses[] = [
                    'id'           => $classData->class_id,
                    'program_name' => $classData->program_name,
                    'enrolled_at'  => $enrollment->created_at,
                    'expires_at'   => $enrollment->expires_at,
                ];
            }
        }

        if ($enrollments->isNotEmpty()) {
            $firstEnrollment = $enrollments->first();
            if ($firstEnrollment && $firstEnrollment->created_at) {
                $joinedDate = Carbon::parse($firstEnrollment->created_at)->translatedFormat('d F Y');
            }
        } else if ($student && $student->created_at) {
            $joinedDate = Carbon::parse($student->created_at)->translatedFormat('d F Y');
        }

        $roleName = DB::table('roles')->where('rolesID', $user->role_id)->value('name') ?? 'STUDENT';

        return response()->json([
            'status' => 'success',
            'user' => [
                'id'               => $user->usersID,
                'name'             => $user->name,
                'email'            => $user->email,
                'photo_url'        => $user->photo_url,
                'role'             => strtoupper($roleName),
                'joined_date'      => $joinedDate,
                'enrolled_classes' => $enrolledClasses
            ]
        ]);
    }

    /**
     * 13. UPLOAD FOTO PROFIL
     */
    public function updatePhoto(Request $request): JsonResponse {
        $request->validate(['photo' => 'required|image|mimes:jpeg,png,jpg|max:2048']);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $path = $request->file('photo')->store('profile_photos', 'public');
            $user->update(['photo' => $path]);

            return response()->json([
                'status' => 'success',
                'message' => 'Foto berhasil diperbarui',
                'photo_url' => $user->photo_url
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Tidak ada gambar'], 400);
    }
}
