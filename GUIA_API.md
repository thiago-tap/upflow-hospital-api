# Guia de Uso da API - Interface Swagger/OpenAPI

## Acesso a Documentacao

Abra seu navegador e acesse: **http://localhost:8000/docs/api**

---

## Dados de Teste Disponiveis

### Pacientes (15 no total)

| ID  | Nome               | CPF         |
| --- | ------------------ | ----------- |
| 1   | Joao Silva         | 52998224725 |
| 2   | Maria Santos       | 11144477735 |
| 3   | Pedro Oliveira     | 22255588846 |
| 4   | Ana Costa          | 33366699957 |
| 5   | Carlos Souza       | 44477711107 |
| 6   | Juliana Ferreira   | 55588822200 |
| 7   | Roberto Lima       | 66699933310 |
| 8   | Fernanda Alves     | 77711144407 |
| 9   | Lucas Pereira      | 88822255500 |
| 10  | Patricia Rodrigues | 99933366610 |
| 11  | Ricardo Martins    | 12345678909 |
| 12  | Camila Souza       | 98765432100 |
| 13  | Bruno Costa        | 14725836982 |
| 14  | Amanda Oliveira    | 25836914737 |
| 15  | Felipe Santos      | 36914725837 |

> **Importante:** Todos os CPFs sao apenas numericos (11 digitos), sem pontos ou tracos. Todos passam na validacao oficial de digitos verificadores.

### Leitos (15 no total)

| Tipo | Codigos |
|---|---|
| **UTI** | UTI-01, UTI-02, UTI-03, UTI-04, UTI-05 |
| **Enfermaria** | ENFERMARIA-01, ENFERMARIA-02, ENFERMARIA-03, ENFERMARIA-04, ENFERMARIA-05 |
| **Quartos** | QUARTO-01, QUARTO-02, QUARTO-03, QUARTO-04, QUARTO-05 |

---

## Como Usar a Interface Swagger

### 1. Listar Todos os Leitos

**Endpoint:** `GET /api/leitos`

**Parametros opcionais:**

| Parametro | Tipo | Padrao | Descricao |
|---|---|---|---|
| `per_page` | int | 15 | Registros por pagina (min: 1, max: 100) |

**Como testar:**

1. Clique em `GET /api/leitos` na interface
2. Clique no botao **"Try it out"**
3. (Opcional) Altere o valor de `per_page` para controlar a paginacao
4. Clique em **"Execute"**
5. Veja a resposta abaixo mostrando os leitos paginados

**Resposta esperada (200):**

```json
{
    "data": [
        {
            "id_leito": 1,
            "codigo": "UTI-01",
            "tipo": "UTI",
            "status": "LIVRE",
            "paciente": null
        },
        {
            "id_leito": 2,
            "codigo": "UTI-02",
            "tipo": "UTI",
            "status": "OCUPADO",
            "paciente": {
                "id": 1,
                "nome": "Joao Silva",
                "cpf": "52998224725"
            }
        }
    ],
    "links": {
        "first": "http://localhost:8000/api/leitos?page=1",
        "last": "http://localhost:8000/api/leitos?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "per_page": 15,
        "to": 15,
        "total": 15
    }
}
```

**Exemplos de paginacao:**

```
GET /api/leitos              -> 15 leitos por pagina (padrao)
GET /api/leitos?per_page=5   -> 5 leitos por pagina (3 paginas)
GET /api/leitos?per_page=3   -> 3 leitos por pagina (5 paginas)
```

---

### 2. Listar Todos os Pacientes

**Endpoint:** `GET /api/pacientes`

**Parametros opcionais:**

| Parametro | Tipo | Padrao | Descricao |
|---|---|---|---|
| `per_page` | int | 15 | Registros por pagina (min: 1, max: 100) |

**Como testar:**

1. Clique em `GET /api/pacientes` na interface
2. Clique em **"Try it out"**
3. (Opcional) Altere o valor de `per_page`
4. Clique em **"Execute"**

**Resposta esperada (200):**

```json
{
    "data": [
        { "id": 1, "nome": "Joao Silva", "cpf": "52998224725" },
        { "id": 2, "nome": "Maria Santos", "cpf": "11144477735" }
    ],
    "links": { "first": "...", "last": "...", "prev": null, "next": null },
    "meta": { "current_page": 1, "per_page": 15, "total": 15 }
}
```

---

### 3. Ocupar um Leito

**Endpoint:** `POST /api/leitos/ocupar`

**Como testar:**

1. Clique em `POST /api/leitos/ocupar`
2. Clique em **"Try it out"**
3. No campo de **Request body**, edite o JSON:
    ```json
    {
        "id_leito": 1,
        "id_paciente": 1
    }
    ```
4. Clique em **"Execute"**

**Exemplo de uso:**

- Para internar **Joao Silva** (ID: 1) no leito **UTI-01** (ID: 1):
    ```json
    {
        "id_leito": 1,
        "id_paciente": 1
    }
    ```

**Resposta de sucesso (200):**

```json
{
    "mensagem": "Paciente internado com sucesso."
}
```

**Possiveis erros (400):**

```json
{ "erro": "Este leito nao esta disponivel (Ocupado ou em Manutencao)." }
```

```json
{ "erro": "Este paciente ja esta ocupando outro leito." }
```

**Erro de validacao (422):**

```json
{
    "message": "The id leito field is required.",
    "errors": {
        "id_leito": ["The id leito field is required."]
    }
}
```

---

### 4. Liberar um Leito

**Endpoint:** `POST /api/leitos/liberar`

**Como testar:**

1. Clique em `POST /api/leitos/liberar`
2. Clique em **"Try it out"**
3. No campo de **Request body**:
    ```json
    {
        "id_leito": 1
    }
    ```
4. Clique em **"Execute"**

**Exemplo:** Para liberar o leito UTI-01 (ID: 1):

```json
{
    "id_leito": 1
}
```

**Resposta de sucesso (200):**

```json
{
    "mensagem": "Leito liberado com sucesso."
}
```

---

### 5. Transferir Paciente

**Endpoint:** `POST /api/leitos/transferir`

A transferencia e realizada de forma atomica (transacao no banco). Se uma etapa falhar, tudo e revertido.

**Como testar:**

1. Primeiro, **ocupe um leito** (use o endpoint 3)
2. Clique em `POST /api/leitos/transferir`
3. Clique em **"Try it out"**
4. No campo de **Request body**:
    ```json
    {
        "id_leito_atual": 1,
        "id_leito_destino": 2
    }
    ```
5. Clique em **"Execute"**

**Exemplo:** Transferir paciente do UTI-01 para UTI-02:

```json
{
    "id_leito_atual": 1,
    "id_leito_destino": 2
}
```

**Resposta de sucesso (200):**

```json
{
    "mensagem": "Transferencia realizada com sucesso."
}
```

**Possiveis erros (400):**

```json
{ "erro": "Nao ha paciente no leito de origem para transferir." }
```

```json
{ "erro": "O leito de destino ja esta ocupado." }
```

---

### 6. Buscar Leito por CPF

**Endpoint:** `GET /api/pacientes/{cpf}/leito`

**Importante:** O CPF deve ser enviado apenas com numeros (11 digitos), sem pontos ou tracos. O CPF e validado com o algoritmo oficial de digitos verificadores.

**Como testar:**

1. Primeiro, **ocupe um leito** com um paciente (use o endpoint 3)
2. Clique em `GET /api/pacientes/{cpf}/leito`
3. Clique em **"Try it out"**
4. No campo **cpf**, digite o CPF do paciente (exemplo: `52998224725`)
5. Clique em **"Execute"**

**Exemplo:** Buscar onde esta o paciente Joao Silva:

- CPF: `52998224725`

**Resposta - Paciente internado (200):**

```json
{
    "paciente": "Joao Silva",
    "leito": "UTI-01",
    "tipo": "UTI",
    "status": "OCUPADO"
}
```

**Resposta - Paciente nao internado (200):**

```json
{
    "mensagem": "O paciente nao esta internado no momento."
}
```

**Resposta - Paciente nao encontrado (404):**

```json
{
    "erro": "Paciente nao encontrado."
}
```

**Resposta - CPF invalido (422):**

```json
{
    "erro": "O cpf deve conter exatamente 11 digitos numericos."
}
```

---

## Codigos de Status HTTP

| Codigo | Significado | Quando ocorre |
|---|---|---|
| 200 | Sucesso | Operacao realizada com sucesso |
| 400 | Erro de negocio | Leito ocupado, paciente ja internado, etc. |
| 404 | Nao encontrado | Paciente nao existe no banco |
| 422 | Validacao falhou | Campos obrigatorios ausentes, CPF invalido |
| 429 | Rate limit | Mais de 60 requisicoes por minuto |

---

## Cenario de Teste Completo

Siga este fluxo para testar todas as funcionalidades:

### Passo 1: Ver todos os leitos vazios

```
GET /api/leitos
```

Todos os 15 leitos devem aparecer com status "LIVRE" e paciente `null`.

### Passo 2: Listar pacientes disponiveis

```
GET /api/pacientes
```

Veja os 15 pacientes disponiveis e seus IDs.

### Passo 3: Internar 3 pacientes

```json
POST /api/leitos/ocupar
{ "id_leito": 1, "id_paciente": 1 }
```

```json
POST /api/leitos/ocupar
{ "id_leito": 6, "id_paciente": 2 }
```

```json
POST /api/leitos/ocupar
{ "id_leito": 11, "id_paciente": 3 }
```

### Passo 4: Verificar lista atualizada

```
GET /api/leitos
```

Agora voce vera 3 leitos ocupados (UTI-01, ENFERMARIA-01, QUARTO-01).

### Passo 5: Buscar onde esta Joao Silva

```
GET /api/pacientes/52998224725/leito
```

Resposta: Joao Silva esta no leito UTI-01.

### Passo 6: Transferir Joao Silva para outro leito

```json
POST /api/leitos/transferir
{ "id_leito_atual": 1, "id_leito_destino": 3 }
```

### Passo 7: Confirmar transferencia

```
GET /api/pacientes/52998224725/leito
```

Agora Joao estara no leito UTI-03.

### Passo 8: Testar erros de negocio

Tente ocupar o leito UTI-03 (ja ocupado):

```json
POST /api/leitos/ocupar
{ "id_leito": 3, "id_paciente": 4 }
```

Resposta esperada: `{ "erro": "Este leito nao esta disponivel (Ocupado ou em Manutencao)." }`

### Passo 9: Testar validacao de CPF

```
GET /api/pacientes/12345678900/leito
```

Resposta esperada: `{ "erro": "O cpf informado nao e valido." }` (CPF com digitos verificadores incorretos)

### Passo 10: Liberar um leito

```json
POST /api/leitos/liberar
{ "id_leito": 6 }
```

### Passo 11: Verificar resultado final

```
GET /api/leitos?per_page=5
```

Veja a paginacao funcionando (5 registros por pagina).

---

## Testando com cURL (Opcional)

Se preferir usar a linha de comando:

```bash
# Listar leitos (5 por pagina)
curl http://localhost:8000/api/leitos?per_page=5

# Listar pacientes
curl http://localhost:8000/api/pacientes

# Ocupar leito
curl -X POST http://localhost:8000/api/leitos/ocupar \
  -H "Content-Type: application/json" \
  -d '{"id_leito": 1, "id_paciente": 1}'

# Buscar por CPF (apenas numeros)
curl http://localhost:8000/api/pacientes/52998224725/leito

# Transferir paciente
curl -X POST http://localhost:8000/api/leitos/transferir \
  -H "Content-Type: application/json" \
  -d '{"id_leito_atual": 1, "id_leito_destino": 3}'

# Liberar leito
curl -X POST http://localhost:8000/api/leitos/liberar \
  -H "Content-Type: application/json" \
  -d '{"id_leito": 3}'
```

---

## Resetar o Banco de Dados

Se quiser voltar ao estado inicial (todos os leitos vazios):

```bash
php artisan migrate:fresh --seed
```

---

## Dicas

- Use os **IDs** dos pacientes e leitos da tabela acima
- Teste os **casos de erro** tentando ocupar um leito ja ocupado
- Teste a **validacao de CPF** com CPFs invalidos
- A **paginacao** pode ser ajustada via `per_page` (1 a 100)
- Teste o **rate limiting** fazendo mais de 60 requisicoes por minuto
- A resposta mostra o **status HTTP** (200 = sucesso, 400 = erro de negocio, 404 = nao encontrado, 422 = validacao)
- Todos os endpoints podem ser testados **diretamente pela interface**, sem precisar de Postman ou cURL

---

**Pronto! Agora voce pode testar toda a API atraves da interface visual em http://localhost:8000/docs/api**
