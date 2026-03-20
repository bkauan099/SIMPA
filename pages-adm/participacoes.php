<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Participações em Projetos</h3>
        <p class="text-muted mb-0">Gerencie vínculos de usuários com projetos</p>
    </div>
    <button class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Vincular Usuário</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-diagram-3"></i></div>
            <div><h4 class="mb-0 fw-bold">24</h4><small class="text-muted">Total de Vínculos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-coin"></i></div>
            <div><h4 class="mb-0 fw-bold">8</h4><small class="text-muted">Com Bolsa Ativa</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-heart"></i></div>
            <div><h4 class="mb-0 fw-bold">16</h4><small class="text-muted">Participações Ativas</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-clock-history"></i></div>
            <div><h4 class="mb-0 fw-bold">1.240h</h4><small class="text-muted">Carga Total Registrada</small></div>
        </div>
    </div>
</div>

<div class="content-card mb-4 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Buscar por projeto ou usuário...">
            </div>
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
        <div class="col-6 col-md-2">
            <select class="form-select">
                <option>Função</option>
                <option>Coordenador</option>
                <option>Pesquisador</option>
                <option>Desenvolvedor</option>
            </select>
        </div>
        <div class="col-12 col-md-3">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="fw-bold mb-0">Lista de Participações</h5>
        <div class="text-muted small">1-5 de 24
            <i class="bi bi-chevron-left ms-2" style="cursor:pointer"></i>
            <i class="bi bi-chevron-right ms-1" style="cursor:pointer"></i>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>PROJETO</th><th>USUÁRIO</th><th>TIPO</th><th>FUNÇÃO</th><th>CARGA</th><th>ENTRADA</th><th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-medium">Equipe SIMPA UEMA</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Andre+Nunes&background=fce7f3&color=be185d" class="rounded-circle" width="28">
                            Andre Nunes
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark border">Projeto Especial</span></td>
                    <td><span class="badge bg-info text-dark">Coordenador</span></td>
                    <td>120h</td>
                    <td>10/01/2026</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger ms-1" title="Remover"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-medium">Projeto Social Comunitário</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Augusto+Nicacio&background=d1fae5&color=065f46" class="rounded-circle" width="28">
                            Augusto Nicácio
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark border">Projeto Especial</span></td>
                    <td><span class="badge bg-warning text-dark">Pesquisador</span></td>
                    <td>85h</td>
                    <td>06/01/2026</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger ms-1" title="Remover"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-medium">Inovação Tecnológica Ambiental</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Bruno+Kauan&background=e0f2fe&color=0369a1" class="rounded-circle" width="28">
                            Bruno Kauan
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark border">Ligas Acadêmicas</span></td>
                    <td><span class="badge bg-success">Colaborador</span></td>
                    <td>80h</td>
                    <td>20/01/2026</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger ms-1" title="Remover"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-medium">Equipe SIMPA UEMA</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Aian&background=ede9fe&color=6d28d9" class="rounded-circle" width="28">
                            Aian
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark border">Projeto Especial</span></td>
                    <td><span class="badge bg-info text-dark">Desenvolvedor</span></td>
                    <td>60h</td>
                    <td>18/01/2026</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger ms-1" title="Remover"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-medium">Projeto Social Comunitário</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Jose+Kauan&background=fef9c3&color=854d0e" class="rounded-circle" width="28">
                            Jose Kauan
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark border">Atlética</span></td>
                    <td><span class="badge bg-secondary">Membro</span></td>
                    <td>40h</td>
                    <td>15/01/2026</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger ms-1" title="Remover"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
