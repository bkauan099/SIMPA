<?php
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? null;
header('Content-Type: application/json');
if (!$id_usuario) { echo json_encode(['ok' => false, 'erro' => 'Sessão expirada']); exit; }
require_once __DIR__ . '/../conexao/conexao.php';

$id_projeto = (int)($_POST['id_projeto'] ?? 0);
$titulo     = $_POST['titulo']     ?? '';

if (!$id_projeto || !$titulo) { echo json_encode(['ok' => false, 'erro' => 'Dados inválidos']); exit; }

// Busca matrícula do aluno logado — usada para garantir ownership no UPDATE
$stmtMat = $pdo->prepare("SELECT matricula FROM usuarios WHERE id_usuario = :id");
$stmtMat->execute([':id' => $id_usuario]);
$matricula = $stmtMat->fetchColumn();
if (!$matricula) { echo json_encode(['ok' => false, 'erro' => 'Não autorizado']); exit; }

// Verifica que o aluno é dono da tarefa
$stmt = $pdo->prepare("
    SELECT ai.id, ai.data, ai.hora FROM agenda_items ai
    WHERE ai.id_usuario = :id_usuario AND ai.id_projeto = :id_projeto AND ai.titulo = :titulo
    LIMIT 1
");
$stmt->execute([':id_usuario' => $id_usuario, ':id_projeto' => $id_projeto, ':titulo' => $titulo]);
$tarefaInfo = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$tarefaInfo) { echo json_encode(['ok' => false, 'erro' => 'Não autorizado']); exit; }

// Bloquear reenvio se o prazo já passou
$prazoPassou = $tarefaInfo['data'] < date('Y-m-d');
if (!$prazoPassou && !empty($tarefaInfo['hora'])) {
    $prazoComHora = new DateTime($tarefaInfo['data'] . ' ' . substr($tarefaInfo['hora'], 0, 5));
    $prazoPassou  = new DateTime() > $prazoComHora;
}
if ($prazoPassou) {
    echo json_encode(['ok' => false, 'erro' => 'Prazo encerrado. Não é possível reenviar esta atividade.']);
    exit;
}

// Muda o documento de refazer → pendente filtrando pela matrícula do aluno
// (evita afetar documentos de outro aluno com mesmo título no mesmo projeto)
$stmt = $pdo->prepare("
    UPDATE producoes SET status = 'pendente'
    WHERE id_projeto = :id_projeto AND titulo = :titulo AND status = 'refazer'
      AND caminho LIKE :prefix
");
$stmt->execute([
    ':id_projeto' => $id_projeto,
    ':titulo'     => $titulo,
    ':prefix'     => 'uploads/producoes/aluno/' . $matricula . '/%',
]);

echo json_encode(['ok' => true]);
