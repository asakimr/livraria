<?php

use App\Http\Controllers\AutorController;
use App\Http\Controllers\AssuntoController;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\RelatorioController;
use Illuminate\Support\Facades\Route;

Route::get("/", function(){
    return view('index');
});

Route::resource("autor", AutorController::class);
Route::resource("assunto", AssuntoController::class);
Route::resource("livro", LivroController::class);

Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
Route::get('/relatorios/exportar', [RelatorioController::class, 'exportarPdf'])->name('relatorios.exportar');

require __DIR__.'/settings.php';
