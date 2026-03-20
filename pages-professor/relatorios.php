<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Relatórios & Produção Acadêmica</h3>
        <p class="text-muted mb-0">Acompanhe o progresso dos alunos e registre a produção dos projetos</p>
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

<!-- ABAS -->
<ul class="nav nav-tabs mb-3" id="tabsRelatorio">
    <li class="nav-item">
        <button class="nav-link active" data-tab="progresso">
            <i class="bi bi-graph-up me-1"></i>Progresso dos Alunos
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-tab="producao">
            <i class="bi bi-journal-richtext me-1"></i>Produção Acadêmica
        </button>
    </li>
</ul>

<!-- TAB: PROGRESSO -->
<div id="tab-progresso">
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
                        <td>SIMPA UEMA</td><td>85h</td>
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
                        <td>Inovação Tec.</td><td>120h</td>
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
                        <td>Projeto Social</td><td>80h</td>
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
                        <td>SIMPA UEMA</td><td>60h</td>
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
</div>

<!-- TAB: PRODUÇÃO ACADÊMICA -->
<div id="tab-producao" style="display:none;">

    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="icon-circle bg-light-blue"><i class="bi bi-file-earmark-text"></i></div>
                <div><h4 class="mb-0 fw-bold">12</h4><small class="text-muted">Relatórios Enviados</small></div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="icon-circle bg-light-blue"><i class="bi bi-journal-bookmark"></i></div>
                <div><h4 class="mb-0 fw-bold">4</h4><small class="text-muted">Publicações</small></div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="icon-circle bg-light-orange"><i class="bi bi-calendar-event"></i></div>
                <div><h4 class="mb-0 fw-bold">3</h4><small class="text-muted">Eventos Registrados</small></div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="icon-circle bg-light-orange"><i class="bi bi-box-seam"></i></div>
                <div><h4 class="mb-0 fw-bold">5</h4><small class="text-muted">Produtos Gerados</small></div>
            </div>
        </div>
    </div>

    <div class="content-card mb-3 p-3">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Buscar produção...">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select class="form-select">
                    <option>Tipo (Todos)</option>
                    <option>Relatório</option>
                    <option>Publicação</option>
                    <option>Evento</option>
                    <option>Produto</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select class="form-select">
                    <option>Projeto (Todos)</option>
                    <option>SIMPA UEMA</option>
                    <option>Projeto Social</option>
                    <option>Inovação Tec.</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button class="btn btn-primary w-100">Filtrar</button>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Produções Registradas</h5>
            <button class="btn btn-sm btn-primary"><i class="bi bi-plus me-1"></i>Registrar</button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th>TÍTULO</th><th>TIPO</th><th>ALUNO</th><th>PROJETO</th><th>DATA</th><th>AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-medium">Relatório Semestral SIMPA</td>
                        <td><span class="badge bg-primary">Relatório</span></td>
                        <td>João</td><td>SIMPA UEMA</td><td>10/11/2026</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-eye"></i></button>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Artigo: Automação de Processos</td>
                        <td><span class="badge" style="background:#8b5cf6;">Publicação</span></td>
                        <td>Augusto</td><td>Inovação Tec.</td><td>05/11/2026</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-eye"></i></button>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Apresentação PROEXAE 2026</td>
                        <td><span class="badge bg-success">Evento</span></td>
                        <td>José</td><td>PROEXAE</td><td>25/11/2026</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-eye"></i></button>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Protótipo Sistema de Monitoramento</td>
                        <td><span class="badge bg-warning text-dark">Produto</span></td>
                        <td>Aian</td><td>Projeto Social</td><td>15/11/2026</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-eye"></i></button>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Relatório de Atividades Extensão</td>
                        <td><span class="badge bg-primary">Relatório</span></td>
                        <td>Bruno</td><td>SIMPA UEMA</td><td>01/11/2026</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-eye"></i></button>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
(function() {
    document.querySelectorAll('#tabsRelatorio .nav-link').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('#tabsRelatorio .nav-link').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const tab = this.dataset.tab;
            document.getElementById('tab-progresso').style.display = tab === 'progresso' ? '' : 'none';
            document.getElementById('tab-producao').style.display  = tab === 'producao'  ? '' : 'none';
        });
    });
})();
</script>
