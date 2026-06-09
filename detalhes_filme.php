<?php
session_start();
require_once 'api_tmdb.php';
require_once 'db_funcoes.php';

$filmeId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($filmeId <= 0){
    header('Location: TelaPrincipal.php');
    exit;
}

$filme = buscarFilmePorId($filmeId);

if(isset($filme['erro'])){
    header('Location: TelaPrincipal.php');
    exit;
}

$urlPoster = obterUrlImagem($filme['poster_path']);
$titulo = htmlspecialchars($filme['title']);
$sinopse = htmlspecialchars($filme['overview']);
$classificacao = obterClassificacaoFilme($filme);

$idUsuarioLogado = isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : null;
$idadeUsuario = isset($_SESSION['idade_usuario']) ? (int)$_SESSION['idade_usuario'] : 0;

if($idUsuarioLogado > 0 && $idadeUsuario <=0){
    if(function_exists('obterDataNascimentoUsuario')){
    $dataNascimento = obterDataNascimentoUsuario($idUsuarioLogado);

    if(!empty($dataNascimento)){
        $nascimento = new DateTime($dataNascimento);
        $hoje = new DateTime();
        $idadeUsuario = $hoje->diff($nascimento)->y;

        $_SESSION['idade_usuario'] = $idadeUsuario;
        $_SESSION['usuario_data_nascimento'] = $dataNascimento;
    }
 }
}

$filmeEhParaMaiores = ($classificacao === '18' || $classificacao ==='18+' || $classificacao ==='+18' || $classificacao === 'Restrito');

$podeInteragir = true;
$estaFavorito = false;
$estaAssistirMaisTarde = false;

if($idUsuarioLogado){
    $estaFavorito = ehFavorito($idUsuarioLogado, $filmeId);
    $estaAssistirMaisTarde = ehAssistirMaisTarde($idUsuarioLogado, $filmeId);

    if($filmeEhParaMaiores && $idadeUsuario < 18){
        $podeInteragir = false;
    }
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
            border-bottom: #1a1a1a;
        }

        /* Tamanho para telas pequenas */
        .logo-filmix {
            height: 100px;
            width: auto;
            transition: height 0.3s ease;
        }

        /* Tamanho para telas grandes */
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
            border: 1px solid #6C757D;
            color: #fff;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        .footer {
            background-color: #1a1a1a !important;
            border: #1a1a1a;
        }

        .footer-disclaimer {
            color: #ccc !important;
        }

        a{
            color: #ccc !important;
        }

        .navbar {
            width: 100%;
            padding-left: 0;
            padding-right: 0;
        }

        .navbar-collapse {
            width: 100%;
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

        /* RESPONSIVIDADE - Detalhes do Filme */
        @media (max-width: 768px) {

            .main-content {
                padding: 30px 20px;
            }

            .filme-container {
                flex-direction: column;
                align-items: center;
            }

            .poster-placeholder {
                width: 100%;
                max-width: 280px;
                height: 420px;
            }

            .poster-actions {
                justify-content: center;
            }

            .filme-titulo {
                font-size: 26px;
                text-align: center;
            }

            .filme-sinopse {
                font-size: 15px;
            }

            .classificacao-label,
            .classificacao-badge {
                text-align: center;
                display: block;
            }
        }
    </style>
    <header class="header" style="background-color: #1a1a1a;">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">

                <a class="navbar-brand" href="TelaPrincipal.php" title="Voltar para a página principal">
                    <img src="img/FILMIX-logo.png" alt="FILMIX" class="logo-filmix">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navFilmix">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navFilmix">

                    <form action="BarraPesquisaFilme.php" method="post" class="d-flex mx-auto my-2 my-lg-0" style="width: 100%; max-width: 400px;">
                        <div class="input-group">
                            <input type="search" name="s" class="form-control bg-dark text-white border-secondary" placeholder="Pesquise seu filme...">
                            <button type="submit" class="btn search-btn">
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
                            <a class="nav-link px-3 text-white" href="Generos_Filmes.php">Gêneros</a>
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
                <!-- Onde entra os botões com proteção da idade -->
                <div class="poster-actions"> 
                    <?php if($podeInteragir): ?>
                    <button type="button" class="poster-action-icon poster-action-btn js-favorito" data-id-tmdb="<?php echo $filmeId; ?>" title="<?php echo $estaFavorito ? 'Remover dos Favoritos' : 'Adicionar aos Favoritos'; ?>" aria-label="Favoritar">
                        <i class="bi bi-star<?php echo $estaFavorito ? '-fill' : ''; ?>"></i>
                    </button>
                    <button type="button" class="poster-action-icon poster-action-btn js-assistir-mais-tarde" data-id-tmdb="<?php echo $filmeId; ?>" title="<?php echo $estaAssistirMaisTarde ? 'Remover de Assistir mais Tarde' : 'Adicionar a Assistir mais Tarde'; ?>" aria-label="Assistir mais Tarde">
                        <i class="bi bi-clock<?php echo $estaAssistirMaisTarde ? '-fill' : ''; ?>"></i>
                    </button>
                    <?php else: ?>
                        <!-- Mensagem exibida para o usuário menor de idade-->
                            <span class="badge bg-danger p-2" title="Recusos desativados para menores de 18 anos">
                                <i class="bi bi-lock-fill"></i>Interação Restrita (+18)
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="filme-info">
                <h1 class="filme-titulo"><?php echo $titulo; ?></h1>

                <p class="filme-sinopse">
                    <?php echo !empty($sinopse) ? $sinopse : 'Sinopse não disponível. Este filme pode não ter sinopse em português ou ainda não foi cadastrada.'; ?>
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
            // Se não puder interagir por conta da idade, o JS trava como "não logado" para trabar o clique
            var logado = <?php echo (isset($_SESSION['id_usuario']) && $podeInteragir) ? 'true' : 'false'; ?>;
            var btnFav = document.querySelector('.js-favorito');
            var btnClock = document.querySelector('.js-assistir-mais-tarde');

            function getRedirectUrl() {
                return 'login.php?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);
            }

            if (btnFav) {
                var icon = btnFav.querySelector('i');
                if (logado) {
                    btnFav.addEventListener('click', function() {
                        var idTmdb = this.getAttribute('data-id-tmdb');
                        var fd = new FormData();
                        fd.append('id_tmdb', idTmdb);
                        fd.append('acao', icon.classList.contains('bi-star-fill') ? 'remover' : 'adicionar');
                        fetch('api_favorito.php', {
                                method: 'POST',
                                body: fd
                            })
                            .then(function(r) {
                                return r.json();
                            })
                            .then(function(res) {
                                if (res.sucesso) {
                                    icon.classList.toggle('bi-star-fill', res.favorito);
                                    icon.classList.toggle('bi-star', !res.favorito);
                                    btnFav.setAttribute('title', res.favorito ? 'Remover dos Favoritos' : 'Adicionar aos Favoritos');
                                } else if (res.erro === 'Não autenticado') {
                                    window.location.href = getRedirectUrl();
                                }
                            });
                    });
                } else {
                    btnFav.addEventListener('click', function() {
                        window.location.href = getRedirectUrl();
                    });
                }
            }

            if (btnClock) {
                var iconClock = btnClock.querySelector('i');
                if (logado) {
                    btnClock.addEventListener('click', function() {
                        var idTmdb = this.getAttribute('data-id-tmdb');
                        var fd = new FormData();
                        fd.append('id_tmdb', idTmdb);
                        fd.append('acao', iconClock.classList.contains('bi-clock-fill') ? 'remover' : 'adicionar');
                        fetch('api_assistir_mais_tarde.php', {
                                method: 'POST',
                                body: fd
                            })
                            .then(function(r) {
                                return r.json();
                            })
                            .then(function(res) {
                                if (res.sucesso) {
                                    iconClock.classList.toggle('bi-clock-fill', res.assistirMaisTarde);
                                    iconClock.classList.toggle('bi-clock', !res.assistirMaisTarde);
                                    btnClock.setAttribute('title', res.assistirMaisTarde ? 'Remover de Assistir mais Tarde' : 'Adicionar a Assistir mais Tarde');
                                } else if (res.erro === 'Não autenticado') {
                                    window.location.href = getRedirectUrl();
                                }
                            });
                    });
                } else {
                    btnClock.addEventListener('click', function() {
                        window.location.href = getRedirectUrl();
                    });
                }
            }
        })();
    </script>
</body>

</html>