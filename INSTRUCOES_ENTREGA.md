# Instrucoes para Entrega do Teste Tecnico

## Checklist Final

Antes de enviar, certifique-se de que:

- [ ] O README.md esta completo e atualizado
- [ ] O projeto esta funcionando localmente (`php artisan serve`)
- [ ] A documentacao Swagger esta acessivel em `/docs/api`
- [ ] Todos os endpoints foram testados
- [ ] Os 18 testes automatizados passam (`php artisan test`)
- [ ] O banco de dados foi populado com seeders (15 pacientes + 15 leitos)
- [ ] O repositorio Git foi inicializado
- [ ] O commit foi feito com todas as alteracoes

---

## Opcao 1: Publicar no GitHub (Recomendado)

### Passo 1: Criar repositorio no GitHub

1. Acesse: https://github.com/new
2. Preencha os campos:
   - **Repository name:** `hospital-api`
   - **Description:** "API REST para gerenciamento de leitos hospitalares - Laravel 12"
   - **Visibility:** Escolha **Public** ou **Private**
   - **NAO marque** "Initialize this repository with a README"
3. Clique em **"Create repository"**

### Passo 2: Adicionar o remote e fazer push

No terminal do projeto, execute:

```bash
# Adicione o remote (substitua SEU-USUARIO pelo seu usuario do GitHub)
git remote add origin https://github.com/SEU-USUARIO/hospital-api.git

# Renomeie a branch para main (padrao do GitHub)
git branch -M main

# Faca o push
git push -u origin main
```

### Passo 3: Enviar o link

Envie o link do repositorio com as seguintes informacoes:

```
Repositorio: https://github.com/SEU-USUARIO/hospital-api

Documentacao: Todas as instrucoes estao no README.md do projeto
API Docs: Apos executar o projeto, a documentacao interativa esta disponivel em http://localhost:8000/docs/api

Destaques tecnicos:
- Laravel 12.x + PHP 8.2 + SQLite
- Arquitetura em camadas (Controller -> Service -> Model)
- 6 endpoints RESTful implementados
- 18 testes automatizados (66 assertions)
- Documentacao OpenAPI/Swagger automatica
- Auditoria completa de acoes
- Rate limiting (60 req/min)
- Validacao de CPF com algoritmo oficial
- Custom Exceptions com status HTTP
- Seeds com 15 pacientes e 15 leitos para testes

Tempo de execucao apos clone:
1. composer install
2. php artisan migrate:fresh --seed
3. php artisan serve
4. Acessar http://localhost:8000/docs/api
```

---

## Opcao 2: Publicar no GitLab

### Passo 1: Criar repositorio no GitLab

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

## Opcao 3: Enviar como arquivo ZIP

Se preferir nao usar GitHub/GitLab:

### Passo 1: Limpar arquivos desnecessarios

```bash
# Remova a pasta vendor (sera reinstalada)
rm -rf vendor

# Remova node_modules se existir
rm -rf node_modules

# Remova o banco de dados (sera recriado)
rm -f database/database.sqlite
```

### Passo 2: Criar arquivo ZIP

**Windows:**
1. Clique com botao direito na pasta `hospital-api`
2. Selecione "Enviar para" > "Pasta compactada"
3. Renomeie para `hospital-api.zip`

**Linux/Mac:**
```bash
cd ..
zip -r hospital-api.zip hospital-api -x "*/vendor/*" "*/node_modules/*" "*/.git/*"
```

---

## Verificacoes Finais Antes de Enviar

Execute estes comandos para garantir que tudo esta funcionando:

```bash
# 1. Instale as dependencias
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

# 6. Verifique a documentacao
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

### Regras de negocio

- Paciente nao pode estar em multiplos leitos simultaneamente
- Leito aceita apenas um paciente por vez
- Transferencias sao atomicas (DB::transaction)
- CPF unico e validado com algoritmo oficial

### Diferenciais implementados

| Diferencial | Descricao |
| --- | --- |
| **Service Layer** | Logica de negocio isolada em `LeitoService.php`, separada do controller |
| **Custom Exceptions** | Classe `LeitoException` com metodos estaticos para cada erro de negocio e status HTTP apropriado |
| **Global Exception Handler** | Erros de negocio tratados globalmente em `bootstrap/app.php`, retornando JSON padronizado |
| **API Resources** | `LeitoResource` e `PacienteResource` padronizam o formato JSON de saida |
| **PHP Enums** | `StatusLeito` e `TipoLeito` com cast automatico no Eloquent para type safety |
| **Paginacao configuravel** | Parametro `per_page` (1 a 100) em todos os endpoints de listagem |
| **Validacao de CPF** | Rule customizada `CpfValido` com algoritmo oficial de digitos verificadores |
| **Rate Limiting** | 60 requisicoes por minuto por IP, configurado via `AppServiceProvider` |
| **Auditoria completa** | Tabela `auditoria_leitos` registra toda acao (OCUPAR, LIBERAR, TRANSFERIR) + Log::info |
| **Testes automatizados** | 18 testes com 66 assertions cobrindo todos os endpoints e regras de negocio |
| **Documentacao Swagger** | OpenAPI gerada automaticamente via Scramble em `/docs/api` |
| **Seeds completos** | 15 pacientes com CPFs validos + 15 leitos (UTI, Enfermaria, Quarto) |
| **SQLite** | Portabilidade total, sem necessidade de configurar servidor de banco |

### Arquitetura

```
Request -> Controller -> Service -> Model -> Database
               |
          Resource -> JSON Response
               |
        LeitoException (erros de negocio)
               |
        AuditoriaLeito (log de acoes)
```

| Camada | Responsabilidade | Arquivo |
| --- | --- | --- |
| **Controller** | Validacao de input e formatacao de response | `LeitoController.php` |
| **Service** | Logica de negocio, transacoes e auditoria | `LeitoService.php` |
| **Model** | Relacionamentos e casting de dados | `Leito.php`, `Paciente.php`, `AuditoriaLeito.php` |
| **Resource** | Padronizacao do formato JSON de saida | `LeitoResource.php`, `PacienteResource.php` |
| **Enum** | Type safety para status e tipos de leito | `StatusLeito.php`, `TipoLeito.php` |
| **Exception** | Erros de negocio com HTTP status codes | `LeitoException.php` |
| **Rule** | Validacao customizada de CPF | `CpfValido.php` |

---

## Dicas Finais

1. **Teste tudo antes de enviar** - Execute `php artisan test` para garantir que os 18 testes passam
2. **Nao inclua o .env no repositorio** - O .gitignore ja esta configurado para isso
3. **Use a documentacao Swagger** - Mencione que ha uma interface interativa em `/docs/api`
4. **Destaque os diferenciais** - Service Layer, testes automatizados e auditoria sao pontos fortes

---

## Pronto para enviar!

Apos seguir um dos metodos acima, seu projeto estara pronto para avaliacao.
