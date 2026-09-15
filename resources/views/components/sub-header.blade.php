@props(['title', 'route' => null, 'buttonLabel' => 'Novo', 'modalTarget' => null])

<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <div>
        <h2 class="h3 fw-bold text-primary mb-0">{{ $title }}</h2>
    </div>

    @if ($modalTarget)
        <!-- Se tiver modal, renderiza um botão -->
        <button type="button" class="btn btn-secondary text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#{{ $modalTarget }}">
            <i class="bi bi-plus-lg me-1"></i> {{ $buttonLabel }}
        </button>
    @elseif ($route)
        <!-- Se tiver rota, renderiza um link padrão -->
        <a href="{{ $route }}" class="btn btn-secondary text-white shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> {{ $buttonLabel }}
        </a>
    @endif
</div>
