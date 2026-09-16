<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $CodAu
 * @property string $Nome
 */
class Autor extends Model
{
    protected $table = "Autor";

    protected $primaryKey = "CodAu";

    public $timestamps = false;

    protected $fillable = [
        "Nome",
    ];

    /** @return BelongsToMany<Livro, $this> */
    public function livros(): BelongsToMany
    {
        return $this->belongsToMany(Livro::class, "Livro_Autor", "Autor_CodAu", "Livro_Codl");
    }
}
