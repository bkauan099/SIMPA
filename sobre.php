<?php
// Página institucional SIMPA
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SIMPA - Sobre o Sistema</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Fonte Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- CSS próprio -->
    <link rel="stylesheet" href="css/sobre.css">
</head>
<body>

<div class="container py-5">

    <!-- TÍTULO -->
    <div class="text-center mb-4">
        <h4 class="titulo-simpa">SIMPA - 2026</h4>
        <p class="subtitulo">
            Sistema Integrado de Monitoramento de Projetos Acadêmicos<br>
            © Universidade Estadual do Maranhão - UEMA
        </p>
    </div>

    <!-- TEXTO -->
    <div class="texto-sobre">
        <p>
            O SIMPA é uma plataforma institucional desenvolvida para apoiar a gestão, o acompanhamento e o monitoramento
            de projetos acadêmicos da Universidade Estadual do Maranhão (UEMA), no âmbito da Pró-Reitoria de Extensão e
            Assuntos Estudantis (PROEXAE).
        </p>

        <p>
            O sistema permite o cadastro, atualização e acompanhamento de projetos, vinculando professores orientadores,
            estudantes e demais participantes, além do controle de atividades, documentos e status de execução.
        </p>

        <p>
            Desenvolvido com as tecnologias PHP, HTML, CSS e JavaScript, utilizando o framework Bootstrap e ambiente
            local XAMPP com servidor Apache, o SIMPA foi projetado para garantir responsividade, padronização visual
            institucional e facilidade de uso.
        </p>
    </div>

    <!-- RESIDENTES -->
    <div class="text-center mt-5 mb-4">
        <h5 class="secao-titulo">RESIDENTES DA CAPACITAÇÃO EM RESIDÊNCIA TIC 16</h5>
    </div>

    <div class="row justify-content-center residentes">

        <!-- Card Residente -->
        <?php
        $residentes = [
            "Front-end",
            "Fullstack",
            "Fullstack",
            "Back-end",
            "Front-end"
        ];

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
                <img src="img/uema.png" class="logo-parceiro">
            </div>

            <div class="col-md-2 col-4">
                <img src="img/proexae.png" class="logo-parceiro">
            </div>

            <div class="col-md-2 col-4">
                <img src="img/cct.png" class="logo-parceiro">
            </div>

            <div class="col-md-3 col-6">
                <img src="img/marandu.png" class="logo-parceiro">
            </div>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-md-4 col-6">
                <img src="img/brisa.png" class="logo-parceiro-grande">
            </div>

            <div class="col-md-4 col-6">
                <img src="img/softex.png" class="logo-parceiro-grande">
            </div>
        </div>

    </div>

</div>

<script src="js/sobre.js"></script>
</body>
</html>