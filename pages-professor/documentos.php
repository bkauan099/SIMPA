<?php
require_once 'conexao/conexao.php';

// 1. Filtros iniciais
$busca = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filtro = isset($_GET['status']) ? trim($_GET['status']) : '';

// 2. Estatísticas dos Cards
$stats = $pdo->query("SELECT COUNT(*) as total, 
    SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
    SUM(CASE WHEN status = 'aprovado' THEN 1 ELSE 0 END) as aprovados,
    SUM(CASE WHEN status = 'reprovado' THEN 1 ELSE 0 END) as reprovados 
    FROM documentos_projeto")->fetch(PDO::FETCH_ASSOC);

// 3. Query da Tabela (Filtro inicial se houver)
$sql = "SELECT d.*, p.titulo as nome_projeto FROM documentos_projeto d 
        LEFT JOIN projetos p ON d.id_projeto = p.id_projeto WHERE 1=1";
$params = [];
if (!empty($busca)) {
    $sql .= " AND (unaccent(COALESCE(d.titulo, d.nome_original)) ILIKE unaccent(?))";
    $params[] = "%$busca%";
}
if (!empty($status_filtro)) {
    $sql .= " AND d.status = ?";
    $params[] = $status_filtro;
}
$sql .= " ORDER BY d.data_upload DESC";
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
?>

<h3 class="fw-bold mb-1">Documentos</h3>
<p class="text-muted mb-4">Revise e aprove os documentos enviados pelos seus alunos</p>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3"><div class="stat-card"><div class="icon-circle bg-light-blue"><i class="bi bi-files"></i></div>
    <div><h4 class="mb-0 fw-bold"><?= $stats['total'] ?></h4><small class="text-muted">Total</small></div></div></div>
    <div class="col-sm-6 col-lg-3"><div class="stat-card"><div class="icon-circle bg-light-orange"><i class="bi bi-clock-history"></i></div>
    <div><h4 class="mb-0 fw-bold"><?= $stats['pendentes'] ?></h4><small class="text-muted">Aguard. Revisão</small></div></div></div>
    <div class="col-sm-6 col-lg-3"><div class="stat-card"><div class="icon-circle bg-light-blue"><i class="bi bi-check2-circle"></i></div>
    <div><h4 class="mb-0 fw-bold"><?= $stats['aprovados'] ?></h4><small class="text-muted">Aprovados</small></div></div></div>
    <div class="col-sm-6 col-lg-3"><div class="stat-card"><div class="icon-circle bg-light-orange"><i class="bi bi-x-circle"></i></div>
    <div><h4 class="mb-0 fw-bold"><?= $stats['reprovados'] ?></h4><small class="text-muted">Reprovados</small></div></div></div>
</div>

<div class="row g-2 mb-3 px-3">
    <div class="col-md-7"><div class="input-group input-group-sm">
        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="text" id="buscaDocModal" class="form-control border-start-0" placeholder="Pesquisar documento..." onkeyup="filtrarDocumentosModal()">
    </div></div>
    <div class="col-md-5"><select id="filtroStatusModal" class="form-select form-select-sm" onchange="filtrarDocumentosModal()">
        <option value="">Todos os Status</option>
        <option value="pendente">Pendente</option>
        <option value="aprovado">Aprovado</option>
        <option value="reprovado">Reprovado</option>
    </select></div>
</div>

<div class="content-card">
    <h5 class="fw-bold mb-3">Documentos dos Alunos</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light"><tr class="text-muted small">
                <th>DOCUMENTO</th><th>PROJETO</th><th>ENVIADO EM</th><th>STATUS</th><th class="text-center">AÇÕES</th>
            </tr></thead>
            <tbody id="corpo_tabela_docs">
                <?php foreach ($documentos as $doc): 
                    $status_class = ['pendente' => 'bg-warning text-dark', 'aprovado' => 'bg-success', 'reprovado' => 'bg-danger'];
                    $exibirNome = !empty($doc['titulo']) ? $doc['titulo'] : $doc['nome_original'];
                ?>
                <tr>
                    <td><div class="d-flex align-items-center"><i class="bi <?= getIconeArquivo($doc['nome_original']) ?> fs-4 me-3"></i>
                    <span class="fw-bold text-dark"><?= htmlspecialchars($exibirNome) ?></span></div></td>
                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($doc['nome_projeto']) ?></span></td>
                    <td><?= date('d/m/Y H:i', strtotime($doc['data_upload'])) ?></td>
                    <td><span class="badge <?= $status_class[$doc['status']] ?>"><?= ucfirst($doc['status']) ?></span></td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="<?= $doc['caminho_arquivo'] ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <?php if ($doc['status'] == 'pendente'): ?>
                                <button class="btn btn-sm btn-outline-success ms-1" onclick="alterarStatusDoc(<?= $doc['id_documento'] ?>, 'aprovado')"><i class="bi bi-check2"></i></button>
                                <button class="btn btn-sm btn-outline-danger ms-1" onclick="alterarStatusDoc(<?= $doc['id_documento'] ?>, 'reprovado')"><i class="bi bi-x"></i></button>
                            <?php else: ?>
                                <div class="btn-group ms-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-pencil-square"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li><a class="dropdown-item" href="#" onclick="alterarStatusDoc(<?= $doc['id_documento'] ?>, 'pendente')">Pendente</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="alterarStatusDoc(<?= $doc['id_documento'] ?>, 'aprovado')">Aprovado</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="alterarStatusDoc(<?= $doc['id_documento'] ?>, 'reprovado')">Reprovado</a></li>
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