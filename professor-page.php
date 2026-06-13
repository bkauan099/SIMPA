<?php
session_start();

if (empty($_SESSION['id_usuario']) || !str_contains(strtolower($_SESSION['perfil'] ?? ''), 'professor')) {
    header("Location: login-page.php");
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'pagina-inicial';

require_once 'conexao/conexao.php';

try {
    $stmt_tipos = $pdo->query("SELECT id_tipo, nome FROM tipo_projetos ORDER BY nome ASC");
    $tipos = $stmt_tipos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $tipos = [];
}

/* ── Notificações do professor ─────────────────────────────── */
$_id_prof = $_SESSION['id_usuario'] ?? 0;
$_notifProf = [];

if ($_id_prof) {
    // 1. Documentos pendentes enviados por alunos
    try {
        $s = $pdo->prepare(
            "SELECT pr.titulo AS doc_titulo, u.nome AS aluno_nome, p.titulo AS proj_titulo
             FROM producoes pr
             LEFT JOIN projetos p ON pr.id_projeto = p.id_projeto
             LEFT JOIN participacao par ON p.id_projeto = par.id_projeto AND par.id_usuario != :id_prof
             LEFT JOIN usuarios u ON par.id_usuario = u.id_usuario
             WHERE pr.status = 'pendente'
               AND p.id_projeto IN (
                   SELECT id_projeto FROM participacao WHERE id_usuario = :id_prof2
               )
             ORDER BY pr.id_producao DESC LIMIT 15"
        );
        $s->execute([':id_prof' => $_id_prof, ':id_prof2' => $_id_prof]);
        foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $quem = !empty($r['aluno_nome']) ? htmlspecialchars($r['aluno_nome']) : 'Um aluno';
            $_notifProf[] = [
                'icone' => 'bi-file-earmark-arrow-up-fill',
                'cor'   => '#3b82f6',
                'texto' => $quem . ' enviou um documento: <strong>' . htmlspecialchars($r['doc_titulo']) . '</strong>',
            ];
        }
    } catch (Exception $__e) {}

    // 2. Tarefas com prazo vencendo hoje
    try {
        $s = $pdo->prepare(
            "SELECT a.titulo, u.nome AS aluno_nome
             FROM agenda_items a
             LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
             JOIN participacao par ON a.id_projeto = par.id_projeto
             WHERE par.id_usuario = :id_prof
               AND a.id_projeto IS NOT NULL
               AND a.data = CURRENT_DATE
               AND (a.concluido = false OR a.concluido IS NULL)
             ORDER BY a.titulo ASC LIMIT 10"
        );
        $s->execute([':id_prof' => $_id_prof]);
        foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $quem = !empty($r['aluno_nome']) ? ' (' . htmlspecialchars($r['aluno_nome']) . ')' : '';
            $_notifProf[] = [
                'icone' => 'bi-alarm-fill',
                'cor'   => '#f97316',
                'texto' => 'Prazo hoje: <strong>' . htmlspecialchars($r['titulo']) . '</strong>' . $quem,
            ];
        }
    } catch (Exception $__e) {}

    // 3. Tarefas em atraso (últimos 7 dias)
    try {
        $s = $pdo->prepare(
            "SELECT a.titulo, a.data, u.nome AS aluno_nome
             FROM agenda_items a
             LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
             JOIN participacao par ON a.id_projeto = par.id_projeto
             WHERE par.id_usuario = :id_prof
               AND a.id_projeto IS NOT NULL
               AND a.data < CURRENT_DATE
               AND a.data >= CURRENT_DATE - INTERVAL '7 days'
               AND (a.concluido = false OR a.concluido IS NULL)
             ORDER BY a.data ASC LIMIT 10"
        );
        $s->execute([':id_prof' => $_id_prof]);
        foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $dt   = new DateTime($r['data']);
            $quem = !empty($r['aluno_nome']) ? ' — ' . htmlspecialchars($r['aluno_nome']) : '';
            $_notifProf[] = [
                'icone' => 'bi-exclamation-triangle-fill',
                'cor'   => '#ef4444',
                'texto' => 'Tarefa em atraso: <strong>' . htmlspecialchars($r['titulo']) . '</strong> (venceu em ' . $dt->format('d/m/Y') . ')' . $quem,
            ];
        }
    } catch (Exception $__e) {}
}
$_totalNotifProf = count($_notifProf);

if (!empty($_GET['ajax'])) {
    $allowed_pages = ['pagina-inicial', 'meus-projetos', 'alunos', 'tarefas', 'cronograma', 'documentos', 'relatorios'];
    if (in_array($page, $allowed_pages)) {
        include "pages-professor/{$page}.php";
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
    <link rel="stylesheet" href="assets/css/professor-page.css?v=6">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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

        /* Search results dropdown */
        #resultados_busca {
            display: none; position: absolute; top: 100%; left: 0; width: 100%;
            max-height: 200px; overflow-y: auto; background-color: white !important;
            border: 1px solid #dee2e6; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 11000 !important; pointer-events: auto !important;
        }
        #resultados_busca .list-group-item { cursor: pointer !important; pointer-events: auto !important; }
    </style>
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
                <li><a href="?page=pagina-inicial" class="<?= $page == 'pagina-inicial' ? 'active' : '' ?>"><i class="bi bi-house-door"></i><span class="nav-label">Página Inicial</span></a></li>
                <li><a href="?page=meus-projetos" class="<?= $page == 'meus-projetos' ? 'active' : '' ?>"><i class="bi bi-folder"></i><span class="nav-label">Meus Projetos</span></a></li>
                <li><a href="?page=alunos" class="<?= $page == 'alunos' ? 'active' : '' ?>"><i class="bi bi-people"></i><span class="nav-label">Meus Alunos</span></a></li>
                <li><a href="?page=tarefas" class="<?= $page == 'tarefas' ? 'active' : '' ?>"><i class="bi bi-check2-square"></i><span class="nav-label">Tarefas</span></a></li>
                <li><a href="?page=cronograma" class="<?= $page == 'cronograma' ? 'active' : '' ?>"><i class="bi bi-calendar-event"></i><span class="nav-label">Cronograma</span></a></li>
                <li><a href="?page=documentos" class="<?= $page == 'documentos' ? 'active' : '' ?>"><i class="bi bi-file-earmark-text"></i><span class="nav-label">Documentos</span></a></li>
                <li><a href="?page=relatorios" class="<?= $page == 'relatorios' ? 'active' : '' ?>"><i class="bi bi-bar-chart-line"></i><span class="nav-label">Relatórios</span></a></li>
                <li class="sidebar-sair"><a href="logout.php"><i class="bi bi-box-arrow-left"></i><span class="nav-label">Sair</span></a></li>
            </ul>
        </nav>

        <div id="content">
            <header class="navbar-custom">
                <div class="topbar-left">
                    <button class="topbar-toggle" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
                    <img src="assets/img/uema-logo.png" alt="UEMA" class="logo-uema-top">
                    <div class="logo-sep"></div>
                    <img src="assets/img/proexae-branco-semfundo.png" alt="ProExae" class="logo-proexae-top">
                </div>
                <div class="topbar-right">
                    <!-- SININHO -->
                    <div class="tb-dropdown-wrap" id="wrapNotif">
                        <button class="tb-icon-btn" id="btnNotif" aria-label="Notificações">
                            <i class="bi bi-bell fs-5"></i>
                            <span class="tb-badge" id="badgeNotif"<?= $_totalNotifProf === 0 ? ' style="display:none"' : '' ?>><?= $_totalNotifProf ?></span>
                        </button>
                        <div class="tb-dropdown tb-dropdown-notif" id="dropNotif">
                            <div class="tb-drop-header">
                                <span class="fw-semibold" style="font-size:0.85rem;color:#1e293b;">Notificações</span>
                                <button class="tb-btn-lerall" id="btnLerTodas">Marcar todas como lidas</button>
                            </div>
                            <div id="listaNotif">
                                <?php if (empty($_notifProf)): ?>
                                <div class="tb-notif-vazia">
                                    <i class="bi bi-bell-slash" style="font-size:1.5rem;display:block;margin-bottom:8px;"></i>
                                    Nenhuma notificação
                                </div>
                                <?php else: foreach ($_notifProf as $_n): ?>
                                <div class="tb-notif-item" data-lida="0">
                                    <div class="tb-notif-texto">
                                        <i class="bi <?= $_n['icone'] ?>" style="color:<?= $_n['cor'] ?>;margin-right:6px;flex-shrink:0;"></i>
                                        <span><?= $_n['texto'] ?></span>
                                    </div>
                                    <button class="tb-notif-toggle">Marcar como lida</button>
                                </div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- PERFIL -->
                    <div class="d-flex align-items-center gap-2" style="cursor:pointer">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nome'] ?? 'Professor') ?>&background=random" class="rounded-circle" width="34">
                        <span class="fw-medium d-none d-sm-inline"><?= htmlspecialchars($_SESSION['nome'] ?? 'Professor') ?> <i class="bi bi-chevron-down small"></i></span>
                    </div>
                </div>
            </header>

            <div class="dashboard-container" id="ajaxContent">
                <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
                    <div id="alertaSucesso" class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" style="border-left: 5px solid #16a34a;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            <div><strong>Projeto cadastrado com sucesso!</strong></div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="limparUrlSucesso()"></button>
                    </div>
                <?php endif; ?>

                <?php
                $allowed_pages = ['pagina-inicial', 'meus-projetos', 'alunos', 'tarefas', 'cronograma', 'documentos', 'relatorios'];
                if (in_array($page, $allowed_pages)) {
                    include "pages-professor/{$page}.php";
                } else {
                    include "pages-professor/pagina-inicial.php";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════ MODAIS BOOTSTRAP ═══════════════════════════ -->

    <!-- Modal Novo Projeto -->
    <div class="modal fade" id="modalNovoProjeto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-bold" style="color:var(--azul-uema)">Novo Projeto</h4>
                    <button type="button" class="btn-close" onclick="fecharQualquerModal()"></button>
                </div>
                <div class="modal-body pt-2">
                    <form id="formNovoProjeto" action="controllers/controller-professor/cadastrar-projeto.php" method="POST">
                        <input type="hidden" name="pagina_origem" id="input_pagina_origem" value="<?= $page ?>">
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
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Data de Início</label>
                                <input type="date" name="data_inicio" id="data_inicio" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Data de Término</label>
                                <input type="date" name="data_fim" id="data_fim" class="form-control">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Carga Horária (Horas)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                <input type="number" name="carga_horaria" class="form-control" placeholder="Ex: 20" min="1" required>
                            </div>
                            <small class="text-muted">Informe a carga horária média do projeto.</small>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" onclick="fecharQualquerModal()" class="btn btn-light border">Cancelar</button>
                            <button type="button" id="btnSalvarProjeto" onclick="cadastrarProjeto()" class="btn btn-primary">Cadastrar Projeto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Sucesso Cadastro -->
    <div class="modal fade" id="modalSucessoCadastro" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
            <div class="modal-content text-center p-4 border-0">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size:4rem;"></i>
                    <h4 class="fw-bold mt-3">Sucesso!</h4>
                    <p id="mensagemSucessoModal" class="text-muted">Projeto adicionado.</p>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-success px-5 fw-bold" onclick="finalizarProcessoSimpa()">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Projeto -->
    <div class="modal fade" id="modalEditarProjeto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-bold text-primary">Editar Projeto</h4>
                    <button type="button" class="btn-close" onclick="fecharQualquerModal()"></button>
                </div>
                <div class="modal-body pt-2">
                    <form action="controllers/controller-professor/editar-projeto.php" method="POST">
                        <input type="hidden" name="id_projeto" id="edit_id_projeto">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Título do Projeto</label>
                            <input type="text" name="titulo" id="edit_titulo" class="form-control" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Área</label>
                                <input type="text" name="area" id="edit_area" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tipo</label>
                                <select name="id_tipo" id="edit_id_tipo" class="form-select" required>
                                    <option value="" disabled>Selecione o tipo...</option>
                                    <?php foreach ($tipos as $tipo): ?>
                                        <option value="<?= $tipo['id_tipo'] ?>"><?= htmlspecialchars($tipo['nome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Descrição</label>
                            <textarea name="descricao" id="edit_descricao" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Data de Início</label>
                                <input type="date" name="data_inicio" id="edit_data_inicio" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Data de Término</label>
                                <input type="date" name="data_fim" id="edit_data_fim" class="form-control">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Carga Horária (Horas)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                <input type="number" name="carga_horaria" id="edit_carga_horaria" class="form-control" min="1" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" onclick="fecharQualquerModal()" class="btn btn-light border">Cancelar</button>
                            <button type="submit" id="btnAplicarAlteracoes" class="btn btn-primary">Aplicar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Gerenciar Alunos -->
    <div class="modal fade" id="modalAlunos" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-bold text-primary">Gerenciar Alunos</h4>
                    <button type="button" class="btn-close" onclick="fecharQualquerModal()"></button>
                </div>
                <div class="modal-body pt-2">
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Adicionar Novo Aluno</h6>
                            <div class="row g-2">
                                <div class="col-md-7 position-relative">
                                    <input type="text" id="busca_aluno" class="form-control" placeholder="Nome ou matrícula..." autocomplete="off">
                                    <input type="hidden" id="id_aluno_selecionado">
                                    <div id="resultados_busca" class="list-group position-absolute w-100 shadow" style="z-index:9999;display:none;"></div>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" id="ch_aluno" class="form-control" placeholder="CH (Horas)" min="1">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" id="btnAdicionarAluno" class="btn btn-primary w-100" onclick="dispararVinculo()">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="lista_alunos_projeto" class="table-responsive">
                        <p class="text-center text-muted">Carregando alunos...</p>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" onclick="fecharQualquerModal()" class="btn btn-secondary px-4">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Exclusão Aluno -->
    <div class="modal fade" id="modalConfirmarExclusaoAluno" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
            <div class="modal-content text-center p-4 border-0">
                <div class="mb-4">
                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:4rem;"></i>
                    <h4 class="fw-bold mt-3">Remover Aluno?</h4>
                    <p class="text-muted">O aluno será desvinculado do projeto.</p>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-light border px-4" onclick="bsHide('modalConfirmarExclusaoAluno')">Cancelar</button>
                    <button type="button" id="btnConfirmarExclusaoAlunoReal" class="btn btn-danger px-4" onclick="executarExclusaoAlunoReal()">Remover Agora</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Aviso CH -->
    <div class="modal fade" id="modalAvisoCH" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:350px;">
            <div class="modal-content text-center p-4 border-0">
                <div class="mb-4">
                    <i class="bi bi-person-fill-exclamation text-warning" style="font-size:3rem;"></i>
                    <h4 class="fw-bold mt-3">Atenção</h4>
                    <p class="text-muted">Por favor, informe a <strong>Carga Horária</strong> do aluno para continuar.</p>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-warning px-5 fw-bold text-white" onclick="bsHide('modalAvisoCH')">ENTENDI</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Aviso Seleção -->
    <div class="modal fade" id="modalAvisoSelecao" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
            <div class="modal-content text-center p-4 border-0">
                <div class="mb-4">
                    <i class="bi bi-person-fill-exclamation text-warning" style="font-size:3rem;"></i>
                    <h4 class="fw-bold mt-3">Aluno não selecionado</h4>
                    <p class="text-muted">Você precisa <strong>clicar em um nome</strong> na lista de sugestões para poder adicionar.</p>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-warning px-5 fw-bold text-white" onclick="bsHide('modalAvisoSelecao')">ENTENDI</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Aviso Duplicado -->
    <div class="modal fade" id="modalAvisoDuplicado" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
            <div class="modal-content text-center p-4 border-0">
                <div class="mb-4">
                    <i class="bi bi-person-fill-exclamation text-warning" style="font-size:3rem;"></i>
                    <h4 class="fw-bold mt-3">Vínculo já existente</h4>
                    <p class="text-muted">Este aluno já está participando deste projeto. Não é possível adicioná-lo duas vezes.</p>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-warning px-5 fw-bold text-white" onclick="bsHide('modalAvisoDuplicado')">ENTENDI</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Gerenciar Documentos -->
    <div class="modal fade" id="modalDocumentos" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-bold text-primary">
                        Documentos do Projeto: <span id="nomeProjetoModal" class="text-dark"></span>
                    </h4>
                    <button type="button" class="btn-close" onclick="fecharQualquerModal()"></button>
                </div>
                <div class="modal-body pt-2">
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Anexar Novo Arquivo</h6>
                            <form id="formUploadDoc" enctype="multipart/form-data">
                                <input type="hidden" name="id_projeto" id="id_projeto_doc">
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <input type="text" name="titulo" class="form-control" placeholder="Título do Documento">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="file" name="arquivo" class="form-control" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" id="btnEnviarDoc" class="btn btn-primary w-100" onclick="realizarUpload()">
                                            <i class="bi bi-upload"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted mt-2 d-block">Formatos: PDF, DOCX, JPG, PNG.</small>
                            </form>
                        </div>
                    </div>
                    <div class="documentos-lista">
                        <h6 class="fw-bold mb-3">Arquivos Presentes</h6>
                        <div id="lista_documentos_projeto" class="table-responsive" style="max-height:300px;overflow-y:auto;">
                            <table class="table table-sm table-hover align-middle">
                                <thead class="table-light">
                                    <tr class="small text-muted">
                                        <th>DOCUMENTO</th>
                                        <th>DATA</th>
                                        <th>STATUS</th>
                                        <th class="text-center">AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody id="corpo_tabela_docs">
                                    <tr><td colspan="4" class="text-center py-3 text-muted">Carregando documentos...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" onclick="fecharQualquerModal()" class="btn btn-secondary px-4">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Sucesso Documento -->
    <div class="modal fade" id="modalSucessoDoc" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
            <div class="modal-content text-center p-4 border-0">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size:4rem;"></i>
                    <h4 class="fw-bold mt-3">Documento enviado!</h4>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-success px-5 fw-bold" onclick="bsHide('modalSucessoDoc')">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Exclusão Documento -->
    <div class="modal fade" id="modalConfirmarExclusaoDoc" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
            <div class="modal-content text-center p-4 border-0">
                <div class="mb-4">
                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:4rem;"></i>
                    <h4 class="fw-bold mt-3">Tem certeza?</h4>
                    <p class="fw-bold mt-3">Esta ação não pode ser desfeita. O arquivo será excluído permanentemente.</p>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-light border px-4" onclick="bsHide('modalConfirmarExclusaoDoc')">Cancelar</button>
                    <button type="button" id="btnConfirmarExclusaoReal" class="btn btn-danger px-4" onclick="executarExclusaoReal()">Excluir Agora</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/ajax-nav.js"></script>

    <script>
        // ── HELPERS BOOTSTRAP MODAL ─────────────────────────────────────────────
        function bsShow(id) {
            bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).show();
        }
        function bsHide(id) {
            bootstrap.Modal.getInstance(document.getElementById(id))?.hide();
        }

        // ── VARIÁVEIS GLOBAIS ───────────────────────────────────────────────────
        let houveAlteracaoNoBanco = false;
        let dadosOriginaisEdicao = "";
        let itemParaRemover = null;
        let projetoDeOrigem = null;

        // ── FECHAR TODOS OS MODAIS ──────────────────────────────────────────────
        function fecharQualquerModal() {
            const ids = [
                'modalEditarProjeto', 'modalNovoProjeto', 'modalAlunos', 'modalDocumentos',
                'modalSucessoDoc', 'modalConfirmarExclusaoDoc', 'modalConfirmarExclusaoAluno',
                'modalAvisoCH', 'modalAvisoSelecao', 'modalAvisoDuplicado', 'modalSucessoCadastro',
                'modalTarefa', 'modalVerTarefa', 'modalConfirmarExclusaoTarefa'
            ];
            ids.forEach(id => {
                const el = document.getElementById(id);
                if (el) bootstrap.Modal.getInstance(el)?.hide();
            });

            if (houveAlteracaoNoBanco) {
                houveAlteracaoNoBanco = false;
                const linkProjetos = document.querySelector('a[href*="meus-projetos"]');
                if (linkProjetos) linkProjetos.click();
                else window.location.href = "?page=meus-projetos";
            }

            if (document.getElementById('busca_aluno')) {
                document.getElementById('busca_aluno').value = '';
                document.getElementById('id_aluno_selecionado').value = '';
                document.getElementById('resultados_busca').style.display = 'none';
            }

            limparUrlSucesso();
        }

        // ── GESTÃO DE PROJETOS ──────────────────────────────────────────────────
        function verificarAlteracoesEdicao() {
            const form = document.querySelector('#modalEditarProjeto form');
            const btn = document.getElementById('btnAplicarAlteracoes');
            const formData = new FormData(form);
            let dadosAtuais = "";
            for (let value of formData.values()) dadosAtuais += value;
            if (dadosAtuais !== dadosOriginaisEdicao) {
                btn.disabled = false; btn.style.opacity = "1";
            } else {
                btn.disabled = true; btn.style.opacity = "0.6";
            }
        }

        function abrirModal() {
            const modal = document.getElementById('modalNovoProjeto');
            modal.querySelector('form').reset();
            bsShow('modalNovoProjeto');
        }

        function abrirModalEditar(projeto) {
            const modal = document.getElementById('modalEditarProjeto');
            const form = modal.querySelector('form');
            const btn = document.getElementById('btnAplicarAlteracoes');
            form.reset();

            document.getElementById('edit_id_projeto').value = projeto.id_projeto;
            document.getElementById('edit_titulo').value = projeto.titulo;
            document.getElementById('edit_area').value = projeto.area;
            document.getElementById('edit_descricao').value = projeto.descricao;
            document.getElementById('edit_id_tipo').value = projeto.id_tipo;
            document.getElementById('edit_carga_horaria').value = projeto.carga_horaria;

            if (projeto.data_inicio) document.getElementById('edit_data_inicio').value = projeto.data_inicio.substring(0, 10);
            if (projeto.data_fim)    document.getElementById('edit_data_fim').value    = projeto.data_fim.substring(0, 10);

            const data = new FormData(form);
            dadosOriginaisEdicao = "";
            for (let value of data.values()) dadosOriginaisEdicao += value;

            btn.disabled = true; btn.style.opacity = "0.6";
            bsShow('modalEditarProjeto');

            form.oninput = verificarAlteracoesEdicao;
            form.onchange = verificarAlteracoesEdicao;
        }

        document.querySelector('#modalNovoProjeto form').onsubmit = function() {
            const btn = document.getElementById('btnSalvarProjeto');
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Cadastrando...`;
        };

        document.querySelector('#modalEditarProjeto form').onsubmit = function() {
            const btn = document.getElementById('btnAplicarAlteracoes');
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Salvando...`;
        };

        // ── GESTÃO DE DOCUMENTOS ────────────────────────────────────────────────
        function abrirModalDocumentos(idProjeto, nomeProjeto) {
            const modal = document.getElementById('modalDocumentos');
            const spanNome = document.getElementById('nomeProjetoModal');
            if (modal) {
                modal.setAttribute('data-id-projeto', idProjeto);
                if (spanNome && nomeProjeto) spanNome.innerText = nomeProjeto;
                bsShow('modalDocumentos');
                document.querySelector('#lista_documentos_projeto tbody').innerHTML =
                    '<tr><td colspan="4" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>';
                listarDocumentos(idProjeto);
            }
        }

        function realizarUpload() {
            const btn = document.getElementById('btnEnviarDoc');
            const idProjeto = document.getElementById('modalDocumentos').getAttribute('data-id-projeto');
            const inputArquivo = document.querySelector('#formUploadDoc input[type="file"]');
            const inputTitulo = document.querySelector('#formUploadDoc input[name="titulo"]');

            if (!inputArquivo.files[0]) { alert("Por favor, selecione um arquivo."); return; }

            const conteudoOriginal = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span>`;

            const formData = new FormData();
            formData.append('id_projeto', idProjeto);
            formData.append('arquivo', inputArquivo.files[0]);
            formData.append('titulo', inputTitulo.value);

            fetch('controllers/controller-professor/upload-documento.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        inputArquivo.value = '';
                        inputTitulo.value = '';
                        bsShow('modalSucessoDoc');
                        listarDocumentos(idProjeto);
                    } else {
                        alert("Erro: " + data.mensagem);
                    }
                })
                .catch(err => { console.error("Erro no upload:", err); alert("Erro na comunicação com o servidor."); })
                .finally(() => { btn.disabled = false; btn.innerHTML = conteudoOriginal; });
        }

        function listarDocumentos(idProjeto) {
            fetch(`controllers/controller-professor/buscar-documentos.php?id_projeto=${idProjeto}`)
                .then(res => res.text())
                .then(html => { document.querySelector('#lista_documentos_projeto tbody').innerHTML = html; })
                .catch(() => {
                    document.querySelector('#lista_documentos_projeto tbody').innerHTML =
                        "<tr><td colspan='4' class='text-center text-danger'>Erro ao carregar lista.</td></tr>";
                });
        }

        window.excluirDocumento = function(idDocumento, idProjeto) {
            itemParaRemover = idDocumento;
            projetoDeOrigem = idProjeto;
            const modalConfirmar = document.getElementById('modalConfirmarExclusaoDoc');
            if (modalConfirmar) {
                bsShow('modalConfirmarExclusaoDoc');
            } else {
                if (confirm('Tem certeza que deseja excluir este documento permanentemente?')) {
                    executarExclusaoReal();
                }
            }
        };

        function executarExclusaoReal() {
            if (!itemParaRemover) return;
            const btn = document.getElementById('btnConfirmarExclusaoReal');
            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Excluindo...`;
            const formData = new FormData();
            formData.append('id_documento', itemParaRemover);
            fetch('controllers/controller-professor/excluir-documento.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        bsHide('modalConfirmarExclusaoDoc');
                        listarDocumentos(projetoDeOrigem);
                    } else alert(data.mensagem);
                })
                .finally(() => {
                    btn.disabled = false; btn.innerHTML = original; itemParaRemover = null;
                });
        }

        // ── GESTÃO DE ALUNOS ────────────────────────────────────────────────────
        function abrirModalAlunos(idProjeto) {
            const modal = document.getElementById('modalAlunos');
            const container = document.getElementById('lista_alunos_projeto');
            modal.setAttribute('data-id-projeto', idProjeto);
            bsShow('modalAlunos');
            container.innerHTML = `<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>`;
            fetch(`controllers/controller-professor/buscar-alunos.php?id_projeto=${idProjeto}`)
                .then(res => res.text())
                .then(html => { container.innerHTML = html; });
        }

        function removerAluno(idUsuario, idProjeto) {
            itemParaRemover = idUsuario;
            projetoDeOrigem = idProjeto || document.getElementById('modalAlunos').getAttribute('data-id-projeto');
            bsShow('modalConfirmarExclusaoAluno');
        }

        function executarExclusaoAlunoReal() {
            if (!itemParaRemover || !projetoDeOrigem) return;
            const btn = document.getElementById('btnConfirmarExclusaoAlunoReal');
            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Removendo...`;

            const formData = new FormData();
            formData.append('acao', 'remover');
            formData.append('id_usuario', itemParaRemover);
            formData.append('id_projeto', projetoDeOrigem);

            fetch('controllers/controller-professor/gerenciar-participacao.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        bsHide('modalConfirmarExclusaoAluno');
                        abrirModalAlunos(projetoDeOrigem);
                        houveAlteracaoNoBanco = true;
                    } else alert(data.mensagem);
                })
                .finally(() => {
                    btn.disabled = false; btn.innerHTML = original; itemParaRemover = null;
                });
        }

        function vincularAluno(idUsuario, idProjeto, chAluno) {
            const btn = document.getElementById('btnAdicionarAluno');
            const conteudoOriginal = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span>`;

            const formData = new FormData();
            formData.append('acao', 'vincular');
            formData.append('id_usuario', idUsuario);
            formData.append('id_projeto', idProjeto);
            formData.append('carga_horaria', chAluno);

            fetch('controllers/controller-professor/gerenciar-participacao.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        houveAlteracaoNoBanco = true;
                        resetarEstadoBusca();
                        abrirModalAlunos(idProjeto);
                    } else if (data.mensagem.includes("já está cadastrado")) {
                        bsShow('modalAvisoDuplicado');
                    } else alert(data.mensagem);
                })
                .finally(() => { btn.disabled = false; btn.innerHTML = conteudoOriginal; });
        }

        // ── AUXILIARES E EVENTOS ────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarEl = document.getElementById('sidebar');
            if (localStorage.getItem('sidebarExpanded') === 'true' && window.innerWidth >= 768) {
                sidebarEl.classList.add('expanded');
            }
            document.documentElement.classList.remove('sidebar-pre-expanded');

            const inputBusca = document.getElementById('busca_aluno');
            const divResultados = document.getElementById('resultados_busca');
            let debounceTimer;

            if (inputBusca) {
                inputBusca.addEventListener('input', function() {
                    this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, "");
                    let termo = this.value.trim().toLowerCase();
                    if (termo.length < 1) { divResultados.style.display = 'none'; return; }
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        fetch(`controllers/controller-professor/buscar-alunos.php?busca=${termo}`)
                            .then(res => res.text())
                            .then(html => { divResultados.innerHTML = html; divResultados.style.display = 'block'; });
                    }, 300);
                });
            }
            document.addEventListener('mousedown', function(e) {
                const divRes = document.getElementById('resultados_busca');
                const inp = document.getElementById('busca_aluno');
                if (divRes && divRes.style.display !== 'none') {
                    if (divRes.contains(e.target)) return;
                    if (e.target !== inp) divRes.style.display = 'none';
                }
            });
        });


        function selecionarAluno(nome, id) {
            // Contexto: modalAlunos (página meus-projetos)
            const inpA = document.getElementById('busca_aluno');
            if (inpA) { inpA.value = nome; }
            const hidA = document.getElementById('id_aluno_selecionado');
            if (hidA) { hidA.value = id; }
            const resA = document.getElementById('resultados_busca');
            if (resA) { resA.style.display = 'none'; }

            // Contexto: modalAdicionarAluno (página alunos.php)
            const inpB = document.getElementById('busca_aluno_geral');
            if (inpB) { inpB.value = nome; }
            const hidB = document.getElementById('id_aluno_vincular');
            if (hidB) { hidB.value = id; }
            const resB = document.getElementById('resultados_busca_geral');
            if (resB) { resB.style.display = 'none'; }
        }

        function dispararVinculo() {
            const idUsuario = document.getElementById('id_aluno_selecionado').value;
            const chAluno   = document.getElementById('ch_aluno').value;
            const idProjeto = document.getElementById('modalAlunos').getAttribute('data-id-projeto');
            if (!idUsuario) return bsShow('modalAvisoSelecao');
            if (!chAluno || chAluno <= 0) return bsShow('modalAvisoCH');
            vincularAluno(idUsuario, idProjeto, chAluno);
        }

        function resetarEstadoBusca() {
            document.getElementById('busca_aluno').value = '';
            document.getElementById('id_aluno_selecionado').value = '';
            document.getElementById('ch_aluno').value = '';
            document.getElementById('resultados_busca').style.display = 'none';
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            document.documentElement.classList.remove('sidebar-pre-expanded');
            if (window.innerWidth < 768) {
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

        function limparUrlSucesso() {
            if (window.location.search.includes('sucesso')) {
                const params = new URLSearchParams(window.location.search);
                const currentPage = params.get('page') || 'pagina-inicial';
                const novaUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + "?page=" + currentPage;
                window.history.replaceState({ path: novaUrl }, '', novaUrl);
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape") fecharQualquerModal();
        });

        function cadastrarProjeto() {
            const form = document.getElementById('formNovoProjeto');
            const btn  = document.getElementById('btnSalvarProjeto');
            if (!form.checkValidity()) { form.reportValidity(); return; }

            const formData      = new FormData(form);
            const textoOriginal = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Cadastrando...`;

            fetch('controllers/controller-professor/cadastrar-projeto.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        document.getElementById('mensagemSucessoModal').innerText = data.mensagem;
                        bsShow('modalSucessoCadastro');
                        btn.innerHTML = textoOriginal;
                        btn.disabled = false;
                    } else {
                        alert("Erro: " + data.mensagem);
                        btn.innerHTML = textoOriginal;
                        btn.disabled = false;
                    }
                })
                .catch(err => {
                    console.error("Erro no envio:", err);
                    btn.innerHTML = textoOriginal;
                    btn.disabled = false;
                });
        }

        function finalizarProcessoSimpa() {
            bsHide('modalSucessoCadastro');
            bsHide('modalNovoProjeto');
            const linkProjetos = document.querySelector('a[href*="meus-projetos"]');
            if (linkProjetos) linkProjetos.click();
            else window.location.href = "?page=meus-projetos";
        }

        function alterarStatusDoc(idDoc, novoStatus, btnEl) {
            if (btnEl) {
                btnEl.disabled = true;
                btnEl._original = btnEl.innerHTML;
                btnEl.innerHTML = `<span class="spinner-border spinner-border-sm"></span>`;
            }
            const formData = new FormData();
            formData.append('id_documento', idDoc);
            formData.append('status', novoStatus);
            fetch('controllers/controller-professor/atualizar-status-doc.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        const linkDocs = document.querySelector('a[href*="documentos"]');
                        if (linkDocs) linkDocs.click();
                        else window.location.reload();
                    } else {
                        if (btnEl) { btnEl.disabled = false; btnEl.innerHTML = btnEl._original; }
                        alert('Erro: ' + data.mensagem);
                    }
                })
                .catch(err => {
                    if (btnEl) { btnEl.disabled = false; btnEl.innerHTML = btnEl._original; }
                    console.error('Erro na requisição:', err);
                });
        }

        function filtrarDocumentosModal() {
            const input        = document.getElementById('buscaDocModal');
            const selectStatus = document.getElementById('filtroStatusModal');
            const tabelaCorpo  = document.getElementById('corpo_tabela_docs');
            if (!tabelaCorpo || !input) return;

            const termo           = input.value.toLowerCase().trim();
            const statusSelecionado = selectStatus.value.toLowerCase().trim();
            const linhas          = tabelaCorpo.querySelectorAll('tr:not(#linha-nenhum-doc)');
            let encontrouAlgum    = false;

            linhas.forEach(linha => {
                const celulaNome  = linha.querySelector('td:first-child span.fw-bold');
                const badgeStatus = linha.querySelector('td span.badge:not(.bg-light)');
                if (celulaNome && badgeStatus) {
                    const bateBusca  = termo === "" || celulaNome.textContent.toLowerCase().trim().includes(termo);
                    const bateStatus = statusSelecionado === "" || badgeStatus.textContent.toLowerCase().trim() === statusSelecionado;
                    if (bateBusca && bateStatus) { linha.style.display = ""; encontrouAlgum = true; }
                    else linha.style.display = "none";
                }
            });

            let linhaAviso = document.getElementById('linha-nenhum-doc');
            if (!encontrouAlgum) {
                if (!linhaAviso) {
                    linhaAviso = document.createElement('tr');
                    linhaAviso.id = 'linha-nenhum-doc';
                    linhaAviso.innerHTML = `<td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-file-earmark-x mb-2" style="font-size:2rem;display:block;"></i>
                        <p class="fw-bold m-0">Nenhum documento encontrado</p></td>`;
                    tabelaCorpo.appendChild(linhaAviso);
                }
            } else if (linhaAviso) {
                linhaAviso.remove();
            }
        }

        // ── NOTIFICAÇÕES ────────────────────────────────────────────────────────
        (function () {
            const btnNotif    = document.getElementById('btnNotif');
            const dropNotif   = document.getElementById('dropNotif');
            const badgeNotif  = document.getElementById('badgeNotif');
            const btnLerTodas = document.getElementById('btnLerTodas');
            const listaNotif  = document.getElementById('listaNotif');

            function atualizarBadge() {
                const n = listaNotif.querySelectorAll('.tb-notif-item[data-lida="0"]').length;
                badgeNotif.textContent = n;
                badgeNotif.style.display = n > 0 ? '' : 'none';
            }

            function fecharDropNotif() {
                dropNotif.classList.remove('aberto');
            }

            btnNotif.addEventListener('click', function (e) {
                e.stopPropagation();
                dropNotif.classList.toggle('aberto');
            });

            document.addEventListener('click', fecharDropNotif);
            dropNotif.addEventListener('click', function (e) { e.stopPropagation(); });

            listaNotif.addEventListener('click', function (e) {
                const btn = e.target.closest('.tb-notif-toggle');
                if (!btn) return;
                const item = btn.closest('.tb-notif-item');
                const lida = item.dataset.lida === '1';
                item.dataset.lida = lida ? '0' : '1';
                btn.textContent   = lida ? 'Marcar como lida' : 'Marcar como não lida';
                atualizarBadge();
            });

            btnLerTodas.addEventListener('click', function () {
                listaNotif.querySelectorAll('.tb-notif-item').forEach(function (item) {
                    item.dataset.lida = '1';
                    item.querySelector('.tb-notif-toggle').textContent = 'Marcar como não lida';
                });
                atualizarBadge();
            });

            atualizarBadge();
        })();
    </script>

</body>
</html>
