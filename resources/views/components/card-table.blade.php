<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light border-bottom">
                    {{ $thead }}
                </thead>
                <tbody>
                    {{ $tbody }}
                </tbody>
            </table>
        </div>
    </div>

    @isset($footer)
        <div class="card-footer bg-white border-top pt-3 pb-1">
            {{ $footer }}
        </div>
    @endisset
</div>
