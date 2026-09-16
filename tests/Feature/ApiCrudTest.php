<?php

use App\Models\Assunto;
use App\Models\Autor;
use App\Services\AutorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('executa CRUD JSON de autores e assuntos sem autenticação', function (string $recurso, string $campo, string $chave) {
    $id = $this->postJson('/api/'.$recurso, [$campo => 'Primeiro'])->assertCreated()->json('data.'.$chave);
    $this->getJson('/api/'.$recurso.'/'.$id)->assertOk()->assertJsonPath('data.'.$campo, 'Primeiro');
    $this->putJson('/api/'.$recurso.'/'.$id, [$campo => 'Segundo'])->assertOk()->assertJsonPath('data.'.$campo, 'Segundo');
    $this->getJson('/api/'.$recurso.'?search=Segundo')->assertOk()->assertJsonPath('total', 1);
    $this->deleteJson('/api/'.$recurso.'/'.$id)->assertNoContent();
    $this->getJson('/api/'.$recurso.'/'.$id)->assertNotFound()->assertJsonPath('message', 'Registro ou rota não encontrado.');
})->with([['autores', 'Nome', 'CodAu'], ['assuntos', 'Descricao', 'codAs']]);

it('executa CRUD de livros com valor decimal e bloqueia exclusão dos vínculos', function () {
    $autor = Autor::create(['Nome' => 'Autor']);
    $assunto = Assunto::create(['Descricao' => 'Assunto']);
    $dados = ['Titulo' => 'Livro', 'Editora' => 'Editora', 'Edicao' => 1, 'AnoPublicacao' => 2026,
        'Valor' => '59.90', 'autores' => [$autor->CodAu], 'assuntos' => [$assunto->codAs]];
    $id = $this->postJson('/api/livros', $dados)->assertCreated()->assertJsonPath('data.Valor', '59.90')->json('data.Codl');
    $this->getJson('/api/livros/'.$id)->assertOk()->assertJsonCount(1, 'data.autores')->assertJsonCount(1, 'data.assuntos');
    $this->deleteJson('/api/autores/'.$autor->CodAu)->assertConflict();
    $this->deleteJson('/api/assuntos/'.$assunto->codAs)->assertConflict();
    $this->putJson('/api/livros/'.$id, array_replace($dados, ['Valor' => '10.50']))->assertOk()->assertJsonPath('data.Valor', '10.50');
    $this->getJson('/api/livros?search=Livro')->assertOk()->assertJsonPath('total', 1);
    $this->deleteJson('/api/livros/'.$id)->assertNoContent();
    $this->assertDatabaseCount('Livro_Autor', 0);
    $this->assertDatabaseCount('Livro_Assunto', 0);
    $this->deleteJson('/api/autores/'.$autor->CodAu)->assertNoContent();
    $this->deleteJson('/api/assuntos/'.$assunto->codAs)->assertNoContent();
});

it('retorna validação em português e rejeita moeda mascarada na API', function () {
    $this->postJson('/api/livros', ['Valor' => '59,90'])->assertUnprocessable()->assertJsonValidationErrors(['Valor', 'Titulo', 'autores', 'assuntos'])
        ->assertJsonPath('message', 'Os dados informados são inválidos.');
    $this->postJson('/api/autores', [])->assertUnprocessable()->assertJsonValidationErrors('Nome');
    $this->postJson('/api/assuntos', [])->assertUnprocessable()->assertJsonValidationErrors('Descricao');
    $this->get('/api/livros/999')->assertNotFound()->assertHeader('Content-Type', 'application/json');
});

it('não revela detalhes internos em falhas da API', function () {
    $this->mock(AutorService::class, function ($mock) {
        $mock->shouldReceive('salvar')->once()->andThrow(new RuntimeException('credencial privada SQL'));
    });
    $this->postJson('/api/autores', ['Nome' => 'Teste'])->assertStatus(500)
        ->assertExactJson(['message' => 'Erro interno. Não foi possível concluir a operação.']);
});

it('rejeita limites inválidos sem alterar o banco', function (array $alteracao, string $campo) {
    $autor = Autor::create(['Nome' => 'Autor']);
    $assunto = Assunto::create(['Descricao' => 'Assunto']);
    $dados = ['Titulo' => 'Livro', 'Editora' => 'Editora', 'Edicao' => 1, 'AnoPublicacao' => 2026,
        'Valor' => '59.90', 'autores' => [$autor->CodAu], 'assuntos' => [$assunto->codAs]];
    $this->postJson('/api/livros', array_replace($dados, $alteracao))->assertUnprocessable()->assertJsonValidationErrors($campo);
    $this->assertDatabaseCount('Livro', 0);
})->with([
    [['Valor' => '-1.00'], 'Valor'], [['Valor' => '1000000.00'], 'Valor'], [['Valor' => '59.999'], 'Valor'],
    [['Edicao' => 0], 'Edicao'], [['AnoPublicacao' => 12345], 'AnoPublicacao'],
    [['autores' => [999]], 'autores.0'], [['assuntos' => []], 'assuntos'],
]);

it('rejeita filtros malformados e mantém JSON sem header Accept', function (string $recurso) {
    $this->get('/api/'.$recurso.'?search[]=x')->assertUnprocessable()->assertJsonValidationErrors('search');
    $this->get('/api/'.$recurso.'?page[]=1')->assertUnprocessable()->assertJsonValidationErrors('page');
    $this->get('/api/'.$recurso.'/999999999999999999999999999999')->assertNotFound();
    $this->get('/api/'.$recurso.'/abc')->assertNotFound();
})->with(['livros', 'autores', 'assuntos']);

it('exige lista de IDs inteiros nas relações da API', function (array $autores, string $campo) {
    $autor = Autor::create(['Nome' => 'Autor']);
    $assunto = Assunto::create(['Descricao' => 'Assunto']);
    $dados = ['Titulo' => 'Livro', 'Editora' => 'Editora', 'Edicao' => 1, 'AnoPublicacao' => 2026,
        'Valor' => '59.90', 'autores' => $autores, 'assuntos' => [$assunto->codAs]];
    $this->postJson('/api/livros', $dados)->assertUnprocessable()->assertJsonValidationErrors($campo);
    $this->assertDatabaseCount('Livro', 0);
})->with([[['x' => 1], 'autores'], [[true], 'autores.0'], [[['x' => 1]], 'autores.0']]);
