<?php

namespace App\Services;

use App\Exceptions\RegraNegocioException;
use App\Models\Autor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AutorService {
    /** @return LengthAwarePaginator<int, Autor> */
    public function obter(int $porPagina = 10, ?string $busca = null): LengthAwarePaginator {

        $query = Autor::orderBy("Nome", "asc");

        if ($busca) {
            $query->where("Nome", "like", "%{$busca}%"); }

        return $query->paginate($porPagina);
    }

    /** @return Collection<int, Autor> */
    public function obterParaSelect(): Collection{
        return Autor::orderBy("Nome", "asc")->get();
    }

    /** @param array<string, mixed> $dados */
    public function salvar(array $dados): Autor{
        return Autor::create($dados);
    }

    /** @param array<string, mixed> $dados */
    public function atualizar(int $id, array $dados): Autor{
        $autor = Autor::findOrFail($id);
        $autor->update($dados);

        return $autor;

    }

    public function excluir(int $id): void{
        DB::transaction(function () use ($id) {
            $registro = Autor::query()->lockForUpdate()->findOrFail($id);
            if ($registro->livros()->exists()) {
                throw new RegraNegocioException("Não é possível excluir o autor enquanto houver livros vinculados.");
            }
            $registro->delete();
        });
    }
}
