<?php

use App\Models\Autor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it("Listar autores no index", function (){
    Autor::create(["Nome" => "Gustavo Pereira"]);

    $this->get(route("autor.index"))
        ->assertStatus(200)
        ->assertSee("Gustavo Pereira");
});

it("Criar Autor", function (){
    $this->post(route("autor.store"), ["Nome" => "Renato Barreto"])
        ->assertRedirect(route("autor.index"))
        ->assertSessionHas("success");
});

it('Recusa autor com nome vazio', function () {
    $this->post(route('autor.store'), [
        'Nome' => '',
    ])
        ->assertSessionHasErrors('Nome'); // Garante que o FormRequest barrou
});

it('Atualizar autor existente', function () {
    $autor = Autor::create(['Nome' => 'Guilherme Reis']);

    $this->put(route('autor.update', $autor->CodAu), [
        'Nome' => 'Roger Amorim',
    ])
        ->assertRedirect(route('autor.index'));

    $this->assertDatabaseHas('Autor', [
        'CodAu' => $autor->CodAu,
        'Nome' => 'Roger Amorim',
    ]);
});

it('Excluir autor', function () {
    $autor = Autor::create(['Nome' => 'Rick Duarte']);

    $this->delete(route('autor.destroy', $autor->CodAu))
        ->assertRedirect(route('autor.index'));

    // Confirma se sumiu do banco
    $this->assertDatabaseMissing('Autor', [
        'CodAu' => $autor->CodAu,
    ]);
});

it('Listagem de Autores', function () {
    for ($i = 1; $i <= 15; $i++) {
        Autor::create(['Nome' => "Autor Teste {$i}"]);
    }

    $response = $this->get(route('autor.index'));
    $response->assertStatus(200);

    $response->assertViewHas('autores', function ($autores) {
        // Usando o expect() do Pest, se algo falhar aqui, a mensagem de erro será exata!
        expect($autores)->toBeInstanceOf(LengthAwarePaginator::class);
        expect($autores->total())->toBe(15);
        expect($autores->lastPage())->toBe(2);

        return true;
    });
});

it('Buscar autor pelo campo de busca', function () {
    // 1. Cria dois autores com nomes bem diferentes
    Autor::create(['Nome' => 'Flavio Castro']);
    Autor::create(['Nome' => 'Cesar Lemos']);

    // 2. Acessa a rota passando a query string de busca (?search=Clarice)
    $response = $this->get(route('autor.index', ['search' => 'Cesar']));

    // 3. Garante que achou a Clarice, mas filtrou (escondeu) o Machado
    $response->assertStatus(200)
        ->assertSee('Cesar Lemos')
        ->assertDontSee('Flavio Castro')
        ->assertSee('value="Cesar"', false)
        ->assertSee('href="'.route('autor.index').'" class="btn btn-light border px-4">Limpar</a>', false);

    $this->get(route('autor.index'))->assertOk()
        ->assertSee('Cesar Lemos')
        ->assertSee('Flavio Castro')
        ->assertSee('value=""', false)
        ->assertDontSee('>Limpar</a>', false);
});
