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
            <li><a href="#" id="menu-participacoes" onclick="carregarPagina('participacoes')" title="Registros">
                <i class="bi bi-clock-history"></i><span class="nav-label">Registros</span></a></li>
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
        default:                   arquivo = 'pages-aluno/pagina-inicial.php';      break;
    }

    window.location.hash = abaSolicitada;

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
            if (err.name === 'AbortError') return;
            container.innerHTML = `<div class="alert alert-danger m-3">Erro ao carregar a página.<br><small>${err.message}</small></div>`;
        });
}

document.addEventListener('DOMContentLoaded', () => {
    const hash = window.location.hash.replace('#', '');
    carregarPagina(hash || 'pagina-inicial');
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

// ── Tarefas ───────────────────────────────────────────────────
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

        const badge = tr.querySelector('.badge-status');
        badge.className = 'badge badge-status ' + statusClass;
        badge.textContent = statusLabel;

        const statIds = { pendente: 'statPendentes', nao_concluido: 'statNaoConcluidos', concluido: 'statConcluidos' };
        const elAnt = document.getElementById(statIds[ant]);
        const elNov = document.getElementById(statIds[statusKey]);
        if (elAnt) elAnt.textContent = Math.max(0, parseInt(elAnt.textContent) - 1);
        if (elNov) elNov.textContent = parseInt(elNov.textContent) + 1;

        const icon = btn.querySelector('i');
        if (concluido) {
            btn.className = 'btn btn-sm btn-outline-warning';
            btn.title = 'Desfazer conclusão';
            icon.className = 'bi bi-arrow-counterclockwise';
        } else {
            btn.className = 'btn btn-sm btn-outline-success';
            btn.title = 'Marcar como concluído';
            icon.className = 'bi bi-check-lg';
        }
        btn.disabled = false;
    })
    .catch(() => { btn.disabled = false; });
}

function toggleDescricao(btn) {
    const tr = btn.closest('tr');
    const descRow = tr.nextElementSibling;
    const icon = btn.querySelector('i');
    const aberto = descRow.style.display !== 'none';
    descRow.style.display = aberto ? 'none' : '';
    icon.className = aberto ? 'bi bi-three-dots-vertical' : 'bi bi-x-lg';
}

function abrirDetalheTarefa(tr) {
    const titulo      = tr.querySelector('td.fw-medium')?.textContent || '';
    const data        = tr.querySelector('td:nth-child(2)')?.textContent || '';
    const hora        = tr.querySelector('td:nth-child(3)')?.textContent || '—';
    const badgeEl     = tr.querySelector('.badge-status');
    const statusLabel = badgeEl?.textContent || '';
    const cls         = badgeEl?.className || '';

    let statusStyle, statusIco;
    if (cls.includes('bg-success'))     { statusStyle = 'background:#dcfce7;color:#16a34a;'; statusIco = 'bi-check-circle'; }
    else if (cls.includes('bg-danger')) { statusStyle = 'background:#fee2e2;color:#dc2626;'; statusIco = 'bi-x-circle'; }
    else                                { statusStyle = 'background:#fef9c3;color:#a16207;'; statusIco = 'bi-hourglass-split'; }

    const descFull = tr.dataset.busca
        ? tr.dataset.busca.replace(titulo.toLowerCase() + ' ', '').trim()
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
            <div class="so-valor">${descFull ? descFull.replace(/\n/g, '<br>') : '<span class="text-muted">Sem descrição.</span>'}</div>
        </div>`, {
        badge: '<i class="bi bi-check2-square me-1"></i>Tarefa',
        badgeCor: '#3b82f6'
    });
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
