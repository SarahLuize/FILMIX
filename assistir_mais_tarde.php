<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php?redirect=' . urlencode('assistir_mais_tarde.php'));
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
        .lista-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 24px;
            color: #333;
        }

        .lista-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 24px;
        }

        .lista-card {
            text-align: center;
        }

        .lista-card a {
            text-decoration: none;
            color: #333;
        }

        .lista-poster {
            width: 100%;
            aspect-ratio: 2/3;
            background: #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #ddd;
        }

        .lista-poster img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .lista-card-titulo {
            margin-top: 10px;
            font-size: 14px;
            font-weight: 500;
        }

        .lista-vazio {
            color: #666;
            font-size: 16px;
            padding: 40px 0;
        }
    </style>
</head>

<body>


    <style>
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
    </style>

    <body>
        <header class="header">
            <nav class="navbar navbar-expand-lg navbar-light">
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
                                <input type="search" name="s" class="search-input" placeholder="Pesquise seu filme...">
                                <button type="submit" class="search-btn">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>

                        <ul class="navbar-nav ms-auto align-items-left">
                            <li class="nav-item">
                                <a class="nav-link px-3 text-black" href="assistir_mais_tarde.php">Assistir mais Tarde</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 text-black" href="favoritos.php">Favoritos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 text-black" href="#">Gêneros</a>
                            </li>

                            <li class="nav-item dropdown ms-lg-2">
                                <a class="nav-link dropdown-toggle" href="#" id="userMenu" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle" style="font-size: 1.5rem; color: #000;"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow">
                                    <li>
                                        <span class="dropdown-item-text text-white px-3 py-2 d-block">
                                            <?php
                                            echo isset($_SESSION['nome_usuario'])
                                                ? htmlspecialchars($_SESSION['nome_usuario'], ENT_QUOTES, 'UTF-8') : 'Visitante';
                                            ?>
                                        </span>
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