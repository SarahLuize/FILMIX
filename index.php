<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FILMIX | Login</title>
</head>
<body>
    <form action="RecebeLogin.php" method="post">
        <h2>Email</h2>
        <input type="text" name="LoginEmail" id="LoginEmail">
        <h2>Senha</h2>
        <input type="password" name="LoginSenha" id="LoginSenha">
        <input type="submit" value="Entrar"> <br><br>

        <a href="#">Esqueci minha senha</a>
        <p>Ainda não tem uma conta? <a href="cadastro.php">Criar conta</a></p>

        <footer><p>"Este produto usa a API do TMDB, mas não é endossado ou certificado pelo TMDB."</p></footer>
    </form>
</body>
</html>