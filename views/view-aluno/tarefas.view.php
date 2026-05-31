<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Minhas Tarefas</h3>
        <p class="text-muted mb-0">Atividades e eventos em aberto</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #16a34a;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#16a34a;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#16a34a;opacity:0.1;line-height:1;pointer-events:none;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#16a34a;color:#fff;opacity:0.85;margin-bottom:10px;">
                <i class="bi bi-check-circle-fill"></i> Concluídas
            </div>
            <div class="fw-bold lh-1" id="statConcluidos" style="font-size:2rem;color:#1e293b;"><?= $estatisticas['concluidos'] ?></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #f59e0b;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#f59e0b;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#f59e0b;opacity:0.1;line-height:1;pointer-events:none;">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#f59e0b;color:#fff;opacity:0.85;margin-bottom:10px;">
                <i class="bi bi-hourglass-split"></i> Pendentes
            </div>
            <div class="fw-bold lh-1" id="statPendentes" style="font-size:2rem;color:#1e293b;"><?= $estatisticas['pendentes'] ?></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #ef4444;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#ef4444;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#ef4444;opacity:0.1;line-height:1;pointer-events:none;">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#ef4444;color:#fff;opacity:0.85;margin-bottom:10px;">
                <i class="bi bi-x-circle-fill"></i> Não Concluídas
            </div>
            <div class="fw-bold lh-1" id="statNaoConcluidos" style="font-size:2rem;color:#1e293b;"><?= $estatisticas['nao_concluidos'] ?></div>
        </div>
    </div>
</div>

<div class="content-card mb-3 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filtroBusca" class="form-control border-start-0" placeholder="Buscar por título..." oninput="filtrarItens()">
            </div>
        </div>
        <div class="col-6 col-md-4">
            <select class="form-select" id="filtroStatus" onchange="filtrarItens()">
                <option value="">Status (Todos)</option>
                <option value="pendente">Pendente</option>
                <option value="nao_concluido">Não Concluído</option>
                <option value="concluido">Concluído</option>
            </select>
        </div>
        <div class="col-12 col-md-2 text-muted small text-center text-md-start" id="contadorItens">
            <?= count($itens) ?> resultado(s)
        </div>
    </div>
</div>

<div class="content-card">
    <h5 class="fw-bold mb-3">Lista de Atividades</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle w-100" id="tabelaTarefas">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>TÍTULO</th>
                    <th>PROJETO</th>
                    <th>PRAZO</th>
                    <th>HORA</th>
                    <th>STATUS</th>
                    <th class="text-center">AÇÃO</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($itens)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">Nenhuma atividade encontrada.</td></tr>
                <?php else: ?>
                    <?php foreach ($itens as $item):
                        $hoje = new DateTime();
                        $hoje->setTime(0,0,0);
                        $prazo = new DateTime($item['data']);

                        if ($item['concluido']) {
                            $statusKey   = 'concluido';
                            $statusLabel = 'Concluído';
                            $statusClass = 'bg-success text-white';
                        } elseif ($prazo < $hoje) {
                            $statusKey   = 'nao_concluido';
                            $statusLabel = 'Não Concluído';
                            $statusClass = 'bg-danger text-white';
                        } else {
                            $statusKey   = 'pendente';
                            $statusLabel = 'Pendente';
                            $statusClass = 'bg-warning text-dark';
                        }
                    ?>
                        <?php
                            $descricao   = $item['descricao'] ?? '';
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
                            onclick="abrirDetalheTarefa(this)"
                            data-tipo="<?= htmlspecialchars($item['tipo']) ?>"
                            data-status="<?= $statusKey ?>"
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
                            <td class="fw-medium"><?= htmlspecialchars($item['titulo']) ?></td>
                            <td class="text-muted small"><?= htmlspecialchars($item['projeto'] ?? '—') ?></td>
                            <td><?= date('d/m/Y', strtotime($item['data'])) ?></td>
                            <td><?= $item['hora'] ? substr($item['hora'], 0, 5) : '—' ?></td>
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
                                            onclick="event.stopPropagation(); abrirModalEdicao(this.closest('tr'))"
                                            title="Ver arquivo">
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
                                            onclick="event.stopPropagation(); toggleConcluido(this)"
                                            title="Desfazer conclusão">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                    <?php if ($temArquivo): ?>
                                    <button class="btn btn-sm btn-outline-primary ms-1"
                                            onclick="event.stopPropagation(); abrirModalEdicao(this.closest('tr'))"
                                            title="Ver arquivo">
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
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-success"
                                            onclick="event.stopPropagation(); toggleConcluido(this)"
                                            title="Marcar como concluído">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <?php if ($temArquivo): ?>
                                    <button class="btn btn-sm btn-outline-warning ms-1"
                                            onclick="event.stopPropagation(); abrirModalEnvio(this.closest('tr'))"
                                            title="Editar arquivo">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <?php else: ?>
                                    <button class="btn btn-sm btn-outline-primary ms-1"
                                            onclick="event.stopPropagation(); abrirModalEnvio(this.closest('tr'))"
                                            title="Enviar arquivo">
                                        <i class="bi bi-paperclip"></i>
                                    </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
