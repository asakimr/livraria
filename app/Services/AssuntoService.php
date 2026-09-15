<?php

namespace App\Services;

use App\Models\Assunto;

use Illuminate\Pagination\LengthAwarePaginator;

class AssuntoService{
    public function obter(int $porPagina = 10, ?string $busca = null ): LengthAwarePaginator{
        $query = Assunto::orderBy("Descricao", "asc");

        if($busca){
            $query->where("Descricao","like","%{$busca}%");
        }

        return $query->paginate($porPagina);
    }

    public function salvar(array $dados){
        return Assunto::create($dados);
    }

    public function atualizar(int $id, array $dados){
        $assunto = Assunto::findOrFail($id);
        $assunto->update($dados);

        return $assunto;
    }

    public function excluir(int $id){
        $assunto = Assunto::findOrFail($id);
        $assunto->delete();
    }
}
