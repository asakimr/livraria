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
                            data-ano="{{ $livro->AnoPublicacao }}"
                            data-valor="{{ $livro->Valor }}"
                            data-autores='{{ $livro->autores->pluck("CodAu") }}'
                            data-assuntos='{{ $livro->assuntos->pluck("codAs") }}'>
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
    <x-modal id="modalNovoLivro" title="Cadastrar Novo Livro" size="modal-lg">
        <form action="{{ route('livro.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_modal" value="modalNovoLivro">

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
                    <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" name="AnoPublicacao" id="AnoPublicacao" class="form-control" required maxlength="4" placeholder="Ex: 1954">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="Autores" class="form-label text-primary fw-semibold">Autores <span class="text-danger">*</span></label>
                    <select name="autores[]" id="Autores" class="form-control choices-multiple" multiple required>
                        @foreach($autores as $autor)
                            <option value="{{ $autor->CodAu }}">{{ $autor->Nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="Assuntos" class="form-label text-primary fw-semibold">Assuntos <span class="text-danger">*</span></label>
                    <select name="assuntos[]" id="Assuntos" class="form-control choices-multiple" multiple required>
                        @foreach($assuntos as $assunto)
                            <option value="{{ $assunto->codAs }}">{{ $assunto->Descricao }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="mb-3">
                    <label for="Valor" class="form-label text-primary fw-semibold">Valor do Livro <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold text-primary">R$</span>
                        <!-- Se for edição, converte o 59.90 do banco de volta para 59,90 visualmente -->
                        <input type="text" name="Valor" id="Valor" class="form-control mascara-dinheiro @error('Valor') is-invalid @enderror"
                            value=""
                            required placeholder="0,00">
                    </div>
                    @error('Valor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-secondary text-white">Salvar</button>
            </div>
        </form>
    </x-modal>

    {{-- Modal para editar os livros --}}
    <x-modal id="modalEditarLivro" title="Editar Livro" size="modal-lg">
        <form id="formEditarLivro" method="POST" action="{{ route('livro.update', 'ID_FALSO') }}" data-update-url="{{ route('livro.update', 'ID_FALSO') }}">
            <input type="hidden" name="_modal" value="modalEditarLivro">
            <input type="hidden" name="_registro" value="">
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
                    <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" name="AnoPublicacao" id="editAnoPublicacao" class="form-control" required maxlength=4 >
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="editAutores" class="form-label text-primary fw-semibold">Autores <span class="text-danger">*</span></label>
                    <select name="autores[]" id="editAutores" class="form-control choices-multiple" multiple required>
                        @foreach($autores as $autor)
                            <option value="{{ $autor->CodAu }}">{{ $autor->Nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="editAssuntos" class="form-label text-primary fw-semibold">Assuntos <span class="text-danger">*</span></label>
                    <select name="assuntos[]" id="editAssuntos" class="form-control choices-multiple" multiple required>
                        @foreach($assuntos as $assunto)
                            <option value="{{ $assunto->codAs }}">{{ $assunto->Descricao }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="mb-3">
                    <label for="editValor" class="form-label text-primary fw-semibold">Valor do Livro <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold text-primary">R$</span>
                        <!-- Se for edição, converte o 59.90 do banco de volta para 59,90 visualmente -->
                        <input type="text" name="Valor" id="editValor" class="form-control mascara-dinheiro @error('Valor') is-invalid @enderror"
                            value=""
                            required placeholder="0,00">
                    </div>
                    @error('Valor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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

            // 1. Inicializar o Choices.js e guardar as instâncias
            const escolhasInstances = {}; // Objeto para guardar as instâncias
            const selectsMultiplos = document.querySelectorAll('.choices-multiple');

            selectsMultiplos.forEach(function (select) {
                // Inicializa e guarda a instância usando o ID do select como chave
                select.escolhasInstance = escolhasInstances[select.id] = new Choices(select, {
                    removeItemButton: true,
                    searchEnabled: true,
                    placeholder: true,
                    placeholderValue: 'Selecione os itens...',
                    noResultsText: 'Nenhum item encontrado',
                    itemSelectText: 'Pressione Enter para selecionar',
                    removeItemLabelText: 'Remover item',
                    loadingText: 'Carregando...',
                    noChoicesText: "Opção de seleção não disponível.",
                    fuseOptions: {
                        threshold: 0.1,
                        distance: 1000
                    }
                });
            });

            // 2. Preencher o Modal de Edição
            const modalEditar = document.getElementById('modalEditarLivro');

            if (modalEditar) {
                modalEditar.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    if (!button) return; // A reabertura após erro mantém os dados recuperados.

                    // Coleta os dados simples
                    const id = button.getAttribute('data-id');
                    const titulo = button.getAttribute('data-titulo');
                    const editora = button.getAttribute('data-editora');
                    const edicao = button.getAttribute('data-edicao');
                    const ano = button.getAttribute('data-ano');
                    const valor = button.getAttribute('data-valor');

                    // Coleta os arrays do N:N (Autores e Assuntos)
                    const autoresIds = JSON.parse(button.getAttribute('data-autores') || '[]');
                    const assuntosIds = JSON.parse(button.getAttribute('data-assuntos') || '[]');

                    // Atualiza a Rota
                    const form = document.getElementById('formEditarLivro');
                    form.elements.namedItem('_registro').value = id;
                    const urlBase = form.action;
                    form.action = urlBase.replace(/livro\/\d+|livro\/ID_FALSO/, `livro/${id}`);

                    // Preenche os inputs de texto
                    document.getElementById('editTitulo').value = titulo;
                    document.getElementById('editEditora').value = editora;
                    document.getElementById('editEdicao').value = edicao;
                    document.getElementById('editAnoPublicacao').value = ano;

                    // Formata o valor monetário de volta para o padrão BR (opcional, caso tenha passado)
                    if(valor) {
                        document.getElementById('editValor').value = parseFloat(valor).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                    }

                    // Mágica do Choices.js para os selects múltiplos
                    const choicesAutores = escolhasInstances['editAutores'];
                    const choicesAssuntos = escolhasInstances['editAssuntos'];

                    if(choicesAutores && choicesAssuntos) {
                        // 1º Removemos as seleções anteriores (para não misturar se abrir 2 livros seguidos)
                        choicesAutores.removeActiveItems();
                        choicesAssuntos.removeActiveItems();

                        // 2º Convertemos os IDs para string (o HTML entende value como string)
                        const autoresFormatados = autoresIds.map(String);
                        const assuntosFormatados = assuntosIds.map(String);

                        // 3º Setamos os valores corretos no componente
                        choicesAutores.setChoiceByValue(autoresFormatados);
                        choicesAssuntos.setChoiceByValue(assuntosFormatados);
                    }
                });
            }

            // 3. Máscara de Dinheiro
            const inputsDinheiro = document.querySelectorAll('.mascara-dinheiro');

            inputsDinheiro.forEach(function(input) {
                input.addEventListener('input', function (e) {
                    let valor = e.target.value;
                    valor = valor.replace(/\D/g, "");
                    valor = (valor / 100).toFixed(2) + '';
                    valor = valor.replace(".", ",");
                    valor = valor.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                    e.target.value = valor;
                });
            });

        }); // FINAL DO EVENTO
    </script>

@endsection
