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

    <div class="cadastro-container">
        <div class="logo-placeholder logo-pequena">
            <a href="TelaPrincipal.php"><img src="img/FILMIX-logo.png" alt="FILMIX" class="logo-img" style="max-height: 130px; max-width: 200px;"></a>
        </div>

        <div class="banner-placeholder placeholder-box">
            <span style="color: #999;">Banner</span>
        </div>

        <form action="#">
            <div class="mb-3">
                <label for="EmailEsqS" class="form-label">Email:</label>
                <input type="text" name="EmailEsqS" id="EmailEsqS" class="form-control" placeholder="Digite seu email cadastrado">
            </div>
            <div class="mb-3">
                <input type="submit" class="btn btn-cadastrar" value="Avançar">
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