# 📚 Guia de Uso da API - Interface Swagger/OpenAPI

## 🔗 Acesso à Documentação

Abra seu navegador e acesse: **http://localhost:8000/docs/api**

---

## 🎯 Dados de Teste Disponíveis

### 👥 Pacientes (15 no total)

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

### 🛏️ Leitos (15 no total)

**UTI:** UTI-01, UTI-02, UTI-03, UTI-04, UTI-05
**Enfermaria:** ENFERMARIA-01, ENFERMARIA-02, ENFERMARIA-03, ENFERMARIA-04, ENFERMARIA-05
**Quartos:** QUARTO-01, QUARTO-02, QUARTO-03, QUARTO-04, QUARTO-05

---

## 📖 Como Usar a Interface Swagger

### 1️⃣ **Listar Todos os Leitos**

**Endpoint:** `GET /api/leitos`

**Como testar:**
1. Clique em `GET /api/leitos` na interface
2. Clique no botão **"Send API Request"** ou **"Try it out"**
3. Clique em **"Send"** ou **"Execute"**
4. Veja a resposta abaixo mostrando todos os leitos

**Resposta esperada:**
```json
[
  {
    "id_leito": 1,
    "codigo": "UTI-01",
    "status": "LIVRE",
    "paciente": null
  },
  {
    "id_leito": 2,
    "codigo": "UTI-02",
    "status": "LIVRE",
    "paciente": null
  }
  // ... mais leitos
]
```

---

### 2️⃣ **Ocupar um Leito**

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
4. Clique em **"Send"** ou **"Execute"**

**Exemplo de uso:**
- Para internar **João Silva** (ID: 1) no leito **UTI-01** (ID: 1):
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

**Resposta de erro (400):**
```json
{
  "erro": "Este leito já está ocupado."
}
```
ou
```json
{
  "erro": "Este paciente já está ocupando outro leito."
}
```

---

### 3️⃣ **Liberar um Leito**

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
4. Clique em **"Send"**

**Exemplo:** Para liberar o leito UTI-01 (ID: 1):
```json
{
  "id_leito": 1
}
```

**Resposta de sucesso:**
```json
{
  "mensagem": "Leito liberado com sucesso."
}
```

---

### 4️⃣ **Transferir Paciente**

**Endpoint:** `POST /api/leitos/transferir`

**Como testar:**
1. Primeiro, **ocupe um leito** (use o endpoint 2️⃣)
2. Clique em `POST /api/leitos/transferir`
3. Clique em **"Try it out"**
4. No campo de **Request body**:
   ```json
   {
     "id_leito_atual": 1,
     "id_leito_destino": 2
   }
   ```
5. Clique em **"Send"**

**Exemplo:** Transferir paciente do UTI-01 para UTI-02:
```json
{
  "id_leito_atual": 1,
  "id_leito_destino": 2
}
```

**Resposta de sucesso:**
```json
{
  "mensagem": "Transferência realizada com sucesso."
}
```

**Resposta de erro (400):**
```json
{
  "erro": "Não há paciente no leito de origem para transferir."
}
```
ou
```json
{
  "erro": "O leito de destino já está ocupado."
}
```

---

### 5️⃣ **Buscar Leito por CPF**

**Endpoint:** `GET /api/pacientes/{cpf}/leito`

**Como testar:**
1. Primeiro, **ocupe um leito** com um paciente (use o endpoint 2️⃣)
2. Clique em `GET /api/pacientes/{cpf}/leito`
3. Clique em **"Try it out"**
4. No campo **cpf**, digite o CPF do paciente (exemplo: `123.456.789-00`)
5. Clique em **"Send"**

**Exemplo:** Buscar onde está o paciente João Silva:
- CPF: `123.456.789-00`

**Resposta de sucesso (200):**
```json
{
  "paciente": "João Silva",
  "leito": "UTI-01",
  "status": "OCUPADO"
}
```

**Resposta se paciente não está internado:**
```json
{
  "mensagem": "O paciente não está internado no momento."
}
```

**Resposta de erro (404):**
```json
{
  "erro": "Paciente não encontrado."
}
```

---

## 🎓 Cenário de Teste Completo

Siga este fluxo para testar todas as funcionalidades:

### Passo 1: Ver todos os leitos vazios
```
GET /api/leitos
```

### Passo 2: Internar 3 pacientes
```json
POST /api/leitos/ocupar
{
  "id_leito": 1,
  "id_paciente": 1
}
```
```json
POST /api/leitos/ocupar
{
  "id_leito": 2,
  "id_paciente": 2
}
```
```json
POST /api/leitos/ocupar
{
  "id_leito": 6,
  "id_paciente": 6
}
```

### Passo 3: Verificar lista atualizada
```
GET /api/leitos
```
(Agora você verá 3 leitos ocupados)

### Passo 4: Buscar onde está João Silva
```
GET /api/pacientes/123.456.789-00/leito
```

### Passo 5: Transferir João Silva
```json
POST /api/leitos/transferir
{
  "id_leito_atual": 1,
  "id_leito_destino": 3
}
```

### Passo 6: Confirmar transferência
```
GET /api/pacientes/123.456.789-00/leito
```
(Agora João estará no leito UTI-03)

### Passo 7: Liberar um leito
```json
POST /api/leitos/liberar
{
  "id_leito": 2
}
```

### Passo 8: Verificar resultado final
```
GET /api/leitos
```

---

## 💡 Dicas

- ✅ Use os **IDs** dos pacientes e leitos da tabela acima
- ✅ Teste os **casos de erro** tentando ocupar um leito já ocupado
- ✅ Teste transferir um paciente inexistente
- ✅ Use o botão **"Clear"** para limpar os campos
- ✅ A resposta mostra o **status HTTP** (200 = sucesso, 400 = erro de validação, 404 = não encontrado)
- ✅ Todos os endpoints podem ser testados **diretamente pela interface**, sem precisar de Postman ou cURL

---

## 🔄 Resetar o Banco de Dados

Se quiser voltar ao estado inicial (todos os leitos vazios):

```bash
php artisan migrate:fresh --seed
```

---

## 📱 Testando com cURL (Opcional)

Se preferir usar a linha de comando:

```bash
# Listar leitos
curl http://localhost:8000/api/leitos

# Ocupar leito
curl -X POST http://localhost:8000/api/leitos/ocupar \
  -H "Content-Type: application/json" \
  -d '{"id_leito": 1, "id_paciente": 1}'

# Buscar por CPF
curl http://localhost:8000/api/pacientes/123.456.789-00/leito
```

---

**🎉 Pronto! Agora você pode testar toda a API através da interface visual em http://localhost:8000/docs/api**
