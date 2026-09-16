<?php

use App\Models\Autor;
use App\Models\Livro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('numera o PDF com o total real de páginas', function ($quantidade) {
    for ($i = 1; $i <= $quantidade; $i++) {
        $autor = Autor::create(['Nome' => 'Autor '.$i]);
        $livro = Livro::create(['Titulo' => 'Livro '.$i, 'Editora' => 'Editora', 'Edicao' => 1, 'AnoPublicacao' => 2026, 'Valor' => 10]);
        $livro->autores()->attach($autor);
    }

    $resposta = $this->get('/relatorios/exportar')->assertOk();
    $pdf = $resposta->getContent();
    preg_match_all('/\/Type\s*\/Page\b/', $pdf, $paginas);
    $total = count($paginas[0]);
    expect($total)->toBeGreaterThan(0);
    if ($quantidade > 1) {
        expect($total)->toBeGreaterThan(1);
    }

    // Inspeciona os comandos reais de texto do PDF, sem depender de programas externos.
    preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $pdf, $streams);
    $conteudo = '';
    foreach ($streams[1] as $stream) {
        $conteudo .= @gzuncompress($stream) ?: $stream;
    }
    expect($conteudo)->not->toContain(' de 0');
    for ($pagina = 1; $pagina <= $total; $pagina++) {
        expect($conteudo)->toContain($pagina.' de '.$total);
    }
})->with([1, 12]);
