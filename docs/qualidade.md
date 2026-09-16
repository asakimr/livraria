# Qualidade e testes

## O que cada ferramenta verifica

Pest executa cenários: envia requisições, chama funções e compara resultados. Uma aplicação pode funcionar e seus testes passarem sem que todos os caminhos de erro tenham sido exercitados.

PHPStan analisa o código sem executar cada fluxo. Ele encontra, por exemplo, classes inexistentes, chamadas incompatíveis e retornos diferentes dos tipos declarados. Larastan é a extensão que ensina ao PHPStan convenções do Laravel, como Eloquent, relações e facades. Avisos podem exigir corrigir código, melhorar anotações ou revisar uma interpretação da ferramenta; não equivalem automaticamente a bugs percebidos pelo usuário.

Pint aplica o padrão de formatação PHP. Falhas indicam estilo, como espaços, imports e aspas; não provam falha funcional.

O preset Laravel foi mantido, com exceções de aspas, posição de chaves, declaração de classes/funções e estilo de comentário de linha para preservar a escrita existente. Essas exceções são de formatação, não desativam verificações funcionais. O PHPStan permanece no nível 7, sem um baseline criado para ocultar os erros encontrados.

PHPStan/Larastan e Pint já constavam em `composer.json`. O comando `composer test` já os incluía, enquanto `php artisan test` executa os testes sem essas etapas. Sua presença no projeto não demonstra que o autor os escolheu conscientemente; podem ter vindo do starter kit.

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

A configuração padrão utiliza SQLite `:memory:`. O workflow do GitHub também prevê MySQL 8 em serviço descartável para exercitar compatibilidade real das migrations/view. Essa configuração é uma receita de execução: consulte o resultado do workflow para afirmar que passou remotamente.

## Cenários de regressão

A suíte deve preservar: CRUD web/API, validações, moeda após erro de outro campo, preço decimal na API, recuperação de modal, relatório com autores homônimos, bloqueio de exclusões vinculadas, rollback da view e erros JSON. Testes unitários verificam regras isoladas; testes funcionais verificam a integração com Laravel e banco.

A execução automatizada não substitui a inspeção humana de máscaras, foco, modais e PDF. Não é alegado TDD retroativo: os testes de regressão foram adicionados durante a correção.

## SonarQube

`sonar-project.properties` define fontes e testes. Para análise opcional, disponibilize um servidor, SonarScanner e as variáveis `SONAR_HOST_URL` e `SONAR_TOKEN`, depois execute `sonar-scanner -Dsonar.qualitygate.wait=true`.

No Jenkins, habilite RUN_SONAR e cadastre a credencial secret text `livraria-sonar-token`; o plugin Credentials Binding injeta o token. O scanner precisa estar no PATH do agente e SONAR_HOST_URL configurada no ambiente. Não grave tokens no repositório.

Cobertura não é gerada ou declarada por esta configuração. Um servidor Sonar, resultado de análise ou quality gate aprovado só pode ser afirmado após execução efetiva.
