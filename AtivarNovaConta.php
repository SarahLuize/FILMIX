<?php
session_start();
require_once 'db_funcoes.php';

if (isset($_GET['id'])) {
    // 1. Pega o ID bruto da URL
    $idUrl = $_GET['id'];
    
    // 2. Remove a proteção de URL que adicionamos no envio
    $idDesprotegido = urldecode($idUrl);
    
    // 3. Agora sim, faz o decode do Base64 limpo
    $idUsuario = base64_decode($idDesprotegido);

    // 4. Converte para número inteiro puro
    $idUsuarioInt = (int)$idUsuario;

    // Se o ID for maior que zero, faz a ativação
        if ($idUsuarioInt > 0) {
            
                if (ativarNovoEmailUsuario($idUsuarioInt)) {
                    
                    $_SESSION['sucesso_login'] = 'Email alterado com sucesso!';

                    header('Location: login.php');
                    exit;
                } else {

                    $_SESSION['erro_login'] = 'Erro ao tentar ativar seu novo e-mail no banco de dados. Tente novamente.!';
                    header('Location: login.php');
                    exit;
                }

        } else {
            $_SESSION['erro-login'] = 'Link de ativação inválido ou corrompido';
        }
} else {
    header('Location: login.php');
    exit;
}
?>