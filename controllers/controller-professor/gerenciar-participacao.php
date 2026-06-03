<?php
header('Content-Type: application/json');
require_once '../../conexao/conexao.php';
require_once '../../model/Usuario.php';

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
