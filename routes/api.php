<?php

use App\Http\Controllers\Api\V1\DevelopmentController;
use App\Http\Controllers\Api\V1\DreamController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\SymbolController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/dreams', [DreamController::class, 'index']);
    Route::post('/dreams', [DreamController::class, 'store']);
    Route::get('/dreams/{dream}', [DreamController::class, 'show']);
    Route::put('/dreams/{dream}', [DreamController::class, 'update']);
    Route::delete('/dreams/{dream}', [DreamController::class, 'destroy']);
    Route::post('/dreams/{dream}/audio', [DreamController::class, 'uploadAudio']);
    Route::post('/dreams/{dream}/transcribe', [DreamController::class, 'transcribe']);
    Route::post('/dreams/{dream}/analyze', [DreamController::class, 'analyze']);
    Route::get('/dreams/{dream}/analysis', [DreamController::class, 'analysis']);

    Route::get('/reports/weekly', [ReportController::class, 'weekly']);
    Route::get('/reports/monthly', [ReportController::class, 'monthly']);

    Route::get('/symbols', [SymbolController::class, 'index']);
    Route::get('/development', [DevelopmentController::class, 'index']);
    Route::post('/reflections', [DevelopmentController::class, 'storeReflection']);
});
