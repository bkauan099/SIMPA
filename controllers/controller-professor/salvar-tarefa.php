<?php
session_start();
require_once '../../conexao/conexao.php';

header('Content-Type: application/json; charset=utf-8');

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
$status_tarefa = trim($_POST['status_tarefa'] ?? 'pendente');
$descricao     = trim($_POST['descricao']    ?? '');

if (empty($titulo)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'O título é obrigatório.']);
    exit;
}
if ($id_projeto <= 0) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Selecione um projeto.']);
    exit;
}

$dataValida   = (!empty($data)) ? $data : null;
$horaValida   = (!empty($hora)) ? $hora : null;
$alunoValido  = ($id_usuario > 0) ? $id_usuario : null;

try {
    if (!empty($id)) {
        $stmt = $pdo->prepare("UPDATE agenda_items SET
            titulo        = :titulo,
            id_projeto    = :id_projeto::int4,
            id_usuario    = :id_usuario,
            data          = :data,
            hora          = :hora,
            prioridade    = :prioridade,
            status_tarefa = :status_tarefa,
            descricao     = :descricao,
            concluido     = :concluido
        WHERE id = :id");

        $stmt->execute([
            ':titulo'        => $titulo,
            ':id_projeto'    => $id_projeto,
            ':id_usuario'    => $alunoValido,
            ':data'          => $dataValida,
            ':hora'          => $horaValida,
            ':prioridade'    => $prioridade,
            ':status_tarefa' => $status_tarefa,
            ':descricao'     => $descricao,
            ':concluido'     => ($status_tarefa === 'concluida') ? 'true' : 'false',
            ':id'            => $id,
        ]);

        echo json_encode(['sucesso' => true, 'mensagem' => 'Tarefa atualizada com sucesso!']);
    } else {
        $stmt = $pdo->prepare("INSERT INTO agenda_items
            (titulo, id_projeto, id_usuario, data, hora, prioridade, status_tarefa, descricao, concluido, tipo)
        VALUES
            (:titulo, :id_projeto::int4, :id_usuario, :data, :hora, :prioridade, :status_tarefa, :descricao, :concluido, 'tarefa')");

        $stmt->execute([
            ':titulo'        => $titulo,
            ':id_projeto'    => $id_projeto,
            ':id_usuario'    => $alunoValido,
            ':data'          => $dataValida,
            ':hora'          => $horaValida,
            ':prioridade'    => $prioridade,
            ':status_tarefa' => $status_tarefa,
            ':descricao'     => $descricao,
            ':concluido'     => ($status_tarefa === 'concluida') ? 'true' : 'false',
        ]);

        echo json_encode(['sucesso' => true, 'mensagem' => 'Tarefa cadastrada com sucesso!']);
    }
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no banco: ' . $e->getMessage()]);
}
