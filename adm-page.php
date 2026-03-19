<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPA ADM - UEMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/adm-page.css">
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="wrapper">

    <!-- SIDEBAR -->
    <nav id="sidebar">
        <div class="sidebar-toggle-wrap">
            <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Expandir menu">
                <i class="bi bi-list"></i>
            </button>
        </div>

        <ul class="list-unstyled components">
            <li><a href="?page=dashboard" class="<?= $page=='dashboard'?'active':'' ?>" title="Dashboard">
                <i class="bi bi-house-door"></i><span class="nav-label">Dashboard</span></a></li>
            <li><a href="?page=usuarios" class="<?= $page=='usuarios'?'active':'' ?>" title="Usuários">
                <i class="bi bi-people"></i><span class="nav-label">Usuários</span></a></li>
            <li><a href="?page=participacoes" class="<?= $page=='participacoes'?'active':'' ?>" title="Participações">
                <i class="bi bi-diagram-3"></i><span class="nav-label">Participações</span></a></li>
            <li><a href="?page=projetos" class="<?= $page=='projetos'?'active':'' ?>" title="Projetos">
                <i class="bi bi-folder"></i><span class="nav-label">Projetos</span></a></li>
            <li><a href="?page=documentos" class="<?= $page=='documentos'?'active':'' ?>" title="Documentos">
                <i class="bi bi-file-earmark-text"></i><span class="nav-label">Documentos</span></a></li>
            <li><a href="?page=visitas" class="<?= $page=='visitas'?'active':'' ?>" title="Visitas">
                <i class="bi bi-bar-chart-fill"></i><span class="nav-label">Visitas</span></a></li>
            <li class="sidebar-sair"><a href="#" title="Sair">
                <i class="bi bi-box-arrow-left"></i><span class="nav-label">Sair</span></a></li>
        </ul>
    </nav>

    <!-- CONTEÚDO -->
    <div id="content">
        <header class="navbar-custom">
            <div class="topbar-left">
                <!-- Hambúrguer no topbar (só aparece no mobile via CSS) -->
                <button class="topbar-toggle" onclick="toggleSidebar()" aria-label="Menu">
                    <i class="bi bi-list"></i>
                </button>
                <img src="assets/img/logo-uema-semfundo.png"      alt="UEMA"    class="logo-uema-top">
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

        <div class="dashboard-container">
            <?php
            $allowed_pages = ['dashboard','usuarios','participacoes','projetos','documentos','visitas'];
            if (in_array($page, $allowed_pages)) {
                include "pages-adm/{$page}.php";
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
const isMobile = () => window.innerWidth < 768;

function toggleSidebar() {
    if (isMobile()) {
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
    if (!isMobile()) {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    }
});
</script>
</body>
</html>
