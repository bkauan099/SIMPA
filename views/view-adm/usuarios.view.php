<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Gestão de Usuários</h3>
        <p class="text-muted mb-0">Gerencie acessos, perfis e status dos usuários do sistema</p>
    </div>
    <button class="btn btn-primary"><i class="bi bi-person-plus me-2"></i>Novo Usuário</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-people"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['total'] ?></h4><small class="text-muted">Total de Usuários</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-person-check"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['ativos'] ?></h4><small class="text-muted">Ativos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-person-x"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['inativos'] ?></h4><small class="text-muted">Inativos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-person-badge"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= $estatisticas['admins'] ?></h4><small class="text-muted">Administradores</small></div>
        </div>
    </div>
</div>

<div class="content-card mb-4 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Buscar por nome, email ou ID">
            </div>
        </div>
        <div class="col-6 col-md-2">
            <button class="btn btn-primary w-100">Pesquisar</button>
        </div>
        <div class="col-6 col-md-2">
            <select class="form-select">
                <option value="">Status</option>
                <option>Ativos</option>
                <option>Inativos</option>
            </select>
        </div>
        <div class="col-12 col-md-3">
            <select class="form-select">
                <option value="">Perfil (Todos)</option>
                <option>Administrador</option>
                <option>Professor Orientador</option>
                <option>Aluno</option>
            </select>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="fw-bold mb-0">Lista de Usuários</h5>
        <div class="text-muted small"><?= count($listaUsuarios) ?> resultados</div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ID</th><th>NOME</th><th>EMAIL</th><th>PERFIL</th><th>STATUS</th><th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaUsuarios)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">Nenhum usuário encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($listaUsuarios as $user): ?>
                        <tr>
                            <td class="fw-bold text-muted">#<?= htmlspecialchars($user['id_usuario']) ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['nome']) ?>&background=random" class="rounded-circle" width="32">
                                    <span class="fw-medium"><?= htmlspecialchars($user['nome']) ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <?php 
                                    $perfil = strtolower($user['perfil']); // Deixa minúsculo para facilitar a comparação
                                    if (strpos($perfil, 'admin') !== false): 
                                ?>
                                    <span class="badge bg-secondary">Administrador</span>
                                <?php elseif (strpos($perfil, 'orientador') !== false || strpos($perfil, 'professor') !== false): ?>
                                    <span class="badge bg-info text-dark">Professor</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark border">Aluno</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['status'] === 'ativo'): ?>
                                    <span class="status-ativo">Ativo</span>
                                <?php else: ?>
                                    <span class="status-inativo">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-secondary ms-1" title="Configurações"><i class="bi bi-gear"></i></button>
                                
                                <?php if ($user['status'] === 'inativo'): ?>
                                    <button class="btn btn-sm btn-outline-success ms-1" title="Ativar"><i class="bi bi-person-check"></i></button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-danger ms-1" title="Desativar"><i class="bi bi-person-x"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>