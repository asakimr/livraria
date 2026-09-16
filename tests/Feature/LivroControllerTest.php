<?php

use App\Models\Assunto;
use App\Models\Autor;
use App\Models\Livro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

// --- TESTES DE LISTAGEM E BUSCA ---

it('Listar livros no index', function () {
    $livro = Livro::create([
        'Titulo' => 'O Senhor dos Anéis',
        'Editora' => 'HarperCollins',
        'Edicao' => 1,
        'AnoPublicacao' => 1954,
        'Valor' => 89.90,
    ]);

    $this->get(route('livro.index'))
        ->assertStatus(200)
        ->assertSee('O Senhor dos Anéis');
});

it('Listagem de Livros com paginação', function () {
    for ($i = 1; $i <= 15; $i++) {
        Livro::create([
            'Titulo' => "Livro Teste {$i}",
            'Editora' => 'Editora Teste',
            'Edicao' => 1,
            'AnoPublicacao' => 2020,
            'Valor' => 50.00,
        ]);
    }

    $response = $this->get(route('livro.index'));
    $response->assertStatus(200);

    $response->assertViewHas('livros', function ($livros) {
        expect($livros)->toBeInstanceOf(LengthAwarePaginator::class);
        expect($livros->total())->toBe(15);
        expect($livros->lastPage())->toBe(2);

        return true;
    });
});

it('Buscar livro pelo campo de busca', function () {
    Livro::create(['Titulo' => 'Harry Potter', 'Editora' => 'Rocco', 'Edicao' => 1, 'AnoPublicacao' => 1997, 'Valor' => 40.00]);
    Livro::create(['Titulo' => 'Percy Jackson', 'Editora' => 'Intrínseca', 'Edicao' => 1, 'AnoPublicacao' => 2005, 'Valor' => 35.00]);

    $response = $this->get(route('livro.index', ['search' => 'Harry']));

    $response->assertStatus(200)
        ->assertSee('Harry Potter')
        ->assertDontSee('Percy Jackson');
});

// --- TESTES DE CRIAÇÃO (STORE) E VALIDAÇÃO ---

it('Criar Livro com relacionamentos N:N e formatar valor', function () {
    $autor = Autor::create(['Nome' => 'J.R.R. Tolkien']);
    $assunto = Assunto::create(['Descricao' => 'Fantasia']);

    $response = $this->post(route('livro.store'), [
        'Titulo' => 'O Hobbit',
        'Editora' => 'HarperCollins',
        'Edicao' => 1,
        'AnoPublicacao' => 1937,
        'Valor' => '59,90', // Testando se o Request converte a vírgula
        'autores' => [$autor->CodAu],
        'assuntos' => [$assunto->codAs],
    ]);

    $response->assertRedirect(route('livro.index'))
        ->assertSessionHas('success');

    // Confirma tabela principal
    $this->assertDatabaseHas('Livro', [
        'Titulo' => 'O Hobbit',
        'Valor' => 59.90, // Confirma que salvou com ponto decimal
    ]);

    // Confirma se salvou na Pivot de Autores
    $this->assertDatabaseHas('Livro_Autor', [
        'Autor_CodAu' => $autor->CodAu,
    ]);

    // Confirma se salvou na Pivot de Assuntos
    $this->assertDatabaseHas('Livro_Assunto', [
        'Assunto_codAs' => $assunto->codAs,
    ]);
});

it('Recusa livro com campos obrigatórios e relacionamentos faltando', function () {
    $response = $this->post(route('livro.store'), [
        'Titulo' => '',
        // Omitindo editora, ano, valor, autores e assuntos de propósito
    ]);

    $response->assertSessionHasErrors(['Titulo', 'Editora', 'Edicao', 'AnoPublicacao', 'Valor', 'autores', 'assuntos']);
});

// --- TESTES DE ATUALIZAÇÃO (UPDATE) E EXCEÇÕES ---

it('Atualizar livro e alterar seus relacionamentos', function () {
    $livro = Livro::create(['Titulo' => 'A Arte da Guerra', 'Editora' => 'AG', 'Edicao' => 1, 'AnoPublicacao' => 2000, 'Valor' => 10.00]);

    $autorAntigo = Autor::create(['Nome' => 'Sun Tzu']);
    $autorNovo = Autor::create(['Nome' => 'Sung Jin Woo']);

    $assunto = Assunto::create(['Descricao' => 'Geral']);

    // Atribui o autor antigo inicialmente
    $livro->autores()->sync([$autorAntigo->CodAu]);
    $livro->assuntos()->sync([$assunto->codAs]);

    $response = $this->put(route('livro.update', $livro->Codl), [
        'Titulo' => 'Martial Warriors',
        'Editora' => 'MW',
        'Edicao' => 2,
        'AnoPublicacao' => 2024,
        'Valor' => '200,00',
        'autores' => [$autorNovo->CodAu], // Trocando o autor
        'assuntos' => [$assunto->codAs],
    ]);

    $response->assertRedirect(route('livro.index'));

    $this->assertDatabaseHas('Livro', ['Titulo' => 'Martial Warriors']);

    // Garante que o autor novo entrou
    $this->assertDatabaseHas('Livro_Autor', ['Autor_CodAu' => $autorNovo->CodAu]);

    // Garante que o autor antigo foi removido (o sync funcionou)
    $this->assertDatabaseMissing('Livro_Autor', ['Autor_CodAu' => $autorAntigo->CodAu]);
});

it('Redireciona e avisa ao tentar editar livro com ID inexistente', function () {
    $autor = Autor::create(['Nome' => 'Robert JR']);
    $assunto = Assunto::create(['Descricao' => 'Amazing Test']);

    // Simulando a ModelNotFoundException sendo capturada pelo catch
    $response = $this->put(route('livro.update', 99999), [
        'Titulo' => 'Fantasma',
        'Editora' => 'Desconhecida',
        'Edicao' => 1,
        'AnoPublicacao' => 2024,
        'Valor' => '10,00',
        'autores' => [$autor->CodAu],
        'assuntos' => [$assunto->codAs],
    ]);

    // Como você programou, deve ir para index e mandar mensagem de erro
    $response->assertRedirect(route('livro.index'))
        ->assertSessionHas('error', 'O livro que você tentou editar não foi encontrado no sistema.');
});

// --- TESTES DE EXCLUSÃO (DESTROY) ---

it('Excluir livro e limpar suas dependências Pivot', function () {
    $livro = Livro::create(['Titulo' => 'Solo Leveling', 'Editora' => 'SL', 'Edicao' => 1, 'AnoPublicacao' => 2020, 'Valor' => 150.00]);
    $autor = Autor::create(['Nome' => 'Autor SL']);

    $livro->autores()->sync([$autor->CodAu]);

    $response = $this->delete(route('livro.destroy', $livro->Codl));

    $response->assertRedirect(route('livro.index'))
        ->assertSessionHas('success');

    // Confirma que apagou da tabela principal
    $this->assertDatabaseMissing('Livro', [
        'Codl' => $livro->Codl,
    ]);

    // Confirma que apagou da pivot
    $this->assertDatabaseMissing('Livro_Autor', [
        'Livro_Codl' => $livro->Codl, // Ajuste para o nome da sua Foreign Key se for diferente
    ]);
});

it('Redireciona e avisa ao tentar excluir livro inexistente', function () {
    // Simulando a ModelNotFoundException no delete
    $response = $this->delete(route('livro.destroy', 99999));

    $response->assertRedirect(route('livro.index'))
        ->assertSessionHas('error', 'O livro que você tentou excluir já não existe no sistema.');
});
