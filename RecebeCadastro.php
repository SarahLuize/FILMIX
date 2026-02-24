<?php

require_once 'db_funcoes.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro.php');
    exit;
}

$nome = isset($_POST['CadastroNome']) ? trim($_POST['CadastroNome']) : '';
$email = isset($_POST['CadastroEmail']) ? trim($_POST['CadastroEmail']) : '';
$senha = isset($_POST['CadastroSenha']) ? $_POST['CadastroSenha'] : '';
$senhaConfirmar = isset($_POST['CadastroSenhaConfirmar']) ? $_POST['CadastroSenhaConfirmar'] : '';
$dataNascimento = isset($_POST['CadastroData']) ? $_POST['CadastroData'] : '';

if (empty($nome) || empty($email) || empty($senha) || empty($senhaConfirmar)) {
    $_SESSION['erro_cadastro'] = 'Por favor, preencha todos os campos obrigatórios.';
    header('Location: cadastro.php');
    exit;
}

if ($senha !== $senhaConfirmar) {
    $_SESSION['erro_cadastro'] = 'As senhas não coincidem. Por favor, verifique e tente novamente.';
    header('Location: cadastro.php');
    exit;
}

if (strlen($senha) < 6) {
    $_SESSION['erro_cadastro'] = 'A senha deve ter no mínimo 6 caracteres.';
    header('Location: cadastro.php');
    exit;
}

$usuarioExistente = buscarUsuarioPorEmail($email);

if ($usuarioExistente) {
    $_SESSION['erro_cadastro'] = 'Este e-mail já está cadastrado no sistema. Por favor, use outro e-mail ou faça login.';
    header('Location: cadastro.php');
    exit;
}

$resultado = inserirUsuario($nome, $email, $senha);

if ($resultado['sucesso']) {
    $_SESSION['sucesso_cadastro'] = 'Cadastro realizado com sucesso! Faça login para continuar.';
    
    if (isset($_POST['ManterConectado']) && $_POST['ManterConectado'] === 'Conectado') {
        $usuario = buscarUsuarioPorEmail($email);
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nome_usuario'] = $usuario['nome'];
        $_SESSION['email_usuario'] = $usuario['email'];
        header('Location: TelaPrincipal.php');
    } else {
        header('Location: index.php');
    }
    exit;
} else {
    $_SESSION['erro_cadastro'] = 'Erro ao realizar cadastro. Por favor, tente novamente.';
    if (isset($resultado['erro'])) {
        error_log('Erro ao cadastrar usuário: ' . $resultado['erro']);
    }
    header('Location: cadastro.php');
    exit;
}

