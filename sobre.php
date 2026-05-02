<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre o SIMPA - UEMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/adm-page.css">
</head>
<body>


    <div id="content" style="margin-left: 0; width: 100%;">
    <header class="navbar-custom d-flex justify-content-between align-items-center px-4 py-2">
        <div class="topbar-left">
                <img src="assets/img/uema-logo.png"      alt="UEMA"    class="logo-uema-top">
                <div class="logo-sep"></div>
                <img src="assets/img/proexae-branco-semfundo.png" alt="ProExae" class="logo-proexae-top">
            </div>
        <div class="text-white d-flex align-items-center gap-3">
            <a href="index.php" class="text-white text-decoration-none fw-bold">
                <i class=""></i> Pagina Inicial
            </a>
        </div>
    </header>

        <div class="container p-5">
            <div class="row justify-content-center text-center mt-5">
                <div class="col-md-8">
                    <h2 class="fw-bold" style="color: #004085;">SOBRE O SIMPA</h2>
                    <p class="text-center mt-3">O <strong>SIMPA</strong> (Sistema Integrado de Monitoramento de Projetos Acadêmicos) é uma plataforma institucional desenvolvida para apoiar a gestão, o acompanhamento
                        e a avaliação de projetos acadêmicos da Universidade Estadual do Maranhão (UEMA), no
                        âmbito da Pró-Reitoria de Extensão e Assuntos Estudantis (PROEXAE).
                        O sistema tem como objetivo central organizar, integrar e dar transparência às informações
                        relacionadas a projetos de extensão e iniciativas acadêmicas, permitindo que estudantes,
                        professores orientadores e gestores acompanhem, de forma padronizada, todas as etapas de
                        execução das atividades.
                        Com interface alinhada à identidade visual institucional da UEMA e foco em usabilidade, o
                        SIMPA contribui para a eficiência da gestão institucional, o fortalecimento das ações de
                        extensão e a tomada de decisões baseadas em dados.
                        Desenvolvido com as tecnologias PHP, HTML, CSS e JavaScript, utilizando o framework
                        Bootstrap e ambiente local XAMPP com servidor Apache.</p>
                </div>
               
            </div>

            <hr>

            <h4 class="fw-bold text-center mb-5 mt-5" style="color: #004085;">EQUIPE DE RESIDENTES TIC 16</h4>
            <p class="fw-bold text-center mb-5 mt-5" style="color: #004085;">Programa de Residência em TIC16 Brisa e Softex Maranhão - 2ª Turma (2026)</p>
            
            <div class="row g-4 text-center justify-content-center">
                <?php
                // Definição dos nomes e cargos para as 5 fotos
                $residentes = [
                    ['nome' => 'José Kauã', 'cargo' => 'Fullstack Developer', 'foto' => 'ft_01.png'],
                    ['nome' => 'Bruno Kauan', 'cargo' => 'Fullstack Developer', 'foto' => 'ft_02.png'],
                    ['nome' => 'Augusto Nicácio', 'cargo' => 'Front-end Developer', 'foto' => 'ft_03.png'],
                    ['nome' => 'Aian', 'cargo' => 'Back-end Developer', 'foto' => 'ft_04.png'],
                    ['nome' => 'André Nunes', 'cargo' => 'UI/UX Designer', 'foto' => 'ft_05.png'],
                    ['nome' => 'Carlos Ronyhelton', 'cargo' => '<strong>Professor</strong>', 'foto' => 'ft_05.png']
                ];

                foreach ($residentes as $r): ?>
                <div class="col-md-2 col-sm-4 col-6 d-flex align-items-stretch">
                    <div class="w-100 d-flex flex-column">
                        <div class="team-photo-container shadow-sm mb-3" style="width: 100%; aspect-ratio: 1/1; overflow: hidden; border-radius: 50%; background-color: #eee;">
                            <img src="assets/img/<?php echo $r['foto']; ?>" 
                                 alt="<?php echo $r['nome']; ?>" 
                                 style="width: 100%; height: 100%; object-fit: cover;"
                                 onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($r['nome']); ?>&background=004085&color=fff'">
                        </div>
                        <div class="mt-auto">
                            <h6 class="fw-bold mb-0" style="font-size: 0.9rem;"><?php echo $r['nome']; ?></h6>
                            <small class="text-muted" style="font-size: 0.8rem;"><?php echo $r['cargo']; ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        

