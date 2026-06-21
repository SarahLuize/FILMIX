<?php
session_start();
require_once 'db_funcoes.php';
$idUsuario = $_SESSION['id_recuperacao'] ?? null;
$nomeUsuario = $_SESSION['nome_recuperado'] ?? null;

if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['novo_email'])){
    header('Location: AlterarEmail.php');
    exit();
}

$novoEmail = filter_input(INPUT_POST, 'novoEmail', FILTER_VALIDATE_EMAIL);

IF(!$novoEmail){
    $_SESSION['alerta_email'] = [
    'status' => 'danger',
    'mensagem' => 'Por favor, insira um endereço de e-mail válido.'
    ];
    header('Location: AlterarEmail.php');
    exit();
}

if(enviarEmailRecuperacao($novoEmail, $idUsuario, $nomeUsuario)){
    $_SESSION['alerta_email'] = [
        'status' => 'success',
        'mensagem' => 'Link de ativação enviado. Acesse sua caixa de entrada.'
    ];
}else{
   $_SESSION['alerta_email']  = [
        'status' => 'danger',
        'mensagem' => 'Não foi possível enviar o e-mail de confirmação. Verifique os dados e tente novamente.'
   ];
}

header('Location: AlterarEmail.php');

?>