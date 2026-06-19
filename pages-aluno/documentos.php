<?php
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? null;
require_once __DIR__ . '/../conexao/conexao.php';
if (!$id_usuario) { echo '<p class="text-danger p-4">Sessão expirada. Recarregue a página.</p>'; exit; }

// Busca matrícula do aluno para filtrar apenas seus próprios arquivos
$stmtMat = $pdo->prepare("SELECT matricula FROM usuarios WHERE id_usuario = :id");
$stmtMat->execute([':id' => $id_usuario]);
$_matriculaDoc = $stmtMat->fetchColumn();

$stmt = $pdo->prepare("
    SELECT
        p.id_producao,
        p.titulo               AS tarefa,
        p.tipo                 AS nome_arquivo,
        p.caminho,
        CAST(p.status AS TEXT) AS status,
        p.data_registro,
        proj.titulo            AS projeto
    FROM producoes p
    JOIN projetos proj ON proj.id_projeto = p.id_projeto
    WHERE p.caminho LIKE :prefix
    ORDER BY p.data_registro DESC
");
$stmt->execute([':prefix' => 'uploads/producoes/aluno/' . $_matriculaDoc . '/%']);
$documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$documentos = array_values(array_filter($documentos, function($d) {
    if (empty($d['caminho'])) return false;
    return file_exists(__DIR__ . '/../' . ltrim($d['caminho'], '/\\'));
}));

const EXTS_VISUALIZAVEL = ['pdf','jpg','jpeg','png','gif','webp','svg',
    'txt','js','ts','jsx','tsx','mjs','cjs','py','php','rb','java','c','cpp','cc',
    'h','hpp','cs','go','rs','swift','kt','html','htm','css','scss','sass','less',
    'xml','json','yaml','yml','toml','ini','conf','env','sh','bash','zsh','bat',
    'cmd','ps1','sql','md','markdown','csv','log'];

function iconeArquivo(string $ext): string {
    return match(true) {
        $ext === 'pdf'                        => 'bi-file-earmark-pdf text-danger',
        in_array($ext, ['doc','docx'])        => 'bi-file-earmark-word text-primary',
        in_array($ext, ['xls','xlsx'])        => 'bi-file-earmark-excel text-success',
        in_array($ext, ['jpg','jpeg','png',
                         'gif','webp','svg']) => 'bi-file-earmark-image text-info',
        $ext === 'txt'                        => 'bi-file-earmark-text text-secondary',
        in_array($ext, ['zip','rar','7z'])    => 'bi-file-earmark-zip text-warning',
        default                               => 'bi-file-earmark text-muted',
    };
}

function statusInfo(string $s): array {
    return match($s) {
        'concluido' => ['label' => 'Aprovado',              'icon' => 'bi-check-circle-fill', 'cor' => '#16a34a', 'bg' => '#dcfce7', 'badge' => 'bg-success text-white',  'badge_style' => ''],
        'cancelado' => ['label' => 'Reprovado',             'icon' => 'bi-x-circle-fill',     'cor' => '#dc2626', 'bg' => '#fee2e2', 'badge' => 'text-white',             'badge_style' => 'background:#dc2626;'],
        'refazer'   => ['label' => 'Corrigir',               'icon' => 'bi-arrow-repeat',      'cor' => '#ea580c', 'bg' => '#fff7ed', 'badge' => 'text-white',             'badge_style' => 'background:#ea580c;'],
        default     => ['label' => 'Aguardando Aprovação',  'icon' => 'bi-hourglass-split',   'cor' => '#d97706', 'bg' => '#fef3c7', 'badge' => 'bg-warning text-dark',   'badge_style' => ''],
    };
}

$contadores = ['pendente' => 0, 'aprovado' => 0, 'reprovado' => 0, 'corrigir' => 0];
foreach ($documentos as $d) {
    $s = $d['status'];
    if ($s === 'concluido') $contadores['aprovado']++;
    elseif ($s === 'cancelado') $contadores['reprovado']++;
    elseif ($s === 'refazer') $contadores['corrigir']++;
    else $contadores['pendente']++;
}
$total = count($documentos);
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Documentos</h3>
        <p class="text-muted mb-0">Arquivos enviados nas suas atividades</p>
    </div>
</div>

<!-- Cartões de status -->
<div class="row g-3 mb-4">
    <div class="col-6 col-sm-4 col-lg">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #f59e0b;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#f59e0b;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#f59e0b;opacity:0.1;line-height:1;pointer-events:none;"><i class="bi bi-hourglass-split"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#f59e0b;color:#fff;opacity:0.85;margin-bottom:10px;">
                <i class="bi bi-hourglass-split"></i> Aguardando
            </div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= $contadores['pendente'] ?></div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-lg">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #ea580c;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#ea580c;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#ea580c;opacity:0.1;line-height:1;pointer-events:none;"><i class="bi bi-arrow-repeat"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#ea580c;color:#fff;opacity:0.85;margin-bottom:10px;">
                <i class="bi bi-arrow-repeat"></i> Corrigir
            </div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= $contadores['corrigir'] ?></div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-lg">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #ef4444;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#ef4444;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#ef4444;opacity:0.1;line-height:1;pointer-events:none;"><i class="bi bi-x-circle-fill"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#ef4444;color:#fff;opacity:0.85;margin-bottom:10px;">
                <i class="bi bi-x-circle-fill"></i> Reprovados
            </div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= $contadores['reprovado'] ?></div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-lg">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #16a34a;position:relative;overflow:hidden;">
            <div style="position:absolute;inset:0;background:#16a34a;opacity:0.04;pointer-events:none;"></div>
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#16a34a;opacity:0.1;line-height:1;pointer-events:none;"><i class="bi bi-check-circle-fill"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#16a34a;color:#fff;opacity:0.85;margin-bottom:10px;">
                <i class="bi bi-check-circle-fill"></i> Aprovados
            </div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= $contadores['aprovado'] ?></div>
        </div>
    </div>
</div>

<!-- Filtro -->
<div class="content-card mb-3 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filtroDocBusca" class="form-control border-start-0"
                       placeholder="Buscar por arquivo ou tarefa..." oninput="filtrarDocumentos()">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select" id="filtroDocTipo" onchange="filtrarDocumentos()">
                <option value="">Tipo (Todos)</option>
                <option value="pdf">PDF</option>
                <option value="outro">Outros</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select class="form-select" id="filtroDocStatus" onchange="filtrarDocumentos()">
                <option value="">Status (Todos)</option>
                <option value="pendente">Aguardando</option>
                <option value="aprovado">Aprovado</option>
                <option value="reprovado">Reprovado</option>
                <option value="corrigir">Corrigir</option>
            </select>
        </div>
        <div class="col-12 col-md-2 text-muted small text-center" id="contadorDocs">
            <?= $total ?> resultado(s)
        </div>
    </div>
</div>

<!-- Tabela -->
<div class="content-card">
    <h5 class="fw-bold mb-3">Meus Documentos</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle w-100" id="tabelaDocumentos">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th>ARQUIVO</th>
                    <th>TAREFA</th>
                    <th>PROJETO</th>
                    <th>ENVIADO EM</th>
                    <th>STATUS</th>
                    <th class="text-center">AÇÃO</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($documentos)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">Nenhum documento encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($documentos as $doc):
                        $nome     = $doc['nome_arquivo'];
                        $ext      = strtolower(pathinfo($nome, PATHINFO_EXTENSION));
                        $isPdf    = ($ext === 'pdf');
                        $podeVer  = in_array($ext, EXTS_VISUALIZAVEL);
                        $icon     = iconeArquivo($ext);
                        $proxyUrl = 'pages-aluno/servir-arquivo.php?id=' . $doc['id_producao'];
                        $buscaKey = strtolower($nome . ' ' . $doc['tarefa'] . ' ' . $doc['projeto']);
                        $st       = statusInfo($doc['status']);
                        $stKey    = match($doc['status']) { 'concluido' => 'aprovado', 'cancelado' => 'reprovado', 'refazer' => 'corrigir', default => 'pendente' };
                    ?>
                    <tr data-busca="<?= htmlspecialchars($buscaKey) ?>"
                        data-tipo="<?= $isPdf ? 'pdf' : 'outro' ?>"
                        data-status="<?= $stKey ?>">
                        <td>
                            <i class="bi <?= $icon ?> me-2 fs-5"></i>
                            <span class="fw-medium"><?= htmlspecialchars($nome) ?></span>
                        </td>
                        <td class="text-muted small"><?= htmlspecialchars($doc['tarefa']) ?></td>
                        <td class="text-muted small"><?= htmlspecialchars($doc['projeto']) ?></td>
                        <td class="text-muted small"><?= !empty($doc['data_registro']) ? date('d/m/Y', strtotime($doc['data_registro'])) : '—' ?></td>
                        <td>
                            <span class="badge <?= $st['badge'] ?>" style="font-size:0.72rem;<?= $st['badge_style'] ?>">
                                <i class="bi <?= $st['icon'] ?> me-1"></i><?= $st['label'] ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <?php if ($podeVer): ?>
                            <button class="btn btn-sm btn-outline-primary me-1"
                                    data-caminho="<?= htmlspecialchars($proxyUrl, ENT_QUOTES) ?>"
                                    data-nome="<?= htmlspecialchars($nome, ENT_QUOTES) ?>"
                                    onclick="abrirModalVisualizar(this.dataset.caminho, this.dataset.nome)"
                                    title="Visualizar">
                                <i class="bi bi-eye"></i>
                            </button>
                            <?php endif; ?>
                            <a href="<?= htmlspecialchars($proxyUrl, ENT_QUOTES) ?>"
                               download="<?= htmlspecialchars($nome, ENT_QUOTES) ?>"
                               class="btn btn-sm btn-outline-secondary" title="Baixar">
                                <i class="bi bi-download"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex align-items-center justify-content-center gap-2 mt-3" id="paginaWrap-documentos" style="display:none;">
        <button class="btn btn-sm btn-outline-primary" id="paginaPrev-documentos" onclick="paginarIr('documentos', -1)"><i class="bi bi-chevron-left"></i></button>
        <span class="d-flex align-items-center gap-1" style="font-size:0.85rem;">
            Página
            <input type="number" min="1" id="paginaInput-documentos" value="1"
                   class="form-control form-control-sm text-center" style="width:55px;"
                   onkeydown="if(event.key==='Enter'){ paginarIrPara('documentos', this.value); this.blur(); }"
                   onblur="paginarIrPara('documentos', this.value)">
            de <span id="paginaTotal-documentos">1</span>
        </span>
        <button class="btn btn-sm btn-outline-primary" id="paginaNext-documentos" onclick="paginarIr('documentos', 1)"><i class="bi bi-chevron-right"></i></button>
    </div>
</div>
<script>paginarIniciar('documentos', '#tabelaDocumentos tbody tr[data-busca]');</script>


<script>
function filtrarDocumentos() {
    const busca  = (document.getElementById('filtroDocBusca')?.value || '').toLowerCase();
    const tipo   = document.getElementById('filtroDocTipo')?.value   || '';
    const status = document.getElementById('filtroDocStatus')?.value || '';
    const linhas = document.querySelectorAll('#tabelaDocumentos tbody tr[data-busca]');
    let visiveis = 0;
    linhas.forEach(tr => {
        const ok = (!busca  || tr.dataset.busca.includes(busca))
                && (!tipo   || tr.dataset.tipo === tipo)
                && (!status || tr.dataset.status === status);
        tr.dataset.filtroOculto = ok ? '0' : '1';
        if (ok) visiveis++;
    });
    const cnt = document.getElementById('contadorDocs');
    if (cnt) cnt.textContent = visiveis + ' resultado(s)';
    if (typeof paginarRecalcular === 'function') paginarRecalcular('documentos');
}
</script>
