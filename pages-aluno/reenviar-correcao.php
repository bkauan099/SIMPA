<?php
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) { echo json_encode(['ok' => false, 'erro' => 'Sessão expirada']); exit; }
require_once __DIR__ . '/../conexao/conexao.php';

$id_projeto = $_POST['id_projeto'] ?? '';
$titulo     = $_POST['titulo']     ?? '';

if (!$id_projeto || !$titulo) { echo json_encode(['ok' => false, 'erro' => 'Dados inválidos']); exit; }

// Verifica que o aluno é dono da tarefa
$stmt = $pdo->prepare("
    SELECT ai.id FROM agenda_items ai
    WHERE ai.id_usuario = :id_usuario AND ai.id_projeto = :id_projeto AND ai.titulo = :titulo
    LIMIT 1
");
$stmt->execute([':id_usuario' => $id_usuario, ':id_projeto' => $id_projeto, ':titulo' => $titulo]);
if (!$stmt->fetch()) { echo json_encode(['ok' => false, 'erro' => 'Não autorizado']); exit; }

// Muda o documento de refazer → pendente
$stmt = $pdo->prepare("
    UPDATE producoes SET status = 'pendente'
    WHERE id_projeto = :id_projeto AND titulo = :titulo AND status = 'refazer'
");
$stmt->execute([':id_projeto' => $id_projeto, ':titulo' => $titulo]);

echo json_encode(['ok' => true]);
