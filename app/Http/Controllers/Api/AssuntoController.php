<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssuntoRequest;
use App\Http\Requests\BuscaRequest;
use App\Models\Assunto;
use App\Services\AssuntoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AssuntoController extends Controller
{
    public function __construct(protected AssuntoService $assuntoService) {}

    public function index(BuscaRequest $request): JsonResponse
    {
        return response()->json($this->assuntoService->obter(10, $request->string('search')->toString()));
    }

    public function store(AssuntoRequest $request): JsonResponse
    {
        $assunto = $this->assuntoService->salvar($request->validated());

        return response()->json(['data' => $assunto], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['data' => Assunto::findOrFail($id)]);
    }

    public function update(AssuntoRequest $request, int $id): JsonResponse
    {
        $assunto = $this->assuntoService->atualizar($id, $request->validated());

        return response()->json(['data' => $assunto]);
    }

    public function destroy(int $id): Response
    {
        $this->assuntoService->excluir($id);

        return response()->noContent();
    }
}
