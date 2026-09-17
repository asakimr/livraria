# Qualidade e testes

## O que cada ferramenta verifica

Pest executa cenários: envia requisições, chama funções e compara resultados. Uma aplicação pode funcionar e seus testes passarem sem que todos os caminhos de erro tenham sido exercitados.

PHPStan analisa o código sem executar cada fluxo. Ele encontra, por exemplo, classes inexistentes, chamadas incompatíveis e retornos diferentes dos tipos declarados. Larastan é a extensão que ensina ao PHPStan convenções do Laravel, como Eloquent, relações e facades. Avisos podem exigir corrigir código, melhorar anotações ou revisar uma interpretação da ferramenta; não equivalem automaticamente a bugs percebidos pelo usuário.

Pint aplica o padrão de formatação PHP. Falhas indicam estilo, como espaços, imports e aspas; não provam falha funcional.

O preset Laravel foi mantido, com exceções de aspas, posição de chaves, declaração de classes/funções e estilo de comentário de linha para preservar a escrita existente. Essas exceções são de formatação, não desativam verificações funcionais. O PHPStan permanece no nível 7, sem baseline de erros.

`composer test` executa Pint, PHPStan/Larastan e Pest. `php artisan test` executa apenas a suíte de testes.

## Comandos

```bash
php artisan test
php artisan test --testsuite=Unit
composer lint:check
composer types:check
composer ci:check
npm run build
```

`composer lint` aplica formatação e modifica arquivos. Os demais comandos de qualidade verificam o código; os testes usam banco isolado conforme o ambiente. Não apontar DB_DATABASE de testes para um banco com dados: RefreshDatabase pode recriar tabelas.

A configuração padrão utiliza SQLite `:memory:`. O workflow do GitHub também prevê MySQL 8 em serviço descartável para exercitar compatibilidade real das migrations/view. Os resultados da matriz SQLite/MySQL estão disponíveis no [GitHub Actions](https://github.com/asakimr/livraria/actions).

## Cenários de regressão

A suíte deve preservar: CRUD web/API, validações, moeda após erro de outro campo, preço decimal na API, recuperação de modal, relatório com autores homônimos, bloqueio de exclusões vinculadas, rollback da view e erros JSON. Testes unitários verificam regras isoladas; testes funcionais verificam a integração com Laravel e banco.

A execução automatizada não substitui a inspeção humana de máscaras, foco, modais e PDF. Os testes de regressão foram adicionados durante a correção dos problemas identificados.
