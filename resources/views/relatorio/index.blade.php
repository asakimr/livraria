@extends('layouts.app')

@section('content')
    <x-sub-header title="Central de Relatórios" />

    <!-- Card de Filtros -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('relatorios.index') }}" method="GET" class="row align-items-end">

                <div class="col-12 col-xl-3 mb-3 mb-xl-0">
                    <label for="autor_id" class="form-label fw-semibold">Autor</label>
                    <select name="autor_id" id="autor_id" class="form-select">
                        <option value="">Todos os Autores</option>
                        @foreach($autores as $autor)
                            <option value="{{ $autor->CodAu }}" {{ request('autor_id') == $autor->CodAu ? 'selected' : '' }}>
                                {{ $autor->Nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-xl-3 mb-3 mb-xl-0">
                    <label for="assuntos" class="form-label fw-semibold">Assuntos</label>
                    <select name="assuntos[]" id="assuntos" class="form-control choices-multiple" multiple>
                        @foreach($assuntos as $assunto)
                            <!-- in_array verifica se o ID atual está dentro dos assuntos pesquisados -->
                            <option value="{{ $assunto->codAs }}" {{ in_array($assunto->codAs, request('assuntos', [])) ? 'selected' : '' }}>
                                {{ $assunto->Descricao }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-xl-3 mb-3 mb-xl-0">
                    <label for="titulo" class="form-label fw-semibold">Título do Livro</label>
                    <input type="text" name="titulo" id="titulo" class="form-control" placeholder="Buscar por título..." value="{{ request('titulo') }}">
                </div>

                <div class="col-12 col-xl-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="{{ route('relatorios.index') }}" class="btn btn-light border w-100">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Cabeçalho da Tabela com Botão de Exportar -->
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-secondary">Pré-visualização dos Dados</h5>

        <a href="{{ route('relatorios.exportar', request()->all()) }}" class="btn btn-danger text-nowrap" target="_blank">
            <i class="bi bi-file-earmark-pdf"></i> Exportar para PDF
        </a>
    </div>

    <!-- Tabela de Preview -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @forelse($autoresAgrupados as $autorId => $livros)
                <div class="bg-light p-3 border-bottom border-top">
                    <h6 class="mb-0 text-primary fw-bold"><i class="bi bi-person-lines-fill me-2"></i>{{ $livros->first()->Autor_Nome }}</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-borderless mb-0">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th style="width: 30%">Título</th>
                                <th style="width: 20%">Editora</th>
                                <th style="width: 15%">Edição/Ano</th>
                                <th style="width: 35%">Assuntos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($livros as $livro)
                                <tr class="border-bottom">
                                    <td class="fw-medium">{{ $livro->Livro_Titulo }}</td>
                                    <td>{{ $livro->Livro_Editora }}</td>
                                    <td>{{ $livro->Livro_Edicao }}ª / {{ $livro->Livro_Ano }}</td>
                                    <td class="text-muted">{{ $livro->Assuntos ?? 'Sem assunto' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @empty
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                    Nenhum registro encontrado para os filtros selecionados.
                </div>
            @endforelse
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectsMultiplos = document.querySelectorAll('.choices-multiple');

            selectsMultiplos.forEach(function (select) {
                new Choices(select, {
                    removeItemButton: true,
                    searchEnabled: true,
                    placeholder: true,
                    placeholderValue: 'Ex: Ficção Científica',
                    itemSelectText: 'Pressione Enter para selecionar',
                    noResultsText: 'Nenhum item encontrado',
                    removeItemLabelText: 'Remover item',
                    loadingText: 'Carregando...',
                    noChoicesText: "Opção indisponível.",
                    fuseOptions: {
                        threshold: 0.1,
                        distance: 1000
                    }
                });
            });

            const containerChoices = document.querySelectorAll('.choices__list--multiple');

            containerChoices.forEach(container => {
                let isDown = false;
                let startX;
                let scrollLeft;

                container.addEventListener('mousedown', (e) => {
                    isDown = true;
                    container.style.cursor = 'grabbing'; // Muda o mouse para uma mão fechada
                    startX = e.pageX - container.offsetLeft;
                    scrollLeft = container.scrollLeft;
                });

                container.addEventListener('mouseleave', () => {
                    isDown = false;
                    container.style.cursor = 'text';
                });

                container.addEventListener('mouseup', () => {
                    isDown = false;
                    container.style.cursor = 'text';
                });

                container.addEventListener('mousemove', (e) => {
                    if (!isDown) return;
                    e.preventDefault();
                    const x = e.pageX - container.offsetLeft;
                    const walk = (x - startX) * 1.5; // Multiplicador de velocidade do arraste
                    container.scrollLeft = scrollLeft - walk;
                });
            });
        });
    </script>
@endsection
