<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NBodyController extends Controller
{
    public function updateFortran(Request $request): JsonResponse
    {
        $particles = $request->input('particles');
        $enginePath = base_path('app/Engines/nbody_sim.exe');

        if (!file_exists($enginePath)) {
            return response()->json(['error' => 'Engine missing'], 500);
        }

        $inFile = storage_path('app/nbody_in.dat');
        $outFile = storage_path('app/nbody_out.dat');

        if (file_exists($outFile)) {
            unlink($outFile);
        }

        $data = "";
        foreach ($particles as $p) {
            $data .= sprintf("%.8f %.8f %.8f %.8f %.8f\n", $p['x'], $p['y'], $p['vx'], $p['vy'], $p['m']);
        }
        file_put_contents($inFile, $data);

        $currentDir = getcwd();
        chdir(storage_path('app'));
        shell_exec("\"$enginePath\" nbody_in.dat nbody_out.dat");
        chdir($currentDir);

        if (!file_exists($outFile)) {
            return response()->json(['error' => 'Calculation failed'], 500);
        }

        $results = file($outFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $updatedParticles = array_map(function($line) {
            $parts = preg_split('/\s+/', trim($line));
            return [
                'x' => (float)$parts[0], 
                'y' => (float)$parts[1],
                'vx' => (float)$parts[2], 
                'vy' => (float)$parts[3],
                'm' => (float)$parts[4]
            ];
        }, $results);

        return response()->json(['particles' => $updatedParticles]);
    }

    public function updatePhp(Request $request): JsonResponse
    {
        $particles = $request->input('particles');
        $n = count($particles);
        $dt = 0.01;
        $G = 1.0;
        $cX = 300.0;
        $cY = 300.0;
        $cM = 500000.0;

        for ($i = 0; $i < $n; $i++) {
            $dxC = $cX - $particles[$i]['x'];
            $dyC = $cY - $particles[$i]['y'];
            $distC = sqrt($dxC**2 + $dyC**2) + 20.0;
            $forceC = ($G * $particles[$i]['m'] * $cM) / ($distC**2);
            $particles[$i]['vx'] += ($forceC * $dxC / $distC) / $particles[$i]['m'] * $dt;
            $particles[$i]['vy'] += ($forceC * $dyC / $distC) / $particles[$i]['m'] * $dt;

            for ($j = 0; $j < $n; $j++) {
                if ($i === $j) continue;
                $dx = $particles[$j]['x'] - $particles[$i]['x'];
                $dy = $particles[$j]['y'] - $particles[$i]['y'];
                $dist = sqrt($dx**2 + $dy**2) + 10.0;
                $force = ($G * $particles[$i]['m'] * $particles[$j]['m']) / ($dist**2);
                $particles[$i]['vx'] += ($force * $dx / $dist) / $particles[$i]['m'] * $dt;
                $particles[$i]['vy'] += ($force * $dy / $dist) / $particles[$i]['m'] * $dt;
            }
        }

        for ($i = 0; $i < $n; $i++) {
            $particles[$i]['x'] += $particles[$i]['vx'] * $dt;
            $particles[$i]['y'] += $particles[$i]['vy'] * $dt;
        }

        return response()->json(['particles' => $particles]);
    }
}
