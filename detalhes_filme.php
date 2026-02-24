<?php
require_once 'api_tmdb.php';

$filmeId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($filmeId <= 0) {
    header('Location: TelaPrincipal.php');
    exit;
}

$filme = buscarFilmePorId($filmeId);

if (isset($filme['erro'])) {
    header('Location: TelaPrincipal.php');
    exit;
}

$urlPoster = obterUrlImagem($filme['poster_path']);
$titulo = htmlspecialchars($filme['title']);
$sinopse = htmlspecialchars($filme['overview']);
$classificacao = obterClassificacaoFilme($filme);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FILMIX | <?php echo $titulo; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/detalhes.css">
    
</head>
<body>
    <header class="header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <div class="logo-placeholder logo-pequena">
                    <span style="color: #999; font-size: 12px;">Logo</span>
                </div>

                <div class="search-container">
                    <form action="BarraPesquisaFilme.php" method="post" class="d-flex w-100">
                        <input type="search" name="s" id="PesquisaFilme" class="search-input" placeholder="Pesquise seu filme">
                        <button type="submit" class="search-btn">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>

                <div class="nav-links">
                    <a href="#">Assistir mais Tarde</a>
                    <a href="#">Favoritos</a>
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
        <div class="filme-container">
            <div class="poster-container">
                <div class="poster-placeholder">
                    <?php if (!empty($urlPoster)): ?>
                        <img src="<?php echo $urlPoster; ?>" alt="<?php echo $titulo; ?>"
                             onerror="this.style.display='none'; this.parentElement.innerHTML='<span style=\'color: #999;\'>Sem imagem</span>'">
                    <?php else: ?>
                        <span style="color: #999;">Sem imagem</span>
                    <?php endif; ?>
                </div>
                <div class="poster-actions">
                    <div class="poster-action-icon">
                        <i class="bi bi-star"></i>
                    </div>
                    <div class="poster-action-icon">
                        <i class="bi bi-clock"></i>
                    </div>
                </div>
            </div>

            <div class="filme-info">
                <h1 class="filme-titulo"><?php echo $titulo; ?></h1>
                
                <p class="filme-sinopse">
                    <?php echo !empty($sinopse) ? $sinopse : 'Sinopse não disponível.'; ?>
                </p>

                <div class="classificacao-label">classificação</div>
                <div class="classificacao-badge"><?php echo $classificacao; ?></div>
            </div>
        </div>
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

