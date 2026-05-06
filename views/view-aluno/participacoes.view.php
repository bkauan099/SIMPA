<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Minhas Participações</h3>
        <p class="text-muted mb-0">Histórico completo de vínculos em projetos e eventos</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-diagram-3"></i></div>
            <div>
                <h4 class="mb-0 fw-bold"><?= $estatisticas['total'] ?></h4>
                <small class="text-muted">Total de Participações</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-heart"></i></div>
            <div>
                <h4 class="mb-0 fw-bold"><?= $estatisticas['ativos'] ?></h4>
                <small class="text-muted">Participações Ativas</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-check-circle"></i></div>
            <div>
                <h4 class="mb-0 fw-bold"><?= $estatisticas['concluidos'] ?></h4>
                <small class="text-muted">Concluídas</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-clock"></i></div>
            <div>
                <h4 class="mb-0 fw-bold"><?= $estatisticas['carga'] ?>h</h4>
                <small class="text-muted">Carga Horária Total</small>
            </div>
        </div>
    </div>
</div>

<div class="content-card">
    <h5 class="fw-bold mb-3">Histórico de Participações</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle w-100">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>PROJETO</th>
                    <th>TIPO</th>
                    <th>FUNÇÃO</th>
                    <th>CARGA</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($participacoes)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">Nenhuma participação encontrada.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($participacoes as $p): ?>
                        <tr>
                            <td class="fw-medium"><?= htmlspecialchars($p['projeto']) ?></td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?= htmlspecialchars($p['tipo'] ?? 'Sem tipo') ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($p['funcao']) ?></td>
                            <td><?= $p['carga_horaria'] ?>h</td>
                            <td>
                                <?php if ($p['status'] === 'ativo'): ?>
                                    <span class="status-ativo">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary text-white">Concluído</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
