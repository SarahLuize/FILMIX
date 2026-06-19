<?php
session_start();

// Se já estiver logado, vai para a tela principal
if (isset($_SESSION['id_usuario'])) {
    header('Location: TelaPrincipal.php');
    exit();
}

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';
$erroLogin = isset($_SESSION['erro_login']) ? $_SESSION['erro_login'] : '';

$sucessoCadastro = '';
if (isset($_SESSION['sucesso_cadastro'])) {
    $sucessoCadastro = $_SESSION['sucesso_cadastro'];
} elseif (isset($_SESSION['sucesso_login'])) {
    $sucessoCadastro = $_SESSION['sucesso_login'];
}
// limpar todas as variaveis da sessão para não repetirem no F5
unset($_SESSION['erro_login'], $_SESSION['sucesso_cadastro'], $_SESSION['erro_login']);
?>
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
    <div class="mt-5 margem-t">
        <div class="logo-placeholder logo-pequena">
            <a href="TelaPrincipal.php" title="Ir para tela principal"><img src="img/FILMIX-logo.png" alt="FILMIX" class="logo-img" style="max-height: 130px; max-width: 200px;"></a>
        </div>

        <div class="banner-container-logos">
            <div class="logos-img">
                <img src="img/logo-login.png" alt="Logotipo LOGIN" class="logo-login">
            </div>
        </div>
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
        
        <!--$_COOKIE-->      
      
        <form action="RecebeLogin.php" method="post">

            <?php if (!empty($redirect)): ?>
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8'); ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="LoginEmail" class="form-label">E-mail:</label>
                <input type="email" class="form-control" name="LoginEmail" id="LoginEmail" placeholder="Insira seu e-mail" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Senha</label>
                <div class="input-group">
                    <input type="password" name="LoginSenha" id="campo-senha" placeholder="Insira seu Senha" class="form-control" required>
                    
                    <button class="btn btn-outline-secondary" type="button" 
                            style="background-color: transparent !important; border-color: #ced4da !important; border-left: none !important; box-shadow: none !important;" 
                            onclick="toggleSenha('campo-senha', 'olho-aberto', 'olho-fechado')">
                        
                        <svg id="olho-aberto" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" 
                            style="color: #495057 !important; display: block;" class="bi bi-eye-fill" viewBox="0 0 16 16">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                        </svg>
                        
                        <svg id="olho-fechado" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" 
                            style="color: #495057 !important;" class="bi bi-eye-slash-fill d-none" viewBox="0 0 16 16">
                            <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474L2.234 4.317C1.159 5.63 0 8 0 8s3 5.5 8 5.5a9.917 9.917 0 0 0 2.79-1.588M5.21 3.089c1.559-.616 3.363-.465 4.79.37l-.583.582.002.004a4.5 4.5 0 0 0-3.344.166l-.1-.1-.195-.195-.162-.162z"/>
                            <path d="M12.234 9.124a15.42 15.42 0 0 0 2.373-2.124s-3-5.5-8-5.5a11.94 11.94 0 0 0-2.583.284l1.6 1.6a3.5 3.5 0 0 1 4.79 4.79z"/>
                            <path d="M0 0h16v16H0zm0 0h16v16H0zm0 0h16v16H0zm0 0h16v16H0z" fill="none"/>
                            <path d="M1 1l14 14" stroke="#495057" stroke-width="2"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-entrar">Entrar</button>
        </form>

        <div class="criar-conta-link">
            <a href="AlterarEmail.php">Alterar meu e-mail</a><br>
            <a href="RecuperarSenha.php">Esqueci minha senha</a>
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
    <script src="js/funcoes.js"></script>
</body>

</html>