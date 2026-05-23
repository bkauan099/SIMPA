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
                <span class="badge-icon">3</span>
                <span class="nav-label">Seletivos</span>
                <span class="badge bg-danger ms-auto badge-text" style="font-size:0.65rem;">3</span>
            </a></li>
            <li><a href="javascript:void(0)" id="menu-documentos" onclick="carregarPagina('documentos')" title="Documentos">
                <i class="bi bi-file-earmark-text"></i><span class="nav-label">Documentos</span></a></li>
            <li><a href="javascript:void(0)" id="menu-certificados" onclick="carregarPagina('certificados')" title="Certificados">
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
            <h6 class="fw-bold mb-0"><i class="bi bi-paperclip me-2" style="color:#3b82f6;"></i>Enviar Atividade</h6>
            <button class="btn-close" onclick="fecharModalEnvio()"></button>
        </div>
        <div id="dropZone" onclick="document.getElementById('inputArquivo').click()"
             style="border:2px dashed #cbd5e1;border-radius:12px;padding:28px;text-align:center;cursor:pointer;transition:border-color .2s;"
             onmouseenter="this.style.borderColor='#3b82f6'" onmouseleave="this.style.borderColor='#cbd5e1'">
            <i class="bi bi-cloud-arrow-up d-block mb-2" style="font-size:2rem;color:#94a3b8;"></i>
            <div style="font-size:0.85rem;color:#64748b;">Clique para selecionar o arquivo</div>
            <div style="font-size:0.75rem;color:#94a3b8;margin-top:4px;">Qualquer formato · Máx. 15 MB</div>
        </div>
        <input type="file" id="inputArquivo" style="display:none;" onchange="selecionarArquivo(this)">
        <div id="arquivoSelecionado" style="display:none;" class="d-flex align-items-center gap-2 p-2 mt-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
            <i class="bi bi-file-earmark text-primary"></i>
            <span id="nomeArquivoSel" class="flex-grow-1 text-truncate" style="font-size:0.85rem;"></span>
            <button class="btn btn-sm btn-link text-danger p-0" onclick="limparArquivoSelecionado()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="mt-3">
            <label class="form-label" style="font-size:0.8rem;color:#64748b;">Observação <span style="color:#94a3b8;">(opcional)</span></label>
            <textarea id="obsEnvio" class="form-control form-control-sm" rows="2" placeholder="Descreva algo sobre o envio..."></textarea>
        </div>
        <div class="mt-3 d-flex justify-content-end">
            <button class="btn btn-primary" id="btnEnviarArquivo" onclick="enviarArquivoTarefa()">
                <i class="bi bi-cloud-arrow-up me-1"></i>Enviar
            </button>
        </div>
    </div>
</div>

<!-- MODAL: Editar envio (rascunho) -->
<div id="modalEdicaoTarefa" style="display:none;position:fixed;inset:0;z-index:1070;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:24px;width:90%;max-width:460px;box-shadow:0 8px 32px rgba(0,0,0,0.18);">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0"><i class="bi bi-pencil me-2 text-secondary"></i>Editar Envio</h6>
            <button class="btn-close" onclick="fecharModalEdicao()"></button>
        </div>
        <div style="font-size:0.78rem;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;">Arquivo atual (rascunho)</div>
        <div class="d-flex align-items-center gap-2 p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
            <i class="bi bi-file-earmark text-primary fs-5"></i>
            <span id="nomeArquivoEdit" class="flex-grow-1 text-truncate" style="font-size:0.85rem;font-weight:500;"></span>
            <button class="btn btn-sm btn-outline-primary rounded-circle p-0 d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width:28px;height:28px;" title="Visualizar" onclick="visualizarArquivoTarefa()">
                <i class="bi bi-eye" style="font-size:0.75rem;"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger rounded-circle p-0 d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width:28px;height:28px;" title="Remover" onclick="confirmarRemoverArquivo()">
                <i class="bi bi-x-lg" style="font-size:0.75rem;"></i>
            </button>
        </div>
        <div id="dropZoneEdit" onclick="document.getElementById('inputArquivoEdit').click()" class="mt-3"
             style="border:2px dashed #cbd5e1;border-radius:12px;padding:16px;text-align:center;cursor:pointer;transition:border-color .2s;"
             onmouseenter="this.style.borderColor='#3b82f6'" onmouseleave="this.style.borderColor='#cbd5e1'">
            <i class="bi bi-arrow-repeat me-2 text-muted"></i>
            <span style="font-size:0.82rem;color:#64748b;">Substituir por outro arquivo</span>
        </div>
        <input type="file" id="inputArquivoEdit" style="display:none;" onchange="substituirArquivo(this)">
        <div id="arquivoSubstituto" style="display:none;" class="d-flex align-items-center gap-2 p-2 mt-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
            <i class="bi bi-file-earmark text-primary"></i>
            <span id="nomeArquivoSub" class="flex-grow-1 text-truncate" style="font-size:0.85rem;"></span>
            <button class="btn btn-sm btn-link text-danger p-0" onclick="limparSubstituto()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div id="obsEdicaoWrap" style="display:none;" class="mt-3">
            <label class="form-label" style="font-size:0.8rem;color:#64748b;">Observação <span style="color:#94a3b8;">(opcional)</span></label>
            <textarea id="obsEdicao" class="form-control form-control-sm" rows="2"></textarea>
            <div class="mt-2 d-flex justify-content-end">
                <button class="btn btn-primary btn-sm" onclick="enviarSubstituto()">
                    <i class="bi bi-cloud-arrow-up me-1"></i>Enviar novo
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Visualizar arquivo -->
<div id="modalVisualizarArquivo" style="display:none;position:fixed;inset:0;z-index:1080;background:rgba(0,0,0,0.6);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;width:95%;max-width:820px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.25);">
        <div class="d-flex justify-content-between align-items-center p-3" style="border-bottom:1px solid #e2e8f0;">
            <span class="fw-semibold" style="font-size:0.88rem;" id="tituloArquivoVis"></span>
            <button class="btn-close" onclick="fecharModalVisualizar()"></button>
        </div>
        <div id="corpoArquivoVis" style="min-height:300px;max-height:72vh;overflow:auto;"></div>
    </div>
</div>

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

function carregarPagina(abaSolicitada) {
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

    location.hash = abaSolicitada;

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
        })
        .catch(err => {
            _fetchAtivo = null;
            if (err.name === 'AbortError') return;
            container.innerHTML = `<div class="alert alert-danger m-3">Erro ao carregar a página.<br><small>${err.message}</small></div>`;
        });
}

document.addEventListener('DOMContentLoaded', () => {
    const hash = location.hash.replace('#', '') || 'pagina-inicial';
    carregarPagina(hash);
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

document.addEventListener('keydown', e => { if (e.key === 'Escape') fecharSlideOver(); });

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

// ── Tarefas ───────────────────────────────────────────────────
let _tarefaAtual = null;

function toggleConcluido(btn) {
    const tr = btn.closest('tr');
    const id = tr.dataset.id;
    btn.disabled = true;

    fetch('pages-aluno/toggle-concluido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(id)
    })
    .then(r => r.json())
    .then(data => {
        if (!data.ok) { btn.disabled = false; return; }
        const concluido = data.concluido;
        const hoje = new Date(); hoje.setHours(0, 0, 0, 0);
        const p = tr.dataset.data.split('-');
        const prazo = new Date(p[0], p[1] - 1, p[2]);

        let statusKey, statusLabel, statusClass;
        if (concluido) {
            statusKey = 'concluido'; statusLabel = 'Concluído'; statusClass = 'bg-success text-white';
        } else if (prazo < hoje) {
            statusKey = 'nao_concluido'; statusLabel = 'Não Concluído'; statusClass = 'bg-danger text-white';
        } else {
            statusKey = 'pendente'; statusLabel = 'Pendente'; statusClass = 'bg-warning text-dark';
        }

        const ant = tr.dataset.status;
        tr.dataset.status = statusKey;
        tr.dataset.concluido = concluido ? '1' : '0';

        tr.querySelector('.badge-status').className = 'badge badge-status ' + statusClass;
        tr.querySelector('.badge-status').textContent = statusLabel;

        const statIds = { pendente: 'statPendentes', nao_concluido: 'statNaoConcluidos', concluido: 'statConcluidos' };
        const elAnt = document.getElementById(statIds[ant]);
        const elNov = document.getElementById(statIds[statusKey]);
        if (elAnt) elAnt.textContent = Math.max(0, parseInt(elAnt.textContent) - 1);
        if (elNov) elNov.textContent = parseInt(elNov.textContent) + 1;

        btn.disabled = false;
        atualizarBotoesTarefa(tr);
    })
    .catch(() => { btn.disabled = false; });
}

function atualizarBotoesTarefa(tr) {
    const temArquivo  = !!tr.dataset.arquivoCaminho;
    const concluido   = tr.dataset.concluido === '1';
    const hoje = new Date(); hoje.setHours(0, 0, 0, 0);
    const p = tr.dataset.data.split('-');
    const prazo = new Date(p[0], p[1] - 1, p[2]);
    const prazoPastou = prazo < hoje;
    const td = tr.querySelector('td:last-child');

    if (concluido && prazoPastou) {
        td.innerHTML = `<button class="btn btn-sm btn-outline-success opacity-50" disabled title="Concluído"><i class="bi bi-check-lg"></i></button>`;
    } else if (concluido) {
        td.innerHTML = `<button class="btn btn-sm btn-outline-warning" onclick="event.stopPropagation();toggleConcluido(this)" title="Desfazer conclusão"><i class="bi bi-arrow-counterclockwise"></i></button>`
            + (temArquivo ? `<button class="btn btn-sm btn-outline-secondary ms-1" onclick="event.stopPropagation();abrirModalEdicao(this.closest('tr'))" title="Ver envio"><i class="bi bi-pencil"></i></button>` : '');
    } else if (temArquivo) {
        td.innerHTML = `<button class="btn btn-sm btn-outline-success" onclick="event.stopPropagation();toggleConcluido(this)" title="Marcar como concluído"><i class="bi bi-check-lg"></i></button>`
            + `<button class="btn btn-sm btn-outline-secondary ms-1" onclick="event.stopPropagation();abrirModalEdicao(this.closest('tr'))" title="Editar envio"><i class="bi bi-pencil"></i></button>`;
    } else {
        td.innerHTML = `<button class="btn btn-sm btn-outline-success" onclick="event.stopPropagation();toggleConcluido(this)" title="Marcar como concluído"><i class="bi bi-check-lg"></i></button>`
            + `<button class="btn btn-sm ms-1" style="border:1.5px solid #93c5fd;color:#3b82f6;background:transparent;" onclick="event.stopPropagation();abrirModalEnvio(this.closest('tr'))" title="Anexar arquivo"><i class="bi bi-paperclip"></i></button>`;
    }
}

function abrirDetalheTarefa(tr) {
    const titulo       = tr.querySelector('td.fw-medium')?.textContent || '';
    const data         = tr.querySelector('td:nth-child(2)')?.textContent || '';
    const hora         = tr.querySelector('td:nth-child(3)')?.textContent || '—';
    const badgeEl      = tr.querySelector('.badge-status');
    const statusLabel  = badgeEl?.textContent || '';
    const cls          = badgeEl?.className || '';
    const arquivoNome  = tr.dataset.arquivoNome || '';
    const arquivoCam   = tr.dataset.arquivoCaminho || '';

    let statusStyle, statusIco;
    if (cls.includes('bg-success'))     { statusStyle = 'background:#dcfce7;color:#16a34a;'; statusIco = 'bi-check-circle'; }
    else if (cls.includes('bg-danger')) { statusStyle = 'background:#fee2e2;color:#dc2626;'; statusIco = 'bi-x-circle'; }
    else                                { statusStyle = 'background:#fef9c3;color:#a16207;'; statusIco = 'bi-hourglass-split'; }

    const descFull = tr.dataset.busca
        ? tr.dataset.busca.replace(titulo.toLowerCase() + ' ', '').trim()
        : '';

    const arquivoHtml = arquivoNome
        ? `<hr class="so-divider">
           <div class="so-campo">
               <div class="so-label">Arquivo enviado</div>
               <div class="so-valor mt-1">
                   <div class="d-flex align-items-center gap-2 p-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                       <i class="bi bi-file-earmark text-primary"></i>
                       <span class="flex-grow-1 text-truncate" style="font-size:0.85rem;">${arquivoNome}</span>
                       <button class="btn btn-sm btn-outline-primary rounded-circle p-0 d-flex align-items-center justify-content-center flex-shrink-0"
                               style="width:28px;height:28px;" onclick="abrirModalVisualizar('${arquivoCam}','${arquivoNome.replace(/'/g,'\\\'')}')" title="Visualizar">
                           <i class="bi bi-eye" style="font-size:0.75rem;"></i>
                       </button>
                   </div>
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

// ── Modais de upload ──────────────────────────────────────────
function abrirModalEnvio(tr) {
    _tarefaAtual = tr;
    document.getElementById('inputArquivo').value = '';
    document.getElementById('arquivoSelecionado').style.display = 'none';
    document.getElementById('obsEnvio').value = '';
    document.getElementById('btnEnviarArquivo').disabled = false;
    document.getElementById('btnEnviarArquivo').innerHTML = '<i class="bi bi-cloud-arrow-up me-1"></i>Enviar';
    document.getElementById('modalEnvioTarefa').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function fecharModalEnvio() {
    document.getElementById('modalEnvioTarefa').style.display = 'none';
    document.body.style.overflow = '';
}
function selecionarArquivo(input) {
    if (!input.files[0]) return;
    document.getElementById('nomeArquivoSel').textContent = input.files[0].name;
    document.getElementById('arquivoSelecionado').style.display = 'flex';
}
function limparArquivoSelecionado() {
    document.getElementById('inputArquivo').value = '';
    document.getElementById('arquivoSelecionado').style.display = 'none';
}
function enviarArquivoTarefa() {
    const file = document.getElementById('inputArquivo').files[0];
    if (!file) { alert('Selecione um arquivo antes de enviar.'); return; }
    if (file.size > 15 * 1024 * 1024) { alert('O arquivo deve ter no máximo 15 MB.'); return; }
    const btn = document.getElementById('btnEnviarArquivo');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Enviando...';
    const fd = new FormData();
    fd.append('id',     _tarefaAtual.dataset.id);
    fd.append('titulo', _tarefaAtual.dataset.titulo);
    fd.append('arquivo', file);
    fd.append('obs', document.getElementById('obsEnvio').value);
    fetch('pages-aluno/upload-tarefa.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-cloud-arrow-up me-1"></i>Enviar';
            if (data.ok) {
                _tarefaAtual.dataset.arquivoCaminho = data.caminho;
                _tarefaAtual.dataset.arquivoNome    = data.nome;
                _tarefaAtual.dataset.idProducao     = data.id_producao;
                atualizarBotoesTarefa(_tarefaAtual);
                fecharModalEnvio();
            } else { alert('Erro: ' + (data.erro || 'Erro desconhecido')); }
        })
        .catch(() => { btn.disabled = false; btn.innerHTML = '<i class="bi bi-cloud-arrow-up me-1"></i>Enviar'; alert('Erro ao enviar.'); });
}

function abrirModalEdicao(tr) {
    _tarefaAtual = tr;
    document.getElementById('nomeArquivoEdit').textContent = tr.dataset.arquivoNome || 'arquivo';
    document.getElementById('inputArquivoEdit').value = '';
    document.getElementById('arquivoSubstituto').style.display = 'none';
    document.getElementById('obsEdicaoWrap').style.display = 'none';
    document.getElementById('modalEdicaoTarefa').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function fecharModalEdicao() {
    document.getElementById('modalEdicaoTarefa').style.display = 'none';
    document.body.style.overflow = '';
}
function visualizarArquivoTarefa() {
    abrirModalVisualizar(_tarefaAtual.dataset.arquivoCaminho, _tarefaAtual.dataset.arquivoNome);
}
function abrirModalVisualizar(caminho, nome) {
    document.getElementById('tituloArquivoVis').textContent = nome;
    const ext = (nome.split('.').pop() || '').toLowerCase();
    const corpo = document.getElementById('corpoArquivoVis');
    if (['jpg','jpeg','png','gif','webp','svg'].includes(ext)) {
        corpo.innerHTML = `<img src="${caminho}" style="max-width:100%;display:block;margin:auto;padding:16px;">`;
    } else if (ext === 'pdf') {
        corpo.innerHTML = `<iframe src="${caminho}" style="width:100%;height:65vh;border:0;"></iframe>`;
    } else {
        corpo.innerHTML = `<div class="text-center py-5"><i class="bi bi-file-earmark fs-1 text-muted mb-3 d-block"></i><p class="text-muted mb-3">${nome}</p><a href="${caminho}" download class="btn btn-primary btn-sm"><i class="bi bi-download me-1"></i>Baixar arquivo</a></div>`;
    }
    document.getElementById('modalVisualizarArquivo').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function fecharModalVisualizar() {
    document.getElementById('modalVisualizarArquivo').style.display = 'none';
    document.body.style.overflow = '';
}
function confirmarRemoverArquivo() {
    if (!confirm('Remover o arquivo enviado?')) return;
    fetch('pages-aluno/remover-arquivo-tarefa.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_producao=' + encodeURIComponent(_tarefaAtual.dataset.idProducao)
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            _tarefaAtual.dataset.arquivoCaminho = '';
            _tarefaAtual.dataset.arquivoNome    = '';
            _tarefaAtual.dataset.idProducao     = '';
            atualizarBotoesTarefa(_tarefaAtual);
            fecharModalEdicao();
        } else { alert('Erro ao remover.'); }
    })
    .catch(() => alert('Erro ao remover.'));
}
function substituirArquivo(input) {
    if (!input.files[0]) return;
    document.getElementById('nomeArquivoSub').textContent = input.files[0].name;
    document.getElementById('arquivoSubstituto').style.display = 'flex';
    document.getElementById('obsEdicaoWrap').style.display = 'block';
}
function limparSubstituto() {
    document.getElementById('inputArquivoEdit').value = '';
    document.getElementById('arquivoSubstituto').style.display = 'none';
    document.getElementById('obsEdicaoWrap').style.display = 'none';
}
function enviarSubstituto() {
    const file = document.getElementById('inputArquivoEdit').files[0];
    if (!file) return;
    if (file.size > 15 * 1024 * 1024) { alert('O arquivo deve ter no máximo 15 MB.'); return; }
    const fd = new FormData();
    fd.append('id',     _tarefaAtual.dataset.id);
    fd.append('titulo', _tarefaAtual.dataset.titulo);
    fd.append('arquivo', file);
    fd.append('obs', document.getElementById('obsEdicao').value);
    fetch('pages-aluno/upload-tarefa.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                _tarefaAtual.dataset.arquivoCaminho = data.caminho;
                _tarefaAtual.dataset.arquivoNome    = data.nome;
                _tarefaAtual.dataset.idProducao     = data.id_producao;
                fecharModalEdicao();
            } else { alert('Erro: ' + (data.erro || 'Erro desconhecido')); }
        })
        .catch(() => alert('Erro ao enviar.'));
}

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

    fetch('pages-aluno/toggle-concluido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(id)
    })
    .then(r => r.json())
    .then(data => {
        if (!data.ok) { btn.disabled = false; return; }
        const concluido = data.concluido;
        const hoje = new Date(); hoje.setHours(0, 0, 0, 0);
        const p = tr.dataset.data.split('-');
        const prazo = new Date(p[0], p[1] - 1, p[2]);

        let statusKey, statusLabel, statusClass;
        if (concluido) {
            statusKey = 'concluido'; statusLabel = 'Concluído'; statusClass = 'bg-success text-white';
        } else if (prazo < hoje) {
            statusKey = 'nao_concluido'; statusLabel = 'Não Concluído'; statusClass = 'bg-danger text-white';
        } else {
            statusKey = 'proximo'; statusLabel = 'Pendente'; statusClass = 'bg-warning text-dark';
        }

        const ant = tr.dataset.status;
        tr.dataset.status = statusKey;

        const badge = tr.querySelector('.badge-status');
        badge.className = 'badge badge-status ' + statusClass;
        badge.textContent = statusLabel;

        const icon = btn.querySelector('i');
        if (concluido) {
            btn.className = 'btn btn-sm btn-outline-warning';
            btn.title = 'Desfazer';
            icon.className = 'bi bi-arrow-counterclockwise';
        } else {
            btn.className = 'btn btn-sm btn-outline-success';
            btn.title = 'Marcar como concluído';
            icon.className = 'bi bi-check-lg';
        }

        const statIds = { proximo: 'statProximos', nao_concluido: 'statNaoConcluidos', concluido: 'statConcluidos' };
        const elAnt = document.getElementById(statIds[ant]);
        const elNov = document.getElementById(statIds[statusKey]);
        if (elAnt) elAnt.textContent = Math.max(0, parseInt(elAnt.textContent) - 1);
        if (elNov) elNov.textContent = parseInt(elNov.textContent) + 1;

        btn.disabled = false;
    })
    .catch(() => { btn.disabled = false; });
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
    document.getElementById('modalDiaLista').innerHTML = itens.length
        ? itens.map((item, i, a) =>
            `<div class="py-2${i < a.length - 1 ? ' border-bottom' : ''}" style="font-size:0.88rem;">${item}</div>`
          ).join('')
        : '<p class="text-muted text-center py-2 mb-0">Nenhuma atividade neste dia.</p>';

    new bootstrap.Modal(document.getElementById('modalDia')).show();
}
</script>
</body>
</html>
