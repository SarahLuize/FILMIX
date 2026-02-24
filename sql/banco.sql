-- BANCO DE DADOS --
CREATE DATABASE sistema;
USE sistema;

-- Tabela de usuários
CREATE TABLE usuario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(250) NOT NULL,
    email VARCHAR(250) NOT NULL UNIQUE,
    senha VARCHAR(250) NOT NULL,
    data_nascimento DATE NOT NULL
);

-- Tabela de filmes (dados vindos da API TMDB)
CREATE TABLE filme (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_tmdb INT NOT NULL,
    cartaz VARCHAR(250),
    titulo VARCHAR(250),
    ano YEAR,
    descricao TEXT,
    genero VARCHAR(250),
    tempo VARCHAR(250),
    popularidade INT,
    pais_origem VARCHAR(250),
    idioma VARCHAR(250),
    diretor VARCHAR(250),
    atores VARCHAR(250),
    nome_estudio VARCHAR(250),
    adulto BOOLEAN
);

-- Tabela de favoritos (relação N:N entre usuário e filme)
CREATE TABLE favorito (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fkfilme INT NOT NULL,
    fkusuario INT NOT NULL,
    data_favorito DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fkfilme) REFERENCES filme(id),
    FOREIGN KEY (fkusuario) REFERENCES usuario(id),
    UNIQUE (fkfilme, fkusuario) -- evita duplicar o mesmo filme nos favoritos
);

-- Tabela de "assistir mais tarde" (relação N:N entre usuário e filme)
CREATE TABLE assistirmaistarde (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fkfilme INT NOT NULL,
    fkusuario INT NOT NULL,
    data_adicao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fkfilme) REFERENCES filme(id),
    FOREIGN KEY (fkusuario) REFERENCES usuario(id),
    UNIQUE (fkfilme, fkusuario) -- evita duplicar o mesmo filme na lista
);