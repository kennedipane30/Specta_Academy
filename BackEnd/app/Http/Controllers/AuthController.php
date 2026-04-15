<?php

namespace App\Http\Controllers;

use App\Models\{User, Student, OtpCode, Enrollment, Material, Schedule, Tryout, Question, TryoutResult, PracticeQuestion};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Hash, Validator, DB, Auth};
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AuthController extends Controller {

    // 1. REGISTRASI SISWA
    public function registerSiswa(Request $request): JsonResponse {
        $v = Validator::make($request->all(), [
            'name' => 'required|regex:/^[a-zA-Z\s]+$/|unique:users',
            'email' => 'required|email|unique:users',
            'nomor_wa' => 'required',
            'password' => ['required', 'confirmed', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
        ]);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);
        DB::beginTransaction();
        try {
            $user = User::create(['name' => trim($request->name), 'email' => $request->email, 'phone' => $request->nomor_wa, 'password' => bcrypt($request->password), 'role_id' => 3, 'is_verified' => false]);
            // MODIFIKASI: school -> address, dob -> date_of_birth, wa_ortu -> parent_phone
            Student::create(['user_id' => $user->usersID, 'address' => '-', 'date_of_birth' => null, 'parent_phone' => '-', 'parent_name' => '-']);
            $otp = rand(100000, 999999);
            OtpCode::updateOrCreate(['user_id' => $user->usersID], ['otp' => $otp, 'valid_until' => Carbon::now()->addMinutes(10)]);
            DB::commit();
            return response()->json(['status' => 'success', 'otp' => $otp, 'name' => $user->name], 201);
        } catch (\Exception $e) { DB::rollBack(); return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500); }
    }

    // 2. VERIFIKASI OTP
    public function verifyRegistration(Request $request): JsonResponse {
        $user = User::where('name', trim($request->name))->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        $otpRecord = OtpCode::where('user_id', $user->usersID)->where('otp', $request->otp)->where('valid_until', '>', now())->first();
        if (!$otpRecord) return response()->json(['status' => 'error', 'message' => 'Kode OTP Salah/Kadaluarsa'], 401);
        $user->is_verified = true; $user->save();
        $otpRecord->delete();
        return response()->json(['status' => 'success', 'message' => 'Akun Aktif!']);
    }

    // 3. LOGIN
    public function login(Request $request): JsonResponse {
        $user = User::where('name', trim($request->name))->first();
        if (!$user || !Hash::check($request->password, $user->password)) return response()->json(['status' => 'error', 'message' => 'Nama/Password Salah'], 401);
        if (!$user->is_verified) return response()->json(['status' => 'error', 'message' => 'Akun belum verifikasi WA!'], 403);
        return response()->json(['status' => 'success', 'token' => $user->createToken('token')->plainTextToken, 'user' => $user->load('student')]);
    }

    public function forgotPassword(Request $request): JsonResponse {
        $request->validate(['phone' => 'required']);
        $user = User::where('phone', $request->phone)->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'Nomor WhatsApp tidak terdaftar!'], 404);
        $otp = rand(100000, 999999);
        OtpCode::updateOrCreate(['user_id' => $user->usersID], ['otp' => $otp, 'valid_until' => Carbon::now()->addMinutes(10)]);
        return response()->json(['status' => 'success', 'message' => 'Kode OTP Reset Password berhasil dikirim', 'otp' => $otp]);
    }

    public function resetPassword(Request $request): JsonResponse {
        $v = Validator::make($request->all(), [
            'phone' => 'required',
            'otp' => 'required|numeric',
            'password' => ['required', 'confirmed', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
        ]);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);
        $user = User::where('phone', $request->phone)->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        $otpRecord = OtpCode::where('user_id', $user->usersID)->where('otp', $request->otp)->where('valid_until', '>', now())->first();
        if (!$otpRecord) return response()->json(['status' => 'error', 'message' => 'Kode OTP Salah atau Kadaluarsa'], 401);
        $user->update(['password' => bcrypt($request->password)]);
        $otpRecord->delete();
        return response()->json(['status' => 'success', 'message' => 'Password berhasil diperbarui!']);
    }

    // 4. LENGKAPI PROFIL
    public function updateProfile(Request $request): JsonResponse {
        $v = Validator::make($request->all(), ['parent_name' => 'required|string', 'alamat' => 'required|string', 'wa_ortu' => 'required', 'nisn' => 'required', 'dob' => 'required|date']);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // MODIFIKASI: school -> address, wa_ortu -> parent_phone, nisn -> national_id_number, dob -> date_of_birth
        $user->student->update([
            'parent_name' => $request->parent_name,
            'address' => $request->alamat,
            'parent_phone' => $request->wa_ortu,
            'national_id_number' => $request->nisn,
            'date_of_birth' => $request->dob
        ]);
        return response()->json(['status' => 'success', 'message' => 'Profil berhasil dilengkapi']);
    }

    // 5. DAFTAR KELAS
 public function joinClass(Request $request): JsonResponse {
        $v = Validator::make($request->all(), [
            'class_id' => 'required',
            'payment_proof' => 'required|image|max:2048'
        ]);

        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        try {
            $user = Auth::user();
            // Simpan gambar ke folder public/proofs
            $path = $request->file('payment_proof')->store('proofs', 'public');

            Enrollment::create([
                'user_id' => $user->usersID,
                'class_id' => $request->class_id,
                'payment_proof' => $path,
                'status' => 'pending' // Default pendaftaran baru
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment received! Waiting for admin confirmation.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Database error'], 500);
        }
    }

    // 6. AMBIL KONTEN (MATERI, TRYOUT, LATIHAN)
    public function getClassContent(Request $request): JsonResponse {
        try {
            $classId = $request->class_id;
            $materi = Material::where('class_id', $classId)->get();
            $tryouts = Tryout::where('class_id', $classId)->get();
            // MODIFIKASI: Menggunakan Model PracticeQuestion
            $latihan = PracticeQuestion::where('class_id', $classId)->get();

            $enroll = Enrollment::where('user_id', Auth::id())->where('class_id', $classId)->first();

            return response()->json([
                'status' => 'success',
                'enroll_status' => $enroll ? $enroll->status : 'none',
                'materi' => $materi,
                'tryouts' => $tryouts,
                'practice_questions' => $latihan
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 7. JADWAL
    public function getSiswaSchedule(Request $request): JsonResponse {
        $user = Auth::user();
        // MODIFIKASI: status 'aktif' -> 'active'
        $activeClassIds = Enrollment::where('user_id', $user->usersID)->where('status', 'active')->pluck('class_id');
        // MODIFIKASI: relasi classModel -> class
        $schedules = Schedule::whereIn('class_id', $activeClassIds)->with(['teacher', 'class'])->get();
        return response()->json(['status' => 'success', 'data' => $schedules]);
    }

    // 8. AMBIL DAFTAR SOAL
    public function getQuestions(Request $request): JsonResponse {
        $questions = Question::where('tryout_id', $request->tryout_id)->get();
        if ($questions->isEmpty()) return response()->json(['status' => 'error', 'message' => 'Soal belum tersedia'], 404);
        return response()->json(['status' => 'success', 'data' => $questions], 200);
    }

    // 9. SUBMIT TRYOUT
    public function submitTryout(Request $request): JsonResponse {
        try {
            $userAnswers = $request->input('answers');
            $correctCount = 0;
            $questions = Question::where('tryout_id', $request->tryout_id)->get();
            foreach ($questions as $q) {
                // MODIFIKASI: questionsID -> question_id
                if (isset($userAnswers[$q->question_id]) && $userAnswers[$q->question_id] == $q->correct_answer) { $correctCount++; }
            }
            $score = count($questions) > 0 ? ($correctCount / count($questions)) * 100 : 0;
            // MODIFIKASI: TryoutResult creation
            $result = TryoutResult::create(['user_id' => Auth::id(), 'tryout_id' => $request->tryout_id, 'score' => (int)$score, 'total_correct' => $correctCount]);
            // MODIFIKASI: resultsID -> tryout_result_id
            return response()->json(['status' => 'success', 'score' => $score, 'result_id' => $result->tryout_result_id, 'correct' => $correctCount], 200);
        } catch (\Exception $e) { return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500); }
    }

    // 10. DOWNLOAD PEMBAHASAN PDF
    public function downloadPembahasan($result_id) {
        $result = TryoutResult::with(['tryout.questions'])->findOrFail($result_id);
        $pdf = Pdf::loadView('pdf.pembahasan', compact('result'));
        return $pdf->download('Pembahasan_Spekta_Academy.pdf');
    }

    // 11. LOGOUT
    public function logout(Request $request): JsonResponse {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Berhasil Logout']);
    }

    public function checkPromo(Request $request): JsonResponse {
        $promo = \App\Models\Promotion::where('code', strtoupper($request->code))
                    ->where('class_id', $request->class_id)
                    ->where('is_active', true) // MODIFIKASI: is_active
                    ->whereDate('start_date', '<=', now())
                    ->whereDate('end_date', '>=', now())
                    ->first();

        if (!$promo) return response()->json(['status' => 'error', 'message' => 'Kode promo tidak valid!'], 404);
        $potongan = $request->price * ($promo->discount_percent / 100);
        return response()->json(['status' => 'success', 'discount_percent' => $promo->discount_percent, 'new_price' => $request->price - $potongan]);
    }
}
