<?php
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario || !str_contains(strtolower($_SESSION['perfil'] ?? ''), 'professor')) {
    http_response_code(401);
    echo json_encode([]);
    exit;
}

require_once __DIR__ . '/../conexao/conexao.php';
header('Content-Type: application/json');

require __DIR__ . '/gerar-notificacoes.php';

echo json_encode($notificacoes);
