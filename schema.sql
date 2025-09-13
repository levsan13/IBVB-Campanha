-- Tabela para armazenar doações
CREATE DATABASE doacoes;
USE doacoes;
CREATE TABLE donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    amount DECIMAL(10,2),
    phone VARCHAR(20),
    city VARCHAR(100),
    txid VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
