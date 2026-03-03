<?php
session_start();
require_once 'api_tmdb.php';
require_once 'db_funcoes.php';

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

$estaFavorito = false;
$estaAssistirMaisTarde = false;
if (isset($_SESSION['id_usuario'])) {
    $estaFavorito = ehFavorito((int) $_SESSION['id_usuario'], $filmeId);
    $estaAssistirMaisTarde = ehAssistirMaisTarde((int) $_SESSION['id_usuario'], $filmeId);
}
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
                    <a href="TelaPrincipal.php"><img src="img/FILMIX-logo.png" alt="FILMIX" class="logo-img" style="max-height: 130px; max-width: 200px;"></a>
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
                    <button type="button" class="poster-action-icon poster-action-btn js-favorito" data-id-tmdb="<?php echo $filmeId; ?>" title="<?php echo $estaFavorito ? 'Remover dos Favoritos' : 'Adicionar aos Favoritos'; ?>" aria-label="Favoritar">
                        <i class="bi bi-star<?php echo $estaFavorito ? '-fill' : ''; ?>"></i>
                    </button>
                    <button type="button" class="poster-action-icon poster-action-btn js-assistir-mais-tarde" data-id-tmdb="<?php echo $filmeId; ?>" title="<?php echo $estaAssistirMaisTarde ? 'Remover de Assistir mais Tarde' : 'Adicionar a Assistir mais Tarde'; ?>" aria-label="Assistir mais Tarde">
                        <i class="bi bi-clock<?php echo $estaAssistirMaisTarde ? '-fill' : ''; ?>"></i>
                    </button>
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
    <script>
    (function() {
        var logado = <?php echo isset($_SESSION['id_usuario']) ? 'true' : 'false'; ?>;
        var btnFav = document.querySelector('.js-favorito');
        if (btnFav && logado) {
            var icon = btnFav.querySelector('i');
            btnFav.addEventListener('click', function() {
                var idTmdb = this.getAttribute('data-id-tmdb');
                var fd = new FormData();
                fd.append('id_tmdb', idTmdb);
                fd.append('acao', icon.classList.contains('bi-star-fill') ? 'remover' : 'adicionar');
                fetch('api_favorito.php', { method: 'POST', body: fd }).then(function(r) { return r.json(); }).then(function(res) {
                    if (res.sucesso) {
                        icon.classList.toggle('bi-star-fill', res.favorito);
                        icon.classList.toggle('bi-star', !res.favorito);
                        btnFav.setAttribute('title', res.favorito ? 'Remover dos Favoritos' : 'Adicionar aos Favoritos');
                    }
                });
            });
        }
        var btnClock = document.querySelector('.js-assistir-mais-tarde');
        if (btnClock && logado) {
            var iconClock = btnClock.querySelector('i');
            btnClock.addEventListener('click', function() {
                var idTmdb = this.getAttribute('data-id-tmdb');
                var fd = new FormData();
                fd.append('id_tmdb', idTmdb);
                fd.append('acao', iconClock.classList.contains('bi-clock-fill') ? 'remover' : 'adicionar');
                fetch('api_assistir_mais_tarde.php', { method: 'POST', body: fd }).then(function(r) { return r.json(); }).then(function(res) {
                    if (res.sucesso) {
                        iconClock.classList.toggle('bi-clock-fill', res.assistirMaisTarde);
                        iconClock.classList.toggle('bi-clock', !res.assistirMaisTarde);
                        btnClock.setAttribute('title', res.assistirMaisTarde ? 'Remover de Assistir mais Tarde' : 'Adicionar a Assistir mais Tarde');
                    }
                });
            });
        }
    })();
    </script>
</body>
</html>

