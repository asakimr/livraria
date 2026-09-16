<?php

use App\Models\Assunto;
use App\Models\Autor;
use App\Models\Livro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('Recupera o modal e o texto enviado após erro de autor ou assunto', function ($recurso, $campo, $modelo, $modal, $editar) {
    $registro = $modelo::create([$campo => 'Original']);
    $dados = [
        $campo => str_repeat('a', 41),
        '_modal' => 'modal'.($editar ? 'Editar' : 'Novo').$modal,
        '_registro' => $editar ? $registro->getKey() : '',
    ];

    $resposta = $this->from(route($recurso.'.index'))
        ->{($editar ? 'put' : 'post')}(route($recurso.($editar ? '.update' : '.store'), $editar ? $registro->getKey() : []), $dados);

    $resposta->assertRedirect(route($recurso.'.index'))->assertSessionHasErrors($campo);
    $resposta = $this->get(route($recurso.'.index'))->assertOk();
    $html = new DOMDocument;
    @$html->loadHTML($resposta->getContent());
    $modais = (new DOMXPath($html))->query('//*[@data-recuperar-formulario]');
    expect($modais->length)->toBe(1);
    expect($modais->item(0)->getAttribute('id'))->toBe($dados['_modal']);
    $recuperados = json_decode($modais->item(0)->getAttribute('data-recuperar-formulario'), true);
    expect($recuperados[$campo])->toBe($dados[$campo]);
    expect((string) $recuperados['_registro'])->toBe((string) $dados['_registro']);
    expect($registro->fresh()->{$campo})->toBe('Original');
})->with([
    ['autor', 'Nome', Autor::class, 'Autor', false],
    ['autor', 'Nome', Autor::class, 'Autor', true],
    ['assunto', 'Descricao', Assunto::class, 'Assunto', false],
    ['assunto', 'Descricao', Assunto::class, 'Assunto', true],
]);

it('Recupera preço brasileiro e seleções do livro após erro sem alterar o banco', function ($editar) {
    $autor = Autor::create(['Nome' => 'Autor escolhido']);
    $assunto = Assunto::create(['Descricao' => 'Assunto escolhido']);
    $livro = Livro::create(['Titulo' => 'Original', 'Editora' => 'Editora', 'Edicao' => 1, 'AnoPublicacao' => 2020, 'Valor' => 10]);
    $dados = [
        'Titulo' => '',
        'Editora' => 'Texto preservado',
        'Edicao' => 2,
        'AnoPublicacao' => 2024,
        'Valor' => '59,90',
        'autores' => [$autor->CodAu],
        'assuntos' => [$assunto->codAs],
        '_modal' => $editar ? 'modalEditarLivro' : 'modalNovoLivro',
        '_registro' => $editar ? $livro->Codl : '',
    ];

    $this->from(route('livro.index'))
        ->{($editar ? 'put' : 'post')}(route($editar ? 'livro.update' : 'livro.store', $editar ? $livro->Codl : []), $dados)
        ->assertSessionHasErrors('Titulo');

    $resposta = $this->get(route('livro.index'))->assertOk();
    $html = new DOMDocument;
    @$html->loadHTML($resposta->getContent());
    $modais = (new DOMXPath($html))->query('//*[@data-recuperar-formulario]');
    expect($modais->length)->toBe(1);
    expect($modais->item(0)->getAttribute('id'))->toBe($dados['_modal']);
    $recuperados = json_decode($modais->item(0)->getAttribute('data-recuperar-formulario'), true);
    expect($recuperados['Valor'])->toBe('59,90');
    expect($recuperados['Editora'])->toBe('Texto preservado');
    expect($recuperados['autores'])->toBe($dados['autores']);
    expect($recuperados['assuntos'])->toBe($dados['assuntos']);
    expect((string) $recuperados['_registro'])->toBe((string) $dados['_registro']);
    expect($livro->fresh()->Titulo)->toBe('Original');
    expect((float) $livro->fresh()->Valor)->toBe(10.0);
})->with([false, true]);

it('Exibe a paginação e mensagens de validação em português', function () {
    app()->setLocale('pt_BR');
    for ($i = 0; $i < 12; $i++) {
        Autor::create(['Nome' => 'Autor '.$i]);
    }

    $this->get(route('autor.index'))->assertOk()
        ->assertSee('Mostrando')->assertSee('resultados')->assertSee('Próxima');
    expect(validator(['Nome' => []], ['Nome' => 'string'])->errors()->first('Nome'))
        ->toBe('O campo nome deve ser um texto.');
});
