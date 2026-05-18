<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Relatórios</h3>
        <p class="text-muted mb-0">Acompanhe o progresso e a carga horária dos seus alunos</p>
    </div>
    <button class="btn btn-outline-primary"><i class="bi bi-download me-2"></i>Exportar</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-clock-history"></i></div>
            <div><h4 class="mb-0 fw-bold">785h</h4><small class="text-muted">Carga Total Registrada</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-graph-up"></i></div>
            <div><h4 class="mb-0 fw-bold">65h</h4><small class="text-muted">Média por Aluno</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-award"></i></div>
            <div><h4 class="mb-0 fw-bold">48</h4><small class="text-muted">Certificados Emitidos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-check2-all"></i></div>
            <div><h4 class="mb-0 fw-bold">82%</h4><small class="text-muted">Taxa de Conclusão</small></div>
        </div>
    </div>
</div>

<div class="content-card mb-3 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Buscar aluno...">
            </div>
        </div>
        <div class="col-6 col-md-4">
            <select class="form-select">
                <option>Projeto (Todos)</option>
                <option>SIMPA UEMA</option>
                <option>Projeto Social</option>
                <option>Inovação Tec.</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
    </div>
</div>

<div class="content-card">
    <h5 class="fw-bold mb-3">Progresso por Aluno</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ALUNO</th><th>PROJETO</th><th>CARGA REGISTRADA</th><th>TAREFAS</th><th>DOCS ENVIADOS</th><th>PROGRESSO</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><div class="d-flex align-items-center gap-2"><img src="https://ui-avatars.com/api/?name=Joao&background=e0f2fe&color=0369a1" class="rounded-circle" width="30">João</div></td>
                    <td>SIMPA UEMA</td>
                    <td>85h</td>
                    <td><span class="badge bg-success">4/5</span></td>
                    <td><span class="badge bg-info text-dark">3/4</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:8px;">
                                <div class="progress-bar bg-success" style="width:80%"></div>
                            </div>
                            <small class="text-muted">80%</small>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><div class="d-flex align-items-center gap-2"><img src="https://ui-avatars.com/api/?name=Augusto+Nicacio&background=d1fae5&color=065f46" class="rounded-circle" width="30">Augusto</div></td>
                    <td>Inovação Tec.</td>
                    <td>120h</td>
                    <td><span class="badge bg-success">5/5</span></td>
                    <td><span class="badge bg-success">4/4</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:8px;">
                                <div class="progress-bar bg-success" style="width:95%"></div>
                            </div>
                            <small class="text-muted">95%</small>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><div class="d-flex align-items-center gap-2"><img src="https://ui-avatars.com/api/?name=Aian&background=ede9fe&color=6d28d9" class="rounded-circle" width="30">Aian</div></td>
                    <td>Projeto Social</td>
                    <td>80h</td>
                    <td><span class="badge bg-warning text-dark">2/5</span></td>
                    <td><span class="badge bg-warning text-dark">1/4</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:8px;">
                                <div class="progress-bar bg-warning" style="width:45%"></div>
                            </div>
                            <small class="text-muted">45%</small>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><div class="d-flex align-items-center gap-2"><img src="https://ui-avatars.com/api/?name=Bruno+Kauan&background=fce7f3&color=be185d" class="rounded-circle" width="30">Bruno</div></td>
                    <td>SIMPA UEMA</td>
                    <td>60h</td>
                    <td><span class="badge bg-info text-dark">3/5</span></td>
                    <td><span class="badge bg-success">3/3</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:8px;">
                                <div class="progress-bar bg-info" style="width:65%"></div>
                            </div>
                            <small class="text-muted">65%</small>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
