<?php
session_start();

$id_usuario = $_SESSION['id_usuario'] ?? null;
$perfil     = strtolower($_SESSION['perfil'] ?? '');
if (!$id_usuario || !str_contains($perfil, 'aluno')) {
    header('Location: login-page.php');
    exit;
}

require_once 'conexao/conexao.php';
$stmt = $pdo->prepare("SELECT nome FROM usuarios WHERE id_usuario = :id");
$stmt->execute([':id' => $id_usuario]);
$nomeUsuario  = $stmt->fetchColumn() ?: 'Usuário';
$primeiroNome = explode(' ', $nomeUsuario)[0];

/* ── Notificações reais ─────────────────────────────────────── */
require __DIR__ . '/pages-aluno/gerar-notificacoes.php';
$_notificacoes = $notificacoes;
$_totalNotif   = count($_notificacoes);
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
            <li><a href="javascript:void(0)" id="menu-pagina-inicial" onclick="carregarPagina('pagina-inicial')" title="Página Inicial">
                <i class="bi bi-house-door"></i><span class="nav-label">Página Inicial</span></a></li>
            <li><a href="javascript:void(0)" id="menu-gerenciar-projetos" onclick="carregarPagina('gerenciar-projetos')" title="Gerenciar Projetos">
                <i class="bi bi-folder"></i><span class="nav-label">Gerenciar Projetos</span></a></li>
            <li><a href="javascript:void(0)" id="menu-participacoes" onclick="carregarPagina('participacoes')" title="Registros">
                <i class="bi bi-clock-history"></i><span class="nav-label">Registros</span></a></li>
            <li><a href="javascript:void(0)" id="menu-tarefas" onclick="carregarPagina('tarefas')" title="Minhas Tarefas">
                <i class="bi bi-clipboard-check"></i><span class="nav-label">Minhas Tarefas</span></a></li>
            <li><a href="javascript:void(0)" id="menu-cronograma" onclick="carregarPagina('cronograma')" title="Cronograma">
                <i class="bi bi-calendar-event"></i><span class="nav-label">Cronograma</span></a></li>
            <li><a href="javascript:void(0)" id="menu-seletivos" onclick="carregarPagina('seletivos')" title="Seletivos">
                <i class="bi bi-megaphone"></i>
                <span class="nav-label">Seletivos</span>
            </a></li>
            <li><a href="javascript:void(0)" id="menu-documentos" onclick="carregarPagina('documentos')" title="Documentos">
                <i class="bi bi-file-earmark-text"></i><span class="nav-label">Documentos</span></a></li>
            <li><a href="javascript:void(0)" id="menu-certificados" onclick="carregarPagina('certificados')" title="Certificados">
                <i class="bi bi-award"></i><span class="nav-label">Certificados</span></a></li>
            <li class="sidebar-sair"><a href="logout.php" title="Sair">
                <i class="bi bi-box-arrow-left"></i><span class="nav-label">Sair</span></a></li>
        </ul>
    </nav>

    <!-- CONTEÚDO -->
    <div id="content">
        <header class="navbar-custom">
            <div class="topbar-left">
                <button class="topbar-toggle" onclick="toggleSidebar()" aria-label="Menu">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <img src="assets/img/logo-uema.png" alt="UEMA" class="logo-uema-top">
                <div class="logo-sep"></div>
                <img src="assets/img/proexae-branco-semfundo.png" alt="ProExae" class="logo-proexae-top">
            </div>
            <div class="topbar-right">

                <!-- SININHO -->
                <div class="tb-dropdown-wrap" id="wrapNotif">
                    <button class="tb-icon-btn" id="btnNotif" aria-label="Notificações">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="tb-badge" id="badgeNotif"<?= $_totalNotif === 0 ? ' style="display:none"' : '' ?>><?= $_totalNotif ?></span>
                    </button>
                    <div class="tb-dropdown tb-dropdown-notif" id="dropNotif">
                        <div class="tb-drop-header">
                            <span class="fw-semibold" style="font-size:0.85rem;color:#1e293b;">Notificações</span>
                            <button class="tb-btn-lerall" id="btnLerTodas">Marcar todas como lidas</button>
                        </div>
                        <div id="listaNotif">
                            <?php if (empty($_notificacoes)): ?>
                            <div class="tb-notif-vazia">
                                <i class="bi bi-bell-slash" style="font-size:1.5rem;display:block;margin-bottom:8px;"></i>
                                Nenhuma notificação
                            </div>
                            <?php else: foreach ($_notificacoes as $_n): ?>
                            <div class="tb-notif-item" data-lida="0">
                                <div class="tb-notif-texto">
                                    <i class="bi <?= $_n['icone'] ?>" style="color:<?= $_n['cor'] ?>;margin-right:6px;flex-shrink:0;"></i><span><?= $_n['texto'] ?></span>
                                </div>
                                <button class="tb-notif-toggle">Marcar como lida</button>
                            </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                </div>

                <!-- PERFIL -->
                <div class="tb-dropdown-wrap" id="wrapPerfil">
                    <button class="tb-icon-btn" id="btnPerfil" aria-label="Perfil">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($nomeUsuario) ?>&background=1d4ed8&color=fff"
                             class="rounded-circle" width="32" alt="Avatar">
                        <span class="fw-medium d-none d-sm-inline" style="font-size:0.88rem;">
                            <?= htmlspecialchars($primeiroNome) ?> <i class="bi bi-chevron-down" style="font-size:0.7rem;"></i>
                        </span>
                    </button>
                    <div class="tb-dropdown tb-dropdown-perfil" id="dropPerfil">
                        <button class="tb-drop-item" onclick="abrirModalPerfil()">
                            <i class="bi bi-person me-2"></i>Meu Perfil
                        </button>
                        <div class="tb-drop-divider"></div>
                        <a href="logout.php" class="tb-drop-item tb-drop-sair">
                            <i class="bi bi-box-arrow-right me-2"></i>Sair
                        </a>
                    </div>
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

<!-- SLIDE-OVER PANEL GLOBAL -->
<div id="slideOver" class="slide-over">
    <div class="slide-over-backdrop" onclick="fecharSlideOver()"></div>
    <div class="slide-over-panel">
        <div class="slide-over-header">
            <div class="flex-grow-1 min-width-0">
                <div class="d-flex align-items-center gap-2 mb-1" id="slideOverBadge"></div>
                <h5 id="slideOverTitulo"></h5>
            </div>
            <button class="slide-over-close" onclick="fecharSlideOver()" title="Fechar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="slide-over-body" id="slideOverBody"></div>
    </div>
</div>

<!-- MODAL: Enviar arquivo de tarefa -->
<div id="modalEnvioTarefa" style="display:none;position:fixed;inset:0;z-index:1070;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:24px;width:90%;max-width:460px;box-shadow:0 8px 32px rgba(0,0,0,0.18);">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0" id="tituloModalEnvio"><i class="bi bi-paperclip me-2" style="color:#3b82f6;"></i>Enviar Atividade</h6>
            <button class="btn-close" onclick="fecharModalEnvio()"></button>
        </div>
        <div id="listaArquivosExistentes" style="display:none;" class="mb-3">
            <div style="font-size:0.78rem;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;">Arquivos Enviados</div>
            <div id="itensArquivosExistentes"></div>
        </div>
        <div id="dropZone" onclick="document.getElementById('inputArquivo').click()"
             ondragover="event.preventDefault(); this.style.borderColor='#3b82f6'; this.style.background='#f0f7ff';"
             ondragleave="this.style.borderColor='#cbd5e1'; this.style.background='';"
             ondrop="event.preventDefault(); this.style.borderColor='#cbd5e1'; this.style.background=''; handleFileDrop(event);"
             onmouseenter="this.style.borderColor='#3b82f6'" onmouseleave="this.style.borderColor='#cbd5e1'"
             style="border:2px dashed #cbd5e1;border-radius:12px;padding:28px;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;">
            <i class="bi bi-cloud-arrow-up d-block mb-2" style="font-size:2rem;color:#94a3b8;"></i>
            <div style="font-size:0.85rem;color:#64748b;">Clique ou arraste arquivo(s) aqui</div>
            <div style="font-size:0.75rem;color:#94a3b8;margin-top:4px;">Qualquer formato · Máx. 15 MB</div>
        </div>
        <input type="file" id="inputArquivo" style="display:none;" multiple onchange="selecionarArquivo(this)">
        <div id="listaArquivosNovos" style="display:none;" class="mt-2">
            <div id="itensArquivosNovos"></div>
        </div>
        <div class="mt-3 d-flex justify-content-end">
            <button class="btn btn-primary" id="btnEnviarArquivo" onclick="enviarArquivoTarefa()">
                <i class="bi bi-floppy me-1"></i>Salvar Rascunho
            </button>
        </div>
    </div>
</div>

<!-- MODAL: Editar envio (rascunho) -->
<div id="modalEdicaoTarefa" style="display:none;position:fixed;inset:0;z-index:1070;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:24px;width:90%;max-width:460px;box-shadow:0 8px 32px rgba(0,0,0,0.18);">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0" id="tituloModalEdicao"><i class="bi bi-pencil me-2 text-secondary"></i>Editar Envio</h6>
            <button class="btn-close" onclick="fecharModalEdicao()"></button>
        </div>
        <div id="labelArquivoEdit" style="font-size:0.78rem;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;">Arquivos Enviados</div>
        <div id="itensArquivosEdit"></div>
        <div id="dropZoneEdit" style="display:none;"></div>
        <div id="arquivoSubstituto" style="display:none;"></div>
        <div id="obsEdicaoWrap" style="display:none;"></div>
    </div>
</div>

<!-- MODAL: Visualizar arquivo -->
<div id="modalVisualizarArquivo" style="display:none;position:fixed;inset:0;z-index:1080;background:rgba(0,0,0,0.6);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;width:96%;max-width:1140px;height:90vh;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.25);display:flex;flex-direction:column;">
        <div class="d-flex justify-content-between align-items-center p-3" style="border-bottom:1px solid #e2e8f0;gap:8px;flex-shrink:0;">
            <span class="fw-semibold text-truncate" style="font-size:0.88rem;flex:1;min-width:0;" id="tituloArquivoVis"></span>
            <div id="controlesZoom" style="display:none;align-items:center;gap:4px;flex-shrink:0;">
                <button class="btn btn-sm btn-outline-secondary" onclick="zoomVisualizar(-1)" title="Diminuir zoom" style="padding:2px 8px;line-height:1;"><i class="bi bi-dash-lg"></i></button>
                <span id="zoomLevelLabel" style="font-size:0.78rem;color:#64748b;min-width:42px;text-align:center;font-weight:600;">100%</span>
                <button class="btn btn-sm btn-outline-secondary" onclick="zoomVisualizar(+1)" title="Aumentar zoom" style="padding:2px 8px;line-height:1;"><i class="bi bi-plus-lg"></i></button>
                <button class="btn btn-sm btn-outline-secondary ms-1" onclick="resetZoomVisualizar()" title="Zoom original" style="padding:2px 7px;font-size:0.7rem;font-weight:700;">1:1</button>
            </div>
            <button class="btn-close flex-shrink-0" onclick="fecharModalVisualizar()"></button>
        </div>
        <div id="corpoArquivoVis" style="flex:1;overflow:hidden;min-height:0;position:relative;"></div>
    </div>
</div>

<?php require_once 'pages-aluno/perfil.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');
let _fetchAtivo = null;

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
    if (!isOverlayMode()) { sidebar.classList.remove('open'); overlay.classList.remove('active'); }
});
window.addEventListener('orientationchange', () => {
    setTimeout(() => {
        if (!isOverlayMode()) { sidebar.classList.remove('open'); overlay.classList.remove('active'); }
    }, 150);
});

function carregarPagina(abaSolicitada, pushState = true) {
    if (_fetchAtivo) { _fetchAtivo.abort(); _fetchAtivo = null; }

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
        default:                   arquivo = 'pages-aluno/pagina-inicial.php'; abaSolicitada = 'pagina-inicial'; break;
    }

    // pushState empilha uma entrada nova no histórico (permite o botão "voltar"
    // do navegador circular pelas páginas da SPA); replaceState só é usado na
    // carga inicial e na resposta ao próprio popstate, pra não duplicar entrada
    if (pushState) {
        history.pushState({ page: abaSolicitada }, '', location.pathname + '#' + abaSolicitada);
    } else {
        history.replaceState({ page: abaSolicitada }, '', location.pathname + '#' + abaSolicitada);
    }

    const container = document.getElementById('conteudo-dinamico');
    container.innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></div>';

    const ctrl = new AbortController();
    _fetchAtivo = ctrl;

    fetch(arquivo, { cache: 'no-store', signal: ctrl.signal })
        .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.text(); })
        .then(html => {
            if (ctrl.signal.aborted) return;
            _fetchAtivo = null;
            container.innerHTML = html;
            // innerHTML não executa <script> — reexecutar manualmente
            container.querySelectorAll('script').forEach(orig => {
                const s = document.createElement('script');
                s.textContent = orig.textContent;
                document.head.appendChild(s);
                document.head.removeChild(s);
            });
        })
        .catch(err => {
            _fetchAtivo = null;
            if (err.name === 'AbortError') return;
            container.innerHTML = `<div class="alert alert-danger m-3">Erro ao carregar a página.<br><small>${err.message}</small></div>`;
        });
}

window.addEventListener('popstate', (e) => {
    const pagina = e.state?.page || location.hash.replace('#', '') || 'pagina-inicial';
    carregarPagina(pagina, false);
});

document.addEventListener('DOMContentLoaded', () => {
    const pagina = history.state?.page
        || location.hash.replace('#', '')
        || 'pagina-inicial';
    carregarPagina(pagina, false);
});

// ── Slide-over global ─────────────────────────────────────────
function abrirSlideOver(titulo, corpo, { badge = '', badgeCor = '#3b82f6' } = {}) {
    const el = document.getElementById('slideOver');
    document.getElementById('slideOverTitulo').textContent = titulo;
    document.getElementById('slideOverBody').innerHTML = corpo;
    const badgeEl = document.getElementById('slideOverBadge');
    if (badge) {
        badgeEl.innerHTML = `<span class="badge rounded-pill px-2 py-1"
            style="background:${badgeCor}18;color:${badgeCor};font-size:0.72rem;">${badge}</span>`;
    } else {
        badgeEl.innerHTML = '';
    }
    el.classList.add('aberto');
    document.body.style.overflow = 'hidden';
}

function fecharSlideOver() {
    document.getElementById('slideOver').classList.remove('aberto');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => {
    if (e.key !== 'Escape') return;
    if (document.getElementById('modalPerfil')?.style.display          === 'flex') { fecharModalPerfil();    return; }
    if (document.getElementById('modalVisualizarArquivo').style.display === 'flex') { fecharModalVisualizar(); return; }
    if (document.getElementById('modalEnvioTarefa').style.display       === 'flex') { fecharModalEnvio();      return; }
    if (document.getElementById('modalEdicaoTarefa').style.display      === 'flex') { fecharModalEdicao();     return; }
    fecharSlideOver();
});

// ── Projetos ──────────────────────────────────────────────────
function abrirDetalhesProjeto(tr) {
    const titulo      = tr.dataset.titulo;
    const tipo        = tr.dataset.tipo || '—';
    const funcao      = tr.dataset.funcao;
    const carga       = tr.dataset.carga;
    const status      = tr.dataset.status;
    const orientador  = tr.dataset.orientador || '—';
    const area        = tr.dataset.area || '';
    const descricao   = tr.dataset.descricao || '';
    const dataInicio  = tr.dataset.dataInicio || '';
    const dataFim     = tr.dataset.dataFim || '';
    const participantes = JSON.parse(tr.dataset.participantes || '[]');

    const ativo = status === 'ativo';
    const statusStyle = ativo ? 'background:#dcfce7;color:#16a34a;' : 'background:#f1f5f9;color:#64748b;';
    const statusIco   = ativo ? 'bi-check-circle' : 'bi-check2-all';
    const statusLabel = ativo ? 'Ativo' : 'Concluído';

    const orientadores = participantes.filter(p => p.funcao.toLowerCase().includes('orientador'));
    const alunos       = participantes.filter(p => !p.funcao.toLowerCase().includes('orientador'));

    const renderMembro = (p, cor) => `
        <div class="d-flex align-items-center gap-2 py-2 border-bottom">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 fw-semibold"
                 style="width:30px;height:30px;background:${cor}18;color:${cor};font-size:0.75rem;">
                ${p.nome.charAt(0).toUpperCase()}
            </div>
            <div>
                <div style="font-size:0.85rem;font-weight:500;">${p.nome}</div>
                <div style="font-size:0.75rem;color:#94a3b8;">${p.funcao}</div>
            </div>
        </div>`;

    const secao = (label, lista, cor) => lista.length ? `
        <div class="so-label mb-1 mt-2">${label}</div>
        ${lista.map(p => renderMembro(p, cor)).join('')}` : '';

    const partHtml = participantes.length
        ? secao('Orientação', orientadores, '#f59e0b') + secao('Membros', alunos, '#3b82f6')
        : '<span class="text-muted small">Sem participantes cadastrados.</span>';

    const periodoHtml = (dataInicio || dataFim) ? `
        <div class="col-6">
            <div class="so-campo mb-0">
                <div class="so-label">Início</div>
                <div class="so-valor"><i class="bi bi-calendar2 me-1 text-muted"></i>${dataInicio || '—'}</div>
            </div>
        </div>
        <div class="col-6">
            <div class="so-campo mb-0">
                <div class="so-label">Término</div>
                <div class="so-valor"><i class="bi bi-calendar2-check me-1 text-muted"></i>${dataFim || '—'}</div>
            </div>
        </div>` : '';

    abrirSlideOver(titulo, `
        <div class="so-campo">
            <div class="so-label">Status</div>
            <div class="so-valor">
                <span class="badge rounded-pill px-2 py-1" style="${statusStyle}font-size:0.8rem;">
                    <i class="bi ${statusIco} me-1"></i>${statusLabel}
                </span>
            </div>
        </div>
        <hr class="so-divider">
        <div class="row g-3">
            <div class="col-6">
                <div class="so-campo mb-0">
                    <div class="so-label">Tipo</div>
                    <div class="so-valor">${tipo}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="so-campo mb-0">
                    <div class="so-label">Sua Função</div>
                    <div class="so-valor">${funcao}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="so-campo mb-0">
                    <div class="so-label">Carga Horária</div>
                    <div class="so-valor"><i class="bi bi-clock me-1 text-muted"></i>${carga}h</div>
                </div>
            </div>
            <div class="col-6">
                <div class="so-campo mb-0">
                    <div class="so-label">Orientador</div>
                    <div class="so-valor">${orientador}</div>
                </div>
            </div>
            ${periodoHtml}
        </div>
        ${area ? `<hr class="so-divider"><div class="so-campo"><div class="so-label">Área</div><div class="so-valor">${area}</div></div>` : ''}
        ${descricao ? `<hr class="so-divider"><div class="so-campo"><div class="so-label">Descrição</div><div class="so-valor">${descricao.replace(/\n/g, '<br>')}</div></div>` : ''}
        <hr class="so-divider">
        <div class="so-campo">
            <div class="so-label">Participantes</div>
            <div class="so-valor mt-1">${partHtml}</div>
        </div>`, {
        badge: `<i class="bi bi-folder me-1"></i>${tipo}`,
        badgeCor: ativo ? '#22c55e' : '#94a3b8'
    });
}

// ── Helper: prazo passou considerando data + hora ─────────────
function _prazoPassou(tr) {
    const p = (tr.dataset.data || '').split('-');
    if (p.length < 3) return false;
    const hoje = new Date(); hoje.setHours(0, 0, 0, 0);
    const prazo = new Date(parseInt(p[0]), parseInt(p[1]) - 1, parseInt(p[2]));
    if (prazo < hoje) return true;
    const hora = tr.dataset.hora;
    if (hora) {
        const partes = hora.split(':');
        const prazoComHora = new Date(parseInt(p[0]), parseInt(p[1]) - 1, parseInt(p[2]),
                                      parseInt(partes[0]), parseInt(partes[1]));
        return new Date() > prazoComHora;
    }
    return false;
}

// ── Tarefas ───────────────────────────────────────────────────
let _tarefaAtual = null;

function toggleConcluido(btn) {
    const tr = btn.closest('tr');
    const id = tr.dataset.id;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

    fetch('pages-aluno/toggle-concluido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(id)
    })
    .then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    })
    .then(data => {
        if (!data.ok) { btn.disabled = false; if (data.erro) alert(data.erro); return; }
        const concluido  = data.concluido;
        const passou     = _prazoPassou(tr);
        const docRefazer = tr.dataset.docRefazer === '1';

        let statusKey, statusLabel, statusClass, statusStyle = '';
        if (concluido && !docRefazer) {
            statusKey = 'concluido'; statusLabel = 'Concluído'; statusClass = 'bg-success text-white';
        } else if (passou) {
            statusKey = 'nao_concluido'; statusLabel = 'Não Concluído'; statusClass = 'bg-danger text-white';
        } else if (docRefazer) {
            statusKey = 'corrigir'; statusLabel = 'Corrigir'; statusClass = 'text-white'; statusStyle = 'background:#ea580c;';
        } else {
            statusKey = 'pendente'; statusLabel = 'Pendente'; statusClass = 'bg-warning text-dark';
        }

        const ant = tr.dataset.status;
        tr.dataset.status = statusKey;
        tr.dataset.concluido = (concluido && !docRefazer) ? '1' : '0';

        const badgeTarefa = tr.querySelector('.badge-status');
        badgeTarefa.className = 'badge badge-status ' + statusClass;
        badgeTarefa.textContent = statusLabel;
        badgeTarefa.style.cssText = statusStyle;

        const statIds = { pendente: 'statPendentes', nao_concluido: 'statNaoConcluidos', concluido: 'statConcluidos', corrigir: 'statCorrigir' };
        const elAnt = document.getElementById(statIds[ant]);
        const elNov = document.getElementById(statIds[statusKey]);
        if (elAnt) elAnt.textContent = Math.max(0, parseInt(elAnt.textContent) - 1);
        if (elNov) elNov.textContent = parseInt(elNov.textContent) + 1;

        btn.disabled = false;
        atualizarBotoesTarefa(tr);
    })
    .catch(err => { btn.disabled = false; console.error('Erro toggleConcluido:', err); });
}

function atualizarBotoesTarefa(tr) {
    const temArquivo   = getArquivos(tr).length > 0;
    const concluido    = tr.dataset.concluido === '1';
    const docRefazer   = tr.dataset.docRefazer === '1';
    const profProcessou = tr.dataset.profProcessou === '1';
    const prazoPastou  = _prazoPassou(tr);
    const td = tr.querySelector('td:last-child');

    const eyeBtn = temArquivo
        ? `<button class="btn btn-sm btn-outline-primary ms-1" onclick="event.stopPropagation();abrirModalEdicao(this.closest('tr'))" title="Ver arquivo"><i class="bi bi-eye"></i></button>`
        : `<button class="btn btn-sm btn-outline-secondary ms-1 opacity-50" onclick="event.stopPropagation()" style="cursor:default;" title="Nenhum arquivo anexado"><i class="bi bi-eye"></i></button>`;

    if (docRefazer) {
        td.innerHTML = `<button class="btn btn-sm btn-outline-success" onclick="event.stopPropagation();reenviarCorrecao(this)" title="Reenviar documento"><i class="bi bi-check-lg"></i></button>`
            + `<button class="btn btn-sm btn-outline-warning ms-1" onclick="event.stopPropagation();abrirModalEnvio(this.closest('tr'))" title="Enviar novo arquivo"><i class="bi bi-pencil"></i></button>`;
    } else if (concluido && prazoPastou) {
        td.innerHTML = `<button class="btn btn-sm btn-outline-secondary opacity-50" onclick="event.stopPropagation()" style="cursor:default;" title="Prazo encerrado, não é possível desfazer"><i class="bi bi-arrow-counterclockwise"></i></button>` + eyeBtn;
    } else if (concluido && profProcessou) {
        td.innerHTML = `<button class="btn btn-sm btn-outline-secondary opacity-50" onclick="event.stopPropagation()" style="cursor:default;" title="Avaliado pelo professor, não é possível desfazer"><i class="bi bi-arrow-counterclockwise"></i></button>` + eyeBtn;
    } else if (concluido) {
        td.innerHTML = `<button class="btn btn-sm btn-outline-warning" onclick="event.stopPropagation();toggleConcluido(this)" title="Desfazer conclusão"><i class="bi bi-arrow-counterclockwise"></i></button>` + eyeBtn;
    } else if (prazoPastou) {
        td.innerHTML = `<button class="btn btn-sm btn-outline-secondary opacity-50" onclick="event.stopPropagation()" disabled title="Prazo encerrado"><i class="bi bi-lock-fill"></i></button>`
            + `<button class="btn btn-sm btn-outline-secondary ms-1 opacity-50" onclick="event.stopPropagation()" disabled title="Prazo encerrado"><i class="bi bi-paperclip"></i></button>`;
    } else if (temArquivo) {
        td.innerHTML = `<button class="btn btn-sm btn-outline-success" onclick="event.stopPropagation();toggleConcluido(this)" title="Marcar como concluído"><i class="bi bi-check-lg"></i></button>`
            + `<button class="btn btn-sm btn-outline-warning ms-1" onclick="event.stopPropagation();abrirModalEnvio(this.closest('tr'))" title="Editar arquivo"><i class="bi bi-pencil"></i></button>`;
    } else {
        td.innerHTML = `<button class="btn btn-sm btn-outline-success" onclick="event.stopPropagation();toggleConcluido(this)" title="Marcar como concluído"><i class="bi bi-check-lg"></i></button>`
            + `<button class="btn btn-sm btn-outline-primary ms-1" onclick="event.stopPropagation();abrirModalEnvio(this.closest('tr'))" title="Enviar arquivo"><i class="bi bi-paperclip"></i></button>`;
    }
}

function abrirDetalheTarefa(tr) {
    const titulo       = tr.querySelector('td.fw-medium')?.textContent || '';
    const data         = tr.querySelector('td:nth-child(3)')?.textContent || '';
    const hora         = tr.querySelector('td:nth-child(4)')?.textContent || '—';
    const badgeEl      = tr.querySelector('.badge-status');
    const statusLabel  = badgeEl?.textContent || '';
    const cls          = badgeEl?.className || '';
    const projeto      = tr.dataset.projeto || '—';
    const arquivos     = getArquivos(tr);

    let statusStyle, statusIco;
    if (cls.includes('bg-success'))     { statusStyle = 'background:#dcfce7;color:#16a34a;'; statusIco = 'bi-check-circle'; }
    else if (cls.includes('bg-danger')) { statusStyle = 'background:#fee2e2;color:#dc2626;'; statusIco = 'bi-x-circle'; }
    else                                { statusStyle = 'background:#fef9c3;color:#a16207;'; statusIco = 'bi-hourglass-split'; }

    const descFull = tr.dataset.descricao || '';

    const arquivoHtml = arquivos.length
        ? `<hr class="so-divider">
           <div class="so-campo">
               <div class="so-label">Arquivos enviados</div>
               <div class="so-valor mt-1">
                   ${arquivos.map(a => {
                       return `<div class="d-flex align-items-center gap-2 p-2 rounded mb-1" style="background:#f8fafc;border:1px solid #e2e8f0;">
                           <i class="bi bi-file-earmark text-primary"></i>
                           <span class="flex-grow-1 text-truncate" style="font-size:0.85rem;">${a.nome}</span>
                           <button class="btn btn-sm btn-outline-primary rounded-circle p-0 d-flex align-items-center justify-content-center flex-shrink-0"
                                   style="width:28px;height:28px;"
                                   data-caminho="${a.caminho}" data-nome="${a.nome}"
                                   onclick="abrirModalVisualizar(this.dataset.caminho,this.dataset.nome)" title="Visualizar">
                               <i class="bi bi-eye" style="font-size:0.75rem;"></i>
                           </button>
                           <a href="${a.caminho}" download="${a.nome}"
                              class="btn btn-sm btn-outline-secondary rounded-circle p-0 d-flex align-items-center justify-content-center flex-shrink-0"
                              style="width:28px;height:28px;" title="Baixar">
                               <i class="bi bi-download" style="font-size:0.75rem;"></i>
                           </a>
                       </div>`;
                   }).join('')}
               </div>
           </div>`
        : '';

    abrirSlideOver(titulo, `
        <div class="so-campo">
            <div class="so-label">Status</div>
            <div class="so-valor">
                <span class="badge rounded-pill px-2 py-1" style="${statusStyle}font-size:0.8rem;">
                    <i class="bi ${statusIco} me-1"></i>${statusLabel}
                </span>
            </div>
        </div>
        <hr class="so-divider">
        <div class="so-campo">
            <div class="so-label">Projeto</div>
            <div class="so-valor"><i class="bi bi-folder2 me-1 text-muted"></i>${projeto}</div>
        </div>
        <hr class="so-divider">
        <div class="row g-3">
            <div class="col-6">
                <div class="so-campo mb-0">
                    <div class="so-label">Data</div>
                    <div class="so-valor"><i class="bi bi-calendar2 me-1 text-muted"></i>${data}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="so-campo mb-0">
                    <div class="so-label">Hora</div>
                    <div class="so-valor"><i class="bi bi-clock me-1 text-muted"></i>${hora !== '—' ? hora : 'Não definida'}</div>
                </div>
            </div>
        </div>
        <hr class="so-divider">
        <div class="so-campo">
            <div class="so-label">Descrição</div>
            <div class="so-valor">${descFull ? descFull.replace(/\n/g,'<br>') : '<span class="text-muted">Sem descrição.</span>'}</div>
        </div>
        ${arquivoHtml}`, {
        badge: '<i class="bi bi-check2-square me-1"></i>Tarefa',
        badgeCor: '#3b82f6'
    });
}

function abrirDetalheCronograma(tr) {
    const titulo      = tr.querySelector('td.fw-medium')?.textContent || '';
    const data        = tr.querySelector('td.fw-bold')?.textContent   || '';
    const hora        = tr.querySelector('td:nth-child(2)')?.textContent || '—';
    const badgeEl     = tr.querySelector('.badge-status');
    const statusLabel = badgeEl?.textContent || '';
    const cls         = badgeEl?.className   || '';
    const tipo        = tr.dataset.tipo || 'tarefa';
    const descricao   = tr.dataset.descricao || '';
    const projeto     = tr.dataset.projeto || '—';
    const arquivos    = getArquivos(tr);

    let statusStyle, statusIco;
    if (cls.includes('bg-success'))     { statusStyle = 'background:#dcfce7;color:#16a34a;'; statusIco = 'bi-check-circle'; }
    else if (cls.includes('bg-danger')) { statusStyle = 'background:#fee2e2;color:#dc2626;'; statusIco = 'bi-x-circle'; }
    else                                { statusStyle = 'background:#fef9c3;color:#a16207;'; statusIco = 'bi-hourglass-split'; }

    const tipoHtml = tipo === 'tarefa'
        ? `<span class="badge bg-light text-dark border"><i class="bi bi-check2-square me-1"></i>Tarefa</span>`
        : `<span class="badge bg-light text-dark border"><i class="bi bi-calendar-event me-1"></i>Evento</span>`;

    const arquivoHtml = arquivos.length
        ? `<hr class="so-divider">
           <div class="so-campo">
               <div class="so-label">Arquivos enviados</div>
               <div class="so-valor mt-1">
                   ${arquivos.map(a => {
                       return `<div class="d-flex align-items-center gap-2 p-2 rounded mb-1" style="background:#f8fafc;border:1px solid #e2e8f0;">
                           <i class="bi bi-file-earmark text-primary"></i>
                           <span class="flex-grow-1 text-truncate" style="font-size:0.85rem;">${a.nome}</span>
                           <button class="btn btn-sm btn-outline-primary rounded-circle p-0 d-flex align-items-center justify-content-center flex-shrink-0"
                                   style="width:28px;height:28px;"
                                   data-caminho="${a.caminho}" data-nome="${a.nome}"
                                   onclick="abrirModalVisualizar(this.dataset.caminho,this.dataset.nome)" title="Visualizar">
                               <i class="bi bi-eye" style="font-size:0.75rem;"></i>
                           </button>
                           <a href="${a.caminho}" download="${a.nome}"
                              class="btn btn-sm btn-outline-secondary rounded-circle p-0 d-flex align-items-center justify-content-center flex-shrink-0"
                              style="width:28px;height:28px;" title="Baixar">
                               <i class="bi bi-download" style="font-size:0.75rem;"></i>
                           </a>
                       </div>`;
                   }).join('')}
               </div>
           </div>`
        : '';

    const badgeTipo = tipo === 'tarefa'
        ? { label: '<i class="bi bi-check2-square me-1"></i>Tarefa', cor: '#3b82f6' }
        : { label: '<i class="bi bi-calendar-event me-1"></i>Evento', cor: '#7c3aed' };

    abrirSlideOver(titulo, `
        <div class="so-campo">
            <div class="so-label">Status</div>
            <div class="so-valor">
                <span class="badge rounded-pill px-2 py-1" style="${statusStyle}font-size:0.8rem;">
                    <i class="bi ${statusIco} me-1"></i>${statusLabel}
                </span>
            </div>
        </div>
        <hr class="so-divider">
        <div class="so-campo">
            <div class="so-label">Projeto</div>
            <div class="so-valor"><i class="bi bi-folder2 me-1 text-muted"></i>${projeto}</div>
        </div>
        <hr class="so-divider">
        <div class="row g-3">
            <div class="col-4">
                <div class="so-campo mb-0">
                    <div class="so-label">Data</div>
                    <div class="so-valor"><i class="bi bi-calendar2 me-1 text-muted"></i>${data}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="so-campo mb-0">
                    <div class="so-label">Hora</div>
                    <div class="so-valor"><i class="bi bi-clock me-1 text-muted"></i>${hora !== '—' ? hora : 'Não definida'}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="so-campo mb-0">
                    <div class="so-label">Tipo</div>
                    <div class="so-valor">${tipoHtml}</div>
                </div>
            </div>
        </div>
        <hr class="so-divider">
        <div class="so-campo">
            <div class="so-label">Descrição</div>
            <div class="so-valor">${descricao ? descricao.replace(/\n/g,'<br>') : '<span class="text-muted">Sem descrição.</span>'}</div>
        </div>
        ${arquivoHtml}`, {
        badge: badgeTipo.label,
        badgeCor: badgeTipo.cor
    });
}

// ── Helpers multi-arquivo ─────────────────────────────────────
function getArquivos(tr) {
    try { return JSON.parse(tr.dataset.arquivos || '[]'); } catch(e) { return []; }
}
function setArquivos(tr, arr) { tr.dataset.arquivos = JSON.stringify(arr); }

// ── Modais de upload ──────────────────────────────────────────
function abrirModalEnvio(tr) {
    _tarefaAtual = tr;
    const isCorrigir = tr.dataset.docRefazer === '1';
    document.getElementById('inputArquivo').value = '';
    document.getElementById('listaArquivosNovos').style.display = 'none';
    document.getElementById('itensArquivosNovos').innerHTML = '';
    document.getElementById('btnEnviarArquivo').disabled = false;
    document.getElementById('tituloModalEnvio').innerHTML = '<i class="bi bi-paperclip me-2" style="color:#3b82f6;"></i>Enviar Atividade';
    document.getElementById('btnEnviarArquivo').innerHTML = '<i class="bi bi-floppy me-1"></i>Salvar Rascunho';
    document.getElementById('btnEnviarArquivo').className = 'btn btn-primary';
    _renderizarArquivosExistentes(getArquivos(tr), false);
    document.getElementById('modalEnvioTarefa').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function fecharModalEnvio() {
    document.getElementById('modalEnvioTarefa').style.display = 'none';
    document.body.style.overflow = '';
}
function reenviarCorrecao(btn) {
    const tr = btn.closest('tr');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
    const fd = new URLSearchParams();
    fd.append('id_projeto', tr.dataset.idProjeto || '');
    fd.append('titulo', tr.dataset.titulo || '');
    fetch('pages-aluno/reenviar-correcao.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { btn.disabled = false; alert(data.erro || 'Erro ao reenviar'); return; }
            tr.dataset.docRefazer = '0';
            tr.dataset.status = 'concluido';
            const badge = tr.querySelector('.badge-status');
            badge.className = 'badge badge-status bg-success text-white';
            badge.textContent = 'Concluído';
            badge.style.cssText = '';
            const sc = document.getElementById('statCorrigir');
            const sk = document.getElementById('statConcluidos');
            if (sc) sc.textContent = Math.max(0, parseInt(sc.textContent) - 1);
            if (sk) sk.textContent = parseInt(sk.textContent) + 1;
            if (tr.closest('#tabelaTarefas')) atualizarBotoesTarefa(tr);
            else atualizarBotoesCronograma(tr);
        })
        .catch(() => { btn.disabled = false; alert('Erro de rede'); });
}
function _renderizarArquivosExistentes(arquivos, somenteLeitura) {
    const container = document.getElementById('listaArquivosExistentes');
    const lista = document.getElementById('itensArquivosExistentes');
    if (!arquivos || arquivos.length === 0) { container.style.display = 'none'; lista.innerHTML = ''; return; }
    container.style.display = '';
    lista.innerHTML = arquivos.map(a => {
        const btnRemove = somenteLeitura ? '' :
            `<button class="btn btn-sm btn-link text-danger p-0" onclick="_removerArquivoExistente(${a.id})" title="Remover"><i class="bi bi-x-lg"></i></button>`;
        return `<div class="d-flex align-items-center gap-2 p-2 rounded mb-1" style="background:#f8fafc;border:1px solid #e2e8f0;">
            <i class="bi bi-file-earmark text-primary"></i>
            <span class="flex-grow-1 text-truncate" style="font-size:0.85rem;">${_escHtml(a.nome)}</span>
            <button class="btn btn-sm btn-link text-primary p-0"
                    data-caminho="${a.caminho}" data-nome="${_escHtml(a.nome)}"
                    onclick="abrirModalVisualizar(this.dataset.caminho,this.dataset.nome)" title="Visualizar"><i class="bi bi-eye"></i></button>
            ${btnRemove}
        </div>`;
    }).join('');
}
function _removerArquivoExistente(idProducao) {
    fetch('pages-aluno/remover-arquivo-tarefa.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_producao=' + encodeURIComponent(idProducao)
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            const arr = getArquivos(_tarefaAtual).filter(a => a.id !== idProducao);
            setArquivos(_tarefaAtual, arr);
            _renderizarArquivosExistentes(arr, false);
            if (_tarefaAtual.closest('#tabelaTarefas')) atualizarBotoesTarefa(_tarefaAtual);
            else atualizarBotoesCronograma(_tarefaAtual);
            if (arr.length === 0) fecharModalEnvio();
        } else { alert('Erro ao remover arquivo.'); }
    })
    .catch(() => alert('Erro ao remover arquivo.'));
}
function selecionarArquivo(input) {
    const files = Array.from(input.files);
    if (!files.length) return;
    const container = document.getElementById('listaArquivosNovos');
    document.getElementById('itensArquivosNovos').innerHTML = files.map(f =>
        `<div class="d-flex align-items-center gap-2 p-2 rounded mb-1" style="background:#f0f9ff;border:1px solid #bae6fd;">
            <i class="bi bi-file-earmark text-info"></i>
            <span class="flex-grow-1 text-truncate" style="font-size:0.85rem;">${_escHtml(f.name)}</span>
        </div>`
    ).join('');
    container.style.display = '';
}
function handleFileDrop(e) {
    const input = document.getElementById('inputArquivo');
    const dt = new DataTransfer();
    Array.from(e.dataTransfer.files).forEach(f => dt.items.add(f));
    input.files = dt.files;
    selecionarArquivo(input);
}
async function enviarArquivoTarefa() {
    const files = Array.from(document.getElementById('inputArquivo').files);
    if (!files.length) { alert('Selecione ao menos um arquivo.'); return; }
    const btn = document.getElementById('btnEnviarArquivo');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Enviando...';
    let arquivos = getArquivos(_tarefaAtual);
    const erros = [];
    for (const file of files) {
        if (file.size > 15 * 1024 * 1024) { erros.push(file.name + ': muito grande (máx. 15 MB)'); continue; }
        const fd = new FormData();
        fd.append('id', _tarefaAtual.dataset.id);
        fd.append('titulo', _tarefaAtual.dataset.titulo);
        fd.append('id_projeto', _tarefaAtual.dataset.idProjeto || '');
        fd.append('arquivo', file);
        try {
            const res = await fetch('pages-aluno/upload-tarefa.php', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.ok) arquivos.push({ id: data.id_producao, caminho: data.caminho, nome: data.nome });
            else erros.push(file.name + ': ' + (data.erro || 'Erro'));
        } catch(e) { erros.push(file.name + ': erro de rede'); }
    }
    setArquivos(_tarefaAtual, arquivos);
    // Novo arquivo enviado: limpa o flag de refazer
    if (arquivos.length > 0) _tarefaAtual.dataset.docRefazer = '0';
    if (_tarefaAtual.closest('#tabelaTarefas')) atualizarBotoesTarefa(_tarefaAtual);
    else atualizarBotoesCronograma(_tarefaAtual);
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-floppy me-1"></i>Salvar Rascunho';
    if (erros.length) alert('Alguns arquivos não foram enviados:\n' + erros.join('\n'));
    else fecharModalEnvio();
}

function abrirModalEdicao(tr) {
    _tarefaAtual = tr;
    const arquivos = getArquivos(tr);
    document.getElementById('tituloModalEdicao').innerHTML = '<i class="bi bi-eye me-2 text-primary"></i>Ver Arquivos';
    document.getElementById('labelArquivoEdit').textContent = 'ARQUIVOS ENVIADOS';
    document.getElementById('dropZoneEdit').style.display = 'none';
    document.getElementById('arquivoSubstituto').style.display = 'none';
    document.getElementById('obsEdicaoWrap').style.display = 'none';
    document.getElementById('itensArquivosEdit').innerHTML = arquivos.map(a => {
        return `<div class="d-flex align-items-center gap-2 p-2 rounded mb-1" style="background:#f8fafc;border:1px solid #e2e8f0;">
            <i class="bi bi-file-earmark text-primary fs-5"></i>
            <span class="flex-grow-1 text-truncate" style="font-size:0.85rem;font-weight:500;">${_escHtml(a.nome)}</span>
            <button class="btn btn-sm btn-outline-primary rounded-circle p-0 d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width:28px;height:28px;"
                    data-caminho="${a.caminho}" data-nome="${_escHtml(a.nome)}"
                    onclick="abrirModalVisualizar(this.dataset.caminho,this.dataset.nome)" title="Visualizar">
                <i class="bi bi-eye" style="font-size:0.75rem;"></i>
            </button>
            <a href="${a.caminho}" download="${_escHtml(a.nome)}"
               class="btn btn-sm btn-outline-secondary rounded-circle p-0 d-flex align-items-center justify-content-center flex-shrink-0"
               style="width:28px;height:28px;" title="Baixar">
                <i class="bi bi-download" style="font-size:0.75rem;"></i>
            </a>
        </div>`;
    }).join('') || '<p class="text-muted small mb-0">Nenhum arquivo enviado.</p>';
    document.getElementById('modalEdicaoTarefa').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function fecharModalEdicao() {
    document.getElementById('modalEdicaoTarefa').style.display = 'none';
    document.body.style.overflow = '';
}
const _EXTS_CODIGO = ['txt','js','ts','jsx','tsx','mjs','cjs',
    'py','php','rb','java','c','cpp','cc','h','hpp','cs','go','rs','swift','kt',
    'html','htm','css','scss','sass','less','xml','svg',
    'json','yaml','yml','toml','ini','conf','env',
    'sh','bash','zsh','bat','cmd','ps1',
    'sql','md','markdown','csv','log'];

function _escHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

function abrirModalVisualizar(caminho, nome) {
    document.getElementById('tituloArquivoVis').textContent = nome;
    const ext  = (nome.split('.').pop() || '').toLowerCase();
    const corpo = document.getElementById('corpoArquivoVis');
    const ctrlZoom = document.getElementById('controlesZoom');

    if (['jpg','jpeg','png','gif','webp'].includes(ext)) {
        _tipoVisualizador = 'imagem';
        _panX = 0; _panY = 0;
        ctrlZoom.style.display = 'flex';
        corpo.innerHTML = `<div id="_imgScroller" style="width:100%;height:100%;overflow:hidden;display:flex;align-items:center;justify-content:center;cursor:default;"><img id="_imgVis" src="${caminho}" style="max-width:100%;max-height:100%;object-fit:contain;user-select:none;-webkit-user-drag:none;"></div>`;
        _setZoom(100);
        _initImgPan();

    } else if (ext === 'pdf') {
        _tipoVisualizador = 'pdf';
        ctrlZoom.style.display = 'none';
        corpo.innerHTML = `<iframe src="${caminho}" style="width:100%;height:100%;border:0;display:block;"></iframe>`;

    } else if (_EXTS_CODIGO.includes(ext)) {
        _tipoVisualizador = 'codigo';
        ctrlZoom.style.display = 'flex';
        _setZoom(100);
        corpo.innerHTML = `<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="text-muted mt-2 small">Carregando...</p></div>`;
        fetch(caminho)
            .then(r => { if (!r.ok) throw new Error(); return r.text(); })
            .then(texto => {
                let conteudo = texto;
                if (ext === 'json') {
                    try { conteudo = JSON.stringify(JSON.parse(texto), null, 2); } catch(e) {}
                }
                const linhas = conteudo.split('\n');
                const nDigitos = String(linhas.length).length;
                const rows = linhas.map((linha, i) =>
                    `<tr>
                        <td style="user-select:none;padding:1px 12px 1px 8px;min-width:${nDigitos + 2}ch;
                                   text-align:right;color:#94a3b8;font-size:0.75rem;
                                   border-right:1px solid #e2e8f0;white-space:pre;">${i + 1}</td>
                        <td style="padding:1px 16px;white-space:pre;font-size:0.82rem;">${_escHtml(linha)}</td>
                    </tr>`
                ).join('');
                corpo.innerHTML = `
                    <div style="overflow:auto;height:100%;width:100%;">
                        <table style="font-family:'Courier New',Courier,monospace;border-collapse:collapse;width:100%;background:#fdfdfd;">
                            <tbody>${rows}</tbody>
                        </table>
                    </div>`;
            })
            .catch(() => {
                corpo.innerHTML = `<div class="text-center py-5 text-danger"><i class="bi bi-exclamation-circle fs-1 d-block mb-2"></i>Não foi possível carregar o arquivo.</div>`;
            });

    } else {
        _tipoVisualizador = 'outro';
        ctrlZoom.style.display = 'none';
        corpo.innerHTML = `<div class="text-center py-5">
            <i class="bi bi-file-earmark fs-1 text-muted mb-3 d-block"></i>
            <p class="text-muted mb-3">${_escHtml(nome)}</p>
            <a href="${caminho}" download class="btn btn-primary btn-sm"><i class="bi bi-download me-1"></i>Baixar arquivo</a>
        </div>`;
    }

    document.getElementById('modalVisualizarArquivo').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function fecharModalVisualizar() {
    document.getElementById('modalVisualizarArquivo').style.display = 'none';
    document.body.style.overflow = '';
}

let _zoomAtual = 100;
let _tipoVisualizador = '';
let _panX = 0, _panY = 0;
let _imgDrag = { active: false, sx: 0, sy: 0, spx: 0, spy: 0 };
const _PASSOS_ZOOM = [100, 110, 125, 150, 175, 200, 250, 300, 400];

function _applyImgTransform() {
    const img = document.getElementById('_imgVis');
    const scroller = document.getElementById('_imgScroller');
    if (!img) return;
    if (_zoomAtual === 100) {
        _panX = 0; _panY = 0;
        img.style.transform = '';
    } else {
        if (scroller) {
            const scale = _zoomAtual / 100;
            const maxX = Math.max(0, (img.clientWidth  * scale - scroller.clientWidth)  / 2);
            const maxY = Math.max(0, (img.clientHeight * scale - scroller.clientHeight) / 2);
            _panX = Math.max(-maxX, Math.min(maxX, _panX));
            _panY = Math.max(-maxY, Math.min(maxY, _panY));
        }
        img.style.transform = `translate(${_panX}px,${_panY}px) scale(${_zoomAtual / 100})`;
    }
}

function _initImgPan() {
    const scroller = document.getElementById('_imgScroller');
    if (!scroller) return;
    scroller.addEventListener('mousedown', function(e) {
        if (_zoomAtual <= 100) return;
        _imgDrag.active = true;
        _imgDrag.sx = e.clientX; _imgDrag.sy = e.clientY;
        _imgDrag.spx = _panX;   _imgDrag.spy = _panY;
        scroller.style.cursor = 'grabbing';
        e.preventDefault();
    });
}

document.addEventListener('mousemove', function(e) {
    if (!_imgDrag.active) return;
    _panX = _imgDrag.spx + (e.clientX - _imgDrag.sx);
    _panY = _imgDrag.spy + (e.clientY - _imgDrag.sy);
    _applyImgTransform();
});
document.addEventListener('mouseup', function() {
    if (!_imgDrag.active) return;
    _imgDrag.active = false;
    const s = document.getElementById('_imgScroller');
    if (s) s.style.cursor = _zoomAtual > 100 ? 'grab' : 'default';
});

function _setZoom(nivel) {
    _zoomAtual = Math.min(400, Math.max(100, nivel));
    const lbl = document.getElementById('zoomLevelLabel');
    if (lbl) lbl.textContent = _zoomAtual + '%';
    if (_tipoVisualizador === 'imagem') {
        _applyImgTransform();
        const scroller = document.getElementById('_imgScroller');
        if (scroller) scroller.style.cursor = _zoomAtual > 100 ? 'grab' : 'default';
    } else if (_tipoVisualizador === 'codigo') {
        document.querySelectorAll('#corpoArquivoVis td:last-child').forEach(td => td.style.fontSize = (0.82 * _zoomAtual / 100) + 'rem');
        document.querySelectorAll('#corpoArquivoVis td:first-child').forEach(td => td.style.fontSize = (0.75 * _zoomAtual / 100) + 'rem');
    }
}
function zoomVisualizar(delta) {
    if (delta > 0) { const p = _PASSOS_ZOOM.find(x => x > _zoomAtual); _setZoom(p || 400); }
    else           { const p = [..._PASSOS_ZOOM].reverse().find(x => x < _zoomAtual); _setZoom(p || 25); }
}
function resetZoomVisualizar() { _panX = 0; _panY = 0; _setZoom(100); }

document.getElementById('corpoArquivoVis').addEventListener('wheel', function(e) {
    if (_tipoVisualizador !== 'imagem' && _tipoVisualizador !== 'codigo') return;
    e.preventDefault();
    zoomVisualizar(e.deltaY < 0 ? 1 : -1);
}, { passive: false });
function toggleDescricao(btn) {
    const tr = btn.closest('tr');
    const descRow = tr.nextElementSibling;
    const icon = btn.querySelector('i');
    const aberto = descRow.style.display !== 'none';
    descRow.style.display = aberto ? 'none' : '';
    icon.className = aberto ? 'bi bi-three-dots-vertical' : 'bi bi-x-lg';
}

function filtrarItens() {
    const busca  = (document.getElementById('filtroBusca')?.value || '').toLowerCase();
    const status = document.getElementById('filtroStatus')?.value || '';
    const linhas = document.querySelectorAll('#tabelaTarefas tbody tr[data-tipo]');
    let n = 0;
    linhas.forEach(tr => {
        const ok = (!busca  || tr.dataset.busca.includes(busca))
                && (!status || tr.dataset.status === status);
        tr.style.display = ok ? '' : 'none';
        const descRow = tr.nextElementSibling;
        if (descRow && descRow.classList.contains('tr-descricao') && !ok) {
            descRow.style.display = 'none';
            const ic = tr.querySelector('.btn-expandir i');
            if (ic) ic.className = 'bi bi-three-dots-vertical';
        }
        if (ok) n++;
    });
    const c = document.getElementById('contadorItens');
    if (c) c.textContent = n + ' resultado(s)';
}

// ── Cronograma ────────────────────────────────────────────────
function toggleDescCron(btn) {
    const tr = btn.closest('tr');
    const descRow = tr.nextElementSibling;
    const icon = btn.querySelector('i');
    const aberto = descRow.style.display !== 'none';
    descRow.style.display = aberto ? 'none' : '';
    icon.className = aberto ? 'bi bi-three-dots-vertical' : 'bi bi-x-lg';
}

function toggleCronograma(btn) {
    const tr = btn.closest('tr');
    const id = tr.dataset.id;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

    fetch('pages-aluno/toggle-concluido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(id)
    })
    .then(r => r.json())
    .then(data => {
        if (!data.ok) { btn.disabled = false; if (data.erro) alert(data.erro); return; }
        const concluido  = data.concluido;
        const passou     = _prazoPassou(tr);
        const docRefazer = tr.dataset.docRefazer === '1';

        let statusKey, statusLabel, statusClass, statusStyle = '';
        if (concluido && !docRefazer) {
            statusKey = 'concluido'; statusLabel = 'Concluído'; statusClass = 'bg-success text-white';
        } else if (passou) {
            statusKey = 'nao_concluido'; statusLabel = 'Não Concluído'; statusClass = 'bg-danger text-white';
        } else if (docRefazer) {
            statusKey = 'corrigir'; statusLabel = 'Corrigir'; statusClass = 'text-white'; statusStyle = 'background:#ea580c;';
        } else {
            statusKey = 'proximo'; statusLabel = 'Pendente'; statusClass = 'bg-warning text-dark';
        }

        const ant = tr.dataset.status;
        tr.dataset.status    = statusKey;
        tr.dataset.concluido = (concluido && !docRefazer) ? '1' : '0';

        const badge = tr.querySelector('.badge-status');
        badge.className = 'badge badge-status ' + statusClass;
        badge.textContent = statusLabel;
        badge.style.cssText = statusStyle;

        const statIds = { proximo: 'statProximos', nao_concluido: 'statNaoConcluidos', concluido: 'statConcluidos', corrigir: 'statCorrigir' };
        const elAnt = document.getElementById(statIds[ant]);
        const elNov = document.getElementById(statIds[statusKey]);
        if (elAnt) elAnt.textContent = Math.max(0, parseInt(elAnt.textContent) - 1);
        if (elNov) elNov.textContent = parseInt(elNov.textContent) + 1;

        atualizarBotoesCronograma(tr);
    })
    .catch(() => { btn.disabled = false; });
}

function atualizarBotoesCronograma(tr) {
    const temArquivo    = getArquivos(tr).length > 0;
    const concluido     = tr.dataset.concluido === '1';
    const docRefazer    = tr.dataset.docRefazer === '1';
    const profProcessou = tr.dataset.profProcessou === '1';
    const temDescricao  = tr.dataset.temDescricao === '1';
    const prazoPastou   = _prazoPassou(tr);
    const td = tr.querySelector('td:last-child');

    const eyeBtn = temArquivo
        ? `<button class="btn btn-sm btn-outline-primary ms-1" onclick="event.stopPropagation();abrirModalEdicao(this.closest('tr'))" title="Ver arquivo"><i class="bi bi-eye"></i></button>`
        : `<button class="btn btn-sm btn-outline-secondary ms-1 opacity-50" onclick="event.stopPropagation()" style="cursor:default;" title="Nenhum arquivo anexado"><i class="bi bi-eye"></i></button>`;

    let acoes = '';
    if (docRefazer) {
        acoes = `<button class="btn btn-sm btn-outline-success" onclick="event.stopPropagation();reenviarCorrecao(this)" title="Reenviar documento"><i class="bi bi-check-lg"></i></button>`
              + `<button class="btn btn-sm btn-outline-warning ms-1" onclick="event.stopPropagation();abrirModalEnvio(this.closest('tr'))" title="Enviar novo arquivo"><i class="bi bi-pencil"></i></button>`;
    } else if (concluido && prazoPastou) {
        acoes = `<button class="btn btn-sm btn-outline-secondary opacity-50" onclick="event.stopPropagation()" style="cursor:default;" title="Prazo encerrado, não é possível desfazer"><i class="bi bi-arrow-counterclockwise"></i></button>` + eyeBtn;
    } else if (concluido && profProcessou) {
        acoes = `<button class="btn btn-sm btn-outline-secondary opacity-50" onclick="event.stopPropagation()" style="cursor:default;" title="Avaliado pelo professor, não é possível desfazer"><i class="bi bi-arrow-counterclockwise"></i></button>` + eyeBtn;
    } else if (concluido) {
        acoes = `<button class="btn btn-sm btn-outline-warning" onclick="event.stopPropagation();toggleCronograma(this)" title="Desfazer conclusão"><i class="bi bi-arrow-counterclockwise"></i></button>` + eyeBtn;
    } else if (prazoPastou) {
        acoes = `<button class="btn btn-sm btn-outline-secondary opacity-50" onclick="event.stopPropagation()" disabled title="Prazo encerrado"><i class="bi bi-lock-fill"></i></button>`
              + `<button class="btn btn-sm btn-outline-secondary ms-1 opacity-50" onclick="event.stopPropagation()" disabled title="Prazo encerrado"><i class="bi bi-paperclip"></i></button>`;
    } else if (temArquivo) {
        acoes = `<button class="btn btn-sm btn-outline-success" onclick="event.stopPropagation();toggleCronograma(this)" title="Marcar como concluído"><i class="bi bi-check-lg"></i></button>`
              + `<button class="btn btn-sm btn-outline-warning ms-1" onclick="event.stopPropagation();abrirModalEnvio(this.closest('tr'))" title="Editar arquivo"><i class="bi bi-pencil"></i></button>`;
    } else {
        acoes = `<button class="btn btn-sm btn-outline-success" onclick="event.stopPropagation();toggleCronograma(this)" title="Marcar como concluído"><i class="bi bi-check-lg"></i></button>`
              + `<button class="btn btn-sm btn-outline-primary ms-1" onclick="event.stopPropagation();abrirModalEnvio(this.closest('tr'))" title="Enviar arquivo"><i class="bi bi-paperclip"></i></button>`;
    }

    if (temDescricao) {
        const descRow = tr.nextElementSibling;
        const descAberta = descRow && descRow.classList.contains('tr-descricao') && descRow.style.display !== 'none';
        acoes += `<button class="btn btn-sm btn-outline-secondary ms-1 btn-expandir" onclick="toggleDescCron(this)" title="Ver descrição"><i class="bi bi-${descAberta ? 'x-lg' : 'three-dots-vertical'}"></i></button>`;
    }

    td.innerHTML = acoes;
}

function filtrarCronograma() {
    const busca  = (document.getElementById('filtroBusca')?.value || '').toLowerCase();
    const tipo   = document.getElementById('filtroTipo')?.value || '';
    const status = document.getElementById('filtroStatus')?.value || '';
    const linhas = document.querySelectorAll('#tabelaCronograma tbody tr[data-tipo]');
    let n = 0;
    linhas.forEach(tr => {
        const ok = (!busca  || tr.dataset.busca.includes(busca))
                && (!tipo   || tr.dataset.tipo   === tipo)
                && (!status || tr.dataset.status === status);
        tr.style.display = ok ? '' : 'none';
        const desc = tr.nextElementSibling;
        if (desc && desc.classList.contains('tr-descricao') && !ok) {
            desc.style.display = 'none';
            const ic = tr.querySelector('.btn-expandir i');
            if (ic) ic.className = 'bi bi-three-dots-vertical';
        }
        if (ok) n++;
    });
    const c = document.getElementById('contadorItens');
    if (c) c.textContent = n + ' resultado(s)';
}

// ── Registros ─────────────────────────────────────────────────
function abrirDetalheRegistro(el) {
    const statusStyle = el.dataset.statusStyle;
    const statusIco   = el.dataset.statusIco;
    const statusLabel = el.dataset.statusLabel;
    const desc        = el.dataset.desc;
    const hora        = el.dataset.hora;
    const projeto     = el.dataset.projeto || '—';

    abrirSlideOver(el.dataset.titulo, `
        <div class="so-campo">
            <div class="so-label">Status</div>
            <div class="so-valor">
                <span class="badge rounded-pill px-2 py-1" style="${statusStyle}font-size:0.8rem;">
                    <i class="bi ${statusIco} me-1"></i>${statusLabel}
                </span>
            </div>
        </div>
        <hr class="so-divider">
        <div class="so-campo">
            <div class="so-label">Projeto</div>
            <div class="so-valor"><i class="bi bi-folder2 me-1 text-muted"></i>${projeto}</div>
        </div>
        <hr class="so-divider">
        <div class="row g-3">
            <div class="col-6">
                <div class="so-campo mb-0">
                    <div class="so-label">Data</div>
                    <div class="so-valor"><i class="bi bi-calendar2 me-1 text-muted"></i>${el.dataset.data}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="so-campo mb-0">
                    <div class="so-label">Hora</div>
                    <div class="so-valor"><i class="bi bi-clock me-1 text-muted"></i>${hora !== '—' ? hora : 'Não definida'}</div>
                </div>
            </div>
        </div>
        <hr class="so-divider">
        <div class="so-campo">
            <div class="so-label">Descrição</div>
            <div class="so-valor">${desc ? desc.replace(/\n/g, '<br>') : '<span class="text-muted">Sem descrição.</span>'}</div>
        </div>`, {
        badge: `<i class="bi ${el.dataset.icone} me-1"></i>${el.dataset.label}`,
        badgeCor: el.dataset.cor
    });
}

function selecionarFiltroRegistro(btn) {
    document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    aplicarFiltroRegistros();
}

function aplicarFiltroRegistros() {
    const tipo = document.querySelector('.filtro-btn.active')?.dataset.tipo || 'todos';
    const txt  = (document.getElementById('buscaRegistro')?.value || '').toLowerCase();
    let n = 0;
    document.querySelectorAll('.registro-card').forEach(c => {
        const ok = (tipo === 'todos' || c.dataset.tipo === tipo)
                && (!txt || c.dataset.busca.includes(txt));
        c.style.display = ok ? '' : 'none';
        if (ok) n++;
    });
    const sem = document.getElementById('semResultados');
    if (sem) sem.classList.toggle('d-none', n > 0);
}

// ── Calendário / Página Inicial ───────────────────────────────
function showDay(el) {
    document.querySelectorAll('.calendar-day.selected').forEach(d => d.classList.remove('selected'));
    if (!el.classList.contains('today')) el.classList.add('selected');

    const itens = JSON.parse(el.dataset.itens || '[]');
    document.getElementById('modalDiaTitulo').textContent = el.dataset.titulo;

    if (!itens.length) {
        document.getElementById('modalDiaLista').innerHTML =
            '<p class="text-muted text-center py-2 mb-0">Nenhuma atividade neste dia.</p>';
    } else {
        const statusCor = { concluido:'#16a34a', atrasado:'#dc2626', proximo:'#d97706', futuro:'#2563eb' };
        const statusBg  = { concluido:'#dcfce7', atrasado:'#fee2e2', proximo:'#fef3c7', futuro:'#dbeafe' };

        const hojeMs = new Date(); hojeMs.setHours(0,0,0,0);

        document.getElementById('modalDiaLista').innerHTML = itens.map(item => {
            let pagina, label;
            if (item.data) {
                const p = item.data.split('-');
                const itemDate = new Date(+p[0], +p[1]-1, +p[2]);
                if (itemDate < hojeMs) {
                    // Prazo passou → sempre em Registros
                    pagina = 'participacoes';
                    label  = 'Ver em Registros';
                } else if (item.tipo === 'tarefa') {
                    pagina = 'tarefas';
                    label  = 'Ver tarefa';
                } else {
                    pagina = 'cronograma';
                    label  = 'Ver no cronograma';
                }
            } else {
                pagina = item.tipo === 'tarefa' ? 'tarefas' : 'cronograma';
                label  = item.tipo === 'tarefa' ? 'Ver tarefa' : 'Ver no cronograma';
            }
            const cor    = statusCor[item.status] || '#64748b';
            const bg     = statusBg[item.status]  || '#f1f5f9';
            return `
            <div onclick="bootstrap.Modal.getInstance(document.getElementById('modalDia')).hide(); carregarPagina('${pagina}')"
                 style="display:flex;align-items:center;justify-content:space-between;gap:10px;
                        border-radius:10px;border:1px solid ${cor}30;background:${bg};
                        padding:10px 12px;margin-bottom:7px;cursor:pointer;transition:opacity .15s;"
                 onmouseenter="this.style.opacity='.82'" onmouseleave="this.style.opacity='1'">
                <div style="min-width:0;flex:1;">
                    <div style="font-size:0.87rem;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        ${item.ico} ${_escHtml(item.titulo)}
                    </div>
                    ${item.hora ? `<div style="font-size:0.75rem;color:#64748b;margin-top:2px;">🕐 ${item.hora}</div>` : ''}
                </div>
                <div style="display:flex;align-items:center;gap:4px;font-size:0.72rem;font-weight:600;
                            color:${cor};white-space:nowrap;flex-shrink:0;">
                    ${label} <i class="bi bi-arrow-right-circle-fill"></i>
                </div>
            </div>`;
        }).join('');
    }

    new bootstrap.Modal(document.getElementById('modalDia')).show();
}
</script>
<script>
(function () {
    const INTERVALO   = 5000;
    const STORAGE_KEY = 'notif_lidas_<?= (int)$id_usuario ?>';

    const QUINZE_DIAS_MS  = 15 * 24 * 60 * 60 * 1000;
    const STORAGE_KEY_TS  = 'notif_lidas_ts_<?= (int)$id_usuario ?>';

    // ── localStorage helpers ──────────────────────────────────────
    function getLidas() {
        try {
            const agora = Date.now();
            const textos = new Set(JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'));
            const ts     = JSON.parse(localStorage.getItem(STORAGE_KEY_TS) || '{}');
            // Remove entradas com mais de 15 dias
            for (const txt of [...textos]) {
                if (ts[txt] && (agora - ts[txt]) > QUINZE_DIAS_MS) textos.delete(txt);
            }
            return textos;
        } catch(e) { return new Set(); }
    }
    function salvarLidas(set) {
        const agora = Date.now();
        const ts    = JSON.parse(localStorage.getItem(STORAGE_KEY_TS) || '{}');
        for (const txt of set) { if (!ts[txt]) ts[txt] = agora; }
        localStorage.setItem(STORAGE_KEY, JSON.stringify([...set].slice(-200)));
        localStorage.setItem(STORAGE_KEY_TS, JSON.stringify(ts));
    }

    // ── Intercepta cliques em "Marcar como lida" para persistir ──
    document.getElementById('listaNotif').addEventListener('click', function(e) {
        const btn = e.target.closest('.tb-notif-toggle');
        if (!btn) return;
        const item  = btn.closest('.tb-notif-item');
        const span  = item.querySelector('span');
        if (!span) return;
        const texto = span.innerHTML.trim();
        const lidas = getLidas();
        if (item.dataset.lida === '1') {
            lidas.delete(texto);
        } else {
            lidas.add(texto);
        }
        salvarLidas(lidas);
    });

    // ── Intercepta "Marcar todas como lidas" ─────────────────────
    document.getElementById('btnLerTodas').addEventListener('click', function() {
        const lidas = getLidas();
        document.querySelectorAll('#listaNotif .tb-notif-item span').forEach(function(s) {
            lidas.add(s.innerHTML.trim());
        });
        salvarLidas(lidas);
    });

    // ── Renderiza lista preservando estado do localStorage ───────
    function renderNotificacoes(lista) {
        const listaEl = document.getElementById('listaNotif');
        const badge   = document.getElementById('badgeNotif');
        if (!listaEl || !badge) return;

        const lidas = getLidas();

        if (lista.length === 0) {
            listaEl.innerHTML = `
                <div class="tb-notif-vazia">
                    <i class="bi bi-bell-slash" style="font-size:1.5rem;display:block;margin-bottom:8px;"></i>
                    Nenhuma notificação
                </div>`;
        } else {
            listaEl.innerHTML = lista.map(function(n) {
                const texto  = n.texto.trim();
                const jaLida = lidas.has(texto) ? '1' : '0';
                const btnTxt = jaLida === '1' ? 'Marcar como não lida' : 'Marcar como lida';
                return `<div class="tb-notif-item" data-lida="${jaLida}">
                    <div class="tb-notif-texto">
                        <i class="bi ${n.icone}" style="color:${n.cor};margin-right:6px;flex-shrink:0;"></i>
                        <span>${texto}</span>
                    </div>
                    <button class="tb-notif-toggle">${btnTxt}</button>
                </div>`;
            }).join('');
        }

        const naoLidos = listaEl.querySelectorAll('.tb-notif-item[data-lida="0"]').length;
        badge.textContent = naoLidos;
        badge.style.display = naoLidos > 0 ? '' : 'none';
    }

    // ── Aplica estado do localStorage no carregamento inicial ────
    (function aplicarLidasIniciais() {
        const lidas = getLidas();
        if (lidas.size === 0) return;
        let naoLidos = 0;
        document.querySelectorAll('#listaNotif .tb-notif-item').forEach(function(item) {
            const span = item.querySelector('span');
            if (!span) return;
            const texto = span.innerHTML.trim();
            if (lidas.has(texto)) {
                item.dataset.lida = '1';
                const btn = item.querySelector('.tb-notif-toggle');
                if (btn) btn.textContent = 'Marcar como não lida';
            } else {
                naoLidos++;
            }
        });
        const badge = document.getElementById('badgeNotif');
        if (badge) {
            badge.textContent = naoLidos;
            badge.style.display = naoLidos > 0 ? '' : 'none';
        }
    })();

    // ── Polling ───────────────────────────────────────────────────
    function buscarNotificacoes() {
        fetch('pages-aluno/notificacoes.php', { cache: 'no-store' })
            .then(function(r) { return r.ok ? r.json() : Promise.reject(); })
            .then(function(lista) { renderNotificacoes(lista); })
            .catch(function() {});
    }

    buscarNotificacoes();
    setInterval(buscarNotificacoes, INTERVALO);
})();
</script>
<script src="assets/js/topbar.js"></script>
</body>
</html>
