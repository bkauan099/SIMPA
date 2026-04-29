<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'pagina-inicial';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPA ALUNO - UEMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/aluno-page.css">
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="wrapper">

    <!-- SIDEBAR: começa minimizada (só ícones) -->
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
            <li><a href="?page=pagina-inicial" class="<?= $page=='pagina-inicial'?'active':'' ?>" title="Página Inicial">
                <i class="bi bi-house-door"></i><span class="nav-label">Página Inicial</span></a></li>
            <li><a href="?page=gerenciar-projetos" class="<?= $page=='gerenciar-projetos'?'active':'' ?>" title="Gerenciar Projetos">
                <i class="bi bi-folder"></i><span class="nav-label">Gerenciar Projetos</span></a></li>
            <li><a href="?page=participacoes" class="<?= $page=='participacoes'?'active':'' ?>" title="Minhas Participações">
                <i class="bi bi-diagram-3"></i><span class="nav-label">Minhas Participações</span></a></li>
            <li><a href="?page=tarefas" class="<?= $page=='tarefas'?'active':'' ?>" title="Minhas Tarefas">
                <i class="bi bi-check2-square"></i><span class="nav-label">Minhas Tarefas</span></a></li>
            <li><a href="?page=cronograma" class="<?= $page=='cronograma'?'active':'' ?>" title="Cronograma">
                <i class="bi bi-calendar-event"></i><span class="nav-label">Cronograma</span></a></li>
            <li><a href="?page=seletivos" class="<?= $page=='seletivos'?'active':'' ?>" title="Seletivos">
                <i class="bi bi-megaphone"></i>
                <span class="badge-icon">3</span>
                <span class="nav-label">Seletivos</span>
                <span class="badge bg-danger ms-auto badge-text" style="font-size:0.65rem;">3</span>
            </a></li>
            <li><a href="?page=documentos" class="<?= $page=='documentos'?'active':'' ?>" title="Documentos">
                <i class="bi bi-file-earmark-text"></i><span class="nav-label">Documentos</span></a></li>
            <li><a href="?page=certificados" class="<?= $page=='certificados'?'active':'' ?>" title="Certificados">
                <i class="bi bi-award"></i><span class="nav-label">Certificados</span></a></li>
            <li class="sidebar-sair"><a href="#" title="Sair">
                <i class="bi bi-box-arrow-left"></i><span class="nav-label">Sair</span></a></li>
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

    <!-- NOTIFICAÇÕES -->
    <div class="position-relative">
        <button id="btnNotificacoes" class="btn text-white position-relative">
            <i class="bi bi-bell fs-5"></i>
            <span id="contadorNotificacao"
      class="badge bg-danger"
      style="
        position:absolute;
        top:2px;
        right:2px;
        font-size:0.6rem;
        padding:3px 6px;
        border-radius:10px;
      ">
    2
</span>
        </button>

        <div id="dropdownNotificacoes" class="dropdown-notificacoes">

            <div class="dropdown-header d-flex justify-content-between">
                <span>Notificações</span>
                <button id="lerTodas" class="btn btn-sm btn-light">Ler todas</button>
            </div>

            <div class="notificacao-item nao-lida">
                <p>Seu projeto foi aprovado pela coordenação.</p>
                <button class="marcar-lida">Lida</button>
                <button class="marcar-nao-lida">Não lida</button>
            </div>

            <div class="notificacao-item nao-lida">
                <p>Nova tarefa adicionada ao projeto.</p>
                <button class="marcar-lida">Lida</button>
                <button class="marcar-nao-lida">Não lida</button>
            </div>

            <div class="notificacao-item nao-lida">
                <p>Atualização no cronograma acadêmico.</p>
                <button class="marcar-lida">Lida</button>
                <button class="marcar-nao-lida">Não lida</button>
            </div>

            <div class="notificacao-item nao-lida">
                <p>Seu perfil foi atualizado com sucesso.</p>
                <button class="marcar-lida">Lida</button>
                <button class="marcar-nao-lida">Não lida</button>
            </div>

            <div class="notificacao-item nao-lida">
                <p>Mensagem do suporte do sistema.</p>
                <button class="marcar-lida">Lida</button>
                <button class="marcar-nao-lida">Não lida</button>
            </div>

        </div>
    </div>

    <!-- PERFIL -->
    <div class="position-relative">
        <button id="btnPerfil" class="btn text-white d-flex align-items-center gap-2">
            <img src="https://ui-avatars.com/api/?name=João&background=random"
                 class="rounded-circle" width="34">
            <span class="fw-medium d-none d-sm-inline">
                João <i class="bi bi-chevron-down small"></i>
            </span>
        </button>

        <div id="dropdownPerfil" class="dropdown-perfil">
            <button>Perfil</button>
            <button>Configurações</button>
        </div>
    </div>

</div>
        </header>

        <div class="dashboard-container">
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
<div id="modalPerfil" class="modal-perfil">
    <div class="modal-content">
        <h5>Perfil do Usuário</h5>
        <p><strong>Nome:</strong> João</p>
        <p><strong>Email:</strong> joao@email.com</p>

        <button class="btn btn-primary">Editar Perfil</button>
        <button id="fecharModal" class="btn btn-light mt-2">Fechar</button>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/topbar.js"></script>
</body>
</body>
</html>
