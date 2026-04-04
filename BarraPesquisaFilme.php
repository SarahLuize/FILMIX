<?php
session_start();
require_once 'api_tmdb.php';

$termoPesquisa = isset($_POST['s']) ? trim($_POST['s']) : (isset($_GET['s']) ? trim($_GET['s']) : '');
$pagina = isset($_GET['page']) ? intval($_GET['page']) : 1;
$resultados = null;
$totalResultados = 0;
$totalPaginas = 0;

if (!empty($termoPesquisa)) {
    $resultados = buscarFilmesPorNome($termoPesquisa, $pagina);
    if (isset($resultados['results'])) {
        $totalResultados = $resultados['total_results'] ?? 0;
        $totalPaginas = $resultados['total_pages'] ?? 0;
    }
} else if (isset($_GET['s']) && !empty($_GET['s'])) {
    $termoPesquisa = trim($_GET['s']);
    $resultados = buscarFilmesPorNome($termoPesquisa, $pagina);
    if (isset($resultados['results'])) {
        $totalResultados = $resultados['total_results'] ?? 0;
        $totalPaginas = $resultados['total_pages'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FILMIX | Pesquisa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/principal.css">
</head>

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

        .resultados-container {
            padding: 0 40px 40px;
        }

        .resultados-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 20px;
            padding-bottom: 20px;
        }

        @media (max-width: 1400px) {
            .resultados-grid {
                grid-template-columns: repeat(5, 1fr);
            }
        }

        @media (max-width: 1200px) {
            .resultados-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 992px) {
            .resultados-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .resultados-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .movie-card {
            min-width: auto;
            width: 100%;
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

        .resultados-info {
            margin: 20px 40px;
            color: #ccc;
            font-size: 16px;
        }

        .paginacao {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin: 30px 0;
            padding: 0 40px;
        }

        .paginacao a,
        .paginacao span {
            padding: 8px 15px;
            background-color: #2a2a2a;
            border: 1px solid #444;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .paginacao a:hover {
            background-color: #3a3a3a;
        }

        .paginacao .pagina-atual {
            background-color: #555;
        }

        .sem-resultados {
            text-align: center;
            padding: 60px 20px;
            color: #999;
            font-size: 18px;
        }

        .voltar-inicio {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #2a2a2a;
            border: 1px solid #444;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }

        .voltar-inicio:hover {
            background-color: #3a3a3a;
            color: #fff;
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
        <?php if (!empty($termoPesquisa)): ?>
            <div class="section-title">Resultados da Pesquisa: "<?php echo htmlspecialchars($termoPesquisa); ?>"</div>

            <?php if (isset($resultados['results']) && !empty($resultados['results'])): ?>
                <div class="resultados-info">
                    Encontrados <?php echo number_format($totalResultados, 0, ',', '.'); ?> resultado(s)
                </div>

                <div class="resultados-container">
                    <div class="resultados-grid">
                        <?php
                        foreach ($resultados['results'] as $filme) {
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
                </div>

                <?php if ($totalPaginas > 1): ?>
                    <div class="paginacao">
                        <?php if ($pagina > 1): ?>
                            <a href="?s=<?php echo urlencode($termoPesquisa); ?>&page=<?php echo ($pagina - 1); ?>">‹ Anterior</a>
                        <?php endif; ?>

                        <span class="pagina-atual">Página <?php echo $pagina; ?> de <?php echo $totalPaginas; ?></span>

                        <?php if ($pagina < $totalPaginas): ?>
                            <a href="?s=<?php echo urlencode($termoPesquisa); ?>&page=<?php echo ($pagina + 1); ?>">Próxima ›</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="sem-resultados">
                    <p>Nenhum filme encontrado para "<?php echo htmlspecialchars($termoPesquisa); ?>"</p>
                    <a href="TelaPrincipal.php" class="voltar-inicio">Voltar ao Início</a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="sem-resultados">
                <p>Digite um termo de pesquisa para buscar filmes</p>
                <a href="TelaPrincipal.php" class="voltar-inicio">Voltar ao Início</a>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>