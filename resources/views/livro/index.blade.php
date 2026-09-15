@extends('layouts.app')

@section('content')
    <!-- Feedback de Sucesso/Erro -->
    <x-alerts />

    <!-- Topo da tela com botão 'Novo' -->
    <x-sub-header
        title="Livros"
        modal-target="modalNovoLivro"
        button-label="Novo Livro"
    />

    <x-barra-busca
        :action="route('livro.index')"
        placeholder="Buscar livro por título..."
        :value="$busca ?? ''"
    />

    <!-- Listagem em Tabela -->
    <x-card-table>
        <x-slot:thead>
            <tr>
                <th style="width: 80px;">ID</th>
                <th>Título</th>
                <th>Editora</th>
                <th>Edição</th>
                <th>Ano</th>
                <th class="text-end" style="width: 140px;">Ações</th>
            </tr>
        </x-slot:thead>

        <x-slot:tbody>
            @forelse ($livros as $livro)
                <tr>
                    <td><span class="text-muted">#{{ $livro->Codl }}</span></td>
                    <td class="fw-semibold">{{ $livro->Titulo }}</td>
                    <td>{{ $livro->Editora }}</td>
                    <td>{{ $livro->Edicao }}ª</td>
                    <td>{{ $livro->AnoPublicacao }}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary me-1"
                            title="Editar"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditarLivro"
                            data-id="{{ $livro->Codl }}"
                            data-titulo="{{ $livro->Titulo }}"
                            data-editora="{{ $livro->Editora }}"
                            data-edicao="{{ $livro->Edicao }}"
                            data-ano="{{ $livro->AnoPublicacao }}">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <form action="{{ route('livro.destroy', $livro->Codl) }}" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente excluir este livro?')">
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
                    <td colspan="6" class="text-center py-4 text-muted">
                        Nenhum livro cadastrado até o momento.
                    </td>
                </tr>
            @endforelse
        </x-slot:tbody>

        <x-slot:footer>
            <div class="d-flex justify-content-end">
                {{ $livros->withQueryString()->links() }}
            </div>
        </x-slot:footer>
    </x-card-table>

    {{-- Modal para adicionar novos livros --}}
    <x-modal id="modalNovoLivro" title="Cadastrar Novo Livro">
        <form action="{{ route('livro.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="Titulo" class="form-label text-primary fw-semibold">Título <span class="text-danger">*</span></label>
                <input type="text" name="Titulo" id="Titulo" class="form-control" required maxlength="40" placeholder="Ex: O Senhor dos Anéis">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="Editora" class="form-label text-primary fw-semibold">Editora <span class="text-danger">*</span></label>
                    <input type="text" name="Editora" id="Editora" class="form-control" required maxlength="40" placeholder="Ex: HarperCollins">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="Edicao" class="form-label text-primary fw-semibold">Edição <span class="text-danger">*</span></label>
                    <input type="number" name="Edicao" id="Edicao" class="form-control" required placeholder="Ex: 1">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="AnoPublicacao" class="form-label text-primary fw-semibold">Ano <span class="text-danger">*</span></label>
                    <input type="number" name="AnoPublicacao" id="AnoPublicacao" class="form-control" required placeholder="Ex: 1954">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-secondary text-white">Salvar</button>
            </div>
        </form>
    </x-modal>

    {{-- Modal para editar os livros --}}
    <x-modal id="modalEditarLivro" title="Editar Livro">
        <form id="formEditarLivro" method="POST" action="{{ route('livro.update', 'ID_FALSO') }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="editTitulo" class="form-label text-primary fw-semibold">Título <span class="text-danger">*</span></label>
                <input type="text" name="Titulo" id="editTitulo" class="form-control" required maxlength="40">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="editEditora" class="form-label text-primary fw-semibold">Editora <span class="text-danger">*</span></label>
                    <input type="text" name="Editora" id="editEditora" class="form-control" required maxlength="40">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="editEdicao" class="form-label text-primary fw-semibold">Edição <span class="text-danger">*</span></label>
                    <input type="number" name="Edicao" id="editEdicao" class="form-control" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="editAnoPublicacao" class="form-label text-primary fw-semibold">Ano <span class="text-danger">*</span></label>
                    <input type="number" name="AnoPublicacao" id="editAnoPublicacao" class="form-control" required>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-secondary text-white">Atualizar</button>
            </div>
        </form>
    </x-modal>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEditar = document.getElementById('modalEditarLivro');

            if (modalEditar) {
                modalEditar.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;

                    // Coleta os dados do botão
                    const id = button.getAttribute('data-id');
                    const titulo = button.getAttribute('data-titulo');
                    const editora = button.getAttribute('data-editora');
                    const edicao = button.getAttribute('data-edicao');
                    const ano = button.getAttribute('data-ano');

                    // Seleciona o form e os inputs
                    const form = document.getElementById('formEditarLivro');

                    // Magia da rota segura: troca o ID_FALSO pelo ID real
                    const urlBase = form.action;
                    // Previne que a URL fique com múltiplos IDs se o modal for aberto várias vezes
                    form.action = urlBase.replace(/livro\/\d+|livro\/ID_FALSO/, `livro/${id}`);

                    // Preenche os campos
                    document.getElementById('editTitulo').value = titulo;
                    document.getElementById('editEditora').value = editora;
                    document.getElementById('editEdicao').value = edicao;
                    document.getElementById('editAnoPublicacao').value = ano;
                });
            }
        });
    </script>
@endsection
