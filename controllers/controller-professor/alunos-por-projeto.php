<?php
session_start();
require_once '../../conexao/conexao.php';

header('Content-Type: application/json; charset=utf-8');

$id_professor = $_SESSION['id_usuario'] ?? null;
if (!$id_professor) {
    echo json_encode([]);
    exit;
}

$id_projeto = intval($_GET['id_projeto'] ?? 0);

if ($id_projeto <= 0) {
    echo json_encode([]);
    exit;
}

try {
    // Só lista alunos se o professor logado participar deste projeto
    $stmt = $pdo->prepare("SELECT u.id_usuario, u.nome
        FROM participacao p
        JOIN usuarios u ON p.id_usuario = u.id_usuario
        WHERE p.id_projeto = :id AND u.perfil = 'aluno'
          AND :id IN (SELECT id_projeto FROM participacao WHERE id_usuario = :prof)
        ORDER BY u.nome ASC");
    $stmt->execute([':id' => $id_projeto, ':prof' => $id_professor]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode([]);
}
