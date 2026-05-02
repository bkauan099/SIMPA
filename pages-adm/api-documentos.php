<?php
// pages-adm/api-documentos.php
header('Content-Type: application/json; charset=utf-8');

set_error_handler(function($errno, $errstr) {
    echo json_encode(['sucesso' => false, 'mensagem' => "Erro PHP: $errstr"]);
    exit;
});


require_once '../conexao/conexao.php';
require_once '../controllers/controller-adm/producaoController.php';

$acao = $_GET['acao'] ?? '';
$controller = new ProducaoController($pdo);

switch ($acao) {
    case 'criar':
        $controller->criar();
        break;
    case 'alterarStatus':
        $controller->alterarStatus();
        break;
    case 'excluir':
        $controller->excluir();
        break;
    default:
        echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida.']);
}
?>
