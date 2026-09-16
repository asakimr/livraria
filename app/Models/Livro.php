<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $Codl
 * @property string $Titulo
 * @property string $Editora
 * @property int $Edicao
 * @property int $AnoPublicacao
 * @property string $Valor
 */
class Livro extends Model
{
    protected $table = "Livro";

    protected $primaryKey = "Codl";

    public $timestamps = false;

    /** @var array<string, string> */
    protected $casts = ["Valor" => "decimal:2", "Edicao" => "integer", "AnoPublicacao" => "integer"];

    protected $fillable = ["Titulo", "Editora", "Edicao", "AnoPublicacao", "Valor"];

    /** @return BelongsToMany<Assunto, $this> */
    public function assuntos(): BelongsToMany{
        return $this->belongsToMany(Assunto::class, "Livro_Assunto", "Livro_Codl", "Assunto_codAs");
    }

    /** @return BelongsToMany<Autor, $this> */
    public function autores(): BelongsToMany{
        return $this->belongsToMany(Autor::class, "Livro_Autor", "Livro_Codl", "Autor_CodAu");
    }
}
