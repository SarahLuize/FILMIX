<?php 

session_start();
require_once 'db_funcoes.php'; // Certifica-se de que este arquivo tem a sua conexão
require_once 'config_db.php';
$conexao = obterConexao();

//Se o usuário não estiver logado, manda de volta para a principal
if (!isset($_SESSION['id_usuario'])) {
    header('Location: TelaPrincipal.php');
    exit;
}

$idUsuario = (int)$_SESSION['id_usuario'];

try {
    // 1. Apaga os registros de favoritos e assistir mais tarde primeiro(Segurança do Banco)
    $sqlFav = "DELETE FROM usuario_favorito 
    WHERE id_usuario = ?";    
    $stmtFav = $conexao->prepare($sqlFav);
    $stmtFav->bind_param("i", $idUsuario); // "i" indica que $idUsuario é um número inteiro
    $stmtFav->execute();

    //2. Apaga assistir mais tarde
    $sqlAssistir = "DELETE FROM usuario_assistir_mais_tarde
    WHERE id_usuario = ?";
    $stmtAssistir = $conexao->prepare($sqlAssistir);
    $stmtAssistir->bind_param("i", $idUsuario);
    $stmtAssistir->execute();

    // 3. Agora sim. apaga o usuário da tabela principal
    $sqlUser = "DELETE FROM usuario 
    WHERE id_usuario = ?";
    $stmtUser = $conexao->prepare($sqlUser);
    $stmtUser->bind_param("i", $idUsuario);
    $stmtUser->execute();

    // Destroi a sessão para deslogar o usuário totalmente
    if(session_start() === PHP_SESSION_NONE){
       session_start(); //  Garante que a sessão está antes de destrir
    }
    session_unset();
    session_destroy();

    // 4. Redireciona para a tela principal avisando que foi apagado
    header('Location: TelaPrincipal.php?conta_apaga=sucesso');
    exit;

} catch (Exception $e) {
    //Se der algum erro no banco, impede a quebra da página e avisa
    echo "Erro ao excluir a conta: " . $e->getMessage();
}
?>