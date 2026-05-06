<?php
require_once '../../conexao/conexao.php';

$id_projeto = $_GET['id_projeto'] ?? null;

if (!$id_projeto) {
    echo "<tr><td colspan='3' class='text-center text-danger'>Projeto não identificado.</td></tr>";
    exit;
}

// No seu arquivo buscar-documentos.php

try {
    // 1. Adicione a coluna 'descricao' na sua consulta SQL
    $sql = "SELECT id_documento, nome_original, descricao, data_upload, caminho_arquivo 
            FROM documentos_projeto 
            WHERE id_projeto = :id_projeto 
            ORDER BY data_upload DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_projeto' => $id_projeto]);
    $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($docs)) {
        echo "<tr><td colspan='4' class='text-center py-3 text-muted'>Nenhum documento anexado ainda.</td></tr>";
    } else {
        foreach ($docs as $doc) {
            $data = date('d/m/Y', strtotime($doc['data_upload']));

            // 2. Lógica para exibir "sem descrição" caso o campo esteja vazio ou nulo
            $descricao = (!empty($doc['descricao']))
                ? htmlspecialchars($doc['descricao'])
                : '<span class="text-muted italic small">sem descrição</span>';

            echo "
            <tr>
                <td class='fw-medium'>{$doc['nome_original']}</td>
                <td class='small'>{$descricao}</td> <!-- Coluna de Descrição -->
                <td class='small text-muted'>{$data}</td>
                <td class='text-center'>
                    <div class='btn-group'>
                        <a href='uploads/documentos/{$doc['caminho_arquivo']}' target='_blank' class='btn btn-sm btn-outline-primary'>
                            <i class='bi bi-eye'></i>
                        </a>
                        <button onclick='removerDocumento({$doc['id_documento']}, {$id_projeto})' class='btn btn-sm btn-outline-danger'>
                            <i class='bi bi-trash'></i>
                        </button>
                    </div>
                </td>
            </tr>";
        }
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='4' class='text-center text-danger'>Erro ao carregar arquivos.</td></tr>";
}
