<?php
session_start();

// Proteção de rota — só ADM pode acessar
if (empty($_SESSION['id_usuario']) || empty($_SESSION['perfil'])) {
    header("Location: login-page.php");
    exit();
}
$perfil = strtolower($_SESSION['perfil'] ?? '');
if (!str_contains($perfil, 'admin')) {
    header("Location: login-page.php");
    exit();
}

$nomeUsuario = htmlspecialchars($_SESSION['nome']  ?? 'Usuário');
$iniciais    = strtoupper(implode('', array_map(fn($p) => $p[0], array_slice(explode(' ', $_SESSION['nome'] ?? 'U'), 0, 2))));
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
    <style>
        /* ── Notificações ── */
        .notif-btn { position:relative; background:none; border:none; padding:6px; color:inherit; cursor:pointer; }
        .notif-badge {
            position:absolute; top:2px; right:2px;
            background:#ef4444; color:#fff; border-radius:999px;
            font-size:.6rem; font-weight:700; min-width:16px; height:16px;
            display:flex; align-items:center; justify-content:center; padding:0 3px;
            line-height:1;
        }
        .notif-dropdown {
            position:absolute; right:0; top:calc(100% + 10px);
            width:340px; background:#fff; border-radius:12px;
            box-shadow:0 8px 32px rgba(0,0,0,.18); z-index:9999;
            display:none; flex-direction:column; overflow:hidden;
        }
        .notif-dropdown.show { display:flex; }
        .notif-header { padding:14px 16px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; }
        .notif-header h6 { margin:0; font-weight:700; font-size:.9rem; }
        .notif-list { max-height:340px; overflow-y:auto; }
        .notif-item {
            display:flex; align-items:flex-start; gap:12px;
            padding:12px 16px; border-bottom:1px solid #f8fafc;
            cursor:pointer; transition:background .15s;
            text-decoration:none; color:inherit;
        }
        .notif-item:hover { background:#f8fafc; }
        .notif-item:last-child { border-bottom:none; }
        .notif-icon { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; margin-top:2px; }
        .notif-icon.projeto  { background:#fef9c3; color:#ca8a04; }
        .notif-icon.documento{ background:#dbeafe; color:#2563eb; }
        .notif-icon.usuario  { background:#dcfce7; color:#16a34a; }
        .notif-footer { padding:12px 16px; text-align:center; border-top:1px solid #f1f5f9; }
        .notif-vazia { padding:28px 16px; text-align:center; color:#94a3b8; }

        /* ── Perfil dropdown ── */
        .perfil-btn { display:flex; align-items:center; gap:8px; background:none; border:none; cursor:pointer; color:inherit; padding:4px 8px; border-radius:8px; transition:background .15s; }
        .perfil-btn:hover { background:rgba(255,255,255,.12); }
        .avatar-circle {
            width:34px; height:34px; border-radius:50%; background:#3b82f6;
            color:#fff; font-size:.75rem; font-weight:700;
            display:flex; align-items:center; justify-content:center; flex-shrink:0;
        }
        .perfil-dropdown {
            position:absolute; right:0; top:calc(100% + 10px);
            width:260px; background:#fff; border-radius:12px;
            box-shadow:0 8px 32px rgba(0,0,0,.18); z-index:9999;
            display:none; flex-direction:column; overflow:hidden;
        }
        .perfil-dropdown.show { display:flex; }
        .perfil-header { padding:16px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:12px; }
        .perfil-header .avatar-circle { width:44px; height:44px; font-size:.9rem; }
        .perfil-header h6 { margin:0; font-weight:700; font-size:.85rem; color:#1e293b; }
        .perfil-header small { color:#64748b; font-size:.75rem; }
        .perfil-menu-item {
            display:flex; align-items:center; gap:10px; padding:11px 16px;
            text-decoration:none; color:#374151; font-size:.875rem;
            transition:background .15s; cursor:pointer; border:none; background:none; width:100%;
        }
        .perfil-menu-item:hover { background:#f8fafc; color:#1e293b; }
        .perfil-menu-item i { width:18px; text-align:center; color:#64748b; font-size:1rem; }
        .perfil-menu-item.sair { color:#ef4444; border-top:1px solid #f1f5f9; }
        .perfil-menu-item.sair i { color:#ef4444; }

        /* ── Modais de perfil ── */
        .modal-perfil .modal-header { border-bottom:0; padding-bottom:0; }
        .modal-perfil .modal-footer { border-top:0; }
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
            <li><a href="#" id="menu-relatorios" onclick="carregarPagina('relatorios')" title="Relatórios">
                <i class="bi bi-graph-up"></i><span class="nav-label">Relatórios</span></a></li>
            <li class="sidebar-sair">
                <a href="pages-adm/sair.php" title="Sair">
                    <i class="bi bi-box-arrow-left"></i><span class="nav-label">Sair</span>
                </a>
            </li>
        </ul>
    </nav>

    <div id="content">
        <header class="navbar-custom">
            <div class="topbar-left">
                <img src="assets/img/uema-logo.png" alt="UEMA" class="logo-uema-top">
                <div class="logo-sep"></div>
                <img src="assets/img/proexae-branco-semfundo.png" alt="ProExae" class="logo-proexae-top">
            </div>

            <div class="topbar-right">

                <!-- ── SINO DE NOTIFICAÇÕES ── -->
                <div class="position-relative">
                    <button class="notif-btn" id="btnNotif" onclick="toggleNotif()" title="Notificações">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="notif-badge" id="notifBadge" style="display:none">0</span>
                    </button>
                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <h6><i class="bi bi-bell me-2"></i>Notificações</h6>
                            <span class="badge bg-primary rounded-pill" id="notifTotal">0</span>
                        </div>
                        <div class="notif-list" id="notifList">
                            <div class="notif-vazia"><i class="bi bi-bell-slash fs-3 d-block mb-2"></i>Carregando...</div>
                        </div>
                        <div class="notif-footer">
                            <small class="text-muted">As notificações se atualizam automaticamente</small>
                        </div>
                    </div>
                </div>

                <!-- ── MENU DE PERFIL ── -->
                <div class="position-relative">
                    <button class="perfil-btn" id="btnPerfil" onclick="togglePerfil()">
                        <div class="avatar-circle" id="topAvatar"><?= $iniciais ?></div>
                        <span class="fw-medium d-none d-sm-inline" id="topNome"><?= $nomeUsuario ?></span>
                        <i class="bi bi-chevron-down small d-none d-sm-inline"></i>
                    </button>
                    <div class="perfil-dropdown" id="perfilDropdown">
                        <div class="perfil-header">
                            <div class="avatar-circle" id="dropAvatar"><?= $iniciais ?></div>
                            <div>
                                <h6 id="dropNome"><?= $nomeUsuario ?></h6>
                                <small id="dropEmail"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></small><br>
                                <small class="badge bg-secondary mt-1"><?= htmlspecialchars($_SESSION['perfil'] ?? '') ?></small>
                            </div>
                        </div>
                        <button class="perfil-menu-item" onclick="abrirModalEditarPerfil()">
                            <i class="bi bi-person-gear"></i> Editar Perfil
                        </button>
                        <button class="perfil-menu-item" onclick="abrirModalTrocarSenha()">
                            <i class="bi bi-key"></i> Trocar Senha
                        </button>
                        <a class="perfil-menu-item sair" href="pages-adm/sair.php">
                            <i class="bi bi-box-arrow-right"></i> Sair
                        </a>
                    </div>
                </div>

            </div><!-- /topbar-right -->
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

<!-- ════ MODAL EDITAR PERFIL ════ -->
<div class="modal fade modal-perfil" id="modalEditarPerfil" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header pb-0">
        <h5 class="modal-title fw-bold"><i class="bi bi-person-gear me-2"></i>Editar Perfil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="text-center mb-4">
            <div class="avatar-circle mx-auto mb-2" style="width:60px;height:60px;font-size:1.4rem;" id="modalAvatar"><?= $iniciais ?></div>
            <small class="text-muted">Administrador</small>
        </div>
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-medium">Nome Completo</label>
                <input type="text" class="form-control" id="perfil_nome" placeholder="Seu nome completo">
            </div>
            <div class="col-12">
                <label class="form-label fw-medium">E-mail</label>
                <input type="email" class="form-control" id="perfil_email" placeholder="seu@email.com">
            </div>
            <div class="col-12">
                <label class="form-label fw-medium">Curso / Departamento</label>
                <input type="text" class="form-control" id="perfil_curso" placeholder="Ex: Ciência da Computação">
            </div>
        </div>
        <div id="perfil_feedback" class="mt-3"></div>
      </div>
      <div class="modal-footer pt-0">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" onclick="salvarPerfil()"><i class="bi bi-save me-1"></i>Salvar</button>
      </div>
    </div>
  </div>
</div>

<!-- ════ MODAL TROCAR SENHA ════ -->
<div class="modal fade modal-perfil" id="modalTrocarSenha" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header pb-0">
        <h5 class="modal-title fw-bold"><i class="bi bi-key me-2"></i>Trocar Senha</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-medium">Senha Atual</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="senha_atual" placeholder="Digite sua senha atual">
                    <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('senha_atual')"><i class="bi bi-eye"></i></button>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label fw-medium">Nova Senha</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="nova_senha" placeholder="Mínimo 6 caracteres">
                    <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('nova_senha')"><i class="bi bi-eye"></i></button>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label fw-medium">Confirmar Nova Senha</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirma_senha" placeholder="Repita a nova senha">
                    <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('confirma_senha')"><i class="bi bi-eye"></i></button>
                </div>
            </div>
            <!-- indicador de força da senha -->
            <div class="col-12">
                <div class="d-flex gap-1 mt-1" id="forca_barra">
                    <div class="flex-fill rounded" style="height:4px;background:#e2e8f0;" id="f1"></div>
                    <div class="flex-fill rounded" style="height:4px;background:#e2e8f0;" id="f2"></div>
                    <div class="flex-fill rounded" style="height:4px;background:#e2e8f0;" id="f3"></div>
                    <div class="flex-fill rounded" style="height:4px;background:#e2e8f0;" id="f4"></div>
                </div>
                <small class="text-muted" id="forca_label"></small>
            </div>
        </div>
        <div id="senha_feedback" class="mt-3"></div>
      </div>
      <div class="modal-footer pt-0">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-warning" onclick="salvarSenha()"><i class="bi bi-key me-1"></i>Alterar Senha</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ══════════════════════════════════════════════
// SIDEBAR
// ══════════════════════════════════════════════
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');

function isOverlayMode() {
    return window.innerWidth < 768 || (window.innerWidth < 992 && window.innerHeight > window.innerWidth);
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

// ══════════════════════════════════════════════
// CARREGAR PÁGINAS
// ══════════════════════════════════════════════
function carregarPagina(abaSolicitada) {
    document.querySelectorAll('#sidebar ul li a').forEach(l => l.classList.remove('active'));
    const menuClicado = document.getElementById('menu-' + abaSolicitada);
    if (menuClicado) menuClicado.classList.add('active');
    if (isOverlayMode()) closeSidebar();
    fecharDropdowns();

    let arquivoParaCarregar = '';
    switch (abaSolicitada) {
        case 'pagina-inicial':  arquivoParaCarregar = 'pages-adm/pagina-inicial.php';  break;
        case 'usuarios':        arquivoParaCarregar = 'pages-adm/usuarios.php';        break;
        case 'participacoes':   arquivoParaCarregar = 'pages-adm/participacoes.php';   break;
        case 'projetos':        arquivoParaCarregar = 'pages-adm/projetos.php';        break;
        case 'documentos':      arquivoParaCarregar = 'pages-adm/documentos.php';      break;
        case 'visitas':         arquivoParaCarregar = 'pages-adm/visitas.php';         break;
        case 'relatorios':      arquivoParaCarregar = 'pages-adm/relatorios.php';      break;
        default:                arquivoParaCarregar = 'pages-adm/pagina-inicial.php';  break;
    }

    const container = document.getElementById('conteudo-dinamico');
    container.innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></div>';

    fetch(arquivoParaCarregar, { cache: 'no-store' })
        .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.text(); })
        .then(html => {
            document.querySelectorAll('script[data-dinamico]').forEach(s => s.remove());
            const temp = document.createElement('div');
            temp.innerHTML = html;
            const scriptData = [];
            temp.querySelectorAll('script').forEach(s => { scriptData.push({ src: s.src, text: s.textContent }); s.remove(); });
            container.innerHTML = temp.innerHTML;
            function runNext(i) {
                if (i >= scriptData.length) return;
                const sd = scriptData[i];
                const ns = document.createElement('script');
                ns.setAttribute('data-dinamico', '1');
                if (sd.src) { ns.src = sd.src; ns.onload = () => runNext(i+1); ns.onerror = () => runNext(i+1); document.body.appendChild(ns); }
                else { ns.textContent = sd.text; document.body.appendChild(ns); runNext(i+1); }
            }
            runNext(0);
        })
        .catch(err => {
            container.innerHTML = `<div class='alert alert-danger m-3'>Erro ao carregar: ${abaSolicitada}<br><small>${err.message}</small></div>`;
        });
}

// ══════════════════════════════════════════════
// FECHAR DROPDOWNS AO CLICAR FORA
// ══════════════════════════════════════════════
function fecharDropdowns() {
    document.getElementById('notifDropdown').classList.remove('show');
    document.getElementById('perfilDropdown').classList.remove('show');
}
document.addEventListener('click', (e) => {
    if (!document.getElementById('btnNotif').contains(e.target) &&
        !document.getElementById('notifDropdown').contains(e.target)) {
        document.getElementById('notifDropdown').classList.remove('show');
    }
    if (!document.getElementById('btnPerfil').contains(e.target) &&
        !document.getElementById('perfilDropdown').contains(e.target)) {
        document.getElementById('perfilDropdown').classList.remove('show');
    }
});

// ══════════════════════════════════════════════
// NOTIFICAÇÕES
// ══════════════════════════════════════════════
function toggleNotif() {
    const dd = document.getElementById('notifDropdown');
    const pd = document.getElementById('perfilDropdown');
    pd.classList.remove('show');
    dd.classList.toggle('show');
    if (dd.classList.contains('show')) carregarNotificacoes();
}

function carregarNotificacoes() {
    fetch('pages-adm/api-notificacoes.php', { cache: 'no-store' })
        .then(r => r.json())
        .then(data => {
            const lista = document.getElementById('notifList');
            const badge = document.getElementById('notifBadge');
            const total = document.getElementById('notifTotal');

            const notifs = data.notificacoes || [];
            const qtd    = notifs.length;

            // Atualizar badge do sino
            if (qtd > 0) {
                badge.style.display = 'flex';
                badge.textContent   = qtd > 9 ? '9+' : qtd;
            } else {
                badge.style.display = 'none';
            }
            total.textContent = qtd;

            if (qtd === 0) {
                lista.innerHTML = '<div class="notif-vazia"><i class="bi bi-bell-slash fs-3 d-block mb-2"></i>Nenhuma notificação pendente</div>';
                return;
            }

            lista.innerHTML = notifs.map(n => `
                <div class="notif-item" onclick="carregarPagina('${n.acao}')">
                    <div class="notif-icon ${n.tipo}">
                        <i class="bi ${n.icone}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-medium small">${n.titulo}</div>
                        <div class="text-muted" style="font-size:.78rem;line-height:1.3">${n.texto}</div>
                    </div>
                    <i class="bi bi-chevron-right text-muted small mt-1"></i>
                </div>
            `).join('');
        })
        .catch(() => {
            document.getElementById('notifList').innerHTML = '<div class="notif-vazia text-danger">Erro ao carregar notificações</div>';
        });
}

// Atualizar badge ao carregar a página e a cada 60s
document.addEventListener('DOMContentLoaded', () => {
    carregarPagina('pagina-inicial');
    atualizarBadge();
    setInterval(atualizarBadge, 60000);
});

function atualizarBadge() {
    fetch('pages-adm/api-notificacoes.php?acao=total', { cache: 'no-store' })
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('notifBadge');
            const qtd   = data.total || 0;
            if (qtd > 0) {
                badge.style.display = 'flex';
                badge.textContent   = qtd > 9 ? '9+' : qtd;
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(() => {});
}

// ══════════════════════════════════════════════
// MENU DE PERFIL
// ══════════════════════════════════════════════
function togglePerfil() {
    const dd = document.getElementById('perfilDropdown');
    const nd = document.getElementById('notifDropdown');
    nd.classList.remove('show');
    dd.classList.toggle('show');
}

function abrirModalEditarPerfil() {
    fecharDropdowns();
    // Buscar dados atuais do usuário
    fetch('pages-adm/api-perfil.php?acao=dados', { cache: 'no-store' })
        .then(r => r.json())
        .then(data => {
            document.getElementById('perfil_nome').value  = data.nome  || '';
            document.getElementById('perfil_email').value = data.email || '';
            document.getElementById('perfil_curso').value = data.curso || '';
            document.getElementById('perfil_feedback').innerHTML = '';
            new bootstrap.Modal(document.getElementById('modalEditarPerfil')).show();
        })
        .catch(() => {
            document.getElementById('perfil_nome').value  = document.getElementById('topNome').textContent.trim();
            document.getElementById('perfil_email').value = '';
            document.getElementById('perfil_curso').value = '';
            document.getElementById('perfil_feedback').innerHTML = '';
            new bootstrap.Modal(document.getElementById('modalEditarPerfil')).show();
        });
}

function salvarPerfil() {
    const body = new FormData();
    body.append('nome',  document.getElementById('perfil_nome').value.trim());
    body.append('email', document.getElementById('perfil_email').value.trim());
    body.append('curso', document.getElementById('perfil_curso').value.trim());

    fetch('pages-adm/api-perfil.php?acao=atualizar', { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            const fb = document.getElementById('perfil_feedback');
            fb.innerHTML = `<div class="alert alert-${data.sucesso ? 'success' : 'danger'}">${data.mensagem}</div>`;
            if (data.sucesso) {
                const novoNome = document.getElementById('perfil_nome').value.trim();
                // Atualizar topbar com novo nome e iniciais
                document.getElementById('topNome').textContent = novoNome;
                const ini = novoNome.split(' ').slice(0,2).map(p => p[0]?.toUpperCase() || '').join('');
                document.getElementById('topAvatar').textContent  = ini;
                document.getElementById('dropAvatar').textContent = ini;
                document.getElementById('modalAvatar').textContent= ini;
                document.getElementById('dropNome').textContent   = novoNome;
                document.getElementById('dropEmail').textContent  = document.getElementById('perfil_email').value.trim();
                setTimeout(() => bootstrap.Modal.getInstance(document.getElementById('modalEditarPerfil')).hide(), 1500);
            }
        })
        .catch(() => {
            document.getElementById('perfil_feedback').innerHTML = '<div class="alert alert-danger">Erro de comunicação.</div>';
        });
}

function abrirModalTrocarSenha() {
    fecharDropdowns();
    ['senha_atual','nova_senha','confirma_senha'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('senha_feedback').innerHTML = '';
    resetForcaSenha();
    new bootstrap.Modal(document.getElementById('modalTrocarSenha')).show();
}

function salvarSenha() {
    const body = new FormData();
    body.append('senha_atual', document.getElementById('senha_atual').value);
    body.append('nova_senha',  document.getElementById('nova_senha').value);
    body.append('confirma',    document.getElementById('confirma_senha').value);

    fetch('pages-adm/api-perfil.php?acao=senha', { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            const fb = document.getElementById('senha_feedback');
            fb.innerHTML = `<div class="alert alert-${data.sucesso ? 'success' : 'danger'}">${data.mensagem}</div>`;
            if (data.sucesso) setTimeout(() => bootstrap.Modal.getInstance(document.getElementById('modalTrocarSenha')).hide(), 1500);
        })
        .catch(() => {
            document.getElementById('senha_feedback').innerHTML = '<div class="alert alert-danger">Erro de comunicação.</div>';
        });
}

// ── Mostrar/ocultar senha ──
function toggleSenha(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

// ── Indicador de força da senha ──
document.addEventListener('DOMContentLoaded', () => {
    const novaSenhaInput = document.getElementById('nova_senha');
    if (novaSenhaInput) {
        novaSenhaInput.addEventListener('input', () => calcularForca(novaSenhaInput.value));
    }
});

function calcularForca(senha) {
    let pontos = 0;
    if (senha.length >= 6)  pontos++;
    if (senha.length >= 10) pontos++;
    if (/[A-Z]/.test(senha) && /[a-z]/.test(senha)) pontos++;
    if (/[0-9]/.test(senha) && /[^A-Za-z0-9]/.test(senha)) pontos++;

    const cores  = ['#ef4444','#f97316','#eab308','#22c55e'];
    const labels = ['Muito fraca','Fraca','Boa','Forte'];
    for (let i = 1; i <= 4; i++) {
        const b = document.getElementById('f' + i);
        b.style.background = i <= pontos ? cores[pontos-1] : '#e2e8f0';
    }
    document.getElementById('forca_label').textContent = senha.length > 0 ? labels[pontos-1] || '' : '';
}
function resetForcaSenha() {
    for (let i = 1; i <= 4; i++) document.getElementById('f' + i).style.background = '#e2e8f0';
    document.getElementById('forca_label').textContent = '';
}
</script>
</body>
</html>
