-- Tabela para armazenar doações (agora com nome e txid)
CREATE TABLE IF NOT EXISTS `donations` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`name` VARCHAR(200) NOT NULL,
`amount` DECIMAL(10,2) NOT NULL,
`phone` VARCHAR(30) DEFAULT NULL,
`city` VARCHAR(100) DEFAULT NULL,
`txid` VARCHAR(150) DEFAULT NULL,
`mp_payment_id` VARCHAR(100) DEFAULT NULL,
`status` VARCHAR(50) DEFAULT 'pending',
`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
INDEX (`txid`),
INDEX (`mp_payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
