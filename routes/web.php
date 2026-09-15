<?php

use App\Http\Controllers\AutorController;
use Illuminate\Support\Facades\Route;

Route::get("/", function(){
    return redirect()->route("autor.index");
});

Route::resource("autor", AutorController::class);

require __DIR__.'/settings.php';
