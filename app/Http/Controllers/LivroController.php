<?php

namespace App\Http\Controllers;

use App\Http\Requests\LivroRequest;
use App\Models\Assunto;
use App\Models\Autor;
use App\Services\AssuntoService;
use App\Services\AutorService;
use App\Services\LivroService;

use Illuminate\Http\Request;

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
        $this->livroService->salvar($request->validated());

        return redirect()->route("livro.index")->with("success", "Livro registrado com sucesso! ");
    }

    public function update(LivroRequest $request, $id){
        $this->livroService->atualizar($id, $request->validated());
        return redirect()->route("livro.index")->with("success", "Livro atualizado com sucesso!!");
    }

    public function destroy($id){
        $this->livroService->excluir($id);
        return redirect()->route("livro.index")->with("success", "Livro apagado com sucesso!!");
    }
}
