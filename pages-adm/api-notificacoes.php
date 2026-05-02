<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['id_usuario'])) {
    echo json_encode(['erro' => 'Não autenticado']);
    exit;
}

require_once '../conexao/conexao.php';
require_once '../model/NotificacaoModel.php';

$acao = $_GET['acao'] ?? 'listar';

try {
    $model = new NotificacaoModel($pdo);
    if ($acao === 'total') {
        echo json_encode(['total' => $model->totalNaoLidas()]);
    } else {
        $notifs = $model->listarParaAdm();
        echo json_encode(['sucesso' => true, 'notificacoes' => $notifs, 'total' => count($notifs)]);
    }
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage(), 'notificacoes' => []]);
}
?>
