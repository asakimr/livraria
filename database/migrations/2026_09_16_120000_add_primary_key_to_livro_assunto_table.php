<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicado = DB::table('Livro_Assunto')
            ->select('Livro_Codl', 'Assunto_codAs')
            ->groupBy('Livro_Codl', 'Assunto_codAs')
            ->havingRaw('COUNT(*) > 1')
            ->first();

        if ($duplicado !== null) {
            throw new RuntimeException(
                'Não foi possível criar a chave de Livro_Assunto: existem associações duplicadas. '
                .'Nenhum registro foi removido. Faça backup e revise as duplicações com '
                .'SELECT Livro_Codl, Assunto_codAs, COUNT(*) AS quantidade FROM Livro_Assunto '
                .'GROUP BY Livro_Codl, Assunto_codAs HAVING COUNT(*) > 1; '
                .'após a correção dos dados, execute a migration novamente.'
            );
        }

        $this->alterarChave(function (Blueprint $table) {
            $table->primary(['Livro_Codl', 'Assunto_codAs']);
        });
    }

    public function down(): void
    {
        // MySQL pode remover o índice automático da FK ao aproveitar a nova PK.
        if (DB::getDriverName() === 'mysql') {
            $indiceDoLivro = collect(Schema::getIndexes('Livro_Assunto'))
                ->contains(fn (array $indice): bool => ! $indice['primary'] && $indice['columns'][0] === 'Livro_Codl');

            if (! $indiceDoLivro) {
                Schema::table('Livro_Assunto', function (Blueprint $table) {
                    $table->index('Livro_Codl', 'livro_assunto_livro_rollback_index');
                });
            }
        }

        $this->alterarChave(function (Blueprint $table) {
            $table->dropPrimary(['Livro_Codl', 'Assunto_codAs']);
        });
    }

    private function alterarChave(Closure $alteracao): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('Livro_Assunto', $alteracao);

            return;
        }

        // SQLite reconstrói a tabela; a view deve continuar apontando para o nome original.
        $modoAnterior = (int) DB::scalar('PRAGMA legacy_alter_table');
        DB::statement('PRAGMA legacy_alter_table = ON');

        try {
            Schema::table('Livro_Assunto', $alteracao);
        } finally {
            DB::statement('PRAGMA legacy_alter_table = '.$modoAnterior);
        }
    }
};
