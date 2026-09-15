<?php

namespace App\Services;

use App\Models\Livro;

use Illuminate\Pagination\LengthAwarePaginator;

class LivroService{
    public function obter(int $porPagina = 10, ?string $buscar = null ):LengthAwarePaginator {
        $query = Livro::orderBy("Titulo", "asc");

        if($buscar)
            $query->where("Titulo","like","%{$buscar}%");

        return $query->paginate($porPagina);
    }

    public function salvar(array $dados){
        return Livro::create($dados);
    }

    public function atualizar(int $id, array $dados){
        $livro = Livro::findOrFail($id);
        $livro->update($dados);

        return $livro;

    }

    public function excluir(int $id){
        $livro = Livro::findOrFail($id);
        $livro->delete();
    }
}
