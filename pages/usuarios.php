<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Gestão de Usuários</h3>
        <p class="text-muted mb-0">Gerencie acessos, perfis e status dos usuários do sistema</p>
    </div>
    <button class="btn btn-primary"><i class="bi bi-person-plus me-2"></i>Novo Usuário</button>
</div>

<div class="content-card mb-4 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filtroUsuarios" class="form-control border-start-0" placeholder="Buscar por nome, email ou ID do usuário">
            </div>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Pesquisar</button>
        </div>
        <div class="col-md-2">
            <select class="form-select">
                <option value="">Status (Todos)</option>
                <option value="ativo">Ativos</option>
                <option value="inativo">Inativos</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select">
                <option value="">Perfil (Todos)</option>
                <option value="admin">Administrador</option>
                <option value="orientador">Professor Orientador</option>
                <option value="bolsista">Aluno Bolsista</option>
            </select>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Lista de Usuários</h5>
        <div class="text-muted small">1-3 de 3 <i class="bi bi-chevron-left ms-2" style="cursor:pointer"></i><i class="bi bi-chevron-right ms-1" style="cursor:pointer"></i></div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="tabelaUsuarios">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ID</th>
                    <th>NOME DO USUÁRIO</th>
                    <th>EMAIL</th>
                    <th>PERFIL</th>
                    <th>STATUS</th>
                    <th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-bold text-muted">#1024</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Bruno+Kauan&background=e0f2fe&color=0369a1" class="rounded-circle" width="32">
                            <span class="fw-medium">Bruno Kauan</span>
                        </div>
                    </td>
                    <td>bruno.kauan@uema.br</td>
                    <td><span class="badge bg-secondary">Administrador</span></td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Configurações"><i class="bi bi-gear"></i></button>
                        <button class="btn btn-sm btn-outline-danger ms-1" title="Desativar"><i class="bi bi-person-x"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">#1018</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Andre+Nunes&background=fce7f3&color=be185d" class="rounded-circle" width="32">
                            <span class="fw-medium">Andre Nunes</span>
                        </div>
                    </td>
                    <td>andre.nunes@uema.br</td>
                    <td><span class="badge bg-info text-dark">Professor Orientador</span></td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Configurações"><i class="bi bi-gear"></i></button>
                        <button class="btn btn-sm btn-outline-danger ms-1" title="Desativar"><i class="bi bi-person-x"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">#1045</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Jose+Kauan&background=fef08a&color=854d0e" class="rounded-circle" width="32">
                            <span class="fw-medium">Jose Kauan</span>
                        </div>
                    </td>
                    <td>jose.kauan@aluno.uema.br</td>
                    <td><span class="badge bg-light text-dark border">Aluno Bolsista</span></td>
                    <td><span class="status-inativo">Inativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" title="Configurações"><i class="bi bi-gear"></i></button>
                        <button class="btn btn-sm btn-outline-success ms-1" title="Ativar"><i class="bi bi-person-check"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>