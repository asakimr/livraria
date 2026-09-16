<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssuntoRequest;
use App\Http\Requests\BuscaRequest;
use App\Services\AssuntoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssuntoController extends Controller
{
    protected AssuntoService $assuntoService;

    public function __construct(AssuntoService $assuntoService){
        $this->assuntoService = $assuntoService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(BuscaRequest $request): View
    {
        $busca = $request->string("search")->toString();

        $assuntos = $this->assuntoService->obter(10, $busca);

        return view("assunto.index", compact("assuntos", "busca"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AssuntoRequest $request): RedirectResponse
    {
        $this->assuntoService->salvar($request->validated());

        return redirect()->route("assunto.index")->with("success", "Assunto registrado com sucesso!! ");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AssuntoRequest $request, int $id): RedirectResponse
    {
        $this->assuntoService->atualizar($id, $request->validated());

        return redirect()->route("assunto.index")->with("success", "Assunto atualizado com sucesso!!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->assuntoService->excluir($id);

        return redirect()->route("assunto.index")->with("success", "Assunto apagado com sucesso!!");
    }
}
