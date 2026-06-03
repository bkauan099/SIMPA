<?php
/* ── Dados do calendário (calculados aqui, sem JavaScript) ─────────────── */
$hoje_str = date('Y-m-d');
$sete_str = date('Y-m-d', strtotime('+7 days'));
$mesAtual  = date('Y-m');

$mapa = [];
foreach (array_merge($tarefas, $eventos) as $item) {
    if (!str_starts_with($item['data'], $mesAtual)) continue;
    $d  = (int) date('j', strtotime($item['data']));
    $hr = $item['hora'] ? substr($item['hora'], 0, 5) : '';
    $ok = !empty($item['concluido']);
    $dt = $item['data'];

    if (!isset($mapa[$d])) {
        $mapa[$d] = ['verde'=>false,'vermelho'=>false,'amarelo'=>false,'azul'=>false,'itens'=>[]];
    }
    if ($ok)              { $mapa[$d]['verde']    = true; $ico = '✅'; $status = 'concluido'; }
    elseif ($dt < $hoje_str) { $mapa[$d]['vermelho'] = true; $ico = '❌'; $status = 'atrasado'; }
    elseif ($dt <= $sete_str){ $mapa[$d]['amarelo']  = true; $ico = '⚠️'; $status = 'proximo'; }
    else                  { $mapa[$d]['azul']     = true; $ico = '📌'; $status = 'futuro'; }

    $mapa[$d]['itens'][] = [
        'id'     => $item['id'],
        'titulo' => $item['titulo'],
        'hora'   => $hr,
        'tipo'   => $item['tipo'],
        'ico'    => $ico,
        'status' => $status,
        'data'   => $dt,
    ];
}

$nomes     = ['','Janeiro','Fevereiro','Março','Abril','Maio','Junho',
              'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
$hojeObj   = new DateTime();
$mesNum    = (int) $hojeObj->format('n');
$anoNum    = (int) $hojeObj->format('Y');
$primDia   = (int) (new DateTime("{$anoNum}-{$mesNum}-01"))->format('w');
$diasMes   = (int) $hojeObj->format('t');
$diaHoje   = (int) $hojeObj->format('j');
?>
<div class="container-fluid mt-2">
    <div class="row g-3" style="min-height:0;">

        <!-- ESQUERDA: Tarefas e Eventos -->
        <div class="col-lg-8 d-flex flex-column">
            <div class="card card-custom p-4 flex-fill">
                <h4 class="mb-3">Tarefas e Eventos</h4>

                <div class="d-flex align-items-center gap-2 mb-3 p-3 rounded flex-wrap"
                     style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <i class="bi bi-folder2-open text-primary flex-shrink-0"></i>
                    <?php if (empty($projetosAtivos)): ?>
                        <span class="text-muted" style="font-size:0.9rem;">Nenhum projeto ativo</span>
                    <?php else: ?>
                        <?php foreach ($projetosAtivos as $nomeProjeto): ?>
                            <span class="badge rounded-pill px-3 py-2"
                                  style="background:#eff6ff;color:#1d4ed8;font-size:0.8rem;font-weight:600;">
                                <?= htmlspecialchars($nomeProjeto) ?>
                            </span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php
                function _statusBadge(array $item, string $hoje_str): string {
                    if (!empty($item['concluido'])) {
                        return '<span class="badge bg-success">Concluído</span>';
                    }
                    $passou = $item['data'] < $hoje_str;
                    if (!$passou && !empty($item['hora'])) {
                        $passou = (new DateTime()) > new DateTime($item['data'] . ' ' . substr($item['hora'], 0, 5));
                    }
                    return $passou
                        ? '<span class="badge bg-danger">Não Concluído</span>'
                        : '<span class="badge bg-warning text-dark">Pendente</span>';
                }
                ?>

                <h6><i class="bi bi-list-check"></i> Tarefas</h6>
                <table class="table table-hover mb-0" style="table-layout:fixed;width:100%">
                    <colgroup><col style="width:55%"><col style="width:25%"><col style="width:20%"></colgroup>
                    <thead class="table-primary">
                        <tr>
                            <th>Título</th>
                            <th>Data</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyTarefas">
                        <?php if (empty($tarefas)): ?>
                            <tr><td colspan="3" class="text-muted text-center">Nenhuma tarefa cadastrada.</td></tr>
                        <?php else: ?>
                            <?php foreach ($tarefas as $i => $t): ?>
                                <tr<?= $i >= 3 ? ' class="linha-extra-t" style="display:none"' : '' ?>
                                    style="cursor:pointer;" onclick="carregarPagina('tarefas')" title="Ver em Minhas Tarefas">
                                    <td>
                                        <div class="fw-medium"><?= htmlspecialchars($t['titulo']) ?></div>
                                        <?php if (!empty($t['projeto']) && $t['projeto'] !== '—'): ?>
                                        <span style="font-size:0.7rem;background:#eff6ff;color:#1d4ed8;padding:1px 8px;border-radius:20px;font-weight:600;">
                                            <i class="bi bi-folder2 me-1"></i><?= htmlspecialchars($t['projeto']) ?>
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-size:0.85rem;"><?= date('d/m/Y', strtotime($t['data'])) ?></div>
                                        <?php if ($t['hora']): ?><div class="text-muted" style="font-size:0.75rem;"><?= substr($t['hora'], 0, 5) ?></div><?php endif; ?>
                                    </td>
                                    <td><?= _statusBadge($t, $hoje_str) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if (count($tarefas) > 3): ?>
                <div class="text-center mt-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="toggleMais('tbodyTarefas','linha-extra-t',this)">
                        <i class="bi bi-chevron-down me-1"></i>Mostrar mais (<?= count($tarefas) - 3 ?>)
                    </button>
                </div>
                <?php endif; ?>

                <h6 class="mt-4"><i class="bi bi-calendar-event"></i> Eventos</h6>
                <table class="table table-hover mb-0" style="table-layout:fixed;width:100%">
                    <colgroup><col style="width:55%"><col style="width:25%"><col style="width:20%"></colgroup>
                    <thead class="table-success">
                        <tr>
                            <th>Título</th>
                            <th>Data</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyEventos">
                        <?php if (empty($eventos)): ?>
                            <tr><td colspan="3" class="text-muted text-center">Nenhum evento cadastrado.</td></tr>
                        <?php else: ?>
                            <?php foreach ($eventos as $i => $e): ?>
                                <tr<?= $i >= 3 ? ' class="linha-extra-e" style="display:none"' : '' ?>
                                    style="cursor:pointer;" onclick="carregarPagina('cronograma')" title="Ver no Cronograma">
                                    <td>
                                        <div class="fw-medium"><?= htmlspecialchars($e['titulo']) ?></div>
                                        <?php if (!empty($e['projeto']) && $e['projeto'] !== '—'): ?>
                                        <span style="font-size:0.7rem;background:#f0fdf4;color:#15803d;padding:1px 8px;border-radius:20px;font-weight:600;">
                                            <i class="bi bi-folder2 me-1"></i><?= htmlspecialchars($e['projeto']) ?>
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-size:0.85rem;"><?= date('d/m/Y', strtotime($e['data'])) ?></div>
                                        <?php if ($e['hora']): ?><div class="text-muted" style="font-size:0.75rem;"><?= substr($e['hora'], 0, 5) ?></div><?php endif; ?>
                                    </td>
                                    <td><?= _statusBadge($e, $hoje_str) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if (count($eventos) > 3): ?>
                <div class="text-center mt-2">
                    <button class="btn btn-sm btn-outline-success" onclick="toggleMais('tbodyEventos','linha-extra-e',this)">
                        <i class="bi bi-chevron-down me-1"></i>Mostrar mais (<?= count($eventos) - 3 ?>)
                    </button>
                </div>
                <?php endif; ?>

                <script>
                function toggleMais(tbodyId, cls, btn) {
                    const linhas = document.querySelectorAll('#' + tbodyId + ' .' + cls);
                    const aberto = linhas[0]?.style.display !== 'none';
                    linhas.forEach(l => l.style.display = aberto ? 'none' : '');
                    btn.innerHTML = aberto
                        ? '<i class="bi bi-chevron-down me-1"></i>Mostrar mais (' + linhas.length + ')'
                        : '<i class="bi bi-chevron-up me-1"></i>Mostrar menos';
                }
                </script>
            </div>
        </div>

        <!-- DIREITA: Calendário + Carga Horária -->
        <div class="col-lg-4 d-flex flex-column gap-3">

            <!-- CALENDÁRIO — gerado em PHP, sem depender de JS -->
            <div class="card card-custom p-3">
                <h6 class="text-center mb-2"><?= $nomes[$mesNum] . ' ' . $anoNum ?></h6>
                <div class="d-flex text-center fw-bold mb-1" style="font-size:0.75rem;">
                    <div class="w-100">Dom</div><div class="w-100">Seg</div><div class="w-100">Ter</div>
                    <div class="w-100">Qua</div><div class="w-100">Qui</div><div class="w-100">Sex</div>
                    <div class="w-100">Sáb</div>
                </div>
                <div class="d-flex flex-wrap" id="calendar">
                    <?php for ($i = 0; $i < $primDia; $i++): ?>
                        <div class="calendar-day"></div>
                    <?php endfor; ?>
                    <?php for ($d = 1; $d <= $diasMes; $d++):
                        $cats      = $mapa[$d] ?? null;
                        $isHoje    = ($d === $diaHoje);
                        $itens     = $cats ? $cats['itens'] : [];
                        $itensJson = htmlspecialchars(
                            json_encode($itens, JSON_UNESCAPED_UNICODE),
                            ENT_QUOTES
                        );
                        $titulo = $d . ' de ' . $nomes[$mesNum] . ' de ' . $anoNum;
                    ?>
                        <div class="calendar-day<?= $isHoje ? ' today' : '' ?>"
                             onclick="showDay(this)"
                             data-titulo="<?= htmlspecialchars($titulo, ENT_QUOTES) ?>"
                             data-itens="<?= $itensJson ?>">
                            <span><?= $d ?></span>
                            <?php if ($cats): ?>
                            <div class="dots-row">
                                <?php if ($cats['verde']):    ?><span class="dot dot-verde"></span><?php endif; ?>
                                <?php if ($cats['vermelho']): ?><span class="dot dot-vermelho"></span><?php endif; ?>
                                <?php if ($cats['amarelo']):  ?><span class="dot dot-amarelo"></span><?php endif; ?>
                                <?php if ($cats['azul']):     ?><span class="dot dot-azul"></span><?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
                <div class="cal-legenda">
                    <div class="cal-legenda-item"><span class="cal-legenda-dot" style="background:#22c55e"></span>Concluída</div>
                    <div class="cal-legenda-item"><span class="cal-legenda-dot" style="background:#ef4444"></span>Não concluída</div>
                    <div class="cal-legenda-item"><span class="cal-legenda-dot" style="background:#eab308"></span>Próxima (≤7d)</div>
                    <div class="cal-legenda-item"><span class="cal-legenda-dot" style="background:#3b82f6"></span>Baixa prioridade</div>
                </div>
            </div>

            <!-- CARGA HORÁRIA -->
            <div class="card card-custom p-3 text-center flex-fill d-flex flex-column justify-content-center">
                <h6><i class="bi bi-clock"></i> Carga Horária</h6>
                <h2 class="mb-0"><?= $cargaHoraria ?>h</h2>
                <small class="text-muted">Acumuladas</small>
            </div>

        </div>
    </div>
</div>

<!-- Modal do dia selecionado -->
<div class="modal fade" id="modalDia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-semibold" id="modalDiaTitulo"></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body pt-2" id="modalDiaLista"></div>
        </div>
    </div>
</div>
