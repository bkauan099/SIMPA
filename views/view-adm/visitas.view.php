<?php $diasAtivos = $diasAtivos ?? 30; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Monitoramento de Acessos</h3>
        <p class="text-muted mb-0">Análise de logins, falhas e atividade de usuários no sistema</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-sm <?= $diasAtivos == 7  ? 'btn-primary' : 'btn-outline-secondary' ?>"
            onclick="recarregarVisitas(7)">7 dias</button>
        <button class="btn btn-sm <?= $diasAtivos == 30 ? 'btn-primary' : 'btn-outline-secondary' ?>"
            onclick="recarregarVisitas(30)">30 dias</button>
        <button class="btn btn-sm <?= $diasAtivos == 90 ? 'btn-primary' : 'btn-outline-secondary' ?>"
            onclick="recarregarVisitas(90)">90 dias</button>
    </div>
</div>

<!-- CARDS ESTATÍSTICAS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #3b82f6;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#3b82f6;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#3b82f6;opacity:0.1;line-height:1;pointer-events:none;"><i class="bi bi-box-arrow-in-right"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#3b82f6;color:#fff;opacity:0.85;margin-bottom:10px;"><i class="bi bi-box-arrow-in-right"></i> Total de Acessos</div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= number_format($estatisticas['total_acessos']) ?></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #16a34a;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#16a34a;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#16a34a;opacity:0.1;line-height:1;pointer-events:none;"><i class="bi bi-check-circle-fill"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#16a34a;color:#fff;opacity:0.85;margin-bottom:10px;"><i class="bi bi-check-circle-fill"></i> Logins com Sucesso</div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= number_format($estatisticas['acessos_sucesso']) ?></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #dc2626;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#dc2626;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#dc2626;opacity:0.1;line-height:1;pointer-events:none;"><i class="bi bi-x-circle-fill"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#dc2626;color:#fff;opacity:0.85;margin-bottom:10px;"><i class="bi bi-x-circle-fill"></i> Tentativas Falhas</div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= number_format($estatisticas['acessos_falha']) ?></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #ca8a04;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#ca8a04;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#ca8a04;opacity:0.1;line-height:1;pointer-events:none;"><i class="bi bi-people-fill"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#ca8a04;color:#fff;opacity:0.85;margin-bottom:10px;"><i class="bi bi-people-fill"></i> Usuários Únicos</div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= number_format($estatisticas['usuarios_unicos']) ?></div>
        </div>
    </div>
</div>

<!-- GRÁFICO DE LINHA -->
<div class="content-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0">Acessos por Dia</h5>
            <small class="text-muted">Últimos <?= $diasAtivos ?> dias</small>
        </div>
        <div class="d-flex gap-3" style="font-size:.8rem">
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#22c55e;margin-right:4px"></span>Sucesso</span>
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#ef4444;margin-right:4px"></span>Falha</span>
        </div>
    </div>
    <?php if (!empty($graficoDias)): ?>
        <div style="position:relative;height:260px">
            <canvas id="graficoAcessos"></canvas>
        </div>
    <?php else: ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bar-chart-line fs-1 d-block mb-2 opacity-25"></i>
            Nenhum registro de acesso no período selecionado.
        </div>
    <?php endif; ?>
</div>

<!-- FILTROS -->
<div class="content-card mb-4 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filtroBusca" class="form-control border-start-0"
                       placeholder="Buscar por nome ou e-mail" oninput="filtrarAcessos()">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select" id="filtroStatus" onchange="filtrarAcessos()">
                <option value="">Status (Todos)</option>
                <option value="sucesso">Sucesso</option>
                <option value="falha">Falha</option>
            </select>
        </div>
        <div class="col-6 col-md-4">
            <select class="form-select" id="filtroPerfil" onchange="filtrarAcessos()">
                <option value="">Perfil (Todos)</option>
                <option value="admin">Administrador</option>
                <option value="professor_orientador">Professor</option>
                <option value="aluno">Aluno</option>
            </select>
        </div>
    </div>
</div>

<!-- TABELA -->
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Histórico de Acessos</h5>
        <span class="text-muted small" id="contadorAcessos"><?= count($listaAcessos) ?> registros</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="tabelaAcessos">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ID</th><th>USUÁRIO</th><th>E-MAIL USADO</th><th>PERFIL</th><th>DATA / HORA</th><th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaAcessos)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">Nenhum registro no período selecionado.</td></tr>
                <?php else: foreach ($listaAcessos as $a): ?>
                    <?php
                        $statusTxt = strtolower($a['status'] ?? '');
                        $eSucesso  = str_contains($statusTxt, 'sucesso') || str_contains($statusTxt, 'success');
                    ?>
                    <tr data-status="<?= $eSucesso ? 'sucesso' : 'falha' ?>"
                        data-perfil="<?= htmlspecialchars(strtolower($a['usuario_perfil'] ?? '')) ?>"
                        data-busca="<?= htmlspecialchars(strtolower(($a['usuario_nome'] ?? '') . $a['email'])) ?>">
                        <td class="fw-bold text-muted">#<?= $a['id_acesso'] ?></td>
                        <td>
                            <?php if (!empty($a['usuario_nome'])): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($a['usuario_nome']) ?>&background=random&size=28"
                                         class="rounded-circle" width="28" height="28">
                                    <span class="fw-medium small"><?= htmlspecialchars($a['usuario_nome']) ?></span>
                                </div>
                            <?php else: ?>
                                <span class="text-muted fst-italic">Desconhecido</span>
                            <?php endif; ?>
                        </td>
                        <td class="small"><?= htmlspecialchars($a['email']) ?></td>
                        <td>
                            <?php
                                $p = strtolower($a['usuario_perfil'] ?? '');
                                if (str_contains($p, 'admin'))
                                    echo '<span class="badge bg-secondary">Admin</span>';
                                elseif (str_contains($p, 'professor') || str_contains($p, 'orientador'))
                                    echo '<span class="badge bg-info text-dark">Professor</span>';
                                elseif (str_contains($p, 'aluno'))
                                    echo '<span class="badge bg-light text-dark border">Aluno</span>';
                                else
                                    echo '<span class="text-muted">—</span>';
                            ?>
                        </td>
                        <td class="small"><?= $a['data'] ? date('d/m/Y H:i', strtotime($a['data'])) : '—' ?></td>
                        <td>
                            <?php if ($eSucesso): ?>
                                <span class="badge bg-success"><i class="bi bi-check me-1"></i>Sucesso</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><i class="bi bi-x me-1"></i>Falha</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    <?php if (!empty($graficoDias)): ?>
    const labels  = <?= json_encode(array_map(fn($r) => date('d/m', strtotime($r['dia'])), $graficoDias)) ?>;
    const sucesso = <?= json_encode(array_map(fn($r) => (int)$r['sucesso'], $graficoDias)) ?>;
    const falha   = <?= json_encode(array_map(fn($r) => (int)$r['falha'],   $graficoDias)) ?>;

    const ctx = document.getElementById('graficoAcessos');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Logins com sucesso',
                    data: sucesso,
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22, 163, 74, 0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.45,
                    pointBackgroundColor: '#16a34a',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#16a34a',
                    pointHoverBorderWidth: 2,
                },
                {
                    label: 'Tentativas falhas',
                    data: falha,
                    borderColor: '#dc2626',
                    backgroundColor: 'rgba(220, 38, 38, 0.07)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.45,
                    pointBackgroundColor: '#dc2626',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#dc2626',
                    pointHoverBorderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: { display: false }, // legenda customizada no header
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#94a3b8',
                    bodyColor: '#f1f5f9',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        title: (items) => 'Dia: ' + items[0].label,
                        label: (item) => '  ' + item.dataset.label + ': ' + item.formattedValue,
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 11 },
                        maxRotation: 0,
                        // mostrar só alguns labels quando há muitos dias
                        maxTicksLimit: <?= $diasAtivos > 30 ? 12 : ($diasAtivos > 7 ? 10 : 7) ?>,
                    },
                    border: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        color: '#94a3b8',
                        font: { size: 11 },
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)',
                        drawBorder: false,
                    },
                    border: { display: false, dash: [4, 4] }
                }
            }
        }
    });
    <?php endif; ?>

    // ── Filtro da tabela ──────────────────────────────────────
    window.filtrarAcessos = function () {
        const busca  = document.getElementById('filtroBusca').value.toLowerCase();
        const status = document.getElementById('filtroStatus').value;
        const perfil = document.getElementById('filtroPerfil').value.toLowerCase();
        const linhas = document.querySelectorAll('#tabelaAcessos tbody tr[data-status]');
        let visiveis = 0;
        linhas.forEach(tr => {
            const ok = (!busca  || tr.dataset.busca.includes(busca))
                    && (!status || tr.dataset.status === status)
                    && (!perfil || tr.dataset.perfil.includes(perfil));
            tr.style.display = ok ? '' : 'none';
            if (ok) visiveis++;
        });
        document.getElementById('contadorAcessos').textContent = visiveis + ' registros';
    };

    // ── Recarregar período — usa nav() para executar scripts ──
    window.recarregarVisitas = function (dias) {
        // Busca o conteúdo com o novo período e injeta via nav()
        // que já garante execução de scripts
        fetch('pages-adm/visitas.php?dias=' + dias, { cache: 'no-store' })
            .then(r => r.text())
            .then(html => {
                document.querySelectorAll('script[data-din]').forEach(s => s.remove());
                const cont = document.getElementById('conteudo-dinamico');
                const tmp  = document.createElement('div');
                tmp.innerHTML = html;
                const scs = [];
                tmp.querySelectorAll('script').forEach(s => {
                    scs.push({ src: s.src, txt: s.textContent });
                    s.remove();
                });
                cont.innerHTML = tmp.innerHTML;
                function runNext(i) {
                    if (i >= scs.length) return;
                    const ns = document.createElement('script');
                    ns.setAttribute('data-din', '1');
                    if (scs[i].src) {
                        ns.src = scs[i].src;
                        ns.onload  = () => runNext(i + 1);
                        ns.onerror = () => runNext(i + 1);
                        document.body.appendChild(ns);
                    } else {
                        ns.textContent = scs[i].txt;
                        document.body.appendChild(ns);
                        runNext(i + 1);
                    }
                }
                runNext(0);
            })
            .catch(() => alert('Erro ao recarregar.'));
    };

})();
</script>
