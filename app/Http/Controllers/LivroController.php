<?php

namespace App\Http\Controllers;

use App\Http\Requests\LivroRequest;
use App\Models\Assunto;
use App\Models\Autor;
use App\Models\Livro;
use App\Services\AssuntoService;
use App\Services\AutorService;
use App\Services\LivroService;

use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;

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

    public function index(Request $request){
        $busca = $request->query("search");

        $livros = $this->livroService->obter(10, $busca);

        $autores = $this->autorService->obterParaSelect();
        $assuntos = $this->assuntoService->obterParaSelect();

        return view("livro.index", compact("livros","busca", "autores", "assuntos"));
    }

    public function store(LivroRequest $request){
        try{
            $this->livroService->salvar($request->validated());

            return redirect()->route("livro.index")->with("success", "Livro registrado com sucesso! ");
        } catch (QueryException $e) {
            return redirect()->back()->withInput()->with('error', 'Erro interno no banco de dados. Não foi possível criar o livro.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function update(LivroRequest $request, $id){
        try{
            $livro = Livro::findOrFail($id);#possivel erro generico#

            $this->livroService->atualizar($livro, $request->validated());

            return redirect()->route("livro.index")->with("success", "Livro atualizado com sucesso!!");

        }catch (ModelNotFoundException $e) {
            return redirect()->route('livro.index')->with('error', 'O livro que você tentou editar não foi encontrado no sistema.');
        } catch (QueryException $e) {
            return redirect()->back()->withInput()->with('error', 'Erro interno no banco de dados. Não foi possível salvar as alterações.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(int $id){
        try{
            $livro = Livro::findOrFail($id);

            $this->livroService->excluir($livro);
            return redirect()->route('livro.index')->with('success', 'Livro excluído com sucesso!');
        }catch (ModelNotFoundException $e) {
            return redirect()->route('livro.index')->with('error', 'O livro que você tentou excluir já não existe no sistema.');

        } catch (QueryException $e) {
            return redirect()->route('livro.index')->with('error', 'Erro interno no banco de dados. Não foi possível excluir o livro.');
        } catch (Exception $e) {
            return redirect()->route('livro.index')->with('error', 'Opss! Houve um erro ao deletar livro.: ' . $e->getMessage());
        }
    }
}
