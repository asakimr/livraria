<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assunto extends Model
{
    protected $table = "Assunto";

    protected $primaryKey = "codAs";

    public $timestamps = false;

    protected $fillable = ["Descricao"];
}
