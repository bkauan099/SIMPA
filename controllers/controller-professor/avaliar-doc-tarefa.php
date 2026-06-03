<?php
ob_start();
session_start();
require_once '../../conexao/conexao.php';
ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

$id_professor = $_SESSION['id_usuario'] ?? 0;
if (!$id_professor) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão inválida']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido']);
    exit;
}

$id_producao = intval($_POST['id_producao'] ?? 0);
$acao        = trim($_POST['acao'] ?? '');

if (!$id_producao || !in_array($acao, ['aprovar', 'reprovar'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos']);
    exit;
}

$novoStatus = ($acao === 'aprovar') ? 'ativo' : 'inativo';

try {
    $stmt = $pdo->prepare("
        UPDATE producoes SET status = :status
        WHERE id_producao = :id
          AND id_projeto IN (
              SELECT id_projeto FROM participacao
              WHERE id_usuario = :prof
                AND (funcao ILIKE '%professor%' OR funcao ILIKE '%coordenador%' OR funcao ILIKE '%orientador%')
          )
    ");
    $stmt->execute([':status' => $novoStatus, ':id' => $id_producao, ':prof' => $id_professor]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Documento não encontrado ou sem permissão']);
        exit;
    }

    echo json_encode(['sucesso' => true, 'novo_status' => $novoStatus]);
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
}
