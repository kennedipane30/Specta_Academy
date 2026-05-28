<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

class PdfStreamController extends Controller
{
    /**
     * Stream PDF dengan chunking untuk menghindari connection closed
     */
    public function stream($filename)
    {
        $path = storage_path('app/public/materi/' . $filename);
        
        if (!file_exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found',
                'path' => $path
            ], 404);
        }
        
        $fileSize = filesize($path);
        
        // Set headers untuk streaming
        header('Content-Type: application/pdf');
        header('Content-Length: ' . $fileSize);
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Cache-Control: public, max-age=3600');
        header('Accept-Ranges: bytes');
        
        // Buka file dan stream dengan chunk
        $handle = fopen($path, 'rb');
        
        if ($handle === false) {
            return response()->json(['error' => 'Cannot open file'], 500);
        }
        
        // Stream per chunk (8KB per chunk)
        while (!feof($handle)) {
            echo fread($handle, 8192);
            ob_flush();
            flush();
        }
        
        fclose($handle);
        exit;
    }
    
    /**
     * Download dengan response Laravel
     */
    public function download($filename)
    {
        $path = storage_path('app/public/materi/' . $filename);
        
        if (!file_exists($path)) {
            return response()->json(['error' => 'File not found'], 404);
        }
        
        return response()->download($path, $filename, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'public, max-age=3600',
            'Content-Length' => filesize($path)
        ]);
    }
    
    /**
     * Direct file access dengan optimasi
     */
    public function direct($filename)
    {
        $path = storage_path('app/public/materi/' . $filename);
        
        if (!file_exists($path)) {
            abort(404);
        }
        
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }
}