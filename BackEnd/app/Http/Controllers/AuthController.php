<?php

namespace App\Http\Controllers;

// IMPORT SEMUA MODEL & LIBRARY
use App\Models\{User, Student, OtpCode, Enrollment, Material, Schedule, Tryout, Question, TryoutResult, PracticeQuestion, ClassModel};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Hash, Validator, DB, Auth, Mail, Log};
use App\Mail\OtpMail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AuthController extends Controller {

    /**
     * 1. REGISTRASI SISWA
     */
    public function registerSiswa(Request $request): JsonResponse {
        Log::info("Mencoba registrasi baru: " . $request->email);

        $v = Validator::make($request->all(), [
            'name' => 'required|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|unique:users',   
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

            Student::create([
                'user_id' => $user->usersID, 
                'address' => '-', 
                'date_of_birth' => null, 
                'parent_phone' => '-', 
                'parent_name' => '-'
            ]);
            
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
            return response()->json(['status' => 'error', 'message' => 'Gagal Sistem: ' . $e->getMessage()], 500); 
        }
    }

    /**
     * 2. VERIFIKASI OTP
     */
    public function verifyRegistration(Request $request): JsonResponse {
        $nameInput = trim($request->name);
        $user = User::where('name', $nameInput)->where('is_verified', false)->latest()->first();
        
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        
        $otpRecord = OtpCode::where('user_id', $user->usersID)
                            ->where('otp', $request->otp)
                            ->where('valid_until', '>', now())
                            ->first();

        if (!$otpRecord) return response()->json(['status' => 'error', 'message' => 'OTP Salah'], 401);
        
        $user->is_verified = true; 
        $user->save();
        $otpRecord->delete();

        return response()->json(['status' => 'success', 'message' => 'Akun Berhasil Aktif!']);
    }

    /**
     * 3. LOGIN
     */
    public function login(Request $request): JsonResponse {
        $user = User::where('name', trim($request->name))->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Nama atau Password Salah'], 401);
        }
        
        if ($user->role_id == 3 && !$user->is_verified) {
            return response()->json(['status' => 'error', 'message' => 'Akun belum diverifikasi!'], 403);
        }

        $user->load(['student.class']);
        return response()->json([
            'status' => 'success', 
            'token' => $user->createToken('token')->plainTextToken, 
            'user' => $user
        ]);
    }

    /**
     * 4. GET CLASS CONTENT (MODIFIKASI DINAMIS ADMIN)
     */
    public function getClassContent(Request $request): JsonResponse {
        $classId = $request->class_id;
        $user = Auth::user();

        $class = ClassModel::with(['materials'])->find($classId);

        if (!$class) {
            return response()->json(['status' => 'error', 'message' => 'Kelas tidak ditemukan'], 404);
        }

        $enrollment = Enrollment::where('user_id', $user->usersID)
                                ->where('class_id', $classId)
                                ->first();

        $status = $enrollment ? $enrollment->status : 'none';
        $tryouts = Tryout::where('class_id', $classId)->get();

        return response()->json([
            'status'        => 'success',
            'enroll_status' => $status,
            'price'         => $class->price,      
            'description'   => $class->description,
            'image_url'     => $class->image_url,  
            'materi'        => $class->materials,
            'tryouts'       => $tryouts,
        ]);
    }

    /**
     * 5. UPDATE PROFIL
     */
    public function updateProfile(Request $request): JsonResponse {
        $v = Validator::make($request->all(), [
            'parent_name' => 'required', 'alamat' => 'required', 'wa_ortu' => 'required', 'nisn' => 'required', 'dob' => 'required'
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
        return response()->json(['status' => 'success', 'message' => 'Profil diperbarui']);
    }

    /**
     * 6. JOIN CLASS
     */
    public function joinClass(Request $request): JsonResponse {
        $request->validate(['class_id' => 'required', 'payment_proof' => 'required|image']);
        
        try {
            $path = $request->file('payment_proof')->store('proofs', 'public');
            Enrollment::create([
                'user_id' => Auth::id(), 
                'class_id' => $request->class_id, 
                'payment_proof' => $path, 
                'status' => 'pending'
            ]);
            return response()->json(['status' => 'success', 'message' => 'Pembayaran sedang dikonfirmasi.']);
        } catch (\Exception $e) { return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500); }
    }

    /**
     * 7. SUBMIT TRYOUT
     */
    public function submitTryout(Request $request): JsonResponse {
        $userAnswers = $request->input('answers');
        $correctCount = 0;
        $questions = Question::where('tryout_id', $request->tryout_id)->get();

        foreach ($questions as $q) {
            if (isset($userAnswers[$q->question_id]) && $userAnswers[$q->question_id] == $q->correct_answer) { 
                $correctCount++; 
            }
        }

        $score = count($questions) > 0 ? ($correctCount / count($questions)) * 100 : 0;
        $result = TryoutResult::create([
            'user_id' => Auth::id(), 
            'tryout_id' => $request->tryout_id, 
            'score' => (int)$score, 
            'total_correct' => $correctCount
        ]);
        
        return response()->json(['status' => 'success', 'score' => $score]);
    }

    /**
     * 8. LOGOUT
     */
    public function logout(Request $request): JsonResponse {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Berhasil Logout']);
    }

    /**
     * 9. CEK PROMO
     */
    public function checkPromo(Request $request): JsonResponse {
        $class = ClassModel::find($request->class_id);
        if (!$class) return response()->json(['status' => 'error', 'message' => 'Kelas tidak ditemukan'], 404);

        $promo = \App\Models\Promotion::where('code', strtoupper($request->code))
                    ->where('class_id', $request->class_id)
                    ->where('is_active', true)
                    ->whereDate('start_date', '<=', now())
                    ->whereDate('end_date', '>=', now())
                    ->first();

        if (!$promo) return response()->json(['status' => 'error', 'message' => 'Promo tidak valid'], 404);
        
        $potongan = $class->price * ($promo->discount_percent / 100);
        return response()->json([
            'status' => 'success', 
            'discount' => $potongan, 
            'new_price' => $class->price - $potongan
        ]);
    }

    /**
     * 10. GET SISWA SCHEDULE
     */
    public function getSiswaSchedule(Request $request): JsonResponse {
        $user = Auth::user();
        $classIds = Enrollment::where('user_id', $user->usersID)->where('status', 'active')->pluck('class_id');

        $schedules = Schedule::whereIn('class_id', $classIds)
                    ->with(['class', 'material']) 
                    ->orderBy('date', 'asc')
                    ->get();

        return response()->json([
            'status' => 'success',
            'data' => $schedules
        ]);
    }

    /**
     * 🔥 11. FORGOT PASSWORD (REQUEST OTP)
     * Tambahkan fungsi ini agar error "Undefined Method" hilang
     */
    public function forgotPassword(Request $request): JsonResponse {
        $v = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($v->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Email tidak terdaftar di sistem kami.'], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();
            $otp = rand(100000, 999999);

            // Simpan OTP ke tabel OtpCode (Sama seperti saat daftar)
            OtpCode::updateOrCreate(['user_id' => $user->usersID], [
                'otp' => $otp,
                'valid_until' => Carbon::now()->addMinutes(15)
            ]);

            // Kirim Email
            Mail::to($user->email)->send(new OtpMail($otp));

            return response()->json([
                'status' => 'success',
                'message' => 'Kode OTP berhasil dikirim ke email Anda.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengirim email: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 🔥 12. RESET PASSWORD (VERIFIKASI OTP & UPDATE PASSWORD)
     */
    public function resetPassword(Request $request): JsonResponse {
        $v = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($v->fails()) {
            return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Cek OTP
        $otpRecord = OtpCode::where('user_id', $user->usersID)
                            ->where('otp', $request->otp)
                            ->where('valid_until', '>', now())
                            ->first();

        if (!$otpRecord) {
            return response()->json(['status' => 'error', 'message' => 'Kode OTP salah atau sudah kadaluarsa.'], 401);
        }

        // Update Password
        $user->update(['password' => bcrypt($request->password)]);

        // Hapus OTP setelah sukses
        $otpRecord->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil diubah. Silakan login kembali.'
        ]);
    }
}