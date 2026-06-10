<?php
require_once 'conexao/conexao.php';

// 1. Filtros iniciais
$busca = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filtro = isset($_GET['status']) ? trim($_GET['status']) : '';

// 2. Estatísticas dos Cards — aprovado→ativo | reprovado→inativo
$stats = $pdo->query("SELECT COUNT(*) as total,
    SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
    SUM(CASE WHEN status = 'ativo'    THEN 1 ELSE 0 END) as aprovados,
    SUM(CASE WHEN status = 'inativo'  THEN 1 ELSE 0 END) as reprovados
    FROM producoes")->fetch(PDO::FETCH_ASSOC);

// 3. Query da Tabela
// MIGRADO: nome_original→tipo | caminho_arquivo→caminho | data_upload→data_registro | id_documento→id_producao
$sql = "SELECT d.*, p.titulo as nome_projeto FROM producoes d
        LEFT JOIN projetos p ON d.id_projeto = p.id_projeto WHERE 1=1";
$params = [];
if (!empty($busca)) {
    $sql .= " AND (unaccent(COALESCE(d.titulo, d.tipo)) ILIKE unaccent(?))";
    $params[] = "%$busca%";
}
if (!empty($status_filtro)) {
    $sql .= " AND d.status = ?";
    $params[] = $status_filtro;
}
$sql .= " ORDER BY d.data_registro DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getIconeArquivo($nome) {
    $ext = strtolower(pathinfo($nome, PATHINFO_EXTENSION));
    if($ext == 'pdf') return 'bi-file-earmark-pdf text-danger';
    if(in_array($ext, ['xlsx','xls','csv'])) return 'bi-file-earmark-excel text-success';
    if(in_array($ext, ['docx','doc'])) return 'bi-file-earmark-word text-primary';
    return 'bi-file-earmark-text text-secondary';
}

// aprovado → ativo | reprovado → inativo
$status_class = [
    'pendente'  => 'bg-warning-subtle text-warning fw-semibold',
    'ativo'     => 'bg-success-subtle text-success fw-semibold',
    'inativo'   => 'bg-danger-subtle text-danger fw-semibold',
    'concluido' => 'bg-success-subtle text-success fw-semibold',
    'cancelado' => 'bg-danger-subtle text-danger fw-semibold',
];
$status_label = [
    'pendente'  => 'Pendente',
    'ativo'     => 'Aprovado',
    'inativo'   => 'Reprovado',
    'concluido' => 'Concluído',
    'cancelado' => 'Cancelado',
];
?>

<h3 class="fw-bold mb-1">Documentos</h3>
<p class="text-muted mb-4">Revise e aprove os documentos enviados pelos seus alunos</p>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-blue">
            <div class="sc-watermark"><i class="bi bi-files"></i></div>
            <div class="sc-label"><i class="bi bi-files"></i> Total</div>
            <div class="sc-number"><?= $stats['total'] ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-yellow">
            <div class="sc-watermark"><i class="bi bi-clock-history"></i></div>
            <div class="sc-label"><i class="bi bi-clock-history"></i> Aguard. Revisão</div>
            <div class="sc-number"><?= $stats['pendentes'] ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-green">
            <div class="sc-watermark"><i class="bi bi-check2-circle"></i></div>
            <div class="sc-label"><i class="bi bi-check2-circle"></i> Aprovados</div>
            <div class="sc-number"><?= $stats['aprovados'] ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card-modern sc-red">
            <div class="sc-watermark"><i class="bi bi-x-circle"></i></div>
            <div class="sc-label"><i class="bi bi-x-circle"></i> Reprovados</div>
            <div class="sc-number"><?= $stats['reprovados'] ?></div>
        </div>
    </div>
</div>

<div class="row g-2 mb-3 px-3">
    <div class="col-md-6"><div class="input-group input-group-sm">
        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="text" id="buscaDocModal" class="form-control border-start-0" placeholder="Pesquisar documento..." onkeyup="filtrarDocumentosModal()">
    </div></div>
    <div class="col-md-3"><select id="filtroStatusModal" class="form-select form-select-sm" onchange="filtrarDocumentosModal()">
        <option value="">Todos os Status</option>
        <option value="pendente">Pendente</option>
        <option value="ativo">Aprovado</option>
        <option value="inativo">Reprovado</option>
    </select></div>
    <div class="col-md-3">
        <button class="btn btn-outline-secondary btn-sm w-100" onclick="limparFiltrosDocs()">Limpar</button>
    </div>
</div>

<script>
function filtrarDocumentosModal() {
    const norm = s => s.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
    const busca  = norm(document.getElementById('buscaDocModal').value.trim());
    const status = document.getElementById('filtroStatusModal').value;

    document.querySelectorAll('#corpo_tabela_docs .linha-doc').forEach(tr => {
        const nomeDoc   = norm(tr.dataset.nome || '');
        const stDoc     = tr.dataset.status || '';
        const bateBusca = busca  === '' || nomeDoc.startsWith(busca);
        const bateStatus = status === '' || stDoc === status;
        tr.style.display = (bateBusca && bateStatus) ? '' : 'none';
    });
}

function limparFiltrosDocs() {
    document.getElementById('buscaDocModal').value    = '';
    document.getElementById('filtroStatusModal').value = '';
    filtrarDocumentosModal();
}
</script>

<div class="card border-0 shadow-sm">
    <div class="card-body">
    <h5 class="fw-bold mb-3">Documentos dos Alunos</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light"><tr class="text-muted small">
                <th>DOCUMENTO</th><th>PROJETO</th><th>ENVIADO EM</th><th>STATUS</th><th class="text-center">AÇÕES</th>
            </tr></thead>
            <tbody id="corpo_tabela_docs">
                <?php foreach ($documentos as $doc):
                    $s = $doc['status'];
                    $badgeClass = $status_class[$s] ?? 'bg-secondary';
                    $badgeLabel = $status_label[$s] ?? ucfirst($s);
                    $exibirNome = !empty($doc['titulo']) ? $doc['titulo'] : $doc['tipo'];
                ?>
                <tr class="linha-doc" data-nome="<?= htmlspecialchars(strtolower($exibirNome)) ?>" data-status="<?= htmlspecialchars($s) ?>">
                    <td><div class="d-flex align-items-center"><i class="bi <?= getIconeArquivo($doc['tipo']) ?> fs-4 me-3"></i>
                    <span class="fw-bold text-dark"><?= htmlspecialchars($exibirNome) ?></span></div></td>
                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($doc['nome_projeto']) ?></span></td>
                    <td><?= date('d/m/Y H:i', strtotime($doc['data_registro'])) ?></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span></td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="<?= $doc['caminho'] ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <?php if ($s === 'pendente'): ?>
                                <button class="btn btn-sm btn-outline-success ms-1" onclick="alterarStatusDoc(<?= $doc['id_producao'] ?>, 'ativo', this)"><i class="bi bi-check2"></i></button>
                                <button class="btn btn-sm btn-outline-danger ms-1" onclick="alterarStatusDoc(<?= $doc['id_producao'] ?>, 'inativo', this)"><i class="bi bi-x"></i></button>
                            <?php else: ?>
                                <div class="btn-group ms-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-pencil-square"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li><a class="dropdown-item" href="#" onclick="alterarStatusDoc(<?= $doc['id_producao'] ?>, 'pendente', this)">Pendente</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="alterarStatusDoc(<?= $doc['id_producao'] ?>, 'ativo', this)">Aprovado</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="alterarStatusDoc(<?= $doc['id_producao'] ?>, 'inativo', this)">Reprovado</a></li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </div>
</div>
