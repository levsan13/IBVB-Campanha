# 📌 Campanha de Doação - Igreja

Este é um site desenvolvido para gerenciar uma campanha de doações via **Pix** utilizando a **API do Mercado Pago**. O sistema é otimizado para **uso em celulares**, possui integração com banco de dados MySQL e atualiza automaticamente o valor arrecadado conforme os pagamentos são confirmados.

---

## 🚀 Funcionalidades

- **Página inicial (`index.html`)**
  - Exibe uma barra superior com o valor total arrecadado.
  - Mostra uma justificativa/explicação da campanha.
  - Botão **"Doe para a campanha"** que leva o usuário para a página de doação.

- **Página de doação (`donate.html`)**
  - Usuário informa:
    - Nome
    - Cidade
    - Telefone
    - Valor da doação
  - O sistema gera automaticamente:
    - **QR Code Pix**
    - **Pix Copia e Cola**
  - Confirmação automática do pagamento.
  - Após confirmado, exibe mensagem de sucesso e redireciona o usuário para a página inicial.

- **Integração com Mercado Pago**
  - Geração de cobrança Pix via API.
  - Uso de **check_payment (`server/check_payment.php`)** para confirmar pagamentos em tempo real.
  - Verificação do status do pagamento no banco de dados.

- **Banco de Dados**
  - Todas as doações são registradas em uma tabela MySQL.
  - Apenas doações **aprovadas** são somadas ao total exibido.

---

## 📂 Estrutura de Pastas

```
.
├── index.html               # Página inicial da campanha
├── donate.html              # Página de doação
├── assets/                  # Recursos estáticos (CSS, imagens, etc.)
│   └── styles.css           # Estilos do site
├── server/                  # Arquivos PHP de backend
│   ├── db.php               # Configuração do banco e credenciais Mercado Pago
│   ├── create_payment.php   # Criação de cobranças Pix via Mercado Pago
│   ├── check_payment.php    # Consulta status de pagamento e atualiza DB
│   └── total.php            # Retorna total de doações aprovadas
└── README.md                # Documentação do projeto
```

---

## 🛠️ Configuração do Banco de Dados

1. Crie o banco e a tabela:

```sql
CREATE DATABASE donations_db;
USE donations_db;

CREATE TABLE donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    value DECIMAL(10,2) NOT NULL,
    txid VARCHAR(100) NOT NULL,
    mp_payment_id VARCHAR(50) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```
2. No arquivo `server/db.php`, configure:

```php
<?php
// Configuração da base de dados. Edite com seus valores.
$DB_HOST = 'HOST_DATA_BASE';
$DB_USER = 'USUARIO';
$DB_PASS = 'SENHA';
$DB_NAME = 'NOME_DO_BANCO';


$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if($mysqli->connect_errno){
http_response_code(500);
die(json_encode(['success'=>false,'error'=>'DB_CONNECT','msg'=>$mysqli->connect_error]));
}
$mysqli->set_charset('utf8mb4');
?>
```

---

## 🔑 Integração com Mercado Pago

1. Crie uma conta no [Mercado Pago Developers](https://www.mercadopago.com.br/developers).  
2. Obtenha suas **credenciais** em: [Credenciais Mercado Pago](https://www.mercadopago.com.br/developers/panel/credentials)  
   - Use o `ACCESS_TOKEN` **de produção** (não use o de teste em ambiente real).
3. Em `server/check_payment.php` e `server/create_mp_payment.php`, altere:
   ```php
   $mp_token = "SEU_TOKEN_MERCADO_PAGO";
   ``` 

---

## 🌐 Fluxo do Sistema

1. Usuário acessa `index.html` e vê o valor total arrecadado.  
2. Ao clicar em **"Doe para a campanha"**, vai para `donate.html`.  
3. Preenche os dados e envia → `server/create_payment.php` cria cobrança no Mercado Pago.  
4. É exibido o **QR Code** e o **Pix Copia e Cola**.  
5. Quando o usuário paga:
   - O **Mercado Pago envia notificação** para `server/webhook_mp.php`.  
   - O webhook confirma o pagamento e atualiza o status no MySQL.  
6. O total exibido em `index.html` é atualizado automaticamente (via `server/total.php`).  
7. Após a confirmação, `donate.html` mostra mensagem e redireciona para a página inicial.

---

## 🎨 Personalização
  
- **Textos da campanha** podem ser editados diretamente em `index.html`.  
- **Banco e titular Pix** são configurados no Mercado Pago.

---

## 📲 Deploy na AWS (EC2)

1. Crie uma instância EC2 (Amazon Linux ou Ubuntu).  
2. Instale Apache, PHP e MySQL:
   ```bash
   sudo yum install -y httpd php php-mysqli mariadb105-server
   ```
3. Configure o banco de dados.  
4. Suba os arquivos do projeto para `/var/www/html/`.  
5. Configure permissões:
   ```bash
   sudo chown -R apache:apache /var/www/html
   sudo systemctl enable httpd
   sudo systemctl start httpd
   ```

---

## ✅ Conclusão

Este site permite gerenciar uma campanha de doações **simples, segura e automatizada**, com Pix via Mercado Pago, banco de dados MySQL e hospedagem em AWS EC2.
