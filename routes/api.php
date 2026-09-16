<?php

use App\Http\Controllers\Api\AssuntoController;
use App\Http\Controllers\Api\AutorController;
use App\Http\Controllers\Api\LivroController;
use Illuminate\Support\Facades\Route;

Route::pattern('id', '[0-9]{1,10}');

Route::apiResource('autores', AutorController::class)->parameters(['autores' => 'id']);
Route::apiResource('assuntos', AssuntoController::class)->parameters(['assuntos' => 'id']);
Route::apiResource('livros', LivroController::class)->parameters(['livros' => 'id']);
