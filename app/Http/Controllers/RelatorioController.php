<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Autor;
use App\Models\Assunto;

class RelatorioController extends Controller
{
    private function obterDadosFiltrados(Request $request)
    {
        $query = DB::table('vw_relatorio_livros_autores');

        // Filtro por Autor
        if ($request->filled('autor_id')) {
            $query->where('CodAu', $request->autor_id);
        }

        // Filtro por Título do Livro
        if ($request->filled('titulo')) {
            $query->where('Livro_Titulo', 'like', '%' . $request->titulo . '%');
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

        return $dados->groupBy('Autor_Nome');
    }

    public function index(Request $request)
    {
        $autoresAgrupados = $this->obterDadosFiltrados($request);
        $autores = Autor::orderBy('Nome')->get();

        $assuntos = Assunto::orderBy('Descricao')->get();

        return view('relatorio.index', compact('autoresAgrupados', 'autores', 'assuntos'));
    }

    #Gera o PDF (É considerado os filtros que estao na tela)
    public function exportarPdf(Request $request)
    {
        $autoresAgrupados = $this->obterDadosFiltrados($request);

        // Reutilizamos a view antiga (agora renomeada para pdf.blade.php)
        $pdf = Pdf::loadView('relatorio.pdf', compact('autoresAgrupados'));

        return $pdf->stream('relatorio_livros.pdf');
    }
}
