<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Página Inicial</h3>
        <p class="text-muted mb-0">Visão geral do sistema e projetos ativos</p>
    </div>
    <button class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Novo Projeto</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #3b82f6;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#3b82f6;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#3b82f6;opacity:0.1;line-height:1;pointer-events:none;"><i class="bi bi-journal-text"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#3b82f6;color:#fff;opacity:0.85;margin-bottom:10px;"><i class="bi bi-journal-text"></i> Projetos Ativos</div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= $estatisticas['projetos_ativos'] ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #3b82f6;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#3b82f6;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#3b82f6;opacity:0.1;line-height:1;pointer-events:none;"><i class="bi bi-people"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#3b82f6;color:#fff;opacity:0.85;margin-bottom:10px;"><i class="bi bi-people"></i> Usuários Cadastrados</div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= $estatisticas['total_usuarios'] ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #f97316;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#f97316;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#f97316;opacity:0.1;line-height:1;pointer-events:none;"><i class="bi bi-hourglass-split"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#f97316;color:#fff;opacity:0.85;margin-bottom:10px;"><i class="bi bi-hourglass-split"></i> Pendências</div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= $estatisticas['pendencias'] ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #f97316;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#f97316;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#f97316;opacity:0.1;line-height:1;pointer-events:none;"><i class="bi bi-bell-fill"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#f97316;color:#fff;opacity:0.85;margin-bottom:10px;"><i class="bi bi-bell-fill"></i> Notificações</div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= $estatisticas['notificacoes'] ?></div>
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