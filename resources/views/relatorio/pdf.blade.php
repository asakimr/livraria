<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Livros por Autor</title>
    <style>
        /* 1. Margens da Página (Essencial para não encavalar o texto) */
        @page {
            margin: 80px 25px 50px 25px; /* Topo, Direita, Baixo, Esquerda */
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333333;
        }

        /* 2. Cabeçalho Fixo (Repete em todas as páginas) */
        header {
            position: fixed;
            top: -60px;
            left: 0px;
            right: 0px;
            height: 40px;
            border-bottom: 2px solid #201547; /* COR PRIMÁRIA */
            padding-bottom: 10px;
        }

        /* 3. Rodapé Fixo (Repete em todas as páginas) */
        footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 30px;
            border-top: 1px solid #dddddd;
            padding-top: 5px;
            font-size: 10px;
            color: #666666;
        }

        /* 4. Mágica do DOMPDF: Numeração automática de páginas */
        .page-number:after {
            content: "Página " counter(page) " de " counter(pages);
        }

        /* Tabelas Auxiliares de Layout */
        .layout-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }
        .layout-table td {
            border: none;
            padding: 0;
        }

        .titulo-relatorio {
            font-size: 18px;
            font-weight: bold;
            color: #201547; /* COR PRIMÁRIA */
            text-align: right;
            text-transform: uppercase;
        }

        /* 5. Estilos dos Dados */
        .autor-section {
            margin-bottom: 30px;
            page-break-inside: avoid; /* Tenta não quebrar a tabela do autor no meio da folha */
        }

        .autor-badge {
            background-color: #201547; /* COR PRIMÁRIA */
            color: #ffffff;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
        }

        .tabela-livros {
            width: 100%;
            border-collapse: collapse;
        }

        .tabela-livros th {
            background-color: #f8f9fa; /* Fundo cinza clarinho */
            color: #495057;
            font-size: 11px;
            text-transform: uppercase;
            text-align: left;
            padding: 10px 8px;
            border: 1px solid #dee2e6;
        }

        .tabela-livros td {
            padding: 8px;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }

        /* Linhas zebradas para facilitar a leitura */
        .tabela-livros tr:nth-child(even) td {
            background-color: #fdfdfd;
        }

        .livro-titulo {
            font-weight: bold;
            color: #212529;
        }

        .assuntos-texto {
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>

    <!-- O HEADER fica no topo absoluto. O DOMPDF repete ele em novas páginas -->
    <header>
        <table class="layout-table">
            <tr>
                <td style="font-size: 14px; font-weight: bold; color: #555;">
                    SGL - Sistema de Gestão de Livraria
                </td>
                <td class="titulo-relatorio">
                    Relatório de Livros
                </td>
            </tr>
        </table>
    </header>

    <!-- O FOOTER fica no final da folha. -->
    <footer>
        <table class="layout-table">
            <tr>
                <td>Gerado em: {{ date('d/m/Y \à\s H:i') }}</td>
                <td style="text-align: right;" class="page-number"></td>
            </tr>
        </table>
    </footer>

    <!-- A tag MAIN vai empurrar o conteúdo automaticamente para baixo do Header -->
    <main>
        @forelse($autoresAgrupados as $autorNome => $livros)
            <div class="autor-section">
                <!-- Faixa com o Nome do Autor -->
                <div class="autor-badge">
                    {{ $autorNome }}
                </div>

                <!-- Tabela de Livros daquele Autor -->
                <table class="tabela-livros">
                    <thead>
                        <tr>
                            <th style="width: 35%">Título</th>
                            <th style="width: 20%">Editora</th>
                            <th style="width: 15%">Edição / Ano</th>
                            <th style="width: 30%">Assuntos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($livros as $livro)
                            <tr>
                                <td class="livro-titulo">{{ $livro->Livro_Titulo }}</td>
                                <td>{{ $livro->Livro_Editora }}</td>
                                <td>{{ $livro->Livro_Edicao }}ª / {{ $livro->Livro_Ano }}</td>
                                <td class="assuntos-texto">{{ $livro->Assuntos ?? 'Sem assunto vinculado' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div style="text-align: center; padding: 40px; color: #888;">
                <p>Nenhum registro encontrado para os filtros selecionados.</p>
            </div>
        @endforelse
    </main>

</body>
</html>
