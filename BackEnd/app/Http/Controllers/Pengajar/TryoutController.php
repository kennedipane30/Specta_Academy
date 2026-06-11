<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\TeacherAssignment;
use App\Models\ClassModel;
use App\Models\TryoutDraft;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TryoutController extends Controller
{
    /**
     * ✅ LANGSUNG DARI TEACHER_ASSIGNMENTS
     */
    private function getAssignmentsWithSubjects(): array
    {
        $userId = Auth::user()->usersID;

        $assignments = TeacherAssignment::with(['classModel'])
            ->where('user_id', $userId)
            ->get();

        $result = [];
        foreach ($assignments as $assignment) {
            $totalSoal = TryoutDraft::where('user_id', $userId)
                ->where('class_id', $assignment->class_id)
                ->where('subject_name', $assignment->subject_name)
                ->count();

            $result[] = (object) [
                'class_id'     => $assignment->class_id,
                'classModel'   => $assignment->classModel,
                'subject_name' => $assignment->subject_name,
                'total_soal'   => $totalSoal,
            ];
        }

        return $result;
    }

    /**
     * ✅ VALIDASI: Cek apakah pengajar memiliki penugasan
     */
    private function hasAssignment(int $classId, string $subjectName): bool
    {
        return TeacherAssignment::where('user_id', Auth::user()->usersID)
            ->where('class_id', $classId)
            ->where('subject_name', $subjectName)
            ->exists();
    }

    public function index()
    {
        $assignmentsWithSubjects = $this->getAssignmentsWithSubjects();
        return view('pengajar.tryout.index', compact('assignmentsWithSubjects'));
    }

    public function create($class_id, $subject_name)
    {
        if (!$this->hasAssignment($class_id, $subject_name)) {
            abort(403, 'Anda tidak ditugaskan untuk kelas dan mata pelajaran ini.');
        }

        $classModel = ClassModel::findOrFail($class_id);
        $userId = Auth::user()->usersID;

        $existingSoal = TryoutDraft::where('class_id', $class_id)
            ->where('user_id', $userId)
            ->where('subject_name', $subject_name)
            ->latest()
            ->get();

        return view('pengajar.tryout.create', [
            'classId'      => $class_id,
            'classModel'   => $classModel,
            'subjectName'  => trim($subject_name),
            'existingSoal' => $existingSoal
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'draft_id'       => 'nullable|exists:tryout_drafts,id',
            'class_id'       => 'required|integer',
            'subject_name'   => 'required|string',
            'question'       => 'required|string',
            'option_a'       => 'required|string',
            'option_b'       => 'required|string',
            'option_c'       => 'required|string',
            'option_d'       => 'required|string',
            'option_e'       => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D,E',
        ]);

        if (!$this->hasAssignment($request->class_id, $request->subject_name)) {
            return back()->with('error', 'Anda tidak ditugaskan untuk mata pelajaran ini.');
        }

        try {
            TryoutDraft::updateOrCreate(
                ['id' => $request->draft_id],
                [
                    'class_id'       => $request->class_id,
                    'user_id'        => Auth::user()->usersID,
                    'subject_name'   => trim($request->subject_name),
                    'question'       => $request->question,
                    'option_a'       => trim($request->option_a),
                    'option_b'       => trim($request->option_b),
                    'option_c'       => trim($request->option_c),
                    'option_d'       => trim($request->option_d),
                    'option_e'       => trim($request->option_e),
                    'correct_answer' => strtoupper($request->correct_answer),
                    'explanation'    => $request->explanation,
                ]
            );

            return back()->with('success', 'Berhasil! Soal telah disimpan ke draf.');

        } catch (\Exception $e) {
            Log::error("Store Draft Error: " . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function importCSV(Request $request)
    {
        $request->validate([
            'file_csv'     => 'required|mimes:csv,txt',
            'class_id'     => 'required|integer',
            'subject_name' => 'required|string'
        ]);

        if (!$this->hasAssignment($request->class_id, $request->subject_name)) {
            return back()->with('error', 'Anda tidak ditugaskan untuk mata pelajaran ini.');
        }

        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), "r");

        fgetcsv($handle, 2000, ";");

        $count = 0;
        $userId = Auth::user()->usersID;
        $forcedSubject = trim($request->subject_name);

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 2000, ";")) !== FALSE) {
                if (!isset($row[1]) || empty(trim($row[1]))) continue;

                TryoutDraft::create([
                    'class_id'       => (int) $request->class_id,
                    'user_id'        => (int) $userId,
                    'subject_name'   => $forcedSubject,
                    'question'       => trim($row[1]),
                    'option_a'       => trim($row[2] ?? '-'),
                    'option_b'       => trim($row[3] ?? '-'),
                    'option_c'       => trim($row[4] ?? '-'),
                    'option_d'       => trim($row[5] ?? '-'),
                    'option_e'       => trim($row[6] ?? '-'),
                    'correct_answer' => strtoupper(substr(trim($row[7] ?? 'A'), 0, 1)),
                    'explanation'    => trim($row[8] ?? ''),
                ]);
                $count++;
            }
            fclose($handle);
            DB::commit();

            return back()->with('success', "Sukses! $count soal berhasil diimport ke mata pelajaran $forcedSubject.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Import CSV Error: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $draft = TryoutDraft::where('id', $id)
                ->where('user_id', Auth::user()->usersID)
                ->first();

            if (!$draft) {
                return back()->with('error', 'Draf tidak ditemukan atau bukan milik Anda.');
            }

            $draft->delete();

            return back()->with('success', 'Soal berhasil dihapus dari draf.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus soal.');
        }
    }

    public function deleteAllDrafts(Request $request)
    {
        $request->validate([
            'class_id'     => 'required|integer',
            'subject_name' => 'required|string'
        ]);

        try {
            if (!$this->hasAssignment($request->class_id, $request->subject_name)) {
                return back()->with('error', 'Anda tidak ditugaskan untuk mata pelajaran ini.');
            }

            TryoutDraft::where('user_id', Auth::user()->usersID)
                ->where('class_id', $request->class_id)
                ->where('subject_name', $request->subject_name)
                ->delete();

            return back()->with('success', "Seluruh draf untuk mata pelajaran ini telah dihapus.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membersihkan draf.');
        }
    }
}
