# API de Gerenciamento de Leitos Hospitalares

API REST desenvolvida em Laravel para gerenciar a ocupação de leitos em um hospital, permitindo controle completo sobre internações, transferências e consultas de pacientes.

## Sobre o Projeto

Sistema de gerenciamento de leitos hospitalares que permite:

- Internar pacientes em leitos
- Liberar leitos ocupados
- Transferir pacientes entre leitos (com transação atômica)
- Listar todos os pacientes disponíveis (com paginação)
- Consultar leito de um paciente por CPF (com validação oficial)
- Verificar status de ocupação dos leitos
- Listar todos os leitos com seus respectivos status (com paginação)
- Auditoria completa de todas as ações (ocupar, liberar, transferir)

## Tecnologias Utilizadas

- **PHP 8.2+**
- **Laravel 12.x**
- **SQLite** (banco de dados leve e portável)
- **Scramble** (documentação OpenAPI/Swagger automática)
- **PHPUnit** (testes automatizados)

## Arquitetura do Projeto

O projeto segue uma arquitetura em camadas com separação clara de responsabilidades:

```
Request -> Controller -> Service -> Model -> Database
               |
          Resource -> JSON Response
               |
        LeitoException (erros de negócio)
               |
        AuditoriaLeito (log de ações)
```

### Camadas

| Camada | Responsabilidade | Arquivos |
|---|---|---|
| **Controller** | Validação de input e formatação de response | `LeitoController.php` |
| **Service** | Lógica de negócio, transações e auditoria | `LeitoService.php` |
| **Model** | Relacionamentos e casting de dados | `Leito.php`, `Paciente.php`, `AuditoriaLeito.php` |
| **Resource** | Padronização do formato JSON de saída | `LeitoResource.php`, `PacienteResource.php` |
| **Enum** | Type safety para status e tipos de leito | `StatusLeito.php`, `TipoLeito.php` |
| **Exception** | Erros de negócio com HTTP status codes | `LeitoException.php` |
| **Rule** | Validação customizada de CPF | `CpfValido.php` |

## Pré-requisitos

- PHP >= 8.2
- Composer
- SQLite3
- Git

## Instalação

### 1. Clone o repositório

```bash
git clone <url-do-repositorio>
cd hospital-api
```

### 2. Instale as dependências

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

Isso criará:

- **15 pacientes** de teste com CPFs válidos (apenas numérico, 11 dígitos)
- **15 leitos** (5 UTI, 5 Enfermaria, 5 Quartos)
- **Tabela de auditoria** para registro de ações

### 5. Inicie o servidor

```bash
php artisan serve
```

A API estará disponível em: **http://localhost:8000**

### 6. Execute os testes

```bash
php artisan test
```

Resultado esperado: **18 testes, 66 assertions, 0 falhas**.

## Documentação da API

### Interface Interativa (Swagger/OpenAPI)

Acesse a documentação interativa completa em:

**http://localhost:8000/docs/api**

Nesta interface você pode:

- Ver todos os endpoints disponíveis
- Testar as requisições diretamente pelo navegador
- Visualizar exemplos de request e response
- Ver validações e códigos de status HTTP

### Endpoints Disponíveis

#### 1. Listar todos os leitos

```http
GET /api/leitos
GET /api/leitos?per_page=5
```

Parâmetros de query opcionais:

| Parâmetro | Tipo | Padrão | Descrição |
|---|---|---|---|
| `per_page` | int | 15 | Registros por página (min: 1, max: 100) |

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

Parâmetros de query opcionais:

| Parâmetro | Tipo | Padrão | Descrição |
|---|---|---|---|
| `per_page` | int | 15 | Registros por página (min: 1, max: 100) |

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

**Possíveis Erros (400):**

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

**Possíveis Erros (400):**

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

**Importante:** O CPF deve ser enviado apenas com números (11 dígitos), sem pontos ou traços.

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

**Resposta (200) - Paciente não internado:**

```json
{
    "mensagem": "O paciente nao esta internado no momento."
}
```

**Resposta (404) - Paciente não encontrado:**

```json
{
    "erro": "Paciente nao encontrado."
}
```

**Resposta (422) - CPF inválido:**

```json
{
    "erro": "O cpf deve conter exatamente 11 digitos numericos."
}
```

---

## Códigos de Status HTTP

| Código | Significado | Quando ocorre |
|---|---|---|
| 200 | Sucesso | Operação realizada com sucesso |
| 400 | Erro de negócio | Leito ocupado, paciente já internado, etc. |
| 404 | Não encontrado | Paciente não existe no banco |
| 422 | Validação falhou | Campos obrigatórios ausentes, CPF inválido |
| 429 | Rate limit | Mais de 60 requisições por minuto |

---

## Testando a API

### Opção 1: Interface Swagger (Recomendado)

1. Acesse http://localhost:8000/docs/api
2. Clique em qualquer endpoint
3. Clique em **"Try it out"**
4. Preencha os dados necessários
5. Clique em **"Send"** ou **"Execute"**

### Opção 2: cURL

```bash
# Listar todos os leitos (5 por página)
curl http://localhost:8000/api/leitos?per_page=5

# Ocupar um leito
curl -X POST http://localhost:8000/api/leitos/ocupar \
  -H "Content-Type: application/json" \
  -d '{"id_leito": 1, "id_paciente": 1}'

# Buscar leito por CPF (apenas números)
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

### Opção 3: Postman

Importe a coleção usando a especificação OpenAPI:

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

### Leitos (15 disponíveis)

| Tipo | Códigos |
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
│   │   └── LeitoException.php          # Exceptions de negócio com status HTTP
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── LeitoController.php     # Controller (validação + response)
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
│   │   └── CpfValido.php              # Validação de CPF (algoritmo oficial)
│   └── Services/
│       └── LeitoService.php            # Lógica de negócio + auditoria
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

## Regras de Negócio

### Validações Implementadas

1. **Um paciente não pode estar em mais de um leito simultaneamente**
   - Ao tentar ocupar um leito com paciente já internado, retorna erro 400

2. **Cada leito só pode ter um paciente por vez**
   - Ao tentar ocupar leito já ocupado ou em manutenção, retorna erro 400
   - Constraint UNIQUE no banco garante integridade

3. **Transferências são atômicas (DB::transaction)**
   - Liberar leito origem + ocupar leito destino acontecem na mesma transação
   - Se uma falhar, a outra também é revertida

4. **CPF único e validado**
   - Constraint UNIQUE no banco de dados
   - Validação com algoritmo oficial (dígitos verificadores)
   - Aceita apenas 11 dígitos numéricos (sem máscara)

5. **Rate Limiting**
   - 60 requisições por minuto por IP
   - Retorna HTTP 429 quando excedido

6. **Auditoria completa**
   - Toda ação (OCUPAR, LIBERAR, TRANSFERIR) é registrada na tabela `auditoria_leitos`
   - Inclui log em arquivo via `Log::info()`

---

## Testes Automatizados

O projeto conta com **18 testes** cobrindo todos os endpoints e regras de negócio:

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

## Comandos Úteis

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

## Decisões de Design

1. **Service Layer**: Lógica de negócio isolada do controller, facilitando testes e manutenção
2. **Custom Exceptions**: Erros de negócio lançam exceptions tipadas, tratadas globalmente no handler
3. **API Resources**: Formato de saída JSON desacoplado dos models, permitindo evolução independente
4. **Enums PHP 8.1**: Type safety para status e tipos de leito, com cast automático no Eloquent
5. **CPF apenas numérico**: Simplifica integrações, evita problemas de encoding na URL
6. **Paginação configurável**: `per_page` via query parameter com limites de segurança (1-100)
7. **SQLite**: Portabilidade total, sem necessidade de configurar servidor de banco
8. **Scramble**: Documentação Swagger gerada automaticamente a partir do código

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

Certifique-se de que o arquivo `bootstrap/app.php` contém a configuração de rotas API.

### Erro 429 (Too Many Requests)

O rate limiting está ativo. Aguarde 1 minuto ou ajuste o limite em `AppServiceProvider.php`.

---

## Licença

Este projeto foi desenvolvido como parte de um teste técnico.
