<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Monitoramento de Acesso</h3>
        <p class="text-muted mb-0">Análise de logins, falhas e atividade de usuários no sistema</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm">Dia</button>
        <button class="btn btn-secondary btn-sm">Mês</button>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle" style="background:#dbeafe;color:#3b82f6;"><i class="bi bi-check-circle-fill"></i></div>
            <div><h4 class="mb-0 fw-bold">18.420</h4><small class="text-muted">Logins com Sucesso</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle" style="background:#fee2e2;color:#ef4444;"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div><h4 class="mb-0 fw-bold">1.250</h4><small class="text-muted">Falhas de Login</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle" style="background:#ede9fe;color:#7c3aed;"><i class="bi bi-people-fill"></i></div>
            <div><h4 class="mb-0 fw-bold">5.602</h4><small class="text-muted">Usuários Ativos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-orange"><i class="bi bi-person-plus-fill"></i></div>
            <div><h4 class="mb-0 fw-bold">432</h4><small class="text-muted">Novos este Mês</small></div>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="fw-bold mb-0">Análise de Acessos</h5>
        <small class="text-muted">Janeiro – Dezembro 2025</small>
    </div>
    <div style="position:relative;height:320px;">
        <canvas id="graficoSeguranca"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('graficoSeguranca').getContext('2d'), {
    type: 'line',
    data: {
        labels: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
        datasets: [
            {
                label: 'Logins com Sucesso',
                data: [1200,1900,1500,2100,2400,2000,1800,2200,2500,2100,2300,2600],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.08)',
                tension: 0.4, fill: true
            },
            {
                label: 'Falhas de Login',
                data: [200,400,350,800,150,200,900,300,200,500,1000,400],
                borderColor: '#ef4444',
                borderDash: [5,5],
                tension: 0.4, fill: false
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
