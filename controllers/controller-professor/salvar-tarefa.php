<?php
session_start();
require_once '../../conexao/conexao.php';

header('Content-Type: application/json; charset=utf-8');

$id_professor = $_SESSION['id_usuario'] ?? null;
if (!$id_professor) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido.']);
    exit;
}

$id            = trim($_POST['id']           ?? '');
$titulo        = trim($_POST['titulo']       ?? '');
$id_projeto    = intval($_POST['id_projeto'] ?? 0);
$id_usuario    = intval($_POST['id_usuario'] ?? 0);
$data          = trim($_POST['data']         ?? '');
$hora          = trim($_POST['hora']         ?? '');
$prioridade    = trim($_POST['prioridade']   ?? 'media');
if (!in_array($prioridade, ['alta', 'media', 'baixa'], true)) $prioridade = 'media';
$descricao     = trim($_POST['descricao']    ?? '');

if (empty($titulo)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'O título é obrigatório.']);
    exit;
}
if ($id_projeto <= 0) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Selecione um projeto.']);
    exit;
}

// Garante que o professor logado participa deste projeto
$stmtOwner = $pdo->prepare(
    "SELECT 1 FROM participacao WHERE id_projeto = :proj AND id_usuario = :uid LIMIT 1"
);
$stmtOwner->execute([':proj' => $id_projeto, ':uid' => $id_professor]);
if (!$stmtOwner->fetch()) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Você não tem permissão sobre este projeto.']);
    exit;
}

// Se for edição, garante que a tarefa também pertence a um projeto do professor
if (!empty($id)) {
    $stmtTarefa = $pdo->prepare("
        SELECT 1 FROM agenda_items ai
        WHERE ai.id = :id
          AND ai.id_projeto IN (SELECT id_projeto FROM participacao WHERE id_usuario = :uid)
        LIMIT 1
    ");
    $stmtTarefa->execute([':id' => $id, ':uid' => $id_professor]);
    if (!$stmtTarefa->fetch()) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Tarefa não encontrada ou sem permissão.']);
        exit;
    }
}

$dataValida   = (!empty($data)) ? $data : null;
$horaValida   = (!empty($hora)) ? $hora : null;
$alunoValido  = ($id_usuario > 0) ? $id_usuario : null;

try {
    if (!empty($id)) {
        $stmt = $pdo->prepare("UPDATE agenda_items SET
            titulo     = :titulo,
            id_projeto = :id_projeto::int4,
            id_usuario = :id_usuario,
            data       = :data,
            hora       = :hora,
            prioridade = :prioridade,
            descricao  = :descricao
        WHERE id = :id");

        $stmt->execute([
            ':titulo'     => $titulo,
            ':id_projeto' => $id_projeto,
            ':id_usuario' => $alunoValido,
            ':data'       => $dataValida,
            ':hora'       => $horaValida,
            ':prioridade' => $prioridade,
            ':descricao'  => $descricao,
            ':id'         => $id,
        ]);

        echo json_encode(['sucesso' => true, 'mensagem' => 'Tarefa atualizada com sucesso!']);
    } else {
        $stmt = $pdo->prepare("INSERT INTO agenda_items
            (titulo, id_projeto, id_usuario, data, hora, prioridade, descricao, tipo)
        VALUES
            (:titulo, :id_projeto::int4, :id_usuario, :data, :hora, :prioridade, :descricao, 'tarefa')");

        $stmt->execute([
            ':titulo'     => $titulo,
            ':id_projeto' => $id_projeto,
            ':id_usuario' => $alunoValido,
            ':data'       => $dataValida,
            ':hora'       => $horaValida,
            ':prioridade' => $prioridade,
            ':descricao'  => $descricao,
        ]);

        echo json_encode(['sucesso' => true, 'mensagem' => 'Tarefa cadastrada com sucesso!']);
    }
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no banco: ' . $e->getMessage()]);
}
