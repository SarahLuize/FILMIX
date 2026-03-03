<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'erro' => 'Método não permitido']);
    exit;
}

$idTmdb = isset($_POST['id_tmdb']) ? (int) $_POST['id_tmdb'] : 0;
$acao = isset($_POST['acao']) ? trim($_POST['acao']) : '';

if ($idTmdb <= 0) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID do filme inválido']);
    exit;
}

if (!in_array($acao, ['adicionar', 'remover'], true)) {
    echo json_encode(['sucesso' => false, 'erro' => 'Ação inválida']);
    exit;
}

require_once 'db_funcoes.php';

$idUsuario = (int) $_SESSION['id_usuario'];

if ($acao === 'adicionar') {
    $resultado = inserirFavorito($idUsuario, $idTmdb);
} else {
    $resultado = removerFavorito($idUsuario, $idTmdb);
}

if ($resultado['sucesso']) {
    echo json_encode(['sucesso' => true, 'favorito' => $acao === 'adicionar']);
} else {
    echo json_encode(['sucesso' => false, 'erro' => $resultado['erro'] ?? 'Erro ao processar']);
}
