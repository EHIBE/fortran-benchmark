<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StratoMeshController extends Controller
{
    public function simulatePhp(Request $request): JsonResponse
    {
        $grid = $request->input('grid', []);
        $n = 100;
        $iters = 500;
        $diff = 0.2;

        for ($iter = 0; $iter < $iters; $iter++) {
            $next = $grid;
            for ($y = 1; $y < $n - 1; $y++) {
                for ($x = 1; $x < $n - 1; $x++) {
                    $idx = $y * $n + $x;
                    $val = $grid[$idx] + $diff * (
                        $grid[$idx - 1] + $grid[$idx + 1] +
                        $grid[$idx - $n] + $grid[$idx + $n] -
                        4 * $grid[$idx]
                    );
                    $next[$idx] = $val;
                }
            }
            $grid = $next;
        }

        return response()->json(['grid' => $grid]);
    }

    public function simulateFortran(Request $request): JsonResponse
    {
        $grid = $request->input('grid', []);
        
        $inputData = implode(" ", $grid) . "\n";
        
        $inputFile = storage_path('app/strato_in_' . uniqid() . '.txt');
        file_put_contents($inputFile, $inputData);

        $binaryPath = base_path('app/Engines/stratomesh.exe');
        $command = '"' . $binaryPath . '" < "' . $inputFile . '"';
        $output = shell_exec($command);

        unlink($inputFile);

        if (! $output) {
            return response()->json(['grid' => $grid]);
        }

        $vals = preg_split('/\s+/', trim($output));
        
        foreach ($vals as $i => $val) {
            if ($val !== '' && isset($grid[$i])) {
                $grid[$i] = (float) $val;
            }
        }

        return response()->json(['grid' => $grid]);
    }
}