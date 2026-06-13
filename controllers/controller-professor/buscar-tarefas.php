<?php
session_start();
require_once '../../conexao/conexao.php';

$id_professor = $_SESSION['id_usuario'] ?? 0;
$modo = $_GET['modo'] ?? 'rows';

// Detalhe de uma tarefa (HTML ou JSON)
if ($modo === 'detalhe' || $modo === 'detalhe_json') {
    $id = trim($_GET['id'] ?? '');
    if (!$id) {
        if ($modo === 'detalhe_json') { header('Content-Type: application/json'); echo json_encode(['erro' => 'ID inválido']); }
        else echo "<p class='text-danger'>ID inválido.</p>";
        exit;
    }
    try {
        $stmt = $pdo->prepare("SELECT a.*, u.nome as nome_aluno, p.titulo as nome_projeto
            FROM agenda_items a
            LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
            LEFT JOIN projetos p ON a.id_projeto = p.id_projeto
            WHERE a.id = :id");
        $stmt->execute([':id' => $id]);
        $tarefa = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tarefa) {
            if ($modo === 'detalhe_json') { header('Content-Type: application/json'); echo json_encode(['erro' => 'Tarefa não encontrada']); }
            else echo "<p class='text-danger'>Tarefa não encontrada.</p>";
            exit;
        }

        if ($modo === 'detalhe_json') {
            header('Content-Type: application/json');
            echo json_encode($tarefa);
            exit;
        }

        $prioLabels  = ['alta' => 'Alta', 'media' => 'Média', 'baixa' => 'Baixa'];
        $prioClasses = ['alta' => 'bg-danger', 'media' => 'bg-warning text-dark', 'baixa' => 'bg-secondary'];
        $stLabels    = ['pendente' => 'Pendente', 'concluido' => 'Concluída', 'cancelado' => 'Cancelada', 'ativo' => 'Ativo', 'inativo' => 'Inativo'];
        $stClasses   = ['pendente' => 'bg-warning-subtle text-warning fw-semibold', 'concluido' => 'bg-success-subtle text-success fw-semibold', 'cancelado' => 'bg-danger-subtle text-danger fw-semibold', 'ativo' => 'bg-success-subtle text-success fw-semibold', 'inativo' => 'bg-danger-subtle text-danger fw-semibold'];

        $prio   = $tarefa['prioridade']    ?? 'media';
        // Status derivado da producao mais recente vinculada por id_agenda_item
        $stmtSt = $pdo->prepare("SELECT status FROM producoes WHERE titulo = :titulo AND id_projeto = :id_projeto ORDER BY id_producao DESC LIMIT 1");
        $stmtSt->execute([':titulo' => $tarefa['titulo'], ':id_projeto' => $tarefa['id_projeto']]);
        $statusRow = $stmtSt->fetch(PDO::FETCH_ASSOC);
        $status = $statusRow['status'] ?? 'pendente';

        // Busca documentos enviados pelo aluno para esta tarefa (vinculados por id_agenda_item)
        $docs = [];
        $stmtDocs = $pdo->prepare("
            SELECT id_producao, tipo, caminho, status
            FROM producoes
            WHERE titulo = :titulo AND id_projeto = :id_projeto
            ORDER BY id_producao ASC
        ");
        $stmtDocs->execute([':titulo' => $tarefa['titulo'], ':id_projeto' => $tarefa['id_projeto']]);
        $docs = $stmtDocs->fetchAll(PDO::FETCH_ASSOC);

        $docStatusLabel = ['pendente' => 'Pendente', 'concluido' => 'Aprovado', 'cancelado' => 'Reprovado', 'ativo' => 'Aprovado', 'inativo' => 'Reprovado'];
        $docStatusClass = ['pendente' => 'bg-warning-subtle text-warning fw-semibold', 'concluido' => 'bg-success-subtle text-success fw-semibold', 'cancelado' => 'bg-danger-subtle text-danger fw-semibold', 'ativo' => 'bg-success-subtle text-success fw-semibold', 'inativo' => 'bg-danger-subtle text-danger fw-semibold'];

        // Coluna esquerda: detalhes da tarefa
        $colDetalhes = "<dl class='row mb-0'>
            <dt class='col-sm-5 text-muted small'>Título</dt>
            <dd class='col-sm-7 fw-bold'>" . htmlspecialchars($tarefa['titulo']) . "</dd>
            <dt class='col-sm-5 text-muted small'>Aluno</dt>
            <dd class='col-sm-7'>" . htmlspecialchars($tarefa['nome_aluno'] ?? 'Não atribuído') . "</dd>
            <dt class='col-sm-5 text-muted small'>Projeto</dt>
            <dd class='col-sm-7'>" . htmlspecialchars($tarefa['nome_projeto'] ?? '—') . "</dd>
            <dt class='col-sm-5 text-muted small'>Prazo</dt>
            <dd class='col-sm-7'>" . ($tarefa['data'] ? date('d/m/Y', strtotime($tarefa['data'])) : '—') . "</dd>
            <dt class='col-sm-5 text-muted small'>Prioridade</dt>
            <dd class='col-sm-7'><span class='badge " . ($prioClasses[$prio] ?? 'bg-secondary') . "'>" . ($prioLabels[$prio] ?? ucfirst($prio)) . "</span></dd>
            <dt class='col-sm-5 text-muted small'>Status</dt>
            <dd class='col-sm-7'><span class='badge " . ($stClasses[$status] ?? 'bg-secondary') . "'>" . ($stLabels[$status] ?? ucfirst($status)) . "</span></dd>
            <dt class='col-sm-5 text-muted small'>Descrição</dt>
            <dd class='col-sm-7'>" . (empty($tarefa['descricao']) ? '—' : nl2br(htmlspecialchars($tarefa['descricao']))) . "</dd>
        </dl>";

        // Coluna direita: documentos anexados
        if (empty($docs)) {
            $colDocs = "<div class='text-center py-4 text-muted'>
                <i class='bi bi-paperclip' style='font-size:2rem;display:block;margin-bottom:8px;'></i>
                <small>Nenhum documento enviado</small>
            </div>";
        } else {
            $colDocs = "<div class='d-flex flex-column gap-2'>";
            foreach ($docs as $doc) {
                $ds     = $doc['status'] ?? 'pendente';
                $dLabel = $docStatusLabel[$ds] ?? ucfirst($ds);
                $dClass = $docStatusClass[$ds] ?? 'bg-secondary';
                $caminho = htmlspecialchars($doc['caminho']);
                $nome    = htmlspecialchars($doc['tipo']);
                $idProd  = intval($doc['id_producao']);

                $btnAprovar = $btnReprovar = '';
                if ($ds !== 'concluido') {
                    $btnAprovar = "<button class='btn btn-sm btn-success' onclick=\"avaliarDoc({$idProd},'aprovar',this)\" title='Aprovar'>
                        <i class='bi bi-check-lg'></i> Aprovar
                    </button>";
                }
                if ($ds !== 'cancelado') {
                    $btnReprovar = "<button class='btn btn-sm btn-outline-danger ms-1' onclick=\"avaliarDoc({$idProd},'reprovar',this)\" title='Reprovar'>
                        <i class='bi bi-x-lg'></i> Reprovar
                    </button>";
                }

                $colDocs .= "<div class='border rounded p-2' id='doc-{$idProd}'>
                    <div class='d-flex align-items-center gap-2 mb-2'>
                        <i class='bi bi-file-earmark-text text-primary flex-shrink-0'></i>
                        <span class='text-truncate small fw-medium flex-grow-1' title='{$nome}'>{$nome}</span>
                        <a href='controllers/controller-professor/servir-doc-tarefa.php?id={$idProd}' target='_blank' class='btn btn-sm btn-outline-primary flex-shrink-0' title='Visualizar'>
                            <i class='bi bi-eye'></i>
                        </a>
                    </div>
                    <div class='d-flex align-items-center gap-1 justify-content-between flex-wrap'>
                        <span class='badge {$dClass} doc-status-badge'>{$dLabel}</span>
                        <div class='doc-acoes'>{$btnAprovar}{$btnReprovar}</div>
                    </div>
                </div>";
            }
            $colDocs .= "</div>";
        }

        echo "<div class='row g-3'>
            <div class='col-md-6 border-end'>{$colDetalhes}</div>
            <div class='col-md-6'>
                <p class='fw-semibold small text-muted mb-2'><i class='bi bi-paperclip me-1'></i>DOCUMENTOS ENVIADOS</p>
                {$colDocs}
            </div>
        </div>";
    } catch (PDOException $e) {
        echo "<p class='text-danger'>Erro: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    exit;
}

// Linhas da tabela (modo padrão)
$busca          = trim($_GET['busca']      ?? '');
$filtro_projeto = intval($_GET['id_projeto'] ?? 0);
$filtro_status  = trim($_GET['status']     ?? '');

try {
    $sql = "SELECT DISTINCT ON (a.id)
        a.id, a.titulo, a.descricao, a.data,
        COALESCE(a.prioridade, 'media') AS prioridade,
        COALESCE(
            (SELECT pr.status FROM producoes pr
             WHERE pr.titulo = a.titulo AND pr.id_projeto = a.id_projeto
             ORDER BY pr.id_producao DESC LIMIT 1),
            'pendente'
        ) AS status_tarefa,
        a.id_usuario, a.id_projeto,
        u.nome AS nome_aluno,
        p.titulo AS nome_projeto,
        (SELECT COUNT(*) FROM producoes pr
         WHERE pr.titulo = a.titulo AND pr.id_projeto = a.id_projeto AND pr.status = 'pendente') AS docs_pendentes
    FROM agenda_items a
    JOIN participacao par ON a.id_projeto = par.id_projeto
    LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
    LEFT JOIN projetos p ON a.id_projeto = p.id_projeto
    WHERE par.id_usuario = :id_professor
      AND a.id_projeto IS NOT NULL";

    $params = [':id_professor' => $id_professor];

    if ($busca !== '') {
        $sql .= " AND (unaccent(a.titulo) ILIKE unaccent(:busca)
                    OR unaccent(COALESCE(u.nome, '')) ILIKE unaccent(:busca2))";
        $params[':busca']  = '%' . $busca . '%';
        $params[':busca2'] = '%' . $busca . '%';
    }
    if ($filtro_projeto > 0) {
        $sql .= " AND a.id_projeto = :id_projeto";
        $params[':id_projeto'] = $filtro_projeto;
    }
    if ($filtro_status !== '') {
        $sql .= " AND COALESCE(
            (SELECT pr.status FROM producoes pr
             WHERE pr.titulo = a.titulo AND pr.id_projeto = a.id_projeto
             ORDER BY pr.id_producao DESC LIMIT 1),
            'pendente') = :status";
        $params[':status'] = $filtro_status;
    }

    $sql .= " ORDER BY a.id, a.data ASC NULLS LAST";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tarefas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($tarefas)) {
        echo "<tr><td colspan='7' class='text-center py-5 text-muted'>
            <i class='bi bi-clipboard-x mb-2' style='font-size:2rem;display:block;'></i>
            <p class='fw-bold m-0'>Nenhuma tarefa encontrada</p>
        </td></tr>";
    } else {
        $prioLabels  = ['alta' => 'Alta', 'media' => 'Média', 'baixa' => 'Baixa'];
        $prioClasses = ['alta' => 'bg-danger', 'media' => 'bg-warning text-dark', 'baixa' => 'bg-secondary'];
        $stLabels    = ['pendente' => 'Pendente', 'concluido' => 'Concluída', 'cancelado' => 'Cancelada', 'ativo' => 'Ativo', 'inativo' => 'Inativo'];
        $stClasses   = ['pendente' => 'bg-warning-subtle text-warning fw-semibold', 'concluido' => 'bg-success-subtle text-success fw-semibold', 'cancelado' => 'bg-danger-subtle text-danger fw-semibold', 'ativo' => 'bg-success-subtle text-success fw-semibold', 'inativo' => 'bg-danger-subtle text-danger fw-semibold'];

        foreach ($tarefas as $t) {
            $prio       = $t['prioridade']    ?? 'media';
            $status     = $t['status_tarefa'] ?? 'pendente';
            $nomeAluno  = $t['nome_aluno']    ?? 'N/A';
            $prazo      = $t['data'] ? date('d/m/Y', strtotime($t['data'])) : '—';
            $id         = htmlspecialchars($t['id'], ENT_QUOTES);
            $prioClass  = $prioClasses[$prio]   ?? 'bg-secondary';
            $prioLabel  = $prioLabels[$prio]    ?? ucfirst($prio);
            $stClass    = $stClasses[$status]   ?? 'bg-secondary';
            $stLabel    = $stLabels[$status]    ?? ucfirst($status);

            $temDocPendente = intval($t['docs_pendentes'] ?? 0) > 0;
            $dotHtml = $temDocPendente
                ? "<span class='position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle' style='width:10px;height:10px;'></span>"
                : '';
            $acoes = "<div class='position-relative d-inline-block'>
                <button class='btn btn-sm btn-outline-primary' onclick=\"verTarefa('{$id}')\" title='Ver detalhes'><i class='bi bi-eye'></i></button>
                {$dotHtml}
            </div>";
            if ($status !== 'concluido') {
                $acoes .= " <button class='btn btn-sm btn-outline-secondary ms-1' onclick=\"editarTarefa('{$id}')\" title='Editar'>
                    <i class='bi bi-pencil'></i></button>";
                $acoes .= " <button class='btn btn-sm btn-outline-danger ms-1' onclick=\"confirmarExcluirTarefa('{$id}')\" title='Excluir'>
                    <i class='bi bi-trash'></i></button>";
            }

            echo "<tr>
                <td class='fw-medium'>" . htmlspecialchars($t['titulo']) . "</td>
                <td>
                    <div class='d-flex align-items-center gap-2'>
                        <img src='https://ui-avatars.com/api/?name=" . urlencode($nomeAluno) . "&background=e0f2fe&color=0369a1'
                             class='rounded-circle' width='26' alt=''>
                        " . htmlspecialchars($nomeAluno) . "
                    </div>
                </td>
                <td>" . htmlspecialchars($t['nome_projeto'] ?? '—') . "</td>
                <td>{$prazo}</td>
                <td><span class='badge {$prioClass}'>{$prioLabel}</span></td>
                <td><span class='badge {$stClass}'>{$stLabel}</span></td>
                <td class='text-center'>{$acoes}</td>
            </tr>";
        }
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='7' class='text-center text-danger py-3'>Erro: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
