<?php
session_start();

require_once 'conexao/conexao.php';
$id_usuario = $_SESSION['id_usuario'] ?? 3;
$stmt = $pdo->prepare("SELECT nome FROM usuarios WHERE id_usuario = :id");
$stmt->execute([':id' => $id_usuario]);
$nomeUsuario  = $stmt->fetchColumn() ?: 'Usuário';
$primeiroNome = explode(' ', $nomeUsuario)[0];
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
            <li><a href="#" id="menu-pagina-inicial" onclick="carregarPagina('pagina-inicial')" title="Página Inicial">
                <i class="bi bi-house-door"></i><span class="nav-label">Página Inicial</span></a></li>
            <li><a href="#" id="menu-gerenciar-projetos" onclick="carregarPagina('gerenciar-projetos')" title="Gerenciar Projetos">
                <i class="bi bi-folder"></i><span class="nav-label">Gerenciar Projetos</span></a></li>
            <li><a href="#" id="menu-participacoes" onclick="carregarPagina('participacoes')" title="Minhas Participações">
                <i class="bi bi-diagram-3"></i><span class="nav-label">Minhas Participações</span></a></li>
            <li><a href="#" id="menu-tarefas" onclick="carregarPagina('tarefas')" title="Minhas Tarefas">
                <i class="bi bi-check2-square"></i><span class="nav-label">Minhas Tarefas</span></a></li>
            <li><a href="#" id="menu-cronograma" onclick="carregarPagina('cronograma')" title="Cronograma">
                <i class="bi bi-calendar-event"></i><span class="nav-label">Cronograma</span></a></li>
            <li><a href="#" id="menu-seletivos" onclick="carregarPagina('seletivos')" title="Seletivos">
                <i class="bi bi-megaphone"></i>
                <span class="badge-icon">3</span>
                <span class="nav-label">Seletivos</span>
                <span class="badge bg-danger ms-auto badge-text" style="font-size:0.65rem;">3</span>
            </a></li>
            <li><a href="#" id="menu-documentos" onclick="carregarPagina('documentos')" title="Documentos">
                <i class="bi bi-file-earmark-text"></i><span class="nav-label">Documentos</span></a></li>
            <li><a href="#" id="menu-certificados" onclick="carregarPagina('certificados')" title="Certificados">
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
                <div class="position-relative">
                    <i class="bi bi-bell fs-5" style="cursor:pointer;"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem;">2</span>
                </div>
                <div class="d-flex align-items-center gap-2" style="cursor:pointer">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($nomeUsuario) ?>&background=random" class="rounded-circle" width="34">
                    <span class="fw-medium d-none d-sm-inline"><?= htmlspecialchars($primeiroNome) ?> <i class="bi bi-chevron-down small"></i></span>
                </div>
            </div>
        </header>

        <div class="dashboard-container" id="conteudo-dinamico">
            <div class="text-center mt-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
            </div>
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
window.addEventListener('orientationchange', () => {
    setTimeout(() => {
        if (!isOverlayMode()) {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        }
    }, 150);
});

function carregarPagina(abaSolicitada) {
    document.querySelectorAll('#sidebar ul li a').forEach(l => l.classList.remove('active'));
    const menuClicado = document.getElementById('menu-' + abaSolicitada);
    if (menuClicado) menuClicado.classList.add('active');
    if (isOverlayMode()) closeSidebar();

    let arquivo = '';
    switch (abaSolicitada) {
        case 'pagina-inicial':     arquivo = 'pages-aluno/pagina-inicial.php';     break;
        case 'gerenciar-projetos': arquivo = 'pages-aluno/gerenciar-projetos.php'; break;
        case 'participacoes':      arquivo = 'pages-aluno/participacoes.php';       break;
        case 'tarefas':            arquivo = 'pages-aluno/tarefas.php';             break;
        case 'cronograma':         arquivo = 'pages-aluno/cronograma.php';          break;
        case 'seletivos':          arquivo = 'pages-aluno/seletivos.php';           break;
        case 'documentos':         arquivo = 'pages-aluno/documentos.php';          break;
        case 'certificados':       arquivo = 'pages-aluno/certificados.php';        break;
        default:                   arquivo = 'pages-aluno/pagina-inicial.php';      break;
    }

    const container = document.getElementById('conteudo-dinamico');
    container.innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></div>';

    fetch(arquivo, { cache: 'no-store' })
        .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.text(); })
        .then(html => {
            document.querySelectorAll('script[data-dinamico]').forEach(s => s.remove());
            const temp = document.createElement('div');
            temp.innerHTML = html;
            const scripts = [];
            temp.querySelectorAll('script').forEach(s => { scripts.push({ src: s.src, text: s.textContent }); s.remove(); });
            container.innerHTML = temp.innerHTML;
            function runNext(i) {
                if (i >= scripts.length) return;
                const ns = document.createElement('script');
                ns.setAttribute('data-dinamico', '1');
                if (scripts[i].src) {
                    ns.src = scripts[i].src;
                    ns.onload = () => runNext(i + 1);
                    ns.onerror = () => runNext(i + 1);
                    document.body.appendChild(ns);
                } else {
                    ns.textContent = scripts[i].text;
                    document.body.appendChild(ns);
                    runNext(i + 1);
                }
            }
            runNext(0);
        })
        .catch(err => {
            container.innerHTML = `<div class="alert alert-danger m-3">Erro ao carregar a página.<br><small>${err.message}</small></div>`;
        });
}

document.addEventListener('DOMContentLoaded', () => carregarPagina('pagina-inicial'));
</script>
</body>
</html>
