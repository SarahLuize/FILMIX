<?php

define('TMDB_API_KEY', 'acb6037e95c18790dc9935bca86c25f3');
define('TMDB_BASE_URL', 'https://api.themoviedb.org/3');
define('TMDB_IMAGE_BASE_URL', 'https://image.tmdb.org/t/p/w500');

function buscarFilmesLancamentos($pagina = 1) {
    $apiKey = TMDB_API_KEY;
    $url = TMDB_BASE_URL . '/movie/now_playing?api_key=' . $apiKey . '&language=pt-BR&page=' . $pagina;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return ['erro' => 'Erro ao buscar filmes em lançamento', 'codigo' => $httpCode];
    }
    
    $dados = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['erro' => 'Erro ao decodificar resposta da API'];
    }
    
    return $dados;
}

function buscarFilmesPopulares($pagina = 1) {
    $apiKey = TMDB_API_KEY;
    $url = TMDB_BASE_URL . '/movie/popular?api_key=' . $apiKey . '&language=pt-BR&page=' . $pagina;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return ['erro' => 'Erro ao buscar filmes populares', 'codigo' => $httpCode];
    }
    
    $dados = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['erro' => 'Erro ao decodificar resposta da API'];
    }
    
    return $dados;
}

function buscarTodosFilmesPopulares($totalPaginas = 5) {
    $todosFilmes = [];
    
    for ($pagina = 1; $pagina <= $totalPaginas; $pagina++) {
        $dados = buscarFilmesPopulares($pagina);
        
        if (isset($dados['results']) && !empty($dados['results'])) {
            $todosFilmes = array_merge($todosFilmes, $dados['results']);
        }
        
        usleep(250000);
    }
    
    return $todosFilmes;
}

function buscarFilmePorId($filmeId) {
    $apiKey = TMDB_API_KEY;
    $url = TMDB_BASE_URL . '/movie/' . $filmeId . '?api_key=' . $apiKey . '&language=pt-BR&append_to_response=release_dates';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return ['erro' => 'Erro ao buscar filme', 'codigo' => $httpCode];
    }
    
    $dados = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['erro' => 'Erro ao decodificar resposta da API'];
    }
    
    return $dados;
}

function obterClassificacaoFilme($filme) {
    if (isset($filme['release_dates']['results'])) {
        foreach ($filme['release_dates']['results'] as $pais) {
            if ($pais['iso_3166_1'] === 'BR' && isset($pais['release_dates'][0]['certification'])) {
                $certificacao = $pais['release_dates'][0]['certification'];
                if (!empty($certificacao)) {
                    return $certificacao;
                }
            }
        }
    }
    
    if (isset($filme['adult']) && $filme['adult']) {
        return '18+';
    }
    
    return 'Livre';
}

function buscarFilmesPorNome($nomeFilme, $pagina = 1) {
    $apiKey = TMDB_API_KEY;
    $nomeFilme = urlencode($nomeFilme);
    $url = TMDB_BASE_URL . '/search/movie?api_key=' . $apiKey . '&language=pt-BR&query=' . $nomeFilme . '&page=' . $pagina;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return ['erro' => 'Erro ao buscar filmes', 'codigo' => $httpCode];
    }
    
    $dados = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['erro' => 'Erro ao decodificar resposta da API'];
    }
    
    return $dados;
}

function obterUrlImagem($caminhoImagem) {
    if (empty($caminhoImagem)) {
        return '';
    }
    
    return TMDB_IMAGE_BASE_URL . $caminhoImagem;
}

function formatarDataFilme($data) {
    if (empty($data)) {
        return '';
    }
    
    $timestamp = strtotime($data);
    return date('d/m/Y', $timestamp);
}

