<h3 class="fw-bold mb-1">Dashboard</h3>
<p class="text-muted mb-4">Visão geral das suas atividades</p>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-journal-text"></i></div>
            <div><h4 class="mb-0 fw-bold">3</h4><small class="text-muted">Projetos Ativos</small></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-clock-history"></i></div>
            <div><h4 class="mb-0 fw-bold">125</h4><small class="text-muted">Horas Registradas</small></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-hourglass-split"></i></div>
            <div><h4 class="mb-0 fw-bold">8</h4><small class="text-muted">Horas Pendentes</small></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-bell-fill"></i></div>
            <div><h4 class="mb-0 fw-bold">3</h4><small class="text-muted">Notificações</small></div>
        </div>
    </div>
</div>

<div class="content-card mb-4 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filtroProjetos" class="form-control border-start-0" placeholder="Buscar por título, orientador ou ID do projeto">
            </div>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Filtrar</button></div>
        <div class="col-md-2"><select class="form-select"><option>Status</option></select></div>
        <div class="col-md-2"><select class="form-select"><option>Pendências</option></select></div>
    </div>
</div>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">Projetos Ativos</h5>
        <div class="text-muted small">1-3 de 3 <i class="bi bi-chevron-left ms-2"></i><i class="bi bi-chevron-right ms-1"></i></div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="tabelaProjetos">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ID</th><th>TÍTULO</th><th>PROFESSOR ORIENTADOR</th><th>STATUS</th><th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-bold">1024</td>
                    <td>Projeto Social Comunitário</td>
                    <td>João Varela</td>
                    <td><span class="status-pill status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-detail-outline">Ver detalhes</button>
                        <button class="btn btn-sm btn-primary ms-1">Ver detalhes</button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold">1018</td>
                    <td>Inovação Tecnológica Ambiental</td>
                    <td>Profª Ana Bezerra</td>
                    <td><span class="status-pill status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-detail-outline">Ver detalhes</button>
                        <button class="btn btn-sm btn-primary ms-1">Ver detalhes</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>