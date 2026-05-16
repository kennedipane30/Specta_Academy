<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Material;
use Illuminate\Support\Facades\Http;

class SyncMaterialsToGo extends Command
{
    protected $signature = 'sync:materi';
    protected $description = 'Kirim semua data materi dari Laravel ke Microservice Go';

    public function handle()
    {
        $materials = Material::all();
        $this->info("Mengirim " . $materials->count() . " data...");

        foreach ($materials as $m) {
            Http::post(env('GO_MATERI_URL') . '/api/materials/sync', [
                'material_id'   => $m->material_id,
                'class_id'      => (int)$m->class_id,
                'user_id'       => (int)($m->user_id ?? 4),
                'title'         => $m->title,
                'material_name' => $m->material_name,
                'week'          => (int)$m->week,
                'file_path'     => $m->file_path,
            ]);
        }

        $this->info("Sinkronisasi Selesai!");
    }
}
