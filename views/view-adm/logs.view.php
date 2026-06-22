<style>
.badge-modulo,.badge-acao{padding:3px 9px;border-radius:6px;font-size:.72rem;font-weight:600;white-space:nowrap;display:inline-flex;align-items:center;gap:4px}
.ctx-pill{background:#f1f5f9;border-radius:4px;padding:1px 6px;font-size:.7rem;color:#64748b;margin:2px 2px 0 0;display:inline-block}
.pag-btn{border:1px solid #e2e8f0;background:#fff;border-radius:6px;padding:4px 10px;cursor:pointer;font-size:.82rem;color:#374151;transition:all .15s}
.pag-btn:hover:not(:disabled){background:#f8fafc;border-color:#cbd5e1}
.pag-btn:disabled{opacity:.4;cursor:default}
.pag-btn.ativo{background:#2B3C50;color:#fff;border-color:#2B3C50}
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Logs de Auditoria</h3>
        <p class="text-muted mb-0">Histórico completo de todas as ações realizadas no sistema</p>
    </div>
    <button class="btn btn-outline-success btn-sm" onclick="exportarCSV()">
        <i class="bi bi-filetype-csv me-1"></i>Exportar CSV
    </button>
</div>

<!-- CARDS DE ESTATÍSTICAS -->
<div class="row g-3 mb-4" id="cardsStats">
    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="icon-circle" style="background:#dbeafe;color:#1d4ed8"><i class="bi bi-journal-text"></i></div>
            <div><h4 class="mb-0 fw-bold" id="st_total">—</h4><small class="text-muted">Total</small></div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="icon-circle" style="background:#dcfce7;color:#16a34a"><i class="bi bi-check-circle"></i></div>
            <div><h4 class="mb-0 fw-bold" id="st_login_ok">—</h4><small class="text-muted">Logins OK</small></div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="icon-circle" style="background:#fee2e2;color:#dc2626"><i class="bi bi-x-circle"></i></div>
            <div><h4 class="mb-0 fw-bold" id="st_login_falha">—</h4><small class="text-muted">Falhas Login</small></div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="icon-circle" style="background:#fef9c3;color:#a16207"><i class="bi bi-pencil-square"></i></div>
            <div><h4 class="mb-0 fw-bold" id="st_mod">—</h4><small class="text-muted">Modificações</small></div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="icon-circle" style="background:#fee2e2;color:#dc2626"><i class="bi bi-trash"></i></div>
            <div><h4 class="mb-0 fw-bold" id="st_rem">—</h4><small class="text-muted">Remoções</small></div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="icon-circle" style="background:#f3e8ff;color:#7c3aed"><i class="bi bi-clock-history"></i></div>
            <div><h4 class="mb-0 fw-bold" id="st_24h">—</h4><small class="text-muted">Últimas 24h</small></div>
        </div>
    </div>
</div>

<!-- FILTROS -->
<div class="content-card mb-4 p-3">
    <div class="row g-2">
        <div class="col-12 col-md-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="fBusca" class="form-control border-start-0"
                       placeholder="Buscar descrição ou usuário...">
            </div>
        </div>
        <div class="col-6 col-md-2">
            <select class="form-select" id="fModulo">
                <option value="">Módulo (Todos)</option>
                <option value="USUARIOS">Usuários</option>
                <option value="PROJETOS">Projetos</option>
                <option value="PARTICIPACOES">Participações</option>
                <option value="DOCUMENTOS">Documentos</option>
                <option value="LOGIN">Login</option>
                <option value="PERFIL">Perfil</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select class="form-select" id="fAcao">
                <option value="">Ação (Todas)</option>
                <option value="CRIAR">Criar</option>
                <option value="EDITAR">Editar</option>
                <option value="EXCLUIR">Excluir</option>
                <option value="ATIVAR">Ativar</option>
                <option value="DESATIVAR">Desativar</option>
                <option value="APROVAR">Aprovar</option>
                <option value="REJEITAR">Rejeitar</option>
                <option value="VINCULAR">Vincular</option>
                <option value="ALTERAR_SENHA">Alterar Senha</option>
                <option value="LOGIN_OK">Login OK</option>
                <option value="LOGIN_FALHA">Login Falha</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <input type="date" class="form-control" id="fDataInicio" placeholder="De">
        </div>
        <div class="col-6 col-md-2">
            <input type="date" class="form-control" id="fDataFim" placeholder="Até">
        </div>
        <div class="col-12 col-md-1">
            <button class="btn btn-primary w-100" onclick="buscar(1)" title="Buscar">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-2">
        <button class="btn btn-link btn-sm text-muted p-0" onclick="limparFiltros()">
            <i class="bi bi-x-circle me-1"></i>Limpar filtros
        </button>
        <div class="d-flex gap-2 align-items-center">
            <small class="text-muted">Por página:</small>
            <select class="form-select form-select-sm" id="fLimite" style="width:70px" onchange="buscar(1)">
                <option value="50">50</option>
                <option value="100" selected>100</option>
                <option value="200">200</option>
                <option value="500">500</option>
            </select>
        </div>
    </div>
</div>

<!-- TABELA -->
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="fw-bold mb-0">Registros de Auditoria</h5>
        <span class="text-muted small" id="infoResultados">carregando...</span>
    </div>

    <div id="spinnerLogs" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
        <p class="text-muted mt-2 small">Carregando registros...</p>
    </div>

    <div class="d-none" id="wrapTabela">
        <div class="table-responsive">
            <table class="table table-hover align-middle" style="font-size:.83rem">
                <thead class="table-light">
                    <tr class="text-muted" style="font-size:.72rem">
                        <th style="width:145px">DATA / HORA</th>
                        <th style="width:125px">MÓDULO</th>
                        <th style="width:110px">AÇÃO</th>
                        <th>DESCRIÇÃO / CONTEXTO</th>
                        <th style="width:160px">RESPONSÁVEL</th>
                        <th style="width:105px">IP</th>
                    </tr>
                </thead>
                <tbody id="tbodyLogs"></tbody>
            </table>
        </div>

        <!-- Paginação -->
        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2" id="paginacao">
            <small class="text-muted" id="infoPag"></small>
            <div class="d-flex gap-1 flex-wrap" id="btnsPag"></div>
        </div>
    </div>

    <div class="text-center py-5 text-muted d-none" id="semLogs">
        <i class="bi bi-journal-x fs-1 d-block mb-2 opacity-25"></i>
        Nenhum registro encontrado com os filtros aplicados.
    </div>
</div>

<script>
function _escHtml(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

const MODULO_ESTILOS = {
    USUARIOS:      { bg:'#dbeafe', cor:'#1d4ed8', icone:'bi-people' },
    PROJETOS:      { bg:'#dcfce7', cor:'#15803d', icone:'bi-folder' },
    PARTICIPACOES: { bg:'#fef9c3', cor:'#a16207', icone:'bi-diagram-3' },
    DOCUMENTOS:    { bg:'#f3e8ff', cor:'#7c3aed', icone:'bi-file-earmark-text' },
    LOGIN:         { bg:'#ffedd5', cor:'#c2410c', icone:'bi-box-arrow-in-right' },
    PERFIL:        { bg:'#e0f2fe', cor:'#0369a1', icone:'bi-person-gear' },
    SISTEMA:       { bg:'#f1f5f9', cor:'#475569', icone:'bi-gear' },
};
const ACAO_ESTILOS = {
    CRIAR:         { bg:'#dcfce7', cor:'#16a34a' },
    EDITAR:        { bg:'#dbeafe', cor:'#2563eb' },
    EXCLUIR:       { bg:'#fee2e2', cor:'#dc2626' },
    ATIVAR:        { bg:'#dcfce7', cor:'#16a34a' },
    DESATIVAR:     { bg:'#fef3c7', cor:'#d97706' },
    APROVAR:       { bg:'#dcfce7', cor:'#16a34a' },
    REJEITAR:      { bg:'#fee2e2', cor:'#dc2626' },
    VINCULAR:      { bg:'#e0f2fe', cor:'#0284c7' },
    DESVINCULAR:   { bg:'#fef3c7', cor:'#d97706' },
    ALTERAR_SENHA: { bg:'#f3e8ff', cor:'#7c3aed' },
    LOGIN_OK:      { bg:'#dcfce7', cor:'#16a34a' },
    LOGIN_FALHA:   { bg:'#fee2e2', cor:'#dc2626' },
    EXPORTAR:      { bg:'#f1f5f9', cor:'#475569' },
};

let paginaAtual = 1;
let totalRegistros = 0;

// Busca ao pressionar Enter
document.getElementById('fBusca').addEventListener('keydown', e => { if (e.key === 'Enter') buscar(1); });

// Carregar ao iniciar
buscar(1);

function getFiltros() {
    return {
        busca:       document.getElementById('fBusca').value.trim(),
        modulo:      document.getElementById('fModulo').value,
        acao_filtro: document.getElementById('fAcao').value,
        data_inicio: document.getElementById('fDataInicio').value,
        data_fim:    document.getElementById('fDataFim').value,
        limite:      document.getElementById('fLimite').value,
    };
}

function buscar(pagina = 1) {
    paginaAtual = pagina;
    const f = getFiltros();
    const limite  = parseInt(f.limite);
    const offset  = (pagina - 1) * limite;

    document.getElementById('spinnerLogs').classList.remove('d-none');
    document.getElementById('wrapTabela').classList.add('d-none');
    document.getElementById('semLogs').classList.add('d-none');
    document.getElementById('infoResultados').textContent = 'buscando...';

    const params = new URLSearchParams({
        acao: 'listar', limite, offset,
        busca: f.busca, modulo: f.modulo,
        acao_filtro: f.acao_filtro,
        data_inicio: f.data_inicio,
        data_fim:    f.data_fim,
    });

    fetch('pages-adm/api-logs.php?' + params, { cache: 'no-store' })
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.text(); // pegar texto primeiro para debugar JSON inválido
        })
        .then(texto => {
            let data;
            try { data = JSON.parse(texto); }
            catch(e) {
                throw new Error('Resposta inválida do servidor: ' + texto.substring(0, 200));
            }

            document.getElementById('spinnerLogs').classList.add('d-none');

            // Erro da API (tabela não existe, acesso negado, etc.)
            if (data.erro || data.sucesso === false) {
                const msg = data.mensagem || data.erro || 'Erro desconhecido';
                const dica = data.dica ? '<br><small class="text-muted">' + data.dica + '</small>' : '';
                document.getElementById('semLogs').innerHTML =
                    '<i class="bi bi-exclamation-triangle fs-1 d-block mb-2 text-warning opacity-75"></i>' +
                    '<div class="fw-medium">' + msg + '</div>' + dica;
                document.getElementById('semLogs').classList.remove('d-none');
                document.getElementById('infoResultados').textContent = 'Erro';
                return;
            }

            totalRegistros = data.total || 0;

            // Atualizar estatísticas
            if (data.estatisticas) {
                const s = data.estatisticas;
                document.getElementById('st_total').textContent       = parseInt(s.total || 0).toLocaleString('pt-BR');
                document.getElementById('st_login_ok').textContent    = s.login_ok     || 0;
                document.getElementById('st_login_falha').textContent = s.login_falha  || 0;
                document.getElementById('st_mod').textContent         = s.modificacoes || 0;
                document.getElementById('st_rem').textContent         = s.remocoes     || 0;
                document.getElementById('st_24h').textContent         = s.ultimas_24h  || 0;
            }

            const logs = data.logs || [];
            document.getElementById('infoResultados').textContent =
                totalRegistros.toLocaleString('pt-BR') + ' registros no total';

            if (!logs.length) {
                document.getElementById('semLogs').innerHTML =
                    '<i class="bi bi-journal-x fs-1 d-block mb-2 opacity-25"></i>' +
                    (document.getElementById('fBusca').value || document.getElementById('fModulo').value
                        ? 'Nenhum registro encontrado com os filtros aplicados.'
                        : 'Nenhum registro de auditoria ainda.<br><small class="text-muted">As ações realizadas no sistema aparecerão aqui.</small>');
                document.getElementById('semLogs').classList.remove('d-none');
                document.getElementById('btnsPag').innerHTML = '';
                document.getElementById('infoPag').textContent = '';
                return;
            }

            renderizarTabela(logs);
            renderizarPaginacao(pagina, limite, totalRegistros);
            document.getElementById('wrapTabela').classList.remove('d-none');
        })
        .catch(err => {
            document.getElementById('spinnerLogs').classList.add('d-none');
            document.getElementById('infoResultados').textContent = 'Erro ao carregar';
            document.getElementById('semLogs').innerHTML =
                '<i class="bi bi-wifi-off fs-1 d-block mb-2 text-danger opacity-75"></i>' +
                '<div class="fw-medium">Falha na comunicação com o servidor</div>' +
                '<small class="text-muted">' + err.message + '</small>';
            document.getElementById('semLogs').classList.remove('d-none');
            console.error('Logs error:', err);
        });
}

function renderizarTabela(logs) {
    document.getElementById('tbodyLogs').innerHTML = logs.map(log => {
        const mc = MODULO_ESTILOS[log.modulo] || MODULO_ESTILOS.SISTEMA;
        const ac = ACAO_ESTILOS[log.acao]     || { bg:'#f1f5f9', cor:'#475569' };
        const labelAcao = log.acao.replace(/_/g, ' ');

        const badgeMod = `<span class="badge-modulo" style="background:${mc.bg};color:${mc.cor}">
            <i class="bi ${mc.icone}"></i>${log.modulo}
        </span>`;
        const badgeAc = `<span class="badge-acao" style="background:${ac.bg};color:${ac.cor}">${labelAcao}</span>`;

        // Contexto como pills
        let ctxHtml = '';
        if (log.contexto && typeof log.contexto === 'object') {
            ctxHtml = '<div class="mt-1">' +
                Object.entries(log.contexto).map(([k,v]) =>
                    `<span class="ctx-pill"><strong>${_escHtml(k)}:</strong> ${_escHtml(v)}</span>`
                ).join('') + '</div>';
        }

        const avatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(log.nome_usuario)}&background=random&size=26`;

        return `<tr>
            <td style="white-space:nowrap;font-size:.78rem;color:#64748b">${log.data_fmt}</td>
            <td>${badgeMod}</td>
            <td>${badgeAc}</td>
            <td>
                <span class="fw-medium">${_escHtml(log.descricao)}</span>
                ${ctxHtml}
            </td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <img src="${avatar}" class="rounded-circle" width="26" height="26">
                    <span style="font-size:.82rem">${_escHtml(log.nome_usuario)}</span>
                </div>
            </td>
            <td style="font-size:.78rem;color:#94a3b8">${_escHtml(log.ip)}</td>
        </tr>`;
    }).join('');
}

function renderizarPaginacao(pagina, limite, total) {
    const totalPaginas = Math.ceil(total / limite);
    const infoPag = document.getElementById('infoPag');
    const btnsPag = document.getElementById('btnsPag');

    const inicio = (pagina - 1) * limite + 1;
    const fim    = Math.min(pagina * limite, total);
    infoPag.textContent = `Mostrando ${inicio}–${fim} de ${total.toLocaleString('pt-BR')}`;

    if (totalPaginas <= 1) { btnsPag.innerHTML = ''; return; }

    let html = `<button class="pag-btn" onclick="buscar(${pagina-1})" ${pagina===1?'disabled':''}>‹</button>`;

    // Mostrar no máximo 7 páginas ao redor da atual
    let start = Math.max(1, pagina - 3);
    let end   = Math.min(totalPaginas, pagina + 3);
    if (start > 1) html += `<button class="pag-btn" onclick="buscar(1)">1</button>${start>2?'<span class="text-muted px-1">…</span>':''}`;
    for (let i = start; i <= end; i++) {
        html += `<button class="pag-btn ${i===pagina?'ativo':''}" onclick="buscar(${i})">${i}</button>`;
    }
    if (end < totalPaginas) html += `${end<totalPaginas-1?'<span class="text-muted px-1">…</span>':''}<button class="pag-btn" onclick="buscar(${totalPaginas})">${totalPaginas}</button>`;
    html += `<button class="pag-btn" onclick="buscar(${pagina+1})" ${pagina>=totalPaginas?'disabled':''}>›</button>`;

    btnsPag.innerHTML = html;
}

function limparFiltros() {
    document.getElementById('fBusca').value      = '';
    document.getElementById('fModulo').value     = '';
    document.getElementById('fAcao').value       = '';
    document.getElementById('fDataInicio').value = '';
    document.getElementById('fDataFim').value    = '';
    buscar(1);
}

function exportarCSV() {
    const f = getFiltros();
    const params = new URLSearchParams({
        acao: 'exportar',
        busca: f.busca, modulo: f.modulo,
        data_inicio: f.data_inicio, data_fim: f.data_fim,
    });
    window.location.href = 'pages-adm/api-logs.php?' + params;
}
</script>
