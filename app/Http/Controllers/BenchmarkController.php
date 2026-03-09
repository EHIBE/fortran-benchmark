<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class BenchmarkController extends Controller
{
    public function run(): JsonResponse
    {
        $fortranPath = base_path('app/Engines/fort_bench.exe');
        $pythonPath = base_path('app/Engines/py_bench.py');
        
        // guard against missing engine files
        if (!file_exists($fortranPath)) {
            return response()->json(['error' => 'fortran binary missing'], 500);
        }
        
        if (!file_exists($pythonPath)) {
            return response()->json(['error' => 'python script missing'], 500);
        }

        // execute external scripts
        $fortranTime = (float) shell_exec($fortranPath);
        $pythonTime = (float) shell_exec('python ' . escapeshellarg($pythonPath));

        // execute native php benchmark
        $phpStart = microtime(true);
        $res = 0.0;
        
        for ($i = 1; $i <= 50000000; $i++) {
            $res += sqrt($i);
        }
        
        $phpTime = microtime(true) - $phpStart;

        return response()->json([
            'fortran' => round($fortranTime, 4),
            'php' => round($phpTime, 4),
            'python' => round($pythonTime, 4)
        ]);
    }
}