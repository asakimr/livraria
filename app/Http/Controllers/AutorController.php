<?php

namespace App\Http\Controllers;

use App\Services\AutorService;
use App\Http\Requests\AutorRequest;
use Illuminate\Http\Request;

class AutorController extends Controller
{
    protected AutorService $autorService;

    public function __construct(AutorService $autorService){
        $this->autorService = $autorService;
    }

    public function index(Request $request){
        $busca = $request->query("search");

        $autores = $this->autorService->obter(10, $busca);

        return view("autor.index", compact("autores"));
    }

    public function store(AutorRequest $request){
        $this->autorService->salvar($request->validated());

        return redirect()->route("autor.index")->with("success", "Autor registrado com sucesso! ");
    }

    public function update(AutorRequest $request, $id){
        $this->autorService->atualizar($id, $request->validated());
        return redirect()->route("autor.index")->with("success", "Autor atualizado com sucesso!!");
    }

    public function destroy($id){
        $this->autorService->excluir($id);
        return redirect()->route("autor.index")->with("success", "Autor apagado com sucesso!!");
    }
}
