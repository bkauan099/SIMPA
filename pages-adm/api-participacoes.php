<?php
ob_start();
require_once '../lib/Guard.php';
Guard::apenasAdmin();
header('Content-Type: application/json; charset=utf-8');

set_error_handler(function($errno, $errstr) {
    if (in_array($errno, [E_NOTICE, E_WARNING, E_DEPRECATED])) return true;
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno.']);
    exit;
});

require_once '../conexao/conexao.php';
require_once '../controllers/controller-adm/participacaoController.php';

$acao = $_GET['acao'] ?? '';
$controller = new ParticipacaoController($pdo);

switch ($acao) {
    case 'criar':              $controller->criar();             break;
    case 'atualizar':          $controller->atualizar();         break;
    case 'alterarStatus':      $controller->alterarStatus();     break;
    case 'excluir':            $controller->excluir();           break;
    case 'listarPorProjeto':   $controller->listarPorProjeto();  break;
    default: echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida.']);
}
?>
