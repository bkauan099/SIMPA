<?php
$id_professor = $_SESSION['id_usuario'] ?? 0;

// Stats gerais
$stats_rel = ['carga_total' => 0, 'media_aluno' => 0, 'certificados' => 0, 'taxa_conclusao' => 0];
try {
    // Carga total e média (apenas alunos dos projetos do professor)
    $stmt = $pdo->prepare("
        SELECT
            COALESCE(SUM(par.carga_horaria), 0) AS carga_total,
            COALESCE(ROUND(AVG(par.carga_horaria)), 0) AS media_aluno
        FROM participacao par
        WHERE par.id_projeto IN (
            SELECT id_projeto FROM participacao WHERE id_usuario = :id
        )
        AND par.id_usuario != :id2
    ");
    $stmt->execute([':id' => $id_professor, ':id2' => $id_professor]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $stats_rel['carga_total'] = intval($row['carga_total']);
        $stats_rel['media_aluno'] = intval($row['media_aluno']);
    }
} catch (PDOException $e) {}

try {
    // Produções aprovadas (status 'ativo') nos projetos do professor
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS total
        FROM producoes
        WHERE id_projeto IN (
            SELECT id_projeto FROM participacao WHERE id_usuario = :id
        )
        AND status = 'ativo'
    ");
    $stmt->execute([':id' => $id_professor]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) $stats_rel['certificados'] = intval($row['total']);
} catch (PDOException $e) {}

try {
    // Taxa de conclusão: tarefas concluídas / total (via producoes)
    $stmt = $pdo->prepare("
        SELECT
            COUNT(DISTINCT a.id) AS total,
            COUNT(DISTINCT CASE WHEN
                COALESCE((SELECT pr.status FROM producoes pr
                          WHERE pr.titulo = a.titulo AND pr.id_projeto = a.id_projeto
                          ORDER BY pr.id_producao DESC LIMIT 1), 'pendente') = 'concluido'
                THEN a.id END) AS concluidas
        FROM agenda_items a
        JOIN participacao par ON a.id_projeto = par.id_projeto
        WHERE par.id_usuario = :id AND a.id_projeto IS NOT NULL
    ");
    $stmt->execute([':id' => $id_professor]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && $row['total'] > 0) {
        $stats_rel['taxa_conclusao'] = round($row['concluidas'] / $row['total'] * 100);
    }
} catch (PDOException $e) {}

// Progresso por aluno
$progresso_alunos = [];
try {
    $stmt = $pdo->prepare("
        SELECT
            u.id_usuario,
            u.nome,
            p.id_projeto,
            p.titulo AS nome_projeto,
            COALESCE(par.carga_horaria, 0) AS carga_horaria,
            COUNT(DISTINCT a.id) AS total_tarefas,
            COUNT(DISTINCT CASE WHEN
                COALESCE((SELECT pr.status FROM producoes pr
                          WHERE pr.titulo = a.titulo AND pr.id_projeto = a.id_projeto
                          ORDER BY pr.id_producao DESC LIMIT 1), 'pendente') = 'concluido'
                THEN a.id END) AS tarefas_concluidas,
            0 AS total_docs,
            0 AS docs_aprovados
        FROM participacao par
        JOIN usuarios u ON par.id_usuario = u.id_usuario
        JOIN projetos p ON par.id_projeto = p.id_projeto
        LEFT JOIN agenda_items a
            ON a.id_projeto = par.id_projeto
           AND a.id_usuario = par.id_usuario
           AND a.id_projeto IS NOT NULL
        WHERE par.id_projeto IN (
            SELECT id_projeto FROM participacao WHERE id_usuario = :id
        )
        AND par.id_usuario != :id2
        GROUP BY u.id_usuario, u.nome, p.id_projeto, p.titulo, par.carga_horaria
        ORDER BY u.nome ASC
    ");
    $stmt->execute([':id' => $id_professor, ':id2' => $id_professor]);
    $progresso_alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

// Projetos do professor para o filtro
$projetos_rel = [];
try {
    $stmt = $pdo->prepare("
        SELECT DISTINCT p.id_projeto, p.titulo
        FROM projetos p
        JOIN participacao par ON p.id_projeto = par.id_projeto
        WHERE par.id_usuario = :id
        ORDER BY p.titulo ASC
    ");
    $stmt->execute([':id' => $id_professor]);
    $projetos_rel = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Relatórios</h3>
        <p class="text-muted mb-0">Acompanhe o progresso dos alunos</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-blue">
            <div class="sc-watermark"><i class="bi bi-clock-history"></i></div>
            <div class="sc-label"><i class="bi bi-clock-history"></i> Carga Total</div>
            <div class="sc-number"><?= $stats_rel['carga_total'] ?>h</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-teal">
            <div class="sc-watermark"><i class="bi bi-graph-up"></i></div>
            <div class="sc-label"><i class="bi bi-graph-up"></i> Média por Aluno</div>
            <div class="sc-number"><?= $stats_rel['media_aluno'] ?>h</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-purple">
            <div class="sc-watermark"><i class="bi bi-award"></i></div>
            <div class="sc-label"><i class="bi bi-award"></i> Produções</div>
            <div class="sc-number"><?= $stats_rel['certificados'] ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-green">
            <div class="sc-watermark"><i class="bi bi-check2-all"></i></div>
            <div class="sc-label"><i class="bi bi-check2-all"></i> Taxa Conclusão</div>
            <div class="sc-number"><?= $stats_rel['taxa_conclusao'] ?>%</div>
        </div>
    </div>
</div>

<div id="tab-progresso">
    <div class="card border-0 shadow-sm mb-3"><div class="card-body py-2">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="filtroRelAluno" class="form-control border-start-0" placeholder="Buscar aluno..." oninput="filtrarRelatorios()">
                </div>
            </div>
            <div class="col-6 col-md-4">
                <select id="filtroRelProjeto" class="form-select" onchange="filtrarRelatorios()">
                    <option value="">Projeto (Todos)</option>
                    <?php foreach ($projetos_rel as $proj): ?>
                        <option value="<?= $proj['id_projeto'] ?>"><?= htmlspecialchars($proj['titulo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <button class="btn btn-outline-secondary w-100" onclick="limparFiltrosRelatorios()">Limpar</button>
            </div>
        </div>
    </div></div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
        <h5 class="fw-bold mb-3">Progresso por Aluno</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th>ALUNO</th><th>PROJETO</th><th>CARGA REGISTRADA</th><th>TAREFAS</th><th>DOCS ENVIADOS</th><th>PROGRESSO</th>
                    </tr>
                </thead>
                <tbody id="tabelaRelatorios">
                    <?php if (empty($progresso_alunos)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-bar-chart-line mb-2" style="font-size:2rem;display:block;"></i>
                                <p class="fw-bold m-0">Nenhum dado de progresso encontrado</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($progresso_alunos as $aluno):
                            $total_tar   = intval($aluno['total_tarefas']);
                            $conc_tar    = intval($aluno['tarefas_concluidas']);
                            $total_docs  = intval($aluno['total_docs']);
                            $aprov_docs  = intval($aluno['docs_aprovados']);
                            $carga       = intval($aluno['carga_horaria']);
                            $nome        = htmlspecialchars($aluno['nome']);
                            $projeto     = htmlspecialchars($aluno['nome_projeto']);
                            $id_proj     = intval($aluno['id_projeto']);

                            // Progresso: baseia em tarefas se houver, senão em docs
                            if ($total_tar > 0) {
                                $progresso = round($conc_tar / $total_tar * 100);
                            } elseif ($total_docs > 0) {
                                $progresso = round($aprov_docs / $total_docs * 100);
                            } else {
                                $progresso = 0;
                            }

                            // Cores
                            $barColor = $progresso >= 75 ? 'bg-success' : ($progresso >= 50 ? 'bg-info' : ($progresso >= 25 ? 'bg-warning' : 'bg-danger'));

                            $tarBadge = $total_tar === 0 ? 'bg-secondary text-white'
                                : ($conc_tar === $total_tar ? 'bg-success text-white'
                                : ($conc_tar > $total_tar / 2 ? 'bg-warning text-dark' : 'bg-danger text-white'));

                            $docBadge = 'bg-secondary';

                            // Avatar: 2 iniciais
                            $partes = explode(' ', $aluno['nome']);
                            $iniciais = strtoupper(substr($partes[0], 0, 1) . (isset($partes[1]) ? substr($partes[1], 0, 1) : ''));
                        ?>
                        <tr data-aluno="<?= strtolower($nome) ?>" data-projeto-id="<?= $id_proj ?>">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($aluno['nome']) ?>&background=e0f2fe&color=0369a1"
                                         class="rounded-circle" width="30" alt="">
                                    <?= $nome ?>
                                </div>
                            </td>
                            <td><?= $projeto ?></td>
                            <td><?= $carga ?>h</td>
                            <td>
                                <span class="badge <?= $tarBadge ?>">
                                    <?= $conc_tar ?>/<?= $total_tar ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary" title="Aguardando integração">—</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:8px;">
                                        <div class="progress-bar <?= $barColor ?>" style="width:<?= $progresso ?>%"></div>
                                    </div>
                                    <small class="text-muted"><?= $progresso ?>%</small>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        </div>
    </div>
</div>

<script>
function filtrarRelatorios() {
    const norm = s => s.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
    const busca   = norm((document.getElementById('filtroRelAluno')?.value   || '').trim());
    const projeto = document.getElementById('filtroRelProjeto')?.value  || '';

    document.querySelectorAll('#tabelaRelatorios tr[data-aluno]').forEach(tr => {
        const nomeAluno  = norm(tr.dataset.aluno  || '');
        const idProjeto  = tr.dataset.projetoId || '';

        const bateAluno   = busca   === '' || nomeAluno.startsWith(busca);
        const bateProjeto = projeto === '' || idProjeto === projeto;
        tr.style.display  = (bateAluno && bateProjeto) ? '' : 'none';
    });
}

function limparFiltrosRelatorios() {
    const inp = document.getElementById('filtroRelAluno');
    const sel = document.getElementById('filtroRelProjeto');
    if (inp) inp.value = '';
    if (sel) sel.value = '';
    document.querySelectorAll('#tabelaRelatorios tr[data-aluno]').forEach(tr => tr.style.display = '');
}
</script>
