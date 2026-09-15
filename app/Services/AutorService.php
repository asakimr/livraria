<?php

namespace App\Services;

use App\Models\Autor;

use Illuminate\Pagination\LengthAwarePaginator;

class AutorService {

    public function obter(int $porPagina = 10, ?string $busca = null): LengthAwarePaginator {

        $query = Autor::orderBy("Nome", "asc");

        if($busca)
            $query->where("Nome","like","%{$busca}%");

        return $query->paginate($porPagina);
    }

    public function salvar(array $dados){
        return Autor::create($dados);
    }

    public function atualizar(int $id, array $dados){
        $autor = Autor::findOrFail($id);
        $autor->update($dados);

        return $autor;

    }

    public function excluir(int $id){
        $autor = Autor::findOrFail($id);
        $autor->delete();
    }
}
