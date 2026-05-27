<?php
require_once '../../conexao/conexao.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_doc = $_POST['id_documento'] ?? null;
    $status = $_POST['status'] ?? null;

    if (!$id_doc || !$status) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos.']);
        exit;
    }

    try {
        // MIGRADO: documentos_projeto → producoes
        // id_documento → id_producao
        $sql = "UPDATE producoes SET status = :status WHERE id_producao = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':status' => $status,
            ':id' => $id_doc
        ]);

        echo json_encode(['sucesso' => true]);
    } catch (PDOException $e) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no banco: ' . $e->getMessage()]);
    }
}
