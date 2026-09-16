<?php

use App\Models\Assunto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it("Listar assuntos no index", function () {
    Assunto::create(["Descricao" => "Ficção Científica"]);

    $this->get(route("assunto.index"))
        ->assertStatus(200)
        ->assertSee("Ficção Científica");
});

it("Criar Assunto", function () {
    $this->post(route("assunto.store"), ["Descricao" => "Romance"])
        ->assertRedirect(route("assunto.index"))
        ->assertSessionHas("success");
});

it('Recusa assunto com descrição vazia', function () {
    $this->post(route('assunto.store'), [
        'Descricao' => '',
    ])
        ->assertSessionHasErrors('Descricao'); // Garante que o AssuntoRequest barrou
});

it('Atualizar assunto existente', function () {
    $assunto = Assunto::create(['Descricao' => 'Suspense']);

    $this->put(route('assunto.update', $assunto->codAs), [
        'Descricao' => 'Terror',
    ])
        ->assertRedirect(route('assunto.index'));

    $this->assertDatabaseHas('Assunto', [
        'codAs' => $assunto->codAs,
        'Descricao' => 'Terror',
    ]);
});

it('Excluir assunto', function () {
    $assunto = Assunto::create(['Descricao' => 'Comédia']);

    $this->delete(route('assunto.destroy', $assunto->codAs))
        ->assertRedirect(route('assunto.index'));

    // Confirma se sumiu do banco
    $this->assertDatabaseMissing('Assunto', [
        'codAs' => $assunto->codAs,
    ]);
});

it('Listagem de Assuntos com paginação', function () {
    for ($i = 1; $i <= 15; $i++) {
        Assunto::create(['Descricao' => "Assunto {$i}"]);
    }

    $response = $this->get(route('assunto.index'));
    $response->assertStatus(200);

    $response->assertViewHas('assuntos', function ($assuntos) {
        expect($assuntos)->toBeInstanceOf(LengthAwarePaginator::class);
        expect($assuntos->total())->toBe(15);
        expect($assuntos->lastPage())->toBe(2);

        return true;
    });
});

it('Buscar assunto pelo campo de busca', function () {
    // 1. Cria dois assuntos distintos
    Assunto::create(['Descricao' => 'Banco de Dados']);
    Assunto::create(['Descricao' => 'Programação Web']);

    // 2. Acessa a rota passando a query string de busca
    $response = $this->get(route('assunto.index', ['search' => 'Banco']));

    // 3. Garante que achou o correto e filtrou o outro
    $response->assertStatus(200)
        ->assertSee('Banco de Dados')
        ->assertDontSee('Programação Web');
});
