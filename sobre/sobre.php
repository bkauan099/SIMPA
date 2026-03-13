<?php
// Página institucional SIMPA
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Fonte Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/sobre.css">

    <title>Sobre o SIMPA</title>
</head>
<body>

<!-- HEADER FIXO -->
<header class="header-simpa">
    <div class="header-wrapper">
        <div class="logo-area">
            <img src="assets/img/uema-logo.png" alt="UEMA" class="logo-uema">
        </div>
        <div class="titulo-area">
            <h1 class="titulo-sistema">SIMPA</h1>
            <h2 class="subtitulo-sistema">
                © Sistema Integrado de Monitoramento de Projetos Acadêmicos
            </h2>
        </div>
        <!-- BOTÃO DE IMPRIMIR -->
        <div class="botao-imprimir" onclick="window.print()">
            <i class="bi bi-printer"></i>
            <span>IMPRIMIR PÁGINA</span>
        </div>
    </div>
</header>

<!-- CONTEÚDO PRINCIPAL ROLÁVEL -->
<main class="conteudo-principal">
    <div class="container py-4">
        <!-- TÍTULO -->
        <div class="text-center mb-4">
            <h2 class="secao-titulo">SOBRE O SIMPA</h2>
        </div>

        <!-- TEXTO INSTITUCIONAL -->
        <div class="texto-sobre">
            <p>O SIMPA é uma plataforma institucional desenvolvida para apoiar a gestão, o acompanhamento e a avaliação de projetos acadêmicos da Universidade Estadual do Maranhão (UEMA), no âmbito da Pró-Reitoria de Extensão e Assuntos Estudantis (PROEXAE).</p>
            <p>O sistema tem como objetivo central organizar, integrar e dar transparência às informações relacionadas a projetos de extensão e iniciativas acadêmicas, permitindo que estudantes, professores orientadores e gestores acompanhem, de forma padronizada, todas as etapas de execução das atividades.</p>
            <p>Com interface alinhada à identidade visual institucional da UEMA e foco em usabilidade, o SIMPA contribui para a eficiência da gestão institucional, o fortalecimento das ações de extensão e a tomada de decisões baseadas em dados.</p>
            <p>Desenvolvido com as tecnologias PHP, HTML, CSS e JavaScript, utilizando o framework Bootstrap e ambiente local XAMPP com servidor Apache.</p>
        </div>

        <!-- RESIDENTES -->
        <div class="text-center mt-5 mb-4">
            <h5 class="secao-titulo">RESIDENTES - RESIDÊNCIA TIC</h5>
            <p class="subtitulo-residentes">Programa de Residência em TIC16 Brisa e Softex Maranhão - 2ª Turma (2026)</p>
        </div>

        <div class="row justify-content-center residentes">
            <!-- RESIDENTE 1 -->
            <div class="col-md-2 col-6 text-center residente-card">
                <div class="foto-placeholder">José Kauã</div>
                <p class="nome-residente">José Kauã</p>
                <p class="funcao mt-2">Fullstack</p>
                <p class="formacao">Formação Acadêmica</p>
                <div class="social">
                    <a href="#" target="_blank" rel="noopener noreferrer"><i class="bi bi-linkedin"></i></a>
                    <a href="https://github.com/Josepkaua" target="_blank" rel="noopener noreferrer"><i class="bi bi-github"></i></a>
                </div>
            </div>
            <!-- RESIDENTE 2 -->
            <div class="col-md-2 col-6 text-center residente-card">
                <div class="foto-placeholder">Bruno Kauan</div>
                <p class="nome-residente">Bruno Kauan</p>
                <p class="funcao mt-2">Fullstack</p>
                <p class="formacao">Formação Acadêmica</p>
                <div class="social">
                    <a href="#" target="_blank" rel="noopener noreferrer"><i class="bi bi-linkedin"></i></a>
                    <a href="https://github.com/bkauan099" target="_blank" rel="noopener noreferrer"><i class="bi bi-github"></i></a>
                </div>
            </div>
            <!-- RESIDENTE 3 -->
            <div class="col-md-2 col-6 text-center residente-card">
                <div class="foto-placeholder">Augusto Nicácio</div>
                <p class="nome-residente">Augusto Nicácio</p>
                <p class="funcao mt-2">Front-end</p>
                <p class="formacao">Formação Acadêmica</p>
                <div class="social">
                    <a href="#" target="_blank" rel="noopener noreferrer"><i class="bi bi-linkedin"></i></a>
                    <a href="https://github.com/AugustoNicacio" target="_blank" rel="noopener noreferrer"><i class="bi bi-github"></i></a>
                </div>
            </div>
            <!-- RESIDENTE 4 -->
            <div class="col-md-2 col-6 text-center residente-card">
                <div class="foto-placeholder">Aian</div>
                <p class="nome-residente">Aian</p>
                <p class="funcao mt-2">Back-End</p>
                <p class="formacao">Formação Acadêmica</p>
                <div class="social">
                    <a href="#" target="_blank" rel="noopener noreferrer"><i class="bi bi-linkedin"></i></a>
                    <a href="#" target="_blank" rel="noopener noreferrer"><i class="bi bi-github"></i></a>
                </div>
            </div>
            <!-- RESIDENTE 5 -->
            <div class="col-md-2 col-6 text-center residente-card">
                <div class="foto-placeholder">
                    <img src="assets/img/residentes/andrenunes.jpeg" alt="André Nunes" class="foto-residente">
                </div>
                <p class="nome-residente">André Nunes</p>
                <p class="funcao mt-2">Front-end</p>
                <p class="formacao">Designer</p>
                <div class="social">
                    <a href="https://www.linkedin.com/in/andrecnr/" target="_blank" rel="noopener noreferrer"><i class="bi bi-linkedin"></i></a>
                    <a href="https://github.com/andreandre-cnr" target="_blank" rel="noopener noreferrer"><i class="bi bi-github"></i></a>
                </div>
            </div>
        </div>

        <!-- LOGOS PARCEIROS -->
        <div class="parceiros text-center mt-5">
            <h6>PARCEIROS</h6>
            <!-- LINHA 1 -->
            <div class="row justify-content-center align-items-center parceiros-linha">
                <div class="col-md-2 col-6">
                    <a href="https://www.uema.br/" target="_blank" rel="noopener noreferrer">
                        <img src="assets/img/uema-logo.png" class="logo-parceiro logo-uema-parceiro" alt="UEMA">
                    </a>
                </div>
                <div class="col-md-2 col-6">
                    <a href="https://www.proexae.uema.br/" target="_blank" rel="noopener noreferrer">
                        <img src="assets/img/proexae-logocor.png" class="logo-parceiro logo-proexae" alt="PROEXAE">
                    </a>
                </div>
                <div class="col-md-2 col-6">
                    <a href="https://www.prog.uema.br/cct/" target="_blank" rel="noopener noreferrer">
                        <img src="assets/img/cct-logoazul.png" class="logo-parceiro logo-cct" alt="CCT-UEMA">
                    </a>
                </div>
                <div class="col-md-2 col-6">
                    <a href="https://marandu.uema.br/" target="_blank" rel="noopener noreferrer">
                        <img src="assets/img/marandu-logo.png" class="logo-parceiro logo-marandu" alt="Agência Marandu">
                    </a>
                </div>
            </div>
            <!-- LINHA 2 -->
            <div class="row justify-content-center align-items-center parceiros-linha">
                <div class="col-md-3 col-6">
                    <a href="https://brisabr.com.br/" target="_blank" rel="noopener noreferrer">
                        <img src="assets/img/brisa-logo.png" class="logo-parceiro-grande logo-brisa" alt="Brisa">
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="https://softex.br/" target="_blank" rel="noopener noreferrer">
                        <img src="assets/img/softex-logocor.png" class="logo-parceiro-grande logo-softex" alt="Softex">
                    </a>
                </div>
            </div>
            <!-- LINHA 3 – ajustada para aproximar as logos MCTI -->
            <div class="row justify-content-center align-items-center parceiros-linha">
                <div class="col-auto">
                    <a href="https://mctifuturo.softex.br/" target="_blank" rel="noopener noreferrer">
                        <img src="assets/img/logo-mcti-futuro.png" class="logo-mcti-futuro" alt="MCTI Futuro">
                    </a>
                </div>
                <div class="col-auto">
                    <a href="https://www.gov.br/mcti/pt-br" target="_blank" rel="noopener noreferrer">
                        <img src="assets/img/mcti-logo.png" class="logo-mcti" alt="MCTI">
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- FOOTER FIXO -->
<footer class="footer-simpa">
    <div class="footer-container">
        <img src="assets/img/uema-logo-rodape.png" alt="UEMA" class="footer-logo">
        <div class="footer-text">
            © Todos os direitos reservados Universidade Estadual do Maranhão - UEMA.<br>
            Cidade Universitária Paulo VI - Avenida Lourenço Vieira da Silva, 1000 – São Luís/MA.<br>
            Fone: (98) 2016-8100.<br>
            Pró-Reitoria de Extensão e Assuntos Estudantis - PROEXAE<br>
            Desenvolvido por: Residentes do Programa de Residência em TIC16 Brisa e Softex Maranhão.
        </div>
    </div>
</footer>

<!-- JS -->
<script src="assets/js/sobre.js"></script>
</body>
</html>