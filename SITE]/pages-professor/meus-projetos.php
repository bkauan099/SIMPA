<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Meus Projetos</h3>
        <p class="text-muted mb-0">Projetos que você coordena ou orienta</p>
    </div>
    <button class="btn btn-primary"><i class="bi bi-folder-plus me-2"></i>Novo Projeto</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-folder-fill"></i></div>
            <div><h4 class="mb-0 fw-bold">3</h4><small class="text-muted">Ativos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-hourglass-split"></i></div>
            <div><h4 class="mb-0 fw-bold">1</h4><small class="text-muted">Aguard. Aprovação</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-people"></i></div>
            <div><h4 class="mb-0 fw-bold">12</h4><small class="text-muted">Alunos no Total</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-check2-all"></i></div>
            <div><h4 class="mb-0 fw-bold">2</h4><small class="text-muted">Concluídos</small></div>
        </div>
    </div>
</div>

<div class="content-card mb-3 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Buscar projeto...">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select">
                <option>Tipo (Todos)</option>
                <option>Projeto Especial</option>
                <option>Ligas Acadêmicas</option>
                <option>Empresa Jr</option>
                <option>Atlética</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select">
                <option>Status (Todos)</option>
                <option>Ativo</option>
                <option>Concluído</option>
            </select>
        </div>
    </div>
</div>

<div class="content-card">
    <h5 class="fw-bold mb-3">Lista de Projetos</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ID</th><th>TÍTULO</th><th>TIPO</th><th>ALUNOS</th><th>CARGA MÉDIA</th><th>STATUS</th><th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-bold text-muted">#200</td>
                    <td class="fw-medium">Equipe SIMPA UEMA</td>
                    <td><span class="badge bg-light text-dark border">Projeto Especial</span></td>
                    <td>5</td><td>95h</td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Ver alunos"><i class="bi bi-people"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-info ms-1" title="Documentos"><i class="bi bi-file-earmark-text"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">#201</td>
                    <td class="fw-medium">Projeto Social Comunitário</td>
                    <td><span class="badge bg-light text-dark border">Projeto Especial</span></td>
                    <td>4</td><td>80h</td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Ver alunos"><i class="bi bi-people"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-info ms-1" title="Documentos"><i class="bi bi-file-earmark-text"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">#205</td>
                    <td class="fw-medium">Inovação Tecnológica Ambiental</td>
                    <td><span class="badge bg-light text-dark border">Ligas Acadêmicas</span></td>
                    <td>3</td><td>70h</td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Ver alunos"><i class="bi bi-people"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-info ms-1" title="Documentos"><i class="bi bi-file-earmark-text"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">#198</td>
                    <td class="fw-medium">Atlética Predadores</td>
                    <td><span class="badge bg-light text-dark border">Atlética</span></td>
                    <td>12</td><td>40h</td>
                    <td><span class="badge bg-secondary text-white">Concluído</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Ver alunos"><i class="bi bi-people"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-success ms-1" title="Reativar"><i class="bi bi-check-circle"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
