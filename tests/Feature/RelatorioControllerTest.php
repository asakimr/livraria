<?php

use App\Models\Assunto;
use App\Models\Autor;
use App\Models\Livro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

// Esta função roda automaticamente antes de CADA teste abaixo
beforeEach(function () {
    // 1. Criamos os Autores
    $this->autorTolkien = Autor::create(['Nome' => 'J.R.R. Tolkien']);
    $this->autorMartin = Autor::create(['Nome' => 'George R.R. Martin']);

    // 2. Criamos os Assuntos
    $this->assuntoFantasia = Assunto::create(['Descricao' => 'Fantasia']);
    $this->assuntoAventura = Assunto::create(['Descricao' => 'Aventura']);
    $this->assuntoPolitica = Assunto::create(['Descricao' => 'Política']);

    // 3. Criamos os Livros
    $this->livroAnel = Livro::create([
        'Titulo' => 'O Senhor dos Anéis',
        'Editora' => 'HarperCollins',
        'Edicao' => 1,
        'AnoPublicacao' => 1954,
        'Valor' => 89.90
    ]);

    $this->livroTrono = Livro::create([
        'Titulo' => 'A Guerra dos Tronos',
        'Editora' => 'Leya',
        'Edicao' => 1,
        'AnoPublicacao' => 1996,
        'Valor' => 99.90
    ]);

    // 4. Vinculamos as relações (Isso alimenta a View SQL)
    $this->livroAnel->autores()->sync([$this->autorTolkien->CodAu]);
    $this->livroAnel->assuntos()->sync([$this->assuntoFantasia->codAs, $this->assuntoAventura->codAs]);

    $this->livroTrono->autores()->sync([$this->autorMartin->CodAu]);
    $this->livroTrono->assuntos()->sync([$this->assuntoFantasia->codAs, $this->assuntoPolitica->codAs]);
});

it('Acessar tela de relatórios e listar todos os registros', function () {
    $response = $this->get(route('relatorios.index'));

    $response->assertStatus(200)
             ->assertSee('O Senhor dos Anéis')
             ->assertSee('A Guerra dos Tronos')
             ->assertSee('J.R.R. Tolkien')
             ->assertSee('George R.R. Martin');
});

it('Filtrar relatório por Autor específico', function () {
    $response = $this->get(route('relatorios.index', [
        'autor_id' => $this->autorTolkien->CodAu
    ]));

    $response->assertStatus(200)
             ->assertSee('O Senhor dos Anéis')
             ->assertDontSee('A Guerra dos Tronos'); // Não deve aparecer o livro do Martin
});

it('Filtrar relatório por Título do Livro', function () {
    $response = $this->get(route('relatorios.index', [
        'titulo' => 'Tronos'
    ]));

    $response->assertStatus(200)
             ->assertSee('A Guerra dos Tronos')
             ->assertDontSee('O Senhor dos Anéis');
});

it('Filtrar relatório por Assuntos (Múltiplos)', function () {
    // Busca por "Política" (Só o Guerra dos Tronos tem)
    $response = $this->get(route('relatorios.index', [
        'assuntos' => [$this->assuntoPolitica->codAs]
    ]));

    $response->assertStatus(200)
             ->assertSee('A Guerra dos Tronos')
             ->assertDontSee('O Senhor dos Anéis');
});

it('Exportar PDF e verificar se o DOMPDF gera o arquivo corretamente', function () {
    // Fazemos a requisição para a rota de exportação
    $response = $this->get(route('relatorios.exportar'));

    // O status deve ser 200 (OK)
    $response->assertStatus(200);

    // O cabeçalho de resposta DEVE ser um PDF
    $response->assertHeader('Content-Type', 'application/pdf');

    // Podemos até garantir que o nome do arquivo enviado está correto no Header
    $contentDisposition = $response->headers->get('Content-Disposition');
    expect($contentDisposition)->toContain('relatorio_livros.pdf');
});

it('Garantir que os filtros aplicados na tela repassam para o PDF', function () {
    // Exportamos o PDF passando o filtro de Autor na URL
    $response = $this->get(route('relatorios.exportar', [
        'autor_id' => $this->autorTolkien->CodAu
    ]));

    $response->assertStatus(200)
             ->assertHeader('Content-Type', 'application/pdf');
});
