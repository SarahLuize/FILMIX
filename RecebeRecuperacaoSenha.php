<?php

require_once 'db_funcoes.php';
require_once 'lib/EnviarEmailRecuperacao.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: RecuperarSenha.php');
    exit;
}

$emailRec = isset($_POST['CadastroEmailRec']) ? trim($_POST['CadastroEmailRec']) : '';

// Busco o usuário no banco
$usuario = buscarUsuarioPorEmail($emailRec);

if ($usuario) {
    // Só recupera se a conta estiver Ativa (Situacao 1) 
    if($usuario['situacao'] == 1){ 
        //Gera o Token e validade (30 minutos)
        $token = bin2hex(random_bytes(16));
       date_default_timezone_set('America/Sao_Paulo');
        $validade = date('Y-m-d H:i:s', strtotime('+24 HOUR'));

        // Salva no banco (Usando uma funcao que criaremos no db_funcoes)
        if(atualizarTokenRecuperacao($emailRec, $token, $validade)){
            //Envia o e-mai
            if(EnviarEmailRecuperacao($emailRec, $token, $usuario['nome'])){
                $_SESSION['sucesso_cadastro'] = "Link de recuperação enviado com sucesso! Verifique seu e-mail.";
            }else{
                $_SESSION['erro_cadastro'] = "Erro ao enviar o e-mail. Tente novamente mais tarde.";
            }
     }    
    }else{
        // Se o usuário existe mas a situação é 0
        $_SESSION['erro_cadastro'] = "Esta conta ainda não foi ativada. Verifique seu e-mail de cadastro original";
    }
}else{
    // Se o e-mail não existe 
    $_SESSION['sucesso_cadastro'] = "Se o e-mail informado esteve em nossa base, você receberá um link de recuperação.";
}

header('Location: RecuperarSenha.php');
exit;

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro.php');
    exit;
}

$emailRec = isset($_POST['CadastroEmailRec']) ? trim($_POST['CadastroEmailRec']) : '';
$usuario = buscarUsuarioPorEmail($emailRec);

if ($usuarioExistente) {
    $_SESSION['erro_cadastro'] = 'Este e-mail já está cadastrado no sistema. Por favor, use outro e-mail ou faça login.';
    header('Location: cadastro.php');
    exit;
}

$_SESSION['id_usuario'] = $usuario['id_usuario'];
$_SESSION['nome_usuario'] = $usuario['nome'];
$_SESSION['email_usuario'] = $usuario['email'];

unset($_SESSION['erro_recuperacao_senha']);

header('Location: login.php');
exit;