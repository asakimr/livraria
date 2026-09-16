<?php

namespace App\Http\Controllers;

use App\Exceptions\RegraNegocioException;
use App\Http\Requests\BuscaRequest;
use App\Http\Requests\LivroRequest;
use App\Models\Livro;
use App\Services\AssuntoService;
use App\Services\AutorService;
use App\Services\LivroService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LivroController extends Controller
{
    protected LivroService $livroService;

    protected AssuntoService $assuntoService;

    protected AutorService $autorService;

    public function __construct(LivroService $livroService, AutorService $autorService, AssuntoService $assuntoService){
        $this->livroService = $livroService;
        $this->autorService = $autorService;
        $this->assuntoService = $assuntoService;
    }

    public function index(BuscaRequest $request): View{
        $busca = $request->string("search")->toString();

        $livros = $this->livroService->obter(10, $busca);

        $autores = $this->autorService->obterParaSelect();
        $assuntos = $this->assuntoService->obterParaSelect();

        return view("livro.index", compact("livros", "busca", "autores", "assuntos"));
    }

    public function store(LivroRequest $request): RedirectResponse{
        try {
            $this->livroService->salvar($request->validated());

            return redirect()->route("livro.index")->with("success", "Livro registrado com sucesso! ");
        } catch (RegraNegocioException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (QueryException $e) {
            report($e);

            return redirect()->back()->withInput()->with('error', 'Erro interno no banco de dados. Não foi possível criar o livro.');
        } catch (Exception $e) {
            report($e);

            return redirect()->back()->withInput()->with('error', 'Não foi possível salvar o livro. Tente novamente.');
        }
    }

    public function update(LivroRequest $request, int $id): RedirectResponse{
        try {
            $livro = Livro::findOrFail($id);

            $this->livroService->atualizar($livro, $request->validated());

            return redirect()->route("livro.index")->with("success", "Livro atualizado com sucesso!!");

        } catch (ModelNotFoundException $e) {
            return redirect()->route('livro.index')->with('error', 'O livro que você tentou editar não foi encontrado no sistema.');
        } catch (RegraNegocioException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (QueryException $e) {
            report($e);

            return redirect()->back()->withInput()->with('error', 'Erro interno no banco de dados. Não foi possível salvar as alterações.');
        } catch (Exception $e) {
            report($e);

            return redirect()->back()->withInput()->with('error', 'Não foi possível salvar o livro. Tente novamente.');
        }
    }

    public function destroy(int $id): RedirectResponse{
        try {
            $livro = Livro::findOrFail($id);

            $this->livroService->excluir($livro);

            return redirect()->route('livro.index')->with('success', 'Livro excluído com sucesso!');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('livro.index')->with('error', 'O livro que você tentou excluir já não existe no sistema.');

        } catch (RegraNegocioException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (QueryException $e) {
            report($e);

            return redirect()->route('livro.index')->with('error', 'Erro interno no banco de dados. Não foi possível excluir o livro.');
        } catch (Exception $e) {
            report($e);

            return redirect()->route('livro.index')->with('error', 'Não foi possível excluir o livro. Tente novamente.');
        }
    }
}
