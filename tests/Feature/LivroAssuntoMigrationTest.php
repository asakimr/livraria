<?php

use App\Models\Assunto;
use App\Models\Autor;
use App\Models\Livro;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

// DDL faz commit implícito no MySQL; cada cenário precisa de migrations próprias.
uses(TestCase::class, DatabaseMigrations::class);

it('impede associações repetidas diretamente no banco', function () {
    $livro = Livro::create(['Titulo' => 'Livro', 'Editora' => 'Editora', 'Edicao' => 1, 'AnoPublicacao' => 2026, 'Valor' => '10.00']);
    $assunto = Assunto::create(['Descricao' => 'Assunto']);
    $vinculo = ['Livro_Codl' => $livro->Codl, 'Assunto_codAs' => $assunto->codAs];
    DB::table('Livro_Assunto')->insert($vinculo);

    expect(fn () => DB::table('Livro_Assunto')->insert($vinculo))->toThrow(QueryException::class);
    $this->assertDatabaseCount('Livro_Assunto', 1);
});

it('preserva associações e a view na aplicação e reversão da chave', function () {
    $migration = require database_path('migrations/2026_09_16_120000_add_primary_key_to_livro_assunto_table.php');
    $livro = Livro::create(['Titulo' => 'Livro', 'Editora' => 'Editora', 'Edicao' => 1, 'AnoPublicacao' => 2026, 'Valor' => '10.00']);
    $assunto = Assunto::create(['Descricao' => 'Assunto']);
    $vinculo = ['Livro_Codl' => $livro->Codl, 'Assunto_codAs' => $assunto->codAs];
    DB::table('Livro_Assunto')->insert($vinculo);

    $migration->down();
    $this->assertDatabaseHas('Livro_Assunto', $vinculo);
    $autor = Autor::create(['Nome' => 'Autor']);
    $livro->autores()->attach($autor->CodAu);
    expect(collect(Schema::getIndexes('Livro_Assunto'))->where('primary', true))->toHaveCount(0);
    $migration->up();
    $this->assertDatabaseHas('Livro_Assunto', $vinculo);
    $this->assertDatabaseCount('Livro_Assunto', 1);
    expect(DB::table('vw_relatorio_livros_autores')->value('Assuntos'))->toBe('Assunto');
    $livro->delete();
    $this->assertDatabaseCount('Livro_Assunto', 0);
});

it('interrompe a migration antes de alterar dados quando há duplicações legadas', function () {
    $migration = require database_path('migrations/2026_09_16_120000_add_primary_key_to_livro_assunto_table.php');
    $migration->down();
    $livro = Livro::create(['Titulo' => 'Livro', 'Editora' => 'Editora', 'Edicao' => 1, 'AnoPublicacao' => 2026, 'Valor' => '10.00']);
    $assunto = Assunto::create(['Descricao' => 'Assunto']);
    $vinculo = ['Livro_Codl' => $livro->Codl, 'Assunto_codAs' => $assunto->codAs];
    DB::table('Livro_Assunto')->insert([$vinculo, $vinculo]);
    $indices = Schema::getIndexes('Livro_Assunto');

    try {
        expect(fn () => $migration->up())->toThrow(RuntimeException::class, 'Nenhum registro foi removido.');
        $this->assertDatabaseCount('Livro_Assunto', 2);
        expect(Schema::getIndexes('Livro_Assunto'))->toBe($indices);
    } finally {
        // Restaura o estado esperado pelo rollback do teste, apenas na base isolada.
        DB::table('Livro_Assunto')->where($vinculo)->delete();
        $migration->up();
    }
});
