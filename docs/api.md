# API JSON

Base local: `http://localhost:8777/api`. Envie `Accept: application/json` e, para POST/PUT/PATCH, `Content-Type: application/json`.

A API é demonstrativa, sem autenticação e sem novas tabelas. Controllers JSON reutilizam Services e Models das telas. Não envia cookie CSRF nem token Bearer.

## Endpoints

Substitua `{recurso}` por `livros`, `autores` ou `assuntos`.

| Método | Caminho | Resposta |
|---|---|---|
| GET | /api/{recurso} | 200, lista paginada (10 por página) |
| POST | /api/{recurso} | 201, objeto criado em data |
| GET | /api/{recurso}/{id} | 200, objeto em data |
| PUT ou PATCH | /api/{recurso}/{id} | 200, objeto atualizado em data |
| DELETE | /api/{recurso}/{id} | 204, sem corpo |

A listagem aceita `?search=texto&page=2`. Atualizações usam o conjunto completo de campos obrigatórios, inclusive em PATCH; não há contrato de atualização parcial.

O filtro search aceita texto de até 200 caracteres; page deve ser inteiro maior ou igual a 1. Valores incompatíveis, como arrays nesses filtros, retornam 422. IDs nas rotas precisam ser numéricos, com 1 a 10 dígitos; formatos diferentes retornam 404.

Registro inexistente retorna 404. Validação retorna 422 com `message` e `errors`. Exclusão de autor/assunto vinculado retorna 409 com mensagem. Erros inesperados retornam 500 sem detalhes internos.

## Campos

- Autor: `Nome`, texto obrigatório com até 40 caracteres.
- Assunto: `Descricao`, texto obrigatório com até 20 caracteres.
- Livro: `Titulo` e `Editora` até 40 caracteres; `Edicao` inteira entre 1 e 2147483647; `AnoPublicacao` inteiro de quatro dígitos; `Valor` entre 0 e 999999.99, com até duas casas decimais; `autores` e `assuntos` como arrays com pelo menos um ID existente e sem repetição.

Na API, o valor é decimal sem máscara: `"59.90"`. Não enviar `"R$ 59,90"` ou `"59,90"`. A resposta mantém as duas casas em string decimal. O formulário web aceita o formato brasileiro e o converte antes da persistência.

Os IDs em autores e assuntos devem ser números inteiros JSON, como `[1, 2]`, não strings (`["1"]`), booleanos ou objetos. O array precisa ser uma lista.

## Exemplo

Os exemplos criam registros no ambiente apontado. Primeiro cadastre um autor e um assunto:

```bash
curl -i http://localhost:8777/api/autores \
  -H 'Accept: application/json' -H 'Content-Type: application/json' \
  -d '{"Nome":"Machado de Assis"}'

curl -i http://localhost:8777/api/assuntos \
  -H 'Accept: application/json' -H 'Content-Type: application/json' \
  -d '{"Descricao":"Romance"}'
```

Use os IDs retornados em `data.CodAu` e `data.codAs`; o exemplo seguinte pressupõe que ambos sejam 1:

```bash
curl -i http://localhost:8777/api/livros \
  -H 'Accept: application/json' -H 'Content-Type: application/json' \
  -d '{"Titulo":"Dom Casmurro","Editora":"Exemplo","Edicao":1,"AnoPublicacao":1899,"Valor":"59.90","autores":[1],"assuntos":[1]}'

curl -i http://localhost:8777/api/livros/1 -H 'Accept: application/json'
curl -i 'http://localhost:8777/api/livros?search=Casmurro' -H 'Accept: application/json'
```

Para editar, envie o mesmo conjunto de campos com `-X PUT`. Para excluir, utilize `-X DELETE`. O ID do livro também deve ser obtido na resposta, não presumido em um banco já preenchido.

Erros de campo usam o formato:

```json
{
  "message": "Mensagem de validação",
  "errors": {
    "Titulo": ["Mensagem explicando o campo inválido."]
  }
}
```

O texto exato depende da regra violada. Os nomes dos campos preservam a escrita do projeto.

## Validação de listas

No ambiente local de demonstração, envie um objeto JSON em `autores` em vez de uma lista:

```bash
curl -i http://localhost:8777/api/livros \
  -H 'Accept: application/json' -H 'Content-Type: application/json' \
  -d '{"Titulo":"Teste de validacao","Editora":"Demonstracao","Edicao":1,"AnoPublicacao":2026,"Valor":"59.90","autores":{"x":1},"assuntos":[1]}'
```

A API retorna HTTP **422**, com uma mensagem em português em `errors.autores` explicando que o campo deve ser uma lista. A requisição é rejeitada antes da persistência, sem criar um livro.

Em JSON, `[1]` é uma lista; `{"x":1}` é um objeto com chave nomeada. O PHP transforma esse objeto em array associativo: a regra `array` aceita a estrutura, mas `list` exige índices consecutivos iniciando em zero. O formulário web envia os IDs selecionados como lista.

Use IDs existentes de autores e assuntos para verificar apenas a validação de lista. IDs inexistentes também geram mensagens de vínculo inválido. No exemplo, mantenha `autores` como objeto para receber o erro da regra `list`.
