@props(['id', 'title', 'size'])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true"
    @if (old('_modal') === $id)
        data-recuperar-formulario="{{ json_encode(session()->getOldInput()) }}"
    @endif
>
    <div class="modal-dialog modal-dialog-centered {{ $size ?? '' }}">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-semibold" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body p-4">
                @if (old('_modal') === $id && $errors->any())
                    <div class="alert alert-danger" role="alert">
                        <strong>Verifique os campos abaixo:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
