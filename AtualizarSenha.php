<?php

require_once 'db_funcoes.php';
session_start();

$token = $_POST['token'] ?? '';
$nova_senha = $_POST['nova_senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';


if(empty($token) || empty($nova_senha) || empty($confirmar_senha)){
    $_SESSION['erro_cadastro'] = "Todos os campos são obrigatórios.";
    header("Location: NovaSenha.php?token=$token");
    exit;
}

if($nova_senha !== $confirmar_senha){
    $_SESSION['erro_cadastro'] = "As senhas não coincidem.";
    header("Location: NovaSenha.php?token=$token");
    exit;
}

$conn = obterConexao();

$senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

$sql = " UPDATE usuario SET senha = ?, token = NULL, validade = NULL
         WHERE token = ?";
$stmt = mysqli_prepare($conn, $sql);

if($stmt){
    mysqli_stmt_bind_param($stmt, "ss", $senha_hash, $token);
    mysqli_stmt_execute($stmt);

    if(mysqli_stmt_affected_rows($stmt) > 0 ){
        $_SESSION['sucesso_cadastro'] = "Senha atualizada com sucesso! Faça login.";
        header('Location: login.php');
    }else{
        $_SESSION['erro_cadastro'] = "Erro ao atualizar. O link pode ter expirado.";
        header('Location: RecuperarSenha.php');
    }
}else{
    $_SESSION['erro_cadastro'] = "Erro interno no servidor.";
    header('Location: RecuperarSenha.php');
}
exit;
?>