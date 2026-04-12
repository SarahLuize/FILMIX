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
   
    if ($usuarioExistente['situacao'] == 1) {
        $_SESSION['erro_cadastro'] = 'Este e-mail já está ativo no Filmix.';
        header('Location: cadastro.php');
        exit;
    }     
    $agora = date('Y-m-d H:i:s');
    if ($usuarioExistente['validade'] <= $agora) {
        
        excluirUsuarioExpirado($email); 
        
    } else {        
        $_SESSION['erro_cadastro'] = 'Você já tem um link enviado que ainda não expirou.';
        header('Location: cadastro.php');
        exit;
    }
}

$token = bin2hex(random_bytes(16));

 // Caso queira testar pouquíssimos minutos
$validade = date('Y-m_d H:i:s', strtotime('+3 minutes'));
$resultado = inserirUsuario($nome, $email, $senha, $token, 0, $validade);

//$resultado = inserirUsuario($nome, $email, $senha, $token, 0);

if ($resultado['sucesso']) {
    //Tentar enviar o e-mail primeiro
    if(enviarEmailAtivacao($email, $nome, $token)){
    $_SESSION['sucesso_cadastro'] = 'Cadastro realizado! Verifique seu e-mail para validar o login.';
     header('Location: login.php');
    exit;
    }else{   
    $_SESSION['erro_cadastro'] = 'Usuário cadastrado, mas houve um erro ao enviar o e-mail de validação. Entre em contato com o suporte.';
        header('Location: cadastro.php');
        exit;
    }
   
}
