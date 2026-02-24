<?php

session_start();

require_once 'api_tmdb.php';

$lancamentos = buscarFilmesLancamentos();
$recomendados = buscarFilmesPopulares();
$popularesFilmix = buscarTodosFilmesPopulares(5);
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

<body>

    <style>
        body {
            background-color: #0a0a0a;
            color: #ffffff;
        }

        .header {
            padding: 20px 40px;
            background-color: #1a1a1a;
        }

        .logo-placeholder {
            width: 120px;
            height: 60px;
            background-color: #2a2a2a;
            border: 2px dashed #555;
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
    </style>

    <header class="header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <div class="logo-placeholder logo-pequena">
                    <a href="TelaPrincipal.php" style="text-decoration: none; color: #999; font-size: 12px;">Logo</a>
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
                            <i class="bi bi-person" style="font-size:1.5rem; color:#fff;"></i>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end" style="background-color:#222;">
                            <li><a class="dropdown-item text-white" href="#">Perfil</a></li>
                            <li>
                                <form action="logout.php" method="POST">
                                    <button type="submit" class="dropdown-item text-white">Desconectar</button>
                                </form>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
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




        <div class="section-title">Recomendados para você</div>

        <div class="recomendados-container">
            <div class="carousel-wrapper">
                <button class="carousel-btn" onclick="scrollCarouselRecomendados('left')">‹</button>
                <div class="movies-grid" id="gridRecomendados">
                    <?php
                    if (isset($recomendados['results']) && !empty($recomendados['results'])) {
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
                <button class="carousel-btn" onclick="scrollCarouselRecomendados('right')">›</button>
            </div>
        </div>

        <div class="section-title">Populares do FILMIX</div>

        <div class="populares-container">
            <div class="populares-grid" id="gridPopulares">
                <?php
                if (!empty($popularesFilmix)) {
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
        }

        function scrollCarouselRecomendados(direction) {
            const carousel = document.getElementById('gridRecomendados');
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
        }
    </script>
</body>

</html>