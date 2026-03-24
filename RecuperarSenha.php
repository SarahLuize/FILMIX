<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FILMIX | Esqueci minha senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/cadastro.css">
    
</head>
<body>
<?php
session_start();
$erroCadastro = isset($_SESSION['erro_cadastro']) ? $_SESSION['erro_cadastro'] : '';
$sucessoCadastro = isset($_SESSION['sucesso_cadastro']) ? $_SESSION['sucesso_cadastro'] : '';
unset($_SESSION['erro_cadastro']);
unset($_SESSION['sucesso_cadastro']);
?>
    <div class="mt-5 margem-t">
        <div class="logo-placeholder logo-pequena">
            <a href="TelaPrincipal.php"><img src="img/FILMIX-logo.png" alt="FILMIX" class="logo-img img-fluid" style="max-height: 130px; max-width: 200px;"></a>
        </div>

        <div class="banner-placeholder placeholder-box pt-xxl-5">
            <div class="logos-img mt-xxl-5">
                <img src="img/esqueci-senha-logo.png" alt="ESQUECI MINHA SENHA logo" class="logo-login mt-xxl-5">
            </div>
        </div>
    </div>

    <br><br><br><br>
    <h4 style="text-align: center;">Por favor, confirme o email fornecido no cadastro</h4>
    <br><br>
    <div class="cadastro-container">
        <?php if (!empty($erroCadastro)): ?>
            <div class="alert-erro">
                <?php echo htmlspecialchars($erroCadastro); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($sucessoCadastro)): ?>
            <div class="alert-sucesso">
                <?php echo htmlspecialchars($sucessoCadastro); ?>
            </div>
        <?php endif; ?>
        <form action="RecebeRecuperacaoSenha.php" method="post">
            <div class="mb-3">
                <label for="CadastroEmailRec" class="form-label">E-mail:</label>
                <input type="email" class="form-control" name="CadastroEmailRec" id="CadastroEmailRec" placeholder="e-mail" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-cadastrar">Enviar</button>
            </div>
        </form>
    </div>
    <br><br>

    <div class="footer mt-xxl-5">
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