@extends("layouts.app")

@section("content")
<x-alerts />

<x-sub-header
    title="Assunto"
    modal-target="modalNovoAssunto"
    button-label="Novo Assunto"
/>

<x-barra-busca
        :action="route('assunto.index')"
        placeholder="Buscar assunto pela descrição..."
        :value="$busca ?? ''"
/>

<x-card-table>
    <x-slot:thead>
        <tr>
            <th style="width: 80px;">ID</th>
            <th>Descrição</th>
            <th class="text-end" style="width: 140px;">Ações</th>
        </tr>
    </x-slot:thead>

    <x-slot:tbody>
        @forelse ($assuntos as $assunto)
            <tr>
                <td><span class="text-muted">#{{ $assunto->codAs }}</span></td>
                <td class="fw-semibold">{{ $assunto->Descricao }}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary me-1"
                        title="Editar"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditarAssunto"
                        data-id="{{ $assunto->codAs }}"
                        data-descricao="{{ $assunto->Descricao }}">
                        <i class="bi bi-pencil"></i>
                    </button>

                    <form action="{{ route('assunto.destroy', $assunto->codAs) }}" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente excluir?')">
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
                    Nenhum assunto cadastrado até o momento.
                </td>
            </tr>
        @endforelse
    </x-slot:tbody>

    <x-slot:footer>
        <div class="d-flex justify-content-end">
            {{ $assuntos->withQueryString()->links() }}
        </div>
    </x-slot:footer>
</x-card-table>


<x-modal id="modalNovoAssunto" title="Cadastrar Novo Assunto">
    <form action="{{ route('assunto.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="Descricao" class="form-label text-primary fw-semibold">Descrição do Assunto <span class="text-danger">*</span></label>
            <input type="text" name="Descricao" id="Descricao" class="form-control" required maxlength="20" placeholder="Ex: Ficção Ciêntifica">
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-secondary text-white">Salvar</button>
        </div>
    </form>
</x-modal>

<x-modal id="modalEditarAssunto" title="Editar Assunto">
    <form id="formEditarAssunto" method="POST" action="">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="editDescricao" class="form-label text-primary fw-semibold">Descrição do Assunto <span class="text-danger">*</span></label>
            <input type="text" name="Descricao" id="editDescricao" class="form-control" required maxlength="20">
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-secondary text-white">Atualizar</button>
        </div>
    </form>
</x-modal>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEditar = document.getElementById('modalEditarAssunto');

        if (modalEditar) {
            modalEditar.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;

                const id = button.getAttribute('data-id');

                const nome = button.getAttribute('data-descricao');

                const form = document.getElementById('formEditarAssunto');
                const inputNome = document.getElementById('editDescricao');

                form.action = `/assunto/${id}`;
                inputNome.value = nome;
            });
        }
    });
</script>

@endsection
