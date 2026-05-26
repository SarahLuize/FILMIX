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
    // Adaptação das classificações indicativas de alguns outros países para a classificação do Brasil
    // caso a classificação brasileira de algum filme não tenha sido adicionada na API ainda 
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
        'G' => 'L',
        'PG' => '10',
        '14A' => '14',
        '18A' => '18'
    ],
    'MX' => [ //MÉXICO
        'A' => 'L',
        'AA' => '6',
        'B' => '12',
        'B-15' => '16',
        'C' => '18'
    ],

    // ------ EUROPA ------
     'GB' => [ //REINO UNIDO
        'U' => 'L',
        'PG' => '10',
        '12/A' => '12',
        '12' => '12',
        '15' => '16',
        '18' => '18',
        'R18' => 'Restrito'
    ],
    'DE' => [ //ALEMANHA
        'FSK 0' => 'L',
        'FSK 6' => '6',
        'FSK 12' => '12',
        'FSK 16' => '16',
        'FSK 18' => '18',
        'Infoprogramm' => 'L',
        'Lehrprogramm' => 'L'
    ],
    'IT' => [ //ITÁLIA
        'T' => 'L',
        '6+' => '6',
        '10+' => '10',
        '14+' => '14',
        '18+' => '18'
    ],
    'FR' => [ //FRANÇA
        'TP' => 'L',
        '-12' => '12',
        '-16' => '16',
        '-18' => '18',
        'X' => 'Restrito'
    ],
    'ES' => [ //ESPANHA
        'A' => 'L',
        'Ai' => 'L',
        '7' => '10',
        '7i' => '10', // Não adicionados o restante, porque são os mesmos do Brasil
        'X' => 'Restrito'
    ],
    'PT' => [ //PORTUGAL
        'Para todos os públicos' => 'L',
        'M/3' => 'L',
        'M/6' => '6',
        'M/12' => '12',
        'M/14' => '14',
        'M/16' => '16',
        'M/18' => '18',
        'P' => 'Restrito'
    ],
    'NL' => [ //PAÍSES BAIXOS/HOLANDA
        'AL' => 'L', // Não adicionados o restante, porque são os mesmos do Brasil
        '9' => '10'
    ],
    'SE' => [ //SUÉCIA
        'Btl' => 'L',
        '7' => '10',
        '11' => '12',
        '15' => '16',
        'Not Approved' => 'Restrito',
    ],
    'RU' => [ //RÚSSIA
        '0+' => 'L',
        '6+' => '6',
        '12+' => '12',
        '16+' => '16',
        '18+' => '18'
    ],

    // ------ ÁSIA ------
    'IN' => [ //ÍNDIA
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
    'HK' => [ //HONG KONG
        'I' => 'L',
        'IIA' => '12',
        'IIB' => '16',
        'III' => '18'
    ],
    'KR' => [ //CORÉIA DO SUL
        'G' => 'L',
        '12' => '12',
        '15' => '16',
        '19' => '18',
        'Restricted Screening' => '18'
    ],
    'PH' => [ //FILIPINAS
        'G' => 'L',
        'PG' => '6',
        'R-13' => '14',
        'R-16' => '16',
        'R-18' => '18',
        'X' => 'Restrito'
    ],
    'TW' => [ //TAIWAN
        '0+' => 'L',
        '6+' => '6',
        '12+' => '12',
        '15+' => '16',
        '18+' => '18'
    ],
    'TH' => [ //TAILÂNDIA
        'P' => 'L',
        'G' => 'L',
        '13' => '14',
        '15' => '16',
        '18' => '18',
        '20' => 'Restrito',
        'Banned' => 'Restrito'
    ],
    'TR' => [ //TURQUIA
        'Genel' => 'L',
        '6A' => 'L',
        '6+' => '6',
        '10A' => '10',
        '10+' => '10',
        '13A' => '14',
        '13+' => '14',
        '16+' => '16',
        '18+' => '18',
    ], 

    // ------ OCEANIA ------
    'AU' => [ //AUSTRÁLIA
        'G' => 'L',
        'PG' => '6',
        'M' => '16',
        'MA 15+' => '16',
        'R 18+' => '18',
        'X 18+' => 'Restrito',
        'RC' => 'Restrito'
    ],
    'NZ' => [ //NOVA ZELÂNDIA
        'G' => 'L',
        'PG' => '6',
        'M' => '16',
        'RP13' => '14',
        'RP16' => '16',
        'RP18' => '18',
        'R13' => '14',
        'R15' => '16',
        'R16' => '16',
        'R18' => '18',
        'R' => 'Restrito'
    ],

    ];

    if (!isset($filme['release_dates']['results'])) { //verifica se não tem a classificação do filme
        // Já retorna 18 se tiver classificado como 'adulto' na API
        if (isset($filme['adult']) && $filme['adult']) {
            return '18';
        }
        //Se não, retorna não classificado
        return 'Não Classificado';
    }

    $BuscaClassificacao = $filme['release_dates']['results'];

    foreach ($BuscaClassificacao as $pais) { //Busca exclusivamente classificação do Brasil
        if (($pais['iso_3166_1'] ?? null) === 'BR' && !empty($pais['release_dates'])) {
            foreach ($pais['release_dates'] as $lancamento) {
                $certificacao = trim($lancamento['certification'] ?? '');
                if ($certificacao != ''){
                    return $certificacao; //já retorna a classificação indicativa do Brasil
                }
            }
        }
    }

    foreach ($BuscaClassificacao as $pais) {
        $codigoPais = $pais['iso_3166_1'] ?? null;
        if (($pais['iso_3166_1'] ?? null) === 'BR' || empty($pais['release_dates'])){
            continue; //Pula a busca se for inválido ou se for 'BR' (já procurou antes)
        }

            foreach ($pais['release_dates'] as $lancamento) {
            $certificacao = trim($lancamento['certification'] ?? '');
            if($certificacao != '') {
                if (isset($classificacoes[$codigoPais][$certificacao])) {
                    return $classificacoes[$codigoPais][$certificacao];
                }
                break;
            }
        }
    }
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
