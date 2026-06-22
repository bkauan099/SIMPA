<?php
session_start();
require_once '../../conexao/conexao.php';

$id_professor = $_SESSION['id_usuario'] ?? 0;
if (!$id_professor) {
    echo "<tr><td colspan='5' class='text-center py-4 text-muted'>Sessão expirada.</td></tr>";
    exit;
}

$id_projeto = isset($_GET['id_projeto']) ? intval($_GET['id_projeto']) : 0;
$busca = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filtro = isset($_GET['status']) ? trim($_GET['status']) : '';

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

try {
    // MIGRADO: documentos_projeto → producoes
    // d.id_documento → d.id_producao | d.nome_original → d.tipo | d.caminho_arquivo → d.caminho | d.data_upload → d.data_registro
    $sql = "SELECT d.*, p.titulo as nome_projeto FROM producoes d
            LEFT JOIN projetos p ON d.id_projeto = p.id_projeto
            WHERE d.id_projeto IN (SELECT id_projeto FROM participacao WHERE id_usuario = :prof)";
    $params = [':prof' => $id_professor];

    if ($id_projeto > 0) {
        $sql .= " AND d.id_projeto = :id_projeto";
        $params[':id_projeto'] = $id_projeto;
    }
    if ($busca !== '') {
        $sql .= " AND (unaccent(COALESCE(d.titulo, d.tipo)) ILIKE unaccent(:busca))";
        $params[':busca'] = '%' . $busca . '%';
    }
    if ($status_filtro !== '') {
        $sql .= " AND d.status = :status";
        $params[':status'] = $status_filtro;
    }

    $sql .= " ORDER BY d.data_registro DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($docs)) {
        echo "<tr><td colspan='4' class='text-center py-4 text-muted'>Nenhum documento encontrado.</td></tr>";
    } else {
        foreach ($docs as $doc) {
            $exibirNome = !empty($doc['titulo']) ? $doc['titulo'] : $doc['tipo'];
            $data = date('d/m/Y', strtotime($doc['data_registro']));
            $s = $doc['status'];
            $badgeClass = $status_class[$s] ?? 'bg-secondary';
            $badgeLabel = $status_label[$s] ?? ucfirst($s);

            if ($id_projeto > 0) {
                echo "<tr>
                    <td><span class='fw-bold text-dark'>" . htmlspecialchars($exibirNome) . "</span></td>
                    <td class='small text-muted'>{$data}</td>
                    <td><span class='badge {$badgeClass}' style='font-size: 0.7rem;'>{$badgeLabel}</span></td>
                    <td class='text-center'>
                        <div class='btn-group'>
                            <a href='controllers/controller-professor/servir-doc-tarefa.php?id={$doc['id_producao']}' target='_blank' class='btn btn-sm btn-outline-primary' title='Visualizar'>
                                <i class='bi bi-eye'></i>
                            </a>
                            <button type='button' class='btn btn-sm btn-outline-danger ms-1'
                                    onclick='excluirDocumento({$doc['id_producao']}, {$id_projeto})' title='Excluir'>
                                <i class='bi bi-trash'></i>
                            </button>
                        </div>
                    </td>
                </tr>";
            } else {
                echo "<tr>
                    <td><span class='fw-bold text-dark'>" . htmlspecialchars($exibirNome) . "</span></td>
                    <td><span class='badge bg-light text-dark border'>" . htmlspecialchars($doc['nome_projeto']) . "</span></td>
                    <td>" . date('d/m/Y H:i', strtotime($doc['data_registro'])) . "</td>
                    <td><span class='badge {$badgeClass}'>{$badgeLabel}</span></td>
                    <td class='text-center'>
                        <div class='btn-group'>
                            <a href='controllers/controller-professor/servir-doc-tarefa.php?id={$doc['id_producao']}' target='_blank' class='btn btn-sm btn-outline-primary'><i class='bi bi-eye'></i></a>
                        </div>
                    </td>
                </tr>";
            }
        }
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='5' class='text-center py-4 text-muted'>Erro ao buscar documentos.</td></tr>";
}
