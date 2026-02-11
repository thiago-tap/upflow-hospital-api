# 🏥 API de Gerenciamento de Leitos Hospitalares

API REST desenvolvida em Laravel para gerenciar a ocupação de leitos em um hospital, permitindo controle completo sobre internações, transferências e consultas de pacientes.

## 📋 Sobre o Projeto

Sistema de gerenciamento de leitos hospitalares que permite:
- ✅ Internar pacientes em leitos
- ✅ Liberar leitos ocupados
- ✅ Transferir pacientes entre leitos
- ✅ Consultar leito de um paciente por CPF
- ✅ Verificar status de ocupação dos leitos
- ✅ Listar todos os leitos com seus respectivos status

## 🚀 Tecnologias Utilizadas

- **PHP 8.2+**
- **Laravel 12.x**
- **SQLite** (banco de dados leve e portável)
- **Scramble** (documentação OpenAPI/Swagger automática)

## 📦 Pré-requisitos

Antes de começar, certifique-se de ter instalado:

- PHP >= 8.2
- Composer
- SQLite3
- Git

## 🔧 Instalação

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
# Copie o arquivo de ambiente (se necessário)
cp .env.example .env

# Gere a chave da aplicação
php artisan key:generate
```

### 4. Configure o banco de dados

O projeto já está configurado para usar SQLite. O arquivo `.env` já contém:

```env
DB_CONNECTION=sqlite
```

### 5. Execute as migrations e seeders

```bash
php artisan migrate:fresh --seed
```

Isso criará:
- **15 pacientes** de teste com CPFs únicos
- **15 leitos** (5 UTI, 5 Enfermaria, 5 Quartos)

### 6. Inicie o servidor

```bash
php artisan serve
```

A API estará disponível em: **http://localhost:8000**

## 📚 Documentação da API

### Interface Interativa (Swagger/OpenAPI)

Acesse a documentação interativa completa em:

**http://localhost:8000/docs/api**

Nesta interface você pode:
- 📖 Ver todos os endpoints disponíveis
- 🧪 Testar as requisições diretamente pelo navegador
- 📋 Visualizar exemplos de request e response
- ✅ Ver validações e códigos de status HTTP

### Endpoints Disponíveis

#### 1. **Listar todos os leitos**

```http
GET /api/leitos
```

**Resposta:**
```json
[
  {
    "id_leito": 1,
    "codigo": "UTI-01",
    "status": "LIVRE",
    "paciente": null
  }
]
```

---

#### 2. **Ocupar um leito**

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
- Leito já ocupado
- Paciente já está em outro leito

---

#### 3. **Liberar um leito**

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

**Resposta:**
```json
{
  "mensagem": "Leito liberado com sucesso."
}
```

---

#### 4. **Transferir paciente**

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

**Resposta:**
```json
{
  "mensagem": "Transferência realizada com sucesso."
}
```

**Possíveis Erros (400):**
- Leito de origem não possui paciente
- Leito de destino já está ocupado

---

#### 5. **Buscar leito por CPF**

```http
GET /api/pacientes/{cpf}/leito
```

**Exemplo:**
```http
GET /api/pacientes/123.456.789-00/leito
```

**Resposta (200):**
```json
{
  "paciente": "João Silva",
  "leito": "UTI-01",
  "status": "OCUPADO"
}
```

**Resposta se não encontrado (404):**
```json
{
  "erro": "Paciente não encontrado."
}
```

---

## 🧪 Testando a API

### Opção 1: Interface Swagger (Recomendado)

1. Acesse http://localhost:8000/docs/api
2. Clique em qualquer endpoint
3. Clique em **"Try it out"**
4. Preencha os dados necessários
5. Clique em **"Send"** ou **"Execute"**

### Opção 2: cURL

```bash
# Listar todos os leitos
curl http://localhost:8000/api/leitos

# Ocupar um leito
curl -X POST http://localhost:8000/api/leitos/ocupar \
  -H "Content-Type: application/json" \
  -d '{"id_leito": 1, "id_paciente": 1}'

# Buscar leito por CPF
curl http://localhost:8000/api/pacientes/123.456.789-00/leito
```

### Opção 3: Postman

Importe a coleção usando a especificação OpenAPI:
```
http://localhost:8000/docs/api.json
```

---

## 📊 Dados de Teste

### Pacientes (15 cadastrados)

| ID | Nome | CPF |
|---|---|---|
| 1 | João Silva | 123.456.789-00 |
| 2 | Maria Santos | 234.567.890-11 |
| 3 | Pedro Oliveira | 345.678.901-22 |
| 4 | Ana Costa | 456.789.012-33 |
| 5 | Carlos Souza | 567.890.123-44 |
| 6 | Juliana Ferreira | 678.901.234-55 |
| 7 | Roberto Lima | 789.012.345-66 |
| 8 | Fernanda Alves | 890.123.456-77 |
| 9 | Lucas Pereira | 901.234.567-88 |
| 10 | Patrícia Rodrigues | 012.345.678-99 |
| 11 | Ricardo Martins | 111.222.333-44 |
| 12 | Camila Souza | 222.333.444-55 |
| 13 | Bruno Costa | 333.444.555-66 |
| 14 | Amanda Oliveira | 444.555.666-77 |
| 15 | Felipe Santos | 555.666.777-88 |

### Leitos (15 disponíveis)

- **UTI:** UTI-01, UTI-02, UTI-03, UTI-04, UTI-05
- **Enfermaria:** ENFERMARIA-01 a 05
- **Quartos:** QUARTO-01 a 05

---

## 🏗️ Estrutura do Projeto

```
hospital-api/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── LeitoController.php    # Controller principal da API
│   └── Models/
│       ├── Leito.php                  # Model de Leito
│       └── Paciente.php               # Model de Paciente
├── database/
│   ├── migrations/                     # Migrations do banco
│   └── seeders/
│       └── DatabaseSeeder.php         # Seed de dados de teste
├── routes/
│   └── api.php                        # Rotas da API
├── .env                               # Configurações do ambiente
└── README.md                          # Este arquivo
```

---

## 🔐 Regras de Negócio

### ✅ Validações Implementadas

1. **Um paciente não pode estar em mais de um leito simultaneamente**
   - Ao tentar ocupar um leito com paciente já internado, retorna erro 400

2. **Cada leito só pode ter um paciente por vez**
   - Ao tentar ocupar leito já ocupado, retorna erro 400

3. **Transferências só ocorrem entre leitos válidos**
   - Leito de origem deve ter paciente
   - Leito de destino deve estar livre

4. **CPF único por paciente**
   - Constraint UNIQUE no banco de dados

---

## 🧹 Comandos Úteis

### Resetar o banco de dados

```bash
php artisan migrate:fresh --seed
```

### Ver rotas da API

```bash
php artisan route:list
```

### Limpar cache

```bash
php artisan cache:clear
php artisan config:clear
```

---

## 📝 Observações Técnicas

### Arquitetura

- **RESTful API** seguindo as melhores práticas
- **Controllers magros** com lógica de negócio encapsulada
- **Validações** usando Laravel Request Validation
- **Relacionamentos Eloquent** (belongsTo, hasOne)
- **Responses padronizadas** com códigos HTTP apropriados

### Banco de Dados

- **SQLite** para facilitar portabilidade e execução
- **Migrations versionadas** para controle de schema
- **Seeders** para dados de teste reproduzíveis
- **Foreign Keys** com constraints para integridade

### Documentação

- **OpenAPI/Swagger** gerado automaticamente via Scramble
- **PHPDoc** completo nos métodos do controller
- **Interface interativa** para testes sem necessidade de ferramentas externas

---

## 🐛 Troubleshooting

### Erro: "Database file not found"

```bash
# Certifique-se de que o arquivo foi criado
touch database/database.sqlite
php artisan migrate:fresh --seed
```

### Erro: "Class not found"

```bash
# Recrie o autoload
composer dump-autoload
```

### Erro 404 nas rotas da API

Certifique-se de que o arquivo `bootstrap/app.php` contém:

```php
->withRouting(
    api: __DIR__.'/../routes/api.php',
    // ...
)
```

---

## 👨‍💻 Desenvolvimento

### Tecnologias e Pacotes

- **dedoc/scramble**: Documentação OpenAPI automática
- **Laravel Sanctum**: Preparado para autenticação (não implementada conforme requisitos)
- **SQLite**: Banco de dados leve e portável

### Decisões de Design

1. **SQLite em vez de MySQL/PostgreSQL**: Facilita a execução sem necessidade de configurar servidor de BD
2. **Scramble**: Documentação automática e atualizada com o código
3. **Seeders com dados realistas**: 15 pacientes e 15 leitos para testes completos
4. **Validação no Controller**: Mantém a simplicidade conforme escopo do projeto

---

## 📄 Licença

Este projeto foi desenvolvido como parte de um teste técnico.

---

## 📧 Contato

Para dúvidas sobre o projeto, entre em contato através do repositório.

---

**Desenvolvido com ❤️ usando Laravel**
