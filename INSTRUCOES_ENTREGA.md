# Instruções para Entrega do Teste Técnico

## Checklist Final

Antes de enviar, certifique-se de que:

- [ ] O README.md está completo e atualizado
- [ ] O projeto está funcionando localmente (`php artisan serve`)
- [ ] A documentação Swagger está acessível em `/docs/api`
- [ ] Todos os endpoints foram testados
- [ ] Os 18 testes automatizados passam (`php artisan test`)
- [ ] O banco de dados foi populado com seeders (15 pacientes + 15 leitos)
- [ ] O repositório Git foi inicializado
- [ ] O commit foi feito com todas as alterações

---

## Opção 1: Publicar no GitHub (Recomendado)

### Passo 1: Criar repositório no GitHub

1. Acesse: https://github.com/new
2. Preencha os campos:
   - **Repository name:** `hospital-api`
   - **Description:** "API REST para gerenciamento de leitos hospitalares - Laravel 12"
   - **Visibility:** Escolha **Public** ou **Private**
   - **NÃO marque** "Initialize this repository with a README"
3. Clique em **"Create repository"**

### Passo 2: Adicionar o remote e fazer push

No terminal do projeto, execute:

```bash
# Adicione o remote (substitua SEU-USUARIO pelo seu usuário do GitHub)
git remote add origin https://github.com/SEU-USUARIO/hospital-api.git

# Renomeie a branch para main (padrão do GitHub)
git branch -M main

# Faça o push
git push -u origin main
```

### Passo 3: Enviar o link

Envie o link do repositório com as seguintes informações:

```
Repositório: https://github.com/SEU-USUARIO/hospital-api

Documentação: Todas as instruções estão no README.md do projeto.
API Docs: Após executar o projeto, a documentação interativa está disponível em http://localhost:8000/docs/api

Destaques técnicos:
- Laravel 12.x + PHP 8.2 + SQLite
- Arquitetura em camadas (Controller -> Service -> Model)
- 6 endpoints RESTful implementados
- 18 testes automatizados (66 assertions)
- Documentação OpenAPI/Swagger automática
- Auditoria completa de ações
- Rate limiting (60 req/min)
- Validação de CPF com algoritmo oficial
- Custom Exceptions com status HTTP
- Seeds com 15 pacientes e 15 leitos para testes

Tempo de execução após clone:
1. composer install
2. php artisan migrate:fresh --seed
3. php artisan serve
4. Acessar http://localhost:8000/docs/api
```

---

## Opção 2: Publicar no GitLab

### Passo 1: Criar repositório no GitLab

1. Acesse: https://gitlab.com/projects/new
2. Escolha **"Create blank project"**
3. Preencha:
   - **Project name:** `hospital-api`
   - **Visibility Level:** Public ou Private
   - **Desmarque** "Initialize repository with a README"
4. Clique em **"Create project"**

### Passo 2: Adicionar remote e push

```bash
git remote add origin https://gitlab.com/SEU-USUARIO/hospital-api.git
git branch -M main
git push -u origin main
```

---

## Opção 3: Enviar como arquivo ZIP

Se preferir não usar GitHub/GitLab:

### Passo 1: Limpar arquivos desnecessários

```bash
# Remova a pasta vendor (será reinstalada)
rm -rf vendor

# Remova node_modules se existir
rm -rf node_modules

# Remova o banco de dados (será recriado)
rm -f database/database.sqlite
```

### Passo 2: Criar arquivo ZIP

**Windows:**
1. Clique com botão direito na pasta `hospital-api`
2. Selecione "Enviar para" > "Pasta compactada"
3. Renomeie para `hospital-api.zip`

**Linux/Mac:**
```bash
cd ..
zip -r hospital-api.zip hospital-api -x "*/vendor/*" "*/node_modules/*" "*/.git/*"
```

---

## Verificações Finais Antes de Enviar

Execute estes comandos para garantir que tudo está funcionando:

```bash
# 1. Instale as dependências
composer install

# 2. Recrie o banco do zero com dados de teste
php artisan migrate:fresh --seed

# 3. Execute os testes automatizados (18 testes, 66 assertions)
php artisan test

# 4. Inicie o servidor
php artisan serve

# 5. Teste os endpoints (em outro terminal)
curl http://localhost:8000/api/leitos
curl http://localhost:8000/api/pacientes
curl http://localhost:8000/api/pacientes/52998224725/leito

# 6. Verifique a documentação
# Abra: http://localhost:8000/docs/api
```

Resultado esperado dos testes:

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

---

## O que foi implementado

### Funcionalidades

- Incluir paciente em leito (POST /api/leitos/ocupar)
- Desocupar leito (POST /api/leitos/liberar)
- Transferir paciente entre leitos (POST /api/leitos/transferir)
- Descobrir leito por CPF (GET /api/pacientes/{cpf}/leito)
- Listar todos os leitos com status (GET /api/leitos)
- Listar todos os pacientes (GET /api/pacientes)

### Regras de negócio

- Paciente não pode estar em múltiplos leitos simultaneamente
- Leito aceita apenas um paciente por vez
- Transferências são atômicas (DB::transaction)
- CPF único e validado com algoritmo oficial

### Diferenciais implementados

| Diferencial | Descrição |
| --- | --- |
| **Service Layer** | Lógica de negócio isolada em `LeitoService.php`, separada do controller |
| **Custom Exceptions** | Classe `LeitoException` com métodos estáticos para cada erro de negócio e status HTTP apropriado |
| **Global Exception Handler** | Erros de negócio tratados globalmente em `bootstrap/app.php`, retornando JSON padronizado |
| **API Resources** | `LeitoResource` e `PacienteResource` padronizam o formato JSON de saída |
| **PHP Enums** | `StatusLeito` e `TipoLeito` com cast automático no Eloquent para type safety |
| **Paginação configurável** | Parâmetro `per_page` (1 a 100) em todos os endpoints de listagem |
| **Validação de CPF** | Rule customizada `CpfValido` com algoritmo oficial de dígitos verificadores |
| **Rate Limiting** | 60 requisições por minuto por IP, configurado via `AppServiceProvider` |
| **Auditoria completa** | Tabela `auditoria_leitos` registra toda ação (OCUPAR, LIBERAR, TRANSFERIR) + Log::info |
| **Testes automatizados** | 18 testes com 66 assertions cobrindo todos os endpoints e regras de negócio |
| **Documentação Swagger** | OpenAPI gerada automaticamente via Scramble em `/docs/api` |
| **Seeds completos** | 15 pacientes com CPFs válidos + 15 leitos (UTI, Enfermaria, Quarto) |
| **SQLite** | Portabilidade total, sem necessidade de configurar servidor de banco |

### Arquitetura

```
Request -> Controller -> Service -> Model -> Database
               |
          Resource -> JSON Response
               |
        LeitoException (erros de negócio)
               |
        AuditoriaLeito (log de ações)
```

| Camada | Responsabilidade | Arquivo |
| --- | --- | --- |
| **Controller** | Validação de input e formatação de response | `LeitoController.php` |
| **Service** | Lógica de negócio, transações e auditoria | `LeitoService.php` |
| **Model** | Relacionamentos e casting de dados | `Leito.php`, `Paciente.php`, `AuditoriaLeito.php` |
| **Resource** | Padronização do formato JSON de saída | `LeitoResource.php`, `PacienteResource.php` |
| **Enum** | Type safety para status e tipos de leito | `StatusLeito.php`, `TipoLeito.php` |
| **Exception** | Erros de negócio com HTTP status codes | `LeitoException.php` |
| **Rule** | Validação customizada de CPF | `CpfValido.php` |

---

## Dicas Finais

1. **Teste tudo antes de enviar** - Execute `php artisan test` para garantir que os 18 testes passam.
2. **Não inclua o .env no repositório** - O .gitignore já está configurado para isso.
3. **Use a documentação Swagger** - Mencione que há uma interface interativa em `/docs/api`.
4. **Destaque os diferenciais** - Service Layer, testes automatizados e auditoria são pontos fortes.

---

## Pronto para enviar!

Após seguir um dos métodos acima, seu projeto estará pronto para avaliação.
