<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Dashboard</h3>
        <p class="text-muted mb-0">Visão geral do sistema e projetos ativos</p>
    </div>
    <button class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Novo Projeto</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-journal-text"></i></div>
            <div><h4 class="mb-0 fw-bold">12</h4><small class="text-muted">Projetos Ativos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-people"></i></div>
            <div><h4 class="mb-0 fw-bold">48</h4><small class="text-muted">Usuários Cadastrados</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-hourglass-split"></i></div>
            <div><h4 class="mb-0 fw-bold">8</h4><small class="text-muted">Pendências</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-bell-fill"></i></div>
            <div><h4 class="mb-0 fw-bold">3</h4><small class="text-muted">Notificações</small></div>
        </div>
    </div>
</div>

<div class="content-card mb-4 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Buscar por título, orientador ou ID do projeto">
            </div>
        </div>
        <div class="col-6 col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
        <div class="col-6 col-md-2">
            <select class="form-select"><option>Status</option><option>Ativo</option><option>Concluído</option></select>
        </div>
        <div class="col-6 col-md-2">
            <select class="form-select"><option>Pendências</option><option>Com pendência</option><option>Sem pendência</option></select>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="fw-bold mb-0">Projetos Ativos</h5>
        <div class="text-muted small">1-4 de 4
            <i class="bi bi-chevron-left ms-2" style="cursor:pointer"></i>
            <i class="bi bi-chevron-right ms-1" style="cursor:pointer"></i>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ID</th><th>TÍTULO</th><th>ORIENTADOR</th><th>PARTICIPANTES</th><th>STATUS</th><th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-bold text-muted">#200</td>
                    <td class="fw-medium">Equipe SIMPA UEMA</td>
                    <td>Prof. André Nunes</td>
                    <td>5</td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Ver detalhes"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Editar"><i class="bi bi-pencil"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">#201</td>
                    <td class="fw-medium">Projeto Social Comunitário</td>
                    <td>Prof. João Varela</td>
                    <td>3</td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Ver detalhes"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Editar"><i class="bi bi-pencil"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">#205</td>
                    <td class="fw-medium">Inovação Tecnológica Ambiental</td>
                    <td>Profª Ana Bezerra</td>
                    <td>4</td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Ver detalhes"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Editar"><i class="bi bi-pencil"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">#198</td>
                    <td class="fw-medium">Atlética Predadores</td>
                    <td>—</td>
                    <td>12</td>
                    <td><span class="badge bg-secondary text-white">Concluído</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Ver detalhes"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Editar"><i class="bi bi-pencil"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
