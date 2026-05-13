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
        // O status_doc é um tipo ENUM ou customizado no seu Postgres, 
        // então garantimos que o valor enviado é válido.
        $sql = "UPDATE documentos_projeto SET status = :status WHERE id_documento = :id";
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
