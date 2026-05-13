<?php
require_once '../../conexao/conexao.php';

$id_projeto = isset($_GET['id_projeto']) ? intval($_GET['id_projeto']) : 0;
$busca = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filtro = isset($_GET['status']) ? trim($_GET['status']) : '';

try {
    $sql = "SELECT d.*, p.titulo as nome_projeto FROM documentos_projeto d 
            LEFT JOIN projetos p ON d.id_projeto = p.id_projeto WHERE 1=1";
    $params = [];

    if ($id_projeto > 0) {
        $sql .= " AND d.id_projeto = :id_projeto";
        $params[':id_projeto'] = $id_projeto;
    }
    if ($busca !== '') {
        $sql .= " AND (unaccent(COALESCE(d.titulo, d.nome_original)) ILIKE unaccent(:busca))";
        $params[':busca'] = '%' . $busca . '%';
    }
    if ($status_filtro !== '') {
        $sql .= " AND d.status = :status";
        $params[':status'] = $status_filtro;
    }

    $sql .= " ORDER BY d.data_upload DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($docs)) {
        echo "<tr><td colspan='4' class='text-center py-4 text-muted'>Nenhum documento encontrado.</td></tr>";
    } else {
        foreach ($docs as $doc) {
            $exibirNome = !empty($doc['titulo']) ? $doc['titulo'] : $doc['nome_original'];
            $data = date('d/m/Y', strtotime($doc['data_upload']));
            $status_class = ['pendente' => 'bg-warning text-dark', 'aprovado' => 'bg-success', 'reprovado' => 'bg-danger'];

            // SE FOR MODAL (id_projeto > 0): Layout de 4 colunas
            if ($id_projeto > 0) {
                echo "<tr>
                    <td><span class='fw-bold text-dark'>" . htmlspecialchars($exibirNome) . "</span></td>
                    <td class='small text-muted'>{$data}</td>
                    <td><span class='badge {$status_class[$doc['status']]}' style='font-size: 0.7rem;'>" . ucfirst($doc['status']) . "</span></td>
                    <td class='text-center'>
                        <div class='btn-group'>
                            <a href='{$doc['caminho_arquivo']}' target='_blank' class='btn btn-sm btn-outline-primary' title='Visualizar'>
                                <i class='bi bi-eye'></i>
                            </a>
                            <button type='button' class='btn btn-sm btn-outline-danger ms-1' 
                                    onclick='excluirDocumento({$doc['id_documento']})' title='Excluir'>
                                <i class='bi bi-trash'></i>
                            </button>
                        </div>
                    </td>
                </tr>";
            }
            // SE FOR PÁGINA GERAL: Layout completo com 5 colunas e ícones
            else {
                echo "<tr>
                    <td><span class='fw-bold text-dark'>" . htmlspecialchars($exibirNome) . "</span></td>
                    <td><span class='badge bg-light text-dark border'>" . htmlspecialchars($doc['nome_projeto']) . "</span></td>
                    <td>" . date('d/m/Y H:i', strtotime($doc['data_upload'])) . "</td>
                    <td><span class='badge {$status_class[$doc['status']]}'>" . ucfirst($doc['status']) . "</span></td>
                    <td class='text-center'>
                        <div class='btn-group'>
                            <a href='{$doc['caminho_arquivo']}' target='_blank' class='btn btn-sm btn-outline-primary'><i class='bi bi-eye'></i></a>
                        </div>
                    </td>
                </tr>";
            }
        }
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='5'>Erro: {$e->getMessage()}</td></tr>";
}
