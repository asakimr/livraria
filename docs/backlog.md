# Backlog da entrega

Registro do estado observado do projeto em 16/09/2026. Este documento organiza funcionalidades, critérios de aceite e evidências atuais; não representa um histórico retroativo de planejamento.

## Funcionalidades concluídas

- [x] **B01 — Cadastro de livros, autores e assuntos.** Listar, buscar, paginar, criar, editar e excluir pela interface web; manter relações de livros e limpar vínculos ao excluir um livro. Evidências: [LivroControllerTest](../tests/Feature/LivroControllerTest.php), [AutorControllerTest](../tests/Feature/AutorControllerTest.php) e [AssuntoControllerTest](../tests/Feature/AssuntoControllerTest.php).
- [x] **B02 — API JSON.** Disponibilizar CRUD dos três recursos, receber preço decimal, validar campos e listas de IDs, responder erros em português e não expor detalhes internos. Evidência: [ApiCrudTest](../tests/Feature/ApiCrudTest.php). A API é aberta no escopo demonstrativo.
- [x] **B03 — Validação dos formulários.** Preservar moeda brasileira, seleções e texto após erro, recuperar o modal correspondente e apresentar mensagens em português. Evidências: [FormularioTest](../tests/Feature/FormularioTest.php), [LivroRequestTest](../tests/Unit/LivroRequestTest.php) e [BackendRegressionTest](../tests/Feature/BackendRegressionTest.php). Os testes de resposta HTML não substituem a inspeção da interação no navegador.
- [x] **B04 — Integridade dos dados.** Bloquear exclusão de autores/assuntos vinculados, desfazer alterações quando a transação falhar e impedir duplicações em Livro_Assunto. A migration deve interromper diante de duplicações legadas sem apagar dados. Evidências: [BackendRegressionTest](../tests/Feature/BackendRegressionTest.php) e [LivroAssuntoMigrationTest](../tests/Feature/LivroAssuntoMigrationTest.php).
- [x] **B05 — Relatório e PDF.** Consultar a view, filtrar por autor/título/assuntos, separar autores homônimos por identificador, manter filtros na exportação e numerar o PDF com o total real de páginas. Evidências: [RelatorioControllerTest](../tests/Feature/RelatorioControllerTest.php), [BackendRegressionTest](../tests/Feature/BackendRegressionTest.php) e [PdfPaginacaoTest](../tests/Feature/PdfPaginacaoTest.php).
- [x] **B06 — Documentação e configuração de CI.** Documentar banco, API e instalação; configurar build, Pint, PHPStan e Pest na matriz SQLite/MySQL e arquivar assets. Evidências: [banco de dados](banco-de-dados.md), [API](api.md), [implantação](implantacao.md), [qualidade](qualidade.md) e [workflow](../.github/workflows/tests.yml). Este item atesta a configuração, não a execução remota.

## Verificações executadas

Na revisão de 16/09/2026:

- `DB_CONNECTION=sqlite DB_DATABASE=:memory: DB_URL= composer test`: Pint aprovado, PHPStan sem erros e **78 testes aprovados, com 409 asserções**. Banco de testes SQLite em memória.
- `npm run build`: aprovado. Emitiu avisos de depreciação Sass, inclusive em Bootstrap, e aviso sobre `fontaine` opcional para otimização dos fallbacks de fontes.

Os comandos de lint e análise de tipos deste projeto são `composer lint:check` e `composer types:check`, já incluídos em `composer test`; não existem scripts npm `lint` ou `typecheck`.

## Validações anteriores

As verificações abaixo já foram realizadas anteriormente, conforme confirmação do responsável pelo projeto. Não foram repetidas na revisão documental de 16/09/2026.

- [x] Validação da instalação e funcionamento com Docker/MySQL.
- [x] Validação dos fluxos no navegador, persistência e apresentação do PDF.
- [x] Verificação da execução remota do GitHub Actions.

## Manutenção opcional

- Revisar os avisos de depreciação Sass e de fallbacks de fontes em uma futura atualização das dependências. O build atual foi aprovado; esse acompanhamento não bloqueia a entrega.

## Limites do escopo

Autenticação, publicação em servidor público e operação de Jenkins/SonarQube não fazem parte desta entrega. O ano de publicação permanece inteiro por decisão documentada em [banco de dados](banco-de-dados.md). A revisão documental não executa migrations no banco de trabalho.
