<?php

namespace App\Services;

use App\Exceptions\RegraNegocioException;
use App\Models\Assunto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AssuntoService{
    /** @return LengthAwarePaginator<int, Assunto> */
    public function obter(int $porPagina = 10, ?string $busca = null): LengthAwarePaginator{
        $query = Assunto::orderBy("Descricao", "asc");

        if ($busca){
            $query->where("Descricao", "like", "%{$busca}%");
        }

        return $query->paginate($porPagina);
    }

    /** @return Collection<int, Assunto> */
    public function obterParaSelect(): Collection{
        return Assunto::orderBy("Descricao", "asc")->get();
    }

    /** @param array<string, mixed> $dados */
    public function salvar(array $dados): Assunto{
        return Assunto::create($dados);
    }

    /** @param array<string, mixed> $dados */
    public function atualizar(int $id, array $dados): Assunto{
        $assunto = Assunto::findOrFail($id);
        $assunto->update($dados);

        return $assunto;
    }

    public function excluir(int $id): void{
        DB::transaction(function () use ($id) {
            $registro = Assunto::query()->lockForUpdate()->findOrFail($id);
            if ($registro->livros()->exists()) {
                throw new RegraNegocioException("Não é possível excluir o assunto enquanto houver livros vinculados.");
            }
            $registro->delete();
        });
    }
}
