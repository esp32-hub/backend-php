# Backend-PHP

API backend para recebimento e consulta de leituras de sensores enviadas por dispositivos ESP32.

> 🚧 **Status v1:** Em desenvolvimento

## Stack

- **Backend:** PHP, SQLite (PDO)
- **Dispositivo:** ESP32 (envia dados via HTTP POST)

## Estrutura do Repositório

```
Backend-PHP/
├── salvar.php      # Recebe e persiste leituras do ESP32
├── listar.php      # Lista leituras para consumo no frontend
└── database.db     # Banco SQLite (gerado automaticamente)
```

## Estrutura do Banco de Dados

### Tabela: `leituras`

| Coluna       | Tipo     | Descrição                        |
|--------------|----------|----------------------------------|
| id           | INTEGER  | Chave primária, autoincremento   |
| temperatura  | REAL     | Temperatura lida pelo sensor     |
| umidade      | REAL     | Umidade lida pelo sensor         |
| predicao     | TEXT     | Predição gerada pelo modelo      |
| criado_em    | DATETIME | Data/hora do registro (UTC)      |

---

## Rotas da API

### `POST /salvar.php`

Recebe uma leitura do ESP32 e persiste no banco.

**Body (JSON):**
```json
{
  "temperatura": 27.5,
  "umidade": 65.3,
  "predicao": "normal"
}
```

**Resposta de sucesso `200`:**
```json
{
  "status": "sucesso",
  "mensagem": "Dados salvos com sucesso no PHP!",
  "recebido": {
    "temperatura": 27.5,
    "umidade": 65.3,
    "predicao": "normal"
  }
}
```

**Resposta de erro `400` — JSON inválido:**
```json
{
  "status": "erro",
  "mensagem": "JSON inválido ou vazio."
}
```

**Resposta de erro `500` — Falha no banco:**
```json
{
  "status": "erro",
  "mensagem": "Erro no banco de dados: ..."
}
```

---

### `GET /listar.php`

Retorna as leituras registradas, ordenadas da mais recente para a mais antiga.

**Query Params:**

| Parâmetro | Tipo    | Padrão | Descrição                        |
|-----------|---------|--------|----------------------------------|
| `limit`   | integer | `50`   | Quantidade de registros por página |
| `offset`  | integer | `0`    | Deslocamento para paginação      |

**Exemplo de requisição:**
```
GET /listar.php?limit=20&offset=0
```

**Resposta de sucesso `200`:**
```json
{
  "status": "sucesso",
  "total": 142,
  "limit": 20,
  "offset": 0,
  "dados": [
    {
      "id": 142,
      "temperatura": 27.5,
      "umidade": 65.3,
      "predicao": "normal",
      "criado_em": "2025-07-10 14:32:00"
    }
  ]
}
```

**Resposta de erro `500`:**
```json
{
  "status": "erro",
  "mensagem": "..."
}
```

---

## Requisitos Funcionais (RF)

- [x] O ESP32 deve poder enviar leituras de temperatura, umidade e predição;
- [x] O sistema deve persistir as leituras automaticamente no banco;
- [x] O frontend deve poder listar todas as leituras para exibição em dashboards;
- [x] O frontend deve poder paginar os resultados via `limit` e `offset`;
- [x] O sistema deve retornar o total de registros junto à listagem.

## Requisitos Não-Funcionais (RNF)

- [x] O banco de dados deve ser criado automaticamente se não existir;
- [x] Todas as respostas devem estar no formato JSON;
- [x] A API deve retornar status HTTP adequados para cada situação;
- [x] A rota de listagem deve permitir requisições cross-origin (CORS);
- [x] Os dados devem ser retornados ordenados do mais recente para o mais antigo.

## Regras de Negócio (RN)

- [x] Requisições com body inválido ou vazio devem ser rejeitadas com `400`;
- [x] Erros internos de banco devem retornar `500` sem expor detalhes sensíveis ao ESP32;
- [x] Os parâmetros `limit` e `offset` devem ser tratados como inteiros para evitar injeção.
