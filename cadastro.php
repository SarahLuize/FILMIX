<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FILMIX | Cadastro</title>
</head>
<body>
    <form action="RecebeCadastro.php" method="post">
        <h2>Nome</h2>
        <input type="text" name="CadastroNome" id="CadastroNome">
        <h2>Email</h2>
        <input type="text" name="CadastroEmail" id="CadastroEmail">
        <h2>Senha</h2>
        <input type="password" name="CadastroSenha" id="CadastroSenha">
        <h2>Confirmar senha</h2>
        <input type="password" name="CadastroSenha" id="CadastroSenha">
        <h2>Data de Nascimento</h2>
        <input type="date" name="CadastroData" id="CadastroData"><br><br>
        <input type="checkbox" name="ManterConectado" id="ManterConectado" value="Conectado">
        <label for="ManterConectado"> Manter-me conectado</label>
        <input type="submit" value="Cadastrar">

        <footer><p>"Este produto usa a API do TMDB, mas não é endossado ou certificado pelo TMDB."</p></footer>
    </form>
</body>
</html>