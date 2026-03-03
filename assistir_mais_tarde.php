<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}

require_once 'db_funcoes.php';
require_once 'api_tmdb.php';

$idUsuario = (int) $_SESSION['id_usuario'];
$idsLista = listarIdsAssistirMaisTardePorUsuario($idUsuario);
$filmes = [];

foreach ($idsLista as $idTmdb) {
    $dados = buscarFilmePorId($idTmdb);
    if (!isset($dados['erro'])) {
        $filmes[] = [
            'id' => $idTmdb,
            'title' => $dados['title'] ?? '',
            'poster_path' => $dados['poster_path'] ?? null
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FILMIX | Assistir mais Tarde</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/detalhes.css">
    <style>
        .lista-title { font-size: 28px; font-weight: bold; margin-bottom: 24px; color: #333; }
        .lista-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 24px; }
        .lista-card { text-align: center; }
        .lista-card a { text-decoration: none; color: #333; }
        .lista-poster { width: 100%; aspect-ratio: 2/3; background: #e0e0e0; border-radius: 8px; overflow: hidden; border: 2px solid #ddd; }
        .lista-poster img { width: 100%; height: 100%; object-fit: cover; }
        .lista-card-titulo { margin-top: 10px; font-size: 14px; font-weight: 500; }
        .lista-vazio { color: #666; font-size: 16px; padding: 40px 0; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
            <div class="logo-placeholder logo-pequena">
                    <a href="TelaPrincipal.php"><img src="img/FILMIX-logo.png" alt="FILMIX" class="logo-img" style="max-height: 130px; max-width: 200px;"></a>
                </div>
                <div class="search-container">
                    <form action="BarraPesquisaFilme.php" method="post" class="d-flex w-100">
                        <input type="search" name="s" id="PesquisaFilme" class="search-input" placeholder="Pesquise seu filme">
                        <button type="submit" class="search-btn"><i class="bi bi-search"></i></button>
                    </form>
                </div>
                <div class="nav-links">
                    <a href="assistir_mais_tarde.php">Assistir mais Tarde</a>
                    <a href="favoritos.php">Favoritos</a>
                    <a href="#">Gêneros</a>
                    <div class="dropdown">
                        <a href="#" role="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration:none;">
                            <i class="bi bi-person" style="font-size:1.5rem; color:#2e2e2e;"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="background-color:#fff;">
                            <li><a class="dropdown-item text-custom-dark" href="#">Perfil</a></li>
                            <li>
                                <form action="logout.php" method="POST">
                                    <button type="submit" class="dropdown-item text-custom-dark">Desconectar</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <h1 class="lista-title">Assistir mais Tarde</h1>
        <?php if (empty($filmes)): ?>
            <p class="lista-vazio">Você ainda não adicionou nenhum filme. Clique no ícone de relógio na página de um filme para adicionar.</p>
        <?php else: ?>
            <div class="lista-grid">
                <?php foreach ($filmes as $f): ?>
                    <?php
                    $urlPoster = obterUrlImagem($f['poster_path']);
                    $titulo = htmlspecialchars($f['title']);
                    ?>
                    <div class="lista-card">
                        <a href="detalhes_filme.php?id=<?php echo $f['id']; ?>">
                            <div class="lista-poster">
                                <?php if (!empty($urlPoster)): ?>
                                    <img src="<?php echo $urlPoster; ?>" alt="<?php echo $titulo; ?>" onerror="this.style.display='none'; this.parentElement.innerHTML='<span style=\'color:#999;font-size:12px;\'>Sem imagem</span>'">
                                <?php else: ?>
                                    <span style="color:#999;font-size:12px;">Sem imagem</span>
                                <?php endif; ?>
                            </div>
                            <div class="lista-card-titulo"><?php echo $titulo; ?></div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <div class="TMDB-logo">
            <img src="img/TMDBlogo.svg" style="display: flex; align-items: center; justify-content: center; height: 45%;" alt="">
        </div>
        <div class="footer-disclaimer">
            <p class="mb-0">Este produto usa a API do TMDB, mas não é endossado ou certificado pelo TMDB.</p>
        </div>
        <div class="footer-links">
            <a href="TelaPrincipal.php">Ver filmes</a>
            <a href="#">Sobre</a>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
