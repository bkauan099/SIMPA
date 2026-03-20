<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Minhas Tarefas</h3>
        <p class="text-muted mb-0">Acompanhe e atualize o status das suas tarefas</p>
    </div>
    <button class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Nova Tarefa</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-list-check"></i></div>
            <div><h4 class="mb-0 fw-bold">8</h4><small class="text-muted">Total de Tarefas</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-hourglass"></i></div>
            <div><h4 class="mb-0 fw-bold">3</h4><small class="text-muted">Pendentes</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-arrow-repeat"></i></div>
            <div><h4 class="mb-0 fw-bold">2</h4><small class="text-muted">Em Andamento</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-check2-circle"></i></div>
            <div><h4 class="mb-0 fw-bold">3</h4><small class="text-muted">Concluídas</small></div>
        </div>
    </div>
</div>

<div class="content-card mb-3 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Buscar tarefa...">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select">
                <option>Status (Todos)</option>
                <option>Pendente</option>
                <option>Em Andamento</option>
                <option>Concluída</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select class="form-select">
                <option>Projeto</option>
                <option>SIMPA UEMA</option>
                <option>Inovação Tec.</option>
            </select>
        </div>
        <div class="col-12 col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
    </div>
</div>

<div class="content-card">
    <h5 class="fw-bold mb-3">Lista de Tarefas</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>TÍTULO</th>
                    <th>PROJETO</th>
                                    <th>TIPO</th>
                    <th>PRAZO</th>
                    <th>PRIORIDADE</th>
                    <th>STATUS</th>
                    <th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-medium">Revisão do Artigo</td>
                    <td>SIMPA UEMA</td>
                                    <td><span class="badge bg-light text-dark border">Projeto Especial</span></td>
                    <td>20/11/2026</td>
                    <td><span class="badge bg-danger">Alta</span></td>
                    <td><span class="badge bg-warning text-dark">Pendente</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-success" title="Marcar Concluída"><i class="bi bi-check2"></i></button>
                        <button class="btn btn-sm btn-outline-primary ms-1" title="Editar"><i class="bi bi-pencil"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-medium">Entrega Relatório</td>
                    <td>Inovação Tec.</td>
                                    <td><span class="badge bg-light text-dark border">Ligas Acadêmicas</span></td>
                    <td>22/11/2026</td>
                    <td><span class="badge bg-danger">Alta</span></td>
                    <td><span class="badge bg-info text-dark">Em Andamento</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-success" title="Marcar Concluída"><i class="bi bi-check2"></i></button>
                        <button class="btn btn-sm btn-outline-primary ms-1" title="Editar"><i class="bi bi-pencil"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-medium">Levantamento Bibliográfico</td>
                    <td>Projeto Social</td>
                                    <td><span class="badge bg-light text-dark border">Projeto Especial</span></td>
                    <td>30/11/2026</td>
                    <td><span class="badge bg-warning text-dark">Média</span></td>
                    <td><span class="badge bg-info text-dark">Em Andamento</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-success" title="Marcar Concluída"><i class="bi bi-check2"></i></button>
                        <button class="btn btn-sm btn-outline-primary ms-1" title="Editar"><i class="bi bi-pencil"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-medium">Reunião com Orientador</td>
                    <td>SIMPA UEMA</td>
                                    <td><span class="badge bg-light text-dark border">Projeto Especial</span></td>
                    <td>10/11/2026</td>
                    <td><span class="badge bg-secondary">Baixa</span></td>
                    <td><span class="badge bg-success">Concluída</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-secondary" title="Ver detalhes"><i class="bi bi-eye"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-medium">Submissão do Formulário PROEXAE</td>
                    <td>Inovação Tec.</td>
                                    <td><span class="badge bg-light text-dark border">Ligas Acadêmicas</span></td>
                    <td>05/11/2026</td>
                    <td><span class="badge bg-warning text-dark">Média</span></td>
                    <td><span class="badge bg-success">Concluída</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-secondary" title="Ver detalhes"><i class="bi bi-eye"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
