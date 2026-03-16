<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Gestão de Projetos</h3>
        <p class="text-muted mb-0">Cadastre, organize e acompanhe os projetos extracurriculares da instituição</p>
    </div>
    <button class="btn btn-primary"><i class="bi bi-folder-plus me-2"></i>Novo Projeto</button>
</div>

<div class="content-card mb-4 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filtroProjetosPage" class="form-control border-start-0" placeholder="Buscar por título ou área de conhecimento">
            </div>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Pesquisar</button>
        </div>
        <div class="col-md-3">
            <select class="form-select">
                <option value="">Tipo (Todos)</option>
                <option value="1">Projeto Especial</option>
                <option value="2">Empresa Júnior</option>
                <option value="3">Diretório Acadêmico</option>
                <option value="4">Atlética</option>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select">
                <option value="">Status (Todos)</option>
                <option value="ativo">Ativo</option>
                <option value="concluido">Concluído</option>
                <option value="cancelado">Cancelado</option>
            </select>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Lista de Projetos</h5>
        <div class="text-muted small">1 de 3 <i class="bi bi-chevron-left ms-2" style="cursor:pointer"></i><i class="bi bi-chevron-right ms-1" style="cursor:pointer"></i></div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="tabelaProjetosPage">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ID</th>
                    <th>TÍTULO</th>
                    <th>TIPO</th>
                    <th>ÁREA</th>
                    <th>STATUS</th>
                    <th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-bold text-muted">#200</td>
                    <td><span class="fw-medium">Equipe SIMPA UEMA</span></td>
                    <td>Projeto Especial</td>
                    <td>PROEXAE</td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Editar Projeto"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-info ms-1" title="Ver Produções"><i class="bi bi-file-earmark-text"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Arquivar"><i class="bi bi-archive"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">#205</td>
                    <td><span class="fw-medium">Cálculo Jr. Consultoria</span></td>
                    <td>Empresa Júnior</td>
                    <td>Multidisciplinar</td>
                    <td><span class="status-ativo">Ativo</span></td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">#201</td>
                    <td><span class="fw-medium">Equipe Baja UEMA</span></td>
                    <td>Projeto Especial</td>
                    <td>Engenharia Mecânica</td>
                    <td><span class="status-ativo">Ativo</span></td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">#198</td>
                    <td><span class="fw-medium">Atlética Predadores</span></td>
                    <td>Atlética</td>
                    <td>Esportes / Computação</td>
                    <td><span class="badge bg-secondary">Concluído</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Editar Projeto"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-info ms-1" title="Ver Produções"><i class="bi bi-file-earmark-text"></i></button>
                        <button class="btn btn-sm btn-outline-success ms-1" title="Reativar"><i class="bi bi-check-circle"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
