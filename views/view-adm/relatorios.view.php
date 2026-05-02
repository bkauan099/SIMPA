<?php if (!empty($erros)): ?>
<div class="alert alert-warning alert-dismissible fade show mb-3">
    <strong><i class="bi bi-exclamation-triangle me-1"></i>Atenção:</strong> Alguns dados não puderam ser carregados.
    <ul class="mb-0 mt-1 small"><?php foreach($erros as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Relatórios do Sistema</h3>
        <p class="text-muted mb-0">Visão consolidada de projetos, usuários, produções e acessos</p>
    </div>
    <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
        <i class="bi bi-printer me-1"></i>Imprimir
    </button>
</div>

<!-- ═══ RESUMO GERAL ═══ -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-folder-fill"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $resumo['total_projetos'] ?></h4><small class="text-muted">Projetos Totais</small></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-people"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $resumo['total_usuarios'] ?></h4><small class="text-muted">Usuários Cadastrados</small></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-diagram-3"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $resumo['total_participacoes'] ?></h4><small class="text-muted">Participações</small></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-files"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $resumo['total_producoes'] ?></h4><small class="text-muted">Produções Cadastradas</small></div>
        </div>
    </div>
</div>

<!-- ═══ LINHA 1: Projetos por Status | Projetos por Tipo ═══ -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="content-card h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2 text-primary"></i>Projetos por Status</h5>
            <?php if (!empty($projetosPorStatus)): ?>
                <canvas id="graficoProjStatus" height="180"></canvas>
                <div class="mt-3">
                    <?php foreach ($projetosPorStatus as $item):
                        $cor = match($item['status']) {
                            'ativo'    => 'bg-success',
                            'pendente' => 'bg-warning',
                            'concluido'=> 'bg-info',
                            default    => 'bg-secondary',
                        };
                    ?>
                    <div class="d-flex justify-content-between align-items-center mb-1 small">
                        <span><span class="badge <?= $cor ?> me-1">&nbsp;</span><?= ucfirst(htmlspecialchars($item['status'])) ?></span>
                        <strong><?= $item['total'] ?></strong>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted text-center py-4">Sem dados disponíveis.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="content-card h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-bar-chart me-2 text-success"></i>Projetos por Tipo</h5>
            <?php if (!empty($projetosPorTipo)): ?>
                <canvas id="graficoProjTipo" height="180"></canvas>
                <div class="mt-3">
                    <?php foreach ($projetosPorTipo as $item): ?>
                    <div class="d-flex justify-content-between align-items-center mb-1 small">
                        <span><?= htmlspecialchars($item['tipo']) ?></span>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress" style="width:80px;height:6px;">
                                <?php $pct = $resumo['total_projetos'] > 0 ? round($item['total'] / $resumo['total_projetos'] * 100) : 0; ?>
                                <div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div>
                            </div>
                            <strong><?= $item['total'] ?></strong>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted text-center py-4">Sem dados disponíveis.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ═══ LINHA 2: Projetos por Mês | Acessos por Mês ═══ -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="content-card h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-calendar3 me-2 text-warning"></i>Novos Projetos por Mês</h5>
            <?php if (!empty($projetosPorMes)): ?>
                <canvas id="graficoProjMes" height="180"></canvas>
            <?php else: ?>
                <p class="text-muted text-center py-4">Sem dados nos últimos 12 meses.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="content-card h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-activity me-2 text-danger"></i>Acessos por Mês</h5>
            <?php if (!empty($acessosPorMes)): ?>
                <canvas id="graficoAcessosMes" height="180"></canvas>
            <?php else: ?>
                <p class="text-muted text-center py-4">Sem dados nos últimos 6 meses.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ═══ LINHA 3: Usuários por Perfil | Produções por Tipo ═══ -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="content-card h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-person-badge me-2 text-info"></i>Usuários por Perfil</h5>
            <?php if (!empty($usuariosPorPerfil)): ?>
                <canvas id="graficoUsuPerfil" height="160"></canvas>
                <div class="mt-3">
                    <?php foreach ($usuariosPorPerfil as $item):
                        $pct = $resumo['total_usuarios'] > 0 ? round($item['total'] / $resumo['total_usuarios'] * 100) : 0;
                        $label = match(strtolower($item['perfil'])) {
                            'admin'                => 'Administrador',
                            'professor_orientador' => 'Professor',
                            'aluno'                => 'Aluno',
                            default                => ucfirst($item['perfil']),
                        };
                    ?>
                    <div class="d-flex justify-content-between align-items-center mb-2 small">
                        <span><?= $label ?></span>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress" style="width:80px;height:6px;">
                                <div class="progress-bar bg-info" style="width:<?= $pct ?>%"></div>
                            </div>
                            <strong><?= $item['total'] ?> <span class="text-muted">(<?= $pct ?>%)</span></strong>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted text-center py-4">Sem dados disponíveis.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="content-card h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-file-earmark-bar-graph me-2 text-secondary"></i>Produções por Tipo</h5>
            <?php if (!empty($producoesPorTipo)): ?>
                <canvas id="graficoProducoes" height="160"></canvas>
                <div class="mt-3">
                    <?php foreach ($producoesPorTipo as $item):
                        $pct = $resumo['total_producoes'] > 0 ? round($item['total'] / $resumo['total_producoes'] * 100) : 0;
                    ?>
                    <div class="d-flex justify-content-between align-items-center mb-1 small">
                        <span><?= ucfirst(htmlspecialchars($item['tipo'] ?? 'Outro')) ?></span>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress" style="width:80px;height:6px;">
                                <div class="progress-bar bg-secondary" style="width:<?= $pct ?>%"></div>
                            </div>
                            <strong><?= $item['total'] ?></strong>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted text-center py-4">Sem produções cadastradas.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ═══ TABELA: TOP PROJETOS POR PARTICIPANTES ═══ -->
<div class="content-card mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-trophy me-2 text-warning"></i>Top 10 Projetos com Mais Participantes</h5>
    <?php if (!empty($topProjetos)): ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="text-muted small"><th>#</th><th>TÍTULO DO PROJETO</th><th>STATUS</th><th>PARTICIPANTES</th></tr>
            </thead>
            <tbody>
                <?php foreach ($topProjetos as $i => $proj):
                    $badgeSt = match($proj['status']) {
                        'ativo'    => 'status-ativo',
                        'concluido'=> 'badge bg-info text-dark',
                        'pendente' => 'badge bg-warning text-dark',
                        default    => 'status-inativo',
                    };
                    $labelSt = match($proj['status']) {
                        'ativo'    => 'Ativo',
                        'concluido'=> 'Concluído',
                        'pendente' => 'Pendente',
                        default    => 'Inativo',
                    };
                ?>
                <tr>
                    <td class="fw-bold text-muted"><?= $i + 1 ?></td>
                    <td class="fw-medium"><?= htmlspecialchars($proj['titulo']) ?></td>
                    <td><span class="<?= $badgeSt ?>"><?= $labelSt ?></span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:8px;max-width:120px;">
                                <?php $maxPart = max(array_column($topProjetos, 'total_participantes')) ?: 1; ?>
                                <div class="progress-bar bg-primary" style="width:<?= round($proj['total_participantes'] / $maxPart * 100) ?>%"></div>
                            </div>
                            <strong><?= $proj['total_participantes'] ?></strong>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <p class="text-muted text-center py-3">Sem dados disponíveis.</p>
    <?php endif; ?>
</div>

<!-- ═══ TABELA: PARTICIPAÇÕES POR FUNÇÃO ═══ -->
<div class="content-card">
    <h5 class="fw-bold mb-3"><i class="bi bi-diagram-3 me-2 text-primary"></i>Participações por Função</h5>
    <?php if (!empty($participacoesPorFuncao)): ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="text-muted small"><th>FUNÇÃO</th><th>TOTAL</th><th>PROPORÇÃO</th></tr>
            </thead>
            <tbody>
                <?php foreach ($participacoesPorFuncao as $item):
                    $pct = $resumo['total_participacoes'] > 0 ? round($item['total'] / $resumo['total_participacoes'] * 100) : 0;
                ?>
                <tr>
                    <td class="fw-medium"><?= htmlspecialchars($item['funcao']) ?></td>
                    <td><strong><?= $item['total'] ?></strong></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:8px;max-width:160px;">
                                <div class="progress-bar bg-primary" style="width:<?= $pct ?>%"></div>
                            </div>
                            <span class="text-muted small"><?= $pct ?>%</span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <p class="text-muted text-center py-3">Sem participações cadastradas.</p>
    <?php endif; ?>
</div>

<!-- ═══ SCRIPTS DOS GRÁFICOS ═══ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const CORES = ['#3b82f6','#22c55e','#f97316','#a855f7','#ec4899','#14b8a6','#eab308','#ef4444','#64748b','#0ea5e9'];

function criarGrafico(id, tipo, labels, datasets, opts) {
    const el = document.getElementById(id);
    if (!el) return;
    new Chart(el.getContext('2d'), {
        type: tipo,
        data: { labels, datasets },
        options: Object.assign({ responsive: true, plugins: { legend: { position: 'bottom' } } }, opts || {})
    });
}

<?php if (!empty($projetosPorStatus)): ?>
criarGrafico('graficoProjStatus', 'doughnut',
    <?= json_encode(array_map(fn($r) => ucfirst($r['status']), $projetosPorStatus)) ?>,
    [{ data: <?= json_encode(array_map(fn($r) => (int)$r['total'], $projetosPorStatus)) ?>,
       backgroundColor: CORES, borderWidth: 2 }]
);
<?php endif; ?>

<?php if (!empty($projetosPorTipo)): ?>
criarGrafico('graficoProjTipo', 'bar',
    <?= json_encode(array_map(fn($r) => $r['tipo'], $projetosPorTipo)) ?>,
    [{ label: 'Projetos', data: <?= json_encode(array_map(fn($r) => (int)$r['total'], $projetosPorTipo)) ?>,
       backgroundColor: CORES, borderRadius: 4 }],
    { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
);
<?php endif; ?>

<?php if (!empty($usuariosPorPerfil)): ?>
criarGrafico('graficoUsuPerfil', 'doughnut',
    <?= json_encode(array_map(fn($r) => match(strtolower($r['perfil'])) {
        'admin' => 'Administrador',
        'professor_orientador' => 'Professor',
        'aluno' => 'Aluno',
        default => ucfirst($r['perfil'])
    }, $usuariosPorPerfil)) ?>,
    [{ data: <?= json_encode(array_map(fn($r) => (int)$r['total'], $usuariosPorPerfil)) ?>,
       backgroundColor: ['#6366f1','#22c55e','#f97316'], borderWidth: 2 }]
);
<?php endif; ?>

<?php if (!empty($producoesPorTipo)): ?>
criarGrafico('graficoProducoes', 'bar',
    <?= json_encode(array_map(fn($r) => ucfirst($r['tipo'] ?? 'Outro'), $producoesPorTipo)) ?>,
    [{ label: 'Produções', data: <?= json_encode(array_map(fn($r) => (int)$r['total'], $producoesPorTipo)) ?>,
       backgroundColor: '#64748b', borderRadius: 4 }],
    { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
);
<?php endif; ?>

<?php if (!empty($projetosPorMes)): ?>
criarGrafico('graficoProjMes', 'line',
    <?= json_encode(array_map(fn($r) => $r['mes'], $projetosPorMes)) ?>,
    [{ label: 'Projetos iniciados', data: <?= json_encode(array_map(fn($r) => (int)$r['total'], $projetosPorMes)) ?>,
       borderColor: '#f97316', backgroundColor: 'rgba(249,115,22,0.1)', fill: true, tension: 0.4, pointRadius: 4 }],
    { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
);
<?php endif; ?>

<?php if (!empty($acessosPorMes)): ?>
criarGrafico('graficoAcessosMes', 'line',
    <?= json_encode(array_map(fn($r) => $r['mes'], $acessosPorMes)) ?>,
    [
        { label: 'Sucesso', data: <?= json_encode(array_map(fn($r) => (int)$r['sucesso'], $acessosPorMes)) ?>,
          borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.1)', fill: true, tension: 0.4, pointRadius: 4 },
        { label: 'Falha',   data: <?= json_encode(array_map(fn($r) => (int)$r['falha'], $acessosPorMes)) ?>,
          borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.1)', fill: true, tension: 0.4, pointRadius: 4 }
    ],
    { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
);
<?php endif; ?>
</script>
