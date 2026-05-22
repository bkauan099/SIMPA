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
                                <button class="btn btn-sm btn-outline-secondary ms-1"
                                        onclick="abrirDetalheTarefa(this.closest('tr'))"
                                        title="Ver detalhes">
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

