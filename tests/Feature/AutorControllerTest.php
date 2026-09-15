<?php

use App\Models\Autor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it("Listar autores no index", function(){
   Autor::create(["Nome" => "Gustavo Pereira"]);

  $this->get(route("autor.index"))
  ->assertStatus(200)
  ->assertSee("Gustavo Pereira");
});

it("Criar Autor", function(){
    $this->post(route("autor.store"), ["Nome" => "Renato Barreto"])
    ->assertRedirect(route("autor.index"))
    ->assertSessionHas("success");
});


it('Recusa autor com nome vazio', function () {
    $this->post(route('autor.store'), [
        'Nome' => ''
    ])
    ->assertSessionHasErrors('Nome'); // Garante que o FormRequest barrou
});

it('Atualizar autor existente', function () {
    $autor = Autor::create(['Nome' => 'Guilherme Reis']);

    $this->put(route('autor.update', $autor->CodAu), [
        'Nome' => 'Roger Amorim'
    ])
    ->assertRedirect(route('autor.index'));

    $this->assertDatabaseHas('autor', [
        'CodAu' => $autor->CodAu,
        'Nome' => 'Roger Amorim'
    ]);
});

it('Excluir autor', function () {
    $autor = Autor::create(['Nome' => 'Rick Duarte']);

    $this->delete(route('autor.destroy', $autor->CodAu))
         ->assertRedirect(route('autor.index'));

    // Confirma se sumiu do banco
    $this->assertDatabaseMissing('autor', [
        'CodAu' => $autor->CodAu
    ]);
});
