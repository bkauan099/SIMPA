<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Gestão de Projetos</h3>
        <p class="text-muted mb-0">Cadastre, organize e acompanhe os projetos da instituição</p>
    </div>
    <button class="btn btn-primary" onclick="abrirModalNovoProjeto()">
        <i class="bi bi-folder-plus me-2"></i>Novo Projeto
    </button>
</div>

<!-- CARDS DE ESTATÍSTICAS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-folder-fill"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['total'] ?></h4><small class="text-muted">Total de Projetos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-check2-circle"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['ativos'] ?></h4><small class="text-muted">Projetos Ativos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-hourglass-split"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['pendentes'] ?></h4><small class="text-muted">Pendentes</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-check2-all"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['concluidos'] ?></h4><small class="text-muted">Concluídos</small></div>
        </div>
    </div>
</div>

<!-- FILTROS -->
<div class="content-card mb-4 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filtroBusca" class="form-control border-start-0" placeholder="Buscar por título, área ou orientador" oninput="filtrarProjetos()">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select" id="filtroStatus" onchange="filtrarProjetos()">
                <option value="">Status (Todos)</option>
                <option value="ativo">Ativo</option>
                <option value="pendente">Pendente</option>
                <option value="concluido">Concluído</option>
                <option value="inativo">Inativo</option>
            </select>
        </div>
        <div class="col-6 col-md-4">
            <select class="form-select" id="filtroTipo" onchange="filtrarProjetos()">
                <option value="">Tipo (Todos)</option>
                <?php foreach ($tiposProjeto as $tipo): ?>
                    <option value="<?= htmlspecialchars($tipo['nome']) ?>"><?= htmlspecialchars($tipo['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<!-- TABELA -->
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Lista de Projetos</h5>
        <div class="text-muted small" id="contadorProjetos"><?= count($listaProjetos) ?> resultados</div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="tabelaProjetos">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ID</th><th>TÍTULO</th><th>TIPO</th><th>ÁREA</th><th>ORIENTADOR</th><th>PARTICIPANTES</th><th>STATUS</th><th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaProjetos)): ?>
                    <tr><td colspan="8" class="text-center py-4 text-muted">Nenhum projeto encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($listaProjetos as $projeto): ?>
                        <tr data-status="<?= htmlspecialchars($projeto['status']) ?>"
                            data-tipo="<?= htmlspecialchars($projeto['tipo_nome'] ?? '') ?>"
                            data-busca="<?= htmlspecialchars(strtolower($projeto['titulo'].$projeto['area'].($projeto['orientador']??''))) ?>">
                            <td class="fw-bold text-muted">#<?= $projeto['id_projeto'] ?></td>
                            <td class="fw-medium"><?= htmlspecialchars($projeto['titulo']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($projeto['tipo_nome'] ?? '—') ?></span></td>
                            <td><?= htmlspecialchars($projeto['area'] ?? '—') ?></td>
                            <td><?= $projeto['orientador'] ? htmlspecialchars($projeto['orientador']) : '<span class="text-muted">—</span>' ?></td>
                            <td><span class="badge bg-secondary"><?= $projeto['total_participantes'] ?></span></td>
                            <td>
                                <?php
                                    $s = $projeto['status'];
                                    $badge = match($s) {
                                        'ativo'    => 'status-ativo',
                                        'pendente' => 'badge bg-warning text-dark',
                                        'concluido'=> 'badge bg-success',
                                        default    => 'status-inativo',
                                    };
                                    $label = match($s) {
                                        'ativo'    => 'Ativo',
                                        'pendente' => 'Pendente',
                                        'concluido'=> 'Concluído',
                                        default    => 'Inativo',
                                    };
                                ?>
                                <span class="<?= $badge ?>"><?= $label ?></span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" title="Editar"
                                    onclick="editarProjeto(<?= $projeto['id_projeto'] ?>, '<?= htmlspecialchars(addslashes($projeto['titulo'])) ?>', '<?= htmlspecialchars(addslashes($projeto['area'] ?? '')) ?>', '<?= $projeto['id_tipo'] ?? '' ?>', '<?= htmlspecialchars(addslashes($projeto['descricao'] ?? '')) ?>', '<?= $projeto['data_inicio'] ?? '' ?>', '<?= $projeto['data_fim'] ?? '' ?>', '<?= $projeto['status'] ?>')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <?php if ($projeto['status'] === 'pendente'): ?>
                                    <button class="btn btn-sm btn-outline-success ms-1" title="Aprovar"
                                        onclick="alterarStatusProjeto(<?= $projeto['id_projeto'] ?>, 'ativo')">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                <?php elseif ($projeto['status'] === 'ativo'): ?>
                                    <button class="btn btn-sm btn-outline-warning ms-1" title="Concluir"
                                        onclick="alterarStatusProjeto(<?= $projeto['id_projeto'] ?>, 'concluido')">
                                        <i class="bi bi-check2-all"></i>
                                    </button>
                                <?php endif; ?>
                                <?php if ($projeto['status'] !== 'inativo'): ?>
                                    <button class="btn btn-sm btn-outline-danger ms-1" title="Desativar"
                                        onclick="alterarStatusProjeto(<?= $projeto['id_projeto'] ?>, 'inativo')">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL CRIAR/EDITAR PROJETO -->
<div class="modal fade" id="modalProjeto" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="modalProjetoTitulo">Novo Projeto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="proj_id">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-medium">Título <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="proj_titulo" placeholder="Título do projeto">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Tipo</label>
                <select class="form-select" id="proj_tipo">
                    <option value="">Selecionar tipo</option>
                    <?php foreach ($tiposProjeto as $tipo): ?>
                        <option value="<?= $tipo['id_tipo'] ?>"><?= htmlspecialchars($tipo['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Área</label>
                <input type="text" class="form-control" id="proj_area" placeholder="Ex: Ciências da Saúde">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Status</label>
                <select class="form-select" id="proj_status">
                    <option value="pendente">Pendente</option>
                    <option value="ativo">Ativo</option>
                    <option value="concluido">Concluído</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Data de Início <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="proj_data_inicio">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Data de Fim</label>
                <input type="date" class="form-control" id="proj_data_fim">
            </div>
            <div class="col-12">
                <label class="form-label fw-medium">Descrição</label>
                <textarea class="form-control" id="proj_descricao" rows="3" placeholder="Descreva o projeto..."></textarea>
            </div>
        </div>
        <div id="proj_feedback" class="mt-3"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" onclick="salvarProjeto()"><i class="bi bi-save me-1"></i>Salvar</button>
      </div>
    </div>
  </div>
</div>

<script>
function abrirModalNovoProjeto() {
    document.getElementById('modalProjetoTitulo').textContent = 'Novo Projeto';
    document.getElementById('proj_id').value = '';
    document.getElementById('proj_titulo').value = '';
    document.getElementById('proj_area').value = '';
    document.getElementById('proj_tipo').value = '';
    document.getElementById('proj_status').value = 'pendente';
    document.getElementById('proj_data_inicio').value = '';
    document.getElementById('proj_data_fim').value = '';
    document.getElementById('proj_descricao').value = '';
    document.getElementById('proj_feedback').innerHTML = '';
    new bootstrap.Modal(document.getElementById('modalProjeto')).show();
}

function editarProjeto(id, titulo, area, tipo, descricao, dataInicio, dataFim, status) {
    document.getElementById('modalProjetoTitulo').textContent = 'Editar Projeto';
    document.getElementById('proj_id').value = id;
    document.getElementById('proj_titulo').value = titulo;
    document.getElementById('proj_area').value = area;
    document.getElementById('proj_tipo').value = tipo;
    document.getElementById('proj_descricao').value = descricao;
    document.getElementById('proj_data_inicio').value = dataInicio;
    document.getElementById('proj_data_fim').value = dataFim;
    document.getElementById('proj_status').value = status;
    document.getElementById('proj_feedback').innerHTML = '';
    new bootstrap.Modal(document.getElementById('modalProjeto')).show();
}

function salvarProjeto() {
    const id = document.getElementById('proj_id').value;
    const action = id ? 'pages-adm/api-projetos.php?acao=atualizar' : 'pages-adm/api-projetos.php?acao=criar';
    const body = new FormData();
    if (id) body.append('id_projeto', id);
    body.append('titulo', document.getElementById('proj_titulo').value);
    body.append('area', document.getElementById('proj_area').value);
    body.append('id_tipo', document.getElementById('proj_tipo').value);
    body.append('descricao', document.getElementById('proj_descricao').value);
    body.append('data_inicio', document.getElementById('proj_data_inicio').value);
    body.append('data_fim', document.getElementById('proj_data_fim').value);
    body.append('status', document.getElementById('proj_status').value);

    fetch(action, { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            const fb = document.getElementById('proj_feedback');
            fb.innerHTML = `<div class="alert alert-${data.sucesso ? 'success' : 'danger'}">${data.mensagem}</div>`;
            if (data.sucesso) setTimeout(() => { bootstrap.Modal.getInstance(document.getElementById('modalProjeto')).hide(); carregarPagina('projetos'); }, 1200);
        })
        .catch(() => { document.getElementById('proj_feedback').innerHTML = '<div class="alert alert-danger">Erro de comunicação.</div>'; });
}

function alterarStatusProjeto(id, status) {
    if (!confirm(`Confirma a alteração do status para "${status}"?`)) return;
    const body = new FormData();
    body.append('id_projeto', id);
    body.append('status', status);
    fetch('pages-adm/api-projetos.php?acao=alterarStatus', { method: 'POST', body })
        .then(r => r.json())
        .then(data => { alert(data.mensagem); if (data.sucesso) carregarPagina('projetos'); })
        .catch(() => alert('Erro de comunicação.'));
}

function filtrarProjetos() {
    const busca  = document.getElementById('filtroBusca').value.toLowerCase();
    const status = document.getElementById('filtroStatus').value.toLowerCase();
    const tipo   = document.getElementById('filtroTipo').value.toLowerCase();
    const linhas = document.querySelectorAll('#tabelaProjetos tbody tr[data-status]');
    let visiveis = 0;
    linhas.forEach(tr => {
        const okBusca  = !busca  || tr.dataset.busca.includes(busca);
        const okStatus = !status || tr.dataset.status === status;
        const okTipo   = !tipo   || tr.dataset.tipo.toLowerCase() === tipo;
        tr.style.display = (okBusca && okStatus && okTipo) ? '' : 'none';
        if (okBusca && okStatus && okTipo) visiveis++;
    });
    document.getElementById('contadorProjetos').textContent = visiveis + ' resultados';
}
</script>
