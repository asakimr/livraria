<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livraria Spassu</title>

    <!-- Ícones do Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Vite -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar shadow-lg">
        <!-- Logo / Brand -->
        <a href="/" class="text-decoration-none p-4 d-flex flex-column">
            <span class="text-white opacity-75" style="font-size: 0.9rem; line-height: 1; text-transform: uppercase; letter-spacing: 1px;">Livraria</span>
            <span class="text-secondary fw-bold" style="font-size: 2rem; line-height: 1;">Spassu</span>
        </a>

        <!-- Menu -->
        <ul class="nav flex-column sidebar-nav mt-2">
            <li class="nav-item">
                <a class="nav-link active" href="#"><i class="bi bi-house-door"></i> Início</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="bi bi-book"></i> Livros</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="bi bi-person-lines-fill"></i> Autores</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="bi bi-tags"></i> Assuntos</a>
            </li>

            <hr class="border-secondary mx-3 my-3">

            <li class="nav-item">
                <a class="nav-link text-secondary" href="#"><i class="bi bi-file-earmark-bar-graph"></i> Relatório</a>
            </li>
        </ul>
    </aside>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="main-content">
        @yield('content')
    </main>

</body>
</html>
