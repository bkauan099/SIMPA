<?php
ob_start();
require_once '../lib/Guard.php';
Guard::apenasAdmin();
header('Content-Type: application/json; charset=utf-8');
if (empty($_SESSION['id_usuario'])) { echo json_encode(['erro'=>'Não autenticado']); exit; }
require_once '../conexao/conexao.php';
require_once '../model/NotificacaoModel.php';
$acao = $_GET['acao'] ?? 'listar';
try {
    $m = new NotificacaoModel($pdo);
    if ($acao === 'total') { echo json_encode(['total' => $m->totalNaoLidas()]); }
    else { $n = $m->listarParaAdm(); echo json_encode(['sucesso'=>true,'notificacoes'=>$n,'total'=>count($n)]); }
} catch (Exception $e) { echo json_encode(['sucesso'=>false,'mensagem'=>$e->getMessage(),'notificacoes'=>[]]); }
?>
