<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Gerenciar Projetos</h3>
        <p class="text-muted mb-0">Projetos em que você participa</p>
    </div>
    <button class="btn btn-primary" onclick="carregarPagina('seletivos')"><i class="bi bi-megaphone me-2"></i>Solicitar Participação em Projeto</button>
</div>

<style>
.pj-card {
    background: #fff;
    border-radius: 14px;
    padding: 20px 20px 18px;
    box-shadow: 0 2px 14px rgba(0,0,0,0.06);
    border-top: 4px solid var(--c);
    position: relative;
    overflow: hidden;
    height: 100%;
}
.pj-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: var(--c);
    opacity: 0.04;
    pointer-events: none;
}
.pj-card .pj-ico {
    position: absolute;
    right: 14px;
    bottom: 10px;
    font-size: 3rem;
    color: var(--c);
    opacity: 0.1;
    line-height: 1;
    pointer-events: none;
}
.pj-card .pj-num {
    font-size: 2rem;
    font-weight: 800;
    color: #1e293b;
    line-height: 1;
    margin-bottom: 6px;
}
.pj-card .pj-label {
    font-size: 0.78rem;
    font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.pj-card .pj-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 20px;
    background: var(--c);
    color: #fff;
    margin-bottom: 10px;
    opacity: 0.85;
}
</style>

<div class="row g-3 mb-4">
    <div class="col-6 col-sm-6 col-lg-3">
        <div class="pj-card" style="--c:#3b82f6;">
            <div class="pj-badge"><i class="bi bi-folder-fill"></i> Ativos</div>
            <div class="pj-num"><?= $estatisticas['ativos'] ?></div>
            <div class="pj-ico"><i class="bi bi-folder-fill"></i></div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-lg-3">
        <div class="pj-card" style="--c:#22c55e;">
            <div class="pj-badge"><i class="bi bi-people-fill"></i> Participações</div>
            <div class="pj-num"><?= $estatisticas['total'] ?></div>
            <div class="pj-ico"><i class="bi bi-people-fill"></i></div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-lg-3">
        <div class="pj-card" style="--c:#8b5cf6;">
            <div class="pj-badge"><i class="bi bi-check2-all"></i> Concluídos</div>
            <div class="pj-num"><?= $estatisticas['concluidos'] ?></div>
            <div class="pj-ico"><i class="bi bi-check2-all"></i></div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-lg-3">
        <div class="pj-card" style="--c:#f59e0b;">
            <div class="pj-badge"><i class="bi bi-clock-history"></i> Carga</div>
            <div class="pj-num"><?= $estatisticas['carga'] ?>h</div>
            <div class="pj-ico"><i class="bi bi-clock-history"></i></div>
        </div>
    </div>
</div>

<div class="content-card">
    <h5 class="fw-bold mb-3">Meus Projetos</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle w-100" id="tabelaProjetos">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>TÍTULO</th>
                    <th>TIPO</th>
                    <th>FUNÇÃO</th>
                    <th>ORIENTADOR</th>
                    <th>CARGA</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($projetos)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">Você não está vinculado a nenhum projeto.</td></tr>
                <?php else: ?>
                    <?php foreach ($projetos as $p):
                        $participantes = htmlspecialchars(
                            $p['participantes'] ?? '[]',
                            ENT_QUOTES
                        );
                    ?>
                        <tr style="cursor:pointer;"
                            onclick="abrirDetalhesProjeto(this)"
                            data-titulo="<?= htmlspecialchars($p['titulo'], ENT_QUOTES) ?>"
                            data-tipo="<?= htmlspecialchars($p['tipo'] ?? '', ENT_QUOTES) ?>"
                            data-funcao="<?= htmlspecialchars($p['funcao'], ENT_QUOTES) ?>"
                            data-carga="<?= $p['carga_horaria'] ?>"
                            data-status="<?= htmlspecialchars($p['status'], ENT_QUOTES) ?>"
                            data-orientador="<?= htmlspecialchars($p['orientador'] ?? '', ENT_QUOTES) ?>"
                            data-area="<?= htmlspecialchars($p['area'] ?? '', ENT_QUOTES) ?>"
                            data-descricao="<?= htmlspecialchars($p['descricao'] ?? '', ENT_QUOTES) ?>"
                            data-data-inicio="<?= $p['data_inicio'] ? date('d/m/Y', strtotime($p['data_inicio'])) : '' ?>"
                            data-data-fim="<?= $p['data_fim'] ? date('d/m/Y', strtotime($p['data_fim'])) : '' ?>"
                            data-participantes="<?= $participantes ?>">
                            <td class="fw-medium"><?= htmlspecialchars($p['titulo']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($p['tipo'] ?? '—') ?></span></td>
                            <td><?= htmlspecialchars($p['funcao']) ?></td>
                            <td><?= $p['orientador'] ? htmlspecialchars($p['orientador']) : '—' ?></td>
                            <td><?= $p['carga_horaria'] ?>h</td>
                            <td>
                                <?php if ($p['status'] === 'ativo'): ?>
                                    <span class="status-ativo">Ativo</span>
                                <?php elseif ($p['status'] === 'concluido'): ?>
                                    <span class="badge bg-success text-white"><i class="bi bi-check-circle-fill me-1"></i>Concluído</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary text-white"><?= ucfirst($p['status']) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex align-items-center justify-content-center gap-2 mt-3" id="paginaWrap-projetos" style="display:none;">
        <button class="btn btn-sm btn-outline-primary" id="paginaPrev-projetos" onclick="paginarIr('projetos', -1)"><i class="bi bi-chevron-left"></i></button>
        <span class="d-flex align-items-center gap-1" style="font-size:0.85rem;">
            Página
            <input type="number" min="1" id="paginaInput-projetos" value="1"
                   class="form-control form-control-sm text-center" style="width:55px;"
                   onkeydown="if(event.key==='Enter'){ paginarIrPara('projetos', this.value); this.blur(); }"
                   onblur="paginarIrPara('projetos', this.value)">
            de <span id="paginaTotal-projetos">1</span>
        </span>
        <button class="btn btn-sm btn-outline-primary" id="paginaNext-projetos" onclick="paginarIr('projetos', 1)"><i class="bi bi-chevron-right"></i></button>
    </div>
</div>
<script>paginarIniciar('projetos', '#tabelaProjetos tbody tr[data-titulo]');</script>
