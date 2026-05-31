<?php
session_start();

require_once 'db_funcoes.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = isset($_POST['LoginEmail']) ? trim($_POST['LoginEmail']) : '';
$senha = isset($_POST['LoginSenha']) ? $_POST['LoginSenha'] : '';
$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : '';

if (!empty($redirect) && preg_match('#^https?://#i', $redirect)) {
    $redirect = '';
}

function voltarParaLoginComRedirect(string $redirect): void {
    if (!empty($redirect)) {
        header('Location: login.php?redirect=' . urlencode($redirect));
    } else {
        header('Location: login.php');
    }
    exit;
}

if (empty($email) || empty($senha)) {
    $_SESSION['erro_login'] = 'Por favor, preencha todos os campos.';
    voltarParaLoginComRedirect($redirect);
}

$usuario = buscarUsuarioAtivo($email);

if (!$usuario) {
    $_SESSION['erro_login'] = 'E-mail não cadastrado no sistema ou não validado. Por favor, cadastre-se para continuar.';
    voltarParaLoginComRedirect($redirect);
}
if (!password_verify($senha, $usuario['senha'])) {
    $_SESSION['erro_login'] = 'Senha incorreta. Tente novamente.';
    voltarParaLoginComRedirect($redirect);	
	
}

$_SESSION['id_usuario'] = $usuario['id_usuario'];
$_SESSION['nome_usuario'] = $usuario['nome'];
$_SESSION['email_usuario'] = $usuario['email'];

// Cálculo automático da idade do usuário no login
if(!empty($usuario['data_nascimento'])) {
    $nascimento = new DateTime($usuario['data_nascimento']);
    $hoje = new DateTime();

    $idade = $hoje->diff($nascimento)->y; // Calcula a diferência exata em anos

    $_SESSION['idade_usuario'] = $idade;
    $_SESSION['usuario_data_nascimento'] = $usuario['data_nascimento'];
}else{
    $_SESSION['idade_usuario'] = 0;
    $_SESSION['usuario_data_nascimento'] = null;
}

unset($_SESSION['erro_login']);

if (!empty($redirect)) {
    header('Location: ' . $redirect);
} else {
    header('Location: TelaPrincipal.php');
}
exit;
