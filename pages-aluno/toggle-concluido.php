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
    $aluno = new Aluno($pdo);
    $novoConcluido = $aluno->toggleConcluido($id, $id_usuario);
    echo json_encode(['ok' => true, 'concluido' => $novoConcluido]);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'erro' => $e->getMessage()]);
}
