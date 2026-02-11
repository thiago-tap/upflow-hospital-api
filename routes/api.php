<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeitoController;

// --- SUAS ROTAS AQUI ---
Route::get('/leitos', [LeitoController::class, 'listar']);
Route::post('/leitos/ocupar', [LeitoController::class, 'ocupar']);
Route::post('/leitos/liberar', [LeitoController::class, 'liberar']);
Route::post('/leitos/transferir', [LeitoController::class, 'transferir']);
Route::get('/pacientes/{cpf}/leito', [LeitoController::class, 'buscarPorCpf']);