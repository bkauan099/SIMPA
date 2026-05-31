<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Cronograma</h3>
        <p class="text-muted mb-0">Tarefas e eventos recentes e futuros</p>
    </div>
</div>


<?php
    $hojeRef = new DateTime(); $hojeRef->setTime(0,0,0);
    $diaSem  = (int)$hojeRef->format('w'); // 0=Dom … 6=Sáb
    $domingo = (clone $hojeRef)->modify('-' . $diaSem . ' days');
    $sabado  = (clone $domingo)->modify('+6 days');
    $nomesDias = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];

    // Agrupa itens por data
    $itensPorDia = [];
    foreach ($itens as $it) { $itensPorDia[$it['data']][] = $it; }
?>
<div class="content-card mb-4 p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-calendar-week me-2 text-primary"></i>Semana Atual</h6>
        <span class="text-muted small"><?= $domingo->format('d/m') ?> – <?= $sabado->format('d/m/Y') ?></span>
    </div>
    <div class="row g-2">
        <?php for ($i = 0; $i < 7; $i++):
            $dia     = (clone $domingo)->modify('+' . $i . ' days');
            $dataKey = $dia->format('Y-m-d');
            $isHoje  = ($dia == $hojeRef);
            $itensNoDia = $itensPorDia[$dataKey] ?? [];
        ?>
        <div class="col">
            <div style="border-radius:10px;padding:8px 6px;min-height:80px;
                        background:<?= $isHoje ? '#eff6ff' : '#f8fafc' ?>;
                        border:1.5px solid <?= $isHoje ? '#3b82f6' : '#e2e8f0' ?>;">
                <div class="text-center mb-2">
                    <div style="font-size:0.68rem;color:#94a3b8;font-weight:700;text-transform:uppercase;">
                        <?= $nomesDias[$i] ?>
                    </div>
                    <div style="font-size:1.05rem;font-weight:800;line-height:1;
                                color:<?= $isHoje ? '#3b82f6' : '#1e293b' ?>;">
                        <?= $dia->format('d') ?>
                    </div>
                </div>
                <?php foreach ($itensNoDia as $it):
                    if ($it['concluido']) { $cor = '#16a34a'; }
                    elseif ($dia < $hojeRef) { $cor = '#ef4444'; }
                    else { $cor = '#f59e0b'; }
                    $icon = $it['tipo'] === 'tarefa' ? 'bi-check2-square' : 'bi-calendar-event';
                ?>
                <div title="<?= htmlspecialchars($it['titulo']) ?>"
                     style="font-size:0.63rem;background:<?= $cor ?>18;border-left:2px solid <?= $cor ?>;
                            padding:2px 4px;border-radius:3px;margin-bottom:2px;
                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis;cursor:default;">
                    <i class="bi <?= $icon ?>" style="color:<?= $cor ?>;"></i>
                    <?= htmlspecialchars(mb_strimwidth($it['titulo'], 0, 14, '…')) ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endfor; ?>
    </div>
</div>

<div class="content-card mb-3 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filtroBusca" class="form-control border-start-0" placeholder="Buscar por título..." oninput="filtrarCronograma()">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select" id="filtroTipo" onchange="filtrarCronograma()">
                <option value="">Tipo (Todos)</option>
                <option value="tarefa">Tarefa</option>
                <option value="evento">Evento</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select class="form-select" id="filtroStatus" onchange="filtrarCronograma()">
                <option value="">Status (Todos)</option>
                <option value="proximo">Pendente</option>
                <option value="nao_concluido">Não Concluído</option>
                <option value="concluido">Concluído</option>
            </select>
        </div>
        <div class="col-12 text-muted small text-center text-md-start" id="contadorItens">
            <?= count($itens) ?> resultado(s)
        </div>
    </div>
</div>

<div class="content-card">
    <h5 class="fw-bold mb-3">Atividades Recentes e Futuras</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle w-100" id="tabelaCronograma">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>DATA</th>
                    <th>HORA</th>
                    <th>TÍTULO</th>
                    <th>PROJETO</th>
                    <th>TIPO</th>
                    <th>STATUS</th>
                    <th class="text-center">AÇÃO</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($itens)): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">Nenhuma atividade encontrada.</td></tr>
                <?php else: ?>
                    <?php foreach ($itens as $item):
                        $hoje = new DateTime(); $hoje->setTime(0,0,0);
                        $prazo = new DateTime($item['data']);
                        if ($item['concluido']) {
                            $statusKey = 'concluido'; $statusLabel = 'Concluído'; $statusClass = 'bg-success text-white';
                        } elseif ($prazo < $hoje) {
                            $statusKey = 'nao_concluido'; $statusLabel = 'Não Concluído'; $statusClass = 'bg-danger text-white';
                        } else {
                            $statusKey = 'proximo'; $statusLabel = 'Pendente'; $statusClass = 'bg-warning text-dark';
                        }
                        $descricao  = $item['descricao'] ?? '';
                        $mesDaLinha = date('Y-m', strtotime($item['data']));
                        $arquivosJson = $item['arquivos'] ?? '[]';
                        $arquivosArr  = json_decode($arquivosJson, true) ?: [];
                        $temArquivo   = !empty($arquivosArr);
                        $prazoPassou  = $prazo < $hoje;
                        if (!$prazoPassou && !empty($item['hora'])) {
                            $agora = new DateTime();
                            $prazoComHora = new DateTime($item['data'] . ' ' . substr($item['hora'], 0, 5));
                            $prazoPassou  = $agora > $prazoComHora;
                        }
                    ?>
                        <tr style="cursor:pointer;"
                            onclick="abrirDetalheCronograma(this)"
                            data-tipo="<?= htmlspecialchars($item['tipo']) ?>"
                            data-status="<?= $statusKey ?>"
                            data-mes="<?= $mesDaLinha ?>"
                            data-busca="<?= htmlspecialchars(strtolower($item['titulo'] . ' ' . $descricao)) ?>"
                            data-id="<?= htmlspecialchars($item['id']) ?>"
                            data-data="<?= htmlspecialchars($item['data']) ?>"
                            data-titulo="<?= htmlspecialchars($item['titulo'], ENT_QUOTES) ?>"
                            data-concluido="<?= $item['concluido'] ? '1' : '0' ?>"
                            data-arquivos="<?= htmlspecialchars($arquivosJson, ENT_QUOTES) ?>"
                            data-descricao="<?= htmlspecialchars($descricao, ENT_QUOTES) ?>"
                            data-projeto="<?= htmlspecialchars($item['projeto'] ?? '—', ENT_QUOTES) ?>"
                            data-id-projeto="<?= htmlspecialchars($item['id_projeto_ref'] ?? '', ENT_QUOTES) ?>"
                            >
                            <td class="fw-bold"><?= date('d/m/Y', strtotime($item['data'])) ?></td>
                            <td><?= $item['hora'] ? substr($item['hora'], 0, 5) : '—' ?></td>
                            <td class="fw-medium"><?= htmlspecialchars($item['titulo']) ?></td>
                            <td class="text-muted small"><?= htmlspecialchars($item['projeto'] ?? '—') ?></td>
                            <td>
                                <?php if ($item['tipo'] === 'tarefa'): ?>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-check2-square me-1"></i>Tarefa</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-calendar-event me-1"></i>Evento</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge badge-status <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                            <td class="text-center">
                                <?php if ($item['concluido'] && $prazoPassou): ?>
                                    <button class="btn btn-sm btn-outline-secondary opacity-50"
                                            onclick="event.stopPropagation()"
                                            style="cursor:default;" title="Prazo encerrado, não é possível desfazer">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                    <?php if ($temArquivo): ?>
                                    <button class="btn btn-sm btn-outline-primary ms-1"
                                            onclick="event.stopPropagation();abrirModalEdicao(this.closest('tr'))" title="Ver arquivo">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php else: ?>
                                    <button class="btn btn-sm btn-outline-secondary ms-1 opacity-50"
                                            onclick="event.stopPropagation()"
                                            style="cursor:default;" title="Nenhum arquivo anexado">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php endif; ?>
                                <?php elseif ($item['concluido']): ?>
                                    <button class="btn btn-sm btn-outline-warning"
                                            onclick="event.stopPropagation();toggleCronograma(this)" title="Desfazer conclusão">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                    <?php if ($temArquivo): ?>
                                    <button class="btn btn-sm btn-outline-primary ms-1"
                                            onclick="event.stopPropagation();abrirModalEdicao(this.closest('tr'))" title="Ver arquivo">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php else: ?>
                                    <button class="btn btn-sm btn-outline-secondary ms-1 opacity-50"
                                            onclick="event.stopPropagation()"
                                            style="cursor:default;" title="Nenhum arquivo anexado">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php endif; ?>
                                <?php elseif ($prazoPassou): ?>
                                    <button class="btn btn-sm btn-outline-secondary opacity-50"
                                            onclick="event.stopPropagation()"
                                            disabled title="Prazo encerrado">
                                        <i class="bi bi-lock-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary ms-1 opacity-50"
                                            onclick="event.stopPropagation()"
                                            disabled title="Prazo encerrado">
                                        <i class="bi bi-paperclip"></i>
                                    </button>
                                <?php elseif ($temArquivo): ?>
                                    <button class="btn btn-sm btn-outline-success"
                                            onclick="event.stopPropagation();toggleCronograma(this)" title="Marcar como concluído">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning ms-1"
                                            onclick="event.stopPropagation();abrirModalEnvio(this.closest('tr'))" title="Editar arquivo">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-success"
                                            onclick="event.stopPropagation();toggleCronograma(this)" title="Marcar como concluído">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary ms-1"
                                            onclick="event.stopPropagation();abrirModalEnvio(this.closest('tr'))" title="Enviar arquivo">
                                        <i class="bi bi-paperclip"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
