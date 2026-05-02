<?php $diasAtivos = $diasAtivos ?? 30; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Monitoramento de Acessos</h3>
        <p class="text-muted mb-0">Análise de logins, falhas e atividade de usuários no sistema</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-sm <?= $diasAtivos == 7  ? 'btn-primary' : 'btn-outline-secondary' ?>" onclick="recarregarVisitas(7)">7 dias</button>
        <button class="btn btn-sm <?= $diasAtivos == 30 ? 'btn-primary' : 'btn-outline-secondary' ?>" onclick="recarregarVisitas(30)">30 dias</button>
        <button class="btn btn-sm <?= $diasAtivos == 90 ? 'btn-primary' : 'btn-outline-secondary' ?>" onclick="recarregarVisitas(90)">90 dias</button>
    </div>
</div>

<!-- CARDS ESTATÍSTICAS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle bg-light-blue"><i class="bi bi-box-arrow-in-right"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= number_format($estatisticas['total_acessos']) ?></h4><small class="text-muted">Total de Acessos</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle" style="background:#dcfce7;color:#16a34a;"><i class="bi bi-check-circle-fill"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= number_format($estatisticas['acessos_sucesso']) ?></h4><small class="text-muted">Logins com Sucesso</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle" style="background:#fee2e2;color:#dc2626;"><i class="bi bi-x-circle-fill"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= number_format($estatisticas['acessos_falha']) ?></h4><small class="text-muted">Tentativas Falhas</small></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-circle" style="background:#fef9c3;color:#ca8a04;"><i class="bi bi-people-fill"></i></div>
            <div><h4 class="mb-0 fw-bold"><?= number_format($estatisticas['usuarios_unicos']) ?></h4><small class="text-muted">Usuários Únicos</small></div>
        </div>
    </div>
</div>

<!-- GRÁFICO -->
<div class="content-card mb-4">
    <h5 class="fw-bold mb-3">Acessos por Dia — Últimos <?= $diasAtivos ?> dias</h5>
    <?php if (!empty($graficoDias)): ?>
        <canvas id="graficoAcessos" height="90"></canvas>
    <?php else: ?>
        <p class="text-muted text-center py-3">Nenhum dado de acesso no período selecionado.</p>
    <?php endif; ?>
</div>

<!-- FILTROS -->
<div class="content-card mb-4 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filtroBusca" class="form-control border-start-0" placeholder="Buscar por nome ou e-mail" oninput="filtrarAcessos()">
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
                    <tr data-status="<?= htmlspecialchars($a['status']) ?>"
                        data-perfil="<?= htmlspecialchars(strtolower($a['usuario_perfil'] ?? '')) ?>"
                        data-busca="<?= htmlspecialchars(strtolower(($a['usuario_nome'] ?? '') . $a['email'])) ?>">
                        <td class="fw-bold text-muted">#<?= $a['id_acesso'] ?></td>
                        <td>
                            <?php if (!empty($a['usuario_nome'])): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($a['usuario_nome']) ?>&background=random&size=28" class="rounded-circle" width="28" height="28">
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
                                if (str_contains($p, 'admin'))      echo '<span class="badge bg-secondary">Admin</span>';
                                elseif (str_contains($p, 'professor') || str_contains($p, 'orientador')) echo '<span class="badge bg-info text-dark">Professor</span>';
                                elseif (str_contains($p, 'aluno'))  echo '<span class="badge bg-light text-dark border">Aluno</span>';
                                else echo '<span class="text-muted">—</span>';
                            ?>
                        </td>
                        <td class="small"><?= $a['data'] ? date('d/m/Y H:i', strtotime($a['data'])) : '—' ?></td>
                        <td>
                            <?php if ($a['status'] === 'sucesso'): ?>
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
<?php if (!empty($graficoDias)): ?>
(function() {
    const labels  = <?= json_encode(array_map(fn($r) => date('d/m', strtotime($r['dia'])), $graficoDias)) ?>;
    const sucesso = <?= json_encode(array_map(fn($r) => (int)$r['sucesso'], $graficoDias)) ?>;
    const falha   = <?= json_encode(array_map(fn($r) => (int)$r['falha'],   $graficoDias)) ?>;

    const ctx = document.getElementById('graficoAcessos');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Sucesso', data: sucesso, backgroundColor: '#22c55e', borderRadius: 4 },
                    { label: 'Falha',   data: falha,   backgroundColor: '#ef4444', borderRadius: 4 }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }
})();
<?php endif; ?>

function filtrarAcessos() {
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
}

function recarregarVisitas(dias) {
    fetch('pages-adm/visitas.php?dias=' + dias, { cache: 'no-store' })
        .then(r => r.text())
        .then(html => {
            const el = document.getElementById('conteudo-dinamico');
            if (el) el.innerHTML = html;
        })
        .catch(() => alert('Erro ao recarregar.'));
}
</script>
