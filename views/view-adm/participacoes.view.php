<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Participações em Projetos</h3>
        <p class="text-muted mb-0">Gerencie vínculos de usuários com projetos</p>
    </div>
    <button class="btn btn-primary" onclick="abrirModalNovaParticipacao()">
        <i class="bi bi-plus-circle me-2"></i>Vincular Usuário
    </button>
</div>

<!-- ESTATÍSTICAS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-diagram-3"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['total'] ?></h4><small class="text-muted">Total de Vínculos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-person-check"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['ativos'] ?></h4><small class="text-muted">Vínculos Ativos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-person-x"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['encerrados'] ?></h4><small class="text-muted">Encerrados</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-folder2-open"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['projetos_com_participantes'] ?></h4><small class="text-muted">Projetos com Membros</small></div>
        </div>
    </div>
</div>

<!-- ABAS: Todos / Por Projeto -->
<div class="content-card mb-3 p-0 overflow-hidden">
    <ul class="nav nav-tabs px-3 pt-2 border-0" id="abas-participacao">
        <li class="nav-item">
            <button class="nav-link active fw-medium" id="aba-todos-btn" onclick="mostrarAba('todos')">
                <i class="bi bi-list-ul me-1"></i>Todos os Vínculos
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-medium" id="aba-projeto-btn" onclick="mostrarAba('projeto')">
                <i class="bi bi-people me-1"></i>Ver por Projeto
            </button>
        </li>
    </ul>
</div>

<!-- ═══ ABA: TODOS OS VÍNCULOS ═══ -->
<div id="aba-todos">
    <div class="content-card mb-4 p-3">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="filtroBusca" class="form-control border-start-0" placeholder="Buscar por nome, projeto ou função" oninput="filtrarParticipacoes()">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select class="form-select" id="filtroStatus" onchange="filtrarParticipacoes()">
                    <option value="">Status (Todos)</option>
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>
            <div class="col-6 col-md-4">
                <select class="form-select" id="filtroPerfil" onchange="filtrarParticipacoes()">
                    <option value="">Perfil (Todos)</option>
                    <option value="admin">Administrador</option>
                    <option value="professor_orientador">Professor</option>
                    <option value="aluno">Aluno</option>
                </select>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Lista de Participações</h5>
            <span class="text-muted small" id="contadorParticipacoes"><?= count($listaParticipacoes) ?> resultados</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tabelaParticipacoes">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th>ID</th><th>USUÁRIO</th><th>PROJETO</th><th>FUNÇÃO</th><th>C.H.</th><th>ENTRADA</th><th>STATUS</th><th class="text-center">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaParticipacoes)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">Nenhuma participação encontrada.</td></tr>
                    <?php else: foreach ($listaParticipacoes as $p): ?>
                        <tr data-status="<?= htmlspecialchars($p['status']) ?>"
                            data-perfil="<?= htmlspecialchars(strtolower($p['usuario_perfil'] ?? '')) ?>"
                            data-busca="<?= htmlspecialchars(strtolower($p['usuario_nome'].$p['projeto_titulo'].$p['funcao'])) ?>">
                            <td class="fw-bold text-muted">#<?= $p['id_participacao'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($p['usuario_nome']) ?>&background=random&size=30" class="rounded-circle" width="30" height="30">
                                    <div>
                                        <div class="fw-medium small"><?= htmlspecialchars($p['usuario_nome']) ?></div>
                                        <div class="text-muted" style="font-size:.73rem"><?= htmlspecialchars($p['usuario_email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="fw-medium"><?= htmlspecialchars($p['projeto_titulo']) ?></span></td>
                            <td><?= htmlspecialchars($p['funcao']) ?></td>
                            <td><?= $p['carga_horaria'] ? $p['carga_horaria'].'h' : '—' ?></td>
                            <td><?= $p['data_entrada'] ? date('d/m/Y', strtotime($p['data_entrada'])) : '—' ?></td>
                            <td>
                                <?php if ($p['status'] === 'ativo'): ?>
                                    <span class="status-ativo">Ativo</span>
                                <?php else: ?>
                                    <span class="status-inativo">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" title="Editar"
                                    onclick="editarParticipacao(<?= $p['id_participacao'] ?>,<?= $p['id_projeto'] ?>,'<?= htmlspecialchars(addslashes($p['usuario_nome'])) ?>','<?= htmlspecialchars(addslashes($p['funcao'])) ?>','<?= $p['carga_horaria'] ?? '' ?>','<?= $p['data_entrada'] ?? '' ?>','<?= $p['data_saida'] ?? '' ?>','<?= $p['status'] ?>')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <?php if ($p['status'] === 'ativo'): ?>
                                    <button class="btn btn-sm btn-outline-danger ms-1" title="Encerrar"
                                        onclick="alterarStatusParticipacao(<?= $p['id_participacao'] ?>, 'inativo')">
                                        <i class="bi bi-person-x"></i>
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-success ms-1" title="Reativar"
                                        onclick="alterarStatusParticipacao(<?= $p['id_participacao'] ?>, 'ativo')">
                                        <i class="bi bi-person-check"></i>
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline-secondary ms-1" title="Excluir"
                                    onclick="excluirParticipacao(<?= $p['id_participacao'] ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ═══ ABA: VER POR PROJETO ═══ -->
<div id="aba-projeto" class="d-none">
    <div class="content-card mb-4 p-3">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-8">
                <label class="form-label fw-medium mb-1">Selecione um projeto para ver seus integrantes</label>
                <select class="form-select" id="seletor_projeto" onchange="carregarIntegrantes()">
                    <option value="">— Selecionar projeto —</option>
                    <?php foreach ($listaProjetos as $proj): ?>
                        <option value="<?= $proj['id_projeto'] ?>"><?= htmlspecialchars($proj['titulo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Card de resultado do projeto -->
    <div id="card_integrantes" class="content-card d-none">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0" id="titulo_projeto_selecionado"></h5>
                <small class="text-muted" id="contador_integrantes"></small>
            </div>
        </div>
        <div id="spinner_integrantes" class="text-center py-4 d-none">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tabela_integrantes">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th>USUÁRIO</th><th>E-MAIL</th><th>FUNÇÃO</th><th>C.H.</th><th>ENTRADA</th><th>SAÍDA</th><th>STATUS</th>
                    </tr>
                </thead>
                <tbody id="body_integrantes">
                    <tr><td colspan="7" class="text-center py-4 text-muted">Selecione um projeto acima.</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="sem_projeto" class="content-card text-center py-5">
        <i class="bi bi-people fs-1 text-muted"></i>
        <p class="text-muted mt-2">Selecione um projeto acima para visualizar seus integrantes</p>
    </div>
</div>

<!-- ═══ MODAL CRIAR / EDITAR PARTICIPAÇÃO ═══ -->
<div class="modal fade" id="modalParticipacao" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="modalPartTitulo">Nova Participação</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="part_id">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-medium">Usuário <span class="text-danger">*</span></label>
                <select class="form-select" id="part_id_usuario">
                    <option value="">Selecionar usuário</option>
                    <?php foreach ($listaUsuariosAtivos as $u): ?>
                        <option value="<?= $u['id_usuario'] ?>"><?= htmlspecialchars($u['nome']) ?> (<?= htmlspecialchars($u['perfil']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Projeto <span class="text-danger">*</span></label>
                <select class="form-select" id="part_id_projeto">
                    <option value="">Selecionar projeto</option>
                    <?php foreach ($listaProjetos as $proj): ?>
                        <option value="<?= $proj['id_projeto'] ?>"><?= htmlspecialchars($proj['titulo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Função <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="part_funcao" placeholder="Ex: Bolsista, Orientador, Voluntário">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Carga Horária (h/sem)</label>
                <input type="number" class="form-control" id="part_carga_horaria" min="1" max="40" placeholder="Ex: 20">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Data de Entrada <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="part_data_entrada">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Data de Saída</label>
                <input type="date" class="form-control" id="part_data_saida">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Status</label>
                <select class="form-select" id="part_status">
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>
        </div>
        <div id="part_feedback" class="mt-3"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" onclick="salvarParticipacao()"><i class="bi bi-save me-1"></i>Salvar</button>
      </div>
    </div>
  </div>
</div>

<script>
// ── Abas ────────────────────────────────────────────────────
function mostrarAba(qual) {
    document.getElementById('aba-todos').classList.toggle('d-none', qual !== 'todos');
    document.getElementById('aba-projeto').classList.toggle('d-none', qual !== 'projeto');
    document.getElementById('aba-todos-btn').classList.toggle('active', qual === 'todos');
    document.getElementById('aba-projeto-btn').classList.toggle('active', qual === 'projeto');
}

// ── Integrantes por Projeto ──────────────────────────────────
function carregarIntegrantes() {
    const idProjeto = document.getElementById('seletor_projeto').value;
    const card      = document.getElementById('card_integrantes');
    const semProj   = document.getElementById('sem_projeto');
    const spinner   = document.getElementById('spinner_integrantes');
    const tbody     = document.getElementById('body_integrantes');
    const tituloEl  = document.getElementById('titulo_projeto_selecionado');
    const contEl    = document.getElementById('contador_integrantes');

    if (!idProjeto) {
        card.classList.add('d-none');
        semProj.classList.remove('d-none');
        return;
    }

    const sel = document.getElementById('seletor_projeto');
    const nomeProjeto = sel.options[sel.selectedIndex].text;

    semProj.classList.add('d-none');
    card.classList.remove('d-none');
    spinner.classList.remove('d-none');
    tbody.innerHTML = '';
    tituloEl.textContent = nomeProjeto;

    fetch(`pages-adm/api-participacoes.php?acao=listarPorProjeto&id_projeto=${idProjeto}`, { cache: 'no-store' })
        .then(r => r.json())
        .then(data => {
            spinner.classList.add('d-none');
            if (!data.sucesso || !data.participantes.length) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">Nenhum integrante neste projeto.</td></tr>';
                contEl.textContent = '0 integrantes';
                return;
            }
            contEl.textContent = data.participantes.length + ' integrante(s)';
            tbody.innerHTML = data.participantes.map(p => {
                const perfilBadge = (() => {
                    const pf = (p.usuario_perfil || '').toLowerCase();
                    if (pf.includes('admin')) return '<span class="badge bg-secondary">Admin</span>';
                    if (pf.includes('professor') || pf.includes('orientador')) return '<span class="badge bg-info text-dark">Professor</span>';
                    return '<span class="badge bg-light text-dark border">Aluno</span>';
                })();
                const statusBadge = p.status === 'ativo'
                    ? '<span class="status-ativo">Ativo</span>'
                    : '<span class="status-inativo">Inativo</span>';
                const entrada = p.data_entrada ? new Date(p.data_entrada).toLocaleDateString('pt-BR') : '—';
                const saida   = p.data_saida   ? new Date(p.data_saida).toLocaleDateString('pt-BR')   : '—';
                const ch      = p.carga_horaria ? p.carga_horaria + 'h' : '—';
                const avatar  = `https://ui-avatars.com/api/?name=${encodeURIComponent(p.usuario_nome)}&background=random&size=32`;
                return `<tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="${avatar}" class="rounded-circle" width="32" height="32">
                            <div>
                                <div class="fw-medium small">${p.usuario_nome}</div>
                                <div class="small text-muted">${perfilBadge}</div>
                            </div>
                        </div>
                    </td>
                    <td class="small">${p.usuario_email}</td>
                    <td class="fw-medium">${p.funcao}</td>
                    <td>${ch}</td>
                    <td>${entrada}</td>
                    <td>${saida}</td>
                    <td>${statusBadge}</td>
                </tr>`;
            }).join('');
        })
        .catch(err => {
            spinner.classList.add('d-none');
            tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-3">Erro ao carregar: ${err.message}</td></tr>`;
        });
}

// ── Modal Criar / Editar ────────────────────────────────────
function abrirModalNovaParticipacao() {
    document.getElementById('modalPartTitulo').textContent = 'Nova Participação';
    ['part_id','part_id_usuario','part_id_projeto','part_funcao','part_carga_horaria','part_data_entrada','part_data_saida'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('part_status').value = 'ativo';
    document.getElementById('part_feedback').innerHTML = '';
    new bootstrap.Modal(document.getElementById('modalParticipacao')).show();
}
function editarParticipacao(id, idProjeto, nomeUsuario, funcao, cargaH, dataEntrada, dataSaida, status) {
    document.getElementById('modalPartTitulo').textContent = 'Editar Participação';
    document.getElementById('part_id').value           = id;
    document.getElementById('part_id_projeto').value   = idProjeto;
    document.getElementById('part_funcao').value        = funcao;
    document.getElementById('part_carga_horaria').value = cargaH;
    document.getElementById('part_data_entrada').value  = dataEntrada;
    document.getElementById('part_data_saida').value    = dataSaida;
    document.getElementById('part_status').value        = status;
    document.getElementById('part_feedback').innerHTML  = `<div class="alert alert-info py-1 small">Editando participação de <strong>${nomeUsuario}</strong>.</div>`;
    new bootstrap.Modal(document.getElementById('modalParticipacao')).show();
}
function salvarParticipacao() {
    const id     = document.getElementById('part_id').value;
    const action = id ? 'pages-adm/api-participacoes.php?acao=atualizar' : 'pages-adm/api-participacoes.php?acao=criar';
    const body   = new FormData();
    if (id) body.append('id_participacao', id);
    body.append('id_projeto',    document.getElementById('part_id_projeto').value);
    body.append('id_usuario',    document.getElementById('part_id_usuario').value);
    body.append('funcao',        document.getElementById('part_funcao').value);
    body.append('carga_horaria', document.getElementById('part_carga_horaria').value);
    body.append('data_entrada',  document.getElementById('part_data_entrada').value);
    body.append('data_saida',    document.getElementById('part_data_saida').value);
    body.append('status',        document.getElementById('part_status').value);
    fetch(action, { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            const fb = document.getElementById('part_feedback');
            fb.innerHTML = `<div class="alert alert-${data.sucesso ? 'success' : 'danger'}">${data.mensagem}</div>`;
            if (data.sucesso) setTimeout(() => { bootstrap.Modal.getInstance(document.getElementById('modalParticipacao')).hide(); carregarPagina('participacoes'); }, 1200);
        })
        .catch(() => { document.getElementById('part_feedback').innerHTML = '<div class="alert alert-danger">Erro de comunicação.</div>'; });
}
function alterarStatusParticipacao(id, status) {
    if (!confirm(`Confirma alteração do status para "${status}"?`)) return;
    const body = new FormData();
    body.append('id_participacao', id); body.append('status', status);
    fetch('pages-adm/api-participacoes.php?acao=alterarStatus', { method: 'POST', body })
        .then(r => r.json())
        .then(data => { alert(data.mensagem); if (data.sucesso) carregarPagina('participacoes'); })
        .catch(() => alert('Erro de comunicação.'));
}
function excluirParticipacao(id) {
    if (!confirm('Tem certeza que deseja excluir este vínculo?')) return;
    const body = new FormData();
    body.append('id_participacao', id);
    fetch('pages-adm/api-participacoes.php?acao=excluir', { method: 'POST', body })
        .then(r => r.json())
        .then(data => { alert(data.mensagem); if (data.sucesso) carregarPagina('participacoes'); })
        .catch(() => alert('Erro de comunicação.'));
}

// ── Filtro tabela ─────────────────────────────────────────────
function filtrarParticipacoes() {
    const busca  = document.getElementById('filtroBusca').value.toLowerCase();
    const status = document.getElementById('filtroStatus').value;
    const perfil = document.getElementById('filtroPerfil').value.toLowerCase();
    const linhas = document.querySelectorAll('#tabelaParticipacoes tbody tr[data-status]');
    let visiveis = 0;
    linhas.forEach(tr => {
        const ok = (!busca  || tr.dataset.busca.includes(busca))
                && (!status || tr.dataset.status === status)
                && (!perfil || tr.dataset.perfil.includes(perfil));
        tr.style.display = ok ? '' : 'none';
        if (ok) visiveis++;
    });
    document.getElementById('contadorParticipacoes').textContent = visiveis + ' resultados';
}
</script>
