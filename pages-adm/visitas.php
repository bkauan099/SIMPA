<style>
        
        
        /* Cores dos Cartões */
        .card-sucesso { background-color: #3895f4; color: white; } /* Azul */
        .card-falha { background-color: #e55353; color: white; }   /* Vermelho (Alerta) */
        .card-ativos { background-color: #6259ca; color: white; }  /* Roxo */
        .card-novos { background-color: #f7b731; color: white; }   /* Amarelo */

        .cartao-estatistica {
            border: none;
            border-radius: 12px;
            padding: 20px;
            transition: transform 0.2s;
            min-height: 130px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        /* Ajuste para o gráfico não quebrar em telas pequenas */
        .container-grafico {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-top: 20px;
            position: relative;
            height: 60vh; /* Altura relativa à tela */
            min-height: 300px;
        }

        .titulo-secao {
            font-size: 1.2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container-fluid p-4">
    
    <h2 class="titulo-secao text-uppercase mb-4"><i class="bi bi-shield-lock-fill me-2"></i>Monitoramento de Acesso</h2>

    <div class="row g-3">
        
        <div class="col-12 col-md-6 col-lg-3">
            <div class="cartao-estatistica card-sucesso">
                <div class="d-flex justify-content-between">
                    <span>LOGINS COM SUCESSO</span>
                    <i class="bi bi-check-circle"></i>
                </div>
                <h2 class="fw-bold mt-2">18.420</h2>
                <small>Tentativas legítimas</small>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="cartao-estatistica card-falha">
                <div class="d-flex justify-content-between">
                    <span>FALHAS DE LOGIN</span>
                    <i class="bi bi-exclamtion-triangle-fill"></i>
                </div>
                <h2 class="fw-bold mt-2">1.250</h2>
                <small class="text-white-50">Possíveis tentativas de invasão</small>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="cartao-estatistica card-ativos">
                <div class="d-flex justify-content-between">
                    <span>USUÁRIOS ATIVOS</span>
                    <i class="bi bi-people"></i>
                </div>
                <h2 class="fw-bold mt-2">5.602</h2>
                <small>Navegando agora</small>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="cartao-estatistica card-novos">
                <div class="d-flex justify-content-between">
                    <span>NOVOS USUÁRIOS</span>
                    <i class="bi bi-person-plus"></i>
                </div>
                <h2 class="fw-bold mt-2">432</h2>
                <small>Cadastrados este mês</small>
            </div>
        </div>

    </div>

    <div class="container-grafico mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0 fw-bold">Análise de Acessos</h5>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary">Dia</button>
                <button class="btn btn-secondary">Mês</button>
            </div>
        </div>
        <canvas id="graficoSeguranca"></canvas>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('graficoSeguranca').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            datasets: [
                {
                    label: 'Sucessos',
                    data: [1200, 1900, 1500, 2100, 2400, 2000, 1800, 2200, 2500, 2100, 2300, 2600],
                    borderColor: '#3895f4',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(56, 149, 244, 0.1)'
                },
                {
                    label: 'Tentativas de Invasão (Falhas)',
                    data: [200, 400, 350, 800, 150, 200, 900, 300, 200, 500, 1000, 400], // Picos altos podem indicar brute force
                    borderColor: '#e55353',
                    borderDash: [5, 5], // Linha tracejada para alertas
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Isso garante a responsividade em celulares
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

