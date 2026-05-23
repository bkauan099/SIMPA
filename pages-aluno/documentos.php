<?php
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? null;
require_once __DIR__ . '/../conexao/conexao.php';
if (!$id_usuario) { echo '<p class="text-danger p-4">Sessão expirada. Recarregue a página.</p>'; exit; }

$stmt = $pdo->prepare("
    SELECT
        p.id_producao,
        p.titulo        AS tarefa,
        p.tipo          AS nome_arquivo,
        p.caminho,
        p.status,
        proj.titulo     AS projeto
    FROM producoes p
    JOIN participacao pa ON pa.id_projeto = p.id_projeto
    JOIN projetos proj   ON proj.id_projeto = p.id_projeto
    WHERE pa.id_usuario = :id
      AND p.status != 'inativo'
    ORDER BY p.id_producao DESC
");
$stmt->execute([':id' => $id_usuario]);
$documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Remove registros cujo arquivo não existe no disco
$documentos = array_values(array_filter($documentos, function($d) {
    if (empty($d['caminho'])) return false;
    return file_exists(__DIR__ . '/../' . ltrim($d['caminho'], '/\\'));
}));

$totalPdf   = 0;
$totalOutros = 0;
foreach ($documentos as $d) {
    $ext = strtolower(pathinfo($d['nome_arquivo'], PATHINFO_EXTENSION));
    if ($ext === 'pdf') $totalPdf++; else $totalOutros++;
}
$total = count($documentos);

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
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1">Documentos</h3>
        <p class="text-muted mb-0">Arquivos enviados nas suas atividades</p>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #3b82f6;position:relative;overflow:hidden;">
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#3b82f6;opacity:0.08;line-height:1;pointer-events:none;"><i class="bi bi-files"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#3b82f6;color:#fff;opacity:0.85;margin-bottom:10px;">
                <i class="bi bi-files"></i> Total
            </div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= $total ?></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #ef4444;position:relative;overflow:hidden;">
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#ef4444;opacity:0.08;line-height:1;pointer-events:none;"><i class="bi bi-file-earmark-pdf"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#ef4444;color:#fff;opacity:0.85;margin-bottom:10px;">
                <i class="bi bi-file-earmark-pdf"></i> PDFs
            </div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= $totalPdf ?></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div style="background:#fff;border-radius:14px;padding:18px 20px 16px;box-shadow:0 2px 14px rgba(0,0,0,0.06);border-top:4px solid #64748b;position:relative;overflow:hidden;">
            <div style="position:absolute;right:12px;bottom:6px;font-size:3rem;color:#64748b;opacity:0.08;line-height:1;pointer-events:none;"><i class="bi bi-file-earmark"></i></div>
            <div style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:20px;background:#64748b;color:#fff;opacity:0.85;margin-bottom:10px;">
                <i class="bi bi-file-earmark"></i> Outros
            </div>
            <div class="fw-bold lh-1" style="font-size:2rem;color:#1e293b;"><?= $totalOutros ?></div>
        </div>
    </div>
</div>

<!-- Filtro -->
<div class="content-card mb-3 p-3">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-7">
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
        <div class="col-6 col-md-2 text-muted small text-center" id="contadorDocs">
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
                    <th class="text-center">AÇÃO</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($documentos)): ?>
                    <tr><td colspan="4" class="text-center py-4 text-muted">Nenhum documento encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($documentos as $doc):
                        $nome       = $doc['nome_arquivo'];
                        $ext        = strtolower(pathinfo($nome, PATHINFO_EXTENSION));
                        $isPdf      = ($ext === 'pdf');
                        $podeVisualizar = in_array($ext, EXTS_VISUALIZAVEL);
                        $icon       = iconeArquivo($ext);
                        $proxyUrl   = 'pages-aluno/servir-arquivo.php?id=' . $doc['id_producao'];
                        $buscaKey   = strtolower($nome . ' ' . $doc['tarefa'] . ' ' . $doc['projeto']);
                    ?>
                    <tr data-busca="<?= htmlspecialchars($buscaKey) ?>"
                        data-tipo="<?= $isPdf ? 'pdf' : 'outro' ?>">
                        <td>
                            <i class="bi <?= $icon ?> me-2 fs-5"></i>
                            <span class="fw-medium"><?= htmlspecialchars($nome) ?></span>
                        </td>
                        <td class="text-muted small"><?= htmlspecialchars($doc['tarefa']) ?></td>
                        <td class="text-muted small"><?= htmlspecialchars($doc['projeto']) ?></td>
                        <td class="text-center">
                            <?php if ($podeVisualizar): ?>
                            <button class="btn btn-sm btn-outline-primary me-1"
                                    data-caminho="<?= htmlspecialchars($proxyUrl, ENT_QUOTES) ?>"
                                    data-nome="<?= htmlspecialchars($nome, ENT_QUOTES) ?>"
                                    onclick="abrirModalVisualizar(this.dataset.caminho, this.dataset.nome)"
                                    title="Visualizar arquivo">
                                <i class="bi bi-eye"></i>
                            </button>
                            <?php endif; ?>
                            <a href="<?= htmlspecialchars($proxyUrl, ENT_QUOTES) ?>"
                               download="<?= htmlspecialchars($nome, ENT_QUOTES) ?>"
                               class="btn btn-sm btn-outline-secondary" title="Baixar arquivo">
                                <i class="bi bi-download"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function filtrarDocumentos() {
    const busca = (document.getElementById('filtroDocBusca')?.value || '').toLowerCase();
    const tipo  = document.getElementById('filtroDocTipo')?.value || '';
    const linhas = document.querySelectorAll('#tabelaDocumentos tbody tr[data-busca]');
    let visiveis = 0;
    linhas.forEach(tr => {
        const okBusca = !busca || tr.dataset.busca.includes(busca);
        const okTipo  = !tipo  || tr.dataset.tipo === tipo;
        const visivel = okBusca && okTipo;
        tr.style.display = visivel ? '' : 'none';
        if (visivel) visiveis++;
    });
    const contador = document.getElementById('contadorDocs');
    if (contador) contador.textContent = visiveis + ' resultado(s)';
}
</script>
