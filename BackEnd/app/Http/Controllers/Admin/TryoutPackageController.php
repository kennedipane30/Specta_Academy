<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TryoutPackageController extends Controller
{
    /**
     * URL Go Service untuk Tryout (Port 9002)
     */
    private function goUrl(): string
    {
        return env('GO_TRYOUT_URL', 'http://127.0.0.1:9002');
    }

    /**
     * 1. DAFTAR PAKET - Ambil dari Microservice
     */
    public function index()
    {
        $classes = ClassModel::all();
        $tryouts = [];
        $serviceError = false;

        try {
            $response = Http::timeout(5)->get($this->goUrl() . '/api/tryouts');

            if ($response->successful()) {
                $data = $response->json();
                $tryoutsData = $data['data'] ?? $data ?? [];

                foreach ($tryoutsData as $item) {
                    $class = $classes->firstWhere('class_id', $item['class_id'] ?? 0);
                    $tryouts[] = (object) [
                        'id' => $item['tryout_id'] ?? 0,
                        'title' => $item['title'] ?? 'Untitled',
                        'class_id' => $item['class_id'] ?? 0,
                        'class_name' => $class ? $class->program_name : 'Kelas #' . ($item['class_id'] ?? '?'),
                        'duration' => $item['duration'] ?? $item['duration_minutes'] ?? 0,
                        'total_questions' => $item['total_questions'] ?? 0,
                        'status' => $item['status'] ?? 'draft',
                        'is_active' => $item['is_active'] ?? false,
                        'created_at' => $item['created_at'] ?? null,
                    ];
                }
            } else {
                $serviceError = true;
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $serviceError = true;
            Log::warning('Go Tryout Service tidak tersedia: ' . $e->getMessage());
        } catch (\Exception $e) {
            $serviceError = true;
            Log::error('Error mengambil tryout: ' . $e->getMessage());
        }

        return view('admin.tryout.packages', compact('tryouts', 'serviceError'));
    }

    /**
     * 2. FORM TAMBAH PAKET
     */
    public function create()
    {
        $classes = ClassModel::orderBy('program_name')->get();
        return view('admin.tryout.create_package', compact('classes'));
    }

    /**
     * 3. SIMPAN PAKET - Langsung ke Microservice
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'class_id'  => 'required|integer',
            'duration'  => 'required|integer|min:1',
            'is_active' => 'required|boolean'
        ]);

        try {
            $payload = [
                'tryout' => [
                    'class_id'  => (int) $request->class_id,
                    'title'     => trim($request->title),
                    'duration_minutes' => (int) $request->duration,
                    'total_questions' => 0,
                    'status'    => 'published',
                    'is_active' => (bool) $request->is_active,
                ],
                'questions' => []
            ];

            $response = Http::timeout(10)->post($this->goUrl() . '/api/tryouts/sync', $payload);

            if ($response->successful()) {
                return redirect()->route('admin.tryout_package.index')
                    ->with('success', 'Paket Tryout berhasil diterbitkan ke aplikasi mobile.');
            } else {
                return back()->with('error', 'Gagal menerbitkan paket: ' . $response->body());
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Connection Error: " . $e->getMessage());
            return back()->with('error', 'Server tryout sedang bermasalah. Silakan coba lagi nanti.');
        } catch (\Exception $e) {
            Log::error("Error Store Tryout: " . $e->getMessage());
            return back()->with('error', 'Gagal menerbitkan paket: ' . $e->getMessage());
        }
    }

    /**
     * 4. FORM EDIT
     */
    public function edit($id)
    {
        $classes = ClassModel::orderBy('program_name')->get();
        $tryout = null;
        $serviceError = false;

        try {
            $response = Http::timeout(5)->get($this->goUrl() . '/api/tryouts');
            if ($response->successful()) {
                $data = $response->json();
                $tryoutsData = $data['data'] ?? $data ?? [];
                foreach ($tryoutsData as $item) {
                    if (($item['tryout_id'] ?? 0) == $id) {
                        $tryout = (object) $item;
                        break;
                    }
                }
            }

            if (!$tryout) {
                return back()->with('error', 'Paket Tryout tidak ditemukan.');
            }

        } catch (\Exception $e) {
            $serviceError = true;
            Log::error('Error mengambil tryout: ' . $e->getMessage());
        }

        return view('admin.tryout.edit_package', compact('classes', 'tryout', 'serviceError'));
    }

    /**
     * 5. UPDATE PAKET
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'class_id'  => 'required|integer',
            'duration'  => 'required|integer|min:1',
            'is_active' => 'required|boolean'
        ]);

        try {
            // Untuk update, kita perlu mengambil data lama lalu hapus dan buat baru
            // Atau implementasikan endpoint PUT di microservice
            // Sementara, kita sync ulang dengan ID yang sama
            $payload = [
                'tryout' => [
                    'tryout_id' => (int) $id,
                    'class_id'  => (int) $request->class_id,
                    'title'     => trim($request->title),
                    'duration_minutes' => (int) $request->duration,
                    'total_questions' => 0,
                    'status'    => 'published',
                    'is_active' => (bool) $request->is_active,
                ],
                'questions' => []
            ];

            $response = Http::timeout(10)->post($this->goUrl() . '/api/tryouts/sync', $payload);

            if ($response->successful()) {
                return redirect()->route('admin.tryout_package.index')
                    ->with('success', 'Paket Tryout berhasil diperbarui.');
            } else {
                return back()->with('error', 'Gagal memperbarui paket.');
            }

        } catch (\Exception $e) {
            Log::error("Error Update Tryout: " . $e->getMessage());
            return back()->with('error', 'Gagal update paket: ' . $e->getMessage());
        }
    }

    /**
     * 6. HAPUS PAKET
     */
    public function destroy($id)
    {
        try {
            $response = Http::timeout(10)->delete($this->goUrl() . '/api/tryouts/' . $id);

            if ($response->successful()) {
                return redirect()->route('admin.tryout_package.index')
                    ->with('success', 'Paket Tryout berhasil dihapus dari sistem.');
            } else {
                return back()->with('error', 'Gagal menghapus paket.');
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Delete Connection Error: " . $e->getMessage());
            return back()->with('error', 'Server tryout sedang bermasalah. Silakan coba lagi nanti.');
        } catch (\Exception $e) {
            Log::error("Error Delete Tryout: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * 7. SYNC ULANG (Refresh data dari microservice)
     */
    public function refresh()
    {
        return redirect()->route('admin.tryout_package.index')
            ->with('success', 'Data berhasil direfresh dari server.');
    }

    /**
     * 8. CEK STATUS KONEKSI KE GO SERVICE
     */
    public function checkConnection()
    {
        try {
            $response = Http::timeout(3)->get($this->goUrl());
            if ($response->successful()) {
                return response()->json([
                    'status' => 'connected',
                    'message' => 'Tryout service berjalan normal'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'disconnected',
                'message' => 'Tryout service tidak tersedia: ' . $e->getMessage()
            ], 503);
        }
    }
}
