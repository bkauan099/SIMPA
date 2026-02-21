<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard ProExae - Uema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --sidebar-bg: #1e3a8a; /* Azul Escuro UEMA */
            --sidebar-active: #3b82f6; /* Azul Claro Ativo */
            --bg-light-gray: #f8fafc;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        body {
            background-color: var(--bg-light-gray);
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Layout Base */
        .wrapper {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            padding: 0px 0px 20px;
            color: white;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
        }

        .sidebar-logo {
            
            padding: 15px 25px ;
            
            display: flex;
            align-items: center;
            gap: 15px;
            
        }

        .sidebar-logo img{
            width: 150px;
            background-color: white;
            border-radius: 8px;
            padding: 5px 5px 5px 5px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            transition: 0.2s;
        }

        .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.05);
        }

        .nav-link.active {
            color: white;
            background-color: var(--sidebar-active);
            border-left: 4px solid white;
        }

        /* Topbar */
        .topbar {
            background-color: var(--sidebar-bg);
            padding: 30px 30px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            
        }
        .topbar img{
            width: 150px;

        }
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        /* Componentes de Dashboard */
        .content-area {
            padding: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            border: none;
        }

        .stat-icon {
            font-size: 1.5rem;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }

        .bg-blue-light { background-color: #eff6ff; color: #3b82f6; }
        .bg-orange-light { background-color: #fff7ed; color: #f97316; }

        .custom-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            border: none;
        }

        /* Tabelas */
        .table > :not(caption) > * > * {
            padding: 16px 10px;
            border-bottom-color: #f1f5f9;
            color: var(--text-dark);
            vertical-align: middle;
        }
        
        .table th {
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        /* Badges e Botões */
        .badge-status {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
        }
        .badge-ativo { background-color: #dcfce7; color: #16a34a; }
        .badge-pendente { background-color: #ffedd5; color: #ea580c; }

        .btn-ghost {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid #e2e8f0;
        }
        .btn-ghost:hover {
            background: #f8fafc;
            color: var(--text-dark);
        }

        .pendencia-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .pendencia-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
    </style>
</head>
<body>
    
<div class="wrapper">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class=" rounded p-1">
                <i class=""></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold">
                    <img src="assets/img/uema-logo.png" alt="">
                </h5>
                <small style="font-size: 0.65rem; line-height: 1;"><br></small>
            </div>
        </div>

        <ul class="nav flex-column mt-3 mb-auto">
            <li class="nav-item">
                <a href="#" class="nav-link active">
                    <i class="bi bi-house-door-fill"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-folder-fill"></i> Meus Projetos
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-list-task"></i> Minhas Atividades
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-file-earmark-text-fill"></i> Documentos
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-award-fill"></i> Certificados
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-person-fill"></i> Meu Perfil
                </a>
            </li>
        </ul>

        <div class="mt-auto p-3">
            <a href="#" class="nav-link">
                <i class="bi bi-power"></i> Sair
            </a>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi  fs-3 text-warning"></i>
                    <h4 class="mb-0 fw-bold">
                        <img src="assets/img/Proexae.png" alt="">
                    </h4>
                </div>
                <div style="font-size: 0.7rem; line-height: 1.2; border-left: 1px solid rgba(255,255,255,0.2);" class="ps-3 ms-2 d-none d-md-block">
                    Programas e<br>Projetos de Extensão<br>Ação Estudantil
                </div>
            </div>

            <div class="d-flex align-items-center gap-4">
                <div class="position-relative">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                        2
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2" style="cursor: pointer;">
                    <img src="https://ui-avatars.com/api/?name=João&background=random" alt="Avatar" class="rounded-circle" width="35" height="35">
                    <span class="fw-medium">João</span>
                    <i class="bi bi-chevron-down fs-6"></i>
                </div>
            </div>
        </header>

        <div class="content-area">
            <h2 class="fw-bold mb-1">Dashboard</h2>
            <p class="text-muted mb-4">Visão geral das suas atividades</p>

            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-blue-light">
                            <i class="bi bi-journal-album"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">3</h3>
                            <span class="text-muted small">Projetos Ativos</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-blue-light">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">125</h3>
                            <span class="text-muted small">Horas Registradas</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-orange-light">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">8</h3>
                            <span class="text-muted small">Horas Pendentes</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-blue-light position-relative">
                            <i class="bi bi-bell-fill"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">3</span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">3</h3>
                            <span class="text-muted small">Notificações</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="custom-card p-3 mb-4 d-flex gap-3 align-items-center flex-wrap">
                <div class="input-group" style="flex: 1; min-width: 300px;">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" placeholder="Buscar projeto por título, orientador ou ID">
                </div>
                <button class="btn btn-primary px-4">Filtrar</button>
                <select class="form-select" style="width: auto;">
                    <option selected>Status</option>
                    <option value="1">Ativo</option>
                    <option value="2">Pendente</option>
                </select>
                <select class="form-select" style="width: auto;">
                    <option selected>Pendências</option>
                </select>
            </div>

            <div class="custom-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">Projetos Ativos</h5>
                        <p class="text-muted small mb-0">Lista dos projetos nos quais você está envolvido.</p>
                    </div>
                    <div class="text-muted small d-flex align-items-center gap-2">
                        1-3 de 3 
                        <button class="btn btn-sm btn-light p-1"><i class="bi bi-chevron-left"></i></button>
                        <button class="btn btn-sm btn-light p-1"><i class="bi bi-chevron-right"></i></button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Professor Orientador</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-medium">1024</td>
                                <td>Projeto Social Comunitário</td>
                                <td>João Varela</td>
                                <td><span class="badge-status badge-ativo">Ativo</span></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-ghost me-2">Ver detalhes</button>
                                    <button class="btn btn-sm btn-primary">Ver detalhes</button>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">1018</td>
                                <td>Inovação Tecnológica Ambien...</td>
                                <td>Profª Ana Bezerra</td>
                                <td><span class="badge-status badge-ativo">Ativo</span></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-ghost me-2">Ver detalhes</button>
                                    <button class="btn btn-sm btn-primary">Ver detalhes</button>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">0985</td>
                                <td>Capacitação em TICs Educac...</td>
                                <td>Prof. Paulo Nogueira</td>
                                <td><span class="badge-status badge-pendente">Pendente</span></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-ghost me-2">Ver detalhes</button>
                                    <button class="btn btn-sm btn-primary">Ver detalhes</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="custom-card p-4 h-100">
                        <h5 class="fw-bold mb-3">Pendências</h5>
                        
                        <div class="pendencia-item">
                            <div class="d-flex align-items-center gap-2 text-muted">
                                <i class="bi bi-exclamation-circle text-warning"></i>
                                <span>2 horas aguardando validação</span>
                            </div>
                            <a href="#" class="text-primary text-decoration-none fw-medium small">Resolver <i class="bi bi-chevron-right"></i></a>
                        </div>
                        
                        <div class="pendencia-item">
                            <div class="d-flex align-items-center gap-2 text-muted">
                                <i class="bi bi-file-earmark-text"></i>
                                <span>1 documento aguardando envio</span>
                            </div>
                            <a href="#" class="text-primary text-decoration-none fw-medium small">Resolver <i class="bi bi-chevron-right"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="custom-card p-4 h-100">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-folder-symlink text-primary fs-5"></i>
                            <h6 class="text-muted fw-medium mb-0">Ver todas as pendências</h6>
                        </div>
                        
                        <div class="pendencia-item">
                            <div class="d-flex align-items-center gap-2 text-muted">
                                <i class="bi bi-file-earmark-text"></i>
                                <span>1 documento aguardando envio</span>
                            </div>
                            <a href="#" class="text-primary text-decoration-none fw-medium small">Resolver <i class="bi bi-chevron-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>

        </div> </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>