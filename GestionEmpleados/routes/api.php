<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\AuthController;
use App\Http\Controllers\MarcacionController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/marcaciones', [MarcacionController::class, 'almacenar']);
Route::get('/marcaciones/{empleado_id}', [MarcacionController::class, 'historial']);
// Rutas protegidas
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);


});

Route::get('/test', function () { return response()->json(['status' => 'ok']); });