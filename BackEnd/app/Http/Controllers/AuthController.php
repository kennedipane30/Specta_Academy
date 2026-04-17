<?php

namespace App\Http\Controllers;

use App\Models\{User, Student, OtpCode, Enrollment, Material, Schedule, Tryout, Question, TryoutResult, PracticeQuestion};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Hash, Validator, DB, Auth, Mail, Log};
use App\Mail\OtpMail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AuthController extends Controller {

    // 1. REGISTRASI SISWA
    public function registerSiswa(Request $request): JsonResponse {
        Log::info("Mencoba registrasi baru: " . $request->email);

        $v = Validator::make($request->all(), [
            // MODIFIKASI: Menghapus 'unique:users' agar nama boleh sama bagi banyak user
            'name' => 'required|regex:/^[a-zA-Z\s]+$/', 
            'email' => 'required|email|unique:users', // Email tetap harus unik (Wajib)
            'nomor_wa' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($v->fails()) {
            return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => trim($request->name), 
                'email' => trim($request->email), 
                'phone' => $request->nomor_wa, 
                'password' => bcrypt($request->password), 
                'role_id' => 3, 
                'is_verified' => false
            ]);

            // Buat record siswa
            Student::create(['user_id' => $user->usersID, 'address' => '-', 'date_of_birth' => null, 'parent_phone' => '-', 'parent_name' => '-']);
            
            // Buat OTP
            $otp = rand(100000, 999999);
            OtpCode::updateOrCreate(['user_id' => $user->usersID], ['otp' => $otp, 'valid_until' => Carbon::now()->addMinutes(10)]);

            // Kirim Email OTP
            Mail::to($user->email)->send(new OtpMail($otp));

            DB::commit();
            Log::info("Registrasi Berhasil. Nama: " . $user->name . " | Email: " . $user->email);

            return response()->json(['status' => 'success', 'name' => $user->name], 201);
        } catch (\Exception $e) { 
            DB::rollBack(); 
            Log::error("Gagal Registrasi: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal Sistem: ' . $e->getMessage()], 500); 
        }
    }

    // 2. VERIFIKASI OTP REGISTRASI
    public function verifyRegistration(Request $request): JsonResponse {
        $nameInput = trim($request->name);
        
        // Cari user yang belum diverifikasi dengan nama tersebut (ambil yang terbaru)
        $user = User::where('name', $nameInput)->where('is_verified', false)->latest()->first();
        
        if (!$user) {
            Log::warning("Verifikasi Gagal: User '" . $nameInput . "' tidak ditemukan atau sudah aktif.");
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan atau sudah aktif'], 404);
        }
        
        $otpRecord = OtpCode::where('user_id', $user->usersID)
                            ->where('otp', $request->otp)
                            ->where('valid_until', '>', now())
                            ->first();

        if (!$otpRecord) return response()->json(['status' => 'error', 'message' => 'Kode OTP Salah atau Kadaluarsa'], 401);
        
        $user->is_verified = true; 
        $user->save();
        $otpRecord->delete();

        return response()->json(['status' => 'success', 'message' => 'Akun Berhasil Aktif!']);
    }

    // 3. KIRIM ULANG OTP (RESEND)
    public function resendOtp(Request $request): JsonResponse {
        $nameInput = trim($request->name);
        Log::info("Mencoba kirim ulang OTP untuk: " . $nameInput);

        // Cari user yang belum diverifikasi dengan nama tersebut
        $user = User::where('name', $nameInput)->where('is_verified', false)->latest()->first();
        
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan atau sudah diverifikasi'], 404);
        }

        $otp = rand(100000, 999999);
        OtpCode::updateOrCreate(
            ['user_id' => $user->usersID], 
            ['otp' => $otp, 'valid_until' => Carbon::now()->addMinutes(10)]
        );

        try {
            Mail::to($user->email)->send(new OtpMail($otp));
            return response()->json(['status' => 'success', 'message' => 'Kode OTP baru telah dikirim!']);
        } catch (\Exception $e) {
            Log::error("Gagal Kirim Email (Resend): " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal mengirim email'], 500);
        }
    }

    // 4. LOGIN
    public function login(Request $request): JsonResponse {
        $user = User::where('name', trim($request->name))->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Nama atau Password Salah'], 401);
        }
        
        if (!$user->is_verified) {
            return response()->json(['status' => 'error', 'message' => 'Akun belum diverifikasi!'], 403);
        }
        
        return response()->json([
            'status' => 'success', 
            'token' => $user->createToken('token')->plainTextToken, 
            'user' => $user->load('student')
        ]);
    }

    // 5. FORGOT PASSWORD (Berdasarkan Email karena Email Unik)
    public function forgotPassword(Request $request): JsonResponse {
        $email = trim($request->email);
        $request->validate(['email' => 'required|email']);
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Email tidak terdaftar!'], 404);
        }
        
        $otp = rand(100000, 999999);
        OtpCode::updateOrCreate(['user_id' => $user->usersID], ['otp' => $otp, 'valid_until' => Carbon::now()->addMinutes(10)]);

        try {
            Mail::to($user->email)->send(new OtpMail($otp));
            return response()->json(['status' => 'success', 'message' => 'Kode OTP Reset Password dikirim ke email']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengirim email'], 500);
        }
    }

    // 6. RESET PASSWORD
    public function resetPassword(Request $request): JsonResponse {
        $v = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|numeric',
            'password' => ['required', 'confirmed', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
        ]);

        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        $user = User::where('email', trim($request->email))->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);

        $otpRecord = OtpCode::where('user_id', $user->usersID)
                            ->where('otp', $request->otp)
                            ->where('valid_until', '>', now())
                            ->first();

        if (!$otpRecord) return response()->json(['status' => 'error', 'message' => 'Kode OTP Salah atau Kadaluarsa'], 401);

        $user->update(['password' => bcrypt($request->password)]);
        $otpRecord->delete();

        return response()->json(['status' => 'success', 'message' => 'Password berhasil diperbarui!']);
    }

    // 7. LENGKAPI PROFIL
    public function updateProfile(Request $request): JsonResponse {
        $v = Validator::make($request->all(), ['parent_name' => 'required', 'alamat' => 'required', 'wa_ortu' => 'required', 'nisn' => 'required', 'dob' => 'required|date']);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);
        $user = Auth::user();
        $user->student->update(['parent_name' => $request->parent_name, 'address' => $request->alamat, 'parent_phone' => $request->wa_ortu, 'national_id_number' => $request->nisn, 'date_of_birth' => $request->dob]);
        return response()->json(['status' => 'success', 'message' => 'Profil berhasil dilengkapi']);
    }

    // 8. DAFTAR KELAS
    public function joinClass(Request $request): JsonResponse {
        $v = Validator::make($request->all(), ['class_id' => 'required', 'payment_proof' => 'required|image|max:2048']);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);
        try {
            $user = Auth::user();
            $path = $request->file('payment_proof')->store('proofs', 'public');
            Enrollment::create(['user_id' => $user->usersID, 'class_id' => $request->class_id, 'payment_proof' => $path, 'status' => 'pending']);
            return response()->json(['status' => 'success', 'message' => 'Pembayaran diterima!'], 200);
        } catch (\Exception $e) { return response()->json(['status' => 'error', 'message' => 'Database error'], 500); }
    }

    // 9. AMBIL KONTEN KELAS
    public function getClassContent(Request $request): JsonResponse {
        try {
            $classId = $request->class_id;
            $materi = Material::where('class_id', $classId)->get();
            $tryouts = Tryout::where('class_id', $classId)->get();
            $latihan = PracticeQuestion::where('class_id', $classId)->get();
            $enroll = Enrollment::where('user_id', Auth::id())->where('class_id', $classId)->first();
            return response()->json(['status' => 'success', 'enroll_status' => $enroll ? $enroll->status : 'none', 'materi' => $materi, 'tryouts' => $tryouts, 'practice_questions' => $latihan], 200);
        } catch (\Exception $e) { return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500); }
    }

    // 10. JADWAL SISWA
    public function getSiswaSchedule(Request $request): JsonResponse {
        $user = Auth::user();
        $activeClassIds = Enrollment::where('user_id', $user->usersID)->where('status', 'active')->pluck('class_id');
        $schedules = Schedule::whereIn('class_id', $activeClassIds)->with(['teacher', 'class'])->get();
        return response()->json(['status' => 'success', 'data' => $schedules]);
    }

    // 11. TRYOUT SYSTEM
    public function getQuestions(Request $request): JsonResponse {
        $questions = Question::where('tryout_id', $request->tryout_id)->get();
        if ($questions->isEmpty()) return response()->json(['status' => 'error', 'message' => 'Soal belum tersedia'], 404);
        return response()->json(['status' => 'success', 'data' => $questions], 200);
    }

    public function submitTryout(Request $request): JsonResponse {
        try {
            $userAnswers = $request->input('answers');
            $correctCount = 0;
            $questions = Question::where('tryout_id', $request->tryout_id)->get();
            foreach ($questions as $q) {
                if (isset($userAnswers[$q->question_id]) && $userAnswers[$q->question_id] == $q->correct_answer) { $correctCount++; }
            }
            $score = count($questions) > 0 ? ($correctCount / count($questions)) * 100 : 0;
            $result = TryoutResult::create(['user_id' => Auth::id(), 'tryout_id' => $request->tryout_id, 'score' => (int)$score, 'total_correct' => $correctCount]);
            return response()->json(['status' => 'success', 'score' => $score, 'result_id' => $result->tryout_result_id, 'correct' => $correctCount], 200);
        } catch (\Exception $e) { return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500); }
    }

    // 12. LOGOUT & PROMO
    public function logout(Request $request): JsonResponse {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Berhasil Logout']);
    }

    public function checkPromo(Request $request): JsonResponse {
        $promo = \App\Models\Promotion::where('code', strtoupper($request->code))->where('class_id', $request->class_id)->where('is_active', true)->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->first();
        if (!$promo) return response()->json(['status' => 'error', 'message' => 'Kode promo tidak valid!'], 404);
        $potongan = $request->price * ($promo->discount_percent / 100);
        return response()->json(['status' => 'success', 'discount_percent' => $promo->discount_percent, 'new_price' => $request->price - $potongan]);
    }
}