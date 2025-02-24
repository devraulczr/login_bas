Segue a documentação atualizada com os novos endpoints de cadastro e transferência:

---

# Site Login  
Um site extremamente simples de login, contendo painel de administrador, menu de transferência e outros recursos.

---

# 🔑 API de Recuperação de Senha  
API simples para recuperação de senha via código enviado por e-mail usando **Brevo**.  

## 🚀 Como Usar  

### 📩 1. Solicitar Código de Recuperação  
**Método:** `GET`  
**URL:**  
```
GET http://seuservidor/api/api.php?action=forgot_password&email=seu@email.com
```

📌 **Parâmetro:**  
- `email` → O e-mail cadastrado na base de dados.  

📨 **Resposta:**  
✅ **Sucesso:**  
```json
{"message": "Código gerado e enviado para seu@email.com"}
```
❌ **Erro:**  
```json
{"error": "Usuário não encontrado"}
```

---

### 🔄 2. Redefinir Senha  
**Método:** `GET`  
**URL:**  
```
GET http://seuservidor/api/api.php?action=reset_password&email=seu@email.com&code=ABC123&new_password=novasenha
```

📌 **Parâmetros:**  
- `email` → O e-mail cadastrado.  
- `code` → Código recebido no e-mail.  
- `new_password` → Nova senha desejada.  

🔓 **Resposta:**  
✅ **Sucesso:**  
```json
{"message": "Senha redefinida com sucesso"}
```
❌ **Erro:**  
```json
{"error": "Código inválido ou expirado"}
```

---

# 📝 Endpoints Adicionais

### 👤 3. Cadastro de Usuário  
Endpoint para cadastro de novos usuários.  

**Método:** `GET`  
**URL:**  
```
GET http://seuservidor/api/api.php?action=cadastrar_usuario&email=seu@email&
username=seuNome&password=suaSenha&csrf_token=token_valido
```

📌 **Parâmetros:**  
- `email` → E-mail do usuário (deve ser único).  
- `username` → Nome de usuário.  
- `password` → Senha desejada (será armazenada criptografada).  
- `csrf_token` → Token de segurança para evitar CSRF (deve ser validado na sessão).  

🔓 **Resposta:**  
✅ **Sucesso:**  
```json
{"message": "Conta criada com sucesso!"}
```
❌ **Erro:**  
```json
{"error": "Email já cadastrado no banco de dados"}
```
ou  
```json
{"error": "Token inválido" | "Token não encontrado"}
```

---

### 💸 4. Transferência de Saldo  
Endpoint para realizar transferência de saldo entre usuários.  
**Observação:** É necessário que o usuário esteja logado (informação armazenada em sessão).  

**Método:** `GET`  
**URL:**  
```
GET http://seuservidor/api/api.php?action=transferir&dest_id=ID_Destinatario&amount=valor
```

📌 **Parâmetros:**  
- `dest_id` → ID do usuário destinatário.  
- `amount` → Valor a ser transferido.  

🔓 **Requisitos e Validações:**  
- O usuário deve estar logado.  
- Não é permitido transferir para si mesmo.  
- O usuário deve possuir saldo suficiente para a transferência.  

🔓 **Resposta:**  
✅ **Sucesso:**  
```json
{"message": "Transferência realizada com sucesso"}
```
❌ **Erro:**  
```json
{"error": "Usuário não logado"}
```
ou  
```json
{"error": "Você não pode transferir para si mesmo"}
```
ou  
```json
{"error": "Saldo insuficiente"}
```
ou  
```json
{"error": "Destinatário não encontrado"}
```

---

## 🛠 Tecnologias Usadas  
✅ **PHP** (Backend)  
✅ **MySQL** (Banco de dados)  
✅ **Brevo** (Envio de e-mails)  

---

## 📂 Estrutura do Banco de Dados  

```sql
CREATE TABLE `usuarios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(30) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `saldo` DECIMAL(10, 2) DEFAULT 0.00,
  `admin` TINYINT(1) DEFAULT 0,
  `reset_code` VARCHAR(10) DEFAULT NULL,
  `code_expires_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

---

Feito com ❤️ por **Raul** 🚀
