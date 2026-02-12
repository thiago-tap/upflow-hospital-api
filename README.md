# API de Gerenciamento de Leitos Hospitalares

API REST desenvolvida em Laravel para gerenciar a ocupacao de leitos em um hospital, permitindo controle completo sobre internacoes, transferencias e consultas de pacientes.

## Sobre o Projeto

Sistema de gerenciamento de leitos hospitalares que permite:

- Internar pacientes em leitos
- Liberar leitos ocupados
- Transferir pacientes entre leitos (com transacao atomica)
- Listar todos os pacientes disponíveis (com paginacao)
- Consultar leito de um paciente por CPF (com validacao oficial)
- Verificar status de ocupacao dos leitos
- Listar todos os leitos com seus respectivos status (com paginacao)
- Auditoria completa de todas as acoes (ocupar, liberar, transferir)

## Tecnologias Utilizadas

- **PHP 8.2+**
- **Laravel 12.x**
- **SQLite** (banco de dados leve e portavel)
- **Scramble** (documentacao OpenAPI/Swagger automatica)
- **PHPUnit** (testes automatizados)

## Arquitetura do Projeto

O projeto segue uma arquitetura em camadas com separacao clara de responsabilidades:

```
Request -> Controller -> Service -> Model -> Database
               |
          Resource -> JSON Response
               |
        LeitoException (erros de negocio)
               |
        AuditoriaLeito (log de acoes)
```

### Camadas

| Camada | Responsabilidade | Arquivos |
|---|---|---|
| **Controller** | Validacao de input e formatacao de response | `LeitoController.php` |
| **Service** | Logica de negocio, transacoes e auditoria | `LeitoService.php` |
| **Model** | Relacionamentos e casting de dados | `Leito.php`, `Paciente.php`, `AuditoriaLeito.php` |
| **Resource** | Padronizacao do formato JSON de saida | `LeitoResource.php`, `PacienteResource.php` |
| **Enum** | Type safety para status e tipos de leito | `StatusLeito.php`, `TipoLeito.php` |
| **Exception** | Erros de negocio com HTTP status codes | `LeitoException.php` |
| **Rule** | Validacao customizada de CPF | `CpfValido.php` |

## Pre-requisitos

- PHP >= 8.2
- Composer
- SQLite3
- Git

## Instalacao

### 1. Clone o repositorio

```bash
git clone <url-do-repositorio>
cd hospital-api
```

### 2. Instale as dependencias

```bash
composer install
```

### 3. Configure o ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Execute as migrations e seeders

```bash
php artisan migrate:fresh --seed
```

Isso criara:

- **15 pacientes** de teste com CPFs validos (apenas numerico, 11 digitos)
- **15 leitos** (5 UTI, 5 Enfermaria, 5 Quartos)
- **Tabela de auditoria** para registro de acoes

### 5. Inicie o servidor

```bash
php artisan serve
```

A API estara disponivel em: **http://localhost:8000**

### 6. Execute os testes

```bash
php artisan test
```

Resultado esperado: **18 testes, 66 assertions, 0 falhas**.

## Documentacao da API

### Interface Interativa (Swagger/OpenAPI)

Acesse a documentacao interativa completa em:

**http://localhost:8000/docs/api**

Nesta interface voce pode:

- Ver todos os endpoints disponiveis
- Testar as requisicoes diretamente pelo navegador
- Visualizar exemplos de request e response
- Ver validacoes e codigos de status HTTP

### Endpoints Disponiveis

#### 1. Listar todos os leitos

```http
GET /api/leitos
GET /api/leitos?per_page=5
```

Parametros de query opcionais:

| Parametro | Tipo | Padrao | Descricao |
|---|---|---|---|
| `per_page` | int | 15 | Registros por pagina (min: 1, max: 100) |

**Resposta (200):**

```json
{
    "data": [
        {
            "id_leito": 1,
            "codigo": "UTI-01",
            "tipo": "UTI",
            "status": "OCUPADO",
            "paciente": {
                "id": 1,
                "nome": "Joao Silva",
                "cpf": "52998224725"
            }
        },
        {
            "id_leito": 2,
            "codigo": "UTI-02",
            "tipo": "UTI",
            "status": "LIVRE",
            "paciente": null
        }
    ],
    "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
    "meta": { "current_page": 1, "per_page": 15, "total": 15 }
}
```

---

#### 2. Listar todos os pacientes

```http
GET /api/pacientes
GET /api/pacientes?per_page=10
```

Parametros de query opcionais:

| Parametro | Tipo | Padrao | Descricao |
|---|---|---|---|
| `per_page` | int | 15 | Registros por pagina (min: 1, max: 100) |

**Resposta (200):**

```json
{
    "data": [
        { "id": 1, "nome": "Joao Silva", "cpf": "52998224725" }
    ],
    "links": { "..." },
    "meta": { "current_page": 1, "per_page": 15, "total": 15 }
}
```

---

#### 3. Ocupar um leito

```http
POST /api/leitos/ocupar
Content-Type: application/json
```

**Body:**

```json
{
    "id_leito": 1,
    "id_paciente": 1
}
```

**Resposta de Sucesso (200):**

```json
{
    "mensagem": "Paciente internado com sucesso."
}
```

**Possiveis Erros (400):**

```json
{ "erro": "Este leito nao esta disponivel (Ocupado ou em Manutencao)." }
```

```json
{ "erro": "Este paciente ja esta ocupando outro leito." }
```

---

#### 4. Liberar um leito

```http
POST /api/leitos/liberar
Content-Type: application/json
```

**Body:**

```json
{
    "id_leito": 1
}
```

**Resposta (200):**

```json
{
    "mensagem": "Leito liberado com sucesso."
}
```

---

#### 5. Transferir paciente

```http
POST /api/leitos/transferir
Content-Type: application/json
```

**Body:**

```json
{
    "id_leito_atual": 1,
    "id_leito_destino": 2
}
```

**Resposta (200):**

```json
{
    "mensagem": "Transferencia realizada com sucesso."
}
```

**Possiveis Erros (400):**

```json
{ "erro": "Nao ha paciente no leito de origem para transferir." }
```

```json
{ "erro": "O leito de destino ja esta ocupado." }
```

---

#### 6. Buscar leito por CPF

```http
GET /api/pacientes/{cpf}/leito
```

**Importante:** O CPF deve ser enviado apenas com numeros (11 digitos), sem pontos ou tracos.

**Exemplo:**

```http
GET /api/pacientes/52998224725/leito
```

**Resposta (200) - Paciente internado:**

```json
{
    "paciente": "Joao Silva",
    "leito": "UTI-01",
    "tipo": "UTI",
    "status": "OCUPADO"
}
```

**Resposta (200) - Paciente nao internado:**

```json
{
    "mensagem": "O paciente nao esta internado no momento."
}
```

**Resposta (404) - Paciente nao encontrado:**

```json
{
    "erro": "Paciente nao encontrado."
}
```

**Resposta (422) - CPF invalido:**

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

## Testando a API

### Opcao 1: Interface Swagger (Recomendado)

1. Acesse http://localhost:8000/docs/api
2. Clique em qualquer endpoint
3. Clique em **"Try it out"**
4. Preencha os dados necessarios
5. Clique em **"Send"** ou **"Execute"**

### Opcao 2: cURL

```bash
# Listar todos os leitos (5 por pagina)
curl http://localhost:8000/api/leitos?per_page=5

# Ocupar um leito
curl -X POST http://localhost:8000/api/leitos/ocupar \
  -H "Content-Type: application/json" \
  -d '{"id_leito": 1, "id_paciente": 1}'

# Buscar leito por CPF (apenas numeros)
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

### Opcao 3: Postman

Importe a colecao usando a especificacao OpenAPI:

```
http://localhost:8000/docs/api.json
```

---

## Dados de Teste

### Pacientes (15 cadastrados)

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

### Leitos (15 disponiveis)

| Tipo | Codigos |
|---|---|
| **UTI** | UTI-01, UTI-02, UTI-03, UTI-04, UTI-05 |
| **Enfermaria** | ENFERMARIA-01, ENFERMARIA-02, ENFERMARIA-03, ENFERMARIA-04, ENFERMARIA-05 |
| **Quartos** | QUARTO-01, QUARTO-02, QUARTO-03, QUARTO-04, QUARTO-05 |

---

## Estrutura do Projeto

```
hospital-api/
├── app/
│   ├── Enums/
│   │   ├── StatusLeito.php             # Enum: LIVRE, OCUPADO, MANUTENCAO
│   │   └── TipoLeito.php              # Enum: UTI, ENFERMARIA, QUARTO
│   ├── Exceptions/
│   │   └── LeitoException.php          # Exceptions de negocio com status HTTP
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── LeitoController.php     # Controller (validacao + response)
│   │   └── Resources/
│   │       ├── LeitoResource.php       # JSON Resource para leitos
│   │       └── PacienteResource.php    # JSON Resource para pacientes
│   ├── Models/
│   │   ├── AuditoriaLeito.php          # Model de auditoria
│   │   ├── Leito.php                   # Model de leito (com enum cast)
│   │   └── Paciente.php               # Model de paciente
│   ├── Providers/
│   │   └── AppServiceProvider.php      # Rate limiting config
│   ├── Rules/
│   │   └── CpfValido.php              # Validacao de CPF (algoritmo oficial)
│   └── Services/
│       └── LeitoService.php            # Logica de negocio + auditoria
├── bootstrap/
│   └── app.php                         # Exception handler global (JSON)
├── database/
│   ├── migrations/                     # Schema do banco
│   └── seeders/
│       └── DatabaseSeeder.php          # 15 pacientes + 15 leitos
├── routes/
│   └── api.php                         # Rotas com rate limiting
├── tests/
│   └── Feature/
│       └── LeitoApiTest.php            # 18 testes, 66 assertions
└── README.md
```

---

## Regras de Negocio

### Validacoes Implementadas

1. **Um paciente nao pode estar em mais de um leito simultaneamente**
   - Ao tentar ocupar um leito com paciente ja internado, retorna erro 400

2. **Cada leito so pode ter um paciente por vez**
   - Ao tentar ocupar leito ja ocupado ou em manutencao, retorna erro 400
   - Constraint UNIQUE no banco garante integridade

3. **Transferencias sao atomicas (DB::transaction)**
   - Liberar leito origem + ocupar leito destino acontecem na mesma transacao
   - Se uma falhar, a outra tambem e revertida

4. **CPF unico e validado**
   - Constraint UNIQUE no banco de dados
   - Validacao com algoritmo oficial (digitos verificadores)
   - Aceita apenas 11 digitos numericos (sem mascara)

5. **Rate Limiting**
   - 60 requisicoes por minuto por IP
   - Retorna HTTP 429 quando excedido

6. **Auditoria completa**
   - Toda acao (OCUPAR, LIBERAR, TRANSFERIR) e registrada na tabela `auditoria_leitos`
   - Inclui log em arquivo via `Log::info()`

---

## Testes Automatizados

O projeto conta com **18 testes** cobrindo todos os endpoints e regras de negocio:

```
 PASS  Tests\Feature\LeitoApiTest
 - listar leitos retorna paginado
 - listar leitos com per page customizado
 - listar leitos com paciente
 - ocupar leito sucesso
 - ocupar leito ja ocupado
 - ocupar paciente ja internado
 - ocupar leito validacao campos obrigatorios
 - liberar leito sucesso
 - liberar leito validacao
 - transferir sucesso
 - transferir sem paciente na origem
 - transferir destino ocupado
 - buscar por cpf encontrado
 - buscar por cpf nao encontrado
 - buscar por cpf invalido
 - buscar por cpf paciente nao internado
 - listar pacientes retorna paginado
 - rate limiting

Tests: 18 passed (66 assertions)
```

Para executar:

```bash
php artisan test
```

---

## Comandos Uteis

```bash
# Resetar banco com dados de teste
php artisan migrate:fresh --seed

# Executar testes
php artisan test

# Ver rotas da API
php artisan route:list

# Limpar cache
php artisan cache:clear && php artisan config:clear
```

---

## Decisoes de Design

1. **Service Layer**: Logica de negocio isolada do controller, facilitando testes e manutencao
2. **Custom Exceptions**: Erros de negocio lancam exceptions tipadas, tratadas globalmente no handler
3. **API Resources**: Formato de saida JSON desacoplado dos models, permitindo evolucao independente
4. **Enums PHP 8.1**: Type safety para status e tipos de leito, com cast automatico no Eloquent
5. **CPF apenas numerico**: Simplifica integracoes, evita problemas de encoding na URL
6. **Paginacao configuravel**: `per_page` via query parameter com limites de seguranca (1-100)
7. **SQLite**: Portabilidade total, sem necessidade de configurar servidor de banco
8. **Scramble**: Documentacao Swagger gerada automaticamente a partir do codigo

---

## Troubleshooting

### Erro: "Database file not found"

```bash
touch database/database.sqlite
php artisan migrate:fresh --seed
```

### Erro: "Class not found"

```bash
composer dump-autoload
```

### Erro 404 nas rotas da API

Certifique-se de que o arquivo `bootstrap/app.php` contém a configuracao de rotas API.

### Erro 429 (Too Many Requests)

O rate limiting esta ativo. Aguarde 1 minuto ou ajuste o limite em `AppServiceProvider.php`.

---

## Licenca

Este projeto foi desenvolvido como parte de um teste tecnico.
