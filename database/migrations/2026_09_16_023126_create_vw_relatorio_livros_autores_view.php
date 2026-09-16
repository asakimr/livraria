<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        #Resolve problema de comaptibilidade do Fresh/Refresh com SQLite e MySQL;
        DB::statement("DROP VIEW IF EXISTS vw_relatorio_livros_autores");

        DB::statement("
                    CREATE VIEW vw_relatorio_livros_autores AS
                    SELECT
                        a.CodAu,
                        a.Nome AS Autor_Nome,
                        l.Codl,
                        l.Titulo AS Livro_Titulo,
                        l.Editora AS Livro_Editora,
                        l.Edicao AS Livro_Edicao,
                        l.AnoPublicacao AS Livro_Ano,
                        GROUP_CONCAT(ass.Descricao) AS Assuntos
                    FROM Autor a
                    JOIN Livro_Autor la ON a.CodAu = la.Autor_CodAu
                    JOIN Livro l ON la.Livro_Codl = l.Codl
                    LEFT JOIN Livro_Assunto las ON l.Codl = las.Livro_Codl
                    LEFT JOIN Assunto ass ON las.Assunto_codAs = ass.codAs
                    GROUP BY a.CodAu, a.Nome, l.Codl, l.Titulo, l.Editora, l.Edicao, l.AnoPublicacao
                ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vw_relatorio_livros_autores');
    }
};
