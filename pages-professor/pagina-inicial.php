<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Página Inicial</h3>
        <p class="text-muted mb-0">Visão geral dos seus projetos e alunos</p>
    </div>
    <button class="btn btn-primary"><i class="bi bi-folder-plus me-2"></i>Novo Projeto</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-folder-fill"></i></div>
            <div><h4 class="mb-0 fw-bold">3</h4><small class="text-muted">Projetos Ativos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-people"></i></div>
            <div><h4 class="mb-0 fw-bold">12</h4><small class="text-muted">Alunos Orientados</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-file-earmark-check"></i></div>
            <div><h4 class="mb-0 fw-bold">5</h4><small class="text-muted">Docs Pendentes</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-alarm"></i></div>
            <div><h4 class="mb-0 fw-bold">3</h4><small class="text-muted">Tarefas Vencendo</small></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Projetos recentes -->
    <div class="col-lg-7">
        <div class="content-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Meus Projetos</h5>
                <a href="?page=meus-projetos" class="btn btn-sm btn-outline-primary">Ver todos</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr class="text-muted small">
                            <th>TÍTULO</th><th>TIPO</th><th>ALUNOS</th><th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-medium">Projeto Social Comunitário</td>
                            <td><span class="badge bg-light text-dark border">Projeto Especial</span></td>
                            <td>4</td>
                            <td><span class="status-ativo">Ativo</span></td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Equipe SIMPA UEMA</td>
                            <td><span class="badge bg-light text-dark border">Projeto Especial</span></td>
                            <td>5</td>
                            <td><span class="status-ativo">Ativo</span></td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Inovação Tecnológica Ambiental</td>
                            <td><span class="badge bg-light text-dark border">Ligas Acadêmicas</span></td>
                            <td>3</td>
                            <td><span class="status-ativo">Ativo</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Documentos pendentes -->
    <div class="col-lg-5">
        <div class="content-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Documentos Pendentes</h5>
                <a href="?page=documentos" class="btn btn-sm btn-outline-primary">Ver todos</a>
            </div>
            <div class="d-flex flex-column gap-2">
                <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <div>
                        <p class="fw-medium mb-0" style="font-size:0.88rem;">Relatório_Nov2023.pdf</p>
                        <small class="text-muted">João · Projeto Social</small>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-success" title="Aprovar"><i class="bi bi-check2"></i></button>
                        <button class="btn btn-sm btn-outline-danger" title="Reprovar"><i class="bi bi-x"></i></button>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <div>
                        <p class="fw-medium mb-0" style="font-size:0.88rem;">Planilha_Horas.xlsx</p>
                        <small class="text-muted">Augusto · SIMPA UEMA</small>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-success" title="Aprovar"><i class="bi bi-check2"></i></button>
                        <button class="btn btn-sm btn-outline-danger" title="Reprovar"><i class="bi bi-x"></i></button>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <div>
                        <p class="fw-medium mb-0" style="font-size:0.88rem;">Artigo_Revisao.docx</p>
                        <small class="text-muted">Aian · Inovação Tec.</small>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-success" title="Aprovar"><i class="bi bi-check2"></i></button>
                        <button class="btn btn-sm btn-outline-danger" title="Reprovar"><i class="bi bi-x"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
