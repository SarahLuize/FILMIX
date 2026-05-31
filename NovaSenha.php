<?php 
require_once 'db_funcoes.php';
session_start();

$token = isset($_GET['token']) ? $_GET['token'] : '';


if(empty($token)){
    $_SESSION['erro_cadastro'] = "Token inválido ou ausente.";
    header('Location: RecuperarSenha.php');
    exit;
}
    
    $usuario = buscarUsuarioPorToken($token);

    if(!$usuario){
    $_SESSION ['erro_cadastro'] = "Este link de recuperação expirou ou é inválido.";
    header('Location: RecuperarSenha.php');
    exit;
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>FILMIX | Nova Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/cadastro.css">
</head>
<body>

    <div class="mt-5 margem-t">
        <div class="logo-placeholder logo-pequena">
            <a href="TelaPrincipal.php"><img src="img/FILMIX-logo.png" alt="FILMIX" class="logo-img img-fluid" style="max-height: 130px; max-width: 200px;"></a>
        </div>

        <div class="banner-placeholder placeholder-box pt-xxl-5">
            <div class="logos-img mt-xxl-5">
                <img src="img/recuperacao-senha.png" alt="RECUPERAÇÃO DE SENHA logo" class="logo-login mt-xxl-5">
            </div>
        </div>
    </div>

    <br><br>
    <div class="cadastro-container mt-5">
        <h4 class="text-center">Crie sua nova senha</h4>
        <br>
        <form action="AtualizarSenha.php" method="post">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

            <div class="mb-3">
                <label class="form-label">Nova Senha:</label>
                <input type="password" class="form-control" name="nova_senha" required placeholder="Digite a nova senha">
            </div>

            <div class="mb-3">
                <label class="form-label">Confirmar Nova Senha:</label>
                <input type="password" class="form-control" name="confirmar_senha" required placeholder="Repita a nova senha">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-cadastrar w-100">Atualizar Senha</button>
            </div>
        </form>
    </div>
    <br><br>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>