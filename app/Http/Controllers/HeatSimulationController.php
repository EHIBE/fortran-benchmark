<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HeatSimulationController extends Controller
{
    public function simulate(Request $request): JsonResponse
    {
        $grid = $request->input('grid');

        if (!$grid || !is_array($grid)) {
            return response()->json(['error' => 'Invalid data'], 400);
        }

        $enginePath = base_path('app/Engines/heat_sim.exe');

        if (!file_exists($enginePath)) {
            return response()->json(['error' => 'Binary missing'], 500);
        }

        $inFile = storage_path('app/heat_in.dat');
        $outFile = storage_path('app/heat_out.dat');

        if (file_exists($outFile)) {
            unlink($outFile);
        }

        $inputText = '';
        foreach ($grid as $row) {
            $inputText .= implode(' ', array_map(fn($val) => sprintf("%.6f", $val), $row)) . "\n";
        }

        file_put_contents($inFile, $inputText);

        $currentDir = getcwd();
        chdir(storage_path('app'));
        shell_exec("\"$enginePath\" heat_in.dat heat_out.dat");
        chdir($currentDir);

        if (!file_exists($outFile)) {
            return response()->json(['error' => 'Simulation failed'], 500);
        }

        $outputText = file_get_contents($outFile);
        $lines = explode("\n", trim($outputText));
        $resultGrid = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            $parsed = preg_split('/\s+/', $line);
            if (count($parsed) >= 50) {
                $resultGrid[] = array_map('floatval', array_slice($parsed, 0, 50));
            }
        }

        return response()->json(['grid' => $resultGrid]);
    }
}
