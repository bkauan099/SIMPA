<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal ProExae - UEMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/adm-page.css">
</head>
<body>

<div id="content">
    <header class="navbar-custom d-flex justify-content-between align-items-center px-4 py-2">
        <div class="topbar-left">
            <img src="assets/img/uema-logo.png" alt="UEMA" class="logo-uema-top">
            <div class="logo-sep"></div>
            <img src="assets/img/proexae-branco-semfundo.png" alt="ProExae" class="logo-proexae-top">
        </div>
        <div class="text-white d-flex align-items-center gap-3">
            <a href="login-page.php" class="text-white text-decoration-none fw-bold">
                <i class="bi bi-person-circle"></i> Acessar Sistema
            </a>
        </div>
    </header>

    <div class="container-fluid p-4">
        <div class="row justify-content-center mb-5">
            <div class="col-12">
                <div id="bannerPrincipal" class="carousel slide shadow-lg rounded overflow-hidden" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#bannerPrincipal" data-bs-slide-to="0" class="active"></button>
                        <button type="button" data-bs-target="#bannerPrincipal" data-bs-slide-to="1"></button>
                        <button type="button" data-bs-target="#bannerPrincipal" data-bs-slide-to="2"></button>
                    </div>

                    <div class="carousel-inner" style="background-color:#f4f7f6;">
                        <div class="carousel-item active" data-bs-interval="5000">
                            <a href="https://www.proexae.uema.br/editais-unabi" target="_blank">
                                <img src="https://www.proexae.uema.br/wp-content/uploads/2026/03/Carrossel-Editais-Abertos-Editais-de-Auxilios-Estudantis-2026-05-scaled.png" class="d-block w-100" style="height: 450px; object-fit: contain; background-color:#f4f7f6;" alt="Editais 2026">
                                
                               
                            </a>
                        </div>
                        <div class="carousel-item" data-bs-interval="5000">
                            <a href="https://www.proexae.uema.br/editais-auxilios/" target="_blank">
                                <img src="https://www.proexae.uema.br/wp-content/uploads/2026/03/banner-site-scaled.png" class="d-block w-100" style="height: 450px; object-fit: contain; background-color:#f4f7f6;" alt="Auxílios Estudantis">
                            </a>
                        </div>
                        <div class="carousel-item" data-bs-interval="5000">
                            <a href="https://eskadauema.com/" target="_blank">
                                <img src="https://www.proexae.uema.br/wp-content/uploads/2023/03/Banner-Eskada-.png" class="d-block w-100" style="height: 450px; object-fit: contain; background-color:#f4f7f6;" alt="Eskada UEMA">
                            </a>
                        </div>
                    </div>

                    <button class="carousel-control-prev" type="button" data-bs-target="#bannerPrincipal" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#bannerPrincipal" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div> 
        </div> 

        <div class="dashboard-container mt-4 px-2">
            <div class="row g-4"> 
                <?php
                $links = [
                    ['url' => 'https://www.proexae.uema.br/coordenadoria-de-assuntos-estudantis/', 'icon' => 'bi-globe', 'txt' => 'Coordenação de Assuntos Estudantis'],
                    ['url' => 'https://www.proexae.uema.br/', 'icon' => 'bi-mortarboard', 'txt' => 'Portal Proexae'],
                    ['url' => 'https://sis.sig.uema.br/sigaa/', 'icon' => 'bi-pc-display', 'txt' => 'Sigaa Uema'],
                    ['url' => 'https://www.proexae.uema.br/coordenacao-de-cultura/', 'icon' => 'bi-palette-fill', 'icon2' => 'fa-solid fa-futbol', 'icon2_style' => 'full', 'txt' => 'Coordenação De Cultura e Desporto'],
                    ['url' => 'https://uemanet.uema.br/', 'icon' => 'bi-laptop', 'txt' => 'Uemanet'],
                    ['url' => 'https://eskadauema.com/', 'icon' => 'bi-book', 'txt' => 'Cursos Online'],
                    ['url' => 'https://sis.sig.uema.br/sigaa/public/extensao/paginaListaPeriodosInscricoesAtividadesPublico.jsf?aba=p-extensao', 'icon' => 'bi-calendar-event', 'txt' => 'Eventos Uema'],
                    ['url' => 'https://www.proexae.uema.br/editais', 'icon' => 'bi-file-earmark-text-fill', 'icon2' => 'bi-megaphone-fill', 'icon2_style' => 'badge', 'txt' => 'Editais Abertos'],
                ];

                foreach ($links as $link): 
                    $icon2Class = !empty($link['icon2']) ? ((strpos($link['icon2'], 'fa-') === 0) ? $link['icon2'] : 'bi ' . $link['icon2']) : '';
                    $icon2Style = $link['icon2_style'] ?? 'badge';
                ?>
                    <div class="col-md-3"> 
                        <a href="<?= $link['url'] ?>" target="_blank" class="text-decoration-none h-100 d-block">
                            <div class="stat-card card-aumentado text-center d-flex flex-column align-items-center justify-content-center p-3">

                                <?php if (!empty($link['icon2']) && $icon2Style === 'full'): ?>
                                    <!-- Ícones no mesmo tamanho, dividindo o mesmo frame -->
                                    <div class="icon-circle bg-light-blue mb-3" style="display:flex; align-items:center; justify-content:center; gap:4px;">
                                        <i class="bi <?= $link['icon'] ?>" style="font-size: 1.3rem; color: #004085;"></i>
                                        <i class="<?= $icon2Class ?>" style="font-size: 1.3rem; color: #004085;"></i>
                                    </div>

                                <?php elseif (!empty($link['icon2'])): ?>
                                    <!-- Ícone 2 sobreposto no canto inferior direito do ícone 1, em menor escala -->
                                    <div class="icon-circle bg-light-blue mb-3" style="position: relative;">
                                        <i class="bi <?= $link['icon'] ?>" style="font-size: 1.5rem; color: #004085;"></i>
                                        <i class="<?= $icon2Class ?>" style="position:absolute; bottom:-4px; right:-8px; font-size:1rem; color:#004085; background:#eef2ff; border-radius:50%; padding:3px; box-shadow:0 0 0 2px #ffffff;"></i>
                                    </div>

                                <?php else: ?>
                                    <div class="icon-circle bg-light-blue mb-3">
                                        <i class="bi <?= $link['icon'] ?>" style="font-size: 1.5rem; color: #004085;"></i>
                                    </div>
                                <?php endif; ?>

                                <h6 class="mb-0 text-dark fw-bold" style="font-size: 0.85rem; line-height: 1.2;"><?= $link['txt'] ?></h6>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold" style="color: #004085; margin: 0;">
                    <i class="bi bi-newspaper me-2"></i>Últimas Notícias ProExae
                </h5>
                <a href="https://www.proexae.uema.br/noticias" target="_blank" class="btn btn-sm btn-outline-primary" style="border-color: #004085; color: #004085;">Ver todas</a>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-noticia">
                        <div class="card-body">
                            <span class="badge mb-2" style="background-color: #004085;">Visita Técnica</span>
                            <h6 class="fw-bold titulo-noticia">
                                <a href="https://www.proexae.uema.br/2026/04/14/alunos-do-curso-de-ciencias-biologicas-da-uema-realizam-visita-ao-restaurante-universitario/" target="_blank" class="text-decoration-none text-dark">
                                    Alunos de Ciências Biológicas realizam visita ao Restaurante Universitário
                                </a>
                            </h6>
                            <p class="small text-muted mb-0">Estudantes da UEMA conhecem de perto a operação e segurança alimentar do RU...</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 pb-3">
                            <small class="text-muted"><i class="bi bi-calendar3"></i> 14 de Abril de 2026</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-noticia">
                        <div class="card-body">
                            <span class="badge mb-2" style="background-color: #004085;">Editais</span>
                            <h6 class="fw-bold titulo-noticia">
                                <a href="https://www.proexae.uema.br/2026/04/01/proexae-lanca-edital-de-inscricoes-para-o-programa-unabi-2026/" target="_blank" class="text-decoration-none text-dark">
                                    Proexae lança edital de inscrições para o Programa UNABI 2026
                                </a>
                            </h6>
                            <p class="small text-muted mb-0">Programa voltado para a terceira idade abre novas vagas para o semestre...</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 pb-3">
                            <small class="text-muted"><i class="bi bi-calendar3"></i> 01 de Abril de 2026</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-noticia">
                        <div class="card-body">
                            <span class="badge mb-2" style="background-color: #004085;">Eventos</span>
                            <h6 class="fw-bold titulo-noticia">
                                <a href="https://www.proexae.uema.br/2026/03/27/ii-jornada-nacional-de-extensao-da-uema-destaca-inovacao-impacto-social-e-integracao-com-os-territorios/" target="_blank" class="text-decoration-none text-dark">
                                    II Jornada Nacional de Extensão destaca inovação e impacto social
                                </a>
                            </h6>
                            <p class="small text-muted mb-0">Evento reforça a integração acadêmica com as demandas dos territórios locais...</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 pb-3">
                            <small class="text-muted"><i class="bi bi-calendar3"></i> 27 de Março de 2026</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'footer.php'; ?>
</body>
</html>