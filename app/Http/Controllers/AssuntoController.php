<?php

namespace App\Http\Controllers;

use App\Models\Assunto;
use Illuminate\Http\Request;
use App\Http\Requests\AssuntoRequest;
use App\Services\AssuntoService;

class AssuntoController extends Controller
{
    protected AssuntoService $assuntoService;

    public function __construct(AssuntoService $assuntoService){
        $this->assuntoService=$assuntoService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $busca = $request->query("search");

        $assuntos = $this->assuntoService->obter(10, $busca);

        return view("assunto.index", compact("assuntos"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AssuntoRequest $request)
    {
        $this->assuntoService->salvar($request->validated());
        return redirect()->route("assunto.index")->with("success", "Assunto registrado com sucesso!! ");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AssuntoRequest $request, $id)
    {
        $this->assuntoService->atualizar($id, $request->validated());
        return redirect()->route("assunto.index")->with("success", "Assunto atualizado com sucesso!!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->assuntoService->excluir($id);
        return redirect()->route("assunto.index")->with("success", "Assunto apagado com sucesso!!");
    }
}
