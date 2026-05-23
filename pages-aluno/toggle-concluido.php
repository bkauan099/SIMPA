<?php
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? 3;
require_once __DIR__ . '/../conexao/conexao.php';
require_once __DIR__ . '/../model/Aluno.php';

header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
if (!$id) {
    echo json_encode(['ok' => false, 'erro' => 'ID ausente']);
    exit;
}

try {
    // Bloquear desfazer se prazo passou e já está concluído
    $stmt = $pdo->prepare("SELECT concluido::int AS concluido, data FROM agenda_items WHERE id = :id AND id_usuario = :uid");
    $stmt->execute([':id' => $id, ':uid' => $id_usuario]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item && (int)$item['concluido'] === 1 && $item['data'] < date('Y-m-d')) {
        echo json_encode(['ok' => false, 'erro' => 'Prazo encerrado. Não é possível desfazer.']);
        exit;
    }

    $aluno = new Aluno($pdo);
    $novoConcluido = $aluno->toggleConcluido($id, $id_usuario);
    echo json_encode(['ok' => true, 'concluido' => $novoConcluido]);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'erro' => $e->getMessage()]);
}
