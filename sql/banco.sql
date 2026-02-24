-- Criar o banco de dados
CREATE DATABASE filmix;
USE filmix;

-- Usuário
CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Filme (dados principais vindos da API TMDB)
CREATE TABLE filme (
    id_filme INT AUTO_INCREMENT PRIMARY KEY,
    id_tmdb INT NOT NULL, -- ID do TMDB (único por filme)
    titulo VARCHAR(200) NOT NULL,
    idioma_original VARCHAR(10),
    popularidade DECIMAL(6,2),
    media_avaliacao DECIMAL(3,1),
    poster_url VARCHAR(255),
    data_lancamento DATE,
    sinopse TEXT,
    UNIQUE (id_tmdb) -- Evita duplicar filmes do TMDB
);

-- Recomendação feita por um usuário
CREATE TABLE recomendacao (
    id_recomendacao INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    titulo VARCHAR(150), -- Ex: "Melhores de ação 2024"
    descricao TEXT,
    data_recomendacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

-- Relação N:N entre filme e recomendação
CREATE TABLE filme_recomendacao (
    id_filme INT NOT NULL,
    id_recomendacao INT NOT NULL,
    PRIMARY KEY (id_filme, id_recomendacao),
    FOREIGN KEY (id_filme) REFERENCES filme(id_filme) ON DELETE CASCADE,
    FOREIGN KEY (id_recomendacao) REFERENCES recomendacao(id_recomendacao) ON DELETE CASCADE
);