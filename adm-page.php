<?php
session_start();
require_once 'conexao/conexao.php';
require_once 'lib/Logger.php';
Logger::setPDO($pdo);
if (empty($_SESSION['id_usuario'])) { header("Location: login-page.php"); exit(); }
$perfil = strtolower($_SESSION['perfil'] ?? '');
if (!str_contains($perfil, 'admin')) { header("Location: login-page.php"); exit(); }

$nomeUsuario = htmlspecialchars($_SESSION['nome'] ?? 'Usuário');
$emailUsuario = htmlspecialchars($_SESSION['email'] ?? '');
$perfilUsuario = htmlspecialchars($_SESSION['perfil'] ?? '');
$partes = array_filter(explode(' ', $_SESSION['nome'] ?? 'U'));
$iniciais = strtoupper(implode('', array_map(fn($p) => mb_substr($p, 0, 1), array_slice($partes, 0, 2))));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPA - Administração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/adm-page.css">
    <style>
        /* ── Notificações ── */
        .notif-btn{position:relative;background:none;border:none;padding:6px 8px;color:inherit;cursor:pointer;border-radius:8px;transition:background .15s}
        .notif-btn:hover{background:rgba(255,255,255,.12)}
        .notif-badge{position:absolute;top:2px;right:2px;background:#ef4444;color:#fff;border-radius:999px;font-size:.6rem;font-weight:700;min-width:16px;height:16px;display:none;align-items:center;justify-content:center;padding:0 3px;line-height:1}
        .notif-drop{position:absolute;right:0;top:calc(100% + 10px);width:330px;background:#fff;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,.18);z-index:9999;display:none;flex-direction:column;overflow:hidden}
        .notif-drop.show{display:flex}
        .notif-drop-header{padding:13px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between}
        .notif-drop-header h6{margin:0;font-weight:700;font-size:.88rem;color:#1e293b}
        .notif-list{max-height:320px;overflow-y:auto}
        .notif-item{display:flex;align-items:flex-start;gap:10px;padding:11px 16px;border-bottom:1px solid #f8fafc;cursor:pointer;transition:background .15s;color:#1e293b;text-decoration:none}
        .notif-item:hover{background:#f8fafc}
        .notif-icon{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0;margin-top:2px}
        .ni-projeto{background:#fef9c3;color:#ca8a04}
        .ni-documento{background:#dbeafe;color:#2563eb}
        .ni-usuario{background:#dcfce7;color:#16a34a}
        .notif-vazia{padding:24px;text-align:center;color:#94a3b8;font-size:.85rem}
        .notif-drop-footer{padding:10px 16px;text-align:center;border-top:1px solid #f1f5f9}

        /* ── Perfil ── */
        .perfil-btn{display:flex;align-items:center;gap:8px;background:none;border:none;cursor:pointer;color:inherit;padding:4px 8px;border-radius:8px;transition:background .15s}
        .perfil-btn:hover{background:rgba(255,255,255,.12)}
        .avatar{width:34px;height:34px;border-radius:50%;background:#3b82f6;color:#fff;font-size:.72rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;user-select:none}
        .perfil-drop{position:absolute;right:0;top:calc(100% + 10px);width:255px;background:#fff;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,.18);z-index:9999;display:none;flex-direction:column;overflow:hidden}
        .perfil-drop.show{display:flex}
        .perfil-drop-header{padding:14px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:12px}
        .perfil-drop-header .avatar{width:42px;height:42px;font-size:.85rem}
        .perfil-drop-header h6{margin:0;font-weight:700;font-size:.83rem;color:#1e293b}
        .perfil-drop-header small{color:#64748b;font-size:.73rem}
        .pm-item{display:flex;align-items:center;gap:10px;padding:10px 16px;color:#374151;font-size:.85rem;transition:background .15s;cursor:pointer;border:none;background:none;width:100%;text-decoration:none}
        .pm-item:hover{background:#f8fafc;color:#1e293b}
        .pm-item i{width:18px;text-align:center;color:#64748b;font-size:.95rem}
        .pm-item.sair{color:#ef4444;border-top:1px solid #f1f5f9}
        .pm-item.sair i{color:#ef4444}

        /* ── Modais de perfil ── */
        .input-olho{position:relative}
        .input-olho input{padding-right:44px}
        .btn-olho-m{position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;font-size:1rem}
        .forca-b{display:flex;gap:4px;margin-top:5px}
        .forca-b div{flex:1;height:4px;border-radius:2px;background:#e2e8f0;transition:background .3s}
        .forca-lbl{font-size:.73rem;color:#64748b;margin-top:2px}
    </style>
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
<div class="wrapper">

    <!-- SIDEBAR -->
    <nav id="sidebar">
        <div class="sidebar-toggle-wrap">
            <button class="sidebar-toggle" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
            <div class="sidebar-brand">
                <div>
                    <span class="sidebar-brand-text">SIMPA</span>
                    <span class="sidebar-brand-sub">Sistema Integrado de Monitoramento de Projetos Acadêmicos</span>
                </div>
            </div>
        </div>
        <ul class="list-unstyled components">
            <li><a href="#" id="menu-pagina-inicial" onclick="nav('pagina-inicial')" title="Página Inicial"><i class="bi bi-house-door"></i><span class="nav-label">Página Inicial</span></a></li>
            <li><a href="#" id="menu-usuarios"       onclick="nav('usuarios')"       title="Usuários"><i class="bi bi-people"></i><span class="nav-label">Usuários</span></a></li>
            <li><a href="#" id="menu-participacoes"  onclick="nav('participacoes')"  title="Participações"><i class="bi bi-diagram-3"></i><span class="nav-label">Participações</span></a></li>
            <li><a href="#" id="menu-projetos"       onclick="nav('projetos')"       title="Projetos"><i class="bi bi-folder"></i><span class="nav-label">Projetos</span></a></li>
            <li><a href="#" id="menu-documentos"     onclick="nav('documentos')"     title="Documentos"><i class="bi bi-file-earmark-text"></i><span class="nav-label">Documentos</span></a></li>
            <li><a href="#" id="menu-visitas"        onclick="nav('visitas')"        title="Visitas"><i class="bi bi-bar-chart-fill"></i><span class="nav-label">Visitas</span></a></li>
            <li><a href="#" id="menu-relatorios"     onclick="nav('relatorios')"     title="Relatórios"><i class="bi bi-graph-up"></i><span class="nav-label">Relatórios</span></a></li>
            <li><a href="#" id="menu-logs" onclick="nav('logs')" title="Logs de Auditoria"><i class="bi bi-journal-text"></i><span class="nav-label">Logs</span></a></li>
            <li class="sidebar-sair"><a href="pages-adm/sair.php" title="Sair"><i class="bi bi-box-arrow-left"></i><span class="nav-label">Sair</span></a></li>
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

                <!-- NOTIFICAÇÕES -->
                <div class="position-relative">
                    <button class="notif-btn" id="btnNotif" onclick="toggleNotif()">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="notif-badge" id="notifBadge">0</span>
                    </button>
                    <div class="notif-drop" id="notifDrop">
                        <div class="notif-drop-header">
                            <h6><i class="bi bi-bell me-2"></i>Notificações</h6>
                            <div style="display:flex;gap:6px;align-items:center;">
                                <button onclick="lerTodasAdm()" style="background:none;border:none;font-size:.75rem;color:#3b82f6;cursor:pointer;padding:0;white-space:nowrap;">Marcar todas como lidas</button>
                                <button onclick="limparAdm()" style="background:none;border:none;font-size:.75rem;color:#ef4444;cursor:pointer;padding:0;">Limpar</button>
                            </div>
                        </div>
                        <div class="notif-list" id="notifList">
                            <div class="notif-vazia">Carregando...</div>
                        </div>
                        <div class="notif-drop-footer">
                            <small class="text-muted" style="font-size:.75rem">Atualiza automaticamente a cada 60s</small>
                        </div>
                    </div>
                </div>

                <!-- PERFIL -->
                <div class="position-relative">
                    <button class="perfil-btn" id="btnPerfil" onclick="togglePerfil()">
                        <div class="avatar" id="topAvatar"><?= $iniciais ?></div>
                        <span class="fw-medium d-none d-sm-inline" id="topNome"><?= $nomeUsuario ?></span>
                        <i class="bi bi-chevron-down small d-none d-sm-inline"></i>
                    </button>
                    <div class="perfil-drop" id="perfilDrop">
                        <div class="perfil-drop-header">
                            <div class="avatar" id="dropAvatar"><?= $iniciais ?></div>
                            <div>
                                <h6 id="dropNome"><?= $nomeUsuario ?></h6>
                                <small id="dropEmail"><?= $emailUsuario ?></small><br>
                                <span class="badge bg-secondary mt-1" style="font-size:.68rem"><?= $perfilUsuario ?></span>
                            </div>
                        </div>
                        <button class="pm-item" onclick="abrirEditarPerfil()"><i class="bi bi-person-gear"></i>Editar Perfil</button>
                        <button class="pm-item" onclick="abrirTrocarSenha()"><i class="bi bi-key"></i>Trocar Senha</button>
                        <a class="pm-item sair" href="pages-adm/sair.php"><i class="bi bi-box-arrow-right"></i>Sair</a>
                    </div>
                </div>

            </div>
        </header>

        <div class="dashboard-container" id="conteudo-dinamico">
            <div class="text-center mt-5"><div class="spinner-border text-primary"></div></div>
        </div>
    </div>
</div>

<!-- MODAL EDITAR PERFIL -->
<div class="modal fade" id="modalPerfil" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title fw-bold"><i class="bi bi-person-gear me-2"></i>Editar Perfil</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="text-center mb-3">
            <div class="avatar mx-auto mb-1" style="width:52px;height:52px;font-size:1.2rem" id="modalAv"><?= $iniciais ?></div>
            <small class="text-muted">Administrador</small>
        </div>
        <div class="mb-3"><label class="form-label fw-medium">Nome Completo</label><input type="text" class="form-control" id="pNome"></div>
        <div class="mb-3"><label class="form-label fw-medium">E-mail</label><input type="email" class="form-control" id="pEmail"></div>
        <div class="mb-3"><label class="form-label fw-medium">Curso / Departamento</label><input type="text" class="form-control" id="pCurso"></div>
        <div id="pfb"></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-primary" onclick="salvarPerfil()"><i class="bi bi-save me-1"></i>Salvar</button></div>
  </div></div>
</div>

<!-- MODAL TROCAR SENHA -->
<div class="modal fade" id="modalSenha" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title fw-bold"><i class="bi bi-key me-2"></i>Trocar Senha</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="mb-3"><label class="form-label fw-medium">Senha Atual</label>
            <div class="input-olho"><input type="password" class="form-control" id="sAtual"><button type="button" class="btn-olho-m" onclick="verM('sAtual')">👁</button></div>
        </div>
        <div class="mb-3"><label class="form-label fw-medium">Nova Senha</label>
            <div class="input-olho"><input type="password" class="form-control" id="sNova" oninput="forcaM(this.value)"><button type="button" class="btn-olho-m" onclick="verM('sNova')">👁</button></div>
            <div class="forca-b"><div id="m1"></div><div id="m2"></div><div id="m3"></div><div id="m4"></div></div>
            <div class="forca-lbl" id="mLbl"></div>
        </div>
        <div class="mb-3"><label class="form-label fw-medium">Confirmar Nova Senha</label>
            <div class="input-olho"><input type="password" class="form-control" id="sConf"><button type="button" class="btn-olho-m" onclick="verM('sConf')">👁</button></div>
        </div>
        <div id="sfb"></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-warning" onclick="salvarSenha()"><i class="bi bi-key me-1"></i>Alterar</button></div>
  </div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Sidebar ──────────────────────────────────────────────────
function isOverlay(){ return window.innerWidth < 992; }
function toggleSidebar(){ isOverlay() ? (document.getElementById('sidebar').classList.toggle('open'), document.getElementById('sidebarOverlay').classList.toggle('active')) : document.getElementById('sidebar').classList.toggle('expanded'); }
function closeSidebar(){ document.getElementById('sidebar').classList.remove('open'); document.getElementById('sidebarOverlay').classList.remove('active'); }
window.addEventListener('resize', ()=>{ if(!isOverlay()){ document.getElementById('sidebar').classList.remove('open'); document.getElementById('sidebarOverlay').classList.remove('active'); }});

// ── Navegação ────────────────────────────────────────────────
function nav(aba){
    document.querySelectorAll('#sidebar ul li a').forEach(l=>l.classList.remove('active'));
    const m=document.getElementById('menu-'+aba); if(m) m.classList.add('active');
    if(isOverlay()) closeSidebar();
    fecharDrops();
    const mapa = { 'pagina-inicial':'pages-adm/pagina-inicial.php', usuarios:'pages-adm/usuarios.php', participacoes:'pages-adm/participacoes.php', projetos:'pages-adm/projetos.php', documentos:'pages-adm/documentos.php', visitas:'pages-adm/visitas.php', relatorios:'pages-adm/relatorios.php', logs:'pages-adm/logs.php' };
    const url = mapa[aba] || 'pages-adm/pagina-inicial.php';
    const cont = document.getElementById('conteudo-dinamico');
    cont.innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-primary"><span class="visually-hidden">Carregando...</span></div></div>';
    fetch(url, {cache:'no-store'})
        .then(r=>{ if(!r.ok) throw new Error('HTTP '+r.status); return r.text(); })
        .then(html=>{
            document.querySelectorAll('script[data-din]').forEach(s=>s.remove());
            const tmp=document.createElement('div'); tmp.innerHTML=html;
            const scs=[]; tmp.querySelectorAll('script').forEach(s=>{ scs.push({src:s.src,txt:s.textContent}); s.remove(); });
            cont.innerHTML=tmp.innerHTML;
            function runNext(i){ if(i>=scs.length) return; const sd=scs[i], ns=document.createElement('script'); ns.setAttribute('data-din','1'); if(sd.src){ ns.src=sd.src; ns.onload=()=>runNext(i+1); ns.onerror=()=>runNext(i+1); document.body.appendChild(ns); } else { ns.textContent=sd.txt; document.body.appendChild(ns); runNext(i+1); } }
            runNext(0);
        })
        .catch(err=>{ cont.innerHTML=`<div class='alert alert-danger m-3'>Erro ao carregar: ${aba}<br><small>${err.message}</small></div>`; });
}
// Alias para compatibilidade com views antigas
function carregarPagina(aba){ nav(aba); }

// ── Fechar drops ao clicar fora ──────────────────────────────
function fecharDrops(){ document.getElementById('notifDrop').classList.remove('show'); document.getElementById('perfilDrop').classList.remove('show'); }
document.addEventListener('click', e=>{
    if(!document.getElementById('btnNotif').contains(e.target) && !document.getElementById('notifDrop').contains(e.target)) document.getElementById('notifDrop').classList.remove('show');
    if(!document.getElementById('btnPerfil').contains(e.target) && !document.getElementById('perfilDrop').contains(e.target)) document.getElementById('perfilDrop').classList.remove('show');
});

// ── Notificações ─────────────────────────────────────────────
const _NOTIF_ADM_UID   = '<?= (int)($_SESSION['id_usuario'] ?? 0) ?>';
const _NK_LIDAS        = 'notif_lidas_adm_' + _NOTIF_ADM_UID;
const _NK_DESC         = 'notif_desc_adm_'  + _NOTIF_ADM_UID;

function _admGetSet(key) { try { return new Set(JSON.parse(localStorage.getItem(key)||'[]')); } catch(e){ return new Set(); } }
function _admSaveSet(key,set) { try { localStorage.setItem(key, JSON.stringify([...set].slice(-500))); } catch(e){} }
function _admKey(n) { return (n.titulo||'') + '||' + (n.texto||''); }

function toggleNotif(){ const d=document.getElementById('notifDrop'); document.getElementById('perfilDrop').classList.remove('show'); d.classList.toggle('show'); if(d.classList.contains('show')) carregarNotifs(); }

function carregarNotifs(){
    fetch('pages-adm/api-notificacoes.php',{cache:'no-store'}).then(r=>r.json()).then(data=>{
        const lista=document.getElementById('notifList'), badge=document.getElementById('notifBadge');
        const lidas=_admGetSet(_NK_LIDAS), desc=_admGetSet(_NK_DESC);
        const notifs=(data.notificacoes||[]).filter(n=>!desc.has(_admKey(n)));
        const naoLidos=notifs.filter(n=>!lidas.has(_admKey(n))).length;
        badge.style.display=naoLidos>0?'flex':'none'; badge.textContent=naoLidos>9?'9+':naoLidos;
        if(!notifs.length){ lista.innerHTML='<div class="notif-vazia"><i class="bi bi-bell-slash d-block fs-4 mb-1"></i>Nenhuma pendência</div>'; return; }
        lista.innerHTML=notifs.map(n=>{
            const key=_admKey(n);
            const lida=lidas.has(key)?'1':'0';
            return `<div class="notif-item" data-notif-key="${key.replace(/"/g,'&quot;')}" data-lida="${lida}" onclick="nav('${n.acao}')" style="${lida==='1'?'opacity:.6;background:#f8fafc':''}">
                <div class="notif-icon ni-${n.tipo}"><i class="bi ${n.icone}"></i></div>
                <div class="flex-grow-1"><div class="fw-medium" style="font-size:.82rem">${n.titulo}</div><div class="text-muted" style="font-size:.76rem">${n.texto}</div></div>
                <i class="bi bi-chevron-right text-muted small mt-1"></i>
            </div>`;
        }).join('');
    }).catch(()=>{ document.getElementById('notifList').innerHTML='<div class="notif-vazia text-danger">Erro ao carregar</div>'; });
}

function lerTodasAdm(){
    const lidas=_admGetSet(_NK_LIDAS);
    document.querySelectorAll('#notifList .notif-item').forEach(function(item){
        const key=item.dataset.notifKey; if(!key) return;
        lidas.add(key); item.dataset.lida='1'; item.style.opacity='.6'; item.style.background='#f8fafc';
    });
    _admSaveSet(_NK_LIDAS,lidas);
    const badge=document.getElementById('notifBadge');
    badge.textContent='0'; badge.style.display='none';
}

function limparAdm(){
    const desc=_admGetSet(_NK_DESC);
    document.querySelectorAll('#notifList .notif-item').forEach(function(item){
        const key=item.dataset.notifKey; if(key) desc.add(key);
    });
    _admSaveSet(_NK_DESC,desc);
    document.getElementById('notifList').innerHTML='<div class="notif-vazia"><i class="bi bi-bell-slash d-block fs-4 mb-1"></i>Nenhuma pendência</div>';
    const badge=document.getElementById('notifBadge'); badge.textContent='0'; badge.style.display='none';
}

function atualizarBadge(){
    fetch('pages-adm/api-notificacoes.php?acao=total',{cache:'no-store'}).then(r=>r.json()).then(d=>{
        const lidas=_admGetSet(_NK_LIDAS), desc=_admGetSet(_NK_DESC);
        // O badge real é controlado pelo carregarNotifs; aqui só atualiza se o painel estiver fechado
        if(!document.getElementById('notifDrop').classList.contains('show')) carregarNotifs();
    }).catch(()=>{});
}

// ── Perfil ───────────────────────────────────────────────────
function togglePerfil(){ const d=document.getElementById('perfilDrop'); document.getElementById('notifDrop').classList.remove('show'); d.classList.toggle('show'); }

function abrirEditarPerfil(){
    fecharDrops();
    fetch('pages-adm/api-perfil.php?acao=dados',{cache:'no-store'}).then(r=>r.json()).then(d=>{
        document.getElementById('pNome').value  = d.nome  || '';
        document.getElementById('pEmail').value = d.email || '';
        document.getElementById('pCurso').value = d.curso || '';
        document.getElementById('pfb').innerHTML='';
        new bootstrap.Modal(document.getElementById('modalPerfil')).show();
    }).catch(()=>{ document.getElementById('pfb').innerHTML=''; new bootstrap.Modal(document.getElementById('modalPerfil')).show(); });
}

function salvarPerfil(){
    const fd=new FormData(); fd.append('nome',document.getElementById('pNome').value.trim()); fd.append('email',document.getElementById('pEmail').value.trim()); fd.append('curso',document.getElementById('pCurso').value.trim());
    fetch('pages-adm/api-perfil.php?acao=atualizar',{method:'POST',body:fd}).then(r=>r.json()).then(d=>{
        document.getElementById('pfb').innerHTML=`<div class="alert alert-${d.sucesso?'success':'danger'} py-2 mt-2">${d.mensagem}</div>`;
        if(d.sucesso){
            const n=document.getElementById('pNome').value.trim();
            const ini=n.split(' ').slice(0,2).map(p=>p[0]?.toUpperCase()||'').join('');
            ['topAvatar','dropAvatar','modalAv'].forEach(id=>document.getElementById(id).textContent=ini);
            document.getElementById('topNome').textContent=n;
            document.getElementById('dropNome').textContent=n;
            document.getElementById('dropEmail').textContent=document.getElementById('pEmail').value.trim();
            setTimeout(()=>bootstrap.Modal.getInstance(document.getElementById('modalPerfil')).hide(),1500);
        }
    }).catch(()=>{ document.getElementById('pfb').innerHTML='<div class="alert alert-danger py-2 mt-2">Erro de comunicação.</div>'; });
}

function abrirTrocarSenha(){
    fecharDrops();
    ['sAtual','sNova','sConf'].forEach(id=>document.getElementById(id).value='');
    for(let i=1;i<=4;i++) document.getElementById('m'+i).style.background='#e2e8f0';
    document.getElementById('mLbl').textContent='';
    document.getElementById('sfb').innerHTML='';
    new bootstrap.Modal(document.getElementById('modalSenha')).show();
}

function salvarSenha(){
    const fd=new FormData(); fd.append('senha_atual',document.getElementById('sAtual').value); fd.append('nova_senha',document.getElementById('sNova').value); fd.append('confirma',document.getElementById('sConf').value);
    fetch('pages-adm/api-perfil.php?acao=senha',{method:'POST',body:fd}).then(r=>r.json()).then(d=>{
        document.getElementById('sfb').innerHTML=`<div class="alert alert-${d.sucesso?'success':'danger'} py-2 mt-2">${d.mensagem}</div>`;
        if(d.sucesso) setTimeout(()=>bootstrap.Modal.getInstance(document.getElementById('modalSenha')).hide(),1500);
    }).catch(()=>{ document.getElementById('sfb').innerHTML='<div class="alert alert-danger py-2 mt-2">Erro de comunicação.</div>'; });
}

function verM(id){ const el=document.getElementById(id); el.type=el.type==='password'?'text':'password'; }
function forcaM(v){ let p=0; if(v.length>=6)p++; if(v.length>=10)p++; if(/[A-Z]/.test(v)&&/[a-z]/.test(v))p++; if(/[0-9]/.test(v)&&/[^A-Za-z0-9]/.test(v))p++; const c=['#ef4444','#f97316','#eab308','#22c55e'],l=['Muito fraca','Fraca','Boa','Forte']; for(let i=1;i<=4;i++) document.getElementById('m'+i).style.background=i<=p?c[p-1]:'#e2e8f0'; document.getElementById('mLbl').textContent=v.length>0?(l[p-1]||''):''; }

// ── Init ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', ()=>{
    nav('pagina-inicial');
    atualizarBadge();
    setInterval(atualizarBadge, 60000);
});
</script>
</body>
</html>
