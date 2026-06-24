-- Criar o banco de dados
CREATE DATABASE filmix;
USE filmix;

-- Usuário
CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    data_nascimento DATE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
	token VARCHAR(220) NOT NULL,
	validade VARCHAR(220) NOT NULL,
	situacao TINYINT(1) NOT NULL DEFAULT 0		
);

CREATE TABLE IF NOT EXISTS usuario_assistir_mais_tarde (
    id_usuario INT NOT NULL,
    id_tmdb INT NOT NULL,
    data_adicionado DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario, id_tmdb),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS usuario_favorito (
    id_usuario INT NOT NULL,
    id_tmdb INT NOT NULL,
    data_adicionado DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario, id_tmdb),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);

INSERT INTO `usuario` VALUES
-- CLASSIFICAÇÃO 6
(1,'Igor2024','igor2024@gmail.com','2020-02-03','$2y$10$l/Pr70TsNZCRoy779B8AHO5K901APq48ntcjo3TjM.3kHD5cxePSO','2026-06-23 13:25:46','','',1),
-- CLASSIFICAÇÃO 10
(2,'Leticia10','leticia10@gmail.com','2016-05-10','$2y$10$o/1gcD1nT6iU8nzwaCbjNeiL/8n6WuIwMrVEoPlhmRHgou6qVVoh2','2026-06-23 13:32:53','','',1),
-- CLASSIFICAÇÃO 12
(3,'Diego16','diego@gmail.com','2014-05-14','$2y$10$zTeqmb67RM1AkVdUod//2OX.mBxZJqFm4xkpqnIV7aCphHnPa29U2','2026-06-23 13:34:04','','',1),
-- CLASSIFICAÇÃO 14
(4,'Luana2012','luana@gmail.com','2012-05-24','$2y$10$SixXdq9gpBn4Vjdpu3K67exetY575CRsswMKCNMQqXJHa3W6TZh/2','2026-06-23 13:34:49','','',1),
-- CLASSIFICAÇÃO 16
(5,'Eric023','eric@gmail.com','2010-04-15','$2y$10$WumUz4DikaetsQ0eBwuAr.oNn4lW.XxfaAq8hl0NX.8EBW8srycYG','2026-06-23 13:35:31','','',1),
-- CLASSIFICAÇÃO 18
(6,'Izabel2008','izabel08@gmail.com','2008-01-30','$2y$10$s5ALwkVlRAVsj9qEyUuVueSHMST20nKg.CS6Zg8WeE.pnyI6cC.9a','2026-06-23 13:36:02','','',1);

INSERT INTO `usuario_favorito` VALUES
-- Usuário: Igor
(1,260513,'2026-06-23 13:38:42'),(1,354912,'2026-06-23 13:38:42'),(1,502356,'2026-06-23 13:38:42'),(1,675445,'2026-06-23 13:43:12'),
-- Usuário: Letícia
(2,118,'2026-06-23 13:59:45'),(2,671,'2026-06-23 13:46:01'),(2,12155,'2026-06-23 14:01:20'),(2,44874,'2026-06-23 13:49:29'),(2,1184918,'2026-06-23 13:46:01'),
-- Usuário: Diego
(3,11,'2026-06-23 14:02:27'),(3,122,'2026-06-23 14:02:27'),(3,299534,'2026-06-23 14:02:27'),(3,353486,'2026-06-24 10:22:45'),
-- Usuário: Luana
(4,2268,'2026-06-24 10:48:25'),(4,9615,'2026-06-24 10:55:33'),(4,19995,'2026-06-23 14:06:17'),(4,297762,'2026-06-23 14:15:45'),(4,624860,'2026-06-23 14:13:24'),
-- Usuário: Eric
(5,155,'2026-06-23 14:06:49'),(5,550,'2026-06-23 14:06:49'),(5,598,'2026-06-24 09:33:59'),(5,11324,'2026-06-23 14:06:49'),(5,27205,'2026-06-23 14:06:49'),(5,68718,'2026-06-24 09:30:23'),(5,157336,'2026-06-23 14:06:49'),
-- Usuário: Izabel
(6,13,'2026-06-23 14:07:37'),(6,769,'2026-06-23 14:07:37'),(6,466272,'2026-06-23 14:07:37'),(6,533535,'2026-06-23 14:07:37');

INSERT INTO `usuario_assistir_mais_tarde` VALUES 
-- Usuário: Igor
(1,808,'2026-06-23 13:44:08'),(1,127380,'2026-06-23 13:43:44'),(1,227973,'2026-06-24 10:40:14'),
-- Usuário: Letícia
(2,672,'2026-06-23 13:46:18'),(2,49013,'2026-06-23 13:46:18'),(2,569094,'2026-06-23 13:46:18'),(2,803796,'2026-06-24 10:28:52'),(2,1022789,'2026-06-23 13:46:18'),
-- Usuário: Diego
(3,181808,'2026-06-23 14:02:46'),(3,209112,'2026-06-23 14:05:11'),(3,667538,'2026-06-23 14:05:47'),
-- Usuário: Luana
(4,120,'2026-06-23 14:06:33'),(4,155,'2026-06-24 10:50:32'),(4,2454,'2026-06-23 14:06:33'),(4,24428,'2026-06-24 10:56:54'),(4,634649,'2026-06-24 10:56:23'),
-- Usuário: Eric
(5,597,'2026-06-23 14:07:07'),(5,603,'2026-06-23 14:07:07'),(5,383498,'2026-06-24 09:37:09'),(5,475557,'2026-06-23 14:07:07'),
-- Usuário: Izabel
(6,550,'2026-06-23 14:07:21'),(6,299536,'2026-06-23 14:07:21'),(6,872585,'2026-06-23 14:07:21');
