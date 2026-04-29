<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Gerenciar Projetos</h3>
        <p class="text-muted mb-0">Projetos em que você participa</p>
    </div>

    <button class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#modalNovoProjeto">
        <i class="bi bi-folder-plus me-2"></i>Solicitar Novo Projeto
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-folder-fill"></i></div>
            <div><h4 class="mb-0 fw-bold">1</h4><small class="text-muted">Projeto Ativo (Bolsa)</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-heart"></i></div>
            <div><h4 class="mb-0 fw-bold">2</h4><small class="text-muted">Projetos Participando</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-check2-all"></i></div>
            <div><h4 class="mb-0 fw-bold">1</h4><small class="text-muted">Concluído</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-clock-history"></i></div>
            <div><h4 class="mb-0 fw-bold">285h</h4><small class="text-muted">Carga Total</small></div>
        </div>
    </div>
</div>

<div class="content-card">
    <h5 class="fw-bold mb-3">Meus Projetos</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>TÍTULO</th>
                    <th>TIPO</th>
                    <th>FUNÇÃO</th>
                    <th>ORIENTADOR</th>
                    <th>CARGA</th>
                    <th>STATUS</th>
                    <th class="text-center">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-medium">Projeto Social Comunitário</td>
                    <td><span class="badge bg-light text-dark border">Projeto Especial</span></td>
                    <td>Pesquisador</td>
                    <td>Prof. João Varela</td>
                    <td>85h</td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <a href="assets/docs/projeto-social.pdf" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="assets/docs/projeto-social.pdf" download class="btn btn-sm btn-outline-secondary ms-1">
                            <i class="bi bi-file-earmark-text"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="fw-medium">Equipe SIMPA UEMA</td>
                    <td><span class="badge bg-light text-dark border">Projeto Especial</span></td>
                    <td>Desenvolvedor</td>
                    <td>Prof. André Nunes</td>
                    <td>120h</td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <a href="assets/docs/simpa.pdf" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="assets/docs/simpa.pdf" download class="btn btn-sm btn-outline-secondary ms-1">
                            <i class="bi bi-file-earmark-text"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="fw-medium">Inovação Tecnológica Ambiental</td>
                    <td><span class="badge bg-light text-dark border">Ligas Acadêmicas</span></td>
                    <td>Colaborador</td>
                    <td>Profª Ana Bezerra</td>
                    <td>80h</td>
                    <td><span class="status-ativo">Ativo</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-secondary disabled"><i class="bi bi-eye-slash"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1 disabled"><i class="bi bi-file-earmark-x"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="fw-medium">Atlética Predadores</td>
                    <td><span class="badge bg-light text-dark border">Atlética</span></td>
                    <td>Membro</td>
                    <td>—</td>
                    <td>40h</td>
                    <td><span class="badge bg-secondary text-white">Concluído</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-secondary disabled"><i class="bi bi-eye-slash"></i></button>
                        <button class="btn btn-sm btn-outline-secondary ms-1 disabled"><i class="bi bi-file-earmark-x"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL NOVO PROJETO -->
<div class="modal fade" id="modalNovoProjeto" tabindex="-1" aria-labelledby="modalNovoProjetoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-novo-projeto">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalNovoProjetoLabel">
                    <i class="bi bi-folder-plus me-2 text-primary"></i>Solicitar Novo Projeto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">

                    <!-- Título + Tipo (mesma linha) -->
                    <div class="col-12 col-md-7">
                        <label class="form-label">Título do Projeto</label>
                        <input type="text" class="form-control" placeholder="Nome do projeto">
                    </div>
                    <div class="col-12 col-md-5">
                        <label class="form-label">Tipo de Projeto</label>
                        <select class="form-select">
                            <option value="">Selecione...</option>
                            <option>Projeto Especial</option>
                            <option>Empresa Júnior</option>
                            <option>Liga Acadêmica</option>
                            <option>Extensão</option>
                        </select>
                    </div>

                    <!-- Descrição (linha inteira) -->
                    <div class="col-12">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" rows="3" placeholder="Descreva brevemente o projeto..."></textarea>
                    </div>

                    <!-- Orientador + Carga (mesma linha) -->
                    <div class="col-12 col-md-7">
                        <label class="form-label">Orientador</label>
                        <input type="text" class="form-control" placeholder="Nome do orientador">
                    </div>
                    <div class="col-12 col-md-5">
                        <label class="form-label">Carga Horária</label>
                        <input type="number" class="form-control" placeholder="Ex: 120">
                    </div>

                    <!-- Anexo (linha inteira) -->
                    <div class="col-12">
                        <label class="form-label">Anexar Documento</label>
                        <input type="file" class="form-control">
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i>SOLICITAR
                </button>
            </div>

        </div>
    </div>
</div>