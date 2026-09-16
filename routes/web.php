<?php

use App\Http\Controllers\AssuntoController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\RelatorioController;
use Illuminate\Support\Facades\Route;

Route::get("/", function (){
    return view('index');
});

Route::pattern("autor", "[0-9]{1,10}");
Route::pattern("assunto", "[0-9]{1,10}");
Route::pattern("livro", "[0-9]{1,10}");

Route::resource("autor", AutorController::class)->only(["index", "store", "update", "destroy"]);
Route::resource("assunto", AssuntoController::class)->only(["index", "store", "update", "destroy"]);
Route::resource("livro", LivroController::class)->only(["index", "store", "update", "destroy"]);

Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
Route::get('/relatorios/exportar', [RelatorioController::class, 'exportarPdf'])->name('relatorios.exportar');
