<?php

namespace App\Http\Controllers;

use App\Http\Requests\AutorRequest;
use App\Http\Requests\BuscaRequest;
use App\Services\AutorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AutorController extends Controller
{
    protected AutorService $autorService;

    public function __construct(AutorService $autorService){
        $this->autorService = $autorService;
    }

    public function index(BuscaRequest $request): View{
        $busca = $request->string("search")->toString();

        $autores = $this->autorService->obter(10, $busca);

        return view("autor.index", compact("autores"));
    }

    public function store(AutorRequest $request): RedirectResponse{
        $this->autorService->salvar($request->validated());

        return redirect()->route("autor.index")->with("success", "Autor registrado com sucesso! ");
    }

    public function update(AutorRequest $request, int $id): RedirectResponse{
        $this->autorService->atualizar($id, $request->validated());

        return redirect()->route("autor.index")->with("success", "Autor atualizado com sucesso!!");
    }

    public function destroy(int $id): RedirectResponse{
        $this->autorService->excluir($id);

        return redirect()->route("autor.index")->with("success", "Autor apagado com sucesso!!");
    }
}
