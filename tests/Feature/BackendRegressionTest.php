<?php

use App\Exceptions\RegraNegocioException;
use App\Models\Assunto;
use App\Models\Autor;
use App\Services\LivroService;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function dadosLivroRegressao(): array
{
    return ['Titulo' => 'Livro', 'Editora' => 'Editora', 'Edicao' => 1, 'AnoPublicacao' => 2026,
        'Valor' => '59,90', 'autores' => [Autor::create(['Nome' => 'Autor'])->CodAu],
        'assuntos' => [Assunto::create(['Descricao' => 'Assunto'])->codAs]];
}

it('preserva valor brasileiro após validação e salva reenvio sem multiplicar preço', function () {
    $dados = dadosLivroRegressao();
    $this->from('/livro')->post('/livro', array_replace($dados, ['Titulo' => '']))
        ->assertSessionHasErrors('Titulo')->assertSessionHas('_old_input.Valor', '59,90');
    $this->post('/livro', array_replace($dados, ['Valor' => session('_old_input.Valor')]))->assertSessionHas('success');
    $this->assertDatabaseHas('Livro', ['Valor' => 59.90]);
});

it('mantém autores homônimos separados e aplica filtro ao conteúdo enviado para PDF', function () {
    $dados = dadosLivroRegressao();
    $dados['Valor'] = '59.90';
    app(LivroService::class)->salvar($dados);
    $outro = Autor::create(['Nome' => 'Autor']);
    app(LivroService::class)->salvar(array_replace($dados, ['Titulo' => 'Outro livro', 'autores' => [$outro->CodAu]]));
    $this->get('/relatorios')->assertOk()->assertViewHas('autoresAgrupados', fn ($grupos) => $grupos->count() === 2);
    Pdf::shouldReceive('loadView')->once()->with('relatorio.pdf', Mockery::on(function ($dadosPdf) use ($outro) {
        $grupos = $dadosPdf['autoresAgrupados'];

        return $grupos->count() === 1 && $grupos->has($outro->CodAu)
            && $grupos->first()->pluck('Livro_Titulo')->all() === ['Outro livro'];
    }))->andReturnSelf();
    Pdf::shouldReceive('render')->once()->andReturnSelf();
    Pdf::shouldReceive('getDomPDF')->once()->andReturn(new Dompdf);
    Pdf::shouldReceive('stream')->once()->with('relatorio_livros.pdf')->andReturn(response('PDF', 200, ['Content-Type' => 'application/pdf']));
    $this->get('/relatorios/exportar?autor_id='.$outro->CodAu)->assertOk();
});

it('avisa na web ao bloquear exclusão de autor e assunto vinculados', function () {
    $dados = dadosLivroRegressao();
    $dados['Valor'] = '59.90';
    app(LivroService::class)->salvar($dados);
    $this->from('/autor')->delete('/autor/'.$dados['autores'][0])->assertRedirect('/autor')->assertSessionHas('error');
    $this->from('/assunto')->delete('/assunto/'.$dados['assuntos'][0])->assertRedirect('/assunto')->assertSessionHas('error');
    $this->assertDatabaseCount('Livro_Autor', 1);
    $this->assertDatabaseCount('Livro_Assunto', 1);
});

it('desfaz alteração e vínculos quando atualização falha dentro da transação', function () {
    $dados = dadosLivroRegressao();
    $dados['Valor'] = '59.90';
    $service = app(LivroService::class);
    $livro = $service->salvar($dados);
    expect(fn () => $service->atualizar($livro, array_replace($dados, ['Titulo' => 'Não salvar', 'autores' => [999999]])))
        ->toThrow(QueryException::class);
    expect($livro->fresh()->Titulo)->toBe('Livro');
    $this->assertDatabaseHas('Livro_Autor', ['Livro_Codl' => $livro->Codl, 'Autor_CodAu' => $dados['autores'][0]]);
});

it('rejeita livro sem relações por exceção de negócio', function () {
    expect(fn () => app(LivroService::class)->salvar(['Titulo' => 'Inválido']))->toThrow(RegraNegocioException::class);
    $this->assertDatabaseCount('Livro', 0);
});

it('remove e recria a view no rollback da migration', function () {
    $migration = require database_path('migrations/2026_09_16_023126_create_vw_relatorio_livros_autores_view.php');
    $migration->down();
    expect(Schema::hasView('vw_relatorio_livros_autores'))->toBeFalse();
    $migration->up();
    expect(DB::table('vw_relatorio_livros_autores')->count())->toBe(0);
});

it('valida filtros malformados antes de consultar relatório ou gerar PDF', function (string $query, string $campo) {
    $this->getJson('/relatorios?'.$query)->assertUnprocessable()->assertJsonValidationErrors($campo);
    $this->getJson('/relatorios/exportar?'.$query)->assertUnprocessable()->assertJsonValidationErrors($campo);
})->with([['titulo[]=x', 'titulo'], ['autor_id[]=1', 'autor_id'], ['assuntos[][]=1', 'assuntos.0']]);
