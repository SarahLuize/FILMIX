<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FILMIX | Cadastro</title>
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
    <div class="logo-placeholder logo-grande placeholder-box">
        <span style="color: #999;">Logo</span>
    </div>

    <div class="banner-placeholder placeholder-box">
        <span style="color: #999;">Banner</span>
    </div>

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
        <form action="RecebeCadastro.php" method="post">
            <div class="mb-3">
                <label for="CadastroNome" class="form-label">Nome:</label>
                <input type="text" class="form-control" name="CadastroNome" id="CadastroNome" placeholder="nome" required>
            </div>

            <div class="mb-3">
                <label for="CadastroEmail" class="form-label">E-mail:</label>
                <input type="email" class="form-control" name="CadastroEmail" id="CadastroEmail" placeholder="e-mail" required>
            </div>

            <div class="mb-3">
                <label for="CadastroSenha" class="form-label">Senha:</label>
                <input type="password" class="form-control" name="CadastroSenha" id="CadastroSenha" placeholder="senha" required>
            </div>

            <div class="mb-3">
                <label for="CadastroSenhaConfirmar" class="form-label">Confirmar Senha:</label>
                <input type="password" class="form-control" name="CadastroSenhaConfirmar" id="CadastroSenhaConfirmar" placeholder="Confirme a senha" required>
            </div>

            <div class="mb-3">
                <label for="CadastroData" class="form-label">Data de Nascimento:</label>
                <div class="date-input-wrapper">
                    <input type="date" class="form-control" name="CadastroData" id="CadastroData" required>
                </div>
            </div>

            <div class="form-actions">
                <div class="checkbox-wrapper">
                    <input type="checkbox" name="ManterConectado" id="ManterConectado" value="Conectado" checked>
                    <label for="ManterConectado" class="mb-0">Manter-me conectado</label>
                </div>
                <button type="submit" class="btn btn-cadastrar">Cadastrar</button>
            </div>
        </form>
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