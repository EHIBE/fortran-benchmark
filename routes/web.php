<?php

use App\Http\Controllers\AeroGridController;
use App\Http\Controllers\BenchmarkController;
use App\Http\Controllers\HeatSimulationController;
use App\Http\Controllers\NBodyController;
use App\Http\Controllers\StratoMeshController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('home');

Route::inertia('/benchmark', 'Benchmark')->name('benchmark');
Route::inertia('/heat-simulator', 'ThermalForge')->name('heat.simulator');
Route::inertia('/galaxy-engine', 'GalaxyEngine')->name('galaxy.engine');
Route::inertia('/strato-mesh', 'StratoMesh')->name('strato.mesh');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::get('/api/benchmark', [BenchmarkController::class, 'run']);

Route::post('/api/simulate-heat', [HeatSimulationController::class, 'simulate'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::post('/api/simulate-fortran', [NBodyController::class, 'updateFortran'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::post('/api/simulate-php', [NBodyController::class, 'updatePhp'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::post('/api/strato-simulate-php', [StratoMeshController::class, 'simulatePhp'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::post('/api/strato-simulate-fortran', [StratoMeshController::class, 'simulateFortran'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

require __DIR__.'/settings.php';