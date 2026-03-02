<?php
// Página institucional SIMPA
?>
<!DOCTYPE html>
<html lang="pt-br">
<header> 
    <div class="header-container text-center py-3">
        <img src="assets/img/uema-logo.png" alt="UEMA" class="logo-uema">
        <h1 class="titulo-sistema">SIMPA</h1>
        <h1>© SIMPA - Sistema Integrado de Monitoramento de Projetos Acadêmicos</h1>
    </div>

<header> 

<head>
    <link rel="stylesheet" href="assets/css/sobre.css">
<script src="assets/js/sobre.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Fonte Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="sobre/assets/css/sobre.css">
</head>
<body>

<!-- HEADER -->

<div class="container py-5">

    <!-- TÍTULO -->
    <div class="text-center mb-4">
        <h2 class="secao-titulo">SOBRE O SIMPA</h2>
    </div>
    <!-- TEXTO -->
    <div class="texto-sobre">
        <p>
        O SIMPA é uma plataforma institucional desenvolvida para apoiar a gestão, o acompanhamento 
        e a avaliação de projetos acadêmicos da Universidade Estadual do Maranhão (UEMA), no âmbito 
        da Pró-Reitoria de Extensão e Assuntos Estudantis (PROEXAE).
        </p>

        <p>
        O sistema tem como objetivo central organizar, integrar e dar transparência às informações relacionadas
        a projetos de extensão e iniciativas acadêmicas, permitindo que estudantes, professores orientadores e 
        gestores acompanhem, de forma padronizada, todas as etapas de execução das atividades.
        </p>
        
        <p>
        Com interface alinhada à identidade visual institucional da UEMA e foco em usabilidade, o SIMPA contribui para a
        eficiência da gestão institucional, o fortalecimento das ações de extensão e a tomada de decisões baseadas em dados.
        </p>

        <p>
        Desenvolvido com as tecnologias PHP, HTML, CSS e JavaScript, utilizando o framework Bootstrap e ambiente
        local XAMPP com servidor Apache.
        </p>
    </div>

    <!-- RESIDENTES -->
    <div class="text-center mt-5 mb-4">
        <h5 class="secao-titulo">RESIDENTES - RESIDÊNCIA TIC</h5>
    </div>

    <div class="row justify-content-center residentes">

        <?php
        $residentes = ["Front-end", "Fullstack", "Fullstack", "Back-end", "Front-end"];

        foreach ($residentes as $funcao) {
            echo '
            <div class="col-md-2 col-6 text-center residente-card">
                <div class="foto-placeholder">FOTO</div>
                <p class="funcao">'.$funcao.'</p>
                <div class="social">
                    <a href="#"><i class="bi bi-linkedin"></i></a>
                    <a href="#"><i class="bi bi-github"></i></a>
                </div>
            </div>
            ';
        }
        ?>
    </div>

    <!-- LOGOS PARCEIROS -->
    <div class="parceiros text-center mt-5">

        <div class="row justify-content-center align-items-center g-4">
            <div class="col-md-2 col-4">
                <img src="assets/img/uema-logo.png" class="logo-parceiro">
            </div>

            <div class="col-md-2 col-4">
                <img src="assets/img/proexae-logocor.png" class="logo-parceiro">
            </div>

            <div class="col-md-2 col-4">
                <img src="assets/img/cct-logoazul.png" class="logo-parceiro">
            </div>

            <div class="col-md-3 col-6">
                <img src="assets/img/marandu-logo.png" class="logo-parceiro">
            </div>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-md-4 col-6">
                <img src="assets/img/brisa-logo.png" class="logo-parceiro-grande">
            </div>

            <div class="col-md-4 col-6">
                <img src="assets/img/softex-logocor.png" class="logo-parceiro-grande">
            </div>
        </div>

    </div>

</div>

<!-- FOOTER -->
<footer class="footer-simpa">
    <div class="footer-container text-center py-4">
        <img src="assets/img/uema-logo.png" class="footer-logo">

        <div class="footer-text">
            © Todos os direitos reservados Universidade Estadual do Maranhão - UEMA.
            Cidade Universitária Paulo VI - Avenida Lourenço Vieira da Silva, 1000 – São Luís/MA.
            Fone: (98) 2016-8100.
            Pró-Reitoria de Extensão e Assuntos Estudantis - PROEXAE
            Desenvolvido por: Brisa Tecnologia e Softex Maranhão.
        </div>
    </div>
</footer>

<!-- JS -->
<script src="sobre/assets/js/sobre.js"></script>

</body>
</html>