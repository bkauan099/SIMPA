<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Gerenciar Projetos</h3>
        <p class="text-muted mb-0">Projetos em que você participa</p>
    </div>
    <button class="btn btn-primary" onclick="carregarPagina('seletivos')"><i class="bi bi-megaphone me-2"></i>Solicitar Participação em Projeto</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-folder-fill"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['ativos'] ?></h4><small class="text-muted">Projetos Ativos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-heart"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['total'] ?></h4><small class="text-muted">Projetos Participando</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-check2-all"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['concluidos'] ?></h4><small class="text-muted">Concluídos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-clock-history"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['carga'] ?>h</h4><small class="text-muted">Carga Total</small></div>
        </div>
    </div>
</div>

<div class="content-card">
    <h5 class="fw-bold mb-3">Meus Projetos</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle w-100">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>TÍTULO</th>
                    <th>TIPO</th>
                    <th>FUNÇÃO</th>
                    <th>ORIENTADOR</th>
                    <th>CARGA</th>
                    <th>STATUS</th>
                    <th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($projetos)): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">Você não está vinculado a nenhum projeto.</td></tr>
                <?php else: ?>
                    <?php foreach ($projetos as $p): ?>
                        <tr>
                            <td class="fw-medium"><?= htmlspecialchars($p['titulo']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($p['tipo'] ?? '—') ?></span></td>
                            <td><?= htmlspecialchars($p['funcao']) ?></td>
                            <td><?= $p['orientador'] ? htmlspecialchars($p['orientador']) : '—' ?></td>
                            <td><?= $p['carga_horaria'] ?>h</td>
                            <td>
                                <?php if ($p['status'] === 'ativo'): ?>
                                    <span class="status-ativo">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary text-white">Concluído</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" title="Ver detalhes"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-outline-secondary ms-1" title="Documentos"><i class="bi bi-file-earmark-text"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
