# FILMIX

Sistema de recomendação de filmes baseado nos favoritos do usuário, criado para pessoas que não sabem o que assistir ou querem organizar sua lista de filmes para ver!

## Funcionalidades

- Cadastro e login
- Verificação em duas etapas (confirmação de e-mail para cadastro e redefinição de senha)
- Redefinição de senha
- Busca de filmes
- Detalhes dos filmes (sinopse, gênero e pôster)
- Favoritar filmes
- Adicionar filmes a lista de assistir mais tarde
- Recomendações personalizadas baseadas nos favoritos
- Navegação por gêneros para facilitar a busca por filmes específicos

## Como usar
(Adicionar)

## Tecnologias utilizadas
- PHP 8.0
- HTML
- CSS
- Bootstrap 5
- MySQL 8.0
- API TMDB
- PHPMailer 6.0
- Composer 2.9.5

## Requisitos
### Hardware (Mínimo)
- Processador: Dual-core 2.0 GHz ou superior
- Memória RAM: 4 GB
- Espaço em Disco: ____

### Software
- Servidor Web: Apache 2.4+
- PHP 8.0 ou superior
- MySQL 8.0 CE
- Composer 2.9.5
- Conexão com Internet (necessária para API do TMDB e envio de e-mails)
- 
## Instalação
1.  Baixe e instale o XAMPP 3.3.0
2. Configure o `php.ini`:
   * Habilite `extension=openssl` e `extension=mbstring`
   * Localize a segunda ocorrência de `date.timezone` e altere para `America/Sao_Paulo`
   * Salve o arquivo e clique em Start no Apache e MySQL
3. Clone o repositório ou baixe a pasta do projeto
4. Copie a pasta `FILMIX` para `C:\xampp\htdocs\filmix`
5. Importe o arquivo `banco.sql`:
   * Via phpMyAdmin: acesse `localhost/phpmyadmin` e importe o arquivo  
   * Via MySQL Workbench: crie uma nova conexão e execute o `banco.sql`  
6. Acesse `localhost/FILMIX` no navegador

## Autores
(Adicionar)

## Contribuições
(Adicionar)
