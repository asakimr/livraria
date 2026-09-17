@extends('layouts.app')

@section('content')
    <section class="card border-0 shadow-sm mb-4" aria-labelledby="boas-vindas">
        <div class="card-body p-4 p-lg-5">
            <i class="bi bi-book text-primary fs-1 d-block mb-3" aria-hidden="true"></i>
            <h1 id="boas-vindas" class="h2 text-primary fw-bold mb-3">Bem-vindo à Livraria</h1>
            <p class="fs-5 mb-2">Gerencie livros, autores e assuntos e consulte relatórios organizados por autor.</p>
            <p class="text-muted mb-4">Utilize o menu lateral para acessar as funcionalidades.</p>
            <p class="border-start border-primary border-3 ps-3 mb-0">
                <strong>Por onde começar?</strong><br>
                Para cadastrar seu primeiro livro, cadastre antes os autores e assuntos.
            </p>
        </div>
    </section>

    <section class="card border-0 shadow-sm" aria-labelledby="sobre-projeto">
        <div class="card-body p-4">
            <h2 id="sobre-projeto" class="h5 text-primary fw-bold mb-3">Sobre o projeto</h2>
            <p class="text-muted">Consulte o código-fonte e a documentação para conhecer a aplicação.</p>
            <a href="https://github.com/asakimr/livraria" class="btn btn-outline-primary mb-4"
                target="_blank" rel="noopener noreferrer" aria-describedby="links-externos">
                <i class="bi bi-github me-2" aria-hidden="true"></i>Repositório no GitHub
                <i class="bi bi-box-arrow-up-right ms-2" aria-hidden="true"></i>
            </a>
            <ul class="list-unstyled d-flex flex-wrap gap-3 mb-3">
                <li><a href="https://github.com/asakimr/livraria/blob/main/docs/api.md" target="_blank" rel="noopener noreferrer" aria-describedby="links-externos">API</a></li>
                <li><a href="https://github.com/asakimr/livraria/blob/main/docs/banco-de-dados.md" target="_blank" rel="noopener noreferrer" aria-describedby="links-externos">Banco de dados</a></li>
                <li><a href="https://github.com/asakimr/livraria/blob/main/docs/implantacao.md" target="_blank" rel="noopener noreferrer" aria-describedby="links-externos">Implantação</a></li>
                <li><a href="https://github.com/asakimr/livraria/blob/main/docs/qualidade.md" target="_blank" rel="noopener noreferrer" aria-describedby="links-externos">Qualidade e testes</a></li>
            </ul>
            <p id="links-externos" class="small text-muted mb-0">Os links do GitHub abrem em uma nova aba.</p>
        </div>
    </section>
@endsection
