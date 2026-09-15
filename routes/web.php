<?php

use App\Http\Controllers\AutorController;
use App\Http\Controllers\AssuntoController;
use App\Http\Controllers\LivroController;
use Illuminate\Support\Facades\Route;

Route::get("/", function(){
    return view('index');
});

Route::resource("autor", AutorController::class);
Route::resource("assunto", AssuntoController::class);
Route::resource("livro", LivroController::class);

require __DIR__.'/settings.php';
