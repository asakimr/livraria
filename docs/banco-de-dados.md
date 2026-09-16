# Banco de dados

As migrations em `database/migrations` são a fonte executável da estrutura. O ambiente Docker utiliza MySQL 8; os testes também podem executar em SQLite. A capitalização de tabelas e campos segue o modelo do desafio.

## Tabelas do domínio

Todos os campos abaixo são obrigatórios. As tabelas do domínio não usam timestamps.

| Tabela | Campo | Tipo MySQL | Regra |
|---|---|---|---|
| Livro | Codl | INT UNSIGNED | Chave primária, auto incremento |
| Livro | Titulo | VARCHAR(40) | Título |
| Livro | Editora | VARCHAR(40) | Editora |
| Livro | Edicao | INT | Inteiro positivo validado pela aplicação |
| Livro | AnoPublicacao | INT | Ano com quatro dígitos validado pela aplicação |
| Livro | Valor | DECIMAL(8,2) | Valor exato, de 0 a 999999,99 na aplicação |
| Autor | CodAu | INT UNSIGNED | Chave primária, auto incremento |
| Autor | Nome | VARCHAR(40) | Não é único; homônimos são permitidos |
| Assunto | codAs | INT UNSIGNED | Chave primária, auto incremento |
| Assunto | Descricao | VARCHAR(20) | Descrição |
| Livro_Autor | Livro_Codl | INT UNSIGNED | FK para Livro.Codl |
| Livro_Autor | Autor_CodAu | INT UNSIGNED | FK para Autor.CodAu |
| Livro_Assunto | Livro_Codl | INT UNSIGNED | FK para Livro.Codl |
| Livro_Assunto | Assunto_codAs | INT UNSIGNED | FK para Assunto.codAs |

`Livro_Autor` tem chave primária composta (`Livro_Codl`, `Autor_CodAu`). `Livro_Assunto` também tem chave primária composta (`Livro_Codl`, `Assunto_codAs`), acrescentada pela migration `2026_09_16_120000_add_primary_key_to_livro_assunto_table`. Ambas impedem associações repetidas inclusive em escrita SQL direta.

### Atualização de uma instalação existente

Faça backup e suspenda escritas durante a atualização. Aplique com `php artisan migrate --force`; não use `migrate:fresh` em uma base com dados. A nova migration verifica duplicações antes de alterar a tabela. Se encontrar qualquer par repetido, interrompe com uma mensagem explicativa, sem remover nem escolher registros automaticamente. Use a consulta abaixo para revisar o legado e decidir a correção antes de tentar novamente:

```sql
SELECT Livro_Codl, Assunto_codAs, COUNT(*) AS quantidade
FROM Livro_Assunto
GROUP BY Livro_Codl, Assunto_codAs
HAVING COUNT(*) > 1;
```

Sem duplicações, as associações existentes são preservadas. O rollback dessa migration remove a chave adicionada, preservando linhas e FKs; após revertê-la, o banco volta a aceitar pares repetidos. No MySQL, o rollback restaura um índice de suporte em Livro_Codl quando necessário: o servidor pode ter descartado o índice automático da FK ao aproveitar a nova PK. No SQLite, a alteração exige reconstruir a tabela: a migration mantém a view apontando para o nome original e restaura a configuração temporária da conexão. No MySQL, aplica a alteração de chave diretamente.

As FKs nomeadas `Livro_Autor_FKIndex1/2` e `Livro_Assunto_FKIndex1/2` têm ON DELETE CASCADE. O MySQL cria os índices necessários às FKs quando não cobertos por um índice existente. O primeiro campo da PK composta já serve ao vínculo de livro em Livro_Autor. Os nomes das constraints não devem ser confundidos com uma garantia de nome idêntico para todos os índices físicos. Para inspecionar uma instalação MySQL:

```sql
SHOW CREATE TABLE Livro;
SHOW CREATE TABLE Livro_Autor;
SHOW CREATE TABLE Livro_Assunto;
SHOW INDEX FROM Livro_Autor;
SHOW INDEX FROM Livro_Assunto;
SHOW CREATE VIEW vw_relatorio_livros_autores;
```

A aplicação exige pelo menos um autor e um assunto por livro. Autores e assuntos vinculados não podem ser excluídos pelos Services; remova ou substitua os vínculos primeiro. Excluir um livro remove seus vínculos pelas FKs. Como a proteção de autor/assunto é de negócio, SQL direto continua sujeito ao comportamento CASCADE original.

## View do relatório

`vw_relatorio_livros_autores` une Autor → Livro_Autor → Livro, com LEFT JOIN para Livro_Assunto e Assunto. Cada linha representa um par autor/livro; `GROUP_CONCAT` reúne as descrições dos assuntos.

| Coluna | Origem |
|---|---|
| CodAu | Autor.CodAu |
| Autor_Nome | Autor.Nome |
| Codl | Livro.Codl |
| Livro_Titulo | Livro.Titulo |
| Livro_Editora | Livro.Editora |
| Livro_Edicao | Livro.Edicao |
| Livro_Ano | Livro.AnoPublicacao |
| Assuntos | Descrições agregadas |

O controller consulta a view, aplica filtros e agrupa por CodAu. Assim, autores diferentes com o mesmo nome não são misturados. Um livro com dois autores aparece em ambos os grupos. Autores sem livros não aparecem devido ao INNER JOIN. A view não é materializada: a consulta usa os dados atuais.

No MySQL, listas muito extensas podem atingir `group_concat_max_len`; esse parâmetro deve ser avaliado se o volume de assuntos crescer. Não foi feita uma otimização baseada em benchmark.

O rollback dessa migration executa `DROP VIEW IF EXISTS`, não DROP TABLE. Nenhuma procedure ou trigger é utilizada: migrations, FKs, FormRequests e Services concentram as responsabilidades necessárias. Não foram criados objetos apenas para preencher a documentação.

## AnoPublicacao inteiro

O diagrama prevê VARCHAR(4), com exceção para ajustes de performance. Foi mantida a decisão do autor de usar INT: quatro bytes no MySQL, comparados com quatro dígitos ASCII mais um byte de comprimento no VARCHAR(4), desconsiderando o restante da linha. Isso representa uma redução modesta de armazenamento; não demonstra redução no tempo de consulta sem medição.

O tipo permite operações numéricas sem conversão. Comparativos futuros são uma possibilidade, não uma funcionalidade implementada. SMALLINT economizaria mais, mas não foi adotado para evitar uma mudança adicional à escolha existente.

## Tabelas de infraestrutura preexistentes

Estas tabelas vieram do projeto Laravel e não foram adicionadas para a nova API:

| Tabela | Campos principais e objetos |
|---|---|
| users | id BIGINT UNSIGNED PK; name/email/password VARCHAR(255); email UNIQUE; email_verified_at TIMESTAMP nullable; remember_token VARCHAR(100) nullable; created_at/updated_at TIMESTAMP nullable |
| password_reset_tokens | email VARCHAR(255) PK; token VARCHAR(255); created_at TIMESTAMP nullable |
| sessions | id VARCHAR(255) PK; user_id BIGINT UNSIGNED nullable com índice (sem FK nesta migration); ip_address VARCHAR(45) nullable; user_agent TEXT nullable; payload LONGTEXT; last_activity INT com índice |
| cache | key VARCHAR(255) PK; value MEDIUMTEXT; expiration BIGINT |
| cache_locks | key VARCHAR(255) PK; owner VARCHAR(255); expiration BIGINT |
| jobs | id BIGINT UNSIGNED PK auto incremento; queue VARCHAR(255) com índice; payload LONGTEXT; attempts SMALLINT UNSIGNED; reserved_at INT UNSIGNED nullable; available_at/created_at INT UNSIGNED |
| job_batches | id VARCHAR(255) PK; name VARCHAR(255); total_jobs/pending_jobs/failed_jobs INT; failed_job_ids LONGTEXT; options MEDIUMTEXT nullable; cancelled_at/finished_at INT nullable; created_at INT |
| failed_jobs | id BIGINT UNSIGNED PK auto incremento; uuid VARCHAR(255) UNIQUE; connection/queue TEXT; payload/exception LONGTEXT; failed_at TIMESTAMP padrão atual; índice connection/queue/failed_at |

O arquivo `.env.example` usa sessão/cache em arquivos e fila síncrona. As migrations de infraestrutura foram preservadas para não apagar objetos existentes. Não existe nova tabela personal_access_tokens.
