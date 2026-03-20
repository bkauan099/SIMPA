<!-- CABEÇALHO -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Olá, Professor 👋</h3>
        <p class="text-muted mb-0" id="dataHoje"></p>
    </div>
</div>

<!-- CARDS DE RESUMO -->
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

<div class="row g-3 mb-3">

    <!-- PRÓXIMAS ATIVIDADES DO DIA -->
    <div class="col-lg-4">
        <div class="content-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-calendar-check me-2 text-primary"></i>Hoje & Amanhã</h5>
                <a href="?page=cronograma" class="btn btn-sm btn-outline-primary">Ver agenda</a>
            </div>
            <div class="d-flex flex-column gap-2" id="atividadesHoje"></div>
        </div>
    </div>

    <!-- GRÁFICO DE PROJETOS POR TIPO -->
    <div class="col-lg-4">
        <div class="content-card h-100">
            <h5 class="fw-bold mb-1"><i class="bi bi-pie-chart me-2 text-primary"></i>Projetos por Tipo</h5>
            <p class="text-muted mb-3" style="font-size:0.8rem;">Distribuição dos seus projetos ativos</p>
            <canvas id="graficoProjetos" height="200"></canvas>
            <div id="legendaGrafico" class="d-flex flex-wrap gap-2 mt-3 justify-content-center" style="font-size:0.78rem;"></div>
        </div>
    </div>

    <!-- ALUNOS QUE PRECISAM DE ATENÇÃO -->
    <div class="col-lg-4">
        <div class="content-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Atenção Necessária</h5>
                <a href="?page=alunos" class="btn btn-sm btn-outline-primary">Ver alunos</a>
            </div>
            <div class="d-flex flex-column gap-2">
                <div class="d-flex align-items-center gap-2 p-2 rounded" style="background:#fff7ed;border:1px solid #fed7aa;">
                    <img src="https://ui-avatars.com/api/?name=Aian&background=ede9fe&color=6d28d9" class="rounded-circle" width="32">
                    <div class="flex-grow-1">
                        <p class="fw-medium mb-0" style="font-size:0.85rem;">Aian</p>
                        <small class="text-muted">2 tarefas atrasadas · Projeto Social</small>
                    </div>
                    <span class="badge bg-danger">Urgente</span>
                </div>
                <div class="d-flex align-items-center gap-2 p-2 rounded" style="background:#fff7ed;border:1px solid #fed7aa;">
                    <img src="https://ui-avatars.com/api/?name=Bruno+Kauan&background=fce7f3&color=be185d" class="rounded-circle" width="32">
                    <div class="flex-grow-1">
                        <p class="fw-medium mb-0" style="font-size:0.85rem;">Bruno</p>
                        <small class="text-muted">Relatório não enviado · SIMPA UEMA</small>
                    </div>
                    <span class="badge bg-warning text-dark">Pendente</span>
                </div>
                <div class="d-flex align-items-center gap-2 p-2 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                    <img src="https://ui-avatars.com/api/?name=Joao&background=e0f2fe&color=0369a1" class="rounded-circle" width="32">
                    <div class="flex-grow-1">
                        <p class="fw-medium mb-0" style="font-size:0.85rem;">João</p>
                        <small class="text-muted">Doc enviado · aguardando revisão</small>
                    </div>
                    <span class="badge bg-info text-dark">Revisar</span>
                </div>
                <div class="d-flex align-items-center gap-2 p-2 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                    <img src="https://ui-avatars.com/api/?name=Augusto+Nicacio&background=d1fae5&color=065f46" class="rounded-circle" width="32">
                    <div class="flex-grow-1">
                        <p class="fw-medium mb-0" style="font-size:0.85rem;">Augusto</p>
                        <small class="text-muted">Prazo bolsa em 3 dias · Inovação Tec.</small>
                    </div>
                    <span class="badge bg-warning text-dark">Prazo</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FEED DE ATIVIDADE RECENTE + DOCUMENTOS PENDENTES -->
<div class="row g-3">

    <!-- FEED -->
    <div class="col-lg-7">
        <div class="content-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-activity me-2 text-primary"></i>Atividade Recente</h5>
            </div>
            <div class="d-flex flex-column">

                <div class="d-flex gap-3 pb-3" style="border-bottom:1px solid #f1f5f9;">
                    <div style="width:34px;height:34px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-file-earmark-arrow-up text-primary"></i>
                    </div>
                    <div>
                        <p class="mb-0" style="font-size:0.87rem;"><strong>João</strong> enviou o documento <strong>Relatório_Nov2026.pdf</strong></p>
                        <small class="text-muted">Projeto Social · há 20 minutos</small>
                    </div>
                </div>

                <div class="d-flex gap-3 py-3" style="border-bottom:1px solid #f1f5f9;">
                    <div style="width:34px;height:34px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-check2-circle text-success"></i>
                    </div>
                    <div>
                        <p class="mb-0" style="font-size:0.87rem;"><strong>Augusto</strong> concluiu a tarefa <strong>Revisão Metodologia</strong></p>
                        <small class="text-muted">Inovação Tec. · há 1 hora</small>
                    </div>
                </div>

                <div class="d-flex gap-3 py-3" style="border-bottom:1px solid #f1f5f9;">
                    <div style="width:34px;height:34px;border-radius:50%;background:#fef9c3;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-exclamation-circle" style="color:#ca8a04;"></i>
                    </div>
                    <div>
                        <p class="mb-0" style="font-size:0.87rem;"><strong>Aian</strong> não entregou a tarefa <strong>Entrega Parcial</strong> no prazo</p>
                        <small class="text-muted">Projeto Social · há 3 horas</small>
                    </div>
                </div>

                <div class="d-flex gap-3 py-3" style="border-bottom:1px solid #f1f5f9;">
                    <div style="width:34px;height:34px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-file-earmark-arrow-up text-primary"></i>
                    </div>
                    <div>
                        <p class="mb-0" style="font-size:0.87rem;"><strong>Bruno</strong> enviou o documento <strong>Planilha_Horas.xlsx</strong></p>
                        <small class="text-muted">SIMPA UEMA · ontem às 17:42</small>
                    </div>
                </div>

                <div class="d-flex gap-3 pt-3">
                    <div style="width:34px;height:34px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-person-plus text-success"></i>
                    </div>
                    <div>
                        <p class="mb-0" style="font-size:0.87rem;"><strong>Carlos</strong> foi adicionado ao projeto <strong>Inovação Tec.</strong></p>
                        <small class="text-muted">ontem às 14:10</small>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- DOCUMENTOS PENDENTES -->
    <div class="col-lg-5">
        <div class="content-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-check me-2 text-primary"></i>Documentos Pendentes</h5>
                <a href="?page=documentos" class="btn btn-sm btn-outline-primary">Ver todos</a>
            </div>
            <div class="d-flex flex-column gap-2">
                <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <div>
                        <p class="fw-medium mb-0" style="font-size:0.88rem;">Relatório_Nov2026.pdf</p>
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
                <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <div>
                        <p class="fw-medium mb-0" style="font-size:0.88rem;">Formulario_Bolsa.pdf</p>
                        <small class="text-muted">Bruno · SIMPA UEMA</small>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-success" title="Aprovar"><i class="bi bi-check2"></i></button>
                        <button class="btn btn-sm btn-outline-danger" title="Reprovar"><i class="bi bi-x"></i></button>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <div>
                        <p class="fw-medium mb-0" style="font-size:0.88rem;">Relatorio_Atividades.pdf</p>
                        <small class="text-muted">Carlos · Inovação Tec.</small>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {

    // Data de hoje
    const hoje = new Date();
    const opts = { weekday:'long', day:'numeric', month:'long', year:'numeric' };
    document.getElementById('dataHoje').textContent =
        hoje.toLocaleDateString('pt-BR', opts).replace(/^\w/, c => c.toUpperCase());

    // Próximas atividades (hoje e amanhã simulados)
    const atividades = [
        { hora: '09:00', titulo: 'Reunião de Orientação',   projeto: 'Projeto Social', tipo: 'Reunião',  cor: '#8b5cf6', quando: 'Hoje'   },
        { hora: '14:00', titulo: 'Prazo Entrega Relatório', projeto: 'SIMPA UEMA',    tipo: 'Prazo',    cor: '#f59e0b', quando: 'Hoje'   },
        { hora: '10:00', titulo: 'Apresentação Final',      projeto: 'PROEXAE',       tipo: 'Evento',   cor: '#10b981', quando: 'Amanhã' },
        { hora: '15:00', titulo: 'Reunião Acompanhamento',  projeto: 'Inovação Tec.', tipo: 'Reunião',  cor: '#8b5cf6', quando: 'Amanhã' },
    ];

    const cont = document.getElementById('atividadesHoje');
    atividades.forEach(function(a) {
        cont.innerHTML += `
            <div class="d-flex align-items-center gap-2 p-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                <div style="width:4px;height:40px;border-radius:4px;background:${a.cor};flex-shrink:0;"></div>
                <div class="flex-grow-1">
                    <p class="fw-medium mb-0" style="font-size:0.83rem;">${a.titulo}</p>
                    <small class="text-muted">${a.projeto}</small>
                </div>
                <div class="text-end" style="flex-shrink:0;">
                    <div style="font-size:0.78rem;font-weight:600;color:#0F2557;">${a.hora}</div>
                    <small class="text-muted">${a.quando}</small>
                </div>
            </div>`;
    });

    // Gráfico de projetos por tipo
    const ctx = document.getElementById('graficoProjetos').getContext('2d');
    const labels = ['Projeto Especial', 'Ligas Acadêmicas', 'Empresa Jr', 'Atlética'];
    const valores = [2, 1, 1, 1];
    const cores = ['#0F2557', '#3b82f6', '#10b981', '#f59e0b'];

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: valores,
                backgroundColor: cores,
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(c) { return ' ' + c.label + ': ' + c.raw + ' projeto(s)'; }
                    }
                }
            }
        }
    });

    // Legenda manual
    const leg = document.getElementById('legendaGrafico');
    labels.forEach(function(l, i) {
        leg.innerHTML += `<span style="display:flex;align-items:center;gap:4px;">
            <span style="width:10px;height:10px;border-radius:50%;background:${cores[i]};display:inline-block;"></span>
            ${l} (${valores[i]})
        </span>`;
    });

})();
</script>