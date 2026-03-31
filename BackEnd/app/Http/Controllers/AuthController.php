<?php

namespace App\Http\Controllers;

// Import semua Model yang dibutuhkan sesuai ERD
use App\Models\{User, Student, OtpCode, Enrollment, Material, Schedule, Tryout, Question, TryoutResult};
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
            Student::create(['user_id' => $user->usersID, 'school' => '-', 'grade' => '12 IPA', 'dob' => null, 'wa_ortu' => '-', 'parent_name' => '-']);
            $otp = rand(100000, 999999);
            OtpCode::updateOrCreate(['user_id' => $user->usersID], ['otp' => $otp, 'valid_until' => Carbon::now()->addMinutes(10)]);
            DB::commit();
            return response()->json(['status' => 'success', 'otp' => $otp, 'name' => $user->name], 201);
        } catch (\Exception $e) { DB::rollBack(); return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500); }
    }

    // 2. VERIFIKASI OTP REGISTRASI
    public function verifyRegistration(Request $request): JsonResponse {
        $user = User::where('name', trim($request->name))->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        $otpRecord = OtpCode::where('user_id', $user->usersID)->where('otp', $request->otp)->where('valid_until', '>', now())->first();
        if (!$otpRecord) return response()->json(['status' => 'error', 'message' => 'Kode OTP Salah/Kadaluarsa'], 401);
        $user->is_verified = true; $user->save();
        $otpRecord->delete();
        return response()->json(['status' => 'success', 'message' => 'Akun Aktif!']);
    }

    // 3. LOGIN SISWA
    public function login(Request $request): JsonResponse {
        $user = User::where('name', trim($request->name))->first();
        if (!$user || !Hash::check($request->password, $user->password)) return response()->json(['status' => 'error', 'message' => 'Nama/Password Salah'], 401);
        if (!$user->is_verified) return response()->json(['status' => 'error', 'message' => 'Akun belum verifikasi WA!'], 403);
        return response()->json(['status' => 'success', 'token' => $user->createToken('token')->plainTextToken, 'user' => $user->load('student')]);
    }

    /**
 * FUNGSI LUPA PASSWORD - STEP 1 (Kirim OTP)
 */
public function forgotPassword(Request $request): JsonResponse {
    $request->validate([
        'phone' => 'required'
    ]);

    // 1. Cari user berdasarkan nomor telepon (Sesuai ERD: kolom phone)
    $user = User::where('phone', $request->phone)->first();

    if (!$user) {
        return response()->json(['status' => 'error', 'message' => 'Nomor WhatsApp tidak terdaftar!'], 404);
    }

    // 2. Generate OTP 6 Digit
    $otp = rand(100000, 999999);

    // 3. Simpan/Update ke tabel otp_codes
    OtpCode::updateOrCreate(
        ['user_id' => $user->usersID],
        [
            'otp' => $otp,
            'valid_until' => Carbon::now()->addMinutes(10)
        ]
    );

    // 4. SIMULASI KIRIM WA (Sesuai kesepakatan: Matikan Fonnte sementara)
    /*
    Http::withHeaders(['Authorization' => env('FONNTE_TOKEN')])->post('https://api.fonnte.com/send', [
        'target' => $request->phone,
        'message' => "KODE RESET PASSWORD SPEKTA ANDA: $otp. Jangan berikan kode ini kepada siapapun.",
    ]);
    */

    return response()->json([
        'status' => 'success',
        'message' => 'Kode OTP Reset Password berhasil dikirim',
        'otp' => $otp // Tampilkan untuk simulasi testing
    ]);
}

/**
 * FUNGSI LUPA PASSWORD - STEP 2 (Update Password Baru)
 */
public function resetPassword(Request $request): JsonResponse {
    $v = Validator::make($request->all(), [
        'phone' => 'required',
        'otp' => 'required|numeric',
        'password' => [
            'required', 'confirmed', 'min:8',
            'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'
        ],
    ], [
        'password.regex' => 'Password baru wajib ada Kapital, Huruf Biasa, Angka, dan Simbol!'
    ]);

    if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

    // 1. Cari User
    $user = User::where('phone', $request->phone)->first();
    if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);

    // 2. Validasi OTP
    $otpRecord = OtpCode::where('user_id', $user->usersID)
                        ->where('otp', $request->otp)
                        ->where('valid_until', '>', now())
                        ->first();

    if (!$otpRecord) {
        return response()->json(['status' => 'error', 'message' => 'Kode OTP Salah atau Kadaluarsa'], 401);
    }

    // 3. UPDATE PASSWORD (Bcrypt Enkripsi - Syarat Keamanan)
    $user->update([
        'password' => bcrypt($request->password)
    ]);

    // 4. Hapus OTP setelah sukses
    $otpRecord->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Password berhasil diperbarui! Silakan login kembali.'
    ], 200);
}

    // 4. LENGKAPI PROFIL
    public function updateProfile(Request $request): JsonResponse {
        $v = Validator::make($request->all(), ['parent_name' => 'required|string', 'alamat' => 'required|string', 'wa_ortu' => 'required', 'nisn' => 'required', 'dob' => 'required|date']);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->student->update(['parent_name' => $request->parent_name, 'school' => $request->alamat, 'wa_ortu' => $request->wa_ortu, 'nisn' => $request->nisn, 'dob' => $request->dob]);
        return response()->json(['status' => 'success', 'message' => 'Profil berhasil dilengkapi']);
    }

    // 5. DAFTAR KELAS
    public function joinClass(Request $request): JsonResponse {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $path = $request->file('payment_proof')->store('proofs', 'public');
        Enrollment::create(['user_id' => $user->usersID, 'class_id' => $request->class_id, 'payment_proof' => $path, 'status' => 'pending']);
        return response()->json(['status' => 'success', 'message' => 'Pendaftaran terkirim!']);
    }

    // 6. AMBIL KONTEN MATERI & TRYOUT
    public function getClassContent(Request $request): JsonResponse {
        try {
            $classId = $request->class_id;
            $materi = Material::where('class_id', $classId)->get();
            $tryouts = Tryout::where('class_id', $classId)->get();
            $enroll = Enrollment::where('user_id', Auth::id())->where('class_id', $classId)->first();
            return response()->json(['status' => 'success', 'enroll_status' => $enroll ? $enroll->status : 'none', 'price' => '900.000', 'duration' => '30 Hari', 'materi' => $materi, 'tryouts' => $tryouts], 200);
        } catch (\Exception $e) { return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500); }
    }

    // 7. JADWAL
    public function getSiswaSchedule(Request $request): JsonResponse {
        $user = Auth::user();
        $activeClassIds = Enrollment::where('user_id', $user->usersID)->where('status', 'aktif')->pluck('class_id');
        $schedules = Schedule::whereIn('class_id', $activeClassIds)->with(['teacher', 'classModel'])->get();
        return response()->json(['status' => 'success', 'data' => $schedules]);
    }

    // 8. AMBIL DAFTAR SOAL
    public function getQuestions(Request $request): JsonResponse {
        $questions = Question::where('tryout_id', $request->tryout_id)->get();
        if ($questions->isEmpty()) return response()->json(['status' => 'error', 'message' => 'Soal belum tersedia'], 404);
        return response()->json(['status' => 'success', 'data' => $questions], 200);
    }

    // 9. SUBMIT TRYOUT & HITUNG NILAI (LOGIKA TETAP DI DALAM CLASS)
    public function submitTryout(Request $request): JsonResponse {
        try {
            $userAnswers = $request->input('answers');
            $correctCount = 0;
            $questions = Question::where('tryout_id', $request->tryout_id)->get();
            foreach ($questions as $q) {
                if (isset($userAnswers[$q->questionsID]) && $userAnswers[$q->questionsID] == $q->correct_answer) { $correctCount++; }
            }
            $score = count($questions) > 0 ? ($correctCount / count($questions)) * 100 : 0;
            $result = TryoutResult::create(['user_id' => Auth::id(), 'tryout_id' => $request->tryout_id, 'score' => (int)$score, 'total_correct' => $correctCount]);
            return response()->json(['status' => 'success', 'score' => $score, 'resultID' => $result->resultsID, 'correct' => $correctCount], 200);
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

} // <--- KURUNG TUTUP CLASS HARUS DI SINI
