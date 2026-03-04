<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard ProExae - UEMA</title>
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Fontes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/aluno-page.css">
</head>
<body>

<div class="wrapper">
    <!-- SIDEBAR -->
    <nav id="sidebar">
        <ul class="list-unstyled components">
            <li><a href="#dashboard" class="active menu-item" data-target="dashboard"><i class="bi bi-house-door"></i> <span>Dashboard</span></a></li>
            <li><a href="#projetos" class="menu-item" data-target="projetos"><i class="bi bi-folder"></i> <span>Meus Projetos</span></a></li>
            <li><a href="#atividades" class="menu-item" data-target="atividades"><i class="bi bi-check2-square"></i> <span>Minhas Atividades</span></a></li>
            <li><a href="#documentos" class="menu-item" data-target="documentos"><i class="bi bi-file-earmark-text"></i> <span>Documentos</span></a></li>
            <li><a href="#certificados" class="menu-item" data-target="certificados"><i class="bi bi-award"></i> <span>Certificados</span></a></li>
            <li><a href="#perfil" class="menu-item" data-target="perfil"><i class="bi bi-person"></i> <span>Meu Perfil</span></a></li>
            <li class="mt-auto"><a href="#sair"><i class="bi bi-box-arrow-left"></i> <span>Sair</span></a></li>
        </ul>
    </nav>

    <!-- CONTEÚDO PRINCIPAL -->
    <div id="content">
        <!-- HEADER COM MENU SANDUÍCHE -->
        <header class="navbar-custom">
            <div class="d-flex align-items-center gap-3">
                <button class="menu-sanduiche" id="menuToggle">
                    <i class="bi bi-list"></i>
                </button>
                <img src="assets/img/uema-logo.png" alt="UEMA" class="logo-uema">
                <img src="assets/img/proexae-logocor.png" alt="ProExae" class="logo-proexae">
            </div>

            <div class="d-flex align-items-center gap-4">
                <div class="position-relative">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">2</span>
                </div>
                <div class="d-flex align-items-center gap-2" style="cursor:pointer">
                    <img src="https://ui-avatars.com/api/?name=João&background=random" class="rounded-circle" width="35">
                    <span class="fw-medium">João <i class="bi bi-chevron-down small"></i></span>
                </div>
            </div>
        </header>

        <!-- ÁREA DAS TELAS -->
        <div class="dashboard-container">
            <!-- TELA DASHBOARD -->
            <div id="tela-dashboard" class="tela ativa">
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
                                <input type="text" id="filtroTabela" class="form-control border-start-0" placeholder="Buscar por título, orientador ou ID">
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
            </div>

            <!-- TELA MEUS PROJETOS -->
            <div id="tela-projetos" class="tela">
                <h3 class="fw-bold mb-1">Meus Projetos</h3>
                <p class="text-muted mb-4">Lista de todos os seus projetos</p>
                <div class="content-card">
                    <p>Conteúdo da página Meus Projetos</p>
                </div>
            </div>

            <!-- TELA MINHAS ATIVIDADES -->
            <div id="tela-atividades" class="tela">
                <h3 class="fw-bold mb-1">Minhas Atividades</h3>
                <p class="text-muted mb-4">Acompanhe suas atividades</p>
                <div class="content-card">
                    <p>Conteúdo da página Minhas Atividades</p>
                </div>
            </div>

            <!-- TELA DOCUMENTOS -->
            <div id="tela-documentos" class="tela">
                <h3 class="fw-bold mb-1">Documentos</h3>
                <p class="text-muted mb-4">Gerencie seus documentos</p>
                <div class="content-card">
                    <p>Conteúdo da página Documentos</p>
                </div>
            </div>

            <!-- TELA CERTIFICADOS -->
            <div id="tela-certificados" class="tela">
                <h3 class="fw-bold mb-1">Certificados</h3>
                <p class="text-muted mb-4">Seus certificados</p>
                <div class="content-card">
                    <p>Conteúdo da página Certificados</p>
                </div>
            </div>

            <!-- TELA MEU PERFIL -->
            <div id="tela-perfil" class="tela">
                <h3 class="fw-bold mb-1">Meu Perfil</h3>
                <p class="text-muted mb-4">Suas informações pessoais</p>
                <div class="content-card">
                    <p>Conteúdo da página Meu Perfil</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/aluno-page.js"></script>

</body>
</html>