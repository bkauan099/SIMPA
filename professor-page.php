<?php
// página padrão
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Professor - SIMPA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="assets/css/aluno-page.css">

    <style>
        .status-ativo {
            background-color: #d1e7dd;
            color: #0f5132;
            padding: 0.35em 0.65em;
            border-radius: 50rem;
            font-size: 0.85em;
            font-weight: 600;
        }

        .status-inativo {
            background-color: #f8d7da;
            color: #842029;
            padding: 0.35em 0.65em;
            border-radius: 50rem;
            font-size: 0.85em;
            font-weight: 600;
        }
    </style>

</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <img src="assets/img/logo-uema-semfundo.png" class="logo-uema">
        </div>

        <ul class="list-unstyled components">

            <li>
                <a href="?page=dashboard"
                   class="<?= $page == 'dashboard' ? 'active' : '' ?>">
                   <i class="bi bi-house-door"></i> Dashboard
                </a>
            </li>

            <li>
                <a href="?page=usuarios"
                   class="<?= $page == 'usuarios' ? 'active' : '' ?>">
                   <i class="bi bi-people"></i> Usuários
                </a>
            </li>

            <li>
                <a href="?page=participacoes"
                   class="<?= $page == 'participacoes' ? 'active' : '' ?>">
                   <i class="bi bi-diagram-3"></i> Participações
                </a>
            </li>

            <li>
                <a href="?page=projetos"
                   class="<?= $page == 'projetos' ? 'active' : '' ?>">
                   <i class="bi bi-folder"></i> Projetos
                </a>
            </li>

            <li>
                <a href="?page=documentos"
                   class="<?= $page == 'documentos' ? 'active' : '' ?>">
                   <i class="bi bi-file-earmark-text"></i> Documentos
                </a>
            </li>

            <li class="mt-auto">
                <a href="#"><i class="bi bi-box-arrow-left"></i> Sair</a>
            </li>

        </ul>
    </nav>


    <!-- CONTENT -->
    <div id="content">

        <header class="navbar-custom">

            <div class="d-flex align-items-center gap-3">
                <img src="assets/img/proexae-branco-semfundo.png"
                     class="logo-proexae">
            </div>

            <div class="d-flex align-items-center gap-4">

                <div class="position-relative">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                          style="font-size: 0.6rem;">2</span>
                </div>

                <div class="d-flex align-items-center gap-2" style="cursor:pointer">
                    <img src="https://ui-avatars.com/api/?name=Professor&background=random"
                         class="rounded-circle"
                         width="35">
                    <span class="fw-medium">
                        Professor <i class="bi bi-chevron-down small"></i>
                    </span>
                </div>

            </div>

        </header>


        <div class="dashboard-container">

            <?php

            // páginas permitidas do PROFESSOR
            $allowed_pages = [
                'dashboard',
                'usuarios',
                'participacoes',
                'projetos',
                'documentos',
            ];

            if (in_array($page, $allowed_pages)) {

                include "pages-professor/{$page}.php";

            } else {

                echo "<div class='alert alert-danger'>Página não encontrada.</div>";

            }

            ?>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>