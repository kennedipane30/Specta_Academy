<?php

namespace App\Http\Controllers;

// IMPORT SEMUA MODEL & LIBRARY YANG DIBUTUHKAN
use App\Models\{User, Student, OtpCode, Enrollment, Material, Schedule, Tryout, Question, TryoutResult, PracticeQuestion};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Hash, Validator, DB, Auth, Mail, Log};
use App\Mail\OtpMail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AuthController extends Controller {

    // 1. REGISTRASI SISWA (OTP via Gmail, Nama boleh sama)
    public function registerSiswa(Request $request): JsonResponse {
        Log::info("Mencoba registrasi baru: " . $request->email);

        $v = Validator::make($request->all(), [
            'name' => 'required|regex:/^[a-zA-Z\s]+$/', // Nama TIDAK wajib unik
            'email' => 'required|email|unique:users',   // Email WAJIB unik
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

            // Buat record profil siswa
            Student::create(['user_id' => $user->usersID, 'address' => '-', 'date_of_birth' => null, 'parent_phone' => '-', 'parent_name' => '-']);
            
            // Buat OTP 6 Digit
            $otp = rand(100000, 999999);
            OtpCode::updateOrCreate(['user_id' => $user->usersID], ['otp' => $otp, 'valid_until' => Carbon::now()->addMinutes(10)]);

            // 🔥 KIRIM OTP KE EMAIL
            Mail::to($user->email)->send(new OtpMail($otp));

            DB::commit();
            Log::info("Registrasi Berhasil. OTP terkirim ke: " . $user->email);

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
        $user = User::where('name', $nameInput)->where('is_verified', false)->latest()->first();
        
        if (!$user) {
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
        $user = User::where('name', $nameInput)->where('is_verified', false)->latest()->first();
        
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        }

        $otp = rand(100000, 999999);
        OtpCode::updateOrCreate(['user_id' => $user->usersID], ['otp' => $otp, 'valid_until' => Carbon::now()->addMinutes(10)]);

        try {
            Mail::to($user->email)->send(new OtpMail($otp));
            return response()->json(['status' => 'success', 'message' => 'Kode OTP baru telah dikirim!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengirim email'], 500);
        }
    }

    // 4. LOGIN (Dengan pemuatan data Tryout & Student)
    public function login(Request $request): JsonResponse {
        $user = User::where('name', trim($request->name))->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Nama atau Password Salah'], 401);
        }
        
        if (!$user->is_verified) {
            return response()->json(['status' => 'error', 'message' => 'Akun belum diverifikasi melalui Email!'], 403);
        }

        // Load data tambahan untuk di HP
        $user->load(['student.class']);
        $user->tryout_results = TryoutResult::with('tryout')->where('user_id', $user->usersID)->latest()->get();
        
        return response()->json([
            'status' => 'success', 
            'token' => $user->createToken('token')->plainTextToken, 
            'user' => $user
        ]);
    }

    // 5. FORGOT PASSWORD (via Email)
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
            return response()->json(['status' => 'success', 'message' => 'Kode OTP Reset Password berhasil dikirim']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengirim email'], 500);
        }
    }

    // 6. RESET PASSWORD
    public function resetPassword(Request $request): JsonResponse {
        $v = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|numeric',
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        $user = User::where('email', trim($request->email))->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);

        $otpRecord = OtpCode::where('user_id', $user->usersID)->where('otp', $request->otp)->where('valid_until', '>', now())->first();

        if (!$otpRecord) return response()->json(['status' => 'error', 'message' => 'Kode OTP Salah atau Kadaluarsa'], 401);

        $user->update(['password' => bcrypt($request->password)]);
        $otpRecord->delete();

        return response()->json(['status' => 'success', 'message' => 'Password berhasil diperbarui!']);
    }

    // 7. LENGKAPI PROFIL
    public function updateProfile(Request $request): JsonResponse {
        $v = Validator::make($request->all(), [
            'parent_name' => 'required|string', 
            'alamat' => 'required|string', 
            'wa_ortu' => 'required', 
            'nisn' => 'required', 
            'dob' => 'required|date'
        ]);

        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        $user = Auth::user();
        $user->student->update([
            'parent_name' => $request->parent_name,
            'address' => $request->alamat,
            'parent_phone' => $request->wa_ortu,
            'national_id_number' => $request->nisn,
            'date_of_birth' => $request->dob
        ]);
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

    // 9. TRYOUT SYSTEM
    public function submitTryout(Request $request): JsonResponse {
        try {
            $userAnswers = $request->input('answers');
            $correctCount = 0;
            $questions = Question::where('tryout_id', $request->tryout_id)->get();

            foreach ($questions as $q) {
                if (isset($userAnswers[$q->question_id]) && $userAnswers[$q->question_id] == $q->correct_answer) { 
                    $correctCount++; 
                }
            }

            $score = count($questions) > 0 ? ($correctCount / count($questions)) * 100 : 0;
            $result = TryoutResult::create(['user_id' => Auth::id(), 'tryout_id' => $request->tryout_id, 'score' => (int)$score, 'total_correct' => $correctCount]);
            
            return response()->json(['status' => 'success', 'score' => $score, 'result_id' => $result->tryout_result_id, 'correct' => $correctCount], 200);
        } catch (\Exception $e) { return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500); }
    }

    // 10. AMBIL PROFIL (Query manual hasil tryout)
    public function getUserProfile(Request $request) {
        $user = User::with(['student.class'])->find(Auth::id());
        $results = TryoutResult::with('tryout')->where('user_id', $user->usersID)->latest()->get();
        $user->tryout_results = $results;

        return response()->json(['status' => 'success', 'user' => $user], 200);
    }

    // 11. DOWNLOAD PEMBAHASAN PDF
    public function downloadPembahasan($result_id) {
        $result = TryoutResult::with(['tryout.questions'])->findOrFail($result_id);
        $pdf = Pdf::loadView('pdf.pembahasan', compact('result'));
        return $pdf->download('Pembahasan_Spekta_Academy.pdf');
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