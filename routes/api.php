<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeitoController;

Route::middleware('throttle:api')->group(function () {
    Route::get('/leitos', [LeitoController::class, 'listar']);
    Route::post('/leitos/ocupar', [LeitoController::class, 'ocupar']);
    Route::post('/leitos/liberar', [LeitoController::class, 'liberar']);
    Route::post('/leitos/transferir', [LeitoController::class, 'transferir']);
    Route::get('/pacientes/{cpf}/leito', [LeitoController::class, 'buscarPorCpf']);
    Route::get('/pacientes', [LeitoController::class, 'listarPacientes']);
});
