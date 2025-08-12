<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\EventoController;


Route::post('/auth', [RegistroController::class, 'manejar']);

Route::middleware('auth:api')->match(['get', 'post'], '/publicaciones', [PublicacionController::class, 'manejar']);

Route::middleware('auth:api')->post('/refresh', [RegistroController::class, 'refreshToken']);

Route::middleware('auth:api')->match(['post', 'get'], '/eventos', [EventoController::class, 'manejar']);


Route::middleware('auth:api')->get('/debug-user', function () {
    return response()->json([
        'usuario_autenticado' => auth()->user(),
        'usuario_id' => optional(auth()->user())->usuario_id
    ]);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
