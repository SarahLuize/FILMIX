<?php

session_start();

require_once 'api_tmdb.php';
require_once 'db_funcoes.php';

$lancamentos = buscarFilmesLancamentos();

$recomendados = ['results' => []];

if (isset($_SESSION['id_usuario'])) {
    $idsFavoritos = listarIdsFavoritosPorUsuario((int) $_SESSION['id_usuario']);
    if (!empty($idsFavoritos)) {
        $listaRecomendados = montarRecomendadosParaUsuarioPorFavoritos($idsFavoritos, 10);
        if (!empty($listaRecomendados)) {
            $recomendados = ['results' => $listaRecomendados];
        }
    }
}

$popularesPagina = isset($_GET['pop_page']) ? max(1, (int) $_GET['pop_page']) : 1;
$popularesResposta = buscarFilmesPopulares($popularesPagina);
$popularesFilmix = [];
$popularesTotalPaginas = 1;
$popularesPaginaAtual = $popularesPagina;
$popularesErro = null;

if (isset($popularesResposta['erro'])) {
    $popularesErro = $popularesResposta['erro'];
} else {
    $popularesFilmix = $popularesResposta['results'] ?? [];
    $popularesTotalPaginas = max(1, (int) ($popularesResposta['total_pages'] ?? 1));
    $popularesPaginaAtual = max(1, (int) ($popularesResposta['page'] ?? $popularesPagina));
}

/** @param int $p número da página (>= 1) */
$urlPaginaPopulares = function (int $p): string {
    $base = $p <= 1
        ? 'TelaPrincipal.php'
        : 'TelaPrincipal.php?' . http_build_query(['pop_page' => $p]);
    return $base . '#populares-filmix';
};
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FILMIX | Início</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<!-- TelaPrincipal.PHP -->

<body>
    <style>
        body {
            background-color: #0a0a0a;
            color: #ffffff;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            flex-direction: column;
        }

        .header {
            padding: 20px 40px;
            background-color: #1a1a1a;
        }

        .logo-placeholder {
            width: 120px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .search-container {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            max-width: 400px;
        }

        .search-input {
            background-color: #2a2a2a;
            border: 1px solid #444;
            color: #fff;
            padding: 8px 15px;
            border-radius: 4px;
            flex: 1;
        }

        .search-input::placeholder {
            color: #999;
        }

        .search-btn {
            background-color: #2a2a2a;
            border: 1px solid #444;
            color: #fff;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        .navbar {
            width: 100%;
            padding-left: 0;
            padding-right: 0;
        }

        .navbar-collapse {
            width: 100%;
        }

        /* Tamanho para Telas Pequenas (Celular) */
        .logo-filmix {
            height: 100px;
            width: auto;
            transition: height 0.3s ease;
        }

        /* Tamanho para Telas Grandes (Computador - Desktop) */
        @media (min-width: 992px) {
            .logo-filmix {
                height: 150px;
            }
        }

        .nav-links {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
        }

        .nav-links a:hover {
            color: #ccc;
        }

        .user-icon {
            width: 35px;
            height: 35px;
            background-color: #2a2a2a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .section-title {
            font-size: 24px;
            font-weight: bold;
            margin: 40px 0 20px 40px;
            padding: 10px 20px;
            border: 2px solid #fff;
            display: inline-block;
        }

        .lancamentos-container {
            position: relative;
            padding: 0 60px;
            margin-bottom: 50px;
        }

        .carousel-wrapper {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .carousel-btn {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid #fff;
            color: #fff;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 20px;
        }

        .carousel-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .carousel-items {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            scroll-behavior: smooth;
            flex: 1;
            -webkit-mask-image: linear-gradient(to right,
                    transparent 0%,
                    black 8%,
                    black 92%,
                    transparent 100%);
            mask-image: linear-gradient(to right,
                    transparent 0%,
                    black 8%,
                    black 92%,
                    transparent 100%);
            padding-left: calc(50% - 100px);
            padding-right: calc(50% - 100px);
            scroll-padding-left: calc(50% - 100px);
        }

        .carousel-items::-webkit-scrollbar {
            display: none;
        }

        .movie-poster {
            min-width: 200px;
            height: 300px;
            background-color: #2a2a2a;
            border: 2px dashed #555;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s;
            transform: scale(0.9);
            transition: transform 0.35s ease, opacity 0.35s ease;
        }

        .movie-poster:hover {
            transform: scale(1.05);
        }

        a .movie-poster {
            text-decoration: none;
        }

        .movie-poster.featured {
            min-width: 250px;
            height: 350px;
            transform: scale(1.0);
        }

        .movie-poster img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }

        .recomendados-container {
            position: relative;
            padding: 0 60px;
            margin-bottom: 50px;
        }

        .movies-grid {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding-bottom: 10px;
            flex: 1;
            scroll-behavior: smooth;
        }

        .movies-grid::-webkit-scrollbar {
            display: none;
        }

        .movie-card {
            min-width: 180px;
            text-align: center;
        }

        a .movie-card {
            text-decoration: none;
        }

        a .movie-card-title {
            color: #fff;
        }

        .movie-card-image {
            width: 100%;
            height: 250px;
            background-color: #2a2a2a;
            border: 2px dashed #555;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .movie-card-image:hover {
            transform: scale(1.05);
        }

        .movie-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }

        .movie-card-title {
            font-size: 14px;
            color: #fff;
            margin-top: 8px;
        }

        .populares-container {
            padding: 0 40px 40px;
        }

        .populares-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 20px;
            padding-bottom: 20px;
        }

        @media (max-width: 1400px) {
            .populares-grid {
                grid-template-columns: repeat(5, 1fr);
            }
        }

        @media (max-width: 1200px) {
            .populares-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 992px) {
            .populares-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .populares-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .populares-grid .movie-card {
            min-width: auto;
            width: 100%;
        }

        .user-icon {
            position: relative;
        }

        .section-subtitle {
            color: #999;
            font-size: 14px;
            margin: -18px 0 18px 40px;
            padding: 0 20px;
            max-width: 960px;
            line-height: 1.4;
        }

        .populares-pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.25rem;
            margin-top: 1.5rem;
            padding-bottom: 1rem;
            flex-wrap: wrap;
        }

        .populares-pagination .populares-page-info {
            color: #ccc;
            font-size: 14px;
        }

        .populares-pagination .carousel-btn.page-nav {
            width: auto;
            min-width: 44px;
            padding: 8px 18px;
            font-size: 14px;
            text-decoration: none;
        }

        .populares-pagination .carousel-btn.page-nav.is-disabled {
            opacity: 0.35;
            pointer-events: none;
            cursor: default;
        }

        @media (min-width: 500px) and (max-width: 768px) {

            .movie-poster {
                min-width: 160px;
                height: 210px;
                transform: scale(1);
            }

            .movie-poster.featured {
                min-width: 200px;
                height: 240px;
                transform: scale(1);

            }

            .carousel-items,
            .movies-grid {
                justify-content: flex-start;
                scroll-snap-type: x mandatory;
                padding-left: calc(50% - 80px);
                padding-right: calc(50% - 80px);
            }

            .movie-poster {
                scroll-snap-align: center;
            }
        }

        @media (max-width: 480px) {
            .movie-poster {
                min-width: 100px;
                height: 150px;
                transform: scale(1);
            }

            .movie-poster.featured {
                min-width: 120px;
                height: 170px;
                transform: scale(1);
            }

            .carousel-items,
            .movies-grid {
                padding-left: calc(50% - 50px);
                padding-right: calc(50% - 50px);
                scroll-padding-left: calc(50% - 50px);
            }
        }

        @media (min-width: 481px) and (max-width: 768px) {
            .movie-poster {
                min-width: 130px;
                height: 195px;
                transform: scale(1);
            }

            .movie-poster.featured {
                min-width: 155px;
                height: 225px;
                transform: scale(1);
            }

            .carousel-items,
            .movies-grid {
                justify-content: flex-start;
                scroll-snap-type: x mandatory;
                padding-left: calc(50% - 65px);
                padding-right: calc(50% - 65px);
                scroll-padding-left: calc(50% - 65px);
            }

            .movie-poster {
                scroll-snap-align: center;
            }
        }

        @media (min-width: 381px) and (max-width: 480px) {
            .movie-poster {
                min-width: 120px;
                height: 175px;
                transform: scale(1);
            }

            .movie-poster.featured {
                min-width: 140px;
                height: 200px;
                transform: scale(1);
            }

            .carousel-items,
            .movies-grid {
                justify-content: flex-start;
                scroll-snap-type: x mandatory;
                padding-left: calc(50% - 60px);
                padding-right: calc(50% - 60px);
                scroll-padding-left: calc(50% - 60px);
            }

            .movie-poster {
                scroll-snap-align: center;
            }
        }

        @media (max-width: 380px) {
            .movie-poster {
                min-width: 95px;
                height: 140px;
                transform: scale(1);
            }

            .movie-poster.featured {
                min-width: 110px;
                height: 160px;
                transform: scale(1);
            }

            .carousel-items,
            .movies-grid {
                justify-content: flex-start;
                scroll-snap-type: x mandatory;
                padding-left: calc(50% - 47px);
                padding-right: calc(50% - 47px);
                scroll-padding-left: calc(50% - 47px);
            }

            .movie-poster {
                scroll-snap-align: center;
            }
        }
    </style>

    <header class="header" style="background-color: #1a1a1a;">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">

                <a class="navbar-brand" href="TelaPrincipal.php">
                    <img src="img/FILMIX-logo.png" alt="FILMIX" class="logo-filmix">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navFilmix">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navFilmix">

                    <form action="BarraPesquisaFilme.php" method="post" class="d-flex mx-auto my-2 my-lg-0" style="width: 100%; max-width: 400px;">
                        <div class="input-group">
                            <input type="search" name="s" class="form-control bg-dark text-white border-secondary" placeholder="Pesquise seu filme...">
                            <button type="submit" class="search-btn">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>

                    <ul class="navbar-nav ms-auto align-items-start">
                        <li class="nav-item">
                            <a class="nav-link px-3 text-white" href="assistir_mais_tarde.php">Assistir mais Tarde</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 text-white" href="favoritos.php">Favoritos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 text-white" href="#">Gêneros</a>
                        </li>

                        <li class="nav-item dropdown ms-lg-2">
                            <a class="nav-link dropdown-toggle" href="#" id="userMenu" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle" style="font-size: 1.5rem; color: #fff;"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow">
                                <li>
                                    <span class="dropdown-item-text text-white px-3 py-2 d-block">
                                        <?php
                                        echo isset($_SESSION['nome_usuario'])
                                            ? htmlspecialchars($_SESSION['nome_usuario'], ENT_QUOTES, 'UTF-8') : 'Visitante';
                                        ?>
                                    </span>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="logout.php" method="POST" class="m-0 px-3">
                                        <button type="submit" class="btn btn-sm btn-danger w-100">Sair</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>

                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="section-title">Lançamentos</div>

        <div class="lancamentos-container">
            <div class="carousel-wrapper">
                <button class="carousel-btn" onclick="scrollCarousel('left')">‹</button>
                <div class="carousel-items" id="carouselLancamentos">
                    <?php
                    if (isset($lancamentos['results']) && !empty($lancamentos['results'])) {
                        $filmesLancamentos = array_slice($lancamentos['results'], 0, 10);
                        foreach ($filmesLancamentos as $index => $filme) {
                            $classePoster = ($index === 1) ? 'movie-poster featured' : 'movie-poster';
                            //$classePoster = 'movie-poster';
                            $urlImagem = obterUrlImagem($filme['poster_path']);
                            $titulo = htmlspecialchars($filme['title']);
                            $filmeId = intval($filme['id']);
                    ?>
                            <a href="detalhes_filme.php?id=<?php echo $filmeId; ?>" style="text-decoration: none;">
                                <div class="<?php echo $classePoster; ?>">
                                    <?php if (!empty($urlImagem)): ?>
                                        <img src="<?php echo $urlImagem; ?>" alt="<?php echo $titulo; ?>"
                                            onerror="this.style.display='none'; this.parentElement.innerHTML='<span style=\'color: #999;\'>Erro ao carregar</span>'">
                                    <?php else: ?>
                                        <span style="color: #999;">Sem imagem</span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php
                        }
                    } else {
                        ?>
                        <div class="movie-poster">
                            <span style="color: #999;">Nenhum lançamento disponível</span>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <button class="carousel-btn" onclick="scrollCarousel('right')">›</button>
            </div>
        </div>




        <?php if (!empty($recomendados['results'])): ?>
            <div class="section-title">Recomendados para você</div>
            <p class="section-subtitle">Com base nos filmes que você favoritou</p>

            <div class="recomendados-container">
                <div class="carousel-wrapper">
                    <button class="carousel-btn" onclick="scrollCarouselRecomendados('left')">‹</button>
                    <div class="movies-grid" id="gridRecomendados">
                        <?php
                        $filmesRecomendados = array_slice($recomendados['results'], 0, 10);
                        foreach ($filmesRecomendados as $filme) {
                            $urlImagem = obterUrlImagem($filme['poster_path']);
                            $titulo = htmlspecialchars($filme['title']);
                            $filmeId = intval($filme['id']);
                        ?>
                            <a href="detalhes_filme.php?id=<?php echo $filmeId; ?>" style="text-decoration: none;">
                                <div class="movie-card">
                                    <div class="movie-card-image">
                                        <?php if (!empty($urlImagem)): ?>
                                            <img src="<?php echo $urlImagem; ?>" alt="<?php echo $titulo; ?>"
                                                onerror="this.style.display='none'; this.parentElement.innerHTML='<span style=\'color: #999; font-size: 12px;\'>Erro</span>'">
                                        <?php else: ?>
                                            <span style="color: #999; font-size: 12px;">Sem imagem</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="movie-card-title"><?php echo $titulo; ?></div>
                                </div>
                            </a>
                        <?php
                        }
                        ?>
                    </div>
                    <button class="carousel-btn" onclick="scrollCarouselRecomendados('right')">›</button>
                </div>
            </div>
        <?php endif; ?>

        <div class="section-title" id="populares-filmix">Populares do FILMIX</div>

        <div class="populares-container">
            <div class="populares-grid" id="gridPopulares">
                <?php
                if ($popularesErro !== null) {
                ?>
                    <div class="movie-card" style="grid-column: 1 / -1;">
                        <div class="movie-card-image" style="height: auto; min-height: 120px; padding: 1rem;">
                            <span style="color: #999; font-size: 14px;">Não foi possível carregar os populares. Tente novamente.</span>
                        </div>
                    </div>
                    <?php
                } elseif (!empty($popularesFilmix)) {
                    foreach ($popularesFilmix as $filme) {
                        $urlImagem = obterUrlImagem($filme['poster_path']);
                        $titulo = htmlspecialchars($filme['title']);
                        $filmeId = intval($filme['id']);
                    ?>
                        <a href="detalhes_filme.php?id=<?php echo $filmeId; ?>" style="text-decoration: none;">
                            <div class="movie-card">
                                <div class="movie-card-image">
                                    <?php if (!empty($urlImagem)): ?>
                                        <img src="<?php echo $urlImagem; ?>" alt="<?php echo $titulo; ?>"
                                            onerror="this.style.display='none'; this.parentElement.innerHTML='<span style=\'color: #999; font-size: 12px;\'>Erro</span>'">
                                    <?php else: ?>
                                        <span style="color: #999; font-size: 12px;">Sem imagem</span>
                                    <?php endif; ?>
                                </div>
                                <div class="movie-card-title"><?php echo $titulo; ?></div>
                            </div>
                        </a>
                    <?php
                    }
                } else {
                    ?>
                    <div class="movie-card">
                        <div class="movie-card-image">
                            <span style="color: #999; font-size: 12px;">Nenhum filme disponível</span>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>

            <?php if ($popularesErro === null && $popularesTotalPaginas > 1): ?>
                <nav class="populares-pagination" aria-label="Mudar página dos populares">
                    <?php
                    $temAnterior = $popularesPaginaAtual > 1;
                    $temProxima = $popularesPaginaAtual < $popularesTotalPaginas;
                    ?>
                    <?php if ($temAnterior): ?>
                        <a class="carousel-btn page-nav" href="<?php echo htmlspecialchars($urlPaginaPopulares($popularesPaginaAtual - 1)); ?>">Anterior</a>
                    <?php else: ?>
                        <span class="carousel-btn page-nav is-disabled" aria-disabled="true">Anterior</span>
                    <?php endif; ?>

                    <span class="populares-page-info">Página <?php echo (int) $popularesPaginaAtual; ?> de <?php echo (int) $popularesTotalPaginas; ?></span>

                    <?php if ($temProxima): ?>
                        <a class="carousel-btn page-nav" href="<?php echo htmlspecialchars($urlPaginaPopulares($popularesPaginaAtual + 1)); ?>">Próxima</a>
                    <?php else: ?>
                        <span class="carousel-btn page-nav is-disabled" aria-disabled="true">Próxima</span>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function scrollCarousel(direction) {
            const carousel = document.getElementById('carouselLancamentos');
            const scrollAmount = 250;

            if (direction === 'left') {
                carousel.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            } else {
                carousel.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            }
            setTimeout(updateFeatured, 350);
        }

        function scrollCarousel(direction) {
            const carousel = document.getElementById('carouselLancamentos');
            const poster = carousel.querySelector('.movie-poster');
            const scrollAmount = poster ? poster.offsetWidth + 20 : 250; // largura real + gap

            if (direction === 'left') {
                carousel.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            } else {
                carousel.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            }
            setTimeout(updateFeatured, 350);
        }

        function updateFeatured() {
            const carousel = document.getElementById('carouselLancamentos');
            const posters = document.querySelectorAll('#carouselLancamentos .movie-poster');

            const centroCarrossel = carousel.getBoundingClientRect().left + carousel.offsetWidth / 2;

            let maisProximo = null;
            let menorDistancia = Infinity;

            posters.forEach(poster => {
                const centroPoster = poster.getBoundingClientRect().left + poster.offsetWidth / 2;
                const distancia = Math.abs(centroCarrossel - centroPoster);

                if (distancia < menorDistancia) {
                    menorDistancia = distancia;
                    maisProximo = poster;
                }
            });

            posters.forEach(p => p.classList.remove('featured'));
            if (maisProximo) maisProximo.classList.add('featured');
        }

        const carousel = document.getElementById('carouselLancamentos');
        carousel.addEventListener('scroll', updateFeatured);
        updateFeatured();
    </script>
</body>

</html>