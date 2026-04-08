<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Página Inicial</h3>
        <p class="text-muted mb-0" id="dataHoje"></p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-folder-fill"></i></div>
            <div>
                <h4 class="mb-0 fw-bold"><?= $estatisticas['ativos'] ?></h4><small class="text-muted">Projetos Ativos</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-people"></i></div>
            <div>
                <h4 class="mb-0 fw-bold"><?= $estatisticas['alunos'] ?></h4><small class="text-muted">Alunos Orientados</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-file-earmark-check"></i></div>
            <div>
                <h4 class="mb-0 fw-bold">5</h4><small class="text-muted">Docs Pendentes</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-alarm"></i></div>
            <div>
                <h4 class="mb-0 fw-bold">3</h4><small class="text-muted">Tarefas Vencendo</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-4">
        <div class="content-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-calendar-check me-2 text-primary"></i>Hoje & Amanhã</h5>
                <a href="?page=cronograma" class="btn btn-sm btn-outline-primary">Ver agenda</a>
            </div>
            <div class="d-flex flex-column gap-2">
                <div class="d-flex align-items-center gap-2 p-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <div style="width:4px;height:40px;border-radius:4px;background:#8b5cf6;flex-shrink:0;"></div>
                    <div class="flex-grow-1">
                        <p class="fw-medium mb-0" style="font-size:0.83rem;">Reunião de Orientação</p>
                        <small class="text-muted">Projeto Social</small>
                    </div>
                    <div class="text-end">
                        <div style="font-size:0.78rem;font-weight:600;color:#0F2557;">09:00</div>
                        <small class="text-muted">Hoje</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="content-card h-100">
            <h5 class="fw-bold mb-1"><i class="bi bi-pie-chart me-2 text-primary"></i>Projetos por Tipo</h5>
            <p class="text-muted mb-3" style="font-size:0.8rem;">Distribuição dos seus projetos ativos</p>
            <canvas id="graficoProjetos" height="200"></canvas>
            <div id="legendaGrafico" class="d-flex flex-wrap gap-2 mt-3 justify-content-center" style="font-size:0.78rem;"></div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="content-card h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Atenção Necessária</h5>
            <div class="d-flex flex-column gap-2">
                <div class="d-flex align-items-center gap-2 p-2 rounded" style="background:#fff7ed;border:1px solid #fed7aa;">
                    <img src="https://ui-avatars.com/api/?name=Aian&background=ede9fe&color=6d28d9" class="rounded-circle" width="32">
                    <div class="flex-grow-1">
                        <p class="fw-medium mb-0" style="font-size:0.85rem;">Aian</p>
                        <small class="text-muted">2 tarefas atrasadas</small>
                    </div>
                    <span class="badge bg-danger">Urgente</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row g-3">

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
            </div>
        </div>
    </div>

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
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    (function() {
        // Data de hoje (JavaScript)
        const hoje = new Date();
        document.getElementById('dataHoje').textContent = hoje.toLocaleDateString('pt-BR', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }).replace(/^\w/, c => c.toUpperCase());

        // Dados injetados pelo PHP para o Chart.js
        const dadosGrafico = <?= json_encode($distribuicaoTipos) ?>;
        const labels = dadosGrafico.map(d => d.nome);
        const valores = dadosGrafico.map(d => d.total);
        const cores = ['#0F2557', '#3b82f6', '#10b981', '#f59e0b'];

        const ctx = document.getElementById('graficoProjetos').getContext('2d');
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
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Legenda manual dinâmica
        const leg = document.getElementById('legendaGrafico');
        labels.forEach((l, i) => {
            leg.innerHTML += `<span style="display:flex;align-items:center;gap:4px;"><span style="width:10px;height:10px;border-radius:50%;background:${cores[i]};display:inline-block;"></span>${l} (${valores[i]})</span>`;
        });
    })();
</script>