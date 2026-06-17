<?php
session_start();
require_once '../../conexao/conexao.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido.']);
    exit;
}

$id = trim($_POST['id'] ?? '');

if (empty($id)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido.']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM agenda_items WHERE id = :id");
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Tarefa excluída com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Tarefa não encontrada.']);
    }
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no banco: ' . $e->getMessage()]);
}
