<?php
session_start();

if (!isset($_SESSION["usuario"]) || $_SESSION["tipo"] != "admin") {
    header("Location: login-page.php");
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'pagina-inicial';

require_once 'conexao/conexao.php'; // Verifique se o caminho está certo

// BUSCA OS TIPOS AQUI NO TOPO
try {
    $stmt_tipos = $pdo->query("SELECT id_tipo, nome FROM tipo_projetos ORDER BY nome ASC");
    $tipos = $stmt_tipos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $tipos = [];
}

// AJAX: retorna só o fragmento, sem layout
if (!empty($_GET['ajax'])) {
    $allowed_pages = ['pagina-inicial', 'usuarios', 'participacoes', 'projetos', 'documentos', 'visitas'];
    if (in_array($page, $allowed_pages)) {
        include "pages-adm/{$page}.php";
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
    <link rel="stylesheet" href="assets/css/adm-page.css?v=4">
    <script>
        if (localStorage.getItem('sidebarExpanded') === 'true') {
            document.documentElement.classList.add('sidebar-pre-expanded');
        }
    </script>
    <style>
        .sidebar-pre-expanded #sidebar {
            width: 240px;
            min-width: 240px;
        }

        .sidebar-pre-expanded #sidebar .nav-label {
            display: inline;
        }

        .sidebar-pre-expanded #sidebar ul li a {
            justify-content: flex-start;
            padding: 12px 20px;
        }

        .sidebar-pre-expanded #sidebar ul li a i {
            width: 28px;
            margin-right: 4px;
        }

        .sidebar-pre-expanded #sidebar .sidebar-brand {
            opacity: 1;
            transform: translateX(0);
        }
    </style>
</head>

<body>

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
                    <li>
                        <a href="?page=pagina-inicial" class="<?= $page == 'pagina-inicial' ? 'active' : '' ?>">
                            <i class="bi bi-house-door"></i><span class="nav-label">Página Inicial</span>
                        </a>
                    </li>
                    <li>
                        <a href="?page=usuarios" class="<?= $page == 'usuarios' ? 'active' : '' ?>">
                            <i class="bi bi-people"></i><span class="nav-label">Usuários</span>
                        </a>
                    </li>
                    <li>
                        <a href="?page=participacoes" class="<?= $page == 'participacoes' ? 'active' : '' ?>">
                            <i class="bi bi-diagram-3"></i><span class="nav-label">Participações</span>
                        </a>
                    </li>
                    <li>
                        <a href="?page=projetos" class="<?= $page == 'projetos' ? 'active' : '' ?>">
                            <i class="bi bi-folder"></i><span class="nav-label">Projetos</span>
                        </a>
                    </li>
                    <li>
                        <a href="?page=documentos" class="<?= $page == 'documentos' ? 'active' : '' ?>">
                            <i class="bi bi-file-earmark-text"></i><span class="nav-label">Documentos</span>
                        </a>
                    </li>
                    <li>
                        <a href="?page=visitas" class="<?= $page == 'visitas' ? 'active' : '' ?>">
                            <i class="bi bi-bar-chart-fill"></i><span class="nav-label">Visitas</span>
                        </a>
                    </li>
                    <li class="sidebar-sair">
                        <a href="logout.php">
                            <i class="bi bi-box-arrow-left"></i><span class="nav-label">Sair</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div id="content">
                <header class="navbar-custom">
                    <div class="topbar-left">
                        <button class="topbar-toggle" onclick="toggleSidebar()">
                            <i class="bi bi-list"></i>
                        </button>
                        <img src="assets/img/uema-logo.png" alt="UEMA" class="logo-uema-top">
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
                    <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
                        <div id="alertaSucesso" class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" style="border-left: 5px solid #16a34a;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                <div>
                                    <strong>Projeto cadastrado com sucesso!</strong>
                                </div>
                            </div>
                            <button type="button" class="btn-close" onclick="fecharAvisoELimparURL()" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php
                    $allowed_pages = ['pagina-inicial', 'usuarios', 'participacoes', 'projetos', 'documentos', 'visitas'];
                    if (in_array($page, $allowed_pages)) {
                        $file = "pages-adm/{$page}.php";
                        if (file_exists($file)) {
                            include $file;
                        } else {
                            echo "<div class='alert alert-danger'>Erro ao carregar conteúdo.</div>";
                        }
                    } else {
                        include "pages-adm/pagina-inicial.php";
                    }
                    ?>
                </div>
            </div>
        </div>

        <div id="modalProjeto" class="modal-container">
            <div class="modal-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="m-0" style="color: var(--azul-uema); font-weight: 700;">Novo Projeto</h4>
                    <button type="button" onclick="fecharModal()" class="btn-close"></button>
                </div>

                <form action="controllers/controller-adm/cadastrar-projeto.php" method="POST">
                    <input type="hidden" name="pagina_origem" id="input_pagina_origem" value="pagina-inicial">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título do Projeto</label>
                        <input type="text" name="titulo" class="form-control" required placeholder="Ex: Monitoramento de Solo">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Área</label>
                            <input type="text" name="area" class="form-control" placeholder="Ex: Agronomia">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo</label>
                            <select name="id_tipo" class="form-select" required>
                                <option value="" disabled selected>Selecione o tipo...</option>
                                <?php foreach ($tipos as $tipo): ?>
                                    <option value="<?= $tipo['id_tipo'] ?>"><?= htmlspecialchars($tipo['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Data de Início</label>
                            <input type="date" name="data_inicio" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Data de Término</label>
                            <input type="date" name="data_fim" class="form-control">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" onclick="fecharModal()" class="btn btn-light border">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Cadastrar Projeto</button>
                    </div>
                </form>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="assets/js/ajax-nav.js"></script>

        <script>
            // Função unificada para limpar a URL mantendo a aba atual
            function fecharAvisoELimparURL() {
                const url = new URL(window.location.href);
                url.searchParams.delete('sucesso');
                window.location.href = url.toString();
            }

            function abrirModal() {
                const modal = document.getElementById('modalProjeto');
                if (modal) {
                    // Esta linha abaixo resolve o problema! 
                    // Ela pega a página atual da URL (ex: ?page=usuarios) e coloca no formulário
                    const urlParams = new URLSearchParams(window.location.search);
                    const paginaAtual = urlParams.get('page') || 'pagina-inicial';
                    document.getElementById('input_pagina_origem').value = paginaAtual;

                    modal.style.display = 'flex';
                }
            }

            function fecharModal() {
                document.getElementById('modalProjeto').style.display = 'none';
            }

            window.onclick = function(e) {
                const modal = document.getElementById('modalProjeto');
                if (e.target === modal) fecharModal();
            };

            // Sidebar logic...
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                if (isOverlayMode()) {
                    sidebar.classList.toggle('open');
                    overlay.classList.toggle('active');
                } else {
                    sidebar.classList.toggle('expanded');
                    localStorage.setItem('sidebarExpanded', sidebar.classList.contains('expanded'));
                }
            }

            function closeSidebar() {
                document.getElementById('sidebar').classList.remove('open');
                document.getElementById('sidebarOverlay').classList.remove('active');
            }

            function isOverlayMode() {
                return window.innerWidth < 768;
            }
        </script>
        <script>
            // Assim que a página carrega completamente
            window.addEventListener('load', function() {
                const url = new URL(window.location.href);

                // Verifica se o parâmetro "sucesso" está na URL
                if (url.searchParams.has('sucesso')) {
                    // Remove o parâmetro do objeto URL
                    url.searchParams.delete('sucesso');

                    // Substitui a URL na barra do navegador SEM recarregar a página
                    window.history.replaceState({}, document.title, url.toString());
                }
            });

            // Sua função do botão 'X' agora só precisa esconder o alerta
            function fecharAvisoELimparURL() {
                const alerta = document.getElementById('alertaSucesso');
                if (alerta) {
                    alerta.classList.remove('show'); // Efeito de fade do Bootstrap
                    setTimeout(() => alerta.remove(), 150); // Remove do HTML
                }
            }
        </script>
    </body>
</body>

</html>