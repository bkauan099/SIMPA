<?php
session_start();
require_once __DIR__ . '/../conexao/conexao.php';
header('Content-Type: application/json');

$id_professor = $_SESSION['id_usuario'] ?? 0;
$id_projeto   = (int)($_GET['id_projeto'] ?? 0);

if (!$id_professor || !$id_projeto) { echo '[]'; exit; }

// Verifica se o professor coordena o projeto
$stmtCheck = $pdo->prepare("
    SELECT 1 FROM participacao
    WHERE id_usuario = :prof AND id_projeto = :proj
      AND (funcao ILIKE '%professor%' OR funcao ILIKE '%coordenador%' OR funcao ILIKE '%orientador%')
    LIMIT 1
");
$stmtCheck->execute([':prof' => $id_professor, ':proj' => $id_projeto]);
if (!$stmtCheck->fetchColumn()) { echo '[]'; exit; }

$stmt = $pdo->prepare("
    SELECT u.id_usuario, u.nome, u.matricula
    FROM participacao pa
    JOIN usuarios u ON u.id_usuario = pa.id_usuario
    WHERE pa.id_projeto = :proj
      AND pa.id_usuario != :prof
      AND (pa.funcao ILIKE '%aluno%' OR pa.funcao NOT ILIKE '%professor%')
    ORDER BY u.nome ASC
");
$stmt->execute([':proj' => $id_projeto, ':prof' => $id_professor]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
