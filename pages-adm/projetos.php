<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Gestão de Projetos</h3>
        <p class="text-muted mb-0">Cadastre, organize e acompanhe os projetos da instituição</p>
    </div>
    <button class="btn btn-primary" onclick="abrirModal()">
        <i class="bi bi-plus-circle me-2"></i>Novo Projeto
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-folder-fill"></i></div>
            <div>
                <h4 class="mb-0 fw-bold">12</h4><small class="text-muted">Projetos Ativos</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <h4 class="mb-0 fw-bold">3</h4><small class="text-muted">Aguardando Aprovação</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-check2-all"></i></div>
            <div>
                <h4 class="mb-0 fw-bold">8</h4><small class="text-muted">Concluídos</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-people"></i></div>
            <div>
                <h4 class="mb-0 fw-bold">48</h4><small class="text-muted">Participantes</small>
            </div>
        </div>
    </div>
</div>

<div class="content-card mb-4 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Buscar por título ou área...">
            </div>
        </div>
        <div class="col-6 col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
        <div class="col-6 col-md-2">
            <select class="form-select">
                <option>Tipo</option>
                <option>Projeto Especial</option>
                <option>Ligas Acadêmicas</option>
                <option>Empresa Jr</option>
                <option>Atlética</option>
            </select>
        </div>
        <div class="col-12 col-md-3">
            <select class="form-select">
                <option>Status (Todos)</option>
                <option>Ativo</option>
                <option>Concluído</option>
                <option>Cancelado</option>
            </select>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="fw-bold mb-0">Lista de Projetos</h5>
        <div class="text-muted small">1-4 de 12
            <i class="bi bi-chevron-left ms-2" style="cursor:pointer"></i>
            <i class="bi bi-chevron-right ms-1" style="cursor:pointer"></i>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ID</th>
                    <th>TÍTULO</th>
                    <th>TIPO</th>
                    <th>ORIENTADOR</th>
                    <th>PARTICIPANTES</th>
                    <th>STATUS</th>
                    <th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-bold text-muted">#200</td>
                    <td class="fw-medium">Equipe SIMPA UEMA</td>
                    <td><span class="badge bg-light text-dark border">Projeto Especial</span></td>
                    <td>Prof. André Nunes</td>
                    <td>5</td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Ver"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-warning ms-1" title="Arquivar"><i class="bi bi-archive"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">#201</td>
                    <td class="fw-medium">Projeto Social Comunitário</td>
                    <td><span class="badge bg-light text-dark border">Projeto Especial</span></td>
                    <td>Prof. João Varela</td>
                    <td>3</td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Ver"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-warning ms-1" title="Arquivar"><i class="bi bi-archive"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">#205</td>
                    <td class="fw-medium">Inovação Tecnológica Ambiental</td>
                    <td><span class="badge bg-light text-dark border">Ligas Acadêmicas</span></td>
                    <td>Profª Ana Bezerra</td>
                    <td>4</td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Ver"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-warning ms-1" title="Arquivar"><i class="bi bi-archive"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">#198</td>
                    <td class="fw-medium">Atlética Predadores</td>
                    <td><span class="badge bg-light text-dark border">Atlética</span></td>
                    <td>—</td>
                    <td>12</td>
                    <td><span class="badge bg-secondary text-white">Concluído</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Ver"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-success ms-1" title="Reativar"><i class="bi bi-check-circle"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>