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
    // O Model retorna true/false direto, então checamos assim:
    $sucesso = $usuarioModel->vincularAoProjeto($id_usuario, $id_projeto, $carga_horaria);

    if ($sucesso) {
        echo json_encode(['sucesso' => true]);
    } else {
        // Se deu erro, geralmente no seu banco é por duplicidade ou erro de constraint
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao vincular ou aluno já cadastrado.']);
    }
    exit;
}

if ($acao == 'remover') {
    $sucesso = $usuarioModel->removerDoProjeto($id_usuario, $id_projeto);
    echo json_encode(['sucesso' => $sucesso]);
    exit;
}
