<?php
// helpers de formatação usados em toda a view
function tempoRelativo($dataStr) {
    if (!$dataStr) return '';
    $diff = time() - strtotime($dataStr);
    if ($diff < 60)   return 'agora mesmo';
    if ($diff < 3600) return floor($diff / 60) . ' min atrás';
    if ($diff < 86400) return floor($diff / 3600) . ' h atrás';
    return floor($diff / 86400) . ' dias atrás';
}
function corTipo($tipo) {
    $cores = ['Reunião'=>'#8b5cf6','Prazo'=>'#f59e0b','Entrega'=>'#3b82f6','Evento'=>'#10b981','Tarefa'=>'#64748b'];
    return $cores[$tipo] ?? '#64748b';
}
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Página Inicial</h3>
        <p class="text-muted mb-0" id="dataHoje"></p>
    </div>
</div>

<!-- ===== CARDS TOPO ===== -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-blue">
            <div class="sc-watermark"><i class="bi bi-folder-fill"></i></div>
            <div class="sc-label"><i class="bi bi-folder-fill"></i> Projetos Ativos</div>
            <div class="sc-number"><?= (int)$estatisticas['ativos'] ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-purple">
            <div class="sc-watermark"><i class="bi bi-people"></i></div>
            <div class="sc-label"><i class="bi bi-people"></i> Alunos Orientados</div>
            <div class="sc-number"><?= (int)$estatisticas['alunos'] ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-yellow">
            <div class="sc-watermark"><i class="bi bi-file-earmark-check"></i></div>
            <div class="sc-label"><i class="bi bi-file-earmark-check"></i> Docs Pendentes</div>
            <div class="sc-number"><?= (int)$estatisticas['docs_pendentes'] ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-red">
            <div class="sc-watermark"><i class="bi bi-alarm"></i></div>
            <div class="sc-label"><i class="bi bi-alarm"></i> Tarefas Vencendo</div>
            <div class="sc-number"><?= (int)$estatisticas['tarefas_vencendo'] ?></div>
        </div>
    </div>
</div>

<!-- ===== LINHA DO MEIO ===== -->
<div class="row g-3 mb-3">

    <!-- HOJE & AMANHÃ -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-calendar-check me-2 text-primary"></i>Hoje &amp; Amanhã</h5>
                <a href="?page=cronograma" class="btn btn-sm btn-outline-primary">Ver agenda</a>
            </div>
            <div class="d-flex flex-column gap-2">
                <?php if (empty($agenda)): ?>
                    <p class="text-muted text-center py-3" style="font-size:0.85rem;">Nenhum evento para hoje ou amanhã.</p>
                <?php else: ?>
                    <?php
                    $hoje_str   = date('Y-m-d');
                    $amanha_str = date('Y-m-d', strtotime('+1 day'));
                    foreach ($agenda as $ev):
                        $cor    = corTipo($ev['tipo']);
                        $quando = ($ev['data'] === $hoje_str) ? 'Hoje' : 'Amanhã';
                        $hora   = $ev['hora'] ? substr($ev['hora'], 0, 5) : '—';
                    ?>
                    <div class="d-flex align-items-center gap-2 p-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                        <div style="width:4px;height:40px;border-radius:4px;background:<?= $cor ?>;flex-shrink:0;"></div>
                        <div class="flex-grow-1" style="min-width:0;">
                            <p class="fw-medium mb-0 text-truncate" style="font-size:0.83rem;"><?= htmlspecialchars($ev['titulo']) ?></p>
                            <small class="text-muted text-truncate d-block"><?= htmlspecialchars($ev['nome_projeto'] ?? '—') ?></small>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div style="font-size:0.78rem;font-weight:600;color:#0F2557;"><?= $hora ?></div>
                            <small class="text-muted"><?= $quando ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div></div>
    </div>

    <!-- PROJETOS POR TIPO -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <h5 class="fw-bold mb-1"><i class="bi bi-pie-chart me-2 text-primary"></i>Projetos por Tipo</h5>
            <p class="text-muted mb-3" style="font-size:0.8rem;">Distribuição dos seus projetos ativos</p>
            <div id="graficoWrap" style="position:relative;">
                <canvas id="graficoProjetos" height="180"></canvas>
                <div id="graficoVazio" class="d-none d-flex flex-column align-items-center justify-content-center py-4 text-muted">
                    <i class="bi bi-pie-chart" style="font-size:2.5rem;opacity:0.3;"></i>
                    <p class="mt-2 mb-0" style="font-size:0.85rem;">Nenhum projeto encontrado.</p>
                </div>
            </div>
            <div id="legendaGrafico" class="d-flex flex-wrap gap-2 mt-3 justify-content-center" style="font-size:0.78rem;"></div>
        </div></div>
    </div>

    <!-- ATENÇÃO NECESSÁRIA -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <h5 class="fw-bold mb-3"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Atenção Necessária</h5>
            <div class="d-flex flex-column gap-2">
                <?php if (empty($atencaoNecessaria)): ?>
                    <div class="d-flex flex-column align-items-center py-3 text-muted">
                        <i class="bi bi-check-circle" style="font-size:2rem;color:#10b981;opacity:0.7;"></i>
                        <p class="mt-2 mb-0" style="font-size:0.85rem;">Tudo em dia! Nenhuma tarefa atrasada.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($atencaoNecessaria as $al):
                        $qtd = (int)$al['total_atrasadas'];
                        $urgente = ($qtd >= 3 || $al['max_prioridade'] === 'alta');
                    ?>
                    <div class="d-flex align-items-center gap-2 p-2 rounded" style="background:<?= $urgente ? '#fff7ed' : '#f8fafc' ?>;border:1px solid <?= $urgente ? '#fed7aa' : '#e2e8f0' ?>;">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($al['nome']) ?>&background=ede9fe&color=6d28d9&size=32"
                             class="rounded-circle flex-shrink-0" width="32" height="32" alt="">
                        <div class="flex-grow-1" style="min-width:0;">
                            <p class="fw-medium mb-0 text-truncate" style="font-size:0.85rem;"><?= htmlspecialchars($al['nome']) ?></p>
                            <small class="text-muted"><?= $qtd ?> tarefa<?= $qtd > 1 ? 's' : '' ?> atrasada<?= $qtd > 1 ? 's' : '' ?></small>
                        </div>
                        <?php if ($urgente): ?>
                            <span class="badge bg-danger-subtle text-danger fw-semibold flex-shrink-0">Urgente</span>
                        <?php else: ?>
                            <span class="badge bg-warning-subtle text-warning fw-semibold flex-shrink-0">Atenção</span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div></div>
    </div>
</div>

<!-- ===== LINHA INFERIOR ===== -->
<div class="row g-3">

    <!-- ATIVIDADE RECENTE -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-activity me-2 text-primary"></i>Atividade Recente</h5>
            </div>
            <div class="d-flex flex-column">
                <?php if (empty($atividadeRecente)): ?>
                    <p class="text-muted text-center py-4" style="font-size:0.85rem;">Nenhuma atividade recente.</p>
                <?php else: ?>
                    <?php
                    $statusIcone = [
                        'pendente'  => ['bi-file-earmark-arrow-up','text-warning'],
                        'ativo'     => ['bi-file-earmark-check','text-success'],
                        'inativo'   => ['bi-file-earmark-x','text-danger'],
                        'concluido' => ['bi-file-earmark-check-fill','text-primary'],
                    ];
                    foreach ($atividadeRecente as $i => $ativ):
                        $icone = $statusIcone[$ativ['status']] ?? ['bi-file-earmark','text-muted'];
                        $borda = ($i < count($atividadeRecente) - 1) ? 'border-bottom:1px solid #f1f5f9;' : '';
                    ?>
                    <div class="d-flex gap-3 pb-3 mb-1" style="<?= $borda ?>">
                        <div style="width:34px;height:34px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi <?= $icone[0] ?> <?= $icone[1] ?>"></i>
                        </div>
                        <div style="min-width:0;">
                            <p class="mb-0" style="font-size:0.87rem;">
                                <strong><?= htmlspecialchars($ativ['nome_aluno'] ?? 'Aluno') ?></strong>
                                enviou o documento
                                <strong><?= htmlspecialchars($ativ['nome_arquivo']) ?></strong>
                            </p>
                            <small class="text-muted"><?= htmlspecialchars($ativ['nome_projeto'] ?? '—') ?> · <?= tempoRelativo($ativ['data_registro']) ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div></div>
    </div>

    <!-- DOCUMENTOS PENDENTES -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100"><div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-check me-2 text-primary"></i>Documentos Pendentes</h5>
                <a href="?page=documentos" class="btn btn-sm btn-outline-primary">Ver todos</a>
            </div>
            <div class="d-flex flex-column gap-2" id="listaPendentes">
                <?php if (empty($documentosPendentes)): ?>
                    <div class="d-flex flex-column align-items-center py-3 text-muted">
                        <i class="bi bi-check2-all" style="font-size:2rem;color:#10b981;opacity:0.7;"></i>
                        <p class="mt-2 mb-0" style="font-size:0.85rem;">Nenhum documento pendente.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($documentosPendentes as $doc): ?>
                    <div class="d-flex justify-content-between align-items-center p-2 rounded doc-item" data-id="<?= (int)$doc['id_producao'] ?>" style="background:#f8fafc;border:1px solid #e2e8f0;">
                        <div style="min-width:0;flex:1;">
                            <p class="fw-medium mb-0 text-truncate" style="font-size:0.88rem;"><?= htmlspecialchars($doc['nome_arquivo']) ?></p>
                            <small class="text-muted text-truncate d-block"><?= htmlspecialchars($doc['nome_aluno'] ?? '—') ?> · <?= htmlspecialchars($doc['nome_projeto'] ?? '—') ?></small>
                        </div>
                        <div class="d-flex gap-1 flex-shrink-0 ms-2">
                            <button class="btn btn-sm btn-outline-success btn-aprovar" data-id="<?= (int)$doc['id_producao'] ?>" title="Aprovar">
                                <i class="bi bi-check2"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-reprovar" data-id="<?= (int)$doc['id_producao'] ?>" title="Reprovar">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div></div>
    </div>
</div>

<!-- ===== SCRIPTS ===== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    // Data de hoje
    const hoje = new Date();
    document.getElementById('dataHoje').textContent = hoje.toLocaleDateString('pt-BR', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    }).replace(/^\w/, c => c.toUpperCase());

    // Gráfico de rosca
    const dadosGrafico = <?= json_encode(array_values($distribuicaoTipos)) ?>;
    const canvasEl = document.getElementById('graficoProjetos');
    const vazioEl  = document.getElementById('graficoVazio');

    if (dadosGrafico.length === 0) {
        if (canvasEl) canvasEl.style.display = 'none';
        if (vazioEl)  { vazioEl.classList.remove('d-none'); vazioEl.classList.add('d-flex'); }
    } else {
        const labels  = dadosGrafico.map(d => d.nome);
        const valores = dadosGrafico.map(d => parseInt(d.total));
        const cores   = ['#0F2557','#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4'];

        if (canvasEl) {
            new Chart(canvasEl.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: valores,
                        backgroundColor: cores.slice(0, labels.length),
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
                                label: c => ` ${c.label}: ${c.raw} projeto${c.raw !== 1 ? 's' : ''}`
                            }
                        }
                    }
                }
            });

            const leg = document.getElementById('legendaGrafico');
            labels.forEach((l, i) => {
                leg.innerHTML += `<span style="display:flex;align-items:center;gap:4px;">
                    <span style="width:10px;height:10px;border-radius:50%;background:${cores[i]};display:inline-block;"></span>
                    ${l} (${valores[i]})
                </span>`;
            });
        }
    }

    // Aprovar / Reprovar documentos
    document.querySelectorAll('.btn-aprovar, .btn-reprovar').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id     = this.dataset.id;
            const status = this.classList.contains('btn-aprovar') ? 'concluido' : 'cancelado';
            const item   = document.querySelector('.doc-item[data-id="' + id + '"]');

            btn.disabled = true;

            fetch('controllers/controller-professor/atualizar-status-doc.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id_documento=' + id + '&status=' + status
            })
            .then(r => r.json())
            .then(data => {
                if (data.sucesso) {
                    if (item) {
                        item.style.transition = 'opacity 0.3s';
                        item.style.opacity = '0';
                        setTimeout(() => {
                            item.remove();
                            // Se não restar itens, mostra mensagem
                            const lista = document.getElementById('listaPendentes');
                            if (lista && lista.querySelectorAll('.doc-item').length === 0) {
                                lista.innerHTML = `<div class="d-flex flex-column align-items-center py-3 text-muted">
                                    <i class="bi bi-check2-all" style="font-size:2rem;color:#10b981;opacity:0.7;"></i>
                                    <p class="mt-2 mb-0" style="font-size:0.85rem;">Nenhum documento pendente.</p>
                                </div>`;
                            }
                        }, 300);
                    }
                } else {
                    alert('Erro ao atualizar: ' + (data.mensagem || 'tente novamente'));
                    btn.disabled = false;
                }
            })
            .catch(() => {
                alert('Falha na comunicação com o servidor.');
                btn.disabled = false;
            });
        });
    });
})();
</script>
