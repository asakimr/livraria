# Instalação e entrega

## Local com SQLite

Requisitos: PHP 8.3+, Composer 2, Node 22.12+ e npm. Extensões PHP: intl, mbstring, PDO SQLite/MySQL, DOM/XML e ZIP, além das extensões exigidas pelo Composer.

Em checkout novo:

```bash
composer setup
composer dev
```

O primeiro comando instala dependências travadas, prepara .env e APP_KEY, cria o arquivo SQLite padrão, aplica migrations e executa npm ci/build. Em instalações existentes ele preserva uma APP_KEY preenchida. Ele não troca credenciais e não executa seed.

Em uma instalação existente, o setup preserva também o restante do .env. Ajuste APP_LOCALE=pt_BR, APP_FALLBACK_LOCALE=pt_BR e APP_FAKER_LOCALE=pt_BR se ainda estiverem em inglês, e execute `php artisan config:clear`. Não é necessário trocar APP_KEY.

Para MySQL fora do Docker, configure DB_CONNECTION=mysql, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME e DB_PASSWORD no .env antes das migrations. Crie o banco e conceda ao usuário permissões para criar tabelas e views. Após alterar configuração, execute `php artisan config:clear`.

## Docker com MySQL

É um ambiente de desenvolvimento e apresentação, usando artisan serve. Requer Docker Engine e Compose v2. O banco reside em volume próprio; não é usado o MySQL do host e a porta 3306 não é publicada. A aplicação publica somente 127.0.0.1:8777.

Em checkout novo:

```bash
export LOCAL_UID="$(id -u)"
export LOCAL_GID="$(id -g)"
docker compose build app
docker compose run --rm app composer install --no-interaction
docker compose run --rm app php scripts/prepare-environment.php
docker compose run --rm app php artisan config:clear
docker compose run --rm app php artisan migrate --force
docker compose --profile tools run --rm node
docker compose up -d app
docker compose ps
curl --fail http://localhost:8777/up
```

O serviço app espera o healthcheck do MySQL. O ambiente do Compose sobrescreve a conexão do .env apenas dentro do container. As dependências e assets são gerados no diretório montado com seu UID/GID. Não é iniciado servidor Vite: o build estático atende a apresentação.

O `artisan serve` usa `--no-reload` para que a conexão MySQL injetada pelo Compose não seja perdida por um processo filho. Em compensação, mudanças em `.env` ou `docker-compose.yml` não são recarregadas automaticamente: execute `docker compose up -d --no-deps --force-recreate app` para recriar somente a aplicação. O serviço `db` e o volume do banco são preservados.

O script prepara um arquivo SQLite padrão vazio mesmo no Docker; a conexão mysql usa somente o serviço db. Dependências não são instaladas e APP_KEY não é regenerada a cada reinício.

Se 8777 estiver ocupada, defina APP_PORT=8778 e ajuste APP_URL no .env. Para o banco local do Compose, as senhas padrão são apenas de demonstração.

As variáveis DOCKER_DB_DATABASE, DOCKER_DB_USERNAME e DOCKER_DB_PASSWORD têm prioridade. Quando ausentes, o Compose reaproveita DB_DATABASE, DB_USERNAME e DB_PASSWORD do .env, como a configuração anterior. A senha root usa DOCKER_DB_ROOT_PASSWORD, com fallback para DB_PASSWORD, também preservando o comportamento anterior. Em checkout novo, sem essas configurações, os padrões são banco/usuário livraria e senhas apenas locais.

Antes de iniciar Docker em um checkout configurado com SQLite e DB_DATABASE apontando para um arquivo, defina explicitamente DOCKER_DB_DATABASE=livraria, DOCKER_DB_USERNAME=livraria e as duas senhas Docker. O nome de arquivo SQLite não é um nome de banco MySQL adequado.

Para reutilizar um volume db_data existente, mantenha o mesmo projeto Compose (mesmo diretório ou nome definido anteriormente), nome de banco, usuário e senhas usados em sua criação. As variáveis MYSQL_* inicializam volumes novos; elas não renomeiam o banco, criam outro usuário ou trocam senhas em um volume existente. Se ocorrer falha de autenticação, pare a aplicação e recupere esses valores na configuração anterior ou backup do .env; ajuste DOCKER_DB_* para corresponder a eles e recrie somente os containers. Não apague o volume para corrigir credenciais. Se as credenciais antigas não estiverem disponíveis, será necessária uma recuperação administrativa do MySQL, com backup e procedimento próprio.

Para parar preservando dados: `docker compose down`. Não use a opção `-v` se quiser preservar o banco.

## Atualizar e reverter

Antes de atualizar uma instalação com dados, faça backup do banco, .env e arquivos persistentes e registre a revisão atual. Na máquina de destino: instale dependências com Composer a partir do lock, compile assets com npm ci/build, aplique migrations pendentes e valide /up, CRUD e relatório. Em Docker, use os comandos equivalentes acima.

`php artisan migrate:status` mostra o estado. `php artisan migrate:rollback --step=1` desfaz a última migration e pode remover dados conforme a migration escolhida: inspecione-a antes. Se a última for a view, o rollback remove apenas a view e o relatório fica indisponível até `php artisan migrate` recriá-la.

Não use migrate:fresh para atualizar uma instalação: ele remove tabelas. Reversão de código não restaura dados; recupere o backup se uma migration destrutiva tiver sido aplicada.

Migrations já aplicadas não são reaplicadas automaticamente quando seu arquivo é editado. Nesta adequação, o ajuste do down da view corrige a reversão futura; não exige apagar ou recriar o banco atual.

## Integração contínua

O GitHub Actions prepara PHP/Node, executa build, Pint, PHPStan e Pest, em matriz SQLite/MySQL descartável. Credenciais do serviço são exclusivas do ambiente de CI.

O GitHub Actions é a automação de referência. Consulte [qualidade e testes](qualidade.md) para os comandos e limites das verificações.

O workflow está configurado para guardar `public/build` depois das verificações, com artefatos `frontend-build-sqlite` e `frontend-build-mysql` e retenção de sete dias. Esses assets não são um pacote completo do backend. Não há deploy automático em produção nem conexão com servidor de terceiros.

A configuração do workflow não comprova execução remota. Confirme os logs de uma execução antes de apresentar a esteira como aprovada.

Uma implantação pública exigiria selecionar o destino, web server com raiz em public, PHP-FPM, TLS, APP_DEBUG=false, credenciais próprias, permissões de storage/bootstrap/cache e política de acesso. Nenhum desses serviços externos foi instalado ou publicado por estes arquivos.
