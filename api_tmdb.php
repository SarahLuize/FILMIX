<?php

define('TMDB_API_KEY', 'acb6037e95c18790dc9935bca86c25f3');
define('TMDB_BASE_URL', 'https://api.themoviedb.org/3');
define('TMDB_IMAGE_BASE_URL', 'https://image.tmdb.org/t/p/w500');

function buscarFilmesLancamentos($pagina = 1)
{
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

function buscarFilmesPopulares($pagina = 1)
{
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

/**
 * Recomendações do TMDB para um filme (filmes similares / sugeridos).
 * see https://developer.themoviedb.org/reference/movie-recommendations
 */
function buscarRecomendacoesTmdbFilme(int $idTmdb, int $pagina = 1)
{
    $apiKey = TMDB_API_KEY;
    $pagina = max(1, $pagina);
    $url = TMDB_BASE_URL . '/movie/' . $idTmdb . '/recommendations?api_key=' . $apiKey . '&language=pt-BR&page=' . $pagina;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        return ['erro' => 'Erro ao buscar recomendações', 'codigo' => $httpCode];
    }

    $dados = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['erro' => 'Erro ao decodificar resposta da API'];
    }

    return $dados;
}

/**
 * Monta até $limite filmes recomendados com base nos TMDB ids favoritados.
 * Agrega recomendações da API para vários favoritos, remove duplicatas e o que já está nos favoritos;
 * completa com populares se faltar quantidade.
 */
function montarRecomendadosParaUsuarioPorFavoritos(array $idsFavoritosTmdb, int $limite = 10): array
{
    $idsFavoritosTmdb = array_values(array_unique(array_map('intval', $idsFavoritosTmdb)));
    if (empty($idsFavoritosTmdb)) {
        return [];
    }

    $exclude = array_flip($idsFavoritosTmdb);
    $merged = [];
    $seen = [];

    $idsConsulta = array_slice($idsFavoritosTmdb, 0, 5);

    foreach ($idsConsulta as $idTmdb) {
        $dados = buscarRecomendacoesTmdbFilme($idTmdb, 1);
        if (isset($dados['erro'])) {
            usleep(100000);
            continue;
        }
        foreach ($dados['results'] ?? [] as $filme) {
            $fid = (int) $filme['id'];
            if (isset($exclude[$fid]) || isset($seen[$fid])) {
                continue;
            }
            $seen[$fid] = true;
            $merged[] = $filme;
        }
        usleep(150000);
    }

    usort($merged, function ($a, $b) {
        $va = (float) ($a['vote_average'] ?? 0);
        $vb = (float) ($b['vote_average'] ?? 0);
        if ($vb !== $va) {
            return $vb <=> $va;
        }
        return (float) ($b['popularity'] ?? 0) <=> (float) ($a['popularity'] ?? 0);
    });

    $merged = array_slice($merged, 0, $limite);

    if (count($merged) < $limite) {
        $pop = buscarFilmesPopulares(1);
        if (!isset($pop['erro']) && !empty($pop['results'])) {
            $ja = array_flip(array_map('intval', array_column($merged, 'id')));
            foreach ($pop['results'] as $filme) {
                if (count($merged) >= $limite) {
                    break;
                }
                $fid = (int) $filme['id'];
                if (isset($exclude[$fid]) || isset($ja[$fid])) {
                    continue;
                }
                $ja[$fid] = true;
                $merged[] = $filme;
            }
        }
    }

    return $merged;
}

function buscarTodosFilmesPopulares($totalPaginas = 5)
{
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

function buscarFilmePorId($filmeId)
{
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

function obterClassificacaoFilme($filme)
{

    $classificacoes = 
    [

    // ------ AMÉRICA DO SUL ------
    'AR' => [ //ARGENTINA
        'ATP' => 'L',
        '13'  => '12',
        '16'  => '16',
        '18'  => '18'
    ],
    'CL' => [ //CHILE
        'TE' => 'L',
        '7'  => '10',
        '14' => '14',
        '18'  => '18'
    ],

    // ------ AMÉRICA DO NORTE ------
    'US' => [ //ESTADOS UNIDOS
        'G' => 'L',
        'PG' => '10',
        'PG-13' => '12',
        'R' => '16',
        'NC-17' => '18'
    ],
    'CA' => [ //CANADÁ
        //
    ],
    'MX' => [ //MEXICO
        //
    ],

    // ------ EUROPA ------
     'GB' => [ //REINO UNIDO
        //
    ],
    'DE' => [ //ALEMANHA
        //
    ],
    'IT' => [ //ITALIA
        //
    ],
    'FR' => [ //FRANÇA
        //
    ],
    'ES' => [ //ESPANHA
        //
    ],
    'PT' => [ //PORTUGAL
        //
    ],
    'NL' => [ //PAÍSES BAIXOS/HOLANDA
        //
    ],

    // ------ ÁSIA  ------
    'IN' => [ //INDIA
        'U'       => 'L',
        'UA'      => '12',
        'UA 7+'   => '10',
        'UA 13+'  => '12',
        'UA 16+'  => '16',
        'A'       => '18',
        'S'       => '18'
    ],
    'JP' => [ //JAPÃO
        'ALL' => 'L',
        '12' => '12',
        'R15+' => '16',
        'R18+' => '18'
    ],
    'KR' => [ //COREIA DO SUL
        'G' => 'L',
        '12' => '12',
        '15' => '16',
        '19' => '18',
        'Restricted Screening' => '18'
    ],
    'PH' => [ //FILIPINAS
        //
    ],

    // ------ OCEANIA ------
    'AU' => [ //AUSTRALIA
        //
    ],
    'NZ' => [ //NOVA ZELANDIA
        //
    ],

    ];

    if (isset($filme['release_dates']['results'])) {
        foreach ($filme['release_dates']['results'] as $pais) {

            $codigoPais = $pais['iso_3166_1'] ?? null;

            if (!$codigoPais || empty($pais['release_dates'])) {
                continue;
            }
            if (!isset($pais['release_dates'][0]['certification'])) {
                continue;
            }
             $certificacao = trim(
                $pais['release_dates'][0]['certification'] ?? ''
            );
            if ($certificacao === '') {
                continue;
            }
            //MOSTRA CLASSIFICAÇÃO
            if (isset($classificacoes[$codigoPais][$certificacao])) {
                return $classificacoes[$codigoPais][$certificacao];
            }
            
            //BRASIL
            if ($codigoPais === 'BR' && !empty($certificacao)) {
                return $certificacao;
            }
        }
    }

    // Adulto TMDb
    if (isset($filme['adult']) && $filme['adult']) {
        return '18';
    }

    //SEM CLASSIFICAÇÃO DEFINIDA
    return 'Não Classificado';
}



function buscarFilmesPorNome($nomeFilme, $pagina = 1)
{
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

function obterUrlImagem($caminhoImagem)
{
    if (empty($caminhoImagem)) {
        return '';
    }

    return TMDB_IMAGE_BASE_URL . $caminhoImagem;
}

function formatarDataFilme($data)
{
    if (empty($data)) {
        return '';
    }

    $timestamp = strtotime($data);
    return date('d/m/Y', $timestamp);
}

/* Retorna a lista de gêneros de filmes */
function buscarGeneros()
{
    $apiKey = TMDB_API_KEY;
    $url = TMDB_BASE_URL . '/genre/movie/list?api_key=' . $apiKey . '&language=pt-BR';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        return ['erro' => 'Erro ao buscar gêneros', 'codigo' => $httpCode];
    }

    $dados = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['erro' => 'Erro ao decodificar resposta da API'];
    }

    return $dados;
}

/* Converte os IDs dos gêneros dos filmes em nomes */
function mostrarNomesGeneros(array $idsGeneros, array $listaGeneros): array
{
    $mapa = array_column($listaGeneros, 'name', 'id');
    $nomes = [];
    foreach ($idsGeneros as $id) {
        if (isset($mapa[$id])) {
            $nomes[] = $mapa[$id];
        }
    }
    return $nomes;
}

/* Busca filmes filtrados por gênero */
function buscarFilmesPorGenero($generoId, $pagina = 1)
{
    $apiKey = TMDB_API_KEY;
    $url = TMDB_BASE_URL . '/discover/movie?api_key=' . $apiKey . '&language=pt-BR&with_genres=' . $generoId . '&page=' . $pagina;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        return ['erro' => 'Erro ao buscar filmes por gênero', 'codigo' => $httpCode];
    }

    $dados = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['erro' => 'Erro ao decodificar resposta da API'];
    }

    return $dados;
}
