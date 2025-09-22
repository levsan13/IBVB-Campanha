# Site de campanha de doação (mobile)


Arquivos incluídos:
- index.html (página inicial / progresso / justificativa)
- donate.html (formulário de doação e etapa de confirmação)
- assets/styles.css (estilos responsivos focados em celular)
- assets/app.js (JS front-end: fetch de total, validação, geração de QR via Chart API)
- server/db.php (configuração de conexão MySQL)
- server/process_donation.php (recebe doação e grava no banco)
- server/total.php (retorna total arrecadado em JSON)


**Instruções de implantação**
1. Coloque os arquivos `index.html`, `donate.html` e a pasta `assets/` no root do seu servidor web.
2. Coloque `server/` em um servidor com PHP 7.x+ e MySQL disponível.
3. Edite `server/db.php` com host, usuário, senha e nome do banco.
4. Importe a tabela SQL (arquivo abaixo) no seu banco.
