<?php
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? 3;
require_once __DIR__ . '/../conexao/conexao.php';

header('Content-Type: application/json');

$id_producao = (int)($_POST['id_producao'] ?? 0);
if (!$id_producao) { echo json_encode(['ok' => false, 'erro' => 'ID inválido.']); exit; }

// Verificar que o arquivo pertence ao aluno via projeto
$stmt = $pdo->prepare(
    "SELECT p.caminho FROM producoes p
     JOIN participacao pa ON pa.id_projeto = p.id_projeto
     WHERE p.id_producao = :id AND pa.id_usuario = :uid"
);
$stmt->execute([':id' => $id_producao, ':uid' => $id_usuario]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) { echo json_encode(['ok' => false, 'erro' => 'Arquivo não encontrado.']); exit; }

$fullPath = __DIR__ . '/../' . $item['caminho'];
if (file_exists($fullPath)) unlink($fullPath);

$stmt = $pdo->prepare("DELETE FROM producoes WHERE id_producao = :id");
$ok = $stmt->execute([':id' => $id_producao]);

echo json_encode(['ok' => $ok]);
