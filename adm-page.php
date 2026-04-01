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
            <li><a href="#" id="menu-usuarios" onclick="carregarPagina('usuarios')" title="Usuários">
                <i class="bi bi-people"></i><span class="nav-label">Usuários</span></a></li>
            <li><a href="#" id="menu-participacoes" onclick="carregarPagina('participacoes')" title="Participações">
                <i class="bi bi-diagram-3"></i><span class="nav-label">Participações</span></a></li>
            <li><a href="#" id="menu-projetos" onclick="carregarPagina('projetos')" title="Projetos">
                <i class="bi bi-folder"></i><span class="nav-label">Projetos</span></a></li>
            <li><a href="#" id="menu-documentos" onclick="carregarPagina('documentos')" title="Documentos">
                <i class="bi bi-file-earmark-text"></i><span class="nav-label">Documentos</span></a></li>
            <li><a href="#" id="menu-visitas" onclick="carregarPagina('visitas')" title="Visitas">
                <i class="bi bi-bar-chart-fill"></i><span class="nav-label">Visitas</span></a></li>
            <li class="sidebar-sair"><a href="#" title="Sair">
                <i class="bi bi-box-arrow-left"></i><span class="nav-label">Sair</span></a></li>
        </ul>
    </nav>

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
                    <img src="https://ui-avatars.com/api/?name=João&background=random" class="rounded-circle" width="34">
                    <span class="fw-medium d-none d-sm-inline">João <i class="bi bi-chevron-down small"></i></span>
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

// --- FUNÇÃO DO SWITCH CASE PARA CARREGAR AS PÁGINAS ---
function carregarPagina(abaSolicitada) {
    // Tira a classe 'active' de todo mundo
    const linksMenu = document.querySelectorAll('#sidebar ul li a');
    linksMenu.forEach(link => link.classList.remove('active'));
    
    // Coloca 'active' só em quem foi clicado
    const menuClicado = document.getElementById('menu-' + abaSolicitada);
    if(menuClicado) menuClicado.classList.add('active');

    // Se estiver no celular (overlay mode), fecha o menu ao clicar
    if (isOverlayMode()) {
        closeSidebar();
    }

    // O SWITCH CASE EXIGIDO
    let arquivoParaCarregar = '';
    switch (abaSolicitada) {
        case 'pagina-inicial': 
            arquivoParaCarregar = 'pages-adm/pagina-inicial.php'; 
            break;
        case 'usuarios': 
            arquivoParaCarregar = 'pages-adm/usuarios.php'; 
            break;
        case 'participacoes': 
            arquivoParaCarregar = 'pages-adm/participacoes.php'; 
            break;
        case 'projetos': 
            arquivoParaCarregar = 'pages-adm/projetos.php'; 
            break;
        case 'documentos': 
            arquivoParaCarregar = 'pages-adm/documentos.php'; 
            break;
        case 'visitas': 
            arquivoParaCarregar = 'pages-adm/visitas.php'; 
            break;
        default: 
            arquivoParaCarregar = 'pages-adm/pagina-inicial.php'; 
            break;
    }

    // Faz a chamada e injeta no HTML (AGORA COM BLOQUEIO DE CACHE!)
    fetch(arquivoParaCarregar, { cache: 'no-store' })
        .then(response => {
            if (!response.ok) throw new Error('Erro na requisição');
            return response.text();
        })
        .then(html => {
            document.getElementById('conteudo-dinamico').innerHTML = html;
        })
        .catch(error => {
            console.error(error);
            document.getElementById('conteudo-dinamico').innerHTML = `<div class='alert alert-danger'>Erro ao carregar a página: ${abaSolicitada}</div>`;
        });
}

// Carrega a página inicial automaticamente ao abrir o sistema
document.addEventListener("DOMContentLoaded", function() {
    carregarPagina('pagina-inicial');
});
</script>
</body>
</html>