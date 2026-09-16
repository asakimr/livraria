# Livraria

Aplicação do desafio técnico PHP: cadastro, consulta, edição e exclusão de livros, autores e assuntos; relatório por autor, consultando uma view e exportando PDF.

Stack: PHP 8.3+, Laravel 13, Blade, Bootstrap, Choices.js, DomPDF, MySQL 8 (Docker) ou SQLite (desenvolvimento/testes), Node 22.12+ e Composer 2.

## Executar localmente

Em um checkout novo, com PHP e extensões intl, mbstring, PDO SQLite/MySQL, DOM/XML e ZIP:

```bash
composer setup
composer dev
```

Acesse http://localhost:8777. O setup instala as dependências dos arquivos lock, cria `.env` se necessário, gera a chave apenas quando vazia, prepara SQLite, aplica migrations e compila os assets. Antes de executar em uma instalação existente, confira o banco configurado em `.env`: o comando aplica migrations nesse banco. Não executa seed nem apaga registros.

Para usar Docker com MySQL, siga [implantação](docs/implantacao.md). Não é preciso instalar PHP ou Node no host nesse fluxo.

## Organização

- Controllers web: telas e redirecionamentos; controllers de API: respostas JSON.
- FormRequests: validação e preparação da entrada.
- Services: operações de negócio e transações.
- Models: tabelas e relacionamentos.
- View SQL: leitura do relatório; Blade/DomPDF: apresentação.
- Testes: integração/funcionais e unidades isoladas.

A API está em `/api/livros`, `/api/autores` e `/api/assuntos`. Não requer token nem adiciona tabelas de autenticação. Foi mantida aberta no escopo demonstrativo do desafio; a interface web existente também permite operações sem login.

## Verificações

```bash
composer test
npm run build
```

`composer test` verifica Pint, PHPStan/Larastan e Pest. `php artisan test` executa apenas os testes, sem substituir as outras verificações. A configuração padrão dos testes utiliza SQLite em memória.

## Documentação

- [Banco de dados, objetos e decisões](docs/banco-de-dados.md)
- [API e exemplos de requisição](docs/api.md)
- [Instalação, Docker, CI e implantação](docs/implantacao.md)
- [Qualidade e testes](docs/qualidade.md)
- [Backlog, critérios de aceite e pendências](docs/backlog.md)

O ano de publicação permanece inteiro por decisão do projeto, com justificativa documentada.
