<?php

require_once 'db_funcoes.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$email = isset($_POST['LoginEmail']) ? trim($_POST['LoginEmail']) : '';
$senha = isset($_POST['LoginSenha']) ? $_POST['LoginSenha'] : '';

if (empty($email) || empty($senha)) {
    $_SESSION['erro_login'] = 'Por favor, preencha todos os campos.';
    header('Location: index.php');
    exit;
}

$usuario = buscarUsuarioPorEmail($email);

if (!$usuario) {
    $_SESSION['erro_login'] = 'E-mail não cadastrado no sistema. Por favor, cadastre-se para continuar.';
    header('Location: index.php');
    exit;
}

if (!password_verify($senha, $usuario['senha'])) {
    $_SESSION['erro_login'] = 'Senha incorreta. Tente novamente.';
    header('Location: index.php');
    exit;
}

$_SESSION['id_usuario'] = $usuario['id_usuario'];
$_SESSION['nome_usuario'] = $usuario['nome'];
$_SESSION['email_usuario'] = $usuario['email'];

unset($_SESSION['erro_login']);

header('Location: TelaPrincipal.php');
exit;

