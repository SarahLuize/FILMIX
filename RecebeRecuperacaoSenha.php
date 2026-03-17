<?php

require_once 'db_funcoes.php';

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

header('Location: index.php');
exit;