<?php

namespace App\Http\Controllers;

use App\Http\Requests\RelatorioRequest;
use App\Models\Assunto;
use App\Models\Autor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use stdClass;

class RelatorioController extends Controller
{
    /** @return Collection<int|string, Collection<int, stdClass>> */
    private function obterDadosFiltrados(RelatorioRequest $request): Collection
    {
        $query = DB::table('vw_relatorio_livros_autores');

        // Filtro por Autor
        if ($request->filled('autor_id')) {
            $query->where('CodAu', $request->autor_id);
        }

        // Filtro por Título do Livro
        if ($request->filled('titulo')) {
            $query->where('Livro_Titulo', 'like', '%'.$request->string('titulo')->toString().'%');
        }

        if ($request->filled('assuntos') && is_array($request->assuntos)) {
            $query->whereExists(function ($subQuery) use ($request) {
                $subQuery->select(DB::raw(1))
                    ->from('Livro_Assunto')
                    ->whereColumn('Livro_Assunto.Livro_Codl', 'vw_relatorio_livros_autores.Codl')
                    ->whereIn('Livro_Assunto.Assunto_codAs', $request->assuntos);
            });
        }

        $dados = $query->orderBy('Autor_Nome')->orderBy('Livro_Titulo')->get();

        return $dados->groupBy('CodAu');
    }

    public function index(RelatorioRequest $request): View
    {
        $autoresAgrupados = $this->obterDadosFiltrados($request);
        $autores = Autor::orderBy('Nome')->get();

        $assuntos = Assunto::orderBy('Descricao')->get();

        return view('relatorio.index', compact('autoresAgrupados', 'autores', 'assuntos'));
    }

    # Gera o PDF (É considerado os filtros que estao na tela)
    public function exportarPdf(RelatorioRequest $request): Response
    {
        $autoresAgrupados = $this->obterDadosFiltrados($request);

        // Reutilizamos a view antiga (agora renomeada para pdf.blade.php)
        $pdf = Pdf::loadView('relatorio.pdf', compact('autoresAgrupados'));

        // O total de páginas só está disponível depois da renderização do DomPDF.
        $pdf->render();
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $fonte = $dompdf->getFontMetrics()->getFont('Helvetica');
        $canvas->page_text($canvas->get_width() - 105, $canvas->get_height() - 38, 'Página {PAGE_NUM} de {PAGE_COUNT}', $fonte, 8, [0.4, 0.4, 0.4]);

        return $pdf->stream('relatorio_livros.pdf');
    }
}
