<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Página Inicial</h3>
        <p class="text-muted mb-0">Visão geral do sistema e projetos ativos</p>
    </div>
    <button class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Novo Projeto</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-journal-text"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['projetos_ativos'] ?></h4><small class="text-muted">Projetos Ativos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-people"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['total_usuarios'] ?></h4><small class="text-muted">Usuários Cadastrados</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-hourglass-split"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['pendencias'] ?></h4><small class="text-muted">Pendências</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-bell-fill"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['notificacoes'] ?></h4><small class="text-muted">Notificações</small></div>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="fw-bold mb-0">Projetos Ativos</h5>
        <div class="text-muted small"><?= count($projetosAtivos) ?> resultados</div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ID</th><th>TÍTULO</th><th>ORIENTADOR</th><th>PARTICIPANTES</th><th>STATUS</th><th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($projetosAtivos)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">Nenhum projeto ativo no momento.</td></tr>
                <?php else: ?>
                    <?php foreach ($projetosAtivos as $projeto): ?>
                        <tr>
                            <td class="fw-bold text-muted">#<?= $projeto['id_projeto'] ?></td>
                            <td class="fw-medium"><?= htmlspecialchars($projeto['titulo']) ?></td>
                            <td><?= $projeto['orientador'] ? htmlspecialchars($projeto['orientador']) : '—' ?></td>
                            <td><?= $projeto['total_participantes'] ?></td>
                            <td><span class="status-ativo">Ativo</span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-outline-secondary ms-1"><i class="bi bi-pencil"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>