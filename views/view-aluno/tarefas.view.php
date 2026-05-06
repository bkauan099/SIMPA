<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Minhas Tarefas</h3>
        <p class="text-muted mb-0">Atividades e eventos em aberto</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-hourglass"></i></div>
            <div><h4 class="mb-0 fw-bold" id="statPendentes"><?= $estatisticas['pendentes'] ?></h4><small class="text-muted">Pendentes</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-x-circle"></i></div>
            <div><h4 class="mb-0 fw-bold" id="statNaoConcluidos"><?= $estatisticas['nao_concluidos'] ?></h4><small class="text-muted">Não Concluídos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-check2-circle"></i></div>
            <div><h4 class="mb-0 fw-bold" id="statConcluidos"><?= $estatisticas['concluidos'] ?></h4><small class="text-muted">Concluídos</small></div>
        </div>
    </div>
</div>

<div class="content-card mb-3 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filtroBusca" class="form-control border-start-0" placeholder="Buscar por título..." oninput="filtrarItens()">
            </div>
        </div>
        <div class="col-6 col-md-4">
            <select class="form-select" id="filtroStatus" onchange="filtrarItens()">
                <option value="">Status (Todos)</option>
                <option value="pendente">Pendente</option>
                <option value="nao_concluido">Não Concluído</option>
                <option value="concluido">Concluído</option>
            </select>
        </div>
        <div class="col-12 col-md-2 text-muted small text-center text-md-start" id="contadorItens">
            <?= count($itens) ?> resultado(s)
        </div>
    </div>
</div>

<div class="content-card">
    <h5 class="fw-bold mb-3">Lista de Atividades</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle w-100" id="tabelaTarefas">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>TÍTULO</th>
                    <th>PRAZO</th>
                    <th>HORA</th>
                    <th>STATUS</th>
                    <th class="text-center">AÇÃO</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($itens)): ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">Nenhuma atividade encontrada.</td></tr>
                <?php else: ?>
                    <?php foreach ($itens as $item):
                        $hoje = new DateTime();
                        $hoje->setTime(0,0,0);
                        $prazo = new DateTime($item['data']);

                        if ($item['concluido']) {
                            $statusKey   = 'concluido';
                            $statusLabel = 'Concluído';
                            $statusClass = 'bg-success text-white';
                        } elseif ($prazo < $hoje) {
                            $statusKey   = 'nao_concluido';
                            $statusLabel = 'Não Concluído';
                            $statusClass = 'bg-danger text-white';
                        } else {
                            $statusKey   = 'pendente';
                            $statusLabel = 'Pendente';
                            $statusClass = 'bg-warning text-dark';
                        }
                    ?>
                        <?php $descricao = $item['descricao'] ?? ''; ?>
                        <tr data-tipo="<?= htmlspecialchars($item['tipo']) ?>"
                            data-status="<?= $statusKey ?>"
                            data-busca="<?= htmlspecialchars(strtolower($item['titulo'] . ' ' . $descricao)) ?>"
                            data-id="<?= htmlspecialchars($item['id']) ?>"
                            data-data="<?= htmlspecialchars($item['data']) ?>">
                            <td class="fw-medium"><?= htmlspecialchars($item['titulo']) ?></td>
                            <td><?= date('d/m/Y', strtotime($item['data'])) ?></td>
                            <td><?= $item['hora'] ? substr($item['hora'], 0, 5) : '—' ?></td>
                            <td><span class="badge badge-status <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                            <td class="text-center">
                                <button class="btn btn-sm <?= $item['concluido'] ? 'btn-outline-warning' : 'btn-outline-success' ?>"
                                        onclick="toggleConcluido(this)"
                                        title="<?= $item['concluido'] ? 'Desfazer conclusão' : 'Marcar como concluído' ?>">
                                    <i class="bi <?= $item['concluido'] ? 'bi-arrow-counterclockwise' : 'bi-check-lg' ?>"></i>
                                </button>
                                <?php if ($descricao): ?>
                                <button class="btn btn-sm btn-outline-secondary ms-1 btn-expandir"
                                        onclick="toggleDescricao(this)"
                                        title="Ver descrição">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr class="tr-descricao" style="display:none">
                            <td colspan="5" class="px-3 py-2 bg-light">
                                <span class="text-muted small"><i class="bi bi-card-text me-1"></i><?= $descricao ? htmlspecialchars($descricao) : 'Sem descrição.' ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
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
        const hoje = new Date();
        hoje.setHours(0, 0, 0, 0);
        const partes = tr.dataset.data.split('-');
        const prazo = new Date(partes[0], partes[1] - 1, partes[2]);

        let statusKey, statusLabel, statusClass;
        if (concluido) {
            statusKey = 'concluido'; statusLabel = 'Concluído'; statusClass = 'bg-success text-white';
        } else if (prazo < hoje) {
            statusKey = 'nao_concluido'; statusLabel = 'Não Concluído'; statusClass = 'bg-danger text-white';
        } else {
            statusKey = 'pendente'; statusLabel = 'Pendente'; statusClass = 'bg-warning text-dark';
        }

        const statusAnterior = tr.dataset.status;
        tr.dataset.status = statusKey;

        const badge = tr.querySelector('.badge-status');
        badge.className = 'badge badge-status ' + statusClass;
        badge.textContent = statusLabel;

        const statIds = { pendente: 'statPendentes', nao_concluido: 'statNaoConcluidos', concluido: 'statConcluidos' };
        const elAnterior = document.getElementById(statIds[statusAnterior]);
        const elNovo     = document.getElementById(statIds[statusKey]);
        if (elAnterior) elAnterior.textContent = Math.max(0, parseInt(elAnterior.textContent) - 1);
        if (elNovo)     elNovo.textContent     = parseInt(elNovo.textContent) + 1;

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

function filtrarItens() {
    const busca  = document.getElementById('filtroBusca').value.toLowerCase();
    const status = document.getElementById('filtroStatus').value;
    const linhas = document.querySelectorAll('#tabelaTarefas tbody tr[data-tipo]');
    let visiveis = 0;
    linhas.forEach(tr => {
        const ok = (!busca  || tr.dataset.busca.includes(busca))
                && (!status || tr.dataset.status === status);
        tr.style.display = ok ? '' : 'none';
        const descRow = tr.nextElementSibling;
        if (descRow && descRow.classList.contains('tr-descricao')) {
            if (!ok) {
                descRow.style.display = 'none';
                tr.querySelector('.btn-expandir i').className = 'bi bi-three-dots-vertical';
            }
        }
        if (ok) visiveis++;
    });
    document.getElementById('contadorItens').textContent = visiveis + ' resultado(s)';
}
</script>
