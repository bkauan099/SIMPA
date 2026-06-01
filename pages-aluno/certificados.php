<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Certificados</h3>
        <p class="text-muted mb-0">Certificados conquistados em projetos e eventos</p>
    </div>
    <button class="btn btn-outline-primary"><i class="bi bi-download me-2"></i>Baixar Todos</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-award"></i></div>
            <div><h4 class="mb-0 fw-bold">32</h4><small class="text-muted">Total de Certificados</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-folder2-open"></i></div>
            <div><h4 class="mb-0 fw-bold">5</h4><small class="text-muted">Projetos Certificados</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-calendar-event"></i></div>
            <div><h4 class="mb-0 fw-bold">27</h4><small class="text-muted">Eventos Certificados</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-clock"></i></div>
            <div><h4 class="mb-0 fw-bold">285h</h4><small class="text-muted">Carga Total Certificada</small></div>
        </div>
    </div>
</div>

<div class="content-card mb-3 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Buscar certificado...">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select">
                <option>Tipo (Todos)</option>
                <option>Projeto</option>
                <option>Evento</option>
                <option>Workshop</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
    </div>
</div>

<div class="row g-3">
    <?php
    $certificados = [
        ['titulo' => 'Equipe SIMPA UEMA', 'tipo' => 'Projeto', 'carga' => '120h', 'data' => 'Dez/2023', 'cor' => 'primary'],
        ['titulo' => 'Workshop Engenharia de Software', 'tipo' => 'Workshop', 'carga' => '8h', 'data' => 'Nov/2023', 'cor' => 'success'],
        ['titulo' => 'Apresentação Final PROEXAE', 'tipo' => 'Evento', 'carga' => '4h', 'data' => 'Nov/2023', 'cor' => 'info'],
        ['titulo' => 'Inovação Tecnológica Ambiental', 'tipo' => 'Projeto', 'carga' => '80h', 'data' => 'Out/2023', 'cor' => 'primary'],
        ['titulo' => 'Semana Acadêmica UEMA', 'tipo' => 'Evento', 'carga' => '16h', 'data' => 'Set/2023', 'cor' => 'info'],
        ['titulo' => 'Atlética Predadores', 'tipo' => 'Projeto', 'carga' => '40h', 'data' => 'Dez/2023', 'cor' => 'secondary'],
    ];
    foreach ($certificados as $cert): ?>
    <div class="col-sm-6 col-lg-4">
        <div class="content-card h-100 d-flex flex-column" style="margin-bottom:0;">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="icon-circle bg-light-blue" style="flex-shrink:0;">
                    <i class="bi bi-award-fill text-warning fs-5"></i>
                </div>
                <div>
                    <p class="fw-bold mb-0" style="font-size:0.95rem;"><?= $cert['titulo'] ?></p>
                    <small class="text-muted"><?= $cert['data'] ?></small>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                <div class="d-flex gap-2">
                    <span class="badge bg-<?= $cert['cor'] ?>"><?= $cert['tipo'] ?></span>
                    <span class="badge bg-light text-dark border"><?= $cert['carga'] ?></span>
                </div>
                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i></button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
