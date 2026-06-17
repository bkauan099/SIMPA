<?php
session_start();
require_once '../../conexao/conexao.php';
header('Content-Type: application/json');

$id_professor = $_SESSION['id_usuario'] ?? null;
if (!$id_professor) { echo json_encode(['sucesso'=>false,'mensagem'=>'Sessão expirada.']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['sucesso'=>false,'mensagem'=>'Método inválido.']); exit; }

$id_producao = (int)($_POST['id_producao'] ?? 0);
if (!$id_producao) { echo json_encode(['sucesso'=>false,'mensagem'=>'ID inválido.']); exit; }

// Busca o registro verificando que pertence a um projeto do professor
$stmt = $pdo->prepare("
    SELECT pr.caminho, pr.tipo
    FROM producoes pr
    JOIN projetos pj ON pj.id_projeto = pr.id_projeto
    JOIN participacao pa ON pa.id_projeto = pj.id_projeto
    WHERE pr.id_producao = :id
      AND pa.id_usuario = :prof
      AND pr.caminho LIKE 'uploads/certificados/aluno/%'
      AND (pa.funcao ILIKE '%professor%' OR pa.funcao ILIKE '%coordenador%' OR pa.funcao ILIKE '%orientador%')
    LIMIT 1
");
$stmt->execute([':id' => $id_producao, ':prof' => $id_professor]);
$doc = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doc) { echo json_encode(['sucesso'=>false,'mensagem'=>'Certificado não encontrado ou sem permissão.']); exit; }

// Remove arquivo físico com proteção path traversal
$baseDir  = realpath(__DIR__ . '/../../uploads');
$fullPath = realpath(__DIR__ . '/../../' . $doc['caminho']);
if ($fullPath && $baseDir && str_starts_with($fullPath, $baseDir) && file_exists($fullPath)) {
    unlink($fullPath);
}

$stmtDel = $pdo->prepare("DELETE FROM producoes WHERE id_producao = :id");
$stmtDel->execute([':id' => $id_producao]);

echo json_encode(['sucesso'=>true]);
