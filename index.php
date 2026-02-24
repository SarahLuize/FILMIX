<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FILMIX | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/index.css">

</head>
<body>
<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header("Location: TelaPrincipal.php");
    exit();
}

$erroLogin = isset($_SESSION['erro_login']) ? $_SESSION['erro_login'] : '';
$sucessoCadastro = isset($_SESSION['sucesso_cadastro']) ? $_SESSION['sucesso_cadastro'] : '';
unset($_SESSION['erro_login']);
unset($_SESSION['sucesso_cadastro']);
?>
    <div class="logo-placeholder logo-grande">
        <span style="color: #999;">Logo</span>
    </div>

    <div class="banner-placeholder">
        <span style="color: #999;">Banner</span>
    </div>

    <div class="login-container">
        <?php if (!empty($erroLogin)): ?>
            <div class="alert-erro">
                <?php echo htmlspecialchars($erroLogin); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($sucessoCadastro)): ?>
            <div class="alert-sucesso">
                <?php echo htmlspecialchars($sucessoCadastro); ?>
            </div>
        <?php endif; ?>
        <form action="RecebeLogin.php" method="post">
            <div class="mb-3">
                <label for="LoginEmail" class="form-label">E-mail:</label>
                <input type="email" class="form-control" name="LoginEmail" id="LoginEmail" placeholder="Insira seu e-mail" required>
            </div>

            <div class="mb-3">
                <label for="LoginSenha" class="form-label">Senha:</label>
                <input type="password" class="form-control" name="LoginSenha" id="LoginSenha" placeholder="Insira sua senha" required>
            </div>

            <button type="submit" class="btn btn-entrar">Entrar</button>
        </form>

        <div class="criar-conta-link">
            <a href="#">Esqueci minha senha</a>
            <p class="mb-0">Ainda não tem conta ? <a href="cadastro.php">Criar Conta</a></p>
        </div>
    </div>

    <div class="footer">
        <div class="TMDB-logo">
            <img src="img/TMDBlogo.svg" style="display: flex; align-items: center; justify-content: center; height: 45%;" alt="">
        </div> 
        <div class="footer-disclaimer">
            <p class="mb-0">Este produto usa a API do TMDB, mas não é endossado ou certificado pelo TMDB.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>