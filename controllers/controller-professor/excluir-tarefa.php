<?php
session_start();
require_once '../../conexao/conexao.php';

header('Content-Type: application/json; charset=utf-8');

$id_professor = $_SESSION['id_usuario'] ?? null;
if (!$id_professor) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada.']);
    exit;
}

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
    // Só exclui se a tarefa pertencer a um projeto em que o professor logado participa
    $stmt = $pdo->prepare("
        DELETE FROM agenda_items
        WHERE id = :id
          AND id_projeto IN (
              SELECT id_projeto FROM participacao WHERE id_usuario = :prof
          )
    ");
    $stmt->execute([':id' => $id, ':prof' => $id_professor]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Tarefa excluída com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Tarefa não encontrada ou sem permissão.']);
    }
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao excluir a tarefa.']);
}
