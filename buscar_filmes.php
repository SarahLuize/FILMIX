<?php  // Página criada para gerar o vetor com os filmes
require_once 'api_tmdb.php';

$busca = $_GET['s'] ?? '';

/* Apenas pesquisa quando tiver no mínimo 2 caracteres */
if(strlen($busca) < 2){
    echo json_encode([]);
    exit;
}

$resultado = buscarFilmesPorNome($busca);

$titulos = [];

foreach ($resultado['results'] as $filme) {
    $titulos[] = $filme['title'] ?? '';
}
echo json_encode($titulos);
?>