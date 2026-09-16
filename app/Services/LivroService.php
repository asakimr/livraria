<?php

namespace App\Services;

use App\Exceptions\RegraNegocioException;
use App\Models\Livro;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class LivroService{
    /** @return LengthAwarePaginator<int, Livro> */
    public function obter(int $porPagina = 10, ?string $buscar = null): LengthAwarePaginator {
        $query = Livro::with(['autores', 'assuntos'])->orderBy("Titulo", "asc");

        if ($buscar) {
            $query->where("Titulo", "like", "%{$buscar}%"); }

        return $query->paginate($porPagina);
    }

    /** @param array<string, mixed> $dados */
    public function salvar(array $dados): Livro{
        return DB::transaction(function () use ($dados) {
            $assuntosIds = $dados['assuntos'] ?? [];
            $autoresIds = $dados['autores'] ?? [];

            if (empty($assuntosIds) || empty($autoresIds)) {
                throw new RegraNegocioException("O livro precisa ter no mínimo um autor e um assunto.");
            }

            unset($dados['assuntos'], $dados['autores']);

            $livro = Livro::create($dados);

            $livro->assuntos()->sync($assuntosIds);

            $livro->autores()->sync($autoresIds);

            if ($livro->assuntos()->count() === 0 || $livro->autores()->count() === 0) {
                throw new RegraNegocioException("Erro ao vincular as relações, rollback aplicado.");
            }

            return $livro;
        });

    }

    /** @param array<string, mixed> $dados */
    public function atualizar(Livro $livro, array $dados): Livro{
        return DB::transaction(function () use ($livro, $dados) {

            $assuntosIds = $dados['assuntos'] ?? [];
            $autoresIds = $dados['autores'] ?? [];

            if (empty($assuntosIds) || empty($autoresIds)) {
                throw new RegraNegocioException("O livro precisa ter no mínimo um autor e um assunto.");
            }

            unset($dados['assuntos'], $dados['autores']);

            $livro->update($dados);

            $livro->assuntos()->sync($assuntosIds);
            $livro->autores()->sync($autoresIds);

            if ($livro->assuntos()->count() === 0 || $livro->autores()->count() === 0) {
                throw new RegraNegocioException("Erro ao vincular as relações, rollback aplicado.");
            }

            return $livro;
        });
    }

    public function excluir(Livro $livro): void{
        DB::transaction(function () use ($livro) {

            $livro->assuntos()->detach();
            $livro->autores()->detach();

            $livro->delete();
        });
    }
}
