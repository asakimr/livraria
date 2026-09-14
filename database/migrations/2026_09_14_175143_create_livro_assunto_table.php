<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('Livro_Assunto', function (Blueprint $table) {
            $table->integer("Livro_Codl")->unsigned();
            $table->integer("Assunto_codAs")->unsigned();

            $table->foreign("Livro_Codl", "Livro_Assunto_FKIndex1")->references("Codl")->on("Livro")->onDelete("cascade");
            $table->foreign("Assunto_codAs", "Livro_Assunto_FKIndex2")->references("codAs")->on("Assunto")->onDelete("cascade");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Livro_Assunto');
    }
};
