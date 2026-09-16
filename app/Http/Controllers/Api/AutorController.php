<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AutorRequest;
use App\Http\Requests\BuscaRequest;
use App\Models\Autor;
use App\Services\AutorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AutorController extends Controller
{
    public function __construct(protected AutorService $autorService) {}

    public function index(BuscaRequest $request): JsonResponse
    {
        return response()->json($this->autorService->obter(10, $request->string('search')->toString()));
    }

    public function store(AutorRequest $request): JsonResponse
    {
        $autor = $this->autorService->salvar($request->validated());

        return response()->json(['data' => $autor], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['data' => Autor::findOrFail($id)]);
    }

    public function update(AutorRequest $request, int $id): JsonResponse
    {
        $autor = $this->autorService->atualizar($id, $request->validated());

        return response()->json(['data' => $autor]);
    }

    public function destroy(int $id): Response
    {
        $this->autorService->excluir($id);

        return response()->noContent();
    }
}
