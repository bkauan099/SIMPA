<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'pagina-inicial';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPA PROFESSOR - UEMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/professor-page.css">
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="wrapper">

    <!-- SIDEBAR -->
    <nav id="sidebar">
        <div class="sidebar-toggle-wrap">
            <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Menu">
                <i class="bi bi-list"></i>
            </button>
            <div class="sidebar-brand">
                <div>
                    <span class="sidebar-brand-text">SIMPA</span>
                    <span class="sidebar-brand-sub">Sistema Integrado de Monitoramento de Projetos Acadêmicos</span>
                </div>
            </div>
        </div>

        <ul class="list-unstyled components">
            <li>
                <a href="?page=pagina-inicial" class="<?= $page=='pagina-inicial'?'active':'' ?>" title="Página Inicial">
                    <i class="bi bi-house-door"></i>
                    <span class="nav-label">Página Inicial</span>
                </a>
            </li>
            <li>
                <a href="?page=meus-projetos" class="<?= $page=='meus-projetos'?'active':'' ?>" title="Meus Projetos">
                    <i class="bi bi-folder"></i>
                    <span class="nav-label">Meus Projetos</span>
                </a>
            </li>
            <li>
                <a href="?page=alunos" class="<?= $page=='alunos'?'active':'' ?>" title="Meus Alunos">
                    <i class="bi bi-people"></i>
                    <span class="nav-label">Meus Alunos</span>
                </a>
            </li>
            <li>
                <a href="?page=tarefas" class="<?= $page=='tarefas'?'active':'' ?>" title="Tarefas">
                    <i class="bi bi-check2-square"></i>
                    <span class="nav-label">Tarefas</span>
                </a>
            </li>
            <li>
                <a href="?page=cronograma" class="<?= $page=='cronograma'?'active':'' ?>" title="Cronograma">
                    <i class="bi bi-calendar-event"></i>
                    <span class="nav-label">Cronograma</span>
                </a>
            </li>
            <li>
                <a href="?page=documentos" class="<?= $page=='documentos'?'active':'' ?>" title="Documentos">
                    <i class="bi bi-file-earmark-text"></i>
                    <span class="nav-label">Documentos</span>
                </a>
            </li>
            <li>
                <a href="?page=relatorios" class="<?= $page=='relatorios'?'active':'' ?>" title="Relatórios">
                    <i class="bi bi-bar-chart-line"></i>
                    <span class="nav-label">Relatórios</span>
                </a>
            </li>
            <li class="sidebar-sair">
                <a href="#" title="Sair">
                    <i class="bi bi-box-arrow-left"></i>
                    <span class="nav-label">Sair</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- CONTEÚDO -->
    <div id="content">
        <header class="navbar-custom">
            <div class="topbar-left">
                <img src="assets/img/uema-logo.png"      alt="UEMA"    class="logo-uema-top">
                <div class="logo-sep"></div>
                <img src="assets/img/proexae-branco-semfundo.png" alt="ProExae" class="logo-proexae-top">
            </div>
            <div class="topbar-right">
                <div class="position-relative">
                    <i class="bi bi-bell fs-5" style="cursor:pointer;"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem;">2</span>
                </div>
                <div class="d-flex align-items-center gap-2" style="cursor:pointer">
                    <img src="https://ui-avatars.com/api/?name=Professor&background=random" class="rounded-circle" width="34">
                    <span class="fw-medium d-none d-sm-inline">Professor <i class="bi bi-chevron-down small"></i></span>
                </div>
            </div>
        </header>

        <div class="dashboard-container">
            <?php
            $allowed_pages = ['pagina-inicial','meus-projetos','alunos','tarefas','cronograma','documentos','relatorios'];
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
<script>
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');

function isOverlayMode() {
    return window.innerWidth < 768 ||
           (window.innerWidth < 992 && window.innerHeight > window.innerWidth);
}

function toggleSidebar() {
    if (isOverlayMode()) {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    } else {
        sidebar.classList.toggle('expanded');
    }
}
function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
}
window.addEventListener('resize', () => {
    if (!isOverlayMode()) {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    }
});
// Fechar ao mudar orientação
window.addEventListener('orientationchange', () => {
    setTimeout(() => {
        if (!isOverlayMode()) {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        }
    }, 150);
});
</script>
</body>
</html>
