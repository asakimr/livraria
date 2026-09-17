@props([
    'action',
    'placeholder' => 'Buscar...',
    'value' => ''
])

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ $action }}" method="GET" class="d-flex flex-column flex-sm-row gap-2">
            <input type="text" name="search" class="form-control" placeholder="{{ $placeholder }}" value="{{ $value }}">

            <button type="submit" class="btn btn-primary text-white text-nowrap px-4">
                <i class="bi bi-search"></i> Buscar
            </button>

            @if(isset($value) && $value !== '')
                <a href="{{ $action }}" class="btn btn-light border px-4">Limpar</a>
            @endif
        </form>
    </div>
</div>
