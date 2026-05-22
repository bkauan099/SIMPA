<?php
$corPorTipo = [
    'tarefa'  => ['cor' => '#3b82f6', 'icone' => 'bi-check2-square',  'label' => 'Tarefa'],
    'evento'  => ['cor' => '#22c55e', 'icone' => 'bi-calendar-event', 'label' => 'Evento'],
    'reuniao' => ['cor' => '#a855f7', 'icone' => 'bi-people',         'label' => 'Reunião'],
    'reunião' => ['cor' => '#a855f7', 'icone' => 'bi-people',         'label' => 'Reunião'],
];
$fallback = ['cor' => '#94a3b8', 'icone' => 'bi-file-text', 'label' => null];

$pct = $estatisticas['total'] > 0
    ? round(($estatisticas['concluidos'] / $estatisticas['total']) * 100)
    : 0;

$hoje = new DateTime(); $hoje->setTime(0,0,0);
?>

<!-- CABEÇALHO -->
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold mb-1">Registros</h3>
        <p class="text-muted mb-0" style="font-size:0.9rem;">
            Histórico de todas as atividades registradas no sistema
        </p>
    </div>
    <div class="input-group shadow-sm" style="max-width:280px;">
        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
        <input type="text" id="buscaRegistro" class="form-control border-start-0 ps-0" placeholder="Buscar registro..." oninput="aplicarFiltroRegistros()">
    </div>
</div>

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:46px;height:46px;background:#eff6ff;">
                    <i class="bi bi-collection fs-5" style="color:#3b82f6;"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1"><?= $estatisticas['total'] ?></div>
                    <div class="text-muted" style="font-size:0.78rem;">Total de registros</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:46px;height:46px;background:#f0fdf4;">
                    <i class="bi bi-check-circle fs-5" style="color:#22c55e;"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold fs-4 lh-1"><?= $estatisticas['concluidos'] ?></div>
                    <div class="text-muted" style="font-size:0.78rem;">Concluídos</div>
                    <div class="progress mt-1" style="height:4px;">
                        <div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:46px;height:46px;background:#fff1f2;">
                    <i class="bi bi-x-circle fs-5" style="color:#ef4444;"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1"><?= $estatisticas['pendentes'] ?></div>
                    <div class="text-muted" style="font-size:0.78rem;">Não concluídos</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:46px;height:46px;background:#f5f3ff;">
                    <i class="bi bi-calendar2-week fs-5" style="color:#a855f7;"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1"><?= $estatisticas['este_mes'] ?></div>
                    <div class="text-muted" style="font-size:0.78rem;">Este mês</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FILTROS DINÂMICOS -->
<div class="d-flex gap-2 flex-wrap mb-3 align-items-center">
    <span class="text-muted me-1" style="font-size:0.8rem;">Filtrar:</span>
    <button class="btn btn-sm btn-outline-secondary filtro-btn active" data-tipo="todos" onclick="selecionarFiltroRegistro(this)">
        <i class="bi bi-collection me-1"></i>Todos <span class="badge bg-secondary ms-1"><?= $estatisticas['total'] ?></span>
    </button>
    <?php foreach ($estatisticas['por_tipo'] as $tipo => $qtd):
        $cfg   = $corPorTipo[strtolower($tipo)] ?? $fallback;
        $label = $cfg['label'] ?? ucfirst($tipo);
    ?>
    <button class="btn btn-sm btn-outline-secondary filtro-btn" data-tipo="<?= htmlspecialchars($tipo) ?>" onclick="selecionarFiltroRegistro(this)">
        <i class="bi <?= $cfg['icone'] ?> me-1"></i><?= htmlspecialchars($label) ?>
        <span class="badge bg-secondary ms-1"><?= $qtd ?></span>
    </button>
    <?php endforeach; ?>
</div>

<!-- LISTA DE REGISTROS -->
<?php if (empty($registros)): ?>
<div class="text-center py-5 text-muted">
    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
    <p class="mb-0">Nenhum registro encontrado.</p>
</div>
<?php else: ?>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="list-group list-group-flush" id="listaRegistros">
        <?php foreach ($registros as $r):
            $tipo  = strtolower($r['tipo'] ?? 'outro');
            $cfg   = $corPorTipo[$tipo] ?? $fallback;
            $cor   = $cfg['cor'];
            $icone = $cfg['icone'];
            $label = $cfg['label'] ?? ucfirst($r['tipo'] ?? 'Outro');
            $desc  = trim($r['descricao'] ?? '');

            $prazo = new DateTime($r['data']);
            if (!empty($r['concluido'])) {
                $statusLabel = 'Concluído';
                $statusStyle = 'background:#dcfce7;color:#16a34a;';
                $statusIco   = 'bi-check-circle';
                $statusKey   = 'concluido';
            } elseif ($prazo < $hoje) {
                $statusLabel = 'Não Concluído';
                $statusStyle = 'background:#fee2e2;color:#dc2626;';
                $statusIco   = 'bi-x-circle';
                $statusKey   = 'nao_concluido';
            } else {
                $statusLabel = 'Pendente';
                $statusStyle = 'background:#fef9c3;color:#a16207;';
                $statusIco   = 'bi-hourglass-split';
                $statusKey   = 'pendente';
            }

            $dataFmt = date('d/m/Y', strtotime($r['data']));
            $hora    = $r['hora'] ? substr($r['hora'], 0, 5) : null;
        ?>
        <div class="list-group-item list-group-item-action registro-card border-0 py-3"
             data-tipo="<?= htmlspecialchars($r['tipo'] ?? '') ?>"
             data-status="<?= $statusKey ?>"
             data-busca="<?= htmlspecialchars(strtolower($r['titulo'] . ' ' . $desc)) ?>"
             style="border-left:3px solid <?= $cor ?> !important; cursor:pointer;"
             onclick="abrirDetalheRegistro(this)"
             data-titulo="<?= htmlspecialchars($r['titulo']) ?>"
             data-desc="<?= htmlspecialchars($desc) ?>"
             data-data="<?= $dataFmt ?>"
             data-hora="<?= htmlspecialchars($hora ?? '—') ?>"
             data-label="<?= htmlspecialchars($label) ?>"
             data-icone="<?= $icone ?>"
             data-cor="<?= $cor ?>"
             data-status-label="<?= $statusLabel ?>"
             data-status-style="<?= htmlspecialchars($statusStyle) ?>"
             data-status-ico="<?= $statusIco ?>">

            <div class="d-flex justify-content-between align-items-start gap-3">

                <!-- Esquerda: ícone + conteúdo -->
                <div class="d-flex gap-3 align-items-start flex-grow-1 min-width-0">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0 mt-1"
                         style="width:34px;height:34px;background:<?= $cor ?>18;">
                        <i class="bi <?= $icone ?>" style="color:<?= $cor ?>;font-size:0.9rem;"></i>
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <div class="fw-semibold text-truncate"><?= htmlspecialchars($r['titulo']) ?></div>
                        <?php if ($desc !== ''): ?>
                        <div class="text-muted mt-1" style="font-size:0.8rem;line-height:1.4;">
                            <?= htmlspecialchars(mb_substr($desc, 0, 100)) ?><?= mb_strlen($desc) > 100 ? '…' : '' ?>
                        </div>
                        <?php endif; ?>
                        <div class="mt-1 d-flex align-items-center gap-2" style="font-size:0.75rem;color:#94a3b8;">
                            <span class="badge px-2 py-1 rounded-pill" style="background:<?= $cor ?>18;color:<?= $cor ?>;">
                                <i class="bi <?= $icone ?> me-1"></i><?= htmlspecialchars($label) ?>
                            </span>
                            <span><i class="bi bi-calendar2 me-1"></i><?= $dataFmt ?></span>
                            <?php if ($hora): ?>
                            <span><i class="bi bi-clock me-1"></i><?= $hora ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Direita: status -->
                <div class="flex-shrink-0">
                    <span class="badge rounded-pill px-2 py-1" style="<?= $statusStyle ?>font-size:0.72rem;">
                        <i class="bi <?= $statusIco ?> me-1"></i><?= $statusLabel ?>
                    </span>
                </div>

            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="semResultados" class="text-center py-5 text-muted d-none">
    <i class="bi bi-funnel fs-1 d-block mb-2 opacity-50"></i>
    <p class="mb-0">Nenhum registro encontrado para este filtro.</p>
</div>
<?php endif; ?>

<style>
.filtro-btn.active { background:#0F2557 !important; border-color:#0F2557 !important; color:#fff !important; }
.filtro-btn.active .badge { background:#fff !important; color:#0F2557 !important; }
.registro-card { transition: background .12s; }
.registro-card:hover { background:#f8fafc !important; }
</style>
