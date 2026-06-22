<?php
session_start();
header('Content-Type: application/json');
require_once '../../conexao/conexao.php';
require_once '../../model/Usuario.php';

$id_professor = $_SESSION['id_usuario'] ?? null;
if (!$id_professor) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada.']);
    exit;
}

$usuarioModel = new Usuario($pdo);

// Pegamos os dados do POST
$acao = $_POST['acao'] ?? '';
$id_usuario = $_POST['id_usuario'] ?? null;
$id_projeto = $_POST['id_projeto'] ?? null;

// --- ADICIONE ESTA LINHA PARA CAPTURAR A CH ---
$carga_horaria = $_POST['carga_horaria'] ?? 0;
// ----------------------------------------------

if (!$id_usuario || !$id_projeto) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos.']);
    exit;
}

// Garante que o professor logado participa deste projeto antes de
// vincular/remover qualquer aluno (evita IDOR entre orientadores)
$stmtOwner = $pdo->prepare(
    "SELECT 1 FROM participacao WHERE id_projeto = :proj AND id_usuario = :uid LIMIT 1"
);
$stmtOwner->execute([':proj' => $id_projeto, ':uid' => $id_professor]);
if (!$stmtOwner->fetch()) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Você não tem permissão sobre este projeto.']);
    exit;
}

if ($acao == 'vincular') {
    $resultado = $usuarioModel->vincularAoProjeto($id_usuario, $id_projeto, $carga_horaria);

    if ($resultado['sucesso']) {
        echo json_encode(['sucesso' => true]);
    } else {
        // Se o erro for 'duplicado', avisamos o professor
        $mensagem = (isset($resultado['erro']) && $resultado['erro'] === 'duplicado')
            ? "Este aluno já está cadastrado neste projeto."
            : "Erro técnico ao vincular aluno.";

        echo json_encode(['sucesso' => false, 'mensagem' => $mensagem]);
    }
    exit;
}

if ($acao == 'remover') {
    $sucesso = $usuarioModel->removerDoProjeto($id_usuario, $id_projeto);
    echo json_encode(['sucesso' => $sucesso]);
    exit;
}
