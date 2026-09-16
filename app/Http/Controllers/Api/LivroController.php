<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BuscaRequest;
use App\Http\Requests\LivroRequest;
use App\Models\Livro;
use App\Services\LivroService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LivroController extends Controller
{
    public function __construct(protected LivroService $livroService) {}

    public function index(BuscaRequest $request): JsonResponse
    {
        return response()->json($this->livroService->obter(10, $request->string('search')->toString()));
    }

    public function store(LivroRequest $request): JsonResponse
    {
        $livro = $this->livroService->salvar($request->validated());

        return response()->json(['data' => $livro->load(['autores', 'assuntos'])], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['data' => Livro::with(['autores', 'assuntos'])->findOrFail($id)]);
    }

    public function update(LivroRequest $request, int $id): JsonResponse
    {
        $livro = $this->livroService->atualizar(Livro::findOrFail($id), $request->validated());

        return response()->json(['data' => $livro->load(['autores', 'assuntos'])]);
    }

    public function destroy(int $id): Response
    {
        $this->livroService->excluir(Livro::findOrFail($id));

        return response()->noContent();
    }
}
