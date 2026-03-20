<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Documentos</h3>
        <p class="text-muted mb-0">Revise e aprove os documentos enviados pelos seus alunos</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-files"></i></div>
            <div><h4 class="mb-0 fw-bold">32</h4><small class="text-muted">Total</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-clock-history"></i></div>
            <div><h4 class="mb-0 fw-bold">5</h4><small class="text-muted">Aguard. Revisão</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-check2-circle"></i></div>
            <div><h4 class="mb-0 fw-bold">25</h4><small class="text-muted">Aprovados</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-x-circle"></i></div>
            <div><h4 class="mb-0 fw-bold">2</h4><small class="text-muted">Reprovados</small></div>
        </div>
    </div>
</div>

<div class="content-card mb-3 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Buscar documento ou aluno...">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select">
                <option>Tipo</option>
                <option>Relatório</option>
                <option>Artigo</option>
                <option>Formulário</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select class="form-select">
                <option>Status</option>
                <option>Pendente</option>
                <option>Aprovado</option>
                <option>Reprovado</option>
            </select>
        </div>
        <div class="col-12 col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
    </div>
</div>

<div class="content-card">
    <h5 class="fw-bold mb-3">Documentos dos Alunos</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ARQUIVO</th><th>TIPO</th><th>ALUNO</th><th>PROJETO</th><th>ENVIADO EM</th><th>STATUS</th><th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><i class="bi bi-file-earmark-pdf text-danger me-2"></i><span class="fw-medium">Relatório_Nov2026.pdf</span></td>
                    <td>Relatório</td>
                    <td><div class="d-flex align-items-center gap-2"><img src="https://ui-avatars.com/api/?name=Joao&background=e0f2fe&color=0369a1" class="rounded-circle" width="26">João</div></td>
                    <td>SIMPA UEMA</td>
                    <td>18/11/2026</td>
                    <td><span class="badge bg-warning text-dark">Pendente</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Visualizar"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-success ms-1" title="Aprovar"><i class="bi bi-check2"></i></button>
                        <button class="btn btn-sm btn-outline-danger ms-1" title="Reprovar"><i class="bi bi-x"></i></button>
                    </td>
                </tr>
                <tr>
                    <td><i class="bi bi-file-earmark-excel text-success me-2"></i><span class="fw-medium">Planilha_Horas.xlsx</span></td>
                    <td>Relatório</td>
                    <td><div class="d-flex align-items-center gap-2"><img src="https://ui-avatars.com/api/?name=Augusto+Nicacio&background=d1fae5&color=065f46" class="rounded-circle" width="26">Augusto</div></td>
                    <td>SIMPA UEMA</td>
                    <td>15/11/2026</td>
                    <td><span class="badge bg-warning text-dark">Pendente</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Visualizar"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-success ms-1" title="Aprovar"><i class="bi bi-check2"></i></button>
                        <button class="btn btn-sm btn-outline-danger ms-1" title="Reprovar"><i class="bi bi-x"></i></button>
                    </td>
                </tr>
                <tr>
                    <td><i class="bi bi-file-earmark-word text-primary me-2"></i><span class="fw-medium">Artigo_Revisao.docx</span></td>
                    <td>Artigo</td>
                    <td><div class="d-flex align-items-center gap-2"><img src="https://ui-avatars.com/api/?name=Aian&background=ede9fe&color=6d28d9" class="rounded-circle" width="26">Aian</div></td>
                    <td>Inovação Tec.</td>
                    <td>10/11/2026</td>
                    <td><span class="badge bg-success">Aprovado</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Visualizar"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Baixar"><i class="bi bi-download"></i></button>
                    </td>
                </tr>
                <tr>
                    <td><i class="bi bi-file-earmark-pdf text-danger me-2"></i><span class="fw-medium">Declaracao_Matricula.pdf</span></td>
                    <td>Declaração</td>
                    <td><div class="d-flex align-items-center gap-2"><img src="https://ui-avatars.com/api/?name=Bruno+Kauan&background=fce7f3&color=be185d" class="rounded-circle" width="26">Bruno</div></td>
                    <td>Projeto Social</td>
                    <td>02/11/2026</td>
                    <td><span class="badge bg-danger">Reprovado</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Visualizar"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-warning ms-1" title="Feedback"><i class="bi bi-chat-text"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>