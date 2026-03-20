<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Meus Alunos</h3>
        <p class="text-muted mb-0">Alunos participantes dos seus projetos</p>
    </div>
    <button class="btn btn-primary"><i class="bi bi-person-plus me-2"></i>Adicionar Aluno</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-people"></i></div>
            <div><h4 class="mb-0 fw-bold">12</h4><small class="text-muted">Total de Alunos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-person-check"></i></div>
            <div><h4 class="mb-0 fw-bold">10</h4><small class="text-muted">Ativos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-clock-history"></i></div>
            <div><h4 class="mb-0 fw-bold">785h</h4><small class="text-muted">Carga Total</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-award"></i></div>
            <div><h4 class="mb-0 fw-bold">48</h4><small class="text-muted">Certificados Emitidos</small></div>
        </div>
    </div>
</div>

<div class="content-card mb-3 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Buscar aluno por nome ou email...">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select">
                <option>Projeto (Todos)</option>
                <option>SIMPA UEMA</option>
                <option>Projeto Social</option>
                <option>Inovação Tec.</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select class="form-select">
                <option>Status</option>
                <option>Ativo</option>
                <option>Inativo</option>
            </select>
        </div>
        <div class="col-12 col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="fw-bold mb-0">Lista de Alunos</h5>
        <div class="text-muted small">1-5 de 12
            <i class="bi bi-chevron-left ms-2" style="cursor:pointer"></i>
            <i class="bi bi-chevron-right ms-1" style="cursor:pointer"></i>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ALUNO</th><th>EMAIL</th><th>PROJETO</th><th>TIPO</th><th>CARGA</th><th>STATUS</th><th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Joao&background=e0f2fe&color=0369a1" class="rounded-circle" width="32">
                            <span class="fw-medium">João</span>
                        </div>
                    </td>
                    <td>joao@aluno.uema.br</td>
                    <td>Projeto Social Comunitário</td>
                    <td><span class="badge bg-light text-dark border">Projeto Especial</span></td>
                    <td>85h</td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Ver progresso"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Ver documentos"><i class="bi bi-file-earmark-text"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Augusto+Nicacio&background=d1fae5&color=065f46" class="rounded-circle" width="32">
                            <span class="fw-medium">Augusto Nicácio</span>
                        </div>
                    </td>
                    <td>augusto@uema.br</td>
                    <td>Equipe SIMPA UEMA</td>
                    <td><span class="badge bg-light text-dark border">Projeto Especial</span></td>
                    <td>120h</td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Ver progresso"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Ver documentos"><i class="bi bi-file-earmark-text"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Aian&background=ede9fe&color=6d28d9" class="rounded-circle" width="32">
                            <span class="fw-medium">Aian</span>
                        </div>
                    </td>
                    <td>aian@uema.br</td>
                    <td>Inovação Tecnológica Ambiental</td>
                    <td><span class="badge bg-light text-dark border">Ligas Acadêmicas</span></td>
                    <td>80h</td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Ver progresso"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Ver documentos"><i class="bi bi-file-earmark-text"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Bruno+Kauan&background=fce7f3&color=be185d" class="rounded-circle" width="32">
                            <span class="fw-medium">Bruno Kauan</span>
                        </div>
                    </td>
                    <td>bruno@uema.br</td>
                    <td>Equipe SIMPA UEMA</td>
                    <td><span class="badge bg-light text-dark border">Projeto Especial</span></td>
                    <td>60h</td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Ver progresso"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Ver documentos"><i class="bi bi-file-earmark-text"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Jose+Kauan&background=fef9c3&color=854d0e" class="rounded-circle" width="32">
                            <span class="fw-medium">Jose Kauan</span>
                        </div>
                    </td>
                    <td>jose.kauan@uema.br</td>
                    <td>Atlética Predadores</td>
                    <td><span class="badge bg-light text-dark border">Atlética</span></td>
                    <td>40h</td>
                    <td><span class="status-inativo">Inativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Ver progresso"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Ver documentos"><i class="bi bi-file-earmark-text"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
