<?php
session_start();

if(!isset($_SESSION["usuario"]) || $_SESSION["tipo"] != "aluno"){
    header("Location: login-page.php");
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'pagina-inicial';

// AJAX: retorna só o fragmento, sem layout
if (!empty($_GET['ajax'])) {
    $allowed_pages = ['pagina-inicial','gerenciar-projetos','participacoes','tarefas','cronograma','seletivos','documentos','certificados'];
    if (in_array($page, $allowed_pages)) {
        include "pages-aluno/{$page}.php";
    } else {
        echo "<div class='alert alert-danger'>Página não encontrada.</div>";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPA - UEMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/aluno-page.css?v=4">
    <script>
        if (localStorage.getItem('sidebarExpanded') === 'true') {
            document.documentElement.classList.add('sidebar-pre-expanded');
        }
    </script>
    <style>
        .sidebar-pre-expanded #sidebar { width: 240px; min-width: 240px; }
        .sidebar-pre-expanded #sidebar .nav-label { display: inline; }
        .sidebar-pre-expanded #sidebar ul li a { justify-content: flex-start; padding: 12px 20px; }
        .sidebar-pre-expanded #sidebar ul li a i { width: 28px; margin-right: 4px; }
        .sidebar-pre-expanded #sidebar .sidebar-brand { opacity: 1; transform: translateX(0); }
    </style>
</head><body>

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
                <a href="?page=gerenciar-projetos" class="<?= $page=='gerenciar-projetos'?'active':'' ?>" title="Gerenciar Projetos">
                    <i class="bi bi-folder"></i>
                    <span class="nav-label">Gerenciar Projetos</span>
                </a>
            </li>
            <li>
                <a href="?page=participacoes" class="<?= $page=='participacoes'?'active':'' ?>" title="Minhas Participações">
                    <i class="bi bi-diagram-3"></i>
                    <span class="nav-label">Minhas Participações</span>
                </a>
            </li>
            <li>
                <a href="?page=tarefas" class="<?= $page=='tarefas'?'active':'' ?>" title="Minhas Tarefas">
                    <i class="bi bi-check2-square"></i>
                    <span class="nav-label">Minhas Tarefas</span>
                </a>
            </li>
            <li>
                <a href="?page=cronograma" class="<?= $page=='cronograma'?'active':'' ?>" title="Cronograma">
                    <i class="bi bi-calendar-event"></i>
                    <span class="nav-label">Cronograma</span>
                </a>
            </li>
            <li>
                <a href="?page=seletivos" class="<?= $page=='seletivos'?'active':'' ?>" title="Seletivos">
                    <i class="bi bi-megaphone"></i>
                    <span class="badge-icon">3</span>
                    <span class="nav-label">Seletivos</span>
                    <span class="badge bg-danger ms-auto badge-text" style="font-size:0.65rem;">3</span>
                </a>
            </li>
            <li>
                <a href="?page=documentos" class="<?= $page=='documentos'?'active':'' ?>" title="Documentos">
                    <i class="bi bi-file-earmark-text"></i>
                    <span class="nav-label">Documentos</span>
                </a>
            </li>
            <li>
                <a href="?page=certificados" class="<?= $page=='certificados'?'active':'' ?>" title="Certificados">
                    <i class="bi bi-award"></i>
                    <span class="nav-label">Certificados</span>
                </a>
            </li>
            <li class="sidebar-sair">
                <a href="logout.php" title="Sair">
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
                <img src="assets/img/logo-uema.png"      alt="UEMA"    class="logo-uema-top">
                <div class="logo-sep"></div>
                <img src="assets/img/proexae-branco-semfundo.png" alt="ProExae" class="logo-proexae-top">
            </div>
            <div class="topbar-right">
                <div class="position-relative">
                    <i class="bi bi-bell fs-5" style="cursor:pointer;"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem;">2</span>
                </div>
                <div class="d-flex align-items-center gap-2" style="cursor:pointer">
                    <img src="https://ui-avatars.com/api/?name=João&background=random" class="rounded-circle" width="34">
                    <span class="fw-medium d-none d-sm-inline">João <i class="bi bi-chevron-down small"></i></span>
                </div>
            </div>
        </header>

        <div class="dashboard-container" id="ajaxContent">
            <?php
            $allowed_pages = ['pagina-inicial','gerenciar-projetos','participacoes','tarefas','cronograma','seletivos','documentos','certificados'];
            if (in_array($page, $allowed_pages)) {
                include "pages-aluno/{$page}.php";
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

// Restaurar estado sem animação
(function() {
    if (!isOverlayMode() && localStorage.getItem('sidebarExpanded') === 'true') {
        sidebar.style.transition = 'none';
        sidebar.classList.add('expanded');
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                sidebar.style.transition = '';
                document.documentElement.classList.remove('sidebar-pre-expanded');
            });
        });
    } else {
        document.documentElement.classList.remove('sidebar-pre-expanded');
    }
})();

function toggleSidebar() {
    if (isOverlayMode()) {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    } else {
        sidebar.classList.toggle('expanded');
        localStorage.setItem('sidebarExpanded', sidebar.classList.contains('expanded'));
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
window.addEventListener('orientationchange', () => {
    setTimeout(() => {
        if (!isOverlayMode()) {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        }
    }, 150);
});
</script>
<script src="assets/js/ajax-nav.js"></script>
</body>
</html>
