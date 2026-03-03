<?php

require_once 'config_db.php';

function inserirUsuario($nome, $email, $senha) {
    $conn = obterConexao();
    
    $nome = mysqli_real_escape_string($conn, $nome);
    $email = mysqli_real_escape_string($conn, $email);
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    
    $query = "INSERT INTO usuario (nome, email, senha) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return ['sucesso' => false, 'erro' => 'Erro ao preparar consulta'];
    }
    
    mysqli_stmt_bind_param($stmt, 'sss', $nome, $email, $senhaHash);
    
    if (mysqli_stmt_execute($stmt)) {
        $idUsuario = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        return ['sucesso' => true, 'id_usuario' => $idUsuario];
    }
    
    $erro = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    return ['sucesso' => false, 'erro' => $erro];
}

function buscarUsuarioPorEmail($email) {
    $conn = obterConexao();
    
    $email = mysqli_real_escape_string($conn, $email);
    $query = "SELECT id_usuario, nome, email, senha, data_cadastro FROM usuario WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return null;
    }
    
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
    
    return $usuario;
}

function inserirFilme($idTmdb, $titulo, $idiomaOriginal, $popularidade, $mediaAvaliacao, $posterUrl, $dataLancamento, $sinopse) {
    $conn = obterConexao();
    
    $query = "INSERT INTO filme (id_tmdb, titulo, idioma_original, popularidade, media_avaliacao, poster_url, data_lancamento, sinopse) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)
              ON DUPLICATE KEY UPDATE 
              titulo = VALUES(titulo),
              idioma_original = VALUES(idioma_original),
              popularidade = VALUES(popularidade),
              media_avaliacao = VALUES(media_avaliacao),
              poster_url = VALUES(poster_url),
              data_lancamento = VALUES(data_lancamento),
              sinopse = VALUES(sinopse)";
    
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return ['sucesso' => false, 'erro' => 'Erro ao preparar consulta'];
    }
    
    mysqli_stmt_bind_param($stmt, 'issdssss', $idTmdb, $titulo, $idiomaOriginal, $popularidade, $mediaAvaliacao, $posterUrl, $dataLancamento, $sinopse);
    
    if (mysqli_stmt_execute($stmt)) {
        $idFilme = mysqli_insert_id($conn);
        
        if ($idFilme == 0) {
            $queryBusca = "SELECT id_filme FROM filme WHERE id_tmdb = ?";
            $stmtBusca = mysqli_prepare($conn, $queryBusca);
            mysqli_stmt_bind_param($stmtBusca, 'i', $idTmdb);
            mysqli_stmt_execute($stmtBusca);
            $resultado = mysqli_stmt_get_result($stmtBusca);
            $filme = mysqli_fetch_assoc($resultado);
            $idFilme = $filme['id_filme'];
            mysqli_stmt_close($stmtBusca);
        }
        
        mysqli_stmt_close($stmt);
        return ['sucesso' => true, 'id_filme' => $idFilme];
    }
    
    $erro = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    return ['sucesso' => false, 'erro' => $erro];
}

function buscarFilmePorIdTmdb($idTmdb) {
    $conn = obterConexao();
    
    $query = "SELECT id_filme, id_tmdb, titulo, idioma_original, popularidade, media_avaliacao, poster_url, data_lancamento, sinopse 
              FROM filme WHERE id_tmdb = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return null;
    }
    
    mysqli_stmt_bind_param($stmt, 'i', $idTmdb);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $filme = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
    
    return $filme;
}

function buscarFilmePorIdFilme($idFilme) {
    $conn = obterConexao();
    
    $query = "SELECT id_filme, id_tmdb, titulo, idioma_original, popularidade, media_avaliacao, poster_url, data_lancamento, sinopse 
              FROM filme WHERE id_filme = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return null;
    }
    
    mysqli_stmt_bind_param($stmt, 'i', $idFilme);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $filme = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
    
    return $filme;
}

function criarRecomendacao($idUsuario, $titulo, $descricao) {
    $conn = obterConexao();
    
    $titulo = mysqli_real_escape_string($conn, $titulo);
    $descricao = mysqli_real_escape_string($conn, $descricao);
    
    $query = "INSERT INTO recomendacao (id_usuario, titulo, descricao) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return ['sucesso' => false, 'erro' => 'Erro ao preparar consulta'];
    }
    
    mysqli_stmt_bind_param($stmt, 'iss', $idUsuario, $titulo, $descricao);
    
    if (mysqli_stmt_execute($stmt)) {
        $idRecomendacao = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        return ['sucesso' => true, 'id_recomendacao' => $idRecomendacao];
    }
    
    $erro = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    return ['sucesso' => false, 'erro' => $erro];
}

function adicionarFilmeRecomendacao($idFilme, $idRecomendacao) {
    $conn = obterConexao();
    
    $query = "INSERT INTO filme_recomendacao (id_filme, id_recomendacao) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return ['sucesso' => false, 'erro' => 'Erro ao preparar consulta'];
    }
    
    mysqli_stmt_bind_param($stmt, 'ii', $idFilme, $idRecomendacao);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['sucesso' => true];
    }
    
    $erro = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    return ['sucesso' => false, 'erro' => $erro];
}

function buscarRecomendacoesPorUsuario($idUsuario) {
    $conn = obterConexao();
    
    $query = "SELECT r.id_recomendacao, r.titulo, r.descricao, r.data_recomendacao 
              FROM recomendacao r 
              WHERE r.id_usuario = ? 
              ORDER BY r.data_recomendacao DESC";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return [];
    }
    
    mysqli_stmt_bind_param($stmt, 'i', $idUsuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $recomendacoes = [];
    
    while ($row = mysqli_fetch_assoc($resultado)) {
        $recomendacoes[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $recomendacoes;
}

function buscarFilmesPorRecomendacao($idRecomendacao) {
    $conn = obterConexao();
    
    $query = "SELECT f.id_filme, f.id_tmdb, f.titulo, f.poster_url, f.data_lancamento, f.media_avaliacao 
              FROM filme f
              INNER JOIN filme_recomendacao fr ON f.id_filme = fr.id_filme
              WHERE fr.id_recomendacao = ?
              ORDER BY f.titulo";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return [];
    }
    
    mysqli_stmt_bind_param($stmt, 'i', $idRecomendacao);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $filmes = [];
    
    while ($row = mysqli_fetch_assoc($resultado)) {
        $filmes[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $filmes;
}

function deletarRecomendacao($idRecomendacao) {
    $conn = obterConexao();
    
    $query = "DELETE FROM recomendacao WHERE id_recomendacao = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return ['sucesso' => false, 'erro' => 'Erro ao preparar consulta'];
    }
    
    mysqli_stmt_bind_param($stmt, 'i', $idRecomendacao);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['sucesso' => true];
    }
    
    $erro = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    return ['sucesso' => false, 'erro' => $erro];
}

function ehFavorito($idUsuario, $idTmdb) {
    $conn = obterConexao();
    $query = "SELECT 1 FROM usuario_favorito WHERE id_usuario = ? AND id_tmdb = ?";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        return false;
    }
    mysqli_stmt_bind_param($stmt, 'ii', $idUsuario, $idTmdb);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_fetch_assoc($resultado) !== null;
    mysqli_stmt_close($stmt);
    return $existe;
}

function inserirFavorito($idUsuario, $idTmdb) {
    $conn = obterConexao();
    $query = "INSERT IGNORE INTO usuario_favorito (id_usuario, id_tmdb) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        return ['sucesso' => false, 'erro' => 'Erro ao preparar consulta'];
    }
    mysqli_stmt_bind_param($stmt, 'ii', $idUsuario, $idTmdb);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['sucesso' => true];
    }
    $erro = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    return ['sucesso' => false, 'erro' => $erro];
}

function removerFavorito($idUsuario, $idTmdb) {
    $conn = obterConexao();
    $query = "DELETE FROM usuario_favorito WHERE id_usuario = ? AND id_tmdb = ?";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        return ['sucesso' => false, 'erro' => 'Erro ao preparar consulta'];
    }
    mysqli_stmt_bind_param($stmt, 'ii', $idUsuario, $idTmdb);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['sucesso' => true];
    }
    $erro = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    return ['sucesso' => false, 'erro' => $erro];
}

function listarIdsFavoritosPorUsuario($idUsuario) {
    $conn = obterConexao();
    $query = "SELECT id_tmdb FROM usuario_favorito WHERE id_usuario = ? ORDER BY data_adicionado DESC";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        return [];
    }
    mysqli_stmt_bind_param($stmt, 'i', $idUsuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $ids = [];
    while ($row = mysqli_fetch_assoc($resultado)) {
        $ids[] = (int) $row['id_tmdb'];
    }
    mysqli_stmt_close($stmt);
    return $ids;
}

function ehAssistirMaisTarde($idUsuario, $idTmdb) {
    $conn = obterConexao();
    $query = "SELECT 1 FROM usuario_assistir_mais_tarde WHERE id_usuario = ? AND id_tmdb = ?";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        return false;
    }
    mysqli_stmt_bind_param($stmt, 'ii', $idUsuario, $idTmdb);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_fetch_assoc($resultado) !== null;
    mysqli_stmt_close($stmt);
    return $existe;
}

function inserirAssistirMaisTarde($idUsuario, $idTmdb) {
    $conn = obterConexao();
    $query = "INSERT IGNORE INTO usuario_assistir_mais_tarde (id_usuario, id_tmdb) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        return ['sucesso' => false, 'erro' => 'Erro ao preparar consulta'];
    }
    mysqli_stmt_bind_param($stmt, 'ii', $idUsuario, $idTmdb);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['sucesso' => true];
    }
    $erro = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    return ['sucesso' => false, 'erro' => $erro];
}

function removerAssistirMaisTarde($idUsuario, $idTmdb) {
    $conn = obterConexao();
    $query = "DELETE FROM usuario_assistir_mais_tarde WHERE id_usuario = ? AND id_tmdb = ?";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        return ['sucesso' => false, 'erro' => 'Erro ao preparar consulta'];
    }
    mysqli_stmt_bind_param($stmt, 'ii', $idUsuario, $idTmdb);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['sucesso' => true];
    }
    $erro = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    return ['sucesso' => false, 'erro' => $erro];
}

function listarIdsAssistirMaisTardePorUsuario($idUsuario) {
    $conn = obterConexao();
    $query = "SELECT id_tmdb FROM usuario_assistir_mais_tarde WHERE id_usuario = ? ORDER BY data_adicionado DESC";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        return [];
    }
    mysqli_stmt_bind_param($stmt, 'i', $idUsuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $ids = [];
    while ($row = mysqli_fetch_assoc($resultado)) {
        $ids[] = (int) $row['id_tmdb'];
    }
    mysqli_stmt_close($stmt);
    return $ids;
}

