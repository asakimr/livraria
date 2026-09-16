<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Assunto;
use App\Models\Autor;


class Livro extends Model
{

    protected $table = "Livro";

    protected $primaryKey = "Codl";

    public $timestamps = false;

    protected $fillable = ["Titulo", "Editora", "Edicao", "AnoPublicacao", "Valor"];

    //N:N
    public function assuntos(){
        return $this->belongsToMany(Assunto::class, "Livro_Assunto", "Livro_Codl", "Assunto_codAs");
    }

    //N:N
    public function autores(){
        return $this->belongsToMany(Autor::class, "Livro_Autor", "Livro_Codl", "Autor_CodAu");
    }
}
