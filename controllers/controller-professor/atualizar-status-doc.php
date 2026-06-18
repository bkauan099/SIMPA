<?php
ob_start();
session_start();
require_once '../../conexao/conexao.php';
ob_end_clean();

header('Content-Type: application/json');

$id_professor = $_SESSION['id_usuario'] ?? 0;
if (!$id_professor) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão inválida']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido']);
    exit;
}

// MIGRADO: documentos_projeto → producoes | id_documento → id_producao
$id_doc = intval($_POST['id_documento'] ?? 0);
$status = trim($_POST['status'] ?? '');

// Mesmo vocabulário usado pelo aluno (model/Aluno.php, toggle-concluido.php):
// pendente | concluido (aprovado) | cancelado (reprovado) | refazer (corrigir)
$statusValidos = ['pendente', 'concluido', 'cancelado', 'refazer'];
if (!$id_doc || !in_array($status, $statusValidos, true)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos.']);
    exit;
}

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
    $stmt->execute([':status' => $status, ':id' => $id_doc, ':prof' => $id_professor]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Documento não encontrado ou sem permissão']);
        exit;
    }

    echo json_encode(['sucesso' => true]);
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar o documento.']);
}
