<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Gestão de Documentos</h3>
        <p class="text-muted mb-0">Revise, aprove ou rejeite documentos enviados pelos projetos</p>
    </div>
    <button class="btn btn-primary" onclick="abrirModalNovoDocumento()">
        <i class="bi bi-cloud-upload me-2"></i>Novo Documento
    </button>
</div>

<!-- ESTATÍSTICAS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-files"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['total'] ?></h4><small class="text-muted">Total de Documentos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-check-circle"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['aprovados'] ?></h4><small class="text-muted">Aprovados</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-clock-history"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['pendentes'] ?></h4><small class="text-muted">Aguardando Revisão</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-x-circle"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['rejeitados'] ?></h4><small class="text-muted">Rejeitados</small></div>
        </div>
    </div>
</div>

<!-- FILTROS -->
<div class="content-card mb-4 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filtroBusca" class="form-control border-start-0" placeholder="Buscar por título ou projeto" oninput="filtrarDocumentos()">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select" id="filtroStatus" onchange="filtrarDocumentos()">
                <option value="">Status (Todos)</option>
                <option value="pendente">Pendente</option>
                <option value="ativo">Aprovado</option>
                <option value="inativo">Rejeitado</option>
            </select>
        </div>
        <div class="col-6 col-md-4">
            <select class="form-select" id="filtroTipo" onchange="filtrarDocumentos()">
                <option value="">Tipo (Todos)</option>
                <option value="relatorio">Relatório</option>
                <option value="artigo">Artigo</option>
                <option value="apresentacao">Apresentação</option>
                <option value="certificado">Certificado</option>
                <option value="outro">Outro</option>
            </select>
        </div>
    </div>
</div>

<!-- TABELA -->
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Lista de Documentos</h5>
        <div class="text-muted small" id="contadorDocs"><?= count($listaProducoes) ?> resultados</div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="tabelaDocs">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ID</th><th>TÍTULO</th><th>TIPO</th><th>PROJETO</th><th>DATA</th><th>STATUS</th><th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaProducoes)): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">Nenhum documento encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($listaProducoes as $doc): ?>
                    <tr data-status="<?= htmlspecialchars($doc['status']) ?>"
                        data-tipo="<?= htmlspecialchars(strtolower($doc['tipo'] ?? '')) ?>"
                        data-busca="<?= htmlspecialchars(strtolower($doc['titulo'].$doc['projeto_titulo'])) ?>">
                        <td class="fw-bold text-muted">#<?= $doc['id_producao'] ?></td>
                        <td>
                            <div class="fw-medium"><?= htmlspecialchars($doc['titulo']) ?></div>
                            <?php if ($doc['caminho']): ?>
                                <a href="<?= htmlspecialchars($doc['caminho']) ?>" target="_blank" class="text-muted small">
                                    <i class="bi bi-link-45deg"></i> Ver arquivo
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                $iconeTipo = match(strtolower($doc['tipo'] ?? '')) {
                                    'relatorio'     => 'bi-file-text text-primary',
                                    'artigo'        => 'bi-journal-text text-success',
                                    'apresentacao'  => 'bi-easel text-warning',
                                    'certificado'   => 'bi-award text-info',
                                    default         => 'bi-file-earmark text-secondary',
                                };
                            ?>
                            <span><i class="bi <?= $iconeTipo ?> me-1"></i><?= htmlspecialchars($doc['tipo'] ?? '—') ?></span>
                        </td>
                        <td><?= htmlspecialchars($doc['projeto_titulo']) ?></td>
                        <td><?= $doc['data_registro'] ? date('d/m/Y', strtotime($doc['data_registro'])) : '—' ?></td>
                        <td>
                            <?php match($doc['status']) {
                                'ativo'    => print('<span class="status-ativo">Aprovado</span>'),
                                'pendente' => print('<span class="badge bg-warning text-dark">Pendente</span>'),
                                default    => print('<span class="status-inativo">Rejeitado</span>'),
                            }; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($doc['status'] === 'pendente'): ?>
                                <button class="btn btn-sm btn-outline-success" title="Aprovar"
                                    onclick="alterarStatusDoc(<?= $doc['id_producao'] ?>, 'ativo')">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger ms-1" title="Rejeitar"
                                    onclick="alterarStatusDoc(<?= $doc['id_producao'] ?>, 'inativo')">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            <?php elseif ($doc['status'] === 'inativo'): ?>
                                <button class="btn btn-sm btn-outline-success" title="Aprovar"
                                    onclick="alterarStatusDoc(<?= $doc['id_producao'] ?>, 'ativo')">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            <?php elseif ($doc['status'] === 'ativo'): ?>
                                <button class="btn btn-sm btn-outline-warning" title="Pendenciar"
                                    onclick="alterarStatusDoc(<?= $doc['id_producao'] ?>, 'pendente')">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            <?php endif; ?>
                            <button class="btn btn-sm btn-outline-secondary ms-1" title="Excluir"
                                onclick="excluirDoc(<?= $doc['id_producao'] ?>)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL NOVO DOCUMENTO -->
<div class="modal fade" id="modalDocumento" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Novo Documento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-medium">Projeto <span class="text-danger">*</span></label>
                <select class="form-select" id="doc_id_projeto">
                    <option value="">Selecionar projeto</option>
                    <?php foreach ($listaProjetos as $proj): ?>
                        <option value="<?= $proj['id_projeto'] ?>"><?= htmlspecialchars($proj['titulo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-medium">Título <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="doc_titulo" placeholder="Nome do documento">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Tipo</label>
                <select class="form-select" id="doc_tipo">
                    <option value="relatorio">Relatório</option>
                    <option value="artigo">Artigo</option>
                    <option value="apresentacao">Apresentação</option>
                    <option value="certificado">Certificado</option>
                    <option value="outro">Outro</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Status Inicial</label>
                <select class="form-select" id="doc_status">
                    <option value="pendente">Pendente</option>
                    <option value="ativo">Aprovado</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-medium">URL / Caminho do Arquivo</label>
                <input type="text" class="form-control" id="doc_caminho" placeholder="https://... ou caminho relativo">
            </div>
        </div>
        <div id="doc_feedback" class="mt-3"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" onclick="salvarDocumento()"><i class="bi bi-save me-1"></i>Salvar</button>
      </div>
    </div>
  </div>
</div>

<script>
function abrirModalNovoDocumento() {
    ['doc_id_projeto','doc_titulo','doc_caminho'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('doc_tipo').value   = 'relatorio';
    document.getElementById('doc_status').value = 'pendente';
    document.getElementById('doc_feedback').innerHTML = '';
    new bootstrap.Modal(document.getElementById('modalDocumento')).show();
}

function salvarDocumento() {
    const body = new FormData();
    body.append('id_projeto', document.getElementById('doc_id_projeto').value);
    body.append('titulo',     document.getElementById('doc_titulo').value);
    body.append('tipo',       document.getElementById('doc_tipo').value);
    body.append('caminho',    document.getElementById('doc_caminho').value);
    body.append('status',     document.getElementById('doc_status').value);

    fetch('pages-adm/api-documentos.php?acao=criar', { method: 'POST', body })
        .then(r => r.json())
        .then(data => {
            const fb = document.getElementById('doc_feedback');
            fb.innerHTML = `<div class="alert alert-${data.sucesso ? 'success' : 'danger'}">${data.mensagem}</div>`;
            if (data.sucesso) setTimeout(() => { bootstrap.Modal.getInstance(document.getElementById('modalDocumento')).hide(); carregarPagina('documentos'); }, 1200);
        })
        .catch(() => { document.getElementById('doc_feedback').innerHTML = '<div class="alert alert-danger">Erro de comunicação.</div>'; });
}

function alterarStatusDoc(id, status) {
    const labels = { ativo: 'Aprovar', inativo: 'Rejeitar', pendente: 'Marcar como Pendente' };
    if (!confirm(`Confirma: ${labels[status] || status} este documento?`)) return;
    const body = new FormData();
    body.append('id_producao', id);
    body.append('status', status);
    fetch('pages-adm/api-documentos.php?acao=alterarStatus', { method: 'POST', body })
        .then(r => r.json())
        .then(data => { alert(data.mensagem); if (data.sucesso) carregarPagina('documentos'); })
        .catch(() => alert('Erro de comunicação.'));
}

function excluirDoc(id) {
    if (!confirm('Tem certeza que deseja excluir este documento? Esta ação é irreversível.')) return;
    const body = new FormData();
    body.append('id_producao', id);
    fetch('pages-adm/api-documentos.php?acao=excluir', { method: 'POST', body })
        .then(r => r.json())
        .then(data => { alert(data.mensagem); if (data.sucesso) carregarPagina('documentos'); })
        .catch(() => alert('Erro de comunicação.'));
}

function filtrarDocumentos() {
    const busca  = document.getElementById('filtroBusca').value.toLowerCase();
    const status = document.getElementById('filtroStatus').value;
    const tipo   = document.getElementById('filtroTipo').value.toLowerCase();
    const linhas = document.querySelectorAll('#tabelaDocs tbody tr[data-status]');
    let visiveis = 0;
    linhas.forEach(tr => {
        const ok = (!busca  || tr.dataset.busca.includes(busca))
                && (!status || tr.dataset.status === status)
                && (!tipo   || tr.dataset.tipo === tipo);
        tr.style.display = ok ? '' : 'none';
        if (ok) visiveis++;
    });
    document.getElementById('contadorDocs').textContent = visiveis + ' resultados';
}
</script>
