<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $codAs
 * @property string $Descricao
 */
class Assunto extends Model
{
    protected $table = "Assunto";

    protected $primaryKey = "codAs";

    public $timestamps = false;

    protected $fillable = ["Descricao"];

    /** @return BelongsToMany<Livro, $this> */
    public function livros(): BelongsToMany
    {
        return $this->belongsToMany(Livro::class, "Livro_Assunto", "Assunto_codAs", "Livro_Codl");
    }
}
