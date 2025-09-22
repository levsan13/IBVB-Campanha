# 💒 Site de Doações - Igreja

Este projeto é um **site responsivo para campanhas de doação via Pix**, totalmente integrado com o **Mercado Pago**.  
O sistema exibe o valor total arrecadado em tempo real e gera QR Code Pix para doação, além de confirmar pagamentos automaticamente.

---

## 📂 Estrutura do Projeto

'''
├── index.html # Página inicial com descrição da campanha e total arrecadado
├── donate.html # Página de doação (gera QRCode Pix e Pix Copia & Cola)
├── assets/
│ ├── styles.css # Estilos do site (responsivo e com fundo personalizado)
├── server/
│ ├── db.php # Conexão com MySQL
│ ├── create_payment.php # Cria cobrança Pix via API Mercado Pago
│ ├── check_payment.php # Confirma se o pagamento foi aprovado
│ ├── total.php # Soma todas as doações aprovadas
└── schema.sql # Script do banco de dados MySQL
'''
---

## 🛠️ Tecnologias Utilizadas

- **Frontend:** HTML5, CSS3, JavaScript (fetch API para chamadas assíncronas)
- **Backend:** PHP 8
- **Banco de Dados:** MySQL (MariaDB)
- **Hospedagem:** AWS EC2 (Apache2 + PHP + MySQL)
- **Integração de Pagamento:** Mercado Pago (Pix)

---

## 📊 Fluxo de Funcionamento

1. O usuário acessa a **página inicial (index.html)**:
   - Visualiza o texto explicativo da campanha.
   - Vê o **valor total já arrecadado** (atualizado a cada 5 segundos via `total.php`).
   - Clica em **"Doe para a campanha"** para ser redirecionado para `donate.html`.

2. Na **página de doação (donate.html)**:
   - O usuário preenche **nome, valor da doação, telefone e cidade**.
   - O sistema chama `server/create_payment.php`:
     - Cria uma **cobrança Pix** usando a API do Mercado Pago.
     - Retorna o **QRCode Pix** e o **Pix Copia & Cola**.
   - O usuário pode pagar via QR Code ou Pix Copia & Cola.

3. **Confirmação do pagamento**:
   - É feito um fecth com server/check_payment.php que se comunica com a API do mercado pago
   - O server/check_payment.php atualiza automaticamente o **status da doação no banco de dados** (aprovado, pendente).
   - O usuário vê em `donate.html` a confirmação do pagamento.
   - Após alguns segundos, o site redireciona automaticamente para `index.html`.

4. **Cálculo do total arrecadado**:
   - `server/total.php` soma apenas os pagamentos **com status "approved"** no banco de dados.
   - O valor aparece no topo da página inicial.

---

## 🔑 **Integração com Mercado Pago**

O projeto usa a API Pix do Mercado Pago.
1. **Obtenha as credenciais**
   - Acesse o Painel do Mercado Pago Developers (https://www.mercadopago.com.br/developers/panel/app)
   - Copie o Access Token (modo *TEST* ou *PROD*).
2. **Configure no servidor**
   - No arquivo server/create_payment.php e server/check_payment.php adicione seu token:

$mp_token = "SEU_TOKEN_MERCADO_PAGO";


## 🗄️ Banco de Dados

Script `schema.sql`:

```sql
CREATE TABLE donations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  city VARCHAR(100) NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  txid VARCHAR(100) UNIQUE NOT NULL,
  mp_payment_id VARCHAR(100) NOT NULL,
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

