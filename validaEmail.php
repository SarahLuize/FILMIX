<?php
    require_once 'db_funcoes.php';
    session_start();

    $token = isset($_GET['token']) ? $_GET['token'] : '';

    if(empty($token)){
        $_SESSION['Erro_login'] = "Token de ativação inválido.";
        header("Location: index.php");    
        exit;
    }
    //Conectando ao banco
    $conn = obterConexao();
      
    $query = "SELECT id_usuario 
              FROM usuario 
              WHERE token = ? AND validade > NOW() AND situacao = 0";
              $stmt = mysqli_prepare($conn, $query);
              mysqli_stmt_bind_param($stmt, "s", $token);
              mysqli_stmt_execute($stmt);
              $resultado = mysqli_stmt_get_result($stmt);
              $usuario = mysqli_fetch_assoc($resultado);

              if($usuario){
                $id = $usuario['id_usuario'];

                $update = "UPDATE usuario SET situacao = 1, token = NULL, validade = NULL 
                           WHERE id_usuario = ?";
                $stmtUpdate = mysqli_prepare($conn, $update);
                mysqli_stmt_bind_param($stmtUpdate, "i", $id);

                if(mysqli_stmt_execute($stmtUpdate)){
                    $_SESSION['sucesso_login'] = "Conta ativa com sucesso! Faça seu login.";                    
                }else{
                    $_SESSION['erro_login'] = "Erro ao ativar conta. Tente novamente mais tarde.";
                }                
              }else{
                //Se não achou o token ou a validade expirou
                $_SESSION['erro_login'] = "Erro link de ativação é inválido ou já expirou.";
              }
            
            header("Location:login.php");
            exit;
?>