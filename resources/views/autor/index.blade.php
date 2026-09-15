@extends('layouts.app')

@section('content')
    <!-- Feedback de Sucesso/Erro -->
    <x-alerts />

    <!-- Topo da tela com botão 'Novo' -->
    <x-sub-header
        title="Autores"
        modal-target="modalNovoAutor"
        button-label="Novo Autor"
    />

    <x-barra-busca
            :action="route('autor.index')"
            placeholder="Buscar autor por nome..."
            :value="$busca ?? ''"
    />

    <!-- Listagem em Tabela -->
    <x-card-table>
        <x-slot:thead>
            <tr>
                <th style="width: 80px;">ID</th>
                <th>Nome</th>
                <th class="text-end" style="width: 140px;">Ações</th>
            </tr>
        </x-slot:thead>

        <x-slot:tbody>
            @forelse ($autores as $autor)
                <tr>
                    <td><span class="text-muted">#{{ $autor->CodAu }}</span></td>
                    <td class="fw-semibold">{{ $autor->Nome }}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary me-1"
                            title="Editar"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditarAutor"
                            data-id="{{ $autor->CodAu }}"
                            data-nome="{{ $autor->Nome }}">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <form action="{{ route('autor.destroy', $autor->CodAu) }}" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente excluir?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center py-4 text-muted">
                        Nenhum autor cadastrado até o momento.
                    </td>
                </tr>
            @endforelse
        </x-slot:tbody>

        <x-slot:footer>
            <div class="d-flex justify-content-end">
                {{ $autores->withQueryString()->links() }}
            </div>
        </x-slot:footer>
    </x-card-table>

    {{-- Modal para adicionar novos autores --}}
    <x-modal id="modalNovoAutor" title="Cadastrar Novo Autor">
        <form action="{{ route('autor.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="Nome" class="form-label text-primary fw-semibold">Nome do Autor <span class="text-danger">*</span></label>
                <input type="text" name="Nome" id="Nome" class="form-control" required maxlength="40" placeholder="Ex: Machado de Assis">
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-secondary text-white">Salvar</button>
            </div>
        </form>
    </x-modal>

    {{-- Modal para editar os autores --}}

    <x-modal id="modalEditarAutor" title="Editar Autor">
        <form id="formEditarAutor" method="POST" action="">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="editNome" class="form-label text-primary fw-semibold">Nome do Autor <span class="text-danger">*</span></label>
                <input type="text" name="Nome" id="editNome" class="form-control" required maxlength="40">
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-secondary text-white">Atualizar</button>
            </div>
        </form>
    </x-modal>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEditar = document.getElementById('modalEditarAutor');

            if (modalEditar) {
                modalEditar.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;

                    const id = button.getAttribute('data-id');
                    const nome = button.getAttribute('data-nome');

                    const form = document.getElementById('formEditarAutor');
                    const inputNome = document.getElementById('editNome');

                    form.action = `/autor/${id}`;
                    inputNome.value = nome;
                });
            }
        });
    </script>
@endsection
