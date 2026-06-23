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