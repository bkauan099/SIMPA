<?php
require_once '../../conexao/conexao.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_documento = $_POST['id_documento'] ?? null;

    if (!$id_documento) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'ID do documento não fornecido.']);
        exit;
    }

    try {
        // 1. Buscar o nome do arquivo no banco para poder apagar da pasta
        $sqlBusca = "SELECT caminho_arquivo FROM documentos_projeto WHERE id_documento = :id";
        $stmtBusca = $pdo->prepare($sqlBusca);
        $stmtBusca->execute([':id' => $id_documento]);
        $doc = $stmtBusca->fetch(PDO::FETCH_ASSOC);

        if ($doc) {
            $caminhoFisico = "../../uploads/documentos/" . $doc['caminho_arquivo'];

            // 2. Deletar o registro no Banco de Dados
            $sqlDelete = "DELETE FROM documentos_projeto WHERE id_documento = :id";
            $stmtDelete = $pdo->prepare($sqlDelete);
            $stmtDelete->execute([':id' => $id_documento]);

            // 3. Se deletou no banco, apaga o arquivo físico da pasta
            if (file_exists($caminhoFisico)) {
                unlink($caminhoFisico);
            }

            echo json_encode(['sucesso' => true]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Documento não encontrado.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao excluir: ' . $e->getMessage()]);
    }
}
