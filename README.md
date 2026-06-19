# FILMIX

Sistema de recomendação de filmes baseado nos favoritos do usuário, criado para pessoas que não sabem o que assistir ou querem organizar sua lista de filmes para ver!

## Funcionalidades

- Cadastro e login
- Verificação em duas etapas (confirmação de e-mail para cadastro e redefinição de senha)
- Redefinição de senha
- Alterar e-mail
- Excluir conta
- Busca de filmes
- Detalhes dos filmes (sinopse, classificação indicativa, gênero e pôster)
- Favoritar filmes
- Adicionar filmes a lista de assistir mais tarde
- Recomendações personalizadas baseadas nos favoritos
- Navegação por gêneros para facilitar a busca por filmes específicos
- Restrição por idade para favoritos e lista de assistir mais tarde

## Como usar
1. Criar uma conta e confirmar o e-mail para concluir o cadastro
2. Fazer login
3. Pesquisar filmes pela barra de busca ou navegar por gêneros
4. Clicar em um filme para ver detalhes (sinopse, classificação, pôster)
5. Favoritar filmes ou adicioná-los à lista de assistir mais tarde
6. Voltar à página principal para ver as recomendações baseadas nos seus favoritos

## Tecnologias utilizadas
- PHP 8.0
- HTML
- CSS
- Bootstrap 5
- MySQL 8.0
- API TMDB v3
- Composer (gerenciamento de dependências)
- PHPMailer (via Composer)

## Requisitos

### Software
- Apache 2.4+
- PHP 8.0+
- MySQL 8.0 CE
- Conexão com Internet (API TMDB e envio de e-mails)
- Composer (gerenciamento de dependências)
  
## Instalação
1.  Baixe e instale o XAMPP 3.3.0
2. Configure o `php.ini`:
   * Habilite `extension=openssl` e `extension=mbstring`
   * Localize a segunda ocorrência de `date.timezone` e altere para `America/Sao_Paulo`
   * Salve o arquivo e inicie o servidor Apache e o MySQL
3. Clone o repositório ou baixe a pasta do projeto
4. Copie a pasta `FILMIX` para `C:\xampp\htdocs\filmix`
5. Importe o arquivo `banco.sql`:
   * Via phpMyAdmin: acesse `localhost/phpmyadmin` e importe o arquivo `banco.sql` 
   * Via MySQL Workbench: crie uma nova conexão e execute o `banco.sql`
6. Antes de executar o projeto, instale o [Composer](https://getcomposer.org/download).
7. Dentro da pasta do projeto, execute:
   ```bash
   composer install
Isso irá instalar todas as dependências do projeto, incluindo o PHPMailer.
8. Acesse no navegador: `http://localhost/FILMIX`

## Autores
- [@Sarah L. M.](https://www.github.com/SarahLuize)
- [@Joel M. S.](https://github.com/jmsifsc)

## Contribuições
(Adicionar)
