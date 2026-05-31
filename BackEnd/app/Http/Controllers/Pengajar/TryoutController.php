<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\TeacherAssignment;
use App\Models\ClassModel;
use App\Models\TryoutDraft; // Model penampung soal
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TryoutController extends Controller
{
    /**
     * 1. DAFTAR PENUGASAN SOAL
     */
    public function index()
    {
        // Ambil kelas & mapel yang ditugaskan ke guru ini
        $assignments = TeacherAssignment::with('classModel')
            ->where('user_id', Auth::user()->usersID)
            ->get();
        
        return view('pengajar.tryout.index', compact('assignments'));
    }
    
    /**
     * 2. FORM INPUT SOAL (MANUAL & CSV)
     */
    public function create($class_id, $subject_name)
    {
        $classModel = ClassModel::findOrFail($class_id);

        // Ambil draf soal yang sudah pernah dikirim guru ini untuk mapel tersebut
        $existingSoal = TryoutDraft::where('class_id', $class_id)
            ->where('subject_name', $subject_name)
            ->where('user_id', Auth::user()->usersID)
            ->latest()
            ->get();
        
        return view('pengajar.tryout.create', [
            'classId'      => $class_id,
            'classModel'   => $classModel,
            'subjectName'  => $subject_name,
            'existingSoal' => $existingSoal
        ]);
    }
    
    /**
     * 3. SIMPAN SOAL SATUAN (MANUAL)
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id'       => 'required',
            'subject_name'   => 'required',
            'question'       => 'required',
            'option_a'       => 'required',
            'option_b'       => 'required',
            'option_c'       => 'required',
            'option_d'       => 'required',
            'option_e'       => 'required',
            'correct_answer' => 'required|in:A,B,C,D,E',
        ]);

        try {
            TryoutDraft::create([
                'class_id'       => $request->class_id,
                'user_id'        => Auth::user()->usersID,
                'subject_name'   => $request->subject_name,
                'question'       => $request->question,
                'option_a'       => $request->option_a,
                'option_b'       => $request->option_b,
                'option_c'       => $request->option_c,
                'option_d'       => $request->option_d,
                'option_e'       => $request->option_e,
                'correct_answer' => $request->correct_answer,
                'explanation'    => $request->explanation,
            ]);

            return back()->with('success', 'Soal satuan berhasil disetor ke Admin.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * 4. IMPORT SOAL MASSAL (VIA CSV)
     */
    public function importCSV(Request $request)
    {
        $request->validate([
            'file_csv'     => 'required|mimes:csv,txt',
            'class_id'     => 'required',
            'subject_name' => 'required'
        ]);

        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), "r");

        // Lewati baris header CSV
        fgetcsv($handle, 1000, ",");

        $count = 0;
        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (empty($row[0])) continue; // Lewati baris kosong

                TryoutDraft::create([
                    'class_id'       => $request->class_id,
                    'user_id'        => Auth::user()->usersID,
                    'subject_name'   => $request->subject_name,
                    'question'       => $row[0],
                    'option_a'       => $row[1],
                    'option_b'       => $row[2],
                    'option_c'       => $row[3],
                    'option_d'       => $row[4],
                    'option_e'       => $row[5],
                    'correct_answer' => strtoupper($row[6]), // A-E
                    'explanation'    => $row[7] ?? null,
                ]);
                $count++;
            }
            fclose($handle);
            DB::commit();

            return back()->with('success', "Berhasil mengimpor $count soal ke draf.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Format CSV salah. Mohon ikuti template yang tersedia.');
        }
    }

    /**
     * 5. HAPUS DRAF (Sebelum dipublish Admin)
     */
    public function destroy($id)
    {
        $draft = TryoutDraft::where('id', $id)
            ->where('user_id', Auth::user()->usersID)
            ->firstOrFail();
            
        $draft->delete();
        return back()->with('success', 'Soal berhasil ditarik/dihapus.');
    }
}