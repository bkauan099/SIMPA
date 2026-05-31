<?php
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? null;
require_once __DIR__ . '/../conexao/conexao.php';

header('Content-Type: application/json');

if (!$id_usuario) {
    echo json_encode(['ok' => false, 'erro' => 'Sessão expirada.']);
    exit;
}

$id_producao = (int)($_POST['id_producao'] ?? 0);
if (!$id_producao) { echo json_encode(['ok' => false, 'erro' => 'ID inválido.']); exit; }

// Obter matrícula do aluno logado — usada como prova de ownership
$stmt = $pdo->prepare("SELECT matricula FROM usuarios WHERE id_usuario = :uid");
$stmt->execute([':uid' => $id_usuario]);
$matricula = $stmt->fetchColumn();

if (!$matricula) {
    echo json_encode(['ok' => false, 'erro' => 'Usuário inválido.']);
    exit;
}

// O caminho de qualquer arquivo enviado por este aluno começa com este prefixo.
// Verificar pelo prefixo garante que um colega de projeto não pode remover
// um arquivo que não foi enviado por ele.
$prefixo = 'uploads/alunos/' . $matricula . '/';

$stmt = $pdo->prepare(
    "SELECT caminho FROM producoes
     WHERE id_producao = :id
       AND caminho LIKE :prefix
       AND status != 'inativo'"
);
$stmt->execute([':id' => $id_producao, ':prefix' => $prefixo . '%']);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo json_encode(['ok' => false, 'erro' => 'Arquivo não encontrado.']);
    exit;
}

$fullPath = __DIR__ . '/../' . $item['caminho'];
if (file_exists($fullPath)) unlink($fullPath);

$stmt = $pdo->prepare("DELETE FROM producoes WHERE id_producao = :id");
$ok = $stmt->execute([':id' => $id_producao]);

echo json_encode(['ok' => $ok]);
