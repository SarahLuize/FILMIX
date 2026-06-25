<?php
session_start();

require_once 'db_funcoes.php';
require_once 'lib/EnviarEmailRecuperacao.php';


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


        if(atualizarTokenRecuperacao($emailRec, $token, $validade)){
            //Envia o e-mai
            if(EnviarEmailRecuperacaoSenha($emailRec, $token, $usuario['nome'])){
                $_SESSION['sucesso_cadastro'] = "Link de recuperação enviado com sucesso! Verifique seu e-mail.";
            }else{
                $_SESSION['erro_cadastro'] = "Não foi possível conectar ao servidor para enviar o e-mail. Verifique sua conexão e tente novamente.";
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
