<?php
// pages-adm/api-participacoes.php
header('Content-Type: application/json; charset=utf-8');

// Captura qualquer erro PHP e devolve como JSON em vez de HTML
set_error_handler(function($errno, $errstr) {
    echo json_encode(['sucesso' => false, 'mensagem' => "Erro PHP: $errstr"]);
    exit;
});

require_once '../conexao/conexao.php';
require_once '../controllers/controller-adm/participacaoController.php';

$acao = $_GET['acao'] ?? '';
$controller = new ParticipacaoController($pdo);

switch ($acao) {
    case 'criar':
        $controller->criar();
        break;
    case 'atualizar':
        $controller->atualizar();
        break;
    case 'alterarStatus':
        $controller->alterarStatus();
        break;
    case 'excluir':
        $controller->excluir();
        break;
    case 'listarPorProjeto':
        $controller->listarPorProjeto();
        break;
    default:
        echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida.']);
}
?>
