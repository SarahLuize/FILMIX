<?php
session_start();
require_once 'db_funcoes.php';

$mensagem = "";
$passo = 1; 

// Bloco Primeiro
if (isset($_POST['btn_validar'])) {
    $username = htmlspecialchars(trim($_POST['usuario']));
    $dataNascInput = $_POST['data_nascimento']; 
    
       $dataFormatada = $dataNascInput;
   
    $usuarioValido = verificarDadosRecuperacao($username, $dataFormatada);

    if ($usuarioValido) {
        $_SESSION['id_recuperacao'] = $usuarioValido['id_usuario']; 
        $_SESSION['nome_recuperado'] = $usuarioValido['nome'];
        $passo = 2; 
    } else {
        $mensagem = "Usuário ou Data de Nascimento não conferem.";
    }
}

// PASSO 2: Atualiza o E-mail

// Bloco Segundo
if (isset($_POST['btn_atualizar_email'])) {
    
    $novoEmail = filter_var($_POST['novo_email'], FILTER_VALIDATE_EMAIL);
    $idUsuario = $_SESSION['id_recuperacao'];
    $nomeUsuario = $_SESSION['nome_recuperado'];

    if ($novoEmail && $idUsuario) {

        $emailExistente = buscarUsuarioPorEmail($novoEmail);

        if($emailExistente){

        $mensagem = "Este e-mail já está em cadastrado em outra conta.";
        $passo = 2;

        }else{

            // Atualiza o E-mail bi banco e bato situacao = 0
            if (atualizarEmailUsuario($idUsuario, $novoEmail)) {
    
                enviarEmailRecuperacao($novoEmail, $idUsuario, $nomeUsuario);

                $_SESSION['sucesso_login'] = 'Link de ativação enviado! Acesse a caixa de entrada.';
                
                unset($_SESSION['id_recuperacao']);
                unset($_SESSION['nome_recuperado']);    
                
                    
                header('Location: login.php');
                exit;
            }else{
                $mensagem = "Erro interno ao atualizar o e-mail. Tente novamente";
                $passo = 2;

            }
        }

    } else {
        $mensagem = "E-mail inválido.";
        $passo = 2;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FILMIX | Recuperar Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/index.css">
</head>

<body >
   <div class="mt-5 margem-t">
        <div class="logo-placeholder logo-pequena">
            <a href="TelaPrincipal.php" title="Voltar para o login"><img src="img/FILMIX-logo.png" alt="FILMIX" class="logo-img" style="max-height: 130px; max-width: 200px;"></a>
        </div>

        <div class="banner-container-logos">
            <div class="logos-img">
                <img src="img/alterar-email-logo.png" alt="Logotipo LOGIN" class="logo-login">
            </div>
        </div>
    </div>

    <div class="login-container">
            
            <?php if(!empty($mensagem)): ?>
                <div class="alert alert-danger"><?php echo $mensagem; ?></div>
            <?php endif; ?>

            <?php if ($passo == 1): ?>
                <?php if(!empty($mensagem)) echo $mensagem?>
                <form method="POST">                    
                    <div class="mb-3">
                        <br>
                        <label class="form-label">Nome de Usuário</label>
                        <input type="text" name="usuario" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data de Nascimento</label>
                        <input type="date" name="data_nascimento" class="form-control" required>
                    </div>
                    <button type="submit" name="btn_validar" class="btn btn-entrar">Verificar Dados</button>
                </form>
            <?php echo "\n"; else: ?>
                <form method="POST">
                    <p class="text-warning">Dados confirmados! Insira seu novo e-mail abaixo:</p>
                    <div class="mb-3">
                        <label>Novo E-mail</label>
                        <input type="email" name="novo_email" class="form-control" required>
                    </div>
                    <button type="submit" name="btn_atualizar_email" class="btn btn-success w-100">Atualizar e Enviar Ativação</button>
                </form>
            <?php endif; ?>
            
            <div class="text-center mt-3 criar-conta-link">
                <a href="login.php">Voltar para o Login</a>
            </div>
        </div>        
    </div>
    <br>
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